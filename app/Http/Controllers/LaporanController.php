<?php

namespace App\Http\Controllers;

use App\Models\DetailTransaksi;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\StokMutasi;
use App\Models\Transaksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $dari = $request->get('dari', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->get('sampai', Carbon::now()->format('Y-m-d'));

        [$totalPenjualan, $totalTransaksi, $totalProdukTerjual, $rataRata, $tabelHarian] = $this->hitungRekap($dari, $sampai);

        return view('laporan.index', compact(
            'dari', 'sampai', 'totalPenjualan', 'totalTransaksi', 'totalProdukTerjual', 'rataRata', 'tabelHarian'
        ));
    }

    public function exportPdf(Request $request)
    {
        $dari = $request->get('dari', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->get('sampai', Carbon::now()->format('Y-m-d'));

        [$totalPenjualan, $totalTransaksi, $totalProdukTerjual, $rataRata, $tabelHarian] = $this->hitungRekap($dari, $sampai);

        $pdf = Pdf::loadView('laporan.pdf', compact(
            'dari', 'sampai', 'totalPenjualan', 'totalTransaksi', 'totalProdukTerjual', 'rataRata', 'tabelHarian'
        ));

        return $pdf->download('laporan-penjualan-' . $dari . '-sd-' . $sampai . '.pdf');
    }

    /* =========================================================
     * LAPORAN STOK BARANG
     * Stok awal, barang masuk, barang keluar, dan stok akhir per produk
     * dalam periode tertentu (diambil dari kartu stok / stok_mutasis).
     * ========================================================= */
    public function stok(Request $request)
    {
        return view('laporan.stok', $this->dataStok($request));
    }

    public function stokPdf(Request $request)
    {
        $data = $this->dataStok($request);
        $pdf = Pdf::loadView('laporan.stok-pdf', $data)->setPaper('a4', 'landscape');

        return $pdf->download('laporan-stok-' . $data['dari'] . '-sd-' . $data['sampai'] . '.pdf');
    }

    private function dataStok(Request $request): array
    {
        $dari = $request->get('dari', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->get('sampai', Carbon::now()->format('Y-m-d'));
        $kategoriId = $request->get('kategori');
        $status = $request->get('status');

        $awal = Carbon::parse($dari)->startOfDay();
        $akhir = Carbon::parse($sampai)->endOfDay();

        // Total masuk & keluar per produk dalam periode
        $mutasiPeriode = StokMutasi::whereBetween('created_at', [$awal, $akhir])
            ->whereNotNull('produk_id')
            ->selectRaw('produk_id, jenis, SUM(qty) as total')
            ->groupBy('produk_id', 'jenis')
            ->get()
            ->groupBy('produk_id');

        // Stok akhir periode = stok_sesudah dari mutasi terakhir sampai tanggal akhir
        $idTerakhir = StokMutasi::where('created_at', '<=', $akhir)
            ->whereNotNull('produk_id')
            ->selectRaw('MAX(id) as id')
            ->groupBy('produk_id')
            ->pluck('id');
        $stokAkhirPeriode = StokMutasi::whereIn('id', $idTerakhir)->pluck('stok_sesudah', 'produk_id');
        $punyaRiwayat = StokMutasi::whereNotNull('produk_id')->distinct()->pluck('produk_id')->flip();

        $produks = Produk::with('kategori')
            ->when($kategoriId, fn ($q) => $q->where('kategori_id', $kategoriId))
            ->orderBy('nama_produk')
            ->get();

        $baris = $produks->map(function (Produk $p) use ($mutasiPeriode, $stokAkhirPeriode, $punyaRiwayat) {
            $mutasi = $mutasiPeriode->get($p->id, collect());
            $masuk = (int) $mutasi->firstWhere('jenis', 'masuk')?->total;
            $keluar = (int) $mutasi->firstWhere('jenis', 'keluar')?->total;

            // Produk lama yang belum punya riwayat mutasi: pakai stok saat ini
            $stokAkhir = $punyaRiwayat->has($p->id)
                ? (int) ($stokAkhirPeriode[$p->id] ?? 0)
                : $p->stok;
            $stokAwal = $stokAkhir - $masuk + $keluar;

            $kondisi = $stokAkhir <= 0 ? 'habis' : ($stokAkhir <= $p->stok_minimum ? 'menipis' : 'aman');

            return [
                'kode' => $p->kode_produk,
                'nama' => $p->nama_produk,
                'kategori' => $p->kategori->nama_kategori ?? '-',
                'harga' => $p->harga,
                'stok_minimum' => $p->stok_minimum,
                'stok_awal' => $stokAwal,
                'masuk' => $masuk,
                'keluar' => $keluar,
                'stok_akhir' => $stokAkhir,
                'nilai' => $stokAkhir * $p->harga,
                'status' => $kondisi,
            ];
        });

        if ($status) {
            $baris = $baris->where('status', $status)->values();
        }

        $ringkasan = [
            'jumlah_produk' => $baris->count(),
            'total_stok' => $baris->sum('stok_akhir'),
            'nilai_persediaan' => $baris->sum('nilai'),
            'total_masuk' => $baris->sum('masuk'),
            'total_keluar' => $baris->sum('keluar'),
            'perlu_restok' => $baris->whereIn('status', ['menipis', 'habis'])->count(),
        ];

        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return compact('dari', 'sampai', 'baris', 'ringkasan', 'kategoris');
    }

    /* =========================================================
     * LAPORAN PRODUK TERJUAL
     * Rekap jumlah terjual & nilai penjualan per produk dalam periode.
     * ========================================================= */
    public function produkTerjual(Request $request)
    {
        return view('laporan.produk-terjual', $this->dataProdukTerjual($request));
    }

    public function produkTerjualPdf(Request $request)
    {
        $data = $this->dataProdukTerjual($request);
        $pdf = Pdf::loadView('laporan.produk-terjual-pdf', $data);

        return $pdf->download('laporan-produk-terjual-' . $data['dari'] . '-sd-' . $data['sampai'] . '.pdf');
    }

    private function dataProdukTerjual(Request $request): array
    {
        $dari = $request->get('dari', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->get('sampai', Carbon::now()->format('Y-m-d'));
        $kategoriId = $request->get('kategori');

        $baris = DetailTransaksi::query()
            ->join('transaksis', 'transaksis.id', '=', 'detail_transaksis.transaksi_id')
            ->leftJoin('produks', 'produks.id', '=', 'detail_transaksis.produk_id')
            ->leftJoin('kategoris', 'kategoris.id', '=', 'produks.kategori_id')
            ->whereBetween('transaksis.created_at', [
                Carbon::parse($dari)->startOfDay(),
                Carbon::parse($sampai)->endOfDay(),
            ])
            ->when($kategoriId, fn ($q) => $q->where('produks.kategori_id', $kategoriId))
            ->groupBy('detail_transaksis.produk_id')
            ->selectRaw('
                detail_transaksis.produk_id,
                MAX(produks.kode_produk) as kode,
                MAX(detail_transaksis.nama_produk) as nama,
                MAX(kategoris.nama_kategori) as kategori,
                SUM(detail_transaksis.qty) as qty,
                COUNT(DISTINCT detail_transaksis.transaksi_id) as jumlah_transaksi,
                SUM(detail_transaksis.subtotal) as total
            ')
            ->orderByDesc('qty')
            ->orderByDesc('total')
            ->get();

        $totalQty = (int) $baris->sum('qty');
        $totalNilai = (int) $baris->sum('total');

        $baris = $baris->map(fn ($r) => [
            'kode' => $r->kode ?? '-',
            'nama' => $r->nama,
            'kategori' => $r->kategori ?? '-',
            'qty' => (int) $r->qty,
            'jumlah_transaksi' => (int) $r->jumlah_transaksi,
            'total' => (int) $r->total,
            'persen' => $totalQty > 0 ? round($r->qty / $totalQty * 100, 1) : 0,
        ]);

        $ringkasan = [
            'jenis_produk' => $baris->count(),
            'total_qty' => $totalQty,
            'total_nilai' => $totalNilai,
            'terlaris' => $baris->first()['nama'] ?? '-',
        ];

        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return compact('dari', 'sampai', 'baris', 'ringkasan', 'kategoris');
    }

    private function hitungRekap(string $dari, string $sampai): array
    {
        $transaksis = Transaksi::whereBetween('created_at', [
            Carbon::parse($dari)->startOfDay(),
            Carbon::parse($sampai)->endOfDay(),
        ])->with('detail')->get();

        $totalPenjualan = $transaksis->sum('total');
        $totalTransaksi = $transaksis->count();
        $totalProdukTerjual = $transaksis->flatMap->detail->sum('qty');
        $rataRata = $totalTransaksi > 0 ? (int) round($totalPenjualan / $totalTransaksi) : 0;

        $tabelHarian = $transaksis
            ->groupBy(fn ($t) => $t->created_at->format('Y-m-d'))
            ->map(function ($grup, $tanggal) {
                return [
                    'tanggal' => Carbon::parse($tanggal)->format('d/m/Y'),
                    'total_transaksi' => $grup->count(),
                    'total_penjualan' => $grup->sum('total'),
                ];
            })
            ->sortByDesc('tanggal')
            ->values();

        return [$totalPenjualan, $totalTransaksi, $totalProdukTerjual, $rataRata, $tabelHarian];
    }
}
