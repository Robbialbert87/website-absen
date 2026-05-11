@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Pegawai</h1>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('pegawai.update', $pegawai->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">NIP</label>
                                <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror" value="{{ old('nip', $pegawai->nip) }}" required>
                                @error('nip')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $pegawai->nama) }}" required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Ruangan</label>
                                <select name="ruangan_id" id="ruangan_id" class="form-select @error('ruangan_id') is-invalid @enderror" required>
                                    @if($pegawai->ruangan)
                                        <option value="{{ $pegawai->ruangan_id }}" selected>{{ $pegawai->ruangan->nama_ruangan }} ({{ $pegawai->ruangan->kode_ruangan }})</option>
                                    @else
                                        <option value="">Cari Ruangan...</option>
                                    @endif
                                </select>
                                @error('ruangan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jabatan</label>
                                <input type="text" name="jabatan" class="form-control @error('jabatan') is-invalid @enderror" value="{{ old('jabatan', $pegawai->jabatan) }}" required>
                                @error('jabatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status_aktif" class="form-select">
                                    <option value="1" {{ old('status_aktif', $pegawai->status_aktif) == '1' ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ old('status_aktif', $pegawai->status_aktif) == '0' ? 'selected' : '' }}>Non-Aktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <div class="mb-0">
                                    <label class="form-label d-block fw-bold">Kategori Kerja</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="kategori_kerja" id="kat_non_shift" value="non_shift" {{ old('kategori_kerja', $pegawai->kategori_kerja) == 'non_shift' ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="kat_non_shift">Non Shift (Jadwal Tetap)</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="kategori_kerja" id="kat_shift" value="shift" {{ old('kategori_kerja', $pegawai->kategori_kerja) == 'shift' ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="kat_shift">Shift (Rotasi)</label>
                                    </div>
                                    @error('kategori_kerja')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('pegawai.index') }}" class="btn btn-light border">Batal</a>
                            <button type="submit" class="btn btn-primary px-4">Perbarui Pegawai</button>
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
        $('#ruangan_id').select2({
            theme: 'bootstrap-5',
            placeholder: 'Cari Ruangan...',
            ajax: {
                url: "{{ route('ruangan.search') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
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
