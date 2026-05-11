@props(['field', 'label'])

@php
    $currentSort = request('sort_by');
    $currentDir = request('sort_dir', 'desc');
    $isSorted = $currentSort === $field;
    $nextDir = $isSorted && $currentDir === 'asc' ? 'desc' : 'asc';
@endphp

<th class="sortable-column px-4 align-middle" data-field="{{ $field }}" data-dir="{{ $nextDir }}" style="cursor: pointer; white-space: nowrap;" title="Klik untuk mengurutkan">
    <div class="d-flex align-items-center">
        <span>{{ $label }}</span>
        <span class="ms-2 text-muted" style="font-size: 0.8em;">
            @if($isSorted)
                @if($currentDir === 'asc')
                    <i class="fas fa-sort-up text-primary"></i>
                @else
                    <i class="fas fa-sort-down text-primary"></i>
                @endif
            @else
                <i class="fas fa-sort opacity-25"></i>
            @endif
        </span>
    </div>
</th>
