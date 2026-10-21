@php($periode = request()->only('dari', 'sampai'))
<ul class="nav nav-pills laporan-tabs mb-3 flex-nowrap overflow-auto">
    <li class="nav-item">
        <a class="nav-link text-nowrap {{ request()->routeIs('laporan.index') ? 'active' : '' }}" href="{{ route('laporan.index', $periode) }}">
            <i class="bi bi-graph-up"></i> Penjualan
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-nowrap {{ request()->routeIs('laporan.produk-terjual') ? 'active' : '' }}" href="{{ route('laporan.produk-terjual', $periode) }}">
            <i class="bi bi-trophy"></i> Produk Terjual
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-nowrap {{ request()->routeIs('laporan.stok') ? 'active' : '' }}" href="{{ route('laporan.stok', $periode) }}">
            <i class="bi bi-boxes"></i> Stok Barang
        </a>
    </li>
</ul>
