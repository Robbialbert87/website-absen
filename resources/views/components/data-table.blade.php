<div class="card shadow-sm border-0 position-relative" id="table-wrapper">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="min-width: 800px;">
                <thead class="text-uppercase" style="background: var(--bg-alt); border-bottom: 2px solid var(--border);">
                    <tr class="small fw-bold" style="color: var(--text-2);">
                        {{ $head }}
                    </tr>
                </thead>
                <tbody>
                    {{ $body }}
                </tbody>
            </table>
        </div>
    </div>
    @if(isset($pagination))
    <div class="card-footer bg-white border-top-0 py-3">
        {{ $pagination }}
    </div>
    @endif
    
    <!-- Loading Overlay -->
    <div id="table-loader" class="position-absolute top-0 start-0 w-100 h-100 bg-white d-none justify-content-center align-items-center rounded" style="opacity: 0.7; z-index: 10;">
        <div class="spinner-border" style="color: var(--accent);" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>
