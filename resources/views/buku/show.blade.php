<div>
    <!-- The only way to do great work is to love what you do. - Steve Jobs -->
    @extends('layout.app')
    @section('title', $book->title)
    @section('section', 'Detail Buku')

    @section('content')
        @php
            $bookPdfExists =
                $book->pdf_file && Illuminate\Support\Facades\Storage::disk('public')->exists($book->pdf_file);
            $aiPdfLimitBytes = config('services.arnaru_ai.max_pdf_bytes', 4 * 1024 * 1024);
            $bookPdfSize = $bookPdfExists
                ? Illuminate\Support\Facades\Storage::disk('public')->size($book->pdf_file)
                : 0;
            $bookPdfTooLarge = $bookPdfExists && $bookPdfSize > $aiPdfLimitBytes;
        @endphp
        <section class="page-heading">
            <div><span class="eyebrow"><i data-lucide="book-open-check"></i> Detail koleksi</span>
                <h1>Detail Buku</h1>
                <p>Informasi lengkap buku dalam inventaris.</p>
            </div>
            <div class="heading-actions">
                @if ($book->pdf_file && Illuminate\Support\Facades\Storage::disk('public')->exists($book->pdf_file))
                    <a class="button button-primary" href="{{ route('buku.read', $book) }}" target="_blank" rel="noopener"><i
                            data-lucide="book-open-check"></i> Baca Buku</a>
                @else
                    <span class="button button-secondary" aria-disabled="true"><i data-lucide="book-open"></i> PDF belum
                        tersedia</span>
                @endif
                <a class="button button-secondary" href="{{ route('buku.index') }}"><i data-lucide="arrow-left"></i>
                    Kembali</a><a class="button button-secondary" href="{{ route('buku.edit', $book) }}"><i
                        data-lucide="pencil"></i> Edit Buku</a>
            </div>
        </section>
        <section class="panel detail-grid">
            <div class="detail-cover"><x-book-cover :book="$book" size="large" /></div>
            <div class="detail-content">
                <span class="category-tag">{{ $book->category?->name ?? 'Tanpa kategori' }}</span>
                <h2>{{ $book->title }}</h2>
                <p class="detail-author">oleh {{ $book->author }}</p>
                <div class="detail-facts">
                    <div class="detail-fact"><span>ISBN</span><strong>{{ $book->isbn }}</strong></div>
                    <div class="detail-fact"><span>Penerbit</span><strong>{{ $book->publisher }}</strong></div>
                    <div class="detail-fact"><span>Tahun
                            Terbit</span><strong>{{ $book->publication_year < 0 ? abs($book->publication_year) . ' SM' : $book->publication_year }}</strong>
                    </div>
                    <div class="detail-fact"><span>Status Stok</span><strong><span
                                @class([
                                    'stock-tag',
                                    'stock-ok' => $book->stock >= 3,
                                    'stock-low' => $book->stock > 0 && $book->stock < 3,
                                    'stock-empty' => $book->stock === 0,
                                ])>{{ $book->stock }}
                                {{ $book->stock === 0 ? 'Habis' : 'Eksemplar' }}</span></strong></div>
                </div>
                <div class="synopsis">
                    <h3>Sinopsis</h3>
                    <p>{{ $book->synopsis ?: 'Belum ada sinopsis untuk buku ini.' }}</p>
                </div>
            </div>
        </section>
        @if ($bookPdfExists && !$bookPdfTooLarge)
            <section class="panel ai-reader" data-ai-chat data-book-id="{{ $book->id }}"
                data-url="{{ route('buku.ai.chat', $book) }}">
                @csrf
                <div class="ai-tool-header">
                    <div><span class="eyebrow"><i data-lucide="sparkles"></i> Asisten Baca</span>
                        <h2>Tanya isi buku</h2>
                        <p>Jawaban AI merujuk pada PDF buku ini.</p>
                    </div>
                    <label class="ai-model-field">Model
                        <select class="field-select" data-ai-model aria-label="Model AI">
                            @foreach (config('services.arnaru_ai.models', []) as $model)
                                <option value="{{ $model }}" @selected($model === config('services.arnaru_ai.model'))>{{ $model }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                </div>
                <div class="ai-mode-list" role="group" aria-label="Aksi Asisten Baca">
                    <button class="ai-mode-button" type="button" data-ai-mode="summary">Ringkas</button>
                    <button class="ai-mode-button" type="button" data-ai-mode="key_points">Poin penting</button>
                    <button class="ai-mode-button" type="button" data-ai-mode="glossary">Glosarium</button>
                    <button class="ai-mode-button" type="button" data-ai-mode="reading_guide">Panduan baca</button>
                    <button class="ai-mode-button" type="button" data-ai-mode="quiz">Buat kuis</button>
                </div>
                <div class="ai-chat-messages" data-ai-messages aria-live="polite">
                    <div class="ai-message ai-message-assistant">
                        <p>Ajukan pertanyaan atau pilih salah satu aksi di atas untuk mulai membaca bersama AI.</p>
                    </div>
                </div>
                <form class="ai-chat-form" data-ai-chat-form>
                    <input type="hidden" name="mode" value="ask" data-ai-mode-value>
                    <textarea class="field-textarea" name="question" data-ai-question maxlength="3000"
                        placeholder="Tanyakan sesuatu tentang buku ini..." aria-label="Pertanyaan tentang buku"></textarea>
                    <button class="button button-primary" type="submit"><i data-lucide="send"></i> Kirim</button>
                </form>
            </section>
        @elseif ($bookPdfTooLarge)
            <section class="panel ai-unavailable ai-pdf-too-large"><i data-lucide="triangle-alert"></i>
                <div><strong>PDF melebihi batas upload AI</strong><span>PDF ini berukuran
                        {{ number_format($bookPdfSize / 1024 / 1024, 2) }} MiB; Arnaru-AI menerima hingga
                        {{ round($aiPdfLimitBytes / 1024 / 1024) }} MiB. Kompres PDF atau unggah versi lebih ringan. File
                        asli tetap bisa dibaca.</span></div>
                <a class="button button-secondary button-small" href="{{ route('buku.edit', $book) }}">Ganti PDF</a>
            </section>
        @elseif (!$bookPdfExists)
            <section class="panel ai-unavailable"><i data-lucide="sparkles"></i>
                <div><strong>Asisten Baca belum tersedia</strong><span>Unggah PDF buku ini untuk meminta ringkasan dan
                        bertanya tentang isinya.</span></div><a class="button button-secondary button-small"
                    href="{{ route('buku.edit', $book) }}">Unggah PDF</a>
            </section>
        @endif
    @endsection
