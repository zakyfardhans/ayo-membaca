<div>
    <!-- He who is contented is rich. - Laozi -->
    @extends('layout.app')
    @section('title', 'Edit Kategori')
    @section('section', 'Edit Kategori')
    @section('content')
        <section class="page-heading">
            <div><span class="eyebrow"><i data-lucide="pencil"></i> Perbarui kategori</span>
                <h1>Edit Kategori</h1>
                <p>Perbarui nama pengelompokan koleksi.</p>
            </div>
        </section>
        <form class="panel form-panel" method="POST" action="{{ route('kategori.update', $category) }}"
            style="max-width: 650px">@csrf @method('PUT')
            <div class="form-field"><label for="name">Nama kategori <span class="required">*</span></label><input
                    class="field-input @error('name') is-invalid @enderror" id="name" name="name"
                    value="{{ old('name', $category->name) }}" maxlength="255" required>
                @error('name')
                    <span class="field-error">{{ $message }}</span>
                @enderror
                <span class="field-hint">Slug akan diperbarui mengikuti nama kategori.</span>
            </div>
            <div class="form-actions"><a class="button button-secondary"
                    href="{{ route('kategori.index') }}">Batal</a><button class="button button-primary" type="submit"><i
                        data-lucide="check"></i> Simpan Perubahan</button></div>
        </form>
    @endsection
