@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Penjualan Hari Ini</div>
                <div class="stat-value">Rp {{ number_format($totalPenjualanHariIni, 0, ',', '.') }}</div>
            </div>
            <div class="stat-icon" style="background:#fdeee0; color:#e8842c;"><i class="bi bi-bag-check"></i></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Transaksi</div>
                <div class="stat-value">{{ $totalTransaksiHariIni }}</div>
            </div>
            <div class="stat-icon" style="background:#e6f7ec; color:#2ea86a;"><i class="bi bi-receipt"></i></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div>
                <div class="stat-label">Produk Terjual</div>
                <div class="stat-value">{{ $produkTerjualHariIni }}</div>
            </div>
            <div class="stat-icon" style="background:#e7f0fd; color:#3a7bd5;"><i class="bi bi-box-seam"></i></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div>
                <div class="stat-label">Stok Menipis</div>
                <div class="stat-value">{{ $stokMenipis }}</div>
            </div>
            <div class="stat-icon" style="background:#fdeaea; color:#e34848;"><i class="bi bi-exclamation-triangle"></i></div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card-panel">
            <h6 class="fw-bold mb-3">Grafik Penjualan 7 Hari Terakhir</h6>
            <canvas id="grafikPenjualan" height="120"></canvas>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card-panel">
            <h6 class="fw-bold mb-3">Penjualan Terbaru</h6>
            <table class="table table-app table-sm mb-0">
                <thead><tr><th>No. Transaksi</th><th>Waktu</th><th class="text-end">Total</th></tr></thead>
                <tbody>
                @forelse($penjualanTerbaru as $trx)
                    <tr>
                        <td>{{ $trx->no_transaksi }}</td>
                        <td>{{ $trx->created_at->format('d/m H:i') }}</td>
                        <td class="text-end">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center text-muted py-3">Belum ada transaksi</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('grafikPenjualan'), {
    type: 'line',
    data: {
        labels: {!! json_encode(collect($grafik)->pluck('tanggal')) !!},
        datasets: [{
            label: 'Penjualan',
            data: {!! json_encode(collect($grafik)->pluck('total')) !!},
            borderColor: '#c9a35f',
            backgroundColor: 'rgba(201,163,95,.15)',
            tension: .35,
            fill: true,
            pointBackgroundColor: '#6b4226',
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: { y: { ticks: { callback: v => 'Rp' + (v/1000) + 'rb' } } }
    }
});
</script>
@endsection
