@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Detail Kegiatan</h1>
            <p class="text-muted">{{ $kegiatan->nama_kegiatan }}</p>
        </div>
        <a href="{{ route('kegiatan.index') }}" class="btn btn-secondary rounded-pill">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <!-- Statistik -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0" style="border-radius: 15px; border-left: 5px solid #2A9D8F !important;">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Hadir</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $absensis->where('status', 'hadir')->count() }} Orang</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0" style="border-radius: 15px; border-left: 5px solid #f6c23e !important;">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Terlambat</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $absensis->where('status', 'terlambat')->count() }} Orang</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0" style="border-radius: 15px; border-left: 5px solid #e74a3b !important;">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Di Luar Radius</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $absensis->where('status', 'di_luar_radius')->count() }} Orang</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 15px;">
        <div class="card-body">
            <h5 class="card-title fw-bold mb-4">Daftar Absensi</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>NIP / Nama Pegawai</th>
                            <th>Waktu Absen</th>
                            <th>Status</th>
                            <th>Foto</th>
                            <th>Lokasi GPS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absensis as $absen)
                        <tr>
                            <td>
                                <strong>{{ $absen->pegawai->nama }}</strong><br>
                                <small class="text-muted">{{ $absen->pegawai->nip }}</small>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($absen->waktu_absen)->format('H:i:s') }}</td>
                            <td>
                                @if($absen->status == 'hadir')
                                    <span class="badge bg-success">Hadir</span>
                                @elseif($absen->status == 'terlambat')
                                    <span class="badge bg-warning">Terlambat</span>
                                @else
                                    <span class="badge bg-danger">Luar Radius</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ asset('storage/' . $absen->foto) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $absen->foto) }}" alt="Foto" width="50" class="rounded">
                                </a>
                            </td>
                            <td>
                                <a href="https://maps.google.com/?q={{ $absen->latitude }},{{ $absen->longitude }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-map-marker-alt"></i> Map
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada pegawai yang absen.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
