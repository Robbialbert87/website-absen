@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Ruangan</h1>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('ruangan.update', $ruangan->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Kode Ruangan</label>
                            <input type="text" name="kode_ruangan" class="form-control @error('kode_ruangan') is-invalid @enderror" value="{{ old('kode_ruangan', $ruangan->kode_ruangan) }}" required>
                            @error('kode_ruangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Ruangan</label>
                            <input type="text" name="nama_ruangan" class="form-control @error('nama_ruangan') is-invalid @enderror" value="{{ old('nama_ruangan', $ruangan->nama_ruangan) }}" required>
                            @error('nama_ruangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kepala Ruangan</label>
                            <select name="kepala_pegawai_id" id="kepala_pegawai_id" class="form-select @error('kepala_pegawai_id') is-invalid @enderror">
                                <option value=""></option>
                                @if($ruangan->kepalaPegawai)
                                    <option value="{{ $ruangan->kepala_pegawai_id }}" selected>
                                        {{ $ruangan->kepalaPegawai->nama }} ({{ $ruangan->kepalaPegawai->nip }})
                                    </option>
                                @endif
                            </select>
                            @error('kepala_pegawai_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Cari berdasarkan nama pegawai.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $ruangan->keterangan) }}</textarea>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('ruangan.index') }}" class="btn btn-light border">Batal</a>
                            <button type="submit" class="btn btn-primary">Perbarui Ruangan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#kepala_pegawai_id').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih Kepala Ruangan',
            allowClear: true,
            ajax: {
                url: "{{ route('ruangan.search-kepala') }}",
                dataType: 'json',
                delay: 250,
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            minimumInputLength: 2
        });
    });
</script>
@endpush
