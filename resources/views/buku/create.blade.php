<div>
    <!-- Act only according to that maxim whereby you can, at the same time, will that it should become a universal law. - Immanuel Kant -->
    @extends('layout.app')
    @section('title', 'Tambah Buku')
    @section('section', 'Tambah Buku')
    @section('content')
        <section class="page-heading">
            <div><span class="eyebrow"><i data-lucide="book-plus"></i> Koleksi baru</span>
                <h1>Tambah Buku</h1>
                <p>Lengkapi informasi buku untuk memasukkannya ke inventaris.</p>
            </div><a class="button button-secondary" href="{{ route('buku.index') }}"><i data-lucide="arrow-left"></i>
                Kembali</a>
        </section>
        @include('buku.partials.form')
    @endsection
