@extends('layout.app')
@section('title', 'Kategori')
@section('section', 'Kategori')

@section('content')
    <section class="page-heading">
        <div><span class="eyebrow"><i data-lucide="layers-3"></i> Pengelompokan koleksi</span>
            <h1>Kategori Buku</h1>
            <p>Atur kategori agar koleksi lebih mudah dicari dan dipantau.</p>
        </div><a class="button button-primary" href="{{ route('kategori.create') }}"><i data-lucide="plus"></i> Tambah
            Kategori</a>
    </section>
    @if (session('error'))
        <div class="toast toast-error" role="alert"><i data-lucide="circle-alert"></i>{{ session('error') }}</div>
    @endif
    <div class="category-grid">
        @forelse ($categories as $category)
            <article class="category-card">
                <div class="category-card-top"><span class="category-card-icon"><i data-lucide="library-big"></i></span>
                    <div class="category-actions"><a class="icon-button" href="{{ route('kategori.edit', $category) }}"
                            aria-label="Edit {{ $category->name }}"><i data-lucide="pencil"></i></a>
                        <form class="confirm-form" method="POST" action="{{ route('kategori.destroy', $category) }}"
                            data-confirm="Hapus kategori {{ $category->name }}? Kategori dengan buku terkait tidak dapat dihapus.">
                            @csrf @method('DELETE')<button class="icon-button" type="submit"
                                aria-label="Hapus {{ $category->name }}"><i data-lucide="trash-2"></i></button></form>
                    </div>
                </div>
                <h2>{{ $category->name }}</h2>
                <p>Kategori · {{ $category->slug }}</p>
                <div class="category-card-bottom">
                    <strong>{{ $category->books_count }}</strong><span>{{ $category->books_count === 1 ? 'judul buku' : 'judul buku' }}</span>
                </div>
            </article>
        @empty
            <div class="panel empty-state"><span class="empty-icon"><i data-lucide="layers-3"></i></span>
                <h3>Belum ada kategori</h3>
                <p>Buat kategori untuk mengelompokkan koleksi buku.</p><a class="button button-primary button-small"
                    href="{{ route('kategori.create') }}"><i data-lucide="plus"></i> Tambah Kategori</a>
            </div>
        @endforelse
    </div>
@endsection
