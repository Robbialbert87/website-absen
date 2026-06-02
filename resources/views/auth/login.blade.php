<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login SIJAGA | {{ config('app.name', 'Absensi') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('/images/logo.png') }}">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Playfair+Display:ital,wght@0,700;1,600&display=swap"
        rel="stylesheet" />

    <style>
        :root {
            --bg: #E8F4F2;
            --surface: #F4FAFA;
            --surface-2: #FFFFFF;
            --accent: #1A7A6E;
            --accent-mid: #2A9D8F;
            --accent-ghost: rgba(26, 122, 110, 0.08);
            --text-1: #0D1E1C;
            --text-2: #3A5C58;
            --text-3: #6B8C88;
            --border: rgba(13, 30, 28, 0.08);
            --shadow-lg: 0 24px 64px rgba(13, 30, 28, 0.13);
            --radius-lg: 24px;
            --silk: cubic-bezier(0.16, 1, 0.3, 1);
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text-1);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Subtle Noise Texture */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            opacity: 0.022;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E");
            background-size: 128px 128px;
        }

        /* Decorative background circles */
        body::after {
            content: '';
            position: absolute;
            top: -10%;
            right: -5%;
            width: 55vw;
            height: 55vw;
            background: radial-gradient(ellipse at center, rgba(91, 191, 181, 0.18) 0%, rgba(42, 157, 143, 0.06) 50%, transparent 75%);
            border-radius: 50%;
            pointer-events: none;
            z-index: -1;
        }

        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
            padding: 20px;
        }

        .login-card {
            background: var(--surface-2);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            padding: 50px 40px;
            box-shadow: var(--shadow-lg);
        }

        .brand-logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .brand-logo img {
            max-height: auto;
            width: 250px;
            object-fit: contain;
        }


        .brand-logo span {
            color: var(--accent);
        }

        .login-subtitle {
            text-align: center;
            color: var(--text-2);
            font-size: 0.95rem;
            margin-bottom: 40px;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-2);
            margin-bottom: 8px;
        }

        .form-control {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 0.95rem;
            color: var(--text-1);
            transition: all 0.3s var(--silk);
        }

        .form-control:focus {
            background: #fff;
            border-color: var(--accent);
            box-shadow: 0 0 0 4px var(--accent-ghost);
            outline: none;
        }

        .btn-primary {
            background: var(--accent);
            border: none;
            border-radius: 100px;
            padding: 14px;
            font-weight: 700;
            width: 100%;
            margin-top: 10px;
            transition: all 0.4s var(--silk);
            box-shadow: 0 6px 20px rgba(26, 122, 110, 0.25);
            color: white;
        }

        .btn-primary:hover {
            background: var(--accent-mid);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(26, 122, 110, 0.35);
        }

        .footer-text {
            text-align: center;
            margin-top: 30px;
            font-size: 0.8rem;
            color: var(--text-3);
        }

        .footer-text strong {
            color: var(--accent);
        }
    </style>
</head>

<body>

    <div class="login-wrapper">
        <div class="login-card">
            <div class="brand-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
            </div>

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="login" class="form-label">Email atau NIP</label>
                    <input type="text" name="login" class="form-control @error('login') is-invalid @enderror"
                        id="login" value="{{ old('login') }}" required autofocus placeholder="name@example.com atau NIP">
                    @error('login')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                        id="password" required placeholder="••••••••">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Sign in</button>
            </form>

            <div class="footer-text">
                RSUD H.Abdul Manap — <strong>Kota Jambi</strong>
            </div>
        </div>
    </div>

</body>

</html>
