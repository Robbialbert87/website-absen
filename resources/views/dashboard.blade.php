@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-5">
        <div>
            <h1 class="h2 mb-1 fw-bold" style="font-family: 'Playfair Display', serif; color: #0D1E1C;">Dashboard Statistics</h1>
            <p class="text-muted mb-0">Ringkasan data sistem manajemen absensi.</p>
        </div>
    </div>

    <div class="row">
        <!-- Total Pegawai Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card p-3 border shadow-sm" style="background-color: #FFFFFF; border-radius: 16px;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="p-3 rounded-circle" style="background-color: rgba(26, 122, 110, 0.1);">
                            <i class="fas fa-users fs-4" style="color: #1A7A6E;"></i>
                        </div>
                        <div class="text-end">
                            <span class="text-muted small text-uppercase fw-bold">Total Pegawai</span>
                            <h2 class="mb-0 fw-bold mt-1" style="color: #0D1E1C;">{{ $stats['total_pegawai'] }}</h2>
                        </div>
                    </div>
                    <div class="progress" style="height: 6px; border-radius: 100px;">
                        <div class="progress-bar" role="progressbar" style="width: 100%; background-color: #1A7A6E;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Ruangan Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card p-3 border shadow-sm" style="background-color: #FFFFFF; border-radius: 16px;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="p-3 rounded-circle" style="background-color: rgba(42, 157, 143, 0.1);">
                            <i class="fas fa-door-open fs-4" style="color: #2A9D8F;"></i>
                        </div>
                        <div class="text-end">
                            <span class="text-muted small text-uppercase fw-bold">Total Ruangan</span>
                            <h2 class="mb-0 fw-bold mt-1" style="color: #0D1E1C;">{{ $stats['total_ruangan'] }}</h2>
                        </div>
                    </div>
                    <div class="progress" style="height: 6px; border-radius: 100px;">
                        <div class="progress-bar" role="progressbar" style="width: 100%; background-color: #2A9D8F;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pegawai Cuti Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card p-3 border shadow-sm" style="background-color: #FFFFFF; border-radius: 16px;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="p-3 rounded-circle" style="background-color: rgba(231, 76, 60, 0.1);">
                            <i class="fas fa-user-clock fs-4" style="color: #e74c3c;"></i>
                        </div>
                        <div class="text-end">
                            <span class="text-muted small text-uppercase fw-bold">Data Pegawai Cuti</span>
                        </div>
                    </div>
                    <div class="row text-center mt-3">
                        <div class="col-6 border-end">
                            <div class="text-muted small mb-1">Hari Ini</div>
                            <h4 class="mb-0 fw-bold" style="color: #0D1E1C;">{{ $stats['total_cuti_hari_ini'] }} <small class="text-muted fw-normal" style="font-size: 0.8rem;">Orang</small></h4>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small mb-1">Bulan Ini</div>
                            <h4 class="mb-0 fw-bold" style="color: #0D1E1C;">{{ $stats['total_cuti_bulan_ini'] }} <small class="text-muted fw-normal" style="font-size: 0.8rem;">Orang</small></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card border shadow-sm" style="background-color: #FFFFFF; border-radius: 16px;">
                <div class="card-header bg-white py-4 border-0">
                    <h5 class="m-0 fw-bold" style="font-family: 'Playfair Display', serif; color: #1A7A6E;">Selamat Datang</h5>
                </div>
                <div class="card-body pt-0 pb-5">
                    <p class="fs-5 text-secondary" style="line-height: 1.8;">Halo <strong>{{ auth()->user()->name }}</strong>, selamat datang kembali di sistem manajemen absensi pegawai.</p>
                    <p class="text-muted">Gunakan panel navigasi di sebelah kiri untuk mengelola data master pegawai, ruangan, dan shift kerja dengan lebih mudah dan efisien.</p>
                    <div class="mt-4">
                        <a href="{{ route('pegawai.index') }}" class="btn btn-primary px-4 py-2 me-2 text-white" style="background-color: #1A7A6E; border: none; border-radius: 100px; text-decoration: none;">Kelola Pegawai</a>
                        <a href="{{ route('jadwal.index') }}" class="btn btn-outline-secondary border px-4 py-2" style="color: #1A7A6E; border-radius: 100px; text-decoration: none;">Lihat Jadwal</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
