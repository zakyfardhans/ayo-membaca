<?php

namespace App\Http\Controllers;

use App\Models\Books;
use App\Models\Categories;
use App\Services\ArnaruAiException;
use App\Services\ArnaruAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Throwable;

class AiBookController extends Controller
{
    public function chat(Request $request, Books $book, ArnaruAiService $ai): JsonResponse
    {
        $models = config('services.arnaru_ai.models', []);
        $data = $request->validate([
            'mode' => ['required', Rule::in(['ask', 'summary', 'key_points', 'glossary', 'reading_guide', 'quiz'])],
            'question' => ['nullable', 'string', 'max:3000', Rule::requiredIf($request->input('mode') === 'ask')],
            'conversationId' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', Rule::in($models)],
        ]);

        if (! $book->pdf_file || ! Storage::disk('public')->exists($book->pdf_file)) {
            return response()->json(['message' => 'Unggah PDF buku ini sebelum menggunakan Asisten Baca.'], 422);
        }
        if ($response = $this->pdfSizeError($book->pdf_file)) {
            return $response;
        }

        $modeInstructions = [
            'ask' => 'Jawab pertanyaan pembaca berdasarkan isi PDF. Jika jawabannya tidak ada di buku, katakan dengan jelas.',
            'summary' => 'Buat ringkasan buku dalam Bahasa Indonesia dengan alur dan gagasan utama yang mudah dipahami.',
            'key_points' => 'Susun poin-poin penting buku dalam Bahasa Indonesia. Prioritaskan gagasan dan kesimpulan yang benar-benar ada di PDF.',
            'glossary' => 'Buat glosarium istilah penting dari PDF dalam Bahasa Indonesia, masing-masing dengan definisi singkat berdasarkan konteks buku.',
            'reading_guide' => 'Buat panduan baca praktis berdasarkan PDF: urutan/topik yang perlu diperhatikan dan pertanyaan refleksi.',
            'quiz' => 'Buat 5 soal pemahaman buku dalam Bahasa Indonesia, sertakan kunci jawaban di bagian terpisah.',
        ];
        $question = $data['mode'] === 'ask'
            ? $data['question']
            : 'Tolong lakukan tugas berikut untuk buku ini: '.str_replace('_', ' ', $data['mode']).'.';

        try {
            return response()->json($ai->chat($question, [
                'model' => $data['model'] ?? config('services.arnaru_ai.model'),
                'conversationId' => $data['conversationId'] ?? null,
                'systemPrompt' => 'Kamu adalah asisten pustakawan. Jawab dalam Bahasa Indonesia, gunakan hanya informasi dalam PDF terlampir, jangan mengarang kutipan atau fakta, dan ikuti instruksi tugas ini: '.$modeInstructions[$data['mode']],
            ], $book->pdf_file));
        } catch (Throwable $exception) {
            return $this->aiFailure($exception, true);
        }
    }

