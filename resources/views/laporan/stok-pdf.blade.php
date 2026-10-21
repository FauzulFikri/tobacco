<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        h2 { margin-bottom: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 14px; }
        th, td { border: 1px solid #ccc; padding: 5px 7px; text-align: left; }
        th { background: #f2f2f2; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .ringkasan td { border: none; padding: 3px 8px; }
        tfoot td { font-weight: bold; background: #fafafa; }
    </style>
</head>
<body>
    <h2>Laporan Stok Barang - Istana Tembakau</h2>
    <p>Periode: {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</p>

    <table class="ringkasan">
        <tr><td>Jumlah Produk</td><td>: {{ $ringkasan['jumlah_produk'] }}</td></tr>
        <tr><td>Total Barang Masuk</td><td>: {{ $ringkasan['total_masuk'] }}</td></tr>
        <tr><td>Total Barang Keluar</td><td>: {{ $ringkasan['total_keluar'] }}</td></tr>
        <tr><td>Total Stok Akhir</td><td>: {{ $ringkasan['total_stok'] }}</td></tr>
        <tr><td>Nilai Persediaan</td><td>: Rp {{ number_format($ringkasan['nilai_persediaan'], 0, ',', '.') }}</td></tr>
        <tr><td>Produk Perlu Restok</td><td>: {{ $ringkasan['perlu_restok'] }}</td></tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>No</th><th>Kode</th><th>Produk</th><th>Kategori</th>
                <th class="text-end">Stok Awal</th><th class="text-end">Masuk</th><th class="text-end">Keluar</th>
                <th class="text-end">Stok Akhir</th><th class="text-end">Stok Min.</th><th class="text-end">Nilai (Rp)</th><th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
        @forelse($baris as $i => $r)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $r['kode'] }}</td>
                <td>{{ $r['nama'] }}</td>
                <td>{{ $r['kategori'] }}</td>
                <td class="text-end">{{ $r['stok_awal'] }}</td>
                <td class="text-end">{{ $r['masuk'] }}</td>
                <td class="text-end">{{ $r['keluar'] }}</td>
                <td class="text-end">{{ $r['stok_akhir'] }}</td>
                <td class="text-end">{{ $r['stok_minimum'] }}</td>
                <td class="text-end">{{ number_format($r['nilai'], 0, ',', '.') }}</td>
                <td class="text-center">{{ ucfirst($r['status']) }}</td>
            </tr>
        @empty
            <tr><td colspan="11">Tidak ada data</td></tr>
        @endforelse
        </tbody>
        @if($baris->isNotEmpty())
        <tfoot>
            <tr>
                <td colspan="5">Total</td>
                <td class="text-end">{{ $ringkasan['total_masuk'] }}</td>
                <td class="text-end">{{ $ringkasan['total_keluar'] }}</td>
                <td class="text-end">{{ $ringkasan['total_stok'] }}</td>
                <td></td>
                <td class="text-end">{{ number_format($ringkasan['nilai_persediaan'], 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <p style="margin-top:10px; color:#777;">Dicetak {{ now()->format('d/m/Y H:i') }}</p>
</body>
</html>
