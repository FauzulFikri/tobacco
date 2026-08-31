@extends('layouts.app')
@section('title', 'Kelola Akun')

@section('content')
<div class="row g-3 justify-content-center">
    <div class="col-lg-8">
        <div class="card-panel mb-3">
            <h6 class="fw-bold mb-3">Foto Profil & Informasi Akun</h6>
            <form action="{{ route('akun.update') }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="d-flex align-items-center gap-3 mb-4">
                    <img src="{{ $user->foto_profil ? asset('storage/'.$user->foto_profil) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=1e1712&color=c9a35f' }}"
                         class="profile-photo" alt="Foto Profil">
                    <div>
                        <label class="btn btn-outline-secondary btn-sm mb-0">
                            <i class="bi bi-camera"></i> Ganti Foto
                            <input type="file" name="foto_profil" accept="image/*" class="d-none" onchange="this.form.querySelector('button[type=submit]').removeAttribute('disabled'); document.getElementById('previewNama').innerText = this.files[0].name;">
                        </label>
                        <div class="text-muted small mt-1" id="previewNama">JPG/PNG, maks 2MB</div>
                    </div>
                </div>

                @if ($errors->any() && !$errors->has('password_lama'))
                    <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Username</label>
                        <input type="text" name="username" class="form-control" value="{{ old('username', $user->username) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Role</label>
                        <input type="text" class="form-control text-capitalize" value="{{ $user->role }}" disabled>
                    </div>
                </div>

                <button type="submit" class="btn btn-emas mt-4">Simpan Perubahan</button>
            </form>
        </div>

        <div class="card-panel">
            <h6 class="fw-bold mb-3">Ubah Password</h6>

            @if ($errors->has('password_lama'))
                <div class="alert alert-danger py-2">{{ $errors->first('password_lama') }}</div>
            @endif

            <form action="{{ route('akun.password') }}" method="POST">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Password Lama</label>
                        <input type="password" name="password_lama" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Password Baru</label>
                        <input type="password" name="password_baru" class="form-control" required minlength="6">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Konfirmasi Password Baru</label>
                        <input type="password" name="password_baru_confirmation" class="form-control" required minlength="6">
                    </div>
                </div>
                <button type="submit" class="btn btn-emas mt-4">Ubah Password</button>
            </form>
        </div>
    </div>
</div>
@endsection
