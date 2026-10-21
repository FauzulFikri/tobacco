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
            <div class="d-flex align-items-center gap-2 gap-sm-3">
            {{-- Lonceng pengingat stok menipis --}}
            <div class="dropdown">
                <button class="btn notif-bell {{ $notifStok->isNotEmpty() ? 'ada' : '' }}" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-label="Notifikasi stok">
                    <i class="bi bi-bell{{ $notifStok->isNotEmpty() ? '-fill' : '' }}"></i>
                    @if($notifStok->isNotEmpty())
                        <span class="notif-badge">{{ $notifStok->count() > 9 ? '9+' : $notifStok->count() }}</span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end notif-menu p-0 shadow">
                    <div class="notif-head">
                        <div class="fw-bold"><i class="bi bi-exclamation-triangle-fill text-warning"></i> Pengingat Stok</div>
                        <div class="small opacity-75">{{ $notifStok->isNotEmpty() ? $notifStok->count().' produk perlu diisi ulang' : 'Semua stok aman' }}</div>
                    </div>
                    <div class="notif-body">
                        @forelse($notifStok as $np)
                            <div class="notif-item">
                                <div class="notif-ikon {{ $np->stok <= 0 ? 'habis' : '' }}"><i class="bi {{ $np->stok <= 0 ? 'bi-x-octagon' : 'bi-box-seam' }}"></i></div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold text-truncate">{{ $np->nama_produk }}</div>
                                    <div class="small text-muted">
                                        {{ $np->stok <= 0 ? 'Stok habis' : 'Sisa '.$np->stok }} &middot; min. {{ $np->stok_minimum }}
                                    </div>
                                </div>
                                <span class="badge {{ $np->stok <= 0 ? 'bg-danger' : 'bg-warning text-dark' }}">{{ $np->stok <= 0 ? 'Habis' : 'Menipis' }}</span>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4 small">
                                <i class="bi bi-check-circle fs-3 text-success d-block mb-1"></i>
                                Tidak ada produk yang stoknya menipis
                            </div>
                        @endforelse
                    </div>
                    @if($notifStok->isNotEmpty())
                    <div class="notif-foot">
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('stok.index') }}" class="btn btn-emas btn-sm w-100"><i class="bi bi-box-arrow-in-down"></i> Catat Barang Masuk</a>
                        @elseif(auth()->user()->role === 'pimpinan')
                            <a href="{{ route('laporan.stok', ['status' => 'menipis']) }}" class="btn btn-emas btn-sm w-100"><i class="bi bi-boxes"></i> Lihat Laporan Stok</a>
                        @else
                            <div class="small text-muted text-center">Beri tahu admin untuk restok</div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

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
        </div>

        <div class="content-body">

            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script src="{{ asset('js/notifikasi.js') }}"></script>
@php
    $flashPopup = [
        'sukses' => session('success'),
        'gagal' => session('error'),
        'validasi' => request()->routeIs('akun.*') ? [] : $errors->all(),
        'stokTurun' => session('peringatan_stok', []),
    ];
@endphp
<script>
    // Pesan dari server (flash session) ditampilkan sebagai pop-up
    document.addEventListener('DOMContentLoaded', function () {
        const flash = @json($flashPopup);
        const urlStok = @json(auth()->user()->role === 'admin' ? route('stok.index') : null);

        // Urutan: hasil aksi dulu, lalu peringatan stok
        let antrian = Promise.resolve();
        if (flash.sukses) notif.sukses(flash.sukses);
        if (flash.gagal) antrian = antrian.then(() => notif.gagal(flash.gagal));
        if (flash.validasi.length) {
            antrian = antrian.then(() => notif.gagal(
                '<ul class="text-start mb-0 ps-3">' + flash.validasi.map(e => '<li>' + e.replace(/</g, '&lt;') + '</li>').join('') + '</ul>',
                'Data belum valid'
            ));
        }
        if (flash.stokTurun.length) {
            antrian = antrian.then(() => new Promise(r => setTimeout(r, flash.sukses ? 900 : 0)))
                .then(() => notif.peringatanStok(flash.stokTurun, {
                    judul: 'Stok Mulai Menipis!',
                    pengantar: 'Setelah aktivitas barusan, stok produk berikut sudah di bawah batas minimum:',
                    urlStok,
                }));
        }
        @if($tampilPopupStok)
        antrian = antrian.then(() => new Promise(r => setTimeout(r, 600)))
            .then(() => notif.peringatanStok(@json($notifStokJson), {
                judul: 'Pengingat Stok Menipis',
                pengantar: 'Ada {{ $notifStok->count() }} produk yang stoknya sudah mencapai batas minimum. Segera lakukan restok ya.',
                urlStok,
            }));
        @endif
    });
</script>
@yield('scripts')
</body>
</html>
