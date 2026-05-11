@forelse ($data as $item)
    @php
        $isCuti = false;
        $isLibur = false;

        if ($item->shift) {
            if ($item->shift->kategori_jadwal === 'cuti' || stripos($item->shift->nama_shift, 'cuti') !== false || stripos($item->kode_shift, 'cuti') !== false) {
                $isCuti = true;
            } elseif (stripos($item->shift->nama_shift, 'libur') !== false || stripos($item->kode_shift, 'libur') !== false || $item->kode_shift === 'L') {
                $isLibur = true;
            }
        } else {
            if (stripos($item->kode_shift, 'cuti') !== false) {
                $isCuti = true;
            } elseif (stripos($item->kode_shift, 'libur') !== false || $item->kode_shift === 'L') {
                $isLibur = true;
            }
        }

        if ($isCuti || $isLibur) {
            $statusText = $isCuti ? 'Cuti' : 'Libur';
            $jamMasuk = Carbon\Carbon::parse($item->tanggal_masuk)->format('Y-m-d') . ' ' . $statusText;
            $jamPulang = Carbon\Carbon::parse($item->tanggal_masuk)->format('Y-m-d') . ' ' . $statusText;
        } else {
            $jamMasuk = Carbon\Carbon::parse($item->tanggal_masuk . ' ' . $item->jam_masuk)->format('Y-m-d H:i:s');
            $jamPulang = Carbon\Carbon::parse($item->tanggal_pulang . ' ' . $item->jam_pulang)->format('Y-m-d H:i:s');
        }
    @endphp
    <tr>
        <td class="ps-4">{{ $item->pegawai?->nip }}</td>
        <td>{{ $item->pegawai?->nama }}</td>
        <td><code>{{ $jamMasuk }}</code></td>
        <td><code>{{ $jamPulang }}</code></td>
        <td class="pe-4 text-center">
            @if ($isCuti)
                <span class="badge bg-warning text-dark rounded-pill">Cuti</span>
            @elseif ($isLibur)
                <span class="badge bg-danger rounded-pill">Libur</span>
            @else
                <span class="badge bg-success rounded-pill">{{ $item->kode_shift ?? ($item->shift?->kode_shift ?? 'Jadwal') }}</span>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center py-5 text-muted">Tidak ada data ditemukan untuk filter ini.</td>
    </tr>
@endforelse
