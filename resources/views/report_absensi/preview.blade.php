@forelse ($data as $group)
    <div class="mb-4">
        <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge bg-secondary" style="font-size: 0.8rem;">
                {{ \Carbon\Carbon::parse($group->kegiatan->tanggal_kegiatan)->translatedFormat('d M Y') }}
            </span>
            <span class="badge bg-info" style="font-size: 0.8rem;">
                {{ $group->kegiatan->jam_mulai }} - {{ $group->kegiatan->jam_selesai }}
            </span>
        </div>
        <h5 class="fw-bold mb-1" style="color: #0D1E1C;">{{ $group->kegiatan->nama_kegiatan }}</h5>
        <p class="text-muted small mb-3">
            <i class="fas fa-map-marker-alt me-1"></i> {{ $group->kegiatan->lokasi }}
        </p>

        @foreach ($group->ruanganData as $ruang)
            <div class="card border mb-3" style="border-radius: 12px;">
                <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                    <span class="fw-bold" style="color: #1A7A6E;">
                        <i class="fas fa-door-open me-1"></i> {{ $ruang->ruangan->nama_ruangan ?? 'Tanpa Ruangan' }}
                    </span>
                    <span class="text-muted small">
                        Hadir: <strong class="text-success">{{ $ruang->hadir }}</strong> |
                        Terlambat: <strong class="text-warning">{{ $ruang->terlambat }}</strong> |
                        Tidak: <strong class="text-danger">{{ $ruang->tidak_hadir }}</strong>
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="small text-muted">
                                <tr>
                                    <th class="ps-3">NIP</th>
                                    <th>Nama Pegawai</th>
                                    <th>Jam Masuk</th>
                                    <th>Status</th>
                                    <th class="pe-3">Waktu Absen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($ruang->pegawais as $p)
                                    <tr>
                                        <td class="ps-3"><code>{{ $p->nip }}</code></td>
                                        <td>{{ $p->nama }}</td>
                                        <td>
                                            @if ($p->jam_masuk)
                                                {{ $p->jam_masuk }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($p->status === 'hadir')
                                                <span class="badge bg-success rounded-pill">Hadir</span>
                                            @elseif ($p->status === 'terlambat')
                                                <span class="badge bg-warning text-dark rounded-pill">Terlambat</span>
                                            @else
                                                <span class="badge bg-secondary rounded-pill">Tidak Hadir</span>
                                            @endif
                                        </td>
                                        <td class="pe-3">
                                            @if ($p->waktu_absen)
                                                {{ \Carbon\Carbon::parse($p->waktu_absen)->format('H:i:s') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-muted">Tidak ada pegawai.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@empty
    <div class="text-center py-5 text-muted">
        <i class="fas fa-inbox fs-1 mb-3 d-block" style="color: #dee2e6;"></i>
        Tidak ada data absensi untuk periode ini.
    </div>
@endforelse
