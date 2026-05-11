<x-data-table>
    <x-slot name="head">
        <x-sort-icon field="name" label="Nama User" />
        <x-sort-icon field="email" label="Email" />
        <th>Pegawai Terhubung</th>
        <th>Role</th>
        <th class="text-end px-4">Aksi</th>
    </x-slot>

    <x-slot name="body">
        @forelse($users as $user)
        <tr>
            <td class="px-4"><strong>{{ $user->name }}</strong></td>
            <td>{{ $user->email }}</td>
            <td>
                @if($user->pegawai)
                    <span class="fw-bold text-dark">{{ $user->pegawai->nama }}</span><br>
                    <code class="small text-muted">{{ $user->pegawai->nip }}</code>
                @else
                    <span class="text-danger small">Belum terhubung</span>
                @endif
            </td>
            <td>
                @foreach($user->roles as $role)
                    <span class="badge bg-info text-white">{{ str_replace('_', ' ', strtoupper($role->name)) }}</span>
                @endforeach
            </td>
            <td class="text-end px-4">
                <a href="{{ route('user.edit', $user->id) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('user.destroy', $user->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus user ini?')">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center py-4 text-muted">Data user tidak ditemukan.</td>
        </tr>
        @endforelse
    </x-slot>

    <x-slot name="pagination">
        <div class="px-3">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    </x-slot>
</x-data-table>
