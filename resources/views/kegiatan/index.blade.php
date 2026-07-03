@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between mb-4 gap-2">
        <h1 class="h4 mb-0 fw-bold" style="font-family: 'Playfair Display', serif; color: #0D1E1C;">Manajemen Kegiatan</h1>
        <a href="{{ route('kegiatan.create') }}" class="btn text-white rounded-pill flex-shrink-0" style="background-color: #1A7A6E;">
            <i class="fas fa-plus me-1"></i> Tambah Kegiatan
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Desktop: Table --}}
    <div class="card shadow-sm border-0 d-none d-md-block" style="border-radius: 15px;">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Kegiatan</th>
                            <th>Tanggal & Waktu</th>
                            <th>Tipe</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kegiatans as $item)
                        <tr>
                            <td class="fw-bold">{{ $item->nama_kegiatan }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($item->tanggal_kegiatan)->format('d M Y') }}<br>
                                <small class="text-muted">{{ $item->jam_mulai }} - {{ $item->jam_selesai }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info text-white" style="font-size: 0.75rem;">
                                    @switch($item->tipe)
                                        @case('apel') Apel @break
                                        @case('kegiatan_langsung') Langsung @break
                                        @default Biasa
                                    @endswitch
                                </span>
                            </td>
                            <td>{{ $item->lokasi }}</td>
                            <td>
                                @if($item->status == 'aktif')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Selesai</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-1 flex-nowrap justify-content-end action-btn-group">
                                    <button type="button" class="btn btn-sm btn-info text-white" onclick="window.location.href='{{ route('kegiatan.show', $item->id) }}'" title="Lihat Absensi"><i class="fas fa-eye"></i></button>
                                    <button type="button" class="btn btn-sm btn-warning" onclick="window.location.href='{{ route('kegiatan.edit', $item->id) }}'" title="Edit"><i class="fas fa-edit"></i></button>
                                    <form action="{{ route('kegiatan.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada data kegiatan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $kegiatans->links() }}
            </div>
        </div>
    </div>

    {{-- Mobile: Card View --}}
    <div class="d-md-none">
        @forelse($kegiatans as $item)
            <div class="card shadow-sm border-0 mb-3" style="border-radius: 15px;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="fw-bold mb-0" style="color: #0D1E1C;">{{ $item->nama_kegiatan }}</h6>
                            <span class="badge bg-info text-white mt-1" style="font-size: 0.65rem;">
                                @switch($item->tipe)
                                    @case('apel') Apel @break
                                    @case('kegiatan_langsung') Langsung @break
                                    @default Biasa
                                @endswitch
                            </span>
                        </div>
                        @if($item->status == 'aktif')
                            <span class="badge bg-success rounded-pill flex-shrink-0">Aktif</span>
                        @else
                            <span class="badge bg-secondary rounded-pill flex-shrink-0">Selesai</span>
                        @endif
                    </div>

                    <div class="small text-muted mb-1">
                        <i class="fas fa-calendar-alt me-1" style="width: 14px;"></i>
                        {{ \Carbon\Carbon::parse($item->tanggal_kegiatan)->format('d M Y') }}
                        &middot; {{ $item->jam_mulai }} - {{ $item->jam_selesai }}
                    </div>

                    <div class="small text-muted mb-3">
                        <i class="fas fa-map-marker-alt me-1" style="width: 14px;"></i>
                        {{ $item->lokasi }}
                    </div>

                    <div class="d-flex gap-2 action-btn-group">
                        <button type="button" class="btn btn-sm btn-info text-white flex-fill" onclick="window.location.href='{{ route('kegiatan.show', $item->id) }}'">
                            <i class="fas fa-eye"></i> Lihat
                        </button>
                        <button type="button" class="btn btn-sm btn-warning flex-fill" onclick="window.location.href='{{ route('kegiatan.edit', $item->id) }}'">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <form action="{{ route('kegiatan.destroy', $item->id) }}" method="POST" class="d-inline flex-fill" onsubmit="return confirm('Yakin ingin menghapus?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger w-100"><i class="fas fa-trash"></i> Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="fas fa-inbox fs-1 mb-3 d-block" style="color: #dee2e6;"></i>
                Belum ada data kegiatan.
            </div>
        @endforelse

        <div class="mt-3">
            {{ $kegiatans->links() }}
        </div>
    </div>
</div>
@endsection
