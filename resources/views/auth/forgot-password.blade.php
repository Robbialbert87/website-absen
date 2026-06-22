<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password | SIJAGA</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('/images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet" />
    <style>
        :root { --bg: #E8F4F2; --surface-2: #FFFFFF; --accent: #1A7A6E; --accent-mid: #2A9D8F; --accent-ghost: rgba(26, 122, 110, 0.08); --text-1: #0D1E1C; --text-2: #3A5C58; --text-3: #6B8C88; --border: rgba(13, 30, 28, 0.08); --shadow-lg: 0 24px 64px rgba(13, 30, 28, 0.13); --radius-lg: 24px; --silk: cubic-bezier(0.16, 1, 0.3, 1);--surface: #F4FAFA; }
        body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text-1); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .wrapper { width: 100%; max-width: 440px; }
        .card { background: var(--surface-2); border-radius: var(--radius-lg); border: 1px solid var(--border); padding: 40px; box-shadow: var(--shadow-lg); }
        .brand-logo { text-align: center; margin-bottom: 24px; }
        .brand-logo img { width: 200px; }
        .form-label { font-weight: 600; font-size: 0.85rem; color: var(--text-2); margin-bottom: 8px; }
        .form-control { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 12px 16px; }
        .form-control:focus { background: #fff; border-color: var(--accent); box-shadow: 0 0 0 4px var(--accent-ghost); }
        .btn-primary { background: var(--accent); border: none; border-radius: 100px; padding: 14px; font-weight: 700; width: 100%; color: white; transition: all 0.3s var(--silk); }
        .btn-primary:hover { background: var(--accent-mid); transform: translateY(-2px); }
        .footer-text { text-align: center; margin-top: 24px; font-size: 0.85rem; }
        .footer-text a { color: var(--accent); text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="brand-logo"><img src="{{ asset('images/logo.png') }}" alt="Logo"></div>
            <p style="color: var(--text-2); font-size: 0.9rem; margin-bottom: 24px;">Lupa password? Masukkan email Anda dan kami akan kirim link reset password.</p>
            <form action="{{ route('password.email') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="email" value="{{ old('email') }}" required autofocus>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <button type="submit" class="btn btn-primary">Kirim Link Reset</button>
            </form>
            <div class="footer-text"><a href="{{ route('login') }}">Kembali ke Login</a></div>
        </div>
    </div>
</body>
</html>
