@extends('layouts.app')
@section('title', 'Stok Barang')

@section('content')
<div class="row g-3 mb-3">
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div>
                <div class="stat-label">Barang Masuk (periode)</div>
                <div class="stat-value">{{ number_format($totalMasuk, 0, ',', '.') }}</div>
            </div>
            <div class="stat-icon" style="background:#e6f7ec; color:#2ea86a;"><i class="bi bi-box-arrow-in-down"></i></div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div>
                <div class="stat-label">Barang Keluar (periode)</div>
                <div class="stat-value">{{ number_format($totalKeluar, 0, ',', '.') }}</div>
            </div>
            <div class="stat-icon" style="background:#fdeee0; color:#e8842c;"><i class="bi bi-box-arrow-up"></i></div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div>
                <div class="stat-label">Produk Stok Menipis</div>
                <div class="stat-value">{{ $stokMenipis }}</div>
            </div>
            <div class="stat-icon" style="background:#fde8e8; color:#d64545;"><i class="bi bi-exclamation-triangle"></i></div>
        </div>
    </div>
</div>

<div class="card-panel">
    <div class="d-flex justify-content-between align-items-end mb-3 flex-wrap gap-2">
        <form method="GET" class="d-flex flex-wrap align-items-end gap-2">
            <div>
                <label class="form-label small">Periode</label>
                <input type="date" name="dari" value="{{ $dari }}" class="form-control">
            </div>
            <span class="pb-2">s/d</span>
            <div><input type="date" name="sampai" value="{{ $sampai }}" class="form-control"></div>
            <div>
                <label class="form-label small">Jenis</label>
                <select name="jenis" class="form-select">
                    <option value="">Semua</option>
                    <option value="masuk" @selected(request('jenis') === 'masuk')>Barang Masuk</option>
                    <option value="keluar" @selected(request('jenis') === 'keluar')>Barang Keluar</option>
                </select>
            </div>
            <div>
                <label class="form-label small">Produk</label>
                <select name="produk" class="form-select" style="max-width:220px;">
                    <option value="">Semua Produk</option>
                    @foreach($produks as $p)
                        <option value="{{ $p->id }}" @selected((string) request('produk') === (string) $p->id)>{{ $p->nama_produk }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn btn-outline-secondary">Tampilkan</button>
        </form>
        <div class="d-flex gap-2">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalMasuk">
                <i class="bi bi-box-arrow-in-down"></i> Barang Masuk
            </button>
            <button class="btn btn-emas" data-bs-toggle="modal" data-bs-target="#modalKeluar">
                <i class="bi bi-box-arrow-up"></i> Barang Keluar
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-app align-middle">
            <thead>
                <tr>
                    <th>Waktu</th><th>Produk</th><th>Jenis</th><th>Sumber / Alasan</th>
                    <th class="text-end">Jumlah</th><th class="text-center">Stok</th><th>Keterangan</th><th>Petugas</th>
                </tr>
            </thead>
            <tbody>
            @forelse($mutasis as $m)
                <tr>
                    <td class="small text-nowrap">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $m->nama_produk }}</td>
                    <td>
                        @if($m->jenis === 'masuk')
                            <span class="badge bg-success-subtle text-success">Masuk</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger">Keluar</span>
                        @endif
                    </td>
                    <td class="small">{{ $m->sumber_label }}</td>
                    <td class="text-end fw-semibold {{ $m->jenis === 'masuk' ? 'text-success' : 'text-danger' }}">
                        {{ $m->jenis === 'masuk' ? '+' : '−' }}{{ $m->qty }}
                    </td>
                    <td class="text-center small text-nowrap">{{ $m->stok_sebelum }} → <strong>{{ $m->stok_sesudah }}</strong></td>
                    <td class="small">
                        @if($m->transaksi)
                            <a href="{{ route('transaksi.nota', $m->transaksi) }}">{{ $m->keterangan }}</a>
                        @else
                            {{ $m->keterangan ?? '-' }}
                        @endif
                    </td>
                    <td class="small">{{ $m->user->name ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Belum ada mutasi stok pada periode ini</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $mutasis->links() }}
</div>

@foreach(['masuk' => \App\Models\StokMutasi::SUMBER_MASUK, 'keluar' => \App\Models\StokMutasi::SUMBER_KELUAR] as $jenis => $sumbers)
<div class="modal fade" id="modal{{ ucfirst($jenis) }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('stok.store') }}" method="POST">
                @csrf
                <input type="hidden" name="jenis" value="{{ $jenis }}">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">{{ $jenis === 'masuk' ? 'Catat Barang Masuk' : 'Catat Barang Keluar' }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label small">Produk</label>
                        <select class="form-select" name="produk_id" required>
                            <option value="">Pilih produk</option>
                            @foreach($produks as $p)
                                <option value="{{ $p->id }}">{{ $p->kode_produk }} - {{ $p->nama_produk }} (stok: {{ $p->stok }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Jumlah</label>
                        <input type="number" class="form-control" name="qty" min="1" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">{{ $jenis === 'masuk' ? 'Sumber' : 'Alasan' }}</label>
                        <select class="form-select" name="sumber" required>
                            @foreach($sumbers as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Keterangan</label>
                        <input type="text" class="form-control" name="keterangan" maxlength="255"
                               placeholder="{{ $jenis === 'masuk' ? 'mis. No. faktur / nama supplier' : 'mis. kemasan rusak' }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn {{ $jenis === 'masuk' ? 'btn-success' : 'btn-emas' }} w-100">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
