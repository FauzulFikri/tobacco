@extends('layouts.app')
@section('title', 'Laporan Penjualan')

@section('content')
<div class="card-panel mb-3">
    <form method="GET" class="d-flex flex-wrap align-items-end gap-2">
        <div>
            <label class="form-label small">Periode</label>
            <input type="date" name="dari" value="{{ $dari }}" class="form-control">
        </div>
        <span class="pb-2">s/d</span>
        <div>
            <input type="date" name="sampai" value="{{ $sampai }}" class="form-control">
        </div>
        <button class="btn btn-emas">Tampilkan</button>
        <a href="{{ route('laporan.export-pdf', request()->query()) }}" class="btn btn-outline-secondary ms-auto">
            <i class="bi bi-file-earmark-pdf"></i> Export PDF
        </a>
    </form>
</div>

<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3">
        <div class="stat-card"><div><div class="stat-label">Total Penjualan</div><div class="stat-value" style="font-size:1.2rem;">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</div></div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card"><div><div class="stat-label">Total Transaksi</div><div class="stat-value" style="font-size:1.2rem;">{{ $totalTransaksi }}</div></div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card"><div><div class="stat-label">Total Produk Terjual</div><div class="stat-value" style="font-size:1.2rem;">{{ $totalProdukTerjual }}</div></div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card"><div><div class="stat-label">Rata-rata Transaksi</div><div class="stat-value" style="font-size:1.2rem;">Rp {{ number_format($rataRata, 0, ',', '.') }}</div></div></div>
    </div>
</div>

<div class="card-panel">
    <h6 class="fw-bold mb-3">Tabel Penjualan</h6>
    <table class="table table-app">
        <thead><tr><th>Tanggal</th><th>Total Transaksi</th><th class="text-end">Total Penjualan</th></tr></thead>
        <tbody>
        @forelse($tabelHarian as $row)
            <tr>
                <td>{{ $row['tanggal'] }}</td>
                <td>{{ $row['total_transaksi'] }}</td>
                <td class="text-end">Rp {{ number_format($row['total_penjualan'], 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr><td colspan="3" class="text-center text-muted py-4">Tidak ada data pada periode ini</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
