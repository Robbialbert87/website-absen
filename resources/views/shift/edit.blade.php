@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Jadwal</h1>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('shift.update', $shift->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Kode Jadwal</label>
                            <input type="text" name="kode_shift" class="form-control @error('kode_shift') is-invalid @enderror" value="{{ old('kode_shift', $shift->kode_shift) }}" required>
                            @error('kode_shift')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Jadwal</label>
                            <input type="text" name="nama_shift" class="form-control @error('nama_shift') is-invalid @enderror" value="{{ old('nama_shift', $shift->nama_shift) }}" required>
                            @error('nama_shift')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kategori Jadwal</label>
                            <select name="kategori_jadwal" class="form-select @error('kategori_jadwal') is-invalid @enderror" required>
                                <option value="non_shift" {{ old('kategori_jadwal', $shift->kategori_jadwal) == 'non_shift' ? 'selected' : '' }}>Non Shift (Biru)</option>
                                <option value="non_shift_5_hari" {{ old('kategori_jadwal', $shift->kategori_jadwal) == 'non_shift_5_hari' ? 'selected' : '' }}>Non Shift 5 Hari (Cyan)</option>
                                <option value="shift" {{ old('kategori_jadwal', $shift->kategori_jadwal) == 'shift' ? 'selected' : '' }}>Shift (Hijau)</option>
                                <option value="cuti" {{ old('kategori_jadwal', $shift->kategori_jadwal) == 'cuti' ? 'selected' : '' }}>Cuti (Kuning)</option>
                            </select>
                            @error('kategori_jadwal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Jam Masuk</label>
                                <input type="time" name="jam_masuk" class="form-control @error('jam_masuk') is-invalid @enderror" value="{{ old('jam_masuk', $shift->jam_masuk) }}" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Jam Pulang</label>
                                <input type="time" name="jam_pulang" class="form-control @error('jam_pulang') is-invalid @enderror" value="{{ old('jam_pulang', $shift->jam_pulang) }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Warna (Hex)</label>
                            <input type="color" name="warna" class="form-control form-control-color w-100" value="{{ old('warna', $shift->warna) }}">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Contoh: Shift malam lintas hari">{{ old('keterangan', $shift->keterangan) }}</textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Hari Aktif (untuk pegawai Non-Shift)</label>
                            <p class="text-muted small mb-2">Tentukan hari apa jadwal ini berlaku sebagai jadwal standar pegawai Non-Shift.</p>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach([
                                    'is_senin' => 'Senin',
                                    'is_selasa' => 'Selasa',
                                    'is_rabu' => 'Rabu',
                                    'is_kamis' => 'Kamis',
                                    'is_jumat' => 'Jumat',
                                    'is_sabtu' => 'Sabtu',
                                    'is_minggu' => 'Minggu',
                                ] as $field => $label)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="{{ $field }}" id="{{ $field }}" value="1"
                                            {{ old($field, $shift->$field) ? 'checked' : '' }}>
                                        <label class="form-check-label badge rounded-pill border text-dark" for="{{ $field }}" style="cursor:pointer; font-size: 0.85rem;">{{ $label }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('shift.index') }}" class="btn btn-light border">Batal</a>
                            <button type="submit" class="btn btn-primary">Perbarui Jadwal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
