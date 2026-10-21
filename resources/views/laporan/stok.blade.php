@extends('layouts.app')
@section('title', 'Laporan Stok Barang')

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
        <div>
            <label class="form-label small">Status</label>
            <select name="status" class="form-select">
                <option value="">Semua</option>
                <option value="aman" @selected(request('status') === 'aman')>Aman</option>
                <option value="menipis" @selected(request('status') === 'menipis')>Menipis</option>
                <option value="habis" @selected(request('status') === 'habis')>Habis</option>
            </select>
        </div>
        <button class="btn btn-emas">Tampilkan</button>
        <a href="{{ route('laporan.stok.pdf', request()->query()) }}" class="btn btn-outline-secondary ms-auto">
            <i class="bi bi-file-earmark-pdf"></i> Export PDF
        </a>
    </form>
</div>

<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3">
        <div class="stat-card"><div><div class="stat-label">Jumlah Produk</div><div class="stat-value" style="font-size:1.2rem;">{{ $ringkasan['jumlah_produk'] }}</div></div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card"><div><div class="stat-label">Total Stok Akhir</div><div class="stat-value" style="font-size:1.2rem;">{{ number_format($ringkasan['total_stok'], 0, ',', '.') }}</div></div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card"><div><div class="stat-label">Nilai Persediaan</div><div class="stat-value" style="font-size:1.2rem;">Rp {{ number_format($ringkasan['nilai_persediaan'], 0, ',', '.') }}</div></div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card"><div><div class="stat-label">Perlu Restok</div><div class="stat-value text-danger" style="font-size:1.2rem;">{{ $ringkasan['perlu_restok'] }}</div></div></div>
    </div>
</div>

<div class="card-panel">
    <h6 class="fw-bold mb-1">Rekap Stok per Produk</h6>
    <p class="text-muted small mb-3">Stok Akhir = Stok Awal + Masuk − Keluar. Nilai persediaan dihitung dari harga jual.</p>
    <div class="table-responsive">
        <table class="table table-app align-middle">
            <thead>
                <tr>
                    <th>Kode</th><th>Produk</th><th>Kategori</th>
                    <th class="text-end">Stok Awal</th><th class="text-end">Masuk</th><th class="text-end">Keluar</th>
                    <th class="text-end">Stok Akhir</th><th class="text-end">Nilai</th><th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
            @forelse($baris as $r)
                <tr>
                    <td class="small">{{ $r['kode'] }}</td>
                    <td>{{ $r['nama'] }}</td>
                    <td class="small">{{ $r['kategori'] }}</td>
                    <td class="text-end">{{ $r['stok_awal'] }}</td>
                    <td class="text-end text-success">{{ $r['masuk'] ? '+'.$r['masuk'] : '0' }}</td>
                    <td class="text-end text-danger">{{ $r['keluar'] ? '−'.$r['keluar'] : '0' }}</td>
                    <td class="text-end fw-semibold">{{ $r['stok_akhir'] }}</td>
                    <td class="text-end">Rp {{ number_format($r['nilai'], 0, ',', '.') }}</td>
                    <td class="text-center">
                        @if($r['status'] === 'habis')
                            <span class="badge bg-danger">Habis</span>
                        @elseif($r['status'] === 'menipis')
                            <span class="badge bg-danger-subtle text-danger">Menipis</span>
                        @else
                            <span class="badge bg-success-subtle text-success">Aman</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="text-center text-muted py-4">Tidak ada produk</td></tr>
            @endforelse
            </tbody>
            @if($baris->isNotEmpty())
            <tfoot>
                <tr class="fw-bold">
                    <td colspan="4">Total</td>
                    <td class="text-end text-success">+{{ $ringkasan['total_masuk'] }}</td>
                    <td class="text-end text-danger">−{{ $ringkasan['total_keluar'] }}</td>
                    <td class="text-end">{{ $ringkasan['total_stok'] }}</td>
                    <td class="text-end">Rp {{ number_format($ringkasan['nilai_persediaan'], 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
