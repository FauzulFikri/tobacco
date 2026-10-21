@extends('layouts.app')
@section('title', 'Produk')

@section('content')
<div class="card-panel">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <form method="GET" class="d-flex">
            <input type="text" name="cari" value="{{ request('cari') }}" class="form-control" placeholder="Cari produk..." style="max-width:260px;">
        </form>
        <button class="btn btn-emas" data-bs-toggle="modal" data-bs-target="#modalTambahProduk">
            <i class="bi bi-plus-lg"></i> Tambah Produk
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-app align-middle">
            <thead>
                <tr>
                    <th>Foto</th><th>Kode</th><th>Nama Produk</th><th>Kategori</th>
                    <th class="text-end">Harga</th><th class="text-end">Stok</th><th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($produks as $produk)
                <tr>
                    <td>
                        @if($produk->gambar)
                            <img src="{{ asset('storage/'.$produk->gambar) }}" class="produk-thumb-tabel" alt="{{ $produk->nama_produk }}">
                        @else
                            <div class="produk-thumb-placeholder"><i class="bi bi-box2"></i></div>
                        @endif
                    </td>
                    <td>{{ $produk->kode_produk }}</td>
                    <td>{{ $produk->nama_produk }}</td>
                    <td>{{ $produk->kategori->nama_kategori }}</td>
                    <td class="text-end">{{ number_format($produk->harga, 0, ',', '.') }}</td>
                    <td class="text-end">
                        {{ $produk->stok }}
                        @if($produk->stok <= $produk->stok_minimum)
                            <span class="badge bg-danger-subtle text-danger">Menipis</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $produk->id }}"><i class="bi bi-pencil"></i></button>
                        <form action="{{ route('produk.destroy', $produk) }}" method="POST" class="d-inline" data-konfirmasi-hapus="Produk {{ $produk->nama_produk }}" data-catatan-hapus="Data penjualan produk ini di laporan juga ikut terhapus.">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>

                <div class="modal fade" id="modalEdit{{ $produk->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('produk.update', $produk) }}" method="POST" enctype="multipart/form-data">
                                @csrf @method('PUT')
                                <div class="modal-header"><h6 class="modal-title fw-bold">Edit Produk</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body">
                                    <div class="mb-3 text-center">
                                        <img src="{{ $produk->gambar ? asset('storage/'.$produk->gambar) : 'https://placehold.co/100x100?text=Foto' }}"
                                             class="produk-thumb-tabel mb-2" style="width:90px;height:90px;" alt="Foto produk">
                                        <div>
                                            <label class="btn btn-outline-secondary btn-sm">
                                                <i class="bi bi-camera"></i> Ganti Foto
                                                <input type="file" name="gambar" accept="image/*" class="d-none">
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mb-2"><label class="form-label small">Kode Produk</label><input class="form-control" name="kode_produk" value="{{ $produk->kode_produk }}" required></div>
                                    <div class="mb-2"><label class="form-label small">Nama Produk</label><input class="form-control" name="nama_produk" value="{{ $produk->nama_produk }}" required></div>
                                    <div class="mb-2"><label class="form-label small">Kategori</label>
                                        <select class="form-select" name="kategori_id" required>
                                            @foreach($kategoris as $kat)
                                                <option value="{{ $kat->id }}" @selected($kat->id === $produk->kategori_id)>{{ $kat->nama_kategori }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-2"><label class="form-label small">Harga</label><input type="number" class="form-control" name="harga" value="{{ $produk->harga }}" required></div>
                                    <div class="mb-2"><label class="form-label small">Stok</label><input type="number" class="form-control" value="{{ $produk->stok }}" disabled>
                                        <div class="form-text">Stok diubah lewat menu <a href="{{ route('stok.index') }}">Stok Barang</a> agar riwayat masuk/keluarnya tercatat.</div></div>
                                    <div class="mb-2"><label class="form-label small">Stok Minimum</label><input type="number" class="form-control" name="stok_minimum" value="{{ $produk->stok_minimum }}"></div>
                                </div>
                                <div class="modal-footer"><button type="submit" class="btn btn-emas w-100">Simpan</button></div>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Belum ada produk</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $produks->links() }}
</div>

<div class="modal fade" id="modalTambahProduk" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header"><h6 class="modal-title fw-bold">Tambah Produk</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3 text-center">
                        <label class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-camera"></i> Pilih Foto Produk
                            <input type="file" name="gambar" accept="image/*" class="d-none">
                        </label>
                        <div class="text-muted small mt-1">Opsional, JPG/PNG maks 2MB</div>
                    </div>
                    <div class="mb-2"><label class="form-label small">Kode Produk</label><input class="form-control" name="kode_produk" required></div>
                    <div class="mb-2"><label class="form-label small">Nama Produk</label><input class="form-control" name="nama_produk" required></div>
                    <div class="mb-2"><label class="form-label small">Kategori</label>
                        <select class="form-select" name="kategori_id" required>
                            <option value="">Pilih kategori</option>
                            @foreach($kategoris as $kat)
                                <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2"><label class="form-label small">Harga</label><input type="number" class="form-control" name="harga" required></div>
                    <div class="mb-2"><label class="form-label small">Stok Awal</label><input type="number" class="form-control" name="stok" min="0" value="0" required></div>
                    <div class="mb-2"><label class="form-label small">Stok Minimum</label><input type="number" class="form-control" name="stok_minimum" value="10"></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-emas w-100">Simpan</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
