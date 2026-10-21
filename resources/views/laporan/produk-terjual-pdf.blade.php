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
        .ringkasan td { border: none; padding: 4px 8px; }
    </style>
</head>
<body>
    <h2>Laporan Produk Terjual - Istana Tembakau</h2>
    <p>Periode: {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</p>

    <table class="ringkasan">
        <tr><td>Produk Terlaris</td><td>: {{ $ringkasan['terlaris'] }}</td></tr>
        <tr><td>Total Unit Terjual</td><td>: {{ $ringkasan['total_qty'] }}</td></tr>
        <tr><td>Jenis Produk Terjual</td><td>: {{ $ringkasan['jenis_produk'] }}</td></tr>
        <tr><td>Penjualan Kotor (sebelum diskon)</td><td>: Rp {{ number_format($ringkasan['total_nilai'], 0, ',', '.') }}</td></tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>No</th><th>Kode</th><th>Produk</th><th>Kategori</th>
                <th class="text-end">Terjual</th><th class="text-end">Transaksi</th>
                <th class="text-end">Nilai (Rp)</th><th class="text-end">Porsi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($baris as $i => $r)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $r['kode'] }}</td>
                <td>{{ $r['nama'] }}</td>
                <td>{{ $r['kategori'] }}</td>
                <td class="text-end">{{ $r['qty'] }}</td>
                <td class="text-end">{{ $r['jumlah_transaksi'] }}</td>
                <td class="text-end">{{ number_format($r['total'], 0, ',', '.') }}</td>
                <td class="text-end">{{ $r['persen'] }}%</td>
            </tr>
        @empty
            <tr><td colspan="8">Tidak ada data</td></tr>
        @endforelse
        </tbody>
    </table>

    <p style="margin-top:10px; color:#777;">Dicetak {{ now()->format('d/m/Y H:i') }}</p>
</body>
</html>
