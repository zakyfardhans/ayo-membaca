<div>
    <!-- Do what you can, with what you have, where you are. - Theodore Roosevelt -->
    @extends('layout.app')
    @section('title', 'Edit Buku')
    @section('section', 'Edit Buku')
    @section('content')
        <section class="page-heading">
            <div><span class="eyebrow"><i data-lucide="pencil"></i> Perbarui koleksi</span>
                <h1>Edit Buku</h1>
                <p>Perbarui detail dan jumlah stok buku ini.</p>
            </div><a class="button button-secondary" href="{{ route('buku.show', $book) }}"><i data-lucide="arrow-left"></i>
                Kembali ke Detail</a>
        </section>
        @include('buku.partials.form', ['book' => $book])
    @endsection
