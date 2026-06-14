@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between mb-4">
        <div>
            <h1 class="h4 mb-1 fw-bold" style="font-family: 'Playfair Display', serif; color: #0D1E1C;">Daftar Kegiatan</h1>
            <p class="text-muted small mb-0">Pilih kegiatan untuk melakukan absensi.</p>
        </div>
    </div>

    <div class="row">
        @forelse($kegiatans as $kegiatan)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 15px;">
                    <div class="card-body">
                        <h5 class="card-title fw-bold" style="color: #1A7A6E;">{{ $kegiatan->nama_kegiatan }}</h5>
                        <p class="card-text mb-1"><i class="fas fa-calendar-alt text-muted"></i> {{ \Carbon\Carbon::parse($kegiatan->tanggal_kegiatan)->format('d M Y') }}</p>
                        <p class="card-text mb-1"><i class="fas fa-clock text-muted"></i> {{ $kegiatan->jam_mulai }} - {{ $kegiatan->jam_selesai }}</p>
                        <p class="card-text mb-3"><i class="fas fa-map-marker-alt text-muted"></i> {{ $kegiatan->lokasi }}</p>
                        
                        <a href="{{ route('user.kegiatan.absen-form', $kegiatan->id) }}" class="btn text-white w-100 rounded-pill" style="background-color: #1A7A6E;">
                            <i class="fas fa-camera"></i> Mulai Absen
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted">Tidak ada kegiatan aktif saat ini.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
