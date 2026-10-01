@php
    $isEdit = isset($book);
    $bookPdfExists =
        $isEdit && $book->pdf_file && Illuminate\Support\Facades\Storage::disk('public')->exists($book->pdf_file);
    $aiPdfLimitBytes = config('services.arnaru_ai.max_pdf_bytes', 4 * 1024 * 1024);
    $bookPdfSize = $bookPdfExists ? Illuminate\Support\Facades\Storage::disk('public')->size($book->pdf_file) : 0;
    $aiPdfAvailable = $bookPdfExists && $bookPdfSize <= $aiPdfLimitBytes;
@endphp
<div class="form-layout">
    <form class="panel form-panel" method="POST"
        action="{{ $isEdit ? route('buku.update', $book) : route('buku.store') }}" enctype="multipart/form-data">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif
        <h2 class="form-section-title">Informasi Buku</h2>
        @if ($isEdit)
            <section class="ai-metadata-tool" data-ai-metadata data-url="{{ route('buku.ai.metadata', $book) }}"
                data-has-pdf="{{ $aiPdfAvailable ? 'true' : 'false' }}">
                <div><strong>Saran metadata dari PDF</strong><span>AI hanya memberi saran; perubahan tetap kamu tinjau
                        sebelum disimpan.</span></div>
                <label class="ai-model-field">Model
                    <select class="field-select" data-ai-model aria-label="Model saran metadata">
                        @foreach (config('services.arnaru_ai.models', []) as $model)
                            <option value="{{ $model }}" @selected($model === config('services.arnaru_ai.model'))>{{ $model }}
                            </option>
                        @endforeach
                    </select>
                </label>
                <button class="button button-secondary button-small" type="button" data-ai-metadata-trigger
                    @disabled(!$aiPdfAvailable)><i data-lucide="sparkles"></i> Buat Saran</button>
                @if ($bookPdfExists && !$aiPdfAvailable)
                    <span class="field-error">PDF {{ number_format($bookPdfSize / 1024 / 1024, 2) }} MiB melebihi batas
                        AI {{ round($aiPdfLimitBytes / 1024 / 1024) }} MiB. Ganti dengan PDF yang lebih ringan.</span>
                @endif
                <div class="ai-metadata-results" data-ai-metadata-results aria-live="polite" hidden></div>
                <button class="button button-primary button-small" type="button" data-ai-metadata-apply hidden>Gunakan
                    Saran</button>
            </section>
        @endif
        <div class="form-grid">
            <div class="form-field full">
                <label for="title">Judul buku <span class="required">*</span></label>
                <input class="field-input @error('title') is-invalid @enderror" id="title" name="title"
                    value="{{ old('title', $book->title ?? '') }}" maxlength="255" required
                    placeholder="Contoh: Filosofi Teras">
                @error('title')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-field">
                <label for="category_id">Kategori <span class="required">*</span></label>
                <select class="field-select @error('category_id') is-invalid @enderror" id="category_id"
                    name="category_id" required>
                    <option value="">Pilih kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $book->category_id ?? '') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-field">
                <label for="isbn">ISBN <span class="required">*</span></label>
                <input class="field-input @error('isbn') is-invalid @enderror" id="isbn" name="isbn"
                    value="{{ old('isbn', $book->isbn ?? '') }}" maxlength="50" required
                    placeholder="Contoh: 978-602-06-xxxx-x">
                @error('isbn')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-field">
                <label for="author">Penulis <span class="required">*</span></label>
                <input class="field-input @error('author') is-invalid @enderror" id="author" name="author"
                    value="{{ old('author', $book->author ?? '') }}" maxlength="255" required
                    placeholder="Nama penulis">
                @error('author')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-field">
                <label for="publisher">Penerbit <span class="required">*</span></label>
                <input class="field-input @error('publisher') is-invalid @enderror" id="publisher" name="publisher"
                    value="{{ old('publisher', $book->publisher ?? '') }}" maxlength="255" required
                    placeholder="Nama penerbit">
                @error('publisher')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-field">
                <label for="publication_year">Tahun terbit <span class="required">*</span> <span
                        class="field-hint">Untuk tahun Sebelum Masehi, gunakan nilai negatif, misalnya
                        -380.</span></label>
                <input class="field-input @error('publication_year') is-invalid @enderror" id="publication_year"
                    name="publication_year" type="number" min="-380" max="{{ date('Y') }}"
                    value="{{ old('publication_year', $book->publication_year ?? '') }}" required
                    placeholder="{{ date('Y') }}">
                @error('publication_year')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-field">
                <label for="stock">Jumlah stok <span class="required">*</span></label>
                <input class="field-input @error('stock') is-invalid @enderror" id="stock" name="stock"
                    type="number" min="0" value="{{ old('stock', $book->stock ?? 0) }}" required>
                @error('stock')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-field full">
                <label for="cover_image">Sampul buku</label>
                <div class="cover-upload"><i data-lucide="image-plus"></i><span>JPG, JPEG, atau PNG · Maksimal 2
                        MB</span><input id="cover_image" name="cover_image" type="file"
                        accept=".jpg,.jpeg,.png,image/jpeg,image/png" data-cover-input="#cover-preview"></div>
                @error('cover_image')
                    <span class="field-error">{{ $message }}</span>
                @enderror
                @if ($isEdit && $book->cover_image)
                    <span class="field-hint">Kosongkan jika ingin tetap menggunakan sampul saat ini.</span>
                @endif
            </div>
            <div class="form-field full">
                <label for="pdf_file">File buku (PDF)</label>
                <div class="cover-upload"><i data-lucide="book-open"></i><span>PDF · Penyimpanan maks. 30 MB; AI maks.
                        {{ round($aiPdfLimitBytes / 1024 / 1024) }} MiB</span><input id="pdf_file" name="pdf_file"
                        type="file" accept=".pdf,application/pdf"></div>
                @error('pdf_file')
                    <span class="field-error">{{ $message }}</span>
                @enderror
                @if ($isEdit && $book->pdf_file)
                    <span class="field-hint">PDF saat ini tersedia. Pilih file baru jika ingin menggantinya.</span>
                @endif
            </div>
            <div class="form-field full">
                <label for="synopsis">Sinopsis</label>
                <textarea class="field-textarea @error('synopsis') is-invalid @enderror" id="synopsis" name="synopsis"
                    placeholder="Tambahkan ringkasan singkat tentang buku...">{{ old('synopsis', $book->synopsis ?? '') }}</textarea>
                @error('synopsis')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="form-actions"><a class="button button-secondary"
                href="{{ $isEdit ? route('buku.show', $book) : route('buku.index') }}">Batal</a><button
                class="button button-primary" type="submit"><i
                    data-lucide="check"></i>{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Buku' }}</button></div>
    </form>
    <aside class="form-sidebar">
        <section class="form-aside-card">
            <h3>Pratinjau Sampul</h3>
            <p>Sampul membantu koleksi lebih mudah dikenali.</p>
            <div id="cover-preview"><x-book-cover :book="$book ?? new App\Models\Books(['title' => old('title', 'Sampul Buku'), 'id' => 1])" size="large" /></div>
        </section>
        <section class="form-aside-card">
            <h3>Catatan Pengisian</h3>
            <p>Pastikan ISBN unik dan pilih kategori yang paling sesuai. Stok dapat bernilai nol jika buku sedang tidak
                tersedia.</p>
        </section>
    </aside>
</div>
