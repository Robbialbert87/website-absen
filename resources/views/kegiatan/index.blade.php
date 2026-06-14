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

    <div class="card shadow-sm border-0" style="border-radius: 15px;">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Kegiatan</th>
                            <th>Tanggal & Waktu</th>
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
                            <td>{{ $item->lokasi }}</td>
                            <td>
                                @if($item->status == 'aktif')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Selesai</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('kegiatan.show', $item->id) }}" class="btn btn-sm btn-info text-white" title="Lihat Absensi"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('kegiatan.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('kegiatan.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada data kegiatan.</td>
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
</div>
@endsection
