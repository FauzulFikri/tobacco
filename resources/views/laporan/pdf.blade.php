<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2 { margin-bottom: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f2f2f2; }
        .text-end { text-align: right; }
        .ringkasan { width: auto; }
        .ringkasan td { border: none; padding: 4px 8px; }
    </style>
</head>
<body>
    <h2>Laporan Penjualan - Istana Tembakau</h2>
    <p>Periode: {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</p>

    <table class="ringkasan">
        <tr><td>Total Penjualan</td><td>: Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td></tr>
        <tr><td>Total Transaksi</td><td>: {{ $totalTransaksi }}</td></tr>
        <tr><td>Total Produk Terjual</td><td>: {{ $totalProdukTerjual }}</td></tr>
        <tr><td>Rata-rata Transaksi</td><td>: Rp {{ number_format($rataRata, 0, ',', '.') }}</td></tr>
    </table>

    <table>
        <thead><tr><th>Tanggal</th><th>Total Transaksi</th><th class="text-end">Total Penjualan</th></tr></thead>
        <tbody>
        @forelse($tabelHarian as $row)
            <tr>
                <td>{{ $row['tanggal'] }}</td>
                <td>{{ $row['total_transaksi'] }}</td>
                <td class="text-end">Rp {{ number_format($row['total_penjualan'], 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr><td colspan="3">Tidak ada data</td></tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>
