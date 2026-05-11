<x-data-table>
    <x-slot name="head">
        <x-sort-icon field="kode_ruangan" label="Kode" />
        <x-sort-icon field="nama_ruangan" label="Nama Ruangan" />
        <x-sort-icon field="kepala_pegawai_id" label="Kepala Ruangan" />
        <th>Keterangan</th>
        <th class="text-end px-4">Aksi</th>
    </x-slot>

    <x-slot name="body">
        @forelse($ruangan as $r)
        <tr>
            <td class="px-4"><span class="badge bg-secondary">{{ $r->kode_ruangan }}</span></td>
            <td><strong>{{ $r->nama_ruangan }}</strong></td>
            <td>
                @if($r->kepalaPegawai)
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px; font-size: 12px;">
                            {{ strtoupper(substr($r->kepalaPegawai->nama, 0, 1)) }}
                        </div>
                        <span>{{ $r->kepalaPegawai->nama }}</span>
                    </div>
                @else
                    <span class="text-muted small">Belum ditentukan</span>
                @endif
            </td>
            <td>{{ $r->keterangan }}</td>
            <td class="text-end px-4">
                <a href="{{ route('ruangan.edit', $r->id) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('ruangan.destroy', $r->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus ruangan ini?')">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center py-4 text-muted">Data ruangan tidak ditemukan.</td>
        </tr>
        @endforelse
    </x-slot>

    <x-slot name="pagination">
        <div class="px-3">
            {{ $ruangan->links('pagination::bootstrap-5') }}
        </div>
    </x-slot>
</x-data-table>
