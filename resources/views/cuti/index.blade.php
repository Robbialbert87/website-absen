@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <x-filter-card title="Data Pegawai Cuti"
        exportExcel="{{ route('cuti.export', request()->query()) }}">
        
        <div class="col-md-2">
            <select name="bulan" class="form-select form-select-sm auto-submit">
                @php
                    $months = [
                        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                    ];
                @endphp
                @foreach($months as $m => $name)
                    <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <select name="tahun" class="form-select form-select-sm auto-submit">
                @for($y = date('Y'); $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>

        <div class="col-md-2">
            <select name="ruangan_id" class="form-select form-select-sm auto-submit">
                <option value="all">Semua Ruangan</option>
                @foreach($ruangans as $r)
                    <option value="{{ $r->id }}" {{ $ruangan_id == $r->id ? 'selected' : '' }}>{{ $r->nama_ruangan }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <select name="shift_id" class="form-select form-select-sm auto-submit">
                <option value="all">Semua Jenis Cuti</option>
                @foreach($cutiShifts as $s)
                    <option value="{{ $s->id }}" {{ $shift_id == $s->id ? 'selected' : '' }}>{{ $s->nama_shift }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari NIP/Nama..." value="{{ $search }}">
        </div>

    </x-filter-card>

    <x-data-table>
        <x-slot name="head">
            <th class="px-4">NIP</th>
            <th>Nama Pegawai</th>
            <th>Ruangan</th>
            <th>Jenis Cuti</th>
            <th>Tanggal Cuti</th>
            <th>Total</th>
            <th>Keterangan</th>
        </x-slot>

        <x-slot name="body">
            @forelse($paginatedItems as $item)
            <tr>
                <td class="px-4"><code>{{ $item['nip'] }}</code></td>
                <td><strong>{{ $item['nama_pegawai'] }}</strong></td>
                <td>{{ $item['ruangan'] }}</td>
                <td>
                    <span class="badge" style="background-color: {{ $item['warna'] ?? '#6c757d' }};">
                        {{ $item['jenis_cuti'] }}
                    </span>
                </td>
                <td>
                    @if($item['total_days'] == 1)
                        {{ \Carbon\Carbon::parse($item['start_date'])->translatedFormat('d F Y') }}
                    @else
                        {{ \Carbon\Carbon::parse($item['start_date'])->translatedFormat('d F Y') }} - 
                        {{ \Carbon\Carbon::parse($item['end_date'])->translatedFormat('d F Y') }}
                    @endif
                </td>
                <td>
                    <span class="badge bg-light text-dark border">
                        {{ $item['total_days'] }} Hari
                    </span>
                </td>
                <td>
                    <small class="text-muted">
                        {{ $item['jenis_cuti'] }} 
                        @if($item['total_days'] > 1)
                            - {{ $item['total_days'] }} Hari
                        @endif
                        <br>
                        {{ $item['keterangan'] }}
                    </small>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-5">
                    <div class="text-muted">
                        <i class="fas fa-calendar-times fa-3x mb-3 opacity-25"></i>
                        <p>Tidak ada data pegawai cuti pada periode ini.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </x-slot>

        <x-slot name="pagination">
            <div class="px-3">
                {{ $paginatedItems->links('pagination::bootstrap-5') }}
            </div>
        </x-slot>
    </x-data-table>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.auto-submit').forEach(select => {
        select.addEventListener('change', () => {
            document.getElementById('filter-form').submit();
        });
    });

    function exportData(type) {
        if (type === 'excel') {
            const form = document.getElementById('filter-form');
            const originalAction = form.action;
            const exportUrl = "{{ route('cuti.export') }}";
            
            // Create a temporary form for export
            const tempForm = form.cloneNode(true);
            tempForm.action = exportUrl;
            tempForm.method = 'GET';
            document.body.appendChild(tempForm);
            tempForm.submit();
            document.body.removeChild(tempForm);
        }
    }
</script>
@endpush
