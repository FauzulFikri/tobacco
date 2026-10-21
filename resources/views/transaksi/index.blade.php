@extends('layouts.app')
@section('title', 'Transaksi / Kasir')

@section('content')
<div class="pos-grid">
    <div>
        <div class="d-flex gap-2 mb-3 flex-wrap align-items-center">
            <input type="text" id="cariProduk" class="form-control" style="max-width:220px; flex:1;" placeholder="Cari produk (nama / kode)">
            <select id="filterKategori" class="form-select" style="max-width:200px;">
                <option value="">Semua Kategori</option>
                @foreach($kategoris as $kat)
                    <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                @endforeach
            </select>
            <a href="{{ route('transaksi.riwayat') }}" class="btn btn-outline-secondary ms-auto">
                <i class="bi bi-clock-history"></i> Riwayat
            </a>
        </div>

        <div class="row g-3" id="daftarProduk">
            @foreach($produks as $produk)
            <div class="col-6 col-md-4 col-lg-3 produk-card" data-kategori="{{ $produk->kategori_id }}" data-nama="{{ strtolower($produk->nama_produk) }}" data-kode="{{ strtolower($produk->kode_produk) }}">
                <div class="produk-item"
                     onclick="tambahKeKeranjang({{ $produk->id }}, '{{ addslashes($produk->nama_produk) }}', {{ $produk->harga }}, {{ $produk->stok }})">
                    @if($produk->gambar)
                        <img src="{{ asset('storage/'.$produk->gambar) }}" class="produk-foto" alt="{{ $produk->nama_produk }}">
                    @else
                        <div class="produk-icon-box"><i class="bi bi-box2"></i></div>
                    @endif
                    <div class="fw-semibold small">{{ $produk->nama_produk }}</div>
                    <div class="text-muted small">Rp {{ number_format($produk->harga, 0, ',', '.') }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="keranjang-box">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-bold mb-0"><i class="bi bi-cart3"></i> Keranjang</h6>
            <button class="btn btn-sm btn-link text-danger" onclick="kosongkanKeranjang()">Kosongkan</button>
        </div>

        <div id="isiKeranjang" style="max-height:280px; overflow-y:auto;">
            <p class="text-muted text-center small py-4" id="keranjangKosong">Belum ada produk dipilih</p>
        </div>

        <hr>

        <div class="d-flex justify-content-between small mb-1">
            <span>Subtotal</span><span id="txtSubtotal">Rp 0</span>
        </div>
        <div class="d-flex justify-content-between small mb-2 align-items-center">
            <span>Diskon</span>
            <div class="d-flex align-items-center gap-1">
                <input type="number" id="inputDiskon" class="form-control form-control-sm" style="width:60px;" value="0" min="0" max="100" onchange="hitungTotal()">
                <span>%</span>
            </div>
        </div>
        <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
            <span>Total</span><span id="txtTotal">Rp 0</span>
        </div>

        <div class="mb-2">
            <label class="form-label small fw-semibold">Metode Pembayaran</label>
            <div class="metode-bayar-group">
                <div class="metode-bayar-btn active" data-metode="cash" onclick="pilihMetode('cash')">
                    <i class="bi bi-cash-stack"></i><span>Cash</span>
                </div>
                <div class="metode-bayar-btn" data-metode="transfer" onclick="pilihMetode('transfer')">
                    <i class="bi bi-bank"></i><span>Transfer</span>
                </div>
                <div class="metode-bayar-btn" data-metode="qris" onclick="pilihMetode('qris')">
                    <i class="bi bi-qr-code"></i><span>QRIS</span>
                </div>
            </div>
        </div>

        <div id="blokUangTunai">
            <div class="mb-2">
                <label class="form-label small">Uang Bayar</label>
                <input type="number" id="inputBayar" class="form-control" placeholder="0" onchange="hitungTotal()">
            </div>
            <div class="d-flex justify-content-between small mb-3">
                <span>Kembalian</span><span id="txtKembalian" class="fw-semibold">Rp 0</span>
            </div>
        </div>

        <div id="blokNonTunai" class="alert alert-secondary small d-none mb-3">
            <i class="bi bi-info-circle"></i> Pembayaran non-tunai dianggap dibayar pas sesuai total, tanpa kembalian.
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary w-50" onclick="kosongkanKeranjang()">Batal</button>
            <button class="btn btn-emas w-50" onclick="bayarTransaksi()">Bayar</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let keranjang = []; // {produk_id, nama, harga, qty, stokMax}
let metodePembayaran = 'cash';

function tambahKeKeranjang(id, nama, harga, stokMax) {
    const existing = keranjang.find(k => k.produk_id === id);
    if (existing) {
        if (existing.qty < stokMax) existing.qty++;
        else alert('Stok tidak mencukupi.');
    } else {
        if (stokMax < 1) { alert('Stok habis.'); return; }
        keranjang.push({ produk_id: id, nama, harga, qty: 1, stokMax });
    }
    renderKeranjang();
}

function ubahQty(id, delta) {
    const item = keranjang.find(k => k.produk_id === id);
    if (!item) return;
    item.qty += delta;
    if (item.qty <= 0) {
        keranjang = keranjang.filter(k => k.produk_id !== id);
    } else if (item.qty > item.stokMax) {
        item.qty = item.stokMax;
        alert('Stok tidak mencukupi.');
    }
    renderKeranjang();
}

function kosongkanKeranjang() {
    keranjang = [];
    document.getElementById('inputDiskon').value = 0;
    document.getElementById('inputBayar').value = '';
    pilihMetode('cash');
    renderKeranjang();
}

function pilihMetode(metode) {
    metodePembayaran = metode;
    document.querySelectorAll('.metode-bayar-btn').forEach(b => b.classList.remove('active'));
    document.querySelector(`.metode-bayar-btn[data-metode="${metode}"]`).classList.add('active');

    const blokTunai = document.getElementById('blokUangTunai');
    const blokNonTunai = document.getElementById('blokNonTunai');
    if (metode === 'cash') {
        blokTunai.classList.remove('d-none');
        blokNonTunai.classList.add('d-none');
    } else {
        blokTunai.classList.add('d-none');
        blokNonTunai.classList.remove('d-none');
    }
}

function renderKeranjang() {
    const box = document.getElementById('isiKeranjang');
    if (keranjang.length === 0) {
        box.innerHTML = '<p class="text-muted text-center small py-4">Belum ada produk dipilih</p>';
    } else {
        box.innerHTML = keranjang.map(k => `
            <div class="keranjang-item">
                <div>
                    <div class="small fw-semibold">${k.nama}</div>
                    <div class="text-muted" style="font-size:.78rem;">Rp ${formatRupiah(k.harga)}</div>
                </div>
                <div class="qty-control">
                    <button onclick="ubahQty(${k.produk_id}, -1)">-</button>
                    <span>${k.qty}</span>
                    <button onclick="ubahQty(${k.produk_id}, 1)">+</button>
                </div>
                <div class="small fw-semibold">Rp ${formatRupiah(k.harga * k.qty)}</div>
            </div>
        `).join('');
    }
    hitungTotal();
}

function hitungTotal() {
    const subtotal = keranjang.reduce((s, k) => s + (k.harga * k.qty), 0);
    const diskonPersen = parseFloat(document.getElementById('inputDiskon').value) || 0;
    const diskonNominal = Math.round(subtotal * diskonPersen / 100);
    const total = subtotal - diskonNominal;
    const bayar = parseFloat(document.getElementById('inputBayar').value) || 0;
    const kembalian = bayar - total;

    document.getElementById('txtSubtotal').innerText = 'Rp ' + formatRupiah(subtotal);
    document.getElementById('txtTotal').innerText = 'Rp ' + formatRupiah(total);
    document.getElementById('txtKembalian').innerText = 'Rp ' + formatRupiah(kembalian > 0 ? kembalian : 0);
}

function formatRupiah(angka) {
    return Number(angka).toLocaleString('id-ID');
}

function bayarTransaksi() {
    if (keranjang.length === 0) { alert('Keranjang masih kosong.'); return; }

    const subtotal = keranjang.reduce((s, k) => s + (k.harga * k.qty), 0);
    const diskonPersen = parseFloat(document.getElementById('inputDiskon').value) || 0;
    const total = subtotal - Math.round(subtotal * diskonPersen / 100);
    const bayar = metodePembayaran === 'cash' ? (parseFloat(document.getElementById('inputBayar').value) || 0) : total;

    if (metodePembayaran === 'cash' && bayar < total) { alert('Uang bayar kurang dari total.'); return; }

    fetch('{{ route('transaksi.store') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({
            items: keranjang.map(k => ({ produk_id: k.produk_id, qty: k.qty })),
            diskon_persen: diskonPersen,
            metode_pembayaran: metodePembayaran,
            uang_bayar: bayar,
        }),
    })
    .then(res => res.json().then(data => ({ status: res.status, body: data })))
    .then(({ status, body }) => {
        if (status === 200 && body.success) {
            window.location.href = body.redirect;
        } else {
            alert(body.message || 'Terjadi kesalahan, silakan coba lagi.');
        }
    })
    .catch(() => alert('Gagal menghubungi server.'));
}

// Filter kategori (dropdown)
document.getElementById('filterKategori').addEventListener('change', function () {
    const kat = this.value;
    document.querySelectorAll('.produk-card').forEach(card => {
        card.style.display = (!kat || card.dataset.kategori === kat) ? '' : 'none';
    });
});

// Cari produk
document.getElementById('cariProduk').addEventListener('input', function () {
    const kata = this.value.toLowerCase();
    document.querySelectorAll('.produk-card').forEach(card => {
        const cocok = card.dataset.nama.includes(kata) || card.dataset.kode.includes(kata);
        card.style.display = cocok ? '' : 'none';
    });
});
</script>
@endsection
