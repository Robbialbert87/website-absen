@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Atur Jadwal Kerja Pegawai</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('jadwal.index') }}">Jadwal Kerja</a></li>
                <li class="breadcrumb-item active">Atur</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('jadwal.create') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small text-muted text-uppercase fw-bold">Ruangan</label>
                <select name="ruangan_id" class="form-select select2" required>
                    <option value="">Pilih Ruangan</option>
                    @foreach($ruangans as $r)
                        <option value="{{ $r->id }}" {{ $ruangan_id == $r->id ? 'selected' : '' }}>{{ $r->nama_ruangan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted text-uppercase fw-bold">Bulan</label>
                <select name="bulan" class="form-select">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ sprintf('%02d', $i) }}" {{ $bulan == sprintf('%02d', $i) ? 'selected' : '' }}>
                            {{ Carbon\Carbon::create(2000, $i, 1)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted text-uppercase fw-bold">Tahun</label>
                <select name="tahun" class="form-select">
                    @for($i = date('Y') - 1; $i <= date('Y') + 1; $i++)
                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-sync me-1"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>
</div>

@if($ruangan_id && count($pegawais) > 0)
<form action="{{ route('jadwal.store') }}" method="POST">
    @csrf
    <input type="hidden" name="ruangan_id" value="{{ $ruangan_id }}">
    <input type="hidden" name="bulan" value="{{ $bulan }}">
    <input type="hidden" name="tahun" value="{{ $tahun }}">

    <div class="card shadow-sm overflow-hidden mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold"><i class="fas fa-th me-2 text-primary"></i> Matrix Penjadwalan</h6>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-danger btn-sm px-3" id="btnReset">
                    <i class="fas fa-trash-alt me-2"></i> Reset Jadwal
                </button>
                <button type="submit" class="btn btn-success btn-sm px-4">
                    <i class="fas fa-save me-2"></i> Simpan Jadwal
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0 align-middle">
                <thead class="bg-light text-center">
                    <tr>
                        <th style="min-width: 200px;">Nama Pegawai</th>
                        @foreach($dates as $date)
                            @php
                                $hariId = [
                                    'Sunday'    => 'Min',
                                    'Monday'    => 'Sen',
                                    'Tuesday'   => 'Sel',
                                    'Wednesday' => 'Rab',
                                    'Thursday'  => 'Kam',
                                    'Friday'    => 'Jum',
                                    'Saturday'  => 'Sab',
                                ][$date->format('l')];
                            @endphp
                            @php
                                $day = $date->format('j');
                                $holiday = $holidays[$day] ?? null;
                                $isSunday = $date->isWeekend();
                                $bgColor = '';
                                if ($holiday) {
                                    $bgColor = $holiday->jenis === 'cuti_bersama' ? '#fff9db' : '#ffeaea';
                                } elseif ($isSunday) {
                                    $bgColor = '#ffeaea';
                                }
                            @endphp
                            <th style="min-width: 55px; background-color: {{ $bgColor }}; font-size: 11px; vertical-align: middle;"
                                @if($holiday) data-bs-toggle="tooltip" title="{{ $holiday->nama_hari_libur }}" @endif>
                                <span class="fw-bold d-block" style="font-size: 13px; line-height: 1.2;">{{ $date->format('j') }}</span>
                                <span class="{{ $isSunday || $holiday ? 'text-danger' : 'text-primary' }}" style="font-size: 10px;">{{ $hariId }}</span>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($pegawais as $p)
                    <tr>
                        <td class="px-3">
                            <div class="fw-bold" style="font-size: 0.9rem;">{{ $p->nama }}</div>
                            <small class="text-muted" style="font-size: 0.75rem;">{{ $p->nip }} ({{ $p->kategori_kerja == 'shift' ? 'Shift' : ($p->kategori_kerja == 'non_shift_5_hari' ? 'Non Shift 5 Hari' : 'Non-Shift') }})</small>
                        </td>
                        @foreach($dates as $date)
                            @php
                                $day = $date->format('j');
                                $existingItem = $jadwal_existing[$p->id][$day][0] ?? null;
                                $currentShiftId = $existingItem?->shift_id ?? null;

                                // Auto-fill for non-shift employees if no existing jadwal
                                $isAutoFilled = false;
                                $holiday = $holidays[$day] ?? null;

                                if (!$currentShiftId && ($p->kategori_kerja == 'non_shift' || $p->kategori_kerja == 'non_shift_5_hari')) {
                                    if ($holiday) {
                                        // Priority 1: National Holiday / Cuti Bersama
                                        $libur = $shifts->where('kode_shift', 'L')->first() 
                                                ?? $shifts->where('kode_shift', 'Libur')->first()
                                                ?? $shifts->filter(fn($s) => str_contains(strtolower($s->nama_shift), 'libur'))->first();
                                        
                                        if ($libur) {
                                            $currentShiftId = $libur->id;
                                            $isAutoFilled = true;
                                        }
                                    } else {
                                        // Priority 2: Weekly Schedule
                                        $dayOfWeek = $date->dayOfWeek; // 0=Sun, 1=Mon, ..., 6=Sat
                                        if (isset($prefillShiftByDay[$dayOfWeek])) {
                                            $currentShiftId = $prefillShiftByDay[$dayOfWeek]->id;
                                            $isAutoFilled = true;
                                        }
                                    }
                                }

                                // Background color logic
                                $cellBg = '';
                                if ($isAutoFilled) {
                                    $cellBg = 'background-color: #f0fdf4;'; // Light green for auto-filled
                                }
                                if ($holiday) {
                                    $cellBg = $holiday->jenis === 'cuti_bersama' ? 'background-color: #fff9db;' : 'background-color: #fff5f5;';
                                } elseif ($date->isWeekend()) {
                                    $cellBg = 'background-color: #fff5f5;';
                                }
                            @endphp
                            <td class="p-1" style="{{ $cellBg }}">
                                <select name="jadwal[{{ $p->id }}][{{ $day }}]"
                                    class="form-select form-select-sm border-0 p-0 text-center {{ $isAutoFilled ? 'bg-success bg-opacity-10' : 'bg-light' }}"
                                    style="font-size: 11px; height: 30px;"
                                    title="{{ $isAutoFilled ? 'Terisi otomatis' : '' }}">
                                    <option value="">-</option>
                                    @foreach($shifts as $s)
                                        {{-- Show only relevant shifts per kategori, plus 'cuti' for both --}}
                                        @if($s->kategori_jadwal == 'cuti')
                                            <option value="{{ $s->id }}" {{ $currentShiftId == $s->id ? 'selected' : '' }}>{{ $s->kode_shift }}</option>
                                        @elseif($p->kategori_kerja == 'shift' && $s->kategori_jadwal == 'shift')
                                            <option value="{{ $s->id }}" {{ $currentShiftId == $s->id ? 'selected' : '' }}>{{ $s->kode_shift }}</option>
                                        @elseif(($p->kategori_kerja == 'non_shift' || $p->kategori_kerja == 'non_shift_5_hari') && ($s->kategori_jadwal == 'non_shift' || $s->kategori_jadwal == 'non_shift_5_hari'))
                                            <option value="{{ $s->id }}" {{ $currentShiftId == $s->id ? 'selected' : '' }}>{{ $s->kode_shift }}</option>
                                        @endif
                                    @endforeach
                                    {{-- Always allow Libur --}}
                                    @php $libur = $shifts->where('kode_shift', 'L')->first(); @endphp
                                    @if($libur && ($p->kategori_kerja == 'non_shift' || $p->kategori_kerja == 'non_shift_5_hari') && $libur->kategori_jadwal != 'non_shift' && $libur->kategori_jadwal != 'non_shift_5_hari')
                                        <option value="{{ $libur->id }}" {{ $currentShiftId == $libur->id ? 'selected' : '' }}>L</option>
                                    @endif
                                </select>
                            </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white text-end d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-outline-danger px-4" id="btnResetFooter">
                <i class="fas fa-trash-alt me-2"></i> Reset Jadwal
            </button>
            <button type="submit" class="btn btn-success px-5">
                <i class="fas fa-save me-2"></i> Simpan Semua Jadwal
            </button>
        </div>
    </div>
</form>
@elseif($ruangan_id)
<div class="alert alert-info border-0 shadow-sm">
    <i class="fas fa-info-circle me-2"></i> Tidak ada pegawai ditemukan di ruangan ini.
</div>
@endif

<style>
    .table th, .table td {
        vertical-align: middle;
    }
    .form-select-sm {
        min-width: 50px;
    }
    .form-select:focus {
        box-shadow: none;
        background-color: #fff !important;
    }
</style>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5'
        });

        $('#btnReset, #btnResetFooter').on('click', function() {
            if (confirm('Apakah Anda yakin ingin mereset semua jadwal yang sudah dipilih di tabel ini?')) {
                $('table select').val('').trigger('change');
            }
        });
    });
</script>
@endpush
