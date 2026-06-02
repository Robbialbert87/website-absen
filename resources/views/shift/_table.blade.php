<x-data-table>
    <x-slot name="head">
        <x-sort-icon field="kode_shift" label="Kode" />
        <x-sort-icon field="nama_shift" label="Nama Jadwal" />
        <x-sort-icon field="kategori_jadwal" label="Kategori" />
        <x-sort-icon field="jam_masuk" label="Jam Masuk" />
        <x-sort-icon field="jam_pulang" label="Jam Pulang" />
        <th>Warna</th>
        <th class="text-end px-4">Aksi</th>
    </x-slot>

    <x-slot name="body">
        @forelse($shifts as $s)
        <tr>
            <td class="px-4"><span class="badge bg-secondary">{{ $s->kode_shift }}</span></td>
            <td>
                <strong>{{ $s->nama_shift }}</strong>
                @if($s->keterangan)
                    <br><small class="text-muted">{{ $s->keterangan }}</small>
                @endif
            </td>
            <td>
                @if($s->kategori_jadwal == 'non_shift')
                    <span class="badge bg-primary">Non Shift</span>
                @elseif($s->kategori_jadwal == 'non_shift_5_hari')
                    <span class="badge bg-info">Non Shift 5 Hari</span>
                @elseif($s->kategori_jadwal == 'cuti')
                    <span class="badge bg-warning text-dark">Cuti</span>
                @else
                    <span class="badge bg-success">Shift</span>
                @endif
            </td>
            <td><code>{{ $s->jam_masuk }}</code></td>
            <td>
                <code>{{ $s->jam_pulang }}</code>
                @if($s->isCrossDay())
                    <span class="badge bg-warning text-dark ms-1" title="Selesai keesokan hari"><i class="fas fa-moon"></i></span>
                @endif
            </td>
            <td>
                <div style="width: 20px; height: 20px; border-radius: 4px; background-color: {{ $s->warna }}; display: inline-block; vertical-align: middle;"></div>
                <span class="ms-1 small text-muted">{{ $s->warna }}</span>
            </td>
            <td class="text-end px-4">
                <a href="{{ route('shift.edit', $s->id) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('shift.destroy', $s->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus jadwal ini?')">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center py-4 text-muted">Data jadwal tidak ditemukan.</td>
        </tr>
        @endforelse
    </x-slot>

    <x-slot name="pagination">
        <div class="px-3">
            {{ $shifts->links('pagination::bootstrap-5') }}
        </div>
    </x-slot>
</x-data-table>
