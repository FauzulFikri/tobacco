<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\StokMutasi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

/**
 * Pengelolaan stok barang: pencatatan barang masuk & barang keluar (kartu stok).
 */
class StokController extends Controller
{
    public function index(Request $request)
    {
        $dari = $request->get('dari', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->get('sampai', Carbon::now()->format('Y-m-d'));

        $query = StokMutasi::with(['user', 'transaksi'])
            ->whereBetween('created_at', [Carbon::parse($dari)->startOfDay(), Carbon::parse($sampai)->endOfDay()]);

        if ($jenis = $request->get('jenis')) {
            $query->where('jenis', $jenis);
        }
        if ($produkId = $request->get('produk')) {
            $query->where('produk_id', $produkId);
        }

        $ringkasan = (clone $query)->selectRaw('jenis, SUM(qty) as total')->groupBy('jenis')->pluck('total', 'jenis');
        $totalMasuk = (int) ($ringkasan['masuk'] ?? 0);
        $totalKeluar = (int) ($ringkasan['keluar'] ?? 0);

        $mutasis = $query->latest()->latest('id')->paginate(15)->withQueryString();
        $produks = Produk::orderBy('nama_produk')->get();
        $stokMenipis = Produk::stokMenipis()->count();

        return view('stok.index', compact(
            'mutasis', 'produks', 'dari', 'sampai', 'totalMasuk', 'totalKeluar', 'stokMenipis'
        ));
    }

    public function store(Request $request)
    {
        $jenis = $request->input('jenis');
        $sumberValid = array_keys($jenis === 'masuk' ? StokMutasi::SUMBER_MASUK : StokMutasi::SUMBER_KELUAR);

        $data = $request->validate([
            'jenis' => 'required|in:masuk,keluar',
            'produk_id' => 'required|exists:produks,id',
            'qty' => 'required|integer|min:1',
            'sumber' => ['required', Rule::in($sumberValid)],
            'keterangan' => 'nullable|string|max:255',
        ]);

        $produk = Produk::findOrFail($data['produk_id']);

        if ($data['jenis'] === 'keluar' && $produk->stok < $data['qty']) {
            return back()->withInput()->with('error', "Stok {$produk->nama_produk} hanya {$produk->stok}, tidak bisa dikeluarkan {$data['qty']}.");
        }

        StokMutasi::catat($produk, $data['jenis'], $data['qty'], $data['sumber'], $data['keterangan'] ?? null);

        $label = $data['jenis'] === 'masuk' ? 'Barang masuk' : 'Barang keluar';

        if ($data['jenis'] === 'keluar' && ($menipis = Produk::yangMenipis([$produk->id]))) {
            session()->flash('peringatan_stok', $menipis);
        }

        return back()->with('success', "{$label} {$produk->nama_produk} sebanyak {$data['qty']} berhasil dicatat.");
    }
}
