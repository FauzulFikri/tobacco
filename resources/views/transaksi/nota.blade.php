@extends('layouts.app')
@section('title', 'Nota Transaksi')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card-panel">
            <div class="text-center mb-3">
                <i class="bi bi-check-circle-fill text-success" style="font-size:2.5rem;"></i>
                <h5 class="fw-bold mt-2">Transaksi Berhasil</h5>
                <div class="text-muted">{{ $transaksi->no_transaksi }}</div>
            </div>
            <hr>
            <table class="table table-sm mb-0">
                <tbody>
                @foreach($transaksi->detail as $item)
                    <tr>
                        <td>{{ $item->nama_produk }} <span class="text-muted">x{{ $item->qty }}</span></td>
                        <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <hr>
            <div class="d-flex justify-content-between small"><span>Subtotal</span><span>Rp {{ number_format($transaksi->subtotal, 0, ',', '.') }}</span></div>
            <div class="d-flex justify-content-between small"><span>Diskon</span><span>{{ $transaksi->diskon_persen }}%</span></div>
            <div class="d-flex justify-content-between small"><span>Metode Pembayaran</span><span class="text-capitalize">{{ $transaksi->metode_pembayaran }}</span></div>
            <div class="d-flex justify-content-between fw-bold fs-5"><span>Total</span><span>Rp {{ number_format($transaksi->total, 0, ',', '.') }}</span></div>
            <div class="d-flex justify-content-between small"><span>Bayar</span><span>Rp {{ number_format($transaksi->bayar, 0, ',', '.') }}</span></div>
            <div class="d-flex justify-content-between small"><span>Kembalian</span><span>Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</span></div>
            <div class="text-muted small mt-2">Kasir: {{ $transaksi->user->name }} • {{ $transaksi->created_at->format('d/m/Y H:i') }}</div>

            <div class="d-flex gap-2 mt-4">
                <button class="btn btn-outline-secondary w-50" onclick="window.print()">Cetak</button>
                <a href="{{ route('transaksi.index') }}" class="btn btn-emas w-50">Transaksi Baru</a>
            </div>
        </div>
    </div>
</div>
@endsection
