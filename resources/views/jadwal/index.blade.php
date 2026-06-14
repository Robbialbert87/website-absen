@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between mb-4">
        <div>
            <h4 class="mb-0 fw-bold" style="font-family: 'Playfair Display', serif; color: #0D1E1C;">Jadwal Kerja Pegawai</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Jadwal Kerja</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('jadwal.index') }}" method="GET" class="row g-3 align-items-end">
                @if (auth()->user()->isAdmin() || auth()->user()->hasRole('super-admin'))
                    <div class="col-md-3">
                        <label class="form-label small text-muted text-uppercase fw-bold">
                            Pilih Ruangan
                        </label>

                        <select name="ruangan_id" id="ruangan_id" class="form-select select2" onchange="this.form.submit()">
                            <option value="all" {{ $selected_ruangan_id == 'all' ? 'selected' : '' }}>
                                -- Semua Ruangan (All) --
                            </option>

                            @foreach ($ruangans as $r)
                                <option value="{{ $r->id }}" {{ $selected_ruangan_id == $r->id ? 'selected' : '' }}>
                                    {{ $r->nama_ruangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <input type="hidden" name="ruangan_id" id="ruangan_id" value="{{ $selected_ruangan_id }}">
                @endif
                <div
                    class="{{ auth()->user()->isAdmin() || auth()->user()->hasRole('super-admin') ? 'col-md-2' : 'col-md-3' }}">
                    <label class="form-label small text-muted text-uppercase fw-bold">Cari Nama Pegawai</label>
                    <div class="input-group">
                        <input type="text" name="search" class="form-select" placeholder="Ketik nama..."
                            value="{{ $search }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted text-uppercase fw-bold">Kategori</label>
                    <select name="kategori_kerja" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua</option>
                        <option value="non_shift" {{ $kategori_kerja == 'non_shift' ? 'selected' : '' }}>Non Shift</option>
                        <option value="non_shift_5_hari" {{ $kategori_kerja == 'non_shift_5_hari' ? 'selected' : '' }}>Non Shift 5 Hari</option>
                        <option value="shift" {{ $kategori_kerja == 'shift' ? 'selected' : '' }}>Shift</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted text-uppercase fw-bold">Bulan</label>
                    <select name="bulan" id="filter_bulan" class="form-select" onchange="this.form.submit()">
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ sprintf('%02d', $i) }}" {{ $bulan == sprintf('%02d', $i) ? 'selected' : '' }}>
                                {{ Carbon\Carbon::create(2000, $i, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted text-uppercase fw-bold">Tahun</label>
                    <select name="tahun" id="filter_tahun" class="form-select" onchange="this.form.submit()">
                        @for ($i = date('Y') - 1; $i <= date('Y') + 1; $i++)
                            <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <div class="d-flex flex-wrap gap-1 justify-content-start justify-content-md-end" style="padding-top: 1.5rem;">
                        <a href="{{ route('jadwal.export-excel', request()->all()) }}" target="_blank" class="btn btn-sm btn-outline-success px-2 py-1"
                            style="font-size: 0.75rem;" title="Export Excel">
                            <i class="fas fa-file-excel"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger px-2 py-1 btn-reset-room"
                            style="font-size: 0.75rem;" title="Reset Jadwal">
                            <i class="fas fa-undo"></i>
                        </button>
                        @if (auth()->user()->isAdmin() || auth()->user()->hasRole('super-admin'))
                            <button type="button" class="btn btn-sm btn-outline-primary px-2 py-1 btn-auto-fill-room"
                                style="font-size: 0.75rem;" title="Auto Input 6 Hari">
                                <i class="fas fa-magic me-1"></i>6 Hari
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-info px-2 py-1 btn-auto-fill-room-5-hari"
                                style="font-size: 0.75rem;" title="Auto Input 5 Hari">
                                <i class="fas fa-magic me-1"></i>5 Hari
                            </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0 matrix-table">
                <thead class="bg-light text-center">
                    <tr>
                        <th rowspan="2" class="px-4"
                            style="min-width: 250px; background: #f8f9fa; z-index: 2; position: sticky; left: 0; border-right: 2px solid #dee2e6;">
                            Nama Pegawai</th>
                        <th colspan="{{ count($dates) }}" class="py-2">
                            {{ Carbon\Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y') }}
                        </th>
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
                            @php
                                $day = $date->format('j');
                                $holiday = $holidays[$day] ?? null;
                                $isSunday = $date->isWeekend();
                                $bgColor = '';
                                if ($holiday) {
                                    $bgColor = $holiday->jenis === 'cuti_bersama' ? '#fff9db' : '#ffe5e5';
                                } elseif ($isSunday) {
                                    $bgColor = '#fff5f5';
                                }
                            @endphp
                            <th style="min-width: 45px; background-color: {{ $bgColor }};" 
                                @if($holiday) data-bs-toggle="tooltip" title="{{ $holiday->nama_hari_libur }}" @endif>
                                <span class="d-block small fw-bold">{{ $date->format('j') }}</span>
                                <span class="d-block {{ $isSunday || $holiday ? 'text-danger' : 'text-muted' }}"
                                    style="font-size: 9px;">{{ $hariId }}</span>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php
                        $weekendMap = [];
                        foreach ($dates as $date) {
                            $weekendMap[$date->format('j')] = $date->isWeekend();
                        }
                    @endphp
                    @forelse($pegawais as $p)
                        <tr>
                            <td class="px-3 py-2 sticky-column" style="background: white; border-right: 2px solid #dee2e6;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="fw-bold text-dark mb-0" style="font-size: 0.9rem;">{{ $p->nama }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.7rem;">{{ $p->nip }}
                                            ({{ $p->kategori_kerja == 'non_shift_5_hari' ? 'Non Shift 5 Hari' : ucfirst($p->kategori_kerja) }})
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-primary btn-sm rounded-pill btn-atur-jadwal px-3"
                                        data-id="{{ $p->id }}" data-nama="{{ $p->nama }}"
                                        data-kategori="{{ $p->kategori_kerja }}" style="font-size: 11px;">
                                        <i class="fas fa-calendar-alt me-1"></i> Atur Jadwal
                                    </button>
                                </div>
                            </td>
                            @foreach ($dates as $date)
                                @php
                                    $day = $date->format('j');
                                    $item = $jadwal[$p->id][$day][0] ?? null;
                                    $holiday = $holidays[$day] ?? null;
                                    $isSunday = $date->isWeekend();
                                    
                                    $cellBg = '';
                                    if ($item && $item->shift) {
                                        $cellBg = $item->shift->warna;
                                    } elseif ($holiday) {
                                        $cellBg = $holiday->jenis === 'cuti_bersama' ? '#fff9db' : '#ffe5e5';
                                    } elseif ($isSunday) {
                                        $cellBg = '#fff5f5';
                                    }
                                    
                                    $textColor = $item && $item->shift ? '#fff' : ($isSunday || $holiday ? '#dc3545' : '#6c757d');
                                @endphp
                                <td class="text-center p-0"
                                    style="height: 45px; background-color: {{ $cellBg }};">
                                    @if ($item)
                                        <span class="fw-bold text-uppercase"
                                            style="font-size: 10px; color: {{ $textColor }}; cursor: pointer;"
                                            title="{{ $item->shift->nama_shift }}: {{ substr($item->jam_masuk, 0, 5) }} - {{ substr($item->jam_pulang, 0, 5) }}"
                                            onclick="showIndividualCalendar('{{ $p->id }}', '{{ $p->nama }}', '{{ $p->kategori_kerja }}')">
                                            {{ $item->shift->nama_shift ?? $item->kode_shift }}
                                        </span>
                                    @else
                                        <span class="text-muted opacity-25" style="font-size: 10px;">-</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($dates) + 1 }}" class="text-center py-5">
                                <img src="https://img.icons8.com/?size=100&id=12773&format=png&color=000000" alt="Empty"
                                    style="height: 120px; justify-self: center;" class="mb-3">
                                <p class="text-muted">Tidak ada data pegawai di ruangan ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($pegawais->hasPages())
            <div class="px-4 py-3 border-top bg-light">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div class="small text-muted fw-medium">
                        Menampilkan <span class="text-dark">{{ $pegawais->firstItem() }}</span> sampai
                        <span class="text-dark">{{ $pegawais->lastItem() }}</span> dari
                        <span class="text-dark">{{ $pegawais->total() }}</span> pegawai
                    </div>
                    <div class="pagination-container">
                        {{ $pegawais->links() }}
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Keterangan Warna Shift (Inside Matrix Card) -->
        <div class="card-footer bg-white border-top py-3">
            <h6 class="fw-bold mb-3 small text-uppercase text-muted">
                <i class="fas fa-info-circle me-2"></i>Keterangan Warna Shift
            </h6>
            <div class="d-flex flex-wrap gap-4">
                @foreach ($shifts as $s)
                    <div class="d-flex align-items-center">
                        <span class="badge me-2"
                            style="background-color: {{ $s->warna }}; width: 12px; height: 12px; border-radius: 50%;">&nbsp;</span>
                        <span class="fw-bold me-1" style="font-size: 0.8rem;">{{ $s->kode_shift }}:</span>
                        <span class="text-muted" style="font-size: 0.8rem;">{{ $s->nama_shift }}
                            ({{ substr($s->jam_masuk, 0, 5) }} - {{ substr($s->jam_pulang, 0, 5) }})
                        </span>
                    </div>
                @endforeach
                <div class="d-flex align-items-center">
                    <span class="badge me-2 bg-light border"
                        style="width: 12px; height: 12px; border-radius: 50%;">&nbsp;</span>
                    <span class="fw-bold me-1" style="font-size: 0.8rem;">-:</span>
                    <span class="text-muted" style="font-size: 0.8rem;">Libur/Belum diatur</span>
                </div>
            </div>
        </div>
    </div>

    <style>
        .matrix-table th,
        .matrix-table td {
            font-size: 0.8rem;
            white-space: nowrap;
        }

        .pagination {
            margin-bottom: 0;
            gap: 2px;
        }

        .pagination .page-link {
            padding: 0.35rem 0.75rem;
            font-size: 0.8rem;
            border-radius: 6px;
            border: none;
            color: #6c757d;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--accent);
            color: white;
            font-weight: bold;
        }

        .pagination .page-link:hover {
            background-color: #f8f9fa;
            color: var(--accent);
        }

        .sticky-column {
            position: sticky;
            left: 0;
            z-index: 10;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
        }

        .btn-xs {
            padding: 1px 5px;
            font-size: 0.7rem;
            line-height: 1.5;
            border-radius: 3px;
        }

        #toggleHolidays, #toggleHolidaysMobile {
            cursor: pointer;
            box-shadow: none !important;
        }
        #toggleHolidays:checked, #toggleHolidaysMobile:checked {
            background-color: #198754 !important;
            border-color: #198754 !important;
        }
        #toggleHolidays:not(:checked), #toggleHolidaysMobile:not(:checked) {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
        }
    </style>

    <!-- Modal Calendar -->
    <div class="modal fade" id="modalCalendar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-fullscreen-lg-down modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h6 class="modal-title fs-6 fs-md-5">
                        <i class="fas fa-calendar-check me-2"></i>
                        <span id="calendarEmployeeName"></span>
                    </h6>
                    <!-- Desktop controls -->
                    <div class="d-none d-md-flex gap-2 align-items-center flex-shrink-0 ms-2">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="toggleHolidays" checked>
                            <label class="form-check-label small fw-bold text-white text-uppercase" for="toggleHolidays" style="font-size: 10px; cursor: pointer; white-space: nowrap;">Libur</label>
                        </div>
                        <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 btn-reset-individual" style="white-space: nowrap;">
                            <i class="fas fa-undo me-1"></i> Reset Jadwal
                        </button>
                        @if (auth()->user()->isAdmin() || auth()->user()->hasRole('super-admin'))
                            <div class="d-none d-flex gap-1" id="autoFillIndividualContainerDesktop">
                                <button type="button" class="btn btn-light btn-sm px-2 btn-auto-fill-individual" data-kategori="non_shift" style="font-size: 0.75rem; white-space: nowrap;">
                                    <i class="fas fa-magic me-1"></i> 6 Hari
                                </button>
                                <button type="button" class="btn btn-outline-info btn-sm px-2 btn-auto-fill-individual-5-hari" data-kategori="non_shift_5_hari" style="font-size: 0.75rem; white-space: nowrap;">
                                    <i class="fas fa-magic me-1"></i> 5 Hari
                                </button>
                            </div>
                        @endif
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Mobile controls toolbar -->
                <div class="bg-primary text-white px-3 pb-3 d-md-none border-0">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="toggleHolidaysMobile" checked>
                            <label class="form-check-label small fw-bold text-white text-uppercase" for="toggleHolidaysMobile" style="font-size: 11px; cursor: pointer;">Libur</label>
                        </div>
                        <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 btn-reset-individual" style="font-size: 0.8rem;">
                            <i class="fas fa-undo me-1"></i> Reset
                        </button>
                        @if (auth()->user()->isAdmin() || auth()->user()->hasRole('super-admin'))
                            <div class="d-none d-flex gap-2" id="autoFillIndividualContainerMobile">
                                <button type="button" class="btn btn-light btn-sm px-3 btn-auto-fill-individual" data-kategori="non_shift" style="font-size: 0.8rem;">
                                    <i class="fas fa-magic me-1"></i> 6 Hari
                                </button>
                                <button type="button" class="btn btn-outline-info btn-sm px-3 btn-auto-fill-individual-5-hari" data-kategori="non_shift_5_hari" style="font-size: 0.8rem;">
                                    <i class="fas fa-magic me-1"></i> 5 Hari
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-body p-0">
                    <div id="calendar"></div>
                </div>
                <div class="modal-footer bg-light border-0 py-2">
                    <small class="text-muted"><i class="fas fa-info-circle me-1"></i> Klik tanggal untuk atur jadwal.</small>
                    <button type="button" class="btn btn-secondary btn-sm px-3 rounded-pill"
                        data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Shift Picker -->
    <div class="modal fade" id="modalShiftPicker" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-fullscreen-sm-down modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-bold">Pilih Jadwal</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-3" id="pickerDateDisplay"></p>
                    <div class="list-group list-group-flush border rounded">
                        @foreach ($shifts as $s)
                            <button type="button"
                                class="list-group-item list-group-item-action py-2 py-md-3 d-flex align-items-center btn-select-shift"
                                data-shift-id="{{ $s->id }}" data-kategori="{{ $s->kategori_jadwal }}">
                                <span class="badge me-3 flex-shrink-0"
                                    style="background-color: {{ $s->warna }}; width: 14px; height: 14px; border-radius: 50%;">&nbsp;</span>
                                <div class="flex-grow-1">
                                    <div class="fw-bold small">{{ $s->nama_shift }}</div>
                                    <small class="text-muted">{{ substr($s->jam_masuk, 0, 5) }}
                                        - {{ substr($s->jam_pulang, 0, 5) }}</small>
                                </div>
                            </button>
                        @endforeach
                        <button type="button"
                            class="list-group-item list-group-item-action py-3 text-danger text-center small fw-bold btn-delete-shift">
                            <i class="fas fa-trash-alt me-1"></i> Hapus Jadwal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .fc .fc-toolbar-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--accent);
        }

        .fc .fc-button-primary {
            background-color: var(--accent);
            border-color: var(--accent);
        }

        .fc .fc-button-primary:hover {
            background-color: var(--accent-mid);
            border-color: var(--accent-mid);
        }

        .fc .fc-daygrid-day-number {
            font-weight: 600;
            padding: 8px;
            color: var(--text-2);
            text-decoration: none;
        }

        .fc .fc-col-header-cell-cushion {
            padding: 10px;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: var(--text-3);
            text-decoration: none;
        }

        .fc-event {
            cursor: pointer;
            transition: transform 0.2s;
            border: none !important;
            padding: 2px 4px;
            border-radius: 4px;
        }

        .fc-event:hover {
            transform: scale(1.02);
        }

        .fc-day-today {
            background-color: rgba(26, 122, 110, 0.05) !important;
        }

        #calendar {
            padding: 20px;
        }

        #calendar .fc .fc-timegrid-slot {
            height: 2rem;
        }

        /* Responsive FullCalendar */
        @media (max-width: 768px) {
            #calendar {
                padding: 12px;
            }

            .fc .fc-toolbar-title {
                font-size: 1rem;
            }

            .fc .fc-button {
                font-size: 0.75rem;
                padding: 0.3rem 0.5rem;
            }

            .fc .fc-daygrid-day-number {
                font-size: 0.75rem;
                padding: 4px;
            }

            .fc .fc-col-header-cell-cushion {
                font-size: 0.6rem;
                padding: 4px 2px;
            }

            .fc .fc-daygrid-day-frame {
                min-height: 40px;
            }

            .fc-event {
                font-size: 0.6rem;
                padding: 1px 2px;
            }
        }

        @media (max-width: 576px) {
            #calendar {
                padding: 8px;
            }

            .fc .fc-toolbar-title {
                font-size: 0.9rem;
            }

            .fc .fc-button {
                font-size: 0.7rem;
                padding: 0.25rem 0.4rem;
            }

            .fc .fc-daygrid-day-number {
                font-size: 0.65rem;
                padding: 2px;
            }

            .fc .fc-col-header-cell-cushion {
                font-size: 0.55rem;
                padding: 2px 1px;
            }

            .fc .fc-daygrid-day-frame {
                min-height: 32px;
            }

            .fc-event {
                font-size: 0.5rem;
                padding: 1px;
            }
        }
    </style>
