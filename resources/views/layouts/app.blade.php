<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#1e1712">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.svg') }}">
    <title>@yield('title', 'Dashboard') - Istana Tembakau POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
<div class="app-shell">
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="document.getElementById('sidebar').classList.remove('show'); this.classList.remove('show');"></div>
    <aside class="sidebar" id="sidebar">
        <div class="brand d-flex align-items-center gap-2">
            <img src="{{ asset('images/logo.svg') }}" alt="Logo" width="34" height="34">
            <h5>ISTANA<br>TEMBAKAU</h5>
        </div>
        <nav class="nav flex-column py-2">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>
            @if(in_array(auth()->user()->role, ['admin','kasir']))
            <a href="{{ route('transaksi.index') }}" class="nav-link {{ request()->routeIs('transaksi.index', 'transaksi.nota') ? 'active' : '' }}">
                <i class="bi bi-cart3"></i> Transaksi
            </a>
            <a href="{{ route('transaksi.riwayat') }}" class="nav-link {{ request()->routeIs('transaksi.riwayat') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i> Riwayat Transaksi
            </a>
            @endif
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('produk.index') }}" class="nav-link {{ request()->routeIs('produk.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> Produk
            </a>
            <a href="{{ route('kategori.index') }}" class="nav-link {{ request()->routeIs('kategori.*') ? 'active' : '' }}">
                <i class="bi bi-tags"></i> Kategori
            </a>
            <a href="{{ route('stok.index') }}" class="nav-link {{ request()->routeIs('stok.*') ? 'active' : '' }}">
                <i class="bi bi-arrow-left-right"></i> Stok Barang
            </a>
            @endif
            @if(in_array(auth()->user()->role, ['admin','pimpinan']))
            <a href="{{ route('laporan.index') }}" class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart"></i> Laporan
            </a>
            @endif
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('pengguna.index') }}" class="nav-link {{ request()->routeIs('pengguna.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Pengguna
            </a>
            @endif
        </nav>
        <div class="mt-auto p-3">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="nav-link border-0 bg-transparent w-100 text-start" type="submit">
                    <i class="bi bi-box-arrow-right"></i> Keluar
                </button>
            </form>
        </div>
    </aside>

    <div class="main-content">
        <div class="topbar">
            <button class="btn d-md-none" onclick="document.getElementById('sidebar').classList.toggle('show'); document.getElementById('sidebarBackdrop').classList.toggle('show');">
                <i class="bi bi-list fs-4"></i>
            </button>
            <h5 class="mb-0">@yield('title', 'Dashboard')</h5>
            <a href="{{ route('akun.edit') }}" class="d-flex align-items-center gap-2 text-decoration-none text-dark topbar-profile">
                @if(auth()->user()->foto_profil)
                    <img src="{{ asset('storage/'.auth()->user()->foto_profil) }}" class="topbar-avatar" alt="Foto Profil">
                @else
                    <div class="bg-secondary bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                @endif
                <div class="d-none d-sm-block">
                    <div class="fw-semibold" style="font-size:.9rem;">{{ auth()->user()->name }}</div>
                    <div class="text-muted" style="font-size:.75rem; text-transform:capitalize;">{{ auth()->user()->role }}</div>
                </div>
            </a>
        </div>

        <div class="content-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>
