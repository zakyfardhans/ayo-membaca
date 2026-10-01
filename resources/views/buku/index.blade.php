@extends('layout.app')
@section('title', 'Semua Buku')
@section('section', 'Semua Buku')

@section('content')
    <section class="page-heading">
        <div><span class="eyebrow"><i data-lucide="book-open"></i> Katalog Perpustakaan</span>
            <h1>Semua Buku</h1>
            <p>Kelola koleksi dan pantau ketersediaan buku.</p>
        </div>
        <div class="heading-actions"><a class="button button-primary" href="{{ route('buku.create') }}"><i
                    data-lucide="plus"></i> Tambah Buku</a></div>
    </section>

    <section class="stat-grid" aria-label="Ringkasan stok">
        <article class="stat-card">
            <div class="stat-top"><span>Total Judul</span><span class="stat-icon"><i data-lucide="library"></i></span></div>
            <div class="stat-value">{{ number_format($bookCount) }} <small>buku</small></div>
            <div class="stat-caption">Koleksi aktif di perpustakaan</div>
        </article>
        <article class="stat-card">
            <div class="stat-top"><span>Halaman Ini</span><span class="stat-icon coral"><i
                        data-lucide="list-filter"></i></span></div>
            <div class="stat-value">{{ $books->count() }} <small>dari {{ $books->total() }} hasil</small></div>
            <div class="stat-caption">Maksimal 10 buku per halaman</div>
        </article>
        <article class="stat-card">
            <div class="stat-top"><span>Stok Menipis</span><span class="stat-icon amber"><i
                        data-lucide="triangle-alert"></i></span></div>
            <div class="stat-value">{{ number_format($lowStockCount) }} <small>judul</small></div>
            <div class="stat-caption">Tersisa kurang dari 3 eksemplar</div>
        </article>
        <article class="stat-card">
            <div class="stat-top"><span>Stok Habis</span><span class="stat-icon green"><i
                        data-lucide="package-x"></i></span></div>
            <div class="stat-value">{{ number_format($emptyStockCount) }} <small>judul</small></div>
            <div class="stat-caption">Tidak ada eksemplar tersedia</div>
        </article>
    </section>

    <section class="panel filter-panel">
        <form class="filter-form" action="{{ route('buku.index') }}" method="GET">
            <label class="search-control"><i data-lucide="search"></i><input class="field-input" type="search"
                    name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari judul, penulis, atau ISBN..."
                    aria-label="Cari judul, penulis, atau ISBN"></label>
            <select class="field-select" name="category" aria-label="Filter kategori">
                <option value="">Semua kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(($filters['category'] ?? '') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <select class="field-select" name="stock" aria-label="Filter stok">
                <option value="">Semua status stok</option>
                <option value="available" @selected(($filters['stock'] ?? '') === 'available')>Tersedia</option>
                <option value="empty" @selected(($filters['stock'] ?? '') === 'empty')>Habis</option>
            </select>
            <button class="button button-primary" type="submit"><i data-lucide="sliders-horizontal"></i> Terapkan</button>
            @if (request()->hasAny(['search', 'category', 'stock']))
                <a class="button button-secondary" href="{{ route('buku.index') }}">Reset</a>
            @endif
        </form>
    </section>

    <section class="panel ai-catalog-panel ai-search-panel" data-ai-catalog data-mode="search"
        data-url="{{ route('ai.catalog') }}">
        @csrf
        <div class="ai-tool-header">
            <div><span class="eyebrow"><i data-lucide="sparkles"></i> Pencarian AI</span>
                <h2>Cari dengan kalimat</h2>
            </div>
            <label class="ai-model-field">Model
                <select class="field-select" data-ai-model aria-label="Model pencarian AI">
                    @foreach (config('services.arnaru_ai.models', []) as $model)
                        <option value="{{ $model }}" @selected($model === config('services.arnaru_ai.model'))>{{ $model }}</option>
                    @endforeach
                </select>
            </label>
        </div>
        <form class="ai-catalog-form" data-ai-catalog-form>
            <input class="field-input" name="prompt" maxlength="500" required
                placeholder="Contoh: buku sains untuk pemula dengan stok tersedia" aria-label="Pencarian buku dengan AI">
            <button class="button button-primary" type="submit"><i data-lucide="search"></i> Cari</button>
        </form>
        <div class="ai-catalog-results" data-ai-catalog-results aria-live="polite"></div>
    </section>

    <div class="result-bar"><span>Menampilkan
            <strong>{{ $books->firstItem() ?? 0 }}–{{ $books->lastItem() ?? 0 }}</strong> dari
            <strong>{{ $books->total() }}</strong> buku</span>
        @if (request()->hasAny(['search', 'category', 'stock']))
            <a href="{{ route('buku.index') }}">Hapus semua filter</a>
        @endif
    </div>
    <section class="panel table-panel">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Buku</th>
                        <th>Kategori</th>
                        <th>ISBN</th>
                        <th>Tahun</th>
                        <th>Stok</th>
                        <th><span class="sr-only">Aksi</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($books as $book)
                        <tr>
                            <td><a class="book-cell" href="{{ route('buku.show', $book) }}"><x-book-cover
                                        :book="$book" /><span
                                        class="book-meta"><strong>{{ $book->title }}</strong><span>{{ $book->author }} ·
                                            {{ $book->publisher }}</span></span></a></td>
                            <td><span class="category-tag">{{ $book->category?->name ?? 'Tanpa kategori' }}</span></td>
                            <td>{{ $book->isbn }}</td>
                            <td>{{ $book->publication_year < 0 ? abs($book->publication_year) . ' SM' : $book->publication_year }}
                            </td>
                            <td><span @class([
                                'stock-tag',
                                'stock-ok' => $book->stock >= 3,
                                'stock-low' => $book->stock > 0 && $book->stock < 3,
                                'stock-empty' => $book->stock === 0,
                            ])>{{ $book->stock }}
                                    {{ $book->stock === 0 ? 'Habis' : 'Eks.' }}</span></td>
                            <td>
                                <div class="table-actions"><a class="icon-button" href="{{ route('buku.show', $book) }}"
                                        aria-label="Detail {{ $book->title }}"><i data-lucide="eye"></i></a><a
                                        class="icon-button" href="{{ route('buku.edit', $book) }}"
                                        aria-label="Edit {{ $book->title }}"><i data-lucide="pencil"></i></a>
                                    @if ($book->pdf_file && Illuminate\Support\Facades\Storage::disk('public')->exists($book->pdf_file))
                                        <a class="icon-button" href="{{ route('buku.read', $book) }}" target="_blank"
                                            rel="noopener" aria-label="Baca {{ $book->title }}"><i
                                                data-lucide="book-open-check"></i></a>
                                    @endif
                                    <form class="confirm-form" method="POST"
                                        action="{{ route('buku.destroy', $book) }}"
                                        data-confirm="Pindahkan buku ini ke Trash?">@csrf @method('DELETE')<button
                                            class="icon-button" type="submit" aria-label="Hapus {{ $book->title }}"><i
                                                data-lucide="trash-2"></i></button></form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state"><span class="empty-icon"><i data-lucide="book-x"></i></span>
                                    <h3>{{ request()->hasAny(['search', 'category', 'stock']) ? 'Buku tidak ditemukan' : 'Belum ada data buku' }}
                                    </h3>
                                    <p>{{ request()->hasAny(['search', 'category', 'stock']) ? 'Coba ubah kata kunci atau filter untuk menemukan koleksi.' : 'Tambahkan buku pertama untuk mulai mengelola inventaris.' }}
                                    </p><a class="button button-primary button-small"
                                        href="{{ route('buku.create') }}"><i data-lucide="plus"></i> Tambah Buku</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($books->hasPages())
            <div class="pagination-wrap"><span>Halaman {{ $books->currentPage() }} dari {{ $books->lastPage() }}</span>
                <nav class="pagination" aria-label="Navigasi halaman">
                    @if ($books->onFirstPage())
                    <span class="disabled" aria-disabled="true"><i data-lucide="chevron-left"></i></span>@else<a
                            href="{{ $books->previousPageUrl() }}" aria-label="Halaman sebelumnya"><i
                                data-lucide="chevron-left"></i></a>
                    @endif
                    @foreach ($books->getUrlRange(max(1, $books->currentPage() - 1), min($books->lastPage(), $books->currentPage() + 1)) as $page => $url)
                        <a @class(['active' => $page === $books->currentPage()]) href="{{ $url }}"
                            @if ($page === $books->currentPage()) aria-current="page" @endif>{{ $page }}</a>
                    @endforeach
                    @if ($books->hasMorePages())
                        <a href="{{ $books->nextPageUrl() }}" aria-label="Halaman selanjutnya"><i
                            data-lucide="chevron-right"></i></a>@else<span class="disabled" aria-disabled="true"><i
                                data-lucide="chevron-right"></i></span>
                    @endif
                </nav>
            </div>
        @endif
    </section>
@endsection
