<?php

namespace App\Http\Controllers;

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
