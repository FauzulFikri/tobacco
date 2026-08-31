<?php

namespace App\Http\Controllers;

use App\Models\DetailTransaksi;
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $hariIni = Carbon::today();

        $totalPenjualanHariIni = Transaksi::whereDate('created_at', $hariIni)->sum('total');
        $totalTransaksiHariIni = Transaksi::whereDate('created_at', $hariIni)->count();
        $produkTerjualHariIni = DetailTransaksi::whereHas('transaksi', function ($q) use ($hariIni) {
            $q->whereDate('created_at', $hariIni);
        })->sum('qty');
        $stokMenipis = Produk::stokMenipis()->count();

        // Grafik penjualan 7 hari terakhir
        $grafik = [];
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = Carbon::today()->subDays($i);
            $grafik[] = [
                'tanggal' => $tanggal->format('d/m'),
                'total' => (int) Transaksi::whereDate('created_at', $tanggal)->sum('total'),
            ];
        }

        $penjualanTerbaru = Transaksi::with('user')->latest()->take(5)->get();

        return view('dashboard.index', compact(
            'totalPenjualanHariIni',
            'totalTransaksiHariIni',
            'produkTerjualHariIni',
            'stokMenipis',
            'grafik',
            'penjualanTerbaru'
        ));
    }
}
