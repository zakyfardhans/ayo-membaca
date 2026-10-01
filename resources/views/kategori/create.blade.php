<div>
    <!-- He who is contented is rich. - Laozi -->
    @extends('layout.app')
    @section('title', 'Tambah Kategori')
    @section('section', 'Tambah Kategori')
    @section('content')
        <section class="page-heading">
            <div><span class="eyebrow"><i data-lucide="layers-3"></i> Kategori koleksi</span>
                <h1>Tambah Kategori</h1>
                <p>Buat pengelompokan baru untuk koleksi perpustakaan.</p>
            </div>
        </section>
        <form class="panel form-panel" method="POST" action="{{ route('kategori.store') }}" style="max-width: 650px">@csrf
            <div class="form-field"><label for="name">Nama kategori <span class="required">*</span></label><input
                    class="field-input @error('name') is-invalid @enderror" id="name" name="name"
                    value="{{ old('name') }}" maxlength="255" required placeholder="Contoh: Sastra Indonesia">
                @error('name')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-actions"><a class="button button-secondary"
                    href="{{ route('kategori.index') }}">Batal</a><button class="button button-primary" type="submit"><i
                        data-lucide="check"></i> Simpan Kategori</button></div>
        </form>
    @endsection
