<x-data-table>
    <x-slot name="head">
        <x-sort-icon field="nip" label="NIP" />
        <x-sort-icon field="nama" label="Nama Pegawai" />
        <x-sort-icon field="kategori_kerja" label="Kategori" />
        <x-sort-icon field="ruangan_id" label="Ruangan" />
        <x-sort-icon field="jabatan" label="Jabatan" />
        <x-sort-icon field="status_aktif" label="Status" />
        @hasanyrole('super_admin|admin')
        <th class="text-end px-4">Aksi</th>
        @endhasanyrole
    </x-slot>

    <x-slot name="body">
        @forelse($pegawai as $p)
        <tr>
            <td class="px-4"><code>{{ $p->nip }}</code></td>
            <td><strong>{{ $p->nama }}</strong></td>
            <td>
                @if($p->kategori_kerja == 'shift')
                    <span class="badge bg-success">Shift</span>
                @elseif($p->kategori_kerja == 'non_shift_5_hari')
                    <span class="badge bg-info">Non Shift 5 Hari</span>
                @else
                    <span class="badge bg-primary">Non Shift</span>
                @endif
            </td>
            <td>{{ $p->ruangan->nama_ruangan ?? '-' }}</td>
            <td>{{ $p->jabatan }}</td>
            <td>
                @if($p->status_aktif)
                    <span class="badge bg-success">Aktif</span>
                @else
                    <span class="badge bg-secondary">Non-Aktif</span>
                @endif
            </td>
            @hasanyrole('super_admin|admin')
            <td class="text-end px-4">
                <a href="{{ route('pegawai.edit', $p->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('pegawai.destroy', $p->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus pegawai ini?')" title="Hapus">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </td>
            @endhasanyrole
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center py-4 text-muted">Data pegawai tidak ditemukan.</td>
        </tr>
        @endforelse
    </x-slot>

    <x-slot name="pagination">
        <div class="px-3">
            {{ $pegawai->links('pagination::bootstrap-5') }}
        </div>
    </x-slot>
</x-data-table>
