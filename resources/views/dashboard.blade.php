@extends('layout.app')
@section('title', 'Dashboard')
@section('section', 'Dashboard')

@section('content')
    <section class="page-heading">
        <div>
            <span class="eyebrow"><i data-lucide="sun"></i> Selamat datang kembali</span>
            <h1>Dashboard</h1>
            <p>Pantau koleksi dan kondisi inventaris perpustakaanmu.</p>
        </div>
        <div class="heading-actions">
            <a class="button button-secondary" href="{{ route('kategori.index') }}"><i data-lucide="layers-3"></i> Kelola
                Kategori</a>
            <a class="button button-primary" href="{{ route('buku.create') }}"><i data-lucide="plus"></i> Tambah Buku</a>
        </div>
    </section>

    <section class="stat-grid" aria-label="Ringkasan inventaris">
        <article class="stat-card">
            <div class="stat-top"><span>Total Koleksi</span><span class="stat-icon"><i data-lucide="book-open"></i></span>
            </div>
            <div class="stat-value">{{ number_format($bookCount) }} <small>judul buku</small></div>
            <div class="stat-caption">Semua buku aktif di katalog</div>
        </article>
        <article class="stat-card">
            <div class="stat-top"><span>Kategori</span><span class="stat-icon coral"><i data-lucide="layers-3"></i></span>
            </div>
            <div class="stat-value">{{ number_format($categoryCount) }} <small>kategori</small></div>
            <div class="stat-caption">Pengelompokan koleksi</div>
        </article>
        <article class="stat-card">
            <div class="stat-top"><span>Stok Menipis</span><span class="stat-icon amber"><i
                        data-lucide="triangle-alert"></i></span></div>
            <div class="stat-value">{{ number_format($lowStockCount) }} <small>judul</small></div>
            <div class="stat-caption">Hanya tersisa 1–2 eksemplar</div>
        </article>
        <article class="stat-card">
            <div class="stat-top"><span>Stok Habis</span><span class="stat-icon green"><i
                        data-lucide="package-x"></i></span></div>
            <div class="stat-value">{{ number_format($emptyStockCount) }} <small>judul</small></div>
            <div class="stat-caption">Perlu dipertimbangkan untuk restok</div>
        </article>
    </section>

    <div class="dashboard-grid">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Koleksi Terbaru</h2>
                    <p>Buku yang terakhir ditambahkan ke inventaris</p>
                </div>
                <a class="text-link" href="{{ route('buku.index') }}">Lihat semua <i data-lucide="arrow-right"></i></a>
            </div>
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Buku</th>
                            <th>Kategori</th>
                            <th>Stok</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentBooks as $book)
                            <tr>
                                <td><a class="book-cell" href="{{ route('buku.show', $book) }}"><x-book-cover
                                            :book="$book" /><span
                                            class="book-meta"><strong>{{ $book->title }}</strong><span>{{ $book->author }}</span></span></a>
                                </td>
                                <td><span class="category-tag">{{ $book->category?->name ?? 'Tanpa kategori' }}</span></td>
                                <td><span @class([
                                    'stock-tag',
                                    'stock-ok' => $book->stock >= 3,
                                    'stock-low' => $book->stock > 0 && $book->stock < 3,
                                    'stock-empty' => $book->stock === 0,
                                ])>{{ $book->stock }}
                                        {{ $book->stock === 0 ? 'Habis' : 'Eks.' }}</span></td>
                                <td><a class="icon-button" href="{{ route('buku.show', $book) }}"
                                        aria-label="Lihat {{ $book->title }}"><i data-lucide="arrow-up-right"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state"><span class="empty-icon"><i data-lucide="book-open"></i></span>
                                        <h3>Belum ada koleksi</h3>
                                        <p>Tambahkan buku pertama untuk mulai mengelola inventaris.</p><a
                                            class="button button-primary button-small" href="{{ route('buku.create') }}"><i
                                                data-lucide="plus"></i> Tambah Buku</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="panel category-panel">
            <div class="panel-header">
                <div>
                    <h2>Kategori Buku</h2>
                    <p>Ringkasan koleksi per kategori</p>
                </div><a class="icon-button" href="{{ route('kategori.index') }}" aria-label="Kelola kategori"><i
                        data-lucide="arrow-up-right"></i></a>
            </div>
            <div class="category-list">
                @forelse ($categories->take(6) as $category)
                    <div class="category-row"><span
                            class="category-dot"></span><span>{{ $category->name }}</span><strong>{{ $category->books_count }}</strong>
                    </div>
                @empty
                    <div class="empty-state">
                        <h3>Belum ada kategori</h3><a class="text-link" href="{{ route('kategori.create') }}">Buat kategori
                            pertama</a>
                    </div>
                @endforelse
            </div>
            <div class="category-total"><span>Dalam buku terhapus</span><strong>{{ $trashCount }}</strong></div>
        </section>
    </div>

    <section class="quick-panel">
        <div><strong>Butuh menambahkan koleksi baru?</strong><span>Catat detail, kategori, dan jumlah stok dalam satu
                formulir.</span></div>
        <a class="button button-primary" href="{{ route('buku.create') }}"><i data-lucide="book-plus"></i> Tambah Buku
            Baru</a>
    </section>

    <section class="panel ai-catalog-panel" data-ai-catalog data-mode="recommend" data-url="{{ route('ai.catalog') }}">
        @csrf
        <div class="ai-tool-header">
            <div><span class="eyebrow"><i data-lucide="sparkles"></i> Rekomendasi AI</span>
                <h2>Temukan buku untuk kebutuhanmu</h2>
            </div>
            <label class="ai-model-field">Model
                <select class="field-select" data-ai-model aria-label="Model rekomendasi AI">
                    @foreach (config('services.arnaru_ai.models', []) as $model)
                        <option value="{{ $model }}" @selected($model === config('services.arnaru_ai.model'))>{{ $model }}</option>
                    @endforeach
                </select>
            </label>
        </div>
        <form class="ai-catalog-form" data-ai-catalog-form>
            <input class="field-input" name="prompt" maxlength="500" required
                placeholder="Contoh: Saya ingin belajar sejarah Indonesia dari dasar" aria-label="Kebutuhan membaca">
            <button class="button button-primary" type="submit"><i data-lucide="sparkles"></i> Cari Rekomendasi</button>
        </form>
        <div class="ai-catalog-results" data-ai-catalog-results aria-live="polite"></div>
    </section>
@endsection
