@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        {{-- <div class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Master Pegawai</h1>
            @hasanyrole('super_admin|admin')
                <div class="d-flex gap-2">
                    <a href="{{ route('pegawai.template.download') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-download me-1"></i> Template
                    </a>
                    <a href="{{ route('pegawai.import.index') }}" class="btn btn-success">
                        <i class="fas fa-file-excel me-1"></i> Import Excel
                    </a>
                </div>
            @endhasanyrole
        </div> --}}

        <x-filter-card title="Master Pegawai"
            createRoute="{{ auth()->user()->hasAnyRole(['super_admin', 'admin'])? route('pegawai.create'): null }}"
            createText="Tambah Pegawai"
            exportExcel="{{ route('pegawai.index', array_merge(request()->query(), ['export' => 'excel'])) }}"
            exportPdf="{{ route('pegawai.index', array_merge(request()->query(), ['export' => 'pdf'])) }}">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm"
                    placeholder="Cari NIP, Nama, Jabatan..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="ruangan_id" class="form-select form-select-sm">
                    <option value="">Semua Ruangan</option>
                    @foreach ($ruangans as $r)
                        <option value="{{ $r->id }}" {{ request('ruangan_id') == $r->id ? 'selected' : '' }}>
                            {{ $r->nama_ruangan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="kategori_kerja" class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    <option value="non_shift" {{ request('kategori_kerja') == 'non_shift' ? 'selected' : '' }}>Non Shift
                    </option>
                    <option value="shift" {{ request('kategori_kerja') == 'shift' ? 'selected' : '' }}>Shift</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status_aktif" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('status_aktif') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status_aktif') == '0' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
            </div>
        </x-filter-card>

        @include('pegawai._table')
    </div>

    @push('scripts')
        <script src="{{ asset('js/datatable.js') }}"></script>
    @endpush
@endsection
