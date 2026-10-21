@extends('layouts.app')
@section('title', 'Riwayat Transaksi')

@php($labelMetode = ['cash' => 'Tunai', 'transfer' => 'Transfer', 'qris' => 'QRIS'])

@section('content')
<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3">
        <div class="stat-card"><div><div class="stat-label">Jumlah Transaksi</div><div class="stat-value" style="font-size:1.2rem;">{{ $totalTransaksi }}</div></div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card"><div><div class="stat-label">Total Penjualan</div><div class="stat-value" style="font-size:1.2rem;">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</div></div></div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="stat-card">
            <div class="w-100">
                <div class="stat-label mb-1">Per Metode Pembayaran</div>
                <div class="d-flex flex-wrap gap-3 small">
                    @foreach($labelMetode as $key => $label)
                        <div><span class="text-muted">{{ $label }}:</span> <strong>Rp {{ number_format($perMetode[$key] ?? 0, 0, ',', '.') }}</strong></div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card-panel">
    <form method="GET" class="d-flex flex-wrap align-items-end gap-2 mb-3">
        <div>
            <label class="form-label small">Periode</label>
            <input type="date" name="dari" value="{{ $dari }}" class="form-control">
        </div>
        <span class="pb-2">s/d</span>
        <div><input type="date" name="sampai" value="{{ $sampai }}" class="form-control"></div>
        <div>
            <label class="form-label small">Cari</label>
            <input type="text" name="cari" value="{{ request('cari') }}" class="form-control" placeholder="No. transaksi / produk" style="max-width:200px;">
        </div>
        <div>
            <label class="form-label small">Metode</label>
            <select name="metode" class="form-select">
                <option value="">Semua</option>
                @foreach($labelMetode as $key => $label)
                    <option value="{{ $key }}" @selected(request('metode') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label small">Kasir</label>
            <select name="kasir" class="form-select">
                <option value="">Semua</option>
                @foreach($kasirs as $k)
                    <option value="{{ $k->id }}" @selected((string) request('kasir') === (string) $k->id)>{{ $k->name }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-emas">Tampilkan</button>
        @if(request()->query())
            <a href="{{ route('transaksi.riwayat') }}" class="btn btn-light">Reset</a>
        @endif
    </form>

    <div class="table-responsive">
        <table class="table table-app align-middle">
            <thead>
                <tr>
                    <th>No. Transaksi</th><th>Waktu</th><th>Kasir</th><th>Produk</th>
                    <th>Metode</th><th class="text-end">Total</th><th class="text-center">Nota</th>
                </tr>
            </thead>
            <tbody>
            @forelse($transaksis as $t)
                <tr>
                    <td class="fw-semibold text-nowrap">{{ $t->no_transaksi }}</td>
                    <td class="small text-nowrap">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                    <td class="small">{{ $t->user->name ?? '-' }}</td>
                    <td class="small" style="max-width:280px;">
                        {{ $t->detail->map(fn ($d) => $d->nama_produk . ' ×' . $d->qty)->implode(', ') }}
                    </td>
                    <td><span class="badge bg-secondary-subtle text-secondary-emphasis">{{ $labelMetode[$t->metode_pembayaran] ?? $t->metode_pembayaran }}</span></td>
                    <td class="text-end text-nowrap">
                        Rp {{ number_format($t->total, 0, ',', '.') }}
                        @if($t->diskon_persen > 0)
                            <div class="small text-muted">diskon {{ $t->diskon_persen }}%</div>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('transaksi.nota', $t) }}" class="btn btn-sm btn-light" title="Lihat / cetak nota"><i class="bi bi-receipt"></i></a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada transaksi yang cocok</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $transaksis->links() }}
</div>
@endsection
