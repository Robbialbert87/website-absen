@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <h1 class="h3 mb-0 text-gray-800">Tambah Jadwal</h1>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('shift.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Kode Jadwal</label>
                                <input type="text" name="kode_shift"
                                    class="form-control @error('kode_shift') is-invalid @enderror"
                                    value="{{ old('kode_shift') }}" placeholder="Contoh: P" required>
                                @error('kode_shift')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Jadwal</label>
                                <input type="text" name="nama_shift"
                                    class="form-control @error('nama_shift') is-invalid @enderror"
                                    value="{{ old('nama_shift') }}" placeholder="Contoh: Pagi" required>
                                @error('nama_shift')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kategori Jadwal</label>
                                <select name="kategori_jadwal"
                                    class="form-select @error('kategori_jadwal') is-invalid @enderror" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="non_shift" {{ old('kategori_jadwal') == 'non_shift' ? 'selected' : '' }}>
                                        Non Shift (Biru)</option>
                                    <option value="shift" {{ old('kategori_jadwal') == 'shift' ? 'selected' : '' }}>Shift
                                        (Hijau)</option>
                                    <option value="cuti" {{ old('kategori_jadwal') == 'cuti' ? 'selected' : '' }}>Cuti (Kuning)</option>
                                </select>
                                @error('kategori_jadwal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label">Jam Masuk</label>
                                    <input type="time" name="jam_masuk"
                                        class="form-control @error('jam_masuk') is-invalid @enderror"
                                        value="{{ old('jam_masuk') }}" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Jam Pulang</label>
                                    <input type="time" name="jam_pulang"
                                        class="form-control @error('jam_pulang') is-invalid @enderror"
                                        value="{{ old('jam_pulang') }}" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Warna (Hex)</label>
                                <input type="color" name="warna" class="form-control form-control-color w-100"
                                    value="{{ old('warna', '#3498db') }}">
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="2" placeholder="Contoh: Shift malam lintas hari">{{ old('keterangan') }}</textarea>
                            </div>
                            <div class="mb-4" id="day-indicator-section">
                                <label class="form-label fw-bold">Hari Aktif (untuk pegawai Non-Shift)</label>
                                <p class="text-muted small mb-2">Tentukan hari apa jadwal ini berlaku sebagai jadwal standar
                                    pegawai Non-Shift.</p>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ([
            'is_senin' => 'Sen',
            'is_selasa' => 'Sel',
            'is_rabu' => 'Rab',
            'is_kamis' => 'Kam',
            'is_jumat' => 'Jum',
            'is_sabtu' => 'Sab',
            'is_minggu' => 'Min',
        ] as $field => $label)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="{{ $field }}"
                                                id="{{ $field }}" value="1"
                                                {{ old($field) ? 'checked' : '' }}>
                                            <label class="form-check-label badge rounded-pill border text-dark"
                                                for="{{ $field }}"
                                                style="cursor:pointer; font-size: 0.85rem;">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('shift.index') }}" class="btn btn-light border">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
