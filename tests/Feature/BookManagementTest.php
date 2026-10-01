<?php

namespace Tests\Feature;

use App\Models\Books;
use App\Models\Categories;
use Database\Seeders\BooksSeeder;
use Database\Seeders\CategoriesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_search_category_and_stock_filters_work_together(): void
    {
        $category = $this->category('Teknologi');
        $otherCategory = $this->category('Fiksi');
        $this->book($category, ['title' => 'Laravel untuk Pemula', 'stock' => 4]);
        $this->book($category, ['title' => 'Laravel Sudah Habis', 'stock' => 0]);
        $this->book($otherCategory, ['title' => 'Laravel di Dunia Fiksi', 'stock' => 6]);

        $this->get('/buku?search=Laravel&category='.$category->id.'&stock=available')
            ->assertOk()
            ->assertSee('Laravel untuk Pemula')
            ->assertDontSee('Laravel Sudah Habis')
            ->assertDontSee('Laravel di Dunia Fiksi');
    }

    public function test_book_can_be_created_with_validated_catalog_fields(): void
    {
        $category = $this->category('Sejarah');

        $this->post(route('buku.store'), [
            'category_id' => $category->id,
            'isbn' => '978-602-000-0001',
            'title' => 'Sejarah Nusantara',
            'author' => 'Ayu Pratama',
            'publisher' => 'Pustaka Kita',
            'publication_year' => 2020,
            'stock' => 3,
            'synopsis' => 'Ringkasan sejarah kepulauan Indonesia.',
        ])->assertRedirect(route('buku.index'));

        $this->assertDatabaseHas('books', [
            'isbn' => '978-602-000-0001',
            'title' => 'Sejarah Nusantara',
            'category_id' => $category->id,
        ]);
    }

    public function test_book_can_be_created_with_a_before_common_era_year(): void
    {
        $category = $this->category('Filsafat');

        $this->post(route('buku.store'), [
            'category_id' => $category->id,
            'isbn' => '9780872201361',
            'title' => 'The Republic',
            'author' => 'Plato',
            'publisher' => 'Hackett Publishing',
            'publication_year' => -380,
            'stock' => 5,
        ])->assertRedirect(route('buku.index'));

        $this->assertDatabaseHas('books', ['isbn' => '9780872201361', 'publication_year' => -380]);
    }

    public function test_book_cover_uses_a_same_origin_storage_path(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('covers/test-cover.jpg', 'image-data');
        $book = $this->book($this->category('Fiksi'), ['cover_image' => 'covers/test-cover.jpg']);

        $this->get(route('buku.show', $book))
            ->assertOk()
            ->assertSee('src="/storage/covers/test-cover.jpg"', false);
    }

    public function test_book_pdf_can_be_uploaded_and_opened_inline(): void
    {
        Storage::fake('public');
        $category = $this->category('Fiksi');

        $this->post(route('buku.store'), [
            'category_id' => $category->id,
            'isbn' => '978-602-111-2223',
            'title' => 'Buku Digital',
            'author' => 'Penulis Contoh',
            'publisher' => 'Penerbit Contoh',
            'publication_year' => 2024,
            'stock' => 1,
            'pdf_file' => UploadedFile::fake()->create('buku-digital.pdf', 1, 'application/pdf'),
        ])->assertRedirect(route('buku.index'));

        $book = Books::where('isbn', '978-602-111-2223')->firstOrFail();
        Storage::disk('public')->assertExists($book->pdf_file);

        $this->get(route('buku.show', $book))
            ->assertOk()
            ->assertSee(route('buku.read', $book), false)
            ->assertSee('Baca Buku', false);
        $this->get(route('buku.index'))
            ->assertOk()
            ->assertSee(route('buku.read', $book), false);

        $this->get(route('buku.read', $book))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('content-disposition', 'inline; filename="'.basename($book->pdf_file).'"');
    }

    public function test_book_without_pdf_shows_status_and_cannot_be_opened(): void
    {
        $book = $this->book($this->category('Fiksi'));

        $this->get(route('buku.show', $book))->assertOk()->assertSee('aria-disabled="true"', false);
        $this->get(route('buku.read', $book))->assertNotFound();
    }

    public function test_replacing_a_book_pdf_deletes_the_old_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('books/old.pdf', 'old-pdf');
        $book = $this->book($this->category('Teknologi'), ['pdf_file' => 'books/old.pdf']);

        $this->put(route('buku.update', $book), [
            'category_id' => $book->category_id,
            'isbn' => $book->isbn,
            'title' => $book->title,
            'author' => $book->author,
            'publisher' => $book->publisher,
            'publication_year' => $book->publication_year,
            'stock' => $book->stock,
            'pdf_file' => UploadedFile::fake()->create('new-book.pdf', 1, 'application/pdf'),
        ])->assertRedirect(route('buku.show', $book));

        $book->refresh();
        Storage::disk('public')->assertMissing('books/old.pdf');
        Storage::disk('public')->assertExists($book->pdf_file);
    }

    public function test_ai_reader_sends_the_book_pdf_and_continues_the_conversation(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('books/reader.pdf', '%PDF-1.4 test');
        $book = $this->book($this->category('Fiksi'), ['pdf_file' => 'books/reader.pdf']);
        $sentRequest = [];
        Http::fake([
            'https://arnaru-ai.vercel.app/api/chat' => function (ClientRequest $request) use (&$sentRequest) {
                $sentRequest = [
                    'url' => $request->url(),
                    'multipart' => $request->isMultipart(),
                    'body' => $request->body(),
                ];

                return Http::response([
                    'data' => ['answer' => 'Ringkasan dari PDF.', 'conversationId' => 'thread-123'],
                ]);
            },
        ]);

        $this->postJson(route('buku.ai.chat', $book), [
            'mode' => 'ask',
            'question' => 'Apa gagasan utamanya?',
            'conversationId' => 'thread-existing',
            'model' => 'gemini-3-flash',
        ])->assertOk()
            ->assertJsonPath('answer', 'Ringkasan dari PDF.')
            ->assertJsonPath('conversationId', 'thread-123');

        $this->assertSame('https://arnaru-ai.vercel.app/api/chat', $sentRequest['url']);
        $this->assertTrue($sentRequest['multipart']);
        $this->assertStringContainsString('name="question"', $sentRequest['body']);
        $this->assertStringContainsString('Apa gagasan utamanya?', $sentRequest['body']);
        $this->assertStringContainsString('thread-existing', $sentRequest['body']);
    }

    public function test_ai_reader_rejects_pdfs_above_the_upstream_limit_before_sending(): void
    {
        Storage::fake('public');
        $oversizedPath = 'books/oversized.pdf';
        Storage::disk('public')->put($oversizedPath, str_repeat('x', config('services.arnaru_ai.max_pdf_bytes') + 1));
        $book = $this->book($this->category('Fiksi'), ['pdf_file' => $oversizedPath]);
        Http::fake();

        $this->postJson(route('buku.ai.chat', $book), [
            'mode' => 'summary',
            'model' => 'gemini-3-flash',
        ])->assertStatus(413)
            ->assertJsonPath('message', fn (string $message) => str_contains($message, '4 MiB') && str_contains($message, 'Kompres PDF'));

        $this->get(route('buku.show', $book))->assertOk()->assertSee('ai-pdf-too-large', false);

        Http::assertNothingSent();
    }

    public function test_upstream_413_is_reported_as_payload_too_large(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('books/small.pdf', '%PDF-1.4');
        $book = $this->book($this->category('Fiksi'), ['pdf_file' => 'books/small.pdf']);
        Http::fake(['https://arnaru-ai.vercel.app/api/chat' => Http::response([], 413)]);

        $this->postJson(route('buku.ai.chat', $book), [
            'mode' => 'summary',
            'model' => 'gemini-3-flash',
        ])->assertStatus(413)
            ->assertJsonPath('message', fn (string $message) => str_contains($message, 'Kompres PDF'));
    }

    public function test_ai_catalog_only_returns_books_from_the_database(): void
    {
        $book = $this->book($this->category('Sains'), ['isbn' => '978-111-222-3334', 'title' => 'Sains Ringkas']);
        Http::fake([
            'https://arnaru-ai.vercel.app/api/chat' => Http::response([
                'answer' => '{"items":[{"isbn":"978-111-222-3334","reason":"Cocok untuk pemula."},{"isbn":"978-000-000-0000","reason":"Tidak ada."}]}',
            ]),
        ]);

        $this->postJson(route('ai.catalog'), [
            'mode' => 'recommend',
            'prompt' => 'Saya ingin belajar sains dari dasar',
            'model' => 'gemini-3-flash',
        ])->assertOk()
            ->assertJsonCount(1, 'books')
            ->assertJsonPath('books.0.id', $book->id)
            ->assertJsonPath('books.0.reason', 'Cocok untuk pemula.');
    }

    public function test_ai_metadata_is_returned_as_reviewable_suggestions_only(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('books/metadata.pdf', '%PDF-1.4 test');
        $category = $this->category('Fiksi');
        $book = $this->book($category, ['pdf_file' => 'books/metadata.pdf']);
        Http::fake([
            'https://arnaru-ai.vercel.app/api/chat' => Http::response([
                'answer' => '{"title":"Judul Usulan","category_slug":"fiksi","publication_year":2021,"synopsis":"Sinopsis usulan."}',
            ]),
        ]);

        $this->postJson(route('buku.ai.metadata', $book), ['model' => 'gemini-3-flash'])
            ->assertOk()
            ->assertJsonPath('suggestions.title', 'Judul Usulan')
            ->assertJsonPath('suggestions.category_id', $category->id);

        $this->assertDatabaseHas('books', ['id' => $book->id, 'title' => 'Koleksi Perpustakaan']);
    }

    public function test_books_can_be_soft_deleted_restored_and_force_deleted(): void
    {
        $book = $this->book($this->category('Sains'));

        $this->delete(route('buku.destroy', $book))->assertRedirect(route('buku.index'));
        $this->assertSoftDeleted('books', ['id' => $book->id]);

        $this->patch(route('buku.restore', $book))->assertRedirect(route('buku.trash'));
        $this->assertDatabaseHas('books', ['id' => $book->id, 'deleted_at' => null]);

        $this->delete(route('buku.destroy', $book));
        $this->delete(route('buku.forceDelete', $book))->assertRedirect(route('buku.trash'));
        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    public function test_category_with_books_cannot_be_deleted(): void
    {
        $category = $this->category('Pemrograman');
        $this->book($category);

        $this->delete(route('kategori.destroy', $category))
            ->assertRedirect(route('kategori.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_curated_book_seeder_replaces_existing_books_and_is_repeatable(): void
    {
        $this->seed(CategoriesSeeder::class);
        $fiction = Categories::where('slug', 'fiksi')->firstOrFail();
        $oldBook = $this->book($fiction, ['isbn' => 'OLD-BOOK-0001', 'title' => 'Buku Lama']);

        $this->seed(BooksSeeder::class);

        $this->assertDatabaseCount('books', 60);
        $this->assertDatabaseMissing('books', ['id' => $oldBook->id]);
        $this->assertDatabaseHas('books', [
            'isbn' => '9789799731230',
            'title' => 'Bumi Manusia',
            'publication_year' => 1980,
        ]);
        $this->assertDatabaseHas('books', [
            'isbn' => '9780872201361',
            'title' => 'The Republic',
            'publication_year' => -380,
        ]);

        $this->seed(BooksSeeder::class);
        $this->assertDatabaseCount('books', 60);
    }

    private function category(string $name): Categories
    {
        return Categories::create(['name' => $name, 'slug' => str()->slug($name)]);
    }

    private function book(Categories $category, array $attributes = []): Books
    {
        return Books::create(array_merge([
            'category_id' => $category->id,
            'isbn' => fake()->unique()->numerify('978-###-###-####'),
            'title' => 'Koleksi Perpustakaan',
            'author' => 'Penulis Contoh',
            'publisher' => 'Penerbit Contoh',
            'publication_year' => 2022,
            'stock' => 5,
        ], $attributes));
    }
}
