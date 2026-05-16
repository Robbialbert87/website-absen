@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="fw-bold mb-0">Report Jadwal {{ ucfirst($type) }}</h2>
                        <p class="text-muted mb-0">Export data jadwal pegawai ke format Excel.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <form id="filterForm" action="{{ route('report.export') }}" method="GET">
                    @if($type === 'shift' || $type === 'non_shift')
                        <input type="hidden" name="kategori_kerja" value="{{ $type }}">
                    @elseif($type !== 'all')
                        <input type="hidden" name="kategori_jadwal" value="{{ $type }}">
                    @endif
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label fw-600">Bulan</label>
                            <select name="bulan" id="bulan" class="form-select select2">
                                @foreach (range(1, 12) as $m)
                                    <option value="{{ sprintf('%02d', $m) }}"
                                        {{ $filters['bulan'] == sprintf('%02d', $m) ? 'selected' : '' }}>
                                        {{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-600">Tahun</label>
                            <select name="tahun" id="tahun" class="form-select select2">
                                @foreach (range(date('Y') - 5, date('Y') + 5) as $y)
                                    <option value="{{ $y }}" {{ $filters['tahun'] == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-600">Ruangan</label>
                            <select name="ruangan_id" id="ruangan_id" class="form-select select2">
                                <option value="">Semua Ruangan</option>
                                @foreach ($ruangans as $r)
                                    <option value="{{ $r->id }}"
                                        {{ $filters['ruangan_id'] == $r->id ? 'selected' : '' }}>
                                        {{ $r->nama_ruangan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if($type === 'all')
                        <div class="col-md-2">
                            <label class="form-label fw-600">Kategori Kerja</label>
                            <select name="kategori_kerja" id="kategori_kerja" class="form-select select2">
                                <option value="">Semua</option>
                                @foreach ($kategori_kerjas as $k)
                                    <option value="{{ $k }}"
                                        {{ $filters['kategori_kerja'] == $k ? 'selected' : '' }}>
                                        {{ $k }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col-md-3">
                            <label class="form-label fw-600">Pegawai (Optional)</label>
                            <select name="pegawai_id" id="pegawai_id" class="form-select select2">
                                <option value="">Semua Pegawai</option>
                                @foreach ($pegawais as $p)
                                    <option value="{{ $p->id }}"
                                        {{ $filters['pegawai_id'] == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama }} ({{ $p->nip }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 d-flex gap-2 mt-4">
                            <button type="button" id="btnPreview" class="btn btn-primary">
                                <i class="fas fa-eye me-2"></i> Preview Data
                            </button>
                            <button type="submit" class="btn btn-success rounded-pill px-4">
                                <i class="fas fa-file-csv me-2"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Preview Section -->
        <div id="previewContainer" style="display: none;">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 fw-bold">Preview Data</h5>
                        <span class="badge bg-primary rounded-pill px-3 py-2" id="totalDataCount">0 Data</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">NIP</th>
                                    <th>Nama Pegawai</th>
                                    <th>jam_masuk</th>
                                    <th>jam_pulang</th>
                                    <th class="pe-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody id="previewBody">
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });

            $('#ruangan_id').on('change', function() {
                const ruanganId = $(this).val();
                const $pegawaiSelect = $('#pegawai_id');
                
                $pegawaiSelect.empty().append('<option value="">Semua Pegawai</option>');
                
                if (ruanganId) {
                    $.get("{{ route('report.pegawai') }}", { ruangan_id: ruanganId }, function(data) {
                        data.forEach(function(p) {
                            $pegawaiSelect.append(`<option value="${p.id}">${p.nama} (${p.nip})</option>`);
                        });
                        $pegawaiSelect.trigger('change');
                    });
                }
            });

            $('#btnPreview').on('click', function() {
                const formData = $('#filterForm').serialize();
                
                $('#previewContainer').show();
                $('#previewBody').html('<tr><td colspan="5" class="text-center py-5"><i class="fas fa-spinner fa-spin me-2"></i> Memuat data...</td></tr>');

                $.get("{{ route('report.preview') }}", formData, function(html) {
                    $('#previewBody').html(html);
                    const count = $('#previewBody tr').not(':has(td[colspan])').length;
                    $('#totalDataCount').text(count + ' Data');
                }).fail(function() {
                    $('#previewBody').html('<tr><td colspan="5" class="text-center py-5 text-danger">Gagal memuat data.</td></tr>');
                });
            });
        });
    </script>
@endpush
