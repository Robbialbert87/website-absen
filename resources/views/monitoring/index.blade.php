@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="flex-column flex-md-row d-flex align-items-start align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <h1 class="h3 h2-sm mb-1 fw-bold" style="font-family: 'Playfair Display', serif; color: #0D1E1C;">Monitoring Jadwal Pegawai</h1>
            <p class="text-muted mb-0">Pantau kelengkapan pengisian jadwal kerja bulanan setiap ruangan.</p>
        </div>
        <div class="w-100 w-md-auto">
            <form action="{{ route('monitoring.index') }}" method="GET" class="row g-2">
                <div class="col-6 col-md-3">
                    <select name="bulan" class="form-select border-0 shadow-sm" style="border-radius: 10px;">
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $stats['monitoring']['selected_month'] == $i ? 'selected' : '' }}>
                                {{ Carbon\Carbon::create(null, $i, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="tahun" class="form-select border-0 shadow-sm" style="border-radius: 10px;">
                        @for ($i = date('Y') - 2; $i <= date('Y') + 1; $i++)
                            <option value="{{ $i }}" {{ $stats['monitoring']['selected_year'] == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-8 col-md-4">
                    <select name="ruangan_id" class="form-select border-0 shadow-sm" style="border-radius: 10px;">
                        <option value="">Semua Ruangan</option>
                        @foreach($listRuangan as $r)
                            <option value="{{ $r->id }}" {{ request('ruangan_id') == $r->id ? 'selected' : '' }}>
                                {{ $r->nama_ruangan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-4 col-md-2">
                    <button type="submit" class="btn btn-primary px-3 px-md-4 shadow-sm w-100" style="border-radius: 10px;">
                        <i class="fas fa-filter me-1 me-md-2"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Alert Warning -->
    @if($stats['monitoring']['summary']['ruangan_belum_lengkap'] > 0)
    <div class="alert alert-warning border-0 shadow-sm mb-4" style="border-radius: 16px; background-color: #fff9db; border-left: 5px solid #fcc419 !important;">
        <div class="d-flex align-items-center">
            <div class="p-2 rounded-circle me-3" style="background-color: rgba(252, 196, 25, 0.2);">
                <i class="fas fa-exclamation-triangle" style="color: #f08c00;"></i>
            </div>
            <div>
                <h6 class="mb-0 fw-bold" style="color: #862e00;">Perhatian!</h6>
                <p class="mb-0 text-muted small">Terdapat <strong>{{ $stats['monitoring']['summary']['ruangan_belum_lengkap'] }} ruangan</strong> yang belum menyelesaikan jadwal kerja. Segera follow up kepala ruangan terkait.</p>
            </div>
        </div>
    </div>
    @endif

    <div class="row g-3">
        <!-- Ruangan Lengkap Card -->
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px; overflow: hidden;">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="p-3 rounded-3" style="background-color: rgba(40, 167, 69, 0.1);">
                            <i class="fas fa-check-double fs-4" style="color: #28a745;"></i>
                        </div>
                        <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2 border-0">Lengkap</span>
                    </div>
                    <h6 class="text-muted mb-1 text-uppercase fw-bold small">Ruangan Sudah Lengkap</h6>
                    <h2 class="mb-0 fw-bold">{{ $stats['monitoring']['summary']['ruangan_lengkap'] }} <span class="fs-6 text-muted fw-normal">Ruangan</span></h2>
                </div>
            </div>
        </div>

        <!-- Ruangan Belum Lengkap Card -->
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px; overflow: hidden;">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="p-3 rounded-3" style="background-color: rgba(255, 193, 7, 0.1);">
                            <i class="fas fa-clock fs-4" style="color: #ffc107;"></i>
                        </div>
                        <span class="badge rounded-pill bg-warning-subtle text-warning px-3 py-2 border-0">Belum Lengkap</span>
                    </div>
                    <h6 class="text-muted mb-1 text-uppercase fw-bold small">Ruangan Belum Lengkap</h6>
                    <h2 class="mb-0 fw-bold text-warning">{{ $stats['monitoring']['summary']['ruangan_belum_lengkap'] }} <span class="fs-6 text-muted fw-normal">Ruangan</span></h2>
                </div>
            </div>
        </div>

        <!-- Total Pegawai Belum Dijadwalkan Card -->
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px; overflow: hidden;">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="p-3 rounded-3" style="background-color: rgba(220, 53, 69, 0.1);">
                            <i class="fas fa-user-times fs-4" style="color: #dc3545;"></i>
                        </div>
                        <span class="badge rounded-pill bg-danger-subtle text-danger px-3 py-2 border-0">Action Required</span>
                    </div>
                    <h6 class="text-muted mb-1 text-uppercase fw-bold small">Total Pegawai Belum Lengkap</h6>
                    <h2 class="mb-0 fw-bold text-danger">{{ $stats['monitoring']['summary']['pegawai_belum_lengkap'] }} <span class="fs-6 text-muted fw-normal">Orang</span></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mt-2">
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 py-4 px-4">
                    <h5 class="m-0 fw-bold" style="color: #1A7A6E;">Kelengkapan Jadwal per Ruangan</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div style="height: 300px;">
                        <canvas id="completenessChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 py-4 px-4">
                    <h5 class="m-0 fw-bold" style="color: #1A7A6E;">Ranking Kelengkapan</h5>
                </div>
                <div class="card-body px-4">
                    <div class="mb-4">
                        <p class="text-muted small fw-bold mb-3 text-uppercase">Paling Lengkap (Top 5)</p>
                        @foreach($stats['monitoring']['top'] as $index => $item)
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge rounded-circle me-3 {{ $index == 0 ? 'bg-success' : 'bg-light text-dark' }}" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">{{ $index + 1 }}</span>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="fw-bold">{{ $item['nama_ruangan'] }}</small>
                                    <small class="text-success fw-bold">{{ $item['persentase'] }}%</small>
                                </div>
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar bg-success" style="width: {{ $item['persentase'] }}%"></div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <hr class="my-4" style="border-style: dashed; border-top-width: 2px;">

                    <div>
                        <p class="text-muted small fw-bold mb-3 text-uppercase">Paling Sedikit Input (Bottom 5)</p>
                        @foreach($stats['monitoring']['bottom'] as $index => $item)
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge rounded-circle me-3 bg-light text-dark" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">{{ $index + 1 }}</span>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="fw-bold">{{ $item['nama_ruangan'] }}</small>
                                    <small class="text-danger fw-bold">{{ $item['persentase'] }}%</small>
                                </div>
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar bg-danger" style="width: {{ $item['persentase'] }}%"></div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Monitoring -->
    <div class="row mt-2">
        <div class="col-lg-12 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="m-0 fw-bold" style="color: #1A7A6E;">Detail Monitoring Ruangan</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 border-0 py-3" style="font-size: 0.85rem; text-transform: uppercase; color: #6c757d;">Nama Ruangan</th>
                                    <th class="border-0 py-3" style="font-size: 0.85rem; text-transform: uppercase; color: #6c757d;">Total Pegawai</th>
                                    <th class="border-0 py-3 text-center" style="font-size: 0.85rem; text-transform: uppercase; color: #6c757d;">Sudah Lengkap</th>
                                    <th class="border-0 py-3 text-center" style="font-size: 0.85rem; text-transform: uppercase; color: #6c757d;">Belum Lengkap</th>
                                    <th class="border-0 py-3" style="font-size: 0.85rem; text-transform: uppercase; color: #6c757d;">Progres Jadwal</th>
                                    <th class="border-0 py-3 text-center" style="font-size: 0.85rem; text-transform: uppercase; color: #6c757d;">Status</th>
                                    <th class="border-0 py-3 text-end pe-4" style="font-size: 0.85rem; text-transform: uppercase; color: #6c757d;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['monitoring']['data'] as $item)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold" style="color: #0D1E1C;">{{ $item['nama_ruangan'] }}</div>
                                    </td>
                                    <td>{{ $item['total_pegawai'] }} Pegawai</td>
                                    <td class="text-center">
                                        <span class="badge bg-success-subtle text-success border-0 px-2 py-1">{{ $item['pegawai_lengkap'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger-subtle text-danger border-0 px-2 py-1">{{ $item['pegawai_belum_lengkap'] }}</span>
                                    </td>
                                    <td style="width: 200px;">
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1" style="height: 8px; border-radius: 10px; background-color: #f0f0f0;">
                                                @php
                                                    $barColor = $item['persentase'] == 100 ? '#28a745' : ($item['persentase'] > 80 ? '#fcc419' : '#dc3545');
                                                @endphp
                                                <div class="progress-bar" style="width: {{ $item['persentase'] }}%; background-color: {{ $barColor }}; border-radius: 10px;"></div>
                                            </div>
                                            <span class="ms-3 fw-bold small">{{ $item['persentase'] }}%</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($item['is_lengkap'])
                                            <span class="badge bg-success px-3 py-2" style="border-radius: 8px;">Jadwal Lengkap</span>
                                        @elseif($item['persentase'] > 80)
                                            <span class="badge bg-warning text-dark px-3 py-2" style="border-radius: 8px;">Hampir Lengkap</span>
                                        @else
                                            <span class="badge bg-danger px-3 py-2" style="border-radius: 8px;">Belum Lengkap</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-outline-primary btn-sm px-3 rounded-pill btn-detail" 
                                                data-id="{{ $item['id'] }}"
                                                data-bulan="{{ $stats['monitoring']['selected_month'] }}"
                                                data-tahun="{{ $stats['monitoring']['selected_year'] }}">
                                            Detail <i class="fas fa-chevron-right ms-1" style="font-size: 0.7rem;"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 p-4">
                <h5 class="modal-title fw-bold" style="color: #1A7A6E;">Detail Pegawai Belum Lengkap - <span id="modalRoomName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="tableDetail">
                        <thead>
                            <tr>
                                <th style="width: 250px;">Nama Pegawai</th>
                                <th class="text-center" style="width: 100px;">Input</th>
                                <th class="text-center" style="width: 100px;">Kurang</th>
                                <th>Status Kalender Bulanan</th>
                            </tr>
                        </thead>
                        <tbody id="detailContent">
                            <!-- Filled by AJAX -->
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 d-flex gap-3 justify-content-center">
                    <div class="d-flex align-items-center gap-1">
                        <span class="badge bg-success" style="width: 12px; height: 12px; padding: 0;">&nbsp;</span>
                        <small class="text-muted">Jadwal Terisi</small>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <span class="badge bg-danger" style="width: 12px; height: 12px; padding: 0;">&nbsp;</span>
                        <small class="text-muted">Belum Terisi</small>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <span class="badge bg-primary" style="width: 12px; height: 12px; padding: 0;">&nbsp;</span>
                        <small class="text-muted">Libur (Minggu / Nasional)</small>
                    </div>
                </div>
                <div id="noDataDetail" class="text-center py-5 d-none">
                    <img src="https://illustrations.popsy.co/teal/work-from-home.svg" style="width: 150px;" class="mb-3">
                    <p class="text-muted">Semua pegawai di ruangan ini sudah memiliki jadwal lengkap.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // Chart Logic
        const ctx = document.getElementById('completenessChart').getContext('2d');
        const data = @json($stats['monitoring']['data']);
        
        const labels = data.map(item => item.nama_ruangan);
        const percentages = data.map(item => item.persentase);
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Persentase Kelengkapan',
                    data: percentages,
                    backgroundColor: percentages.map(p => p == 100 ? '#1A7A6E' : (p > 80 ? '#ffc107' : '#dc3545')),
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { 
                        max: 100,
                        grid: { display: false }
                    },
                    y: { 
                        grid: { display: false }
                    }
                }
            }
        });

        // Detail Modal AJAX
        $('.btn-detail').on('click', function() {
            const id = $(this).data('id');
            const bulan = $(this).data('bulan');
            const tahun = $(this).data('tahun');
            
            $('#detailContent').html('<tr><td colspan="4" class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></td></tr>');
            $('#modalDetail').modal('show');

            $.ajax({
                url: "{{ route('dashboard.monitoring-detail') }}",
                type: "GET",
                data: { ruangan_id: id, bulan: bulan, tahun: tahun },
                success: function(response) {
                    $('#modalRoomName').text(response.ruangan);
                    let html = '';
                    
                    if (response.details.length > 0) {
                        $('#tableDetail').removeClass('d-none');
                        $('#noDataDetail').addClass('d-none');
                        
                        response.details.forEach(item => {
                            let calendarHtml = '';
                            item.day_status.forEach(ds => {
                                let badgeClass, title;
                                if (ds.is_off) {
                                    badgeClass = 'bg-primary';
                                    title = ds.label || 'Libur';
                                } else if (ds.is_filled) {
                                    badgeClass = 'bg-success';
                                    title = 'Terisi';
                                } else {
                                    badgeClass = 'bg-danger';
                                    title = 'Kosong';
                                }
                                calendarHtml += `<span class="badge ${badgeClass} mb-1 me-1" title="${title}" style="width:28px;height:28px;display:inline-flex;align-items:center;justify-content:center;font-size:0.75rem;cursor:default;">${ds.day}</span>`;
                            });

                            const autoOffBadge = item.auto_off > 0
                                ? `<span class="badge bg-primary ms-1">${item.auto_off} Libur</span>`
                                : '';

                            html += `
                                <tr>
                                    <td class="fw-bold text-dark">${item.nama_pegawai}</td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark px-3 py-2 border">${item.total_input} Hari</span>
                                        ${autoOffBadge}
                                    </td>
                                    <td class="text-center"><span class="badge bg-danger px-3 py-2">${item.missing_count} Hari</span></td>
                                    <td>
                                        <div class="d-flex flex-wrap">
                                            ${calendarHtml}
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });
                        $('#detailContent').html(html);
                    } else {
                        $('#tableDetail').addClass('d-none');
                        $('#noDataDetail').removeClass('d-none');
                        $('#detailContent').html('');
                    }
                },
                error: function() {
                    $('#detailContent').html('<tr><td colspan="4" class="text-center text-danger py-4">Gagal mengambil data.</td></tr>');
                }
            });
        });
    });
</script>
@endpush
@endsection
