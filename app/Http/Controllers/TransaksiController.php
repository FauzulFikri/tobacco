<?php

namespace App\Http\Controllers;

use App\Models\DetailTransaksi;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $kategoriId = $request->get('kategori');

        $query = Produk::with('kategori')->where('stok', '>', 0);
        if ($kategoriId) {
            $query->where('kategori_id', $kategoriId);
        }
        if ($cari = $request->get('cari')) {
            $query->where(function ($q) use ($cari) {
                $q->where('nama_produk', 'like', "%{$cari}%")
                    ->orWhere('kode_produk', 'like', "%{$cari}%");
            });
        }

        $produks = $query->orderBy('nama_produk')->get();
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        $riwayat = Transaksi::with('user')->latest()->take(10)->get();

        return view('transaksi.index', compact('produks', 'kategoris', 'riwayat'));
    }

    /**
     * Simpan transaksi baru dari keranjang POS.
     * Perhitungan mengikuti rumus di proposal:
     * Total = Jumlah x Harga, Diskon = Total x %, Bayar = Total - Diskon, Kembalian = Uang - Bayar
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produks,id',
            'items.*.qty' => 'required|integer|min:1',
            'diskon_persen' => 'nullable|integer|min:0|max:100',
            'metode_pembayaran' => 'required|in:cash,transfer,qris',
            'uang_bayar' => 'required|integer|min:0',
        ]);

        $transaksi = DB::transaction(function () use ($validated) {
            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $produk = Produk::lockForUpdate()->findOrFail($item['produk_id']);

                if ($produk->stok < $item['qty']) {
                    abort(422, "Stok {$produk->nama_produk} tidak mencukupi.");
                }

                $subtotalItem = $produk->harga * $item['qty'];
                $subtotal += $subtotalItem;

                $itemsData[] = [
                    'produk' => $produk,
                    'qty' => $item['qty'],
                    'subtotal' => $subtotalItem,
                ];
            }

            $diskonPersen = $validated['diskon_persen'] ?? 0;
            $diskonNominal = (int) round($subtotal * $diskonPersen / 100);
            $total = $subtotal - $diskonNominal;

            // Non-tunai (transfer/QRIS) dianggap dibayar pas, tidak ada kembalian
            if ($validated['metode_pembayaran'] !== 'cash') {
                $uangBayar = $total;
                $kembalian = 0;
            } else {
                $uangBayar = $validated['uang_bayar'];
                $kembalian = $uangBayar - $total;

                if ($kembalian < 0) {
                    abort(422, 'Uang bayar kurang dari total belanja.');
                }
            }

            $transaksi = Transaksi::create([
                'no_transaksi' => 'TRX-' . now()->format('ymd') . '-' . str_pad((string) (Transaksi::whereDate('created_at', now())->count() + 1), 4, '0', STR_PAD_LEFT),
                'user_id' => Auth::id(),
                'subtotal' => $subtotal,
                'diskon_persen' => $diskonPersen,
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'total' => $total,
                'bayar' => $uangBayar,
                'kembalian' => $kembalian,
            ]);

            foreach ($itemsData as $data) {
                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'produk_id' => $data['produk']->id,
                    'nama_produk' => $data['produk']->nama_produk,
                    'harga_satuan' => $data['produk']->harga,
                    'qty' => $data['qty'],
                    'subtotal' => $data['subtotal'],
                ]);

                $data['produk']->decrement('stok', $data['qty']);
            }

            return $transaksi;
        });

        return response()->json([
            'success' => true,
            'no_transaksi' => $transaksi->no_transaksi,
            'redirect' => route('transaksi.nota', $transaksi->id),
        ]);
    }

    public function nota(Transaksi $transaksi)
    {
        $transaksi->load('detail', 'user');

        return view('transaksi.nota', compact('transaksi'));
    }
}
