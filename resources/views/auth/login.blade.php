<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="theme-color" content="#1e1712">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
    <title>Login - Istana Tembakau POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
<div class="login-wrapper">
    <div class="login-card">
        <div class="login-brand">
            <img src="{{ asset('images/logo.svg') }}" alt="Logo" width="56" height="56">
            <h1>ISTANA TEMBAKAU</h1>
            <p class="mb-0" style="color:var(--emas-terang);">Point of Sale System</p>
            <p class="mt-2 small text-white-50">Kelola transaksi dengan mudah dan efisien</p>
        </div>
        <div class="login-form-side">
            <h3 class="fw-bold mb-1">Login</h3>
            <p class="text-muted mb-4">Masuk ke akun Anda untuk melanjutkan</p>

            @if ($errors->any())
                <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username" value="{{ old('username') }}" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label small" for="remember">Ingat saya</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-emas w-100 py-2">Masuk</button>
            </form>

            <div class="mt-4 pt-3 border-top small text-muted">
                Akun contoh: <b>admin</b> / <b>kasir1</b> / <b>pimpinan</b> — password: <b>password</b>
            </div>
        </div>
    </div>
</div>
</body>
</html>
