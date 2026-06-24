@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="fw-bold mb-0" style="font-family: 'Playfair Display', serif; color: #0D1E1C;">Report Kegiatan</h2>
                        <p class="text-muted mb-0">Laporan absensi kegiatan per lokasi dan ruangan.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <form id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label fw-600">Bulan</label>
                            <select name="bulan" id="bulan" class="form-select select2">
                                @foreach (range(1, 12) as $m)
                                    <option value="{{ sprintf('%02d', $m) }}"
                                        {{ date('m') == sprintf('%02d', $m) ? 'selected' : '' }}>
                                        {{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-600">Tahun</label>
                            <select name="tahun" id="tahun" class="form-select select2">
                                @foreach (range(date('Y') - 5, date('Y') + 5) as $y)
                                    <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-600">Kegiatan</label>
                            <select name="kegiatan_id" id="kegiatan_id" class="form-select select2">
                                <option value="">Semua Kegiatan</option>
                                @foreach ($kegiatans as $k)
                                    <option value="{{ $k->id }}">
                                        {{ $k->nama_kegiatan }} ({{ \Carbon\Carbon::parse($k->tanggal_kegiatan)->translatedFormat('d M Y') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-600">Ruangan</label>
                            <select name="ruangan_id" id="ruangan_id" class="form-select select2">
                                <option value="">Semua Ruangan</option>
                                @foreach ($ruangans as $r)
                                    <option value="{{ $r->id }}">{{ $r->nama_ruangan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 d-flex flex-wrap gap-2 mt-4">
                            <button type="button" id="btnPreview" class="btn btn-primary">
                                <i class="fas fa-eye me-2"></i> Preview Data
                            </button>
                            <button type="button" id="btnExportExcel" class="btn btn-success rounded-pill px-4">
                                <i class="fas fa-file-excel me-2"></i> Export Excel
                            </button>
                            <button type="button" id="btnExportPdf" class="btn btn-danger rounded-pill px-4">
                                <i class="fas fa-file-pdf me-2"></i> Export PDF
                            </button>
                            <button type="button" id="btnReset" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="fas fa-undo me-2"></i> Reset
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div id="previewContainer" style="display: none;">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 fw-bold">Preview Data</h5>
                        <span class="badge bg-primary rounded-pill px-3 py-2" id="totalDataCount">0 Data</span>
                    </div>
                </div>
                <div class="card-body p-4" id="previewBody">
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

            function serializeForm() {
                return $('#filterForm').serialize();
            }

            function loadPreview() {
                $('#previewContainer').show();
                $('#previewBody').html(
                    '<div class="text-center py-5"><i class="fas fa-spinner fa-spin me-2"></i> Memuat data...</div>'
                );

                $.get("{{ route('report.absensi.preview') }}", serializeForm(), function(html) {
                    $('#previewBody').html(html);
                }).fail(function() {
                    $('#previewBody').html(
                        '<div class="text-center py-5 text-danger">Gagal memuat data.</div>');
                });
            }

            $('#btnPreview').on('click', loadPreview);

            $('#btnExportExcel').on('click', function() {
                const params = serializeForm();
                window.location.href = "{{ route('report.absensi.export', ['format' => 'excel']) }}?" + params;
            });

            $('#btnExportPdf').on('click', function() {
                const params = serializeForm();
                window.location.href = "{{ route('report.absensi.export', ['format' => 'pdf']) }}?" + params;
            });

            $('#btnReset').on('click', function() {
                const now = new Date();
                const bulan = String(now.getMonth() + 1).padStart(2, '0');
                const tahun = now.getFullYear();
                $('#bulan').val(bulan).trigger('change');
                $('#tahun').val(tahun).trigger('change');
                $('#kegiatan_id').val('').trigger('change');
                $('#ruangan_id').val('').trigger('change');
                $('#previewContainer').hide();
            });
        });
    </script>
@endpush
