@extends('layouts.app')
@section('title', 'Laporan Produk Terjual')

@section('content')
@include('laporan._tabs')

<div class="card-panel mb-3">
    <form method="GET" class="d-flex flex-wrap align-items-end gap-2">
        <div>
            <label class="form-label small">Periode</label>
            <input type="date" name="dari" value="{{ $dari }}" class="form-control">
        </div>
        <span class="pb-2">s/d</span>
        <div><input type="date" name="sampai" value="{{ $sampai }}" class="form-control"></div>
        <div>
            <label class="form-label small">Kategori</label>
            <select name="kategori" class="form-select">
                <option value="">Semua</option>
                @foreach($kategoris as $kat)
                    <option value="{{ $kat->id }}" @selected((string) request('kategori') === (string) $kat->id)>{{ $kat->nama_kategori }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-emas">Tampilkan</button>
        <a href="{{ route('laporan.produk-terjual.pdf', request()->query()) }}" class="btn btn-outline-secondary ms-auto">
            <i class="bi bi-file-earmark-pdf"></i> Export PDF
        </a>
    </form>
</div>

<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3">
        <div class="stat-card"><div><div class="stat-label">Produk Terlaris</div><div class="stat-value" style="font-size:1.05rem;">{{ $ringkasan['terlaris'] }}</div></div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card"><div><div class="stat-label">Total Unit Terjual</div><div class="stat-value" style="font-size:1.2rem;">{{ number_format($ringkasan['total_qty'], 0, ',', '.') }}</div></div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card"><div><div class="stat-label">Jenis Produk Terjual</div><div class="stat-value" style="font-size:1.2rem;">{{ $ringkasan['jenis_produk'] }}</div></div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card"><div><div class="stat-label">Penjualan Kotor</div><div class="stat-value" style="font-size:1.2rem;">Rp {{ number_format($ringkasan['total_nilai'], 0, ',', '.') }}</div></div></div>
    </div>
</div>

<div class="card-panel">
    <h6 class="fw-bold mb-1">Peringkat Produk Terjual</h6>
    <p class="text-muted small mb-3">Diurutkan dari jumlah terjual terbanyak. Nilai penjualan dihitung sebelum diskon transaksi.</p>
    <div class="table-responsive">
        <table class="table table-app align-middle">
            <thead>
                <tr>
                    <th>#</th><th>Kode</th><th>Produk</th><th>Kategori</th>
                    <th class="text-end">Terjual</th><th class="text-end">Transaksi</th>
                    <th class="text-end">Nilai Penjualan</th><th style="min-width:140px;">Porsi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($baris as $i => $r)
                <tr>
                    <td class="fw-bold">{{ $i + 1 }}</td>
                    <td class="small">{{ $r['kode'] }}</td>
                    <td>{{ $r['nama'] }}</td>
                    <td class="small">{{ $r['kategori'] }}</td>
                    <td class="text-end fw-semibold">{{ $r['qty'] }}</td>
                    <td class="text-end">{{ $r['jumlah_transaksi'] }}</td>
                    <td class="text-end">Rp {{ number_format($r['total'], 0, ',', '.') }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height:6px;">
                                <div class="progress-bar" style="width: {{ $r['persen'] }}%; background: var(--coklat-tombol);"></div>
                            </div>
                            <span class="small text-muted">{{ $r['persen'] }}%</span>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Belum ada produk terjual pada periode ini</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