@endsection

@push('scripts')
    <!-- FullCalendar 6 -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let currentPegawaiId = null;
        let currentPegawaiKategori = null;
        let currentSelectedDate = null;
        let calendar = null;
        let modalCalendar, modalShiftPicker;
        let hasChanged = false;
        let showHolidays = true;

        function showIndividualCalendar(id, nama, kategori) {
            currentPegawaiId = id;
            currentPegawaiKategori = kategori;
            hasChanged = false; // Reset flag for new session
            $('#calendarEmployeeName').text(nama);

            const showAutoFill = (kategori === 'non_shift' || kategori === 'non_shift_5_hari');
            $('#autoFillIndividualContainerDesktop, #autoFillIndividualContainerMobile').toggleClass('d-none', !showAutoFill);
            if (showAutoFill) {
                if (kategori === 'non_shift_5_hari') {
                    $('.btn-auto-fill-individual').addClass('d-none');
                    $('.btn-auto-fill-individual-5-hari').removeClass('d-none');
                } else {
                    $('.btn-auto-fill-individual').removeClass('d-none');
                    $('.btn-auto-fill-individual-5-hari').addClass('d-none');
                }
            }

            modalCalendar.show();

            // Initialize calendar after modal is shown to ensure correct sizing
            setTimeout(() => {
                initCalendar();
            }, 200);
        }

        function filterShifts() {
            console.log('Filtering shifts for category:', currentPegawaiKategori);
            $('.btn-select-shift').each(function() {
                const shiftKategori = $(this).attr('data-kategori') || '';
                const shiftNama = $(this).find('.fw-bold').text().toLowerCase();

                let shouldShow = false;

                // 1. Cuti dan Libur selalu muncul
                if (shiftKategori.includes('cuti') || shiftKategori.includes('libur') ||
                    shiftNama.includes('cuti') || shiftNama.includes('libur')) {
                    shouldShow = true;
                }
                // 2. Jika pegawai kategori 'shift'
                else if (currentPegawaiKategori === 'shift') {
                    // Tampilkan yang kategorinya 'shift', 'pagi', 'siang', 'malam' 
                    // ATAU yang namanya mengandung kata 'shift'
                    if (shiftKategori === 'shift' || shiftKategori === 'pagi' ||
                        shiftKategori === 'siang' || shiftKategori === 'malam' ||
                        shiftNama.includes('shift')) {
                        shouldShow = true;
                    }
                }
                // 3. Jika pegawai kategori 'non_shift' atau 'non_shift_5_hari'
                else if (currentPegawaiKategori === 'non_shift' || currentPegawaiKategori === 'non_shift_5_hari') {
                    // Tampilkan yang kategorinya 'non_shift', 'non_shift_5_hari', atau 'pagi' (biasanya office hours)
                    // Dan pastikan namanya TIDAK mengandung kata 'shift'
                    if ((shiftKategori === 'non_shift' || shiftKategori === 'non_shift_5_hari' || shiftKategori === 'pagi' || shiftKategori === '') &&
                        !shiftNama.includes('shift')) {
                        shouldShow = true;
                    }
                }

                if (shouldShow) {
                    $(this).removeClass('d-none').addClass('d-flex');
                } else {
                    $(this).removeClass('d-flex').addClass('d-none');
                }
            });
        }

        $(document).ready(function() {
            const modalCalendarEl = document.getElementById('modalCalendar');
            modalCalendar = new bootstrap.Modal(modalCalendarEl);
            modalShiftPicker = new bootstrap.Modal(document.getElementById('modalShiftPicker'));

            // Reload page on modal close if changes were made
            modalCalendarEl.addEventListener('hidden.bs.modal', function() {
                if (hasChanged) {
                    location.reload();
                }
            });

            $('.select2').select2({
                theme: 'bootstrap-5'
            });

            function onToggleHoliday() {
                showHolidays = $('#toggleHolidays').is(':checked') || $('#toggleHolidaysMobile').is(':checked');
                $('#toggleHolidays, #toggleHolidaysMobile').prop('checked', showHolidays);
                if (calendar) {
                    calendar.refetchEvents();
                }
            }
            $('#toggleHolidays, #toggleHolidaysMobile').on('change', onToggleHoliday);

            $('.btn-atur-jadwal').on('click', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');
                const kategori = $(this).data('kategori');
                showIndividualCalendar(id, nama, kategori);
            });
        });

        function initCalendar() {
            const calendarEl = document.getElementById('calendar');
            if (calendar) {
                calendar.destroy();
            }

            const eventsUrl  = '{{ url('jadwal/events') }}';
            const holidayUrl = '{{ route('api.holidays') }}';

            const isMobile = window.innerWidth < 768;
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'id',
                headerToolbar: isMobile ? {
                    left:   'title',
                    center: '',
                    right:  'prev,next'
                } : {
                    left:   'prev,next today',
                    center: 'title',
                    right:  'dayGridMonth,dayGridWeek'
                },
                height: 'auto',
                contentHeight: 'auto',
                // Two event sources: employee schedule + holidays
                eventSources: [
                    {
                        // Employee schedule events
                        url: eventsUrl + '/' + currentPegawaiId,
                        method: 'GET',
                    },
                    {
                        // Holiday background events — fetched per visible month
                        id: 'holidays',
                        events: function(fetchInfo, successCallback, failureCallback) {
                            if (!showHolidays) {
                                successCallback([]);
                                return;
                            }
                            const d = new Date(fetchInfo.start);
                            // fetchInfo.start is the first day of the visible range (could be prev month)
                            // Use the middle of the range to get the actual month
                            const mid = new Date((fetchInfo.start.valueOf() + fetchInfo.end.valueOf()) / 2);
                            const year  = mid.getFullYear();
                            const month = mid.getMonth() + 1;

                            $.get(holidayUrl, { year: year, month: month })
                                .done(function(data) { successCallback(data); })
                                .fail(function()     { failureCallback();    });
                        }
                    }
                ],
                editable:   false,
                selectable: true,
                // Style the day cells that are holidays
                dayCellDidMount: function(info) {
                    if (info.date.getDay() === 0) { // Sunday
                        info.el.style.backgroundColor = 'rgba(220,53,69,0.06)';
                        info.el.querySelector('.fc-daygrid-day-number').style.color = '#dc3545';
                    }
                },
                eventDidMount: function(info) {
                    if (info.event.extendedProps.is_holiday) {
                        // Add Bootstrap Tooltip
                        $(info.el).attr('data-bs-toggle', 'tooltip')
                                 .attr('data-bs-placement', 'top')
                                 .attr('title', info.event.extendedProps.label);
                        
                        new bootstrap.Tooltip(info.el);

                        info.el.style.opacity = '1'; // Keep background visible but soft via RGBA
                        
                        // Also colour the day number
                        const dayEl = info.el.closest('.fc-daygrid-day');
                        if (dayEl) {
                            const numEl = dayEl.querySelector('.fc-daygrid-day-number');
                            if (numEl) {
                                numEl.style.color = '#dc3545';
                                $(numEl).attr('title', info.event.extendedProps.label);
                            }
                            
                            // Small label under the date number
                            if (!dayEl.querySelector('.holiday-label')) {
                                const lbl = document.createElement('div');
                                lbl.className   = 'holiday-label';
                                lbl.textContent = info.event.extendedProps.label;
                                lbl.style.cssText = 'font-size:9px;color:#d63384;text-align:center;padding:0 2px;line-height:1.1;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;font-weight:bold;margin-top:-5px;';
                                const frame = dayEl.querySelector('.fc-daygrid-day-frame');
                                if (frame) frame.prepend(lbl);
                            }
                        }
                    }
                },
                dateClick: function(info) {
                    currentSelectedDate = info.dateStr;
                    $('#pickerDateDisplay').text(new Date(info.date).toLocaleDateString('id-ID', {
                        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
                    }));
                    
                    filterShifts();

                    // Logic: Auto-select "Libur" for non_shift / non_shift_5_hari on Sundays or Holidays
                    if (currentPegawaiKategori === 'non_shift' || currentPegawaiKategori === 'non_shift_5_hari') {
                        const date = new Date(info.date);
                        const isSunday = date.getDay() === 0;
                        const isHoliday = calendar.getEvents().some(e => 
                            e.extendedProps.is_holiday && 
                            e.startStr === info.dateStr
                        );

                        if (isSunday || isHoliday) {
                            // Find "Libur" button in modal or just trigger save with ID 3
                            // User requested: default pilihan jadwal otomatis: Libur
                            // I will use Shift ID 3 as the default
                            selectShift(3);
                            return;
                        }
                    }

                    modalShiftPicker.show();
                },
                eventClick: function(info) {
                    // Ignore clicks on holiday background events
                    if (info.event.extendedProps.is_holiday) return;

                    currentSelectedDate = info.event.extendedProps.tanggal_masuk;
                    $('#pickerDateDisplay').text(new Date(currentSelectedDate).toLocaleDateString('id-ID', {
                        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
                    }));
                    filterShifts();
                    modalShiftPicker.show();
                }
            });
            calendar.render();
        }

        $(document).ready(function() {

            function doAutoFillIndividual(kategori, label) {
                const viewDate = calendar.getDate();
                const month = (viewDate.getMonth() + 1).toString().padStart(2, '0');
                const year = viewDate.getFullYear();

                Swal.fire({
                    title: 'Auto Input Jadwal?',
                    text: `Jadwal ${label} untuk bulan ${month}/${year} akan diisi otomatis. Jadwal yang sudah ada akan diperbarui.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Jalankan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('jadwal.auto-fill') }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                pegawai_id: currentPegawaiId,
                                bulan: month,
                                tahun: year,
                                kategori: kategori
                            },
                            success: function(response) {
                                hasChanged = true;
                                calendar.refetchEvents();
                                Swal.fire('Berhasil', response.message, 'success');
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal', xhr.responseJSON?.message ||
                                    'Terjadi kesalahan', 'error');
                            }
                        });
                    }
                });
            }

            $('.btn-auto-fill-individual').on('click', function() {
                doAutoFillIndividual('non_shift', 'Non Shift');
            });

            $('.btn-auto-fill-individual-5-hari').on('click', function() {
                doAutoFillIndividual('non_shift_5_hari', 'Non Shift 5 Hari');
            });

            function doAutoFillRoom(kategori, label) {
                const ruangan_id = $('#ruangan_id').val();
                let month = $('#filter_bulan').val();
                let year = $('#filter_tahun').val();
                
                const isAll = ruangan_id === 'all';
                const locationText = isAll ? 'SELURUH ruangan' : 'ruangan ini';

                Swal.fire({
                    title: 'Auto Input ' + (isAll ? 'Seluruh Ruangan?' : 'Ruangan?'),
                    text: `Semua pegawai ${label} di ${locationText} akan diisi jadwalnya secara otomatis untuk bulan ${month}/${year}.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Jalankan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('jadwal.auto-fill') }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                ruangan_id: ruangan_id,
                                bulan: month,
                                tahun: year,
                                kategori: kategori
                            },
                            success: function(response) {
                                location.reload();
                                Swal.fire('Berhasil', response.message, 'success');
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal', xhr.responseJSON?.message ||
                                    'Terjadi kesalahan', 'error');
                            }
                        });
                    }
                });
            }

            $('.btn-auto-fill-room').on('click', function() {
                doAutoFillRoom('non_shift', 'Non Shift');
            });

            $('.btn-auto-fill-room-5-hari').on('click', function() {
                doAutoFillRoom('non_shift_5_hari', 'Non Shift 5 Hari');
            });

            $('.btn-reset-room').on('click', function() {
                const ruangan_id = $('#ruangan_id').val();
                let month = $('#filter_bulan').val();
                let year = $('#filter_tahun').val();
                
                const isAll = ruangan_id === 'all';
                const locationText = isAll ? 'SELURUH ruangan' : 'ruangan ini';

                Swal.fire({
                    title: 'Reset Jadwal ' + (isAll ? 'Seluruh Ruangan?' : 'Ruangan?'),
                    text: `Seluruh jadwal kerja untuk SEMUA pegawai di ${locationText} pada bulan ${month}/${year} akan dihapus permanen!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus Semua!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('jadwal.reset') }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                ruangan_id: ruangan_id,
                                bulan: month,
                                tahun: year
                            },
                            success: function(response) {
                                location.reload();
                                Swal.fire('Berhasil', response.message, 'success');
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal', xhr.responseJSON?.message ||
                                    'Terjadi kesalahan', 'error');
                            }
                        });
                    }
                });
            });

            $('.btn-reset-individual').on('click', function() {
                const viewDate = calendar.getDate();
                const month = (viewDate.getMonth() + 1).toString().padStart(2, '0');
                const year = viewDate.getFullYear();

                Swal.fire({
                    title: 'Reset Jadwal Pegawai?',
                    text: `Seluruh jadwal kerja pegawai ini pada bulan ${month}/${year} akan dihapus permanen!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('jadwal.reset') }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                pegawai_id: currentPegawaiId,
                                bulan: month,
                                tahun: year
                            },
                            success: function(response) {
                                hasChanged = true;
                                calendar.refetchEvents();
                                Swal.fire('Berhasil', response.message, 'success');
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal', xhr.responseJSON?.message ||
                                    'Terjadi kesalahan', 'error');
                            }
                        });
                    }
                });
            });            $('.btn-select-shift').on('click', function() {
                const shiftId = $(this).data('shift-id');
                selectShift(shiftId);
            });

            function selectShift(shiftId) {
                $.ajax({
                    url: '{{ route('jadwal.save-single') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        pegawai_id: currentPegawaiId,
                        shift_id: shiftId,
                        tanggal: currentSelectedDate
                    },
                    success: function(response) {
                        hasChanged = true;
                        modalShiftPicker.hide();
                        calendar.refetchEvents();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat menyimpan jadwal.'
                        });
                    }
                });
            }
;

            $('.btn-delete-shift').on('click', function() {
                Swal.fire({
                    title: 'Hapus Jadwal?',
                    text: "Jadwal pada tanggal ini akan dihapus.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('jadwal.delete-single') }}',
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}',
                                pegawai_id: currentPegawaiId,
                                tanggal: currentSelectedDate
                            },
                            success: function(response) {
                                hasChanged = true;
                                modalShiftPicker.hide();
                                calendar.refetchEvents();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
