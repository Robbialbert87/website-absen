@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        {{-- <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen User</h1>
        <a href="{{ route('user.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Tambah User
        </a>
    </div> --}}

        <x-filter-card title="Manajemen User" createRoute="{{ route('user.create') }}" createText="Tambah User"
            exportExcel="{{ route('user.index', array_merge(request()->query(), ['export' => 'excel'])) }}"
            exportPdf="{{ route('user.index', array_merge(request()->query(), ['export' => 'pdf'])) }}">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm"
                    placeholder="Cari Nama User..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="role" class="form-select form-select-sm">
                    <option value="">Semua Role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                            {{ str_replace('_', ' ', strtoupper($role->name)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select name="ruangan_id" class="form-select form-select-sm">
                    <option value="">Semua Ruangan</option>
                    @foreach ($ruangans as $r)
                        <option value="{{ $r->id }}" {{ request('ruangan_id') == $r->id ? 'selected' : '' }}>
                            {{ $r->nama_ruangan }}</option>
                    @endforeach
                </select>
            </div>
        </x-filter-card>

        @include('user._table')
    </div>

    @push('scripts')
        <script src="{{ asset('js/datatable.js') }}"></script>
    @endpush
@endsection