    public function catalog(Request $request, ArnaruAiService $ai): JsonResponse
    {
        $models = config('services.arnaru_ai.models', []);
        $data = $request->validate([
            'mode' => ['required', Rule::in(['search', 'recommend'])],
            'prompt' => ['required', 'string', 'min:3', 'max:500'],
            'model' => ['nullable', Rule::in($models)],
        ]);

        $books = Books::with('category')
            ->when($data['mode'] === 'recommend', fn ($query) => $query->where('stock', '>', 0))
            ->orderBy('title')
            ->limit(80)
            ->get();

        if ($books->isEmpty()) {
            return response()->json(['books' => [], 'message' => 'Belum ada buku yang cocok untuk diproses.']);
        }

        $catalog = $books->map(fn (Books $book) => [
            'isbn' => $book->isbn,
            'title' => $book->title,
            'author' => $book->author,
            'publisher' => $book->publisher,
            'category' => $book->category?->name,
            'publication_year' => $book->publication_year,
            'stock' => $book->stock,
            'synopsis' => $book->synopsis,
        ])->values()->all();

        $modeInstructions = $data['mode'] === 'recommend'
            ? 'Pilih paling banyak 5 buku yang sesuai dengan kebutuhan pembaca. Semua kandidat yang diberikan tersedia di katalog.'
            : 'Pilih paling banyak 10 buku yang paling sesuai dengan permintaan pencarian. Jangan menambah buku yang tidak ada di kandidat.';
        $question = $modeInstructions.' Permintaan: '.$data['prompt']."\n\nKandidat katalog (data faktual):\n".json_encode($catalog, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        try {
            $result = $ai->chat($question, [
                'model' => $data['model'] ?? config('services.arnaru_ai.model'),
                'systemPrompt' => 'Kamu membantu pencarian katalog perpustakaan. Keluarkan hanya JSON valid dengan bentuk {"items":[{"isbn":"ISBN kandidat","reason":"alasan singkat dalam Bahasa Indonesia"}]}. Gunakan ISBN persis dari kandidat. Jangan pernah membuat klaim ketersediaan di luar data kandidat.',
            ]);
        } catch (Throwable $exception) {
            return $this->aiFailure($exception, false);
        }

        $items = $this->decodeItems($result['answer']);
        $byIsbn = $books->keyBy(fn (Books $book) => $this->normalizeIsbn($book->isbn));
        $matches = [];

        foreach ($items as $item) {
            $isbn = is_array($item) ? ($item['isbn'] ?? null) : $item;
            if (! is_string($isbn) || ! ($book = $byIsbn->get($this->normalizeIsbn($isbn)))) {
                continue;
            }

            $matches[] = [
                'id' => $book->id,
                'isbn' => $book->isbn,
                'title' => $book->title,
                'author' => $book->author,
                'category' => $book->category?->name,
                'stock' => $book->stock,
                'reason' => is_array($item) && is_string($item['reason'] ?? null)
                    ? $item['reason']
                    : 'Dipilih berdasarkan kecocokan dengan permintaanmu.',
            ];
        }

        return response()->json([
            'books' => $matches,
            'message' => $matches ? null : 'AI tidak menemukan judul yang cocok di katalog saat ini.',
        ]);
    }

    public function metadata(Request $request, Books $book, ArnaruAiService $ai): JsonResponse
    {
        $data = $request->validate([
            'model' => ['nullable', Rule::in(config('services.arnaru_ai.models', []))],
        ]);

        if (! $book->pdf_file || ! Storage::disk('public')->exists($book->pdf_file)) {
            return response()->json(['message' => 'Unggah PDF buku ini sebelum meminta saran metadata.'], 422);
        }
        if ($response = $this->pdfSizeError($book->pdf_file)) {
            return $response;
        }

        $categories = Categories::orderBy('name')->get(['id', 'name', 'slug']);
        $categoryOptions = $categories->map(fn (Categories $category) => [
            'name' => $category->name,
            'slug' => $category->slug,
        ])->values()->all();

        try {
            $result = $ai->chat(
                'Ekstrak metadata buku dari PDF. Kembalikan hanya JSON valid dengan kunci title, author, publisher, publication_year, isbn, category_slug, synopsis. Gunakan category_slug hanya dari pilihan berikut: '.json_encode($categoryOptions, JSON_UNESCAPED_UNICODE),
                [
                    'model' => $data['model'] ?? config('services.arnaru_ai.model'),
                    'systemPrompt' => 'Kamu membantu pustakawan mengisi katalog. Ambil hanya informasi yang didukung dokumen. Untuk metadata yang tidak ditemukan, gunakan null. Jangan menyimpan perubahan.',
                ],
                $book->pdf_file,
            );
        } catch (Throwable $exception) {
            return $this->aiFailure($exception, true);
        }

        $suggestions = $this->decodeObject($result['answer']);
        if (isset($suggestions['metadata']) && is_array($suggestions['metadata'])) {
            $suggestions = $suggestions['metadata'];
        }

        $clean = [];
        foreach (['title', 'author', 'publisher', 'isbn', 'synopsis'] as $field) {
            if (is_string($suggestions[$field] ?? null) && trim($suggestions[$field]) !== '') {
                $clean[$field] = trim(strip_tags($suggestions[$field]));
            }
        }
        if (is_numeric($suggestions['publication_year'] ?? null)) {
            $year = (int) $suggestions['publication_year'];
            if ($year >= -380 && $year <= (int) date('Y')) {
                $clean['publication_year'] = $year;
            }
        }
        if (is_string($suggestions['category_slug'] ?? null)) {
            $category = $categories->firstWhere('slug', $suggestions['category_slug']);
            if ($category) {
                $clean['category_id'] = $category->id;
                $clean['category_name'] = $category->name;
            }
        }

        return response()->json(['suggestions' => $clean]);
    }

    private function decodeItems(string $answer): array
    {
        $decoded = $this->decodeObject($answer);
        $items = $decoded['items'] ?? $decoded['recommendations'] ?? $decoded['isbns'] ?? [];

        return is_array($items) ? array_slice(array_values($items), 0, 10) : [];
    }

    private function decodeObject(string $answer): array
    {
        $answer = trim(preg_replace('/^```(?:json)?\s*|\s*```$/i', '', trim($answer)) ?? $answer);
        $start = strpos($answer, '{');
        $end = strrpos($answer, '}');

        if ($start === false || $end === false || $end < $start) {
            return [];
        }

        $decoded = json_decode(substr($answer, $start, $end - $start + 1), true);

        return is_array($decoded) ? $decoded : [];
    }

    private function normalizeIsbn(string $isbn): string
    {
        return preg_replace('/\D+/', '', $isbn) ?? '';
    }

    private function pdfSizeError(string $path): ?JsonResponse
    {
        $size = Storage::disk('public')->size($path);
        $limit = config('services.arnaru_ai.max_pdf_bytes', 4 * 1024 * 1024);

        if ($size <= $limit) {
            return null;
        }

        $sizeMb = number_format($size / 1024 / 1024, 2);
        $limitMb = number_format($limit / 1024 / 1024, 0);

        return response()->json([
            'message' => "PDF ini berukuran {$sizeMb} MiB, sedangkan batas kirim Arnaru-AI {$limitMb} MiB. Kompres PDF atau unggah versi lebih ringan; PDF asli tetap bisa dibaca.",
        ], 413);
    }

    private function aiFailure(Throwable $exception, bool $includesPdf): JsonResponse
    {
        report($exception);

        if ($exception instanceof ArnaruAiException && $exception->statusCode === 413) {
            $message = $includesPdf
                ? 'Arnaru-AI menolak ukuran PDF ini. Kompres PDF atau unggah versi lebih ringan; PDF asli tetap bisa dibaca.'
                : 'Permintaan katalog terlalu besar untuk Arnaru-AI. Coba permintaan yang lebih spesifik.';

            return response()->json(['message' => $message], 413);
        }

        if ($exception instanceof ArnaruAiException && $exception->statusCode === 429) {
            return response()->json(['message' => 'Batas permintaan Arnaru-AI tercapai. Tunggu sebentar lalu coba lagi.'], 429);
        }

        return response()->json(['message' => 'Layanan AI sedang tidak tersedia. Coba lagi sebentar.'], 502);
    }
}
