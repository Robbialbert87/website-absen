<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email | SIJAGA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet" />
    <style>
        :root { --bg: #E8F4F2; --surface-2: #FFFFFF; --accent: #1A7A6E; --text-2: #3A5C58; --border: rgba(13, 30, 28, 0.08); --shadow-lg: 0 24px 64px rgba(13, 30, 28, 0.13); --radius-lg: 24px; }
        body { font-family: 'DM Sans', sans-serif; background: var(--bg); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .wrapper { width: 100%; max-width: 520px; }
        .card { background: var(--surface-2); border-radius: var(--radius-lg); border: 1px solid var(--border); padding: 40px; box-shadow: var(--shadow-lg); }
        .btn-primary { background: var(--accent); border: none; border-radius: 100px; padding: 12px 24px; font-weight: 600; color: white; text-decoration: none; display: inline-block; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <h3 style="color: var(--accent); margin-bottom: 16px;">Verifikasi Email</h3>
            <p style="color: var(--text-2); margin-bottom: 8px;">Sebelum melanjutkan, periksa email Anda untuk link verifikasi.</p>
            <p style="color: var(--text-2); margin-bottom: 24px;">Jika tidak menerima email, klik tombol di bawah untuk mengirim ulang.</p>
            <form action="{{ route('verification.send') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">Kirim Ulang Email Verifikasi</button>
            </form>
        </div>
    </div>
</body>
</html>
