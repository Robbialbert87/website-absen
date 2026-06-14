@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between mb-4 gap-2">
        <h1 class="h4 mb-0 fw-bold" style="font-family: 'Playfair Display', serif; color: #0D1E1C;">Manajemen Role & Permission</h1>
        <a href="{{ route('role.create') }}" class="btn btn-primary flex-shrink-0">
            <i class="fas fa-plus me-1"></i> Tambah Role
        </a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4">Nama Role</th>
                            <th>Permissions</th>
                            <th class="text-end px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                        <tr>
                            <td class="px-4"><strong>{{ ucfirst($role->name) }}</strong></td>
                            <td>
                                @foreach($role->permissions as $permission)
                                    <span class="badge bg-secondary mb-1 small">{{ $permission->name }}</span>
                                @endforeach
                            </td>
                            <td class="text-end px-4">
                                <a href="{{ route('role.edit', $role->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($role->name !== 'admin')
                                <form action="{{ route('role.destroy', $role->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus role ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            {{ $roles->links() }}
        </div>
    </div>
</div>
@endsection
