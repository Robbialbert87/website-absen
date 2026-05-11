@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        {{-- <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Master Ruangan</h1>
        <div>
            <a href="{{ route('ruangan.import.index') }}" class="btn btn-success me-2">
                <i class="fas fa-file-import me-1"></i> Import Excel
            </a>
            <a href="{{ route('ruangan.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Tambah Ruangan
            </a>
        </div>
    </div> --}}

        <x-filter-card title="Master Ruangan" createRoute="{{ route('ruangan.create') }}" createText="Tambah Ruangan"
            exportExcel="{{ route('ruangan.index', array_merge(request()->query(), ['export' => 'excel'])) }}"
            exportPdf="{{ route('ruangan.index', array_merge(request()->query(), ['export' => 'pdf'])) }}">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm"
                    placeholder="Cari Kode, Nama Ruangan..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="kepala_pegawai_id" class="form-select form-select-sm">
                    <option value="">Semua Kepala Ruangan</option>
                    @foreach ($kepalaRuangan as $kepala)
                        <option value="{{ $kepala->id }}"
                            {{ request('kepala_pegawai_id') == $kepala->id ? 'selected' : '' }}>{{ $kepala->nama }}</option>
                    @endforeach
                </select>
            </div>
        </x-filter-card>

        @include('ruangan._table')
    </div>

    @push('scripts')
        <script src="{{ asset('js/datatable.js') }}"></script>
    @endpush
@endsection
