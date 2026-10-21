@extends('layouts.app')
@section('title', 'Kategori')

@section('content')
<div class="card-panel">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0">Daftar Kategori</h6>
        <button class="btn btn-emas" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
            <i class="bi bi-plus-lg"></i> Tambah Kategori
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-app align-middle">
            <thead><tr><th>No</th><th>Nama Kategori</th><th>Jumlah Produk</th><th class="text-center">Aksi</th></tr></thead>
            <tbody>
            @forelse($kategoris as $i => $kat)
                <tr>
                    <td>{{ $kategoris->firstItem() + $i }}</td>
                    <td>{{ $kat->nama_kategori }}</td>
                    <td>{{ $kat->produks_count }}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#modalEditKat{{ $kat->id }}"><i class="bi bi-pencil"></i></button>
                        <form action="{{ route('kategori.destroy', $kat) }}" method="POST" class="d-inline" data-konfirmasi-hapus="Kategori {{ $kat->nama_kategori }}" data-catatan-hapus="Kategori yang masih punya produk tidak bisa dihapus.">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <div class="modal fade" id="modalEditKat{{ $kat->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('kategori.update', $kat) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="modal-header"><h6 class="modal-title fw-bold">Edit Kategori</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body">
                                    <input class="form-control" name="nama_kategori" value="{{ $kat->nama_kategori }}" required>
                                </div>
                                <div class="modal-footer"><button class="btn btn-emas w-100">Simpan</button></div>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-4">Belum ada kategori</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $kategoris->links() }}
</div>

<div class="modal fade" id="modalTambahKategori" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('kategori.store') }}" method="POST">
                @csrf
                <div class="modal-header"><h6 class="modal-title fw-bold">Tambah Kategori</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <input class="form-control" name="nama_kategori" placeholder="Nama kategori" required>
                </div>
                <div class="modal-footer"><button class="btn btn-emas w-100">Simpan</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
