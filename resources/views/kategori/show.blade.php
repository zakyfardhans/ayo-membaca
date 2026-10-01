<div>
    <!-- We must ship. - Taylor Otwell -->
    @extends('layout.app')
    @section('title', $category->name)
    @section('section', 'Kategori')
    @section('content')
        <section class="page-heading">
            <div><span class="eyebrow"><i data-lucide="layers-3"></i> Detail kategori</span>
                <h1>{{ $category->name }}</h1>
                <p>{{ $category->books->count() }} buku dalam kategori ini.</p>
            </div><a class="button button-secondary" href="{{ route('kategori.index') }}"><i data-lucide="arrow-left"></i>
                Kembali</a>
        </section>
        <section class="panel table-panel">
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Buku</th>
                            <th>ISBN</th>
                            <th>Stok</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($category->books as $book)
                            <tr>
                                <td><a class="book-cell" href="{{ route('buku.show', $book) }}"><x-book-cover
                                            :book="$book" /><span
                                            class="book-meta"><strong>{{ $book->title }}</strong><span>{{ $book->author }}</span></span></a>
                                </td>
                                <td>{{ $book->isbn }}</td>
                                <td>{{ $book->stock }}</td>
                                <td><a class="icon-button" href="{{ route('buku.show', $book) }}"
                                        aria-label="Lihat {{ $book->title }}"><i data-lucide="arrow-up-right"></i></a></td>
                            </tr>
                        @empty<tr>
                                <td colspan="4">
                                    <div class="empty-state">
                                        <h3>Belum ada buku di kategori ini</h3><a class="button button-primary button-small"
                                            href="{{ route('buku.create') }}"><i data-lucide="plus"></i> Tambah Buku</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endsection
