@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between mb-4 mb-sm-5">
        <div>
            <h1 class="h3 h2-sm mb-1 fw-bold" style="font-family: 'Playfair Display', serif; color: #0D1E1C;">Dashboard Statistics</h1>
            <p class="text-muted mb-0">Ringkasan data sistem manajemen absensi.</p>
        </div>
    </div>
    <!-- Selamat Datang -->
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card border shadow-sm" style="background-color: #FFFFFF; border-radius: 16px;">
                <div class="card-header bg-white py-4 border-0">
                    <h5 class="m-0 fw-bold" style="font-family: 'Playfair Display', serif; color: #1A7A6E;">Selamat Datang</h5>
                </div>
                <div class="card-body pt-0 pb-5">
                    <p class="fs-5 text-secondary" style="line-height: 1.8;">Halo <strong>{{ auth()->user()->name }}</strong>, selamat datang kembali di sistem manajemen absensi pegawai.</p>
                    <p class="text-muted">Gunakan panel navigasi di sebelah kiri untuk mengelola data master pegawai, ruangan, dan shift kerja dengan lebih mudah and efisien.</p>
                    <div class="mt-4">
                        <a href="{{ route('pegawai.index') }}" class="btn btn-primary px-4 py-2 me-2 text-white" style="background-color: #1A7A6E; border: none; border-radius: 100px; text-decoration: none;">Kelola Pegawai</a>
                        <a href="{{ route('jadwal.index') }}" class="btn btn-outline-secondary border px-4 py-2" style="color: #1A7A6E; border-radius: 100px; text-decoration: none;">Lihat Jadwal</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(!auth()->user()->isAdmin())
    <!-- Pegawai Hari Ini Card (Collapse/Accordion) -->
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card border shadow-sm" style="background-color: #FFFFFF; border-radius: 16px;">
                <div class="card-header bg-white py-4 border-0 d-flex align-items-center justify-content-between"
                     data-bs-toggle="collapse"
                     data-bs-target="#collapsePegawaiHariIni"
                     role="button"
                     aria-expanded="false"
                     aria-controls="collapsePegawaiHariIni"
                     style="cursor: pointer;">
                    <div class="d-flex align-items-center">
                        <div class="p-2 rounded-circle me-3" style="background-color: rgba(26, 122, 110, 0.1);">
                            <i class="fas fa-clipboard-list fs-4" style="color: #1A7A6E;"></i>
                        </div>
                        <div>
                            <h5 class="m-0 fw-bold" style="font-family: 'Playfair Display', serif; color: #1A7A6E;">Pegawai Hari Ini</h5>
                            <p class="text-muted small mb-0">{{ \Carbon\Carbon::today()->translatedFormat('l, d F Y') }}</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-down fs-5 toggle-icon" style="color: #1A7A6E; transition: transform 0.3s;"></i>
                </div>
                <div class="collapse" id="collapsePegawaiHariIni">
                    <div class="card-body pt-0">
                        <div class="mb-3">
                            <select id="filterShift" class="form-select form-select-sm shadow-sm" style="width: auto; min-width: 160px; border-radius: 10px; border-color: #dee2e6;">
                                <option value="semua">Semua</option>
                                <option value="pagi">Pagi</option>
                                <option value="siang">Siang</option>
                                <option value="malam">Malam</option>
                                <option value="libur">Libur</option>
                            </select>
                        </div>
                    @if(!empty($pegawaiPerRuangan))
                        @foreach($pegawaiPerRuangan as $room)
                            <div class="mb-3">
                                <h6 class="fw-bold mb-3" style="color: #0D1E1C;">
                                    <i class="fas fa-door-open me-2" style="color: #1A7A6E;"></i>
                                    {{ $room['ruangan']->nama_ruangan }}
                                    <span class="badge bg-primary-subtle text-primary ms-2">{{ $room['total'] }} pegawai</span>
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless mb-0 align-middle">
                                        <thead>
                                            <tr class="text-muted small">
                                                <th class="ps-0" style="width: 35%;">Nama Pegawai</th>
                                                <th class="ps-0" style="width: 12%;">Shift</th>
                                                <th class="ps-0" style="width: 15%;">Jam Masuk</th>
                                                <th class="ps-0" style="width: 15%;">Jam Pulang</th>
                                                <th class="ps-0" style="width: 23%;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach(['pagi', 'siang', 'malam', 'libur'] as $key)
                                                @foreach($room['kategori'][$key] as $p)
                                                    @php
                                                        $isNonShift = $p['kategori_kerja'] === 'non_shift' || $p['kategori_kerja'] === 'non_shift_5_hari';
                                                    @endphp
                                                    <tr class="row-hari-ini kategori-{{ $key }}" data-kategori="{{ $key }}">
                                                        <td class="ps-0 fw-medium">{{ $p['nama'] }}</td>
                                                        <td class="ps-0">
                                                            @if($isNonShift)
                                                                <span class="badge px-2 py-1" style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; font-weight: 600;">Non Shift</span>
                                                            @else
                                                                <span class="badge bg-light text-dark border px-2 py-1">{{ $p['kode_shift'] ?: '-' }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="ps-0">{{ $p['jam_masuk'] }}</td>
                                                        <td class="ps-0">{{ $p['jam_pulang'] }}</td>
                                                        <td class="ps-0"></td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @if(!$loop->last)
                                <hr class="my-3" style="border-style: dashed;">
                            @endif
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-check fs-1" style="color: #dee2e6;"></i>
                            <p class="text-muted mt-3 mb-0">Tidak ada data pegawai untuk hari ini.</p>
                        </div>
                    @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <!-- Stats Cards -->
    <div class="row g-3 mt-4">
        <div class="col-sm-6 col-xl-4">
            <div class="card border shadow-sm h-100" style="background-color: #FFFFFF; border-radius: 16px;">
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
        <div class="col-sm-6 col-xl-4">
            <div class="card border shadow-sm h-100" style="background-color: #FFFFFF; border-radius: 16px;">
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
        <div class="col-sm-6 col-xl-4">
            <div class="card border shadow-sm h-100" style="background-color: #FFFFFF; border-radius: 16px;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="p-3 rounded-circle" style="background-color: rgba(231, 76, 60, 0.1);">
                            <i class="fas fa-user-clock fs-4" style="color: #e74c3c;"></i>
                        </div>
                        <div class="text-end">
                            <span class="text-muted small text-uppercase fw-bold">Data Pegawai Cuti</span>
                        </div>
                    </div>
                    <div class="row text-center mt-3 g-0">
                        <div class="col-6 border-end">
                            <a href="{{ route('cuti.index', ['bulan' => date('m'), 'tahun' => date('Y')]) }}" class="text-decoration-none">
                                <div class="text-muted small mb-1">Hari Ini</div>
                                <h4 class="mb-0 fw-bold" style="color: #0D1E1C;">{{ $stats['total_cuti_hari_ini'] }} <small class="text-muted fw-normal" style="font-size: 0.8rem;">Orang</small></h4>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('cuti.index', ['bulan' => date('m'), 'tahun' => date('Y')]) }}" class="text-decoration-none">
                                <div class="text-muted small mb-1">Bulan Ini</div>
                                <h4 class="mb-0 fw-bold" style="color: #0D1E1C;">{{ $stats['total_cuti_bulan_ini'] }} <small class="text-muted fw-normal" style="font-size: 0.8rem;">Orang</small></h4>
                            </a>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('cuti.index') }}" class="btn btn-link btn-sm text-decoration-none p-0" style="color: #e74c3c; font-size: 0.85rem;">
                            Selengkapnya <i class="fas fa-chevron-right ms-1" style="font-size: 0.7rem;"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
$(document).ready(function() {
    // Collapse chevron rotation
    $('#collapsePegawaiHariIni').on('show.bs.collapse', function() {
        $('.toggle-icon').css('transform', 'rotate(180deg)');
    }).on('hide.bs.collapse', function() {
        $('.toggle-icon').css('transform', 'rotate(0deg)');
    });

    // Filter
    $('#filterShift').on('change', function() {
        var val = $(this).val();
        if (val === 'semua') {
            $('.row-hari-ini').show();
        } else {
            $('.row-hari-ini').hide();
            $('.kategori-' + val).show();
        }
    });
    $('#filterShift').trigger('change');
});
</script>
@endpush
@endsection
