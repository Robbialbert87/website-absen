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
                    class="{{ auth()->user()->isAdmin() || auth()->user()->hasRole('super-admin') ? 'col-md-3' : 'col-md-6' }}">
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
                <div class="col-md-2 text-end">
                    <div class="d-flex gap-1 justify-content-end">
                        <button type="button" class="btn btn-outline-danger rounded-pill px-3 btn-reset-room"
                            style="font-size: 0.75rem;" title="Reset Jadwal">
                            <i class="fas fa-undo"></i>
                        </button>
                        @if (auth()->user()->isAdmin() || auth()->user()->hasRole('super-admin'))
                            <button type="button" class="btn btn-outline-primary rounded-pill px-3 btn-auto-fill-room"
                                style="font-size: 0.75rem;" title="Auto Input">
                                <i class="fas fa-magic"></i>
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
                            <th style="min-width: 45px; background-color: {{ $date->isWeekend() ? '#fff5f5' : '' }};">
                                <span class="d-block small fw-bold">{{ $date->format('j') }}</span>
                                <span class="d-block {{ $date->isWeekend() ? 'text-danger' : 'text-muted' }}"
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
                                            ({{ ucfirst($p->kategori_kerja) }})
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
                                    $isWeekend = $weekendMap[$day] ?? false;
                                    $bgColor = $item && $item->shift ? $item->shift->warna : '';
                                    $textColor = $bgColor ? '#fff' : ($isWeekend ? '#dc3545' : '#6c757d');
                                @endphp
                                <td class="text-center p-0"
                                    style="height: 45px; background-color: {{ $bgColor ?: ($isWeekend ? '#fff5f5' : '') }};">
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
    </style>

    <!-- Modal Calendar -->
    <div class="modal fade" id="modalCalendar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h5 class="modal-title">
                        <i class="fas fa-calendar-check me-2"></i>
                        Jadwal Kerja: <span id="calendarEmployeeName"></span>
                    </h5>
                    <div class="ms-auto me-3 d-flex gap-2">
                        <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 btn-reset-individual">
                            <i class="fas fa-undo me-1"></i> Reset Jadwal
                        </button>
                        @if (auth()->user()->isAdmin() || auth()->user()->hasRole('super-admin'))
                            <div class="d-none" id="autoFillIndividualContainer">
                                <button type="button"
                                    class="btn btn-light btn-sm rounded-pill px-3 btn-auto-fill-individual">
                                    <i class="fas fa-magic me-1"></i> Auto Input Non-Shift
                                </button>
                            </div>
                        @endif
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div id="calendar" style="min-height: 600px;"></div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <div class="me-auto">
                        <small class="text-muted"><i class="fas fa-info-circle me-1"></i> Klik tanggal untuk menambah/ubah
                            jadwal. Klik event untuk menghapus.</small>
                    </div>
                    <button type="button" class="btn btn-secondary px-4 rounded-pill"
                        data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Shift Picker -->
    <div class="modal fade" id="modalShiftPicker" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
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
                                class="list-group-item list-group-item-action py-2 d-flex align-items-center btn-select-shift"
                                data-shift-id="{{ $s->id }}" data-kategori="{{ $s->kategori_jadwal }}">
                                <span class="badge me-3"
                                    style="background-color: {{ $s->warna }}; width: 12px; height: 12px; border-radius: 50%;">&nbsp;</span>
                                <div class="flex-grow-1">
                                    <div class="fw-bold small">{{ $s->nama_shift }}</div>
                                    <small class="text-muted" style="font-size: 10px;">{{ substr($s->jam_masuk, 0, 5) }}
                                        - {{ substr($s->jam_pulang, 0, 5) }}</small>
                                </div>
                            </button>
                        @endforeach
                        <button type="button"
                            class="list-group-item list-group-item-action py-2 text-danger text-center small fw-bold btn-delete-shift">
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

        function showIndividualCalendar(id, nama, kategori) {
            currentPegawaiId = id;
            currentPegawaiKategori = kategori;
            hasChanged = false; // Reset flag for new session
            $('#calendarEmployeeName').text(nama);

            if (kategori === 'non_shift') {
                $('#autoFillIndividualContainer').removeClass('d-none');
            } else {
                $('#autoFillIndividualContainer').addClass('d-none');
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
                // 3. Jika pegawai kategori 'non_shift'
                else if (currentPegawaiKategori === 'non_shift') {
                    // Tampilkan yang kategorinya 'non_shift' atau 'pagi' (biasanya office hours)
                    // Dan pastikan namanya TIDAK mengandung kata 'shift'
                    if ((shiftKategori === 'non_shift' || shiftKategori === 'pagi' || shiftKategori === '') &&
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

            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'id',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,dayGridWeek'
                },
                events: `/jadwal/events/${currentPegawaiId}`,
                editable: false,
                selectable: true,
                dateClick: function(info) {
                    currentSelectedDate = info.dateStr;
                    $('#pickerDateDisplay').text(new Date(info.date).toLocaleDateString('id-ID', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    }));
                    filterShifts();
                    modalShiftPicker.show();
                },
                eventClick: function(info) {
                    currentSelectedDate = info.event.extendedProps.tanggal_masuk;
                    $('#pickerDateDisplay').text(new Date(currentSelectedDate).toLocaleDateString('id-ID', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    }));
                    filterShifts();
                    modalShiftPicker.show();
                }
            });
            calendar.render();
        }

        $(document).ready(function() {

            $('.btn-auto-fill-individual').on('click', function() {
                const viewDate = calendar.getDate();
                const month = (viewDate.getMonth() + 1).toString().padStart(2, '0');
                const year = viewDate.getFullYear();

                Swal.fire({
                    title: 'Auto Input Jadwal?',
                    text: `Jadwal non-shift untuk bulan ${month}/${year} akan diisi otomatis. Jadwal yang sudah ada akan diperbarui.`,
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
            });

            $('.btn-auto-fill-room').on('click', function() {
                const ruangan_id = $('#ruangan_id').val();
                let month = $('#filter_bulan').val();
                let year = $('#filter_tahun').val();
                
                const isAll = ruangan_id === 'all';
                const locationText = isAll ? 'SELURUH ruangan' : 'ruangan ini';

                Swal.fire({
                    title: 'Auto Input ' + (isAll ? 'Seluruh Ruangan?' : 'Ruangan?'),
                    text: `Semua pegawai non-shift di ${locationText} akan diisi jadwalnya secara otomatis untuk bulan ${month}/${year}.`,
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
            });

            $('.btn-select-shift').on('click', function() {
                const shiftId = $(this).data('shift-id');

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
            });

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
