<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0b2a5b">
    <title>@yield('title', 'AyoMembaca') · AyoMembaca</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>


<body>
    <div class="app-shell">
        <aside class="sidebar" id="sidebar">
            <a class="brand" href="{{ route('dashboard') }}" aria-label="AyoMembaca, dashboard">
                <span class="brand-mark"><img src="{{ asset('images/logo.png') }}" alt=""></span>
                <span class="brand-copy"><strong>AyoMembaca</strong><small>Pengelolaan Perpustakaan</small></span>
            </a>

            <div class="sidebar-label">MENU UTAMA</div>
            <nav class="side-nav" aria-label="Navigasi utama">
                <a href="{{ route('dashboard') }}" @class(['side-link', 'is-active' => request()->routeIs('dashboard')])>
                    <i data-lucide="layout-dashboard"></i><span>Dashboard</span>
                </a>
                <a href="{{ route('buku.index') }}" @class([
                    'side-link',
                    'is-active' => request()->routeIs('buku.index', 'buku.show', 'buku.edit'),
                ])>
                    <i data-lucide="book-open"></i><span>Semua Buku</span>
                </a>
                <a href="{{ route('buku.create') }}" @class([
                    'side-link',
                    'is-active' => request()->routeIs('buku.create'),
                ])>
                    <i data-lucide="book-plus"></i><span>Tambah Buku</span>
                </a>
                <a href="{{ route('kategori.index') }}" @class(['side-link', 'is-active' => request()->routeIs('kategori.*')])>
                    <i data-lucide="layers-3"></i><span>Kategori</span>
                </a>
                <a href="{{ route('buku.trash') }}" @class(['side-link', 'is-active' => request()->routeIs('buku.trash')])>
                    <i data-lucide="trash-2"></i><span>Buku Terhapus</span>
                    @if (($trashCount ?? null) > 0)
                        <span class="nav-count">{{ $trashCount }}</span>
                    @endif
                </a>
            </nav>

            <div class="sidebar-foot">
                <div class="sidebar-note"><span class="note-mark"><i data-lucide="sparkles"></i></span>
                    <strong>Ruang baca yang teratur</strong>
                    <span>Kelola koleksi, pantau stok, dan temukan buku lebih cepat.</span>
                </div>
                <div class="profile-row">
                    <div class="avatar">AP</div>
                    <div><strong>Admin Perpustakaan</strong><small>Pengelola</small></div><i
                        data-lucide="chevron-down"></i>
                </div>
            </div>
        </aside>

        <div class="workspace">
            <header class="topbar">
                <button class="icon-button menu-toggle" type="button" aria-label="Buka navigasi" aria-expanded="false"
                    data-menu-toggle><i data-lucide="menu"></i></button>
                <div class="breadcrumb"><span>Perpustakaan</span><i
                        data-lucide="chevron-right"></i><strong>@yield('section', 'Ringkasan')</strong></div>
                <div class="topbar-actions">
                    <form class="top-search" action="{{ route('buku.index') }}" method="GET" role="search">
                        <i data-lucide="search"></i><input name="search" value="{{ request('search') }}"
                            placeholder="Cari judul, penulis, ISBN..." aria-label="Cari buku">
                        <kbd>⌘ K</kbd>
                    </form>
                    <span class="topbar-date"><i
                            data-lucide="calendar-days"></i>{{ now()->translatedFormat('d M Y') }}</span>
                    <div class="top-avatar" aria-label="Admin">A</div>
                </div>
            </header>

            <main class="main-content">
                @if (session('success'))
                    <div class="toast toast-success" role="status"><i
                            data-lucide="circle-check"></i><span>{{ session('success') }}</span><button
                            class="toast-close" type="button" data-dismiss aria-label="Tutup notifikasi"><i
                                data-lucide="x"></i></button></div>
                @endif
                @if ($errors->any())
                    <div class="toast toast-error" role="alert"><i data-lucide="circle-alert"></i><span>Periksa
                            kembali data yang kamu masukkan.</span><button class="toast-close" type="button"
                            data-dismiss aria-label="Tutup notifikasi"><i data-lucide="x"></i></button></div>
                @endif
                @yield('content')
                <footer class="page-footer"><span>AyoMembaca</span><span>Kelola koleksi dengan lebih mudah</span>
                </footer>
            </main>
        </div>
    </div>
    <div class="sidebar-scrim" data-menu-close></div>
</body>

</html>
