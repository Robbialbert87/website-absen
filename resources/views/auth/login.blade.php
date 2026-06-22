<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SIJAGA</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('/images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet" />
    <style>
        :root {
            --bg: #E8F4F2; --surface: #F4FAFA; --surface-2: #FFFFFF; --accent: #1A7A6E; --accent-mid: #2A9D8F; --accent-ghost: rgba(26, 122, 110, 0.08); --text-1: #0D1E1C; --text-2: #3A5C58; --border: rgba(13, 30, 28, 0.08); --shadow-lg: 0 24px 64px rgba(13, 30, 28, 0.13); --radius-lg: 24px; --silk: cubic-bezier(0.16, 1, 0.3, 1);
        }
        body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text-1); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .login-wrapper { width: 100%; max-width: 420px; }
        .login-card { background: var(--surface-2); border-radius: var(--radius-lg); border: 1px solid var(--border); padding: 40px; box-shadow: var(--shadow-lg); }
        .brand-logo { text-align: center; margin-bottom: 24px; }
        .brand-logo img { width: 200px; }
        .nav-tabs { border-bottom: 2px solid var(--border); gap: 0; }
        .nav-tabs .nav-link { border: none; color: var(--text-2); font-weight: 600; font-size: 0.9rem; padding: 12px 16px; margin-bottom: -2px; background: none; border-radius: 0; border-bottom: 2px solid transparent; transition: all 0.3s var(--silk); }
        .nav-tabs .nav-link:hover { color: var(--accent); }
        .nav-tabs .nav-link.active { color: var(--accent); background: none; border-bottom: 2px solid var(--accent); }
        .form-label { font-weight: 600; font-size: 0.85rem; color: var(--text-2); margin-bottom: 8px; }
        .form-control { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 12px 16px; font-size: 0.95rem; color: var(--text-1); transition: all 0.3s var(--silk); }
        .form-control:focus { background: #fff; border-color: var(--accent); box-shadow: 0 0 0 4px var(--accent-ghost); outline: none; }
        .btn-primary { background: var(--accent); border: none; border-radius: 100px; padding: 14px; font-weight: 700; width: 100%; margin-top: 10px; transition: all 0.4s var(--silk); box-shadow: 0 6px 20px rgba(26, 122, 110, 0.25); color: white; }
        .btn-primary:hover { background: var(--accent-mid); transform: translateY(-2px); box-shadow: 0 8px 25px rgba(26, 122, 110, 0.35); }
        .footer-text { text-align: center; margin-top: 24px; font-size: 0.85rem; color: var(--text-3); }
        .footer-text a { color: var(--accent); text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="brand-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
            </div>

            <ul class="nav nav-tabs" id="loginTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="nip-tab" data-bs-toggle="tab" data-bs-target="#nip" type="button" role="tab">NIP</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="email-tab" data-bs-toggle="tab" data-bs-target="#email" type="button" role="tab">Email</button>
                </li>
            </ul>

            <div class="tab-content mt-4">
                <div class="tab-pane fade show active" id="nip" role="tabpanel">
                    <form action="{{ route('nip-login') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="nip" class="form-label">NIP</label>
                            <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror" id="nip" value="{{ old('nip') }}" placeholder="Masukkan NIP">
                            @error('nip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="nip_password" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="nip_password">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="remember" class="form-check-input" id="remember_nip">
                                <label class="form-check-label" for="remember_nip" style="font-size: 0.85rem; color: var(--text-2);">Ingat saya</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Masuk</button>
                    </form>
                </div>

                <div class="tab-pane fade" id="email" role="tabpanel">
                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email / NIP</label>
                            <input type="text" name="email" class="form-control @error('email') is-invalid @enderror" id="email" value="{{ old('email') }}" placeholder="nama@email.com atau NIP">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="email_password" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="email_password">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="remember" class="form-check-input" id="remember_email">
                                <label class="form-check-label" for="remember_email" style="font-size: 0.85rem; color: var(--text-2);">Ingat saya</label>
                            </div>
                            <a href="{{ route('password.request') }}" style="font-size: 0.85rem; color: var(--accent); text-decoration: none; font-weight: 600;">Lupa password?</a>
                        </div>
                        <button type="submit" class="btn btn-primary">Masuk</button>
                    </form>
                </div>
            </div>

            <div class="footer-text">
                Belum punya akun? <a href="{{ route('register') }}">Daftar</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
