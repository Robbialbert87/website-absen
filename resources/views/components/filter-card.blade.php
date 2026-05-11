@props(['exportExcel' => null, 'exportPdf' => null, 'createRoute' => null, 'createText' => 'Tambah Data', 'title' => 'Master Data'])

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1 fw-bold" style="font-family: 'Playfair Display', serif; color: var(--text-1);">{{ $title }}</h1>
        <p class="text-muted small mb-0">Kelola dan pantau data {{ strtolower($title) }} Anda di sini.</p>
    </div>
    <div class="d-flex gap-2">
        @if($exportPdf)
        <button type="button" class="btn btn-outline-danger border-0 rounded-pill px-3" onclick="exportData('pdf')" title="Export PDF">
            <i class="fas fa-file-pdf"></i>
        </button>
        @endif
        @if($exportExcel)
        <button type="button" class="btn btn-outline-success border-0 rounded-pill px-3" onclick="exportData('excel')" title="Export Excel">
            <i class="fas fa-file-excel"></i>
        </button>
        @endif
        @if($createRoute)
        <a href="{{ $createRoute }}" class="btn btn-primary rounded-pill px-4 ms-2">
            <i class="fas fa-plus me-2"></i> {{ $createText }}
        </a>
        @endif
    </div>
</div>

<div class="card mb-4 shadow-sm border-0">
    <div class="card-header bg-white py-3 border-bottom-0">
        <form id="filter-form" action="{{ request()->url() }}" method="GET" class="row g-2 align-items-center">
            {{ $slot }}
            <div class="col-auto ms-auto d-flex align-items-center gap-2">
                <label class="text-muted small mb-0">Tampilkan:</label>
                <select name="per_page" class="form-select form-select-sm w-auto" id="per_page_select">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
                <a href="{{ request()->url() }}" class="btn btn-sm btn-light border ms-2" title="Reset Filter">
                    <i class="fas fa-sync-alt"></i>
                </a>
            </div>
            <input type="hidden" name="sort_by" id="sort_by_input" value="{{ request('sort_by') }}">
            <input type="hidden" name="sort_dir" id="sort_dir_input" value="{{ request('sort_dir', 'desc') }}">
            <input type="hidden" name="export" id="export_input" value="">
        </form>
    </div>
</div>
