@extends('layout.app')
@section('title', 'Buku Terhapus')
@section('section', 'Buku Terhapus')

@section('content')
    <section class="page-heading">
        <div><span class="eyebrow"><i data-lucide="trash-2"></i> Pemulihan koleksi</span>
            <h1>Buku Terhapus</h1>
            <p>Buku di sini bisa dipulihkan atau dihapus permanen.</p>
        </div><a class="button button-secondary" href="{{ route('buku.index') }}"><i data-lucide="arrow-left"></i> Kembali ke
            Katalog</a>
    </section>
    <section class="panel table-panel">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Buku</th>
                        <th>Kategori</th>
                        <th>ISBN</th>
                        <th>Dihapus</th>
                        <th><span class="sr-only">Aksi</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($books as $book)
                        <tr>
                            <td>
                                <div class="book-cell"><x-book-cover :book="$book" /><span
                                        class="book-meta"><strong>{{ $book->title }}</strong><span>{{ $book->author }}</span></span>
                                </div>
                            </td>
                            <td><span class="category-tag">{{ $book->category?->name ?? 'Tanpa kategori' }}</span></td>
                            <td>{{ $book->isbn }}</td>
                            <td>{{ $book->deleted_at->format('d M Y') }}</td>
                            <td>
                                <div class="table-actions">
                                    <form method="POST" action="{{ route('buku.restore', $book) }}">@csrf
                                        @method('PATCH')<button class="button button-secondary button-small"
                                            type="submit"><i data-lucide="rotate-ccw"></i> Pulihkan</button></form>
                                    <form class="confirm-form" method="POST"
                                        action="{{ route('buku.forceDelete', $book) }}"
                                        data-confirm="Hapus buku dan file sampul secara permanen? Tindakan ini tidak dapat dibatalkan.">
                                        @csrf @method('DELETE')<button class="icon-button" type="submit"
                                            aria-label="Hapus permanen {{ $book->title }}"><i data-lucide="x"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state"><span class="empty-icon"><i
                                            data-lucide="archive-restore"></i></span>
                                    <h3>Trash masih kosong</h3>
                                    <p>Buku yang dihapus dari katalog akan muncul di sini.</p><a
                                        class="button button-secondary button-small" href="{{ route('buku.index') }}">Lihat
                                        Katalog</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($books->hasPages())
            <div class="pagination-wrap"><span>Halaman {{ $books->currentPage() }} dari
                    {{ $books->lastPage() }}</span>{{ $books->links() }}</div>
        @endif
    </section>
@endsection
