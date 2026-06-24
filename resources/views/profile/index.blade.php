@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between mb-4 mb-sm-5">
        <div>
            <h1 class="h3 h2-sm mb-1 fw-bold" style="font-family: 'Playfair Display', serif; color: #0D1E1C;">Profile</h1>
            <p class="text-muted mb-0">Informasi akun dan pengaturan password.</p>
        </div>
    </div>

    <div class="row g-4">
        {{-- Kartu Info Profil --}}
        <div class="col-lg-5">
            <div class="card border shadow-sm h-100" style="background-color: #FFFFFF; border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=1A7A6E&color=fff&size=120"
                            class="rounded-circle shadow-sm mb-3" width="100" height="100" alt="Avatar">
                        <h4 class="fw-bold mb-1" style="color: #0D1E1C;">{{ $user->name }}</h4>
                        <span class="badge px-3 py-2" style="background-color: rgba(26, 122, 110, 0.1); color: #1A7A6E; font-weight: 600;">
                            {{ ucfirst($user->getRoleNames()->first() ?? 'user') }}
                        </span>
                    </div>

                    <hr class="my-4" style="border-style: dashed;">

                    <div class="mb-3">
                        <label class="text-muted small text-uppercase fw-bold mb-1">Username</label>
                        <p class="mb-0 fw-medium">{{ $user->username ?? '-' }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small text-uppercase fw-bold mb-1">NIP</label>
                        <p class="mb-0 fw-medium">{{ $user->nip ?? '-' }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small text-uppercase fw-bold mb-1">Ruangan</label>
                        <p class="mb-0 fw-medium">{{ $user->ruangan->nama_ruangan ?? '-' }}</p>
                    </div>

                    <div class="mb-0">
                        <label class="text-muted small text-uppercase fw-bold mb-1">Data Pegawai</label>
                        <p class="mb-0 fw-medium">{{ $user->pegawai->nama ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kartu Ganti Password --}}
        <div class="col-lg-7">
            <div class="card border shadow-sm" style="background-color: #FFFFFF; border-radius: 16px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-1" style="color: #0D1E1C;">
                        <i class="fas fa-key me-2" style="color: #1A7A6E;"></i>Ganti Password
                    </h5>
                    <p class="text-muted small mb-4">Password minimal 6 karakter.</p>

                    <form action="{{ route('profile.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Password Saat Ini</label>
                            <input type="password" name="current_password"
                                class="form-control @error('current_password') is-invalid @enderror" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-2"></i>Simpan Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
