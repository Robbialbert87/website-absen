@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        {{-- <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Master Jadwal</h1>
        <a href="{{ route('shift.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Tambah Jadwal
        </a>
    </div> --}}

        <x-filter-card title="Master Jadwal" createRoute="{{ route('shift.create') }}" createText="Tambah Jadwal"
            exportExcel="{{ route('shift.index', array_merge(request()->query(), ['export' => 'excel'])) }}"
            exportPdf="{{ route('shift.index', array_merge(request()->query(), ['export' => 'pdf'])) }}">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm"
                    placeholder="Cari Kode, Nama Jadwal..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="kategori_jadwal" class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    <option value="non_shift" {{ request('kategori_jadwal') == 'non_shift' ? 'selected' : '' }}>Non Shift
                    </option>
                    <option value="shift" {{ request('kategori_jadwal') == 'shift' ? 'selected' : '' }}>Shift</option>
                    <option value="cuti" {{ request('kategori_jadwal') == 'cuti' ? 'selected' : '' }}>Cuti</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="jam_kerja" class="form-control form-control-sm"
                    placeholder="Filter Jam (cth: 08:00-16:00)" value="{{ request('jam_kerja') }}">
            </div>
        </x-filter-card>

        @include('shift._table')
    </div>

    @push('scripts')
        <script src="{{ asset('js/datatable.js') }}"></script>
    @endpush
@endsection
