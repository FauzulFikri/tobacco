@extends('layouts.app')
@section('title', 'Pengguna')

@section('content')
<div class="card-panel">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0">Daftar Pengguna</h6>
        <button class="btn btn-emas" data-bs-toggle="modal" data-bs-target="#modalTambahPengguna">
            <i class="bi bi-plus-lg"></i> Tambah Pengguna
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-app align-middle">
            <thead><tr><th>No</th><th>Nama</th><th>Username</th><th>Role</th><th class="text-center">Aksi</th></tr></thead>
            <tbody>
            @forelse($penggunas as $i => $p)
                <tr>
                    <td>{{ $penggunas->firstItem() + $i }}</td>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->username }}</td>
                    <td><span class="badge bg-secondary-subtle text-dark text-capitalize">{{ $p->role }}</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#modalEditUser{{ $p->id }}"><i class="bi bi-pencil"></i></button>
                        @if($p->id !== auth()->id())
                        <form action="{{ route('pengguna.destroy', $p) }}" method="POST" class="d-inline" data-konfirmasi-hapus="Pengguna {{ $p->name }} ({{ $p->username }})">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                <div class="modal fade" id="modalEditUser{{ $p->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('pengguna.update', $p) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="modal-header"><h6 class="modal-title fw-bold">Edit Pengguna</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body">
                                    <div class="mb-2"><label class="form-label small">Nama</label><input class="form-control" name="name" value="{{ $p->name }}" required></div>
                                    <div class="mb-2"><label class="form-label small">Username</label><input class="form-control" name="username" value="{{ $p->username }}" required></div>
                                    <div class="mb-2"><label class="form-label small">Email</label><input type="email" class="form-control" name="email" value="{{ $p->email }}" required></div>
                                    <div class="mb-2"><label class="form-label small">Password baru (kosongkan jika tidak diubah)</label><input type="password" class="form-control" name="password"></div>
                                    <div class="mb-2"><label class="form-label small">Role</label>
                                        <select class="form-select" name="role" required>
                                            <option value="admin" @selected($p->role==='admin')>Admin</option>
                                            <option value="kasir" @selected($p->role==='kasir')>Kasir</option>
                                            <option value="pimpinan" @selected($p->role==='pimpinan')>Pimpinan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer"><button class="btn btn-emas w-100">Simpan</button></div>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada pengguna</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $penggunas->links() }}
</div>

<div class="modal fade" id="modalTambahPengguna" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('pengguna.store') }}" method="POST">
                @csrf
                <div class="modal-header"><h6 class="modal-title fw-bold">Tambah Pengguna</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-2"><label class="form-label small">Nama</label><input class="form-control" name="name" required></div>
                    <div class="mb-2"><label class="form-label small">Username</label><input class="form-control" name="username" required></div>
                    <div class="mb-2"><label class="form-label small">Email</label><input type="email" class="form-control" name="email" required></div>
                    <div class="mb-2"><label class="form-label small">Password</label><input type="password" class="form-control" name="password" required></div>
                    <div class="mb-2"><label class="form-label small">Role</label>
                        <select class="form-select" name="role" required>
                            <option value="admin">Admin</option>
                            <option value="kasir">Kasir</option>
                            <option value="pimpinan">Pimpinan</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer"><button class="btn btn-emas w-100">Simpan</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
