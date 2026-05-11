@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Jadwal Kerja Pegawai</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Jadwal Kerja</li>
                </ol>
            </nav>
        </div>
        @can('manage-jadwal')
            <a href="{{ route('jadwal.create', ['ruangan_id' => $selected_ruangan_id, 'bulan' => $bulan, 'tahun' => $tahun]) }}"
                class="btn btn-primary">
                <i class="fas fa-edit me-2"></i> Atur Jadwal
            </a>
        @endcan
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('jadwal.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small text-muted text-uppercase fw-bold">Ruangan</label>
                    <select name="ruangan_id" class="form-select select2" onchange="this.form.submit()">
                        @foreach ($ruangans as $r)
                            <option value="{{ $r->id }}" {{ $selected_ruangan_id == $r->id ? 'selected' : '' }}>
                                {{ $r->nama_ruangan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted text-uppercase fw-bold">Bulan</label>
                    <select name="bulan" class="form-select" onchange="this.form.submit()">
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ sprintf('%02d', $i) }}" {{ $bulan == sprintf('%02d', $i) ? 'selected' : '' }}>
                                {{ Carbon\Carbon::create(2000, $i, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted text-uppercase fw-bold">Tahun</label>
                    <select name="tahun" class="form-select" onchange="this.form.submit()">
                        @for ($i = date('Y') - 1; $i <= date('Y') + 1; $i++)
                            <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0">
                <thead class="bg-light text-center align-middle">
                    <tr>
                        <th rowspan="2" style="min-width: 200px;">Nama Pegawai</th>
                        <th colspan="{{ count($dates) }}">
                            {{ Carbon\Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y') }}</th>
                    </tr>
                    <tr>
                        @foreach ($dates as $date)
                            @php
                                $hariId = [
                                    'Sunday' => 'Min',
                                    'Monday' => 'Sen',
                                    'Tuesday' => 'Sel',
                                    'Wednesday' => 'Rab',
                                    'Thursday' => 'Kam',
                                    'Friday' => 'Jum',
                                    'Saturday' => 'Sab',
                                ][$date->format('l')];
                            @endphp
                            <th
                                style="min-width: 40px; background-color: {{ $date->isWeekend() ? '#ffeaea' : '' }}; font-size: 11px; text-align: center; vertical-align: middle;">
                                <span class="fw-bold d-block"
                                    style="font-size: 13px; line-height: 1.2;">{{ $date->format('j') }}</span>
                                <span class="{{ $date->isWeekend() ? 'text-danger' : 'text-primary' }}"
                                    style="font-size: 10px;">{{ $hariId }}</span>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($pegawais as $p)
                        <tr>
                            <td class="px-3">
                                <div class="fw-bold">{{ $p->nama }}</div>
                                <small class="text-muted" style="font-size: 0.75rem;">{{ $p->nip }}
                                    ({{ $p->kategori_kerja == 'shift' ? 'Shift' : 'Non-Shift' }})
                                </small>

                            </td>
                            @foreach ($dates as $date)
                                @php
                                    $day = $date->format('j');
                                    $item = $jadwal[$p->id][$day][0] ?? null;
                                    $bgColor = $item && $item->shift ? $item->shift->warna : '';
                                    $hariId = [
                                        'Sunday' => 'Min',
                                        'Monday' => 'Sen',
                                        'Tuesday' => 'Sel',
                                        'Wednesday' => 'Rab',
                                        'Thursday' => 'Kam',
                                        'Friday' => 'Jum',
                                        'Saturday' => 'Sab',
                                    ][$date->format('l')];
                                @endphp
                                <td class="text-center align-middle"
                                    style="background-color: {{ $bgColor ?: ($date->isWeekend() ? '#fff5f5' : '') }}; color: {{ $bgColor ? '#fff' : '' }}">
                                    @if ($item)
                                        <small class="fw-bold text-uppercase"
                                            title="{{ $item->jam_masuk }} - {{ $item->jam_pulang }}">
                                            {{ $hariId }}
                                            </span>
                                        @else
                                            <span class="text-muted small">-</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($dates) + 1 }}" class="text-center py-5 text-muted">
                                Belum ada data pegawai di ruangan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        <h6>Keterangan:</h6>
        <div class="d-flex flex-wrap gap-3 small">
            @foreach ($shifts as $s)
                <div>
                    <span class="badge"
                        style="background-color: {{ $s->warna }}; color: #fff">{{ $s->kode_shift }}</span>
                    {{ $s->nama_shift }} ({{ substr($s->jam_masuk, 0, 5) }} - {{ substr($s->jam_pulang, 0, 5) }})
                </div>
            @endforeach
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5'
            });
        });
    </script>
@endpush
