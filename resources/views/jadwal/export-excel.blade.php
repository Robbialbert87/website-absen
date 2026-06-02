<table>
    <thead>
        <tr>
            <th colspan="{{ count($dates) + 1 }}" style="text-align: center; font-size: 14px; font-weight: bold;">
                Jadwal Kerja Pegawai - Bulan: {{ Carbon\Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y') }}
                @if($selected_ruangan_id && $selected_ruangan_id !== 'all')
                    @php $ruangan = $ruangans->firstWhere('id', $selected_ruangan_id); @endphp
                    (Ruangan: {{ $ruangan ? $ruangan->nama_ruangan : '' }})
                @else
                    (Semua Ruangan)
                @endif
            </th>
        </tr>
        <tr>
            <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle; width: 30px;">Nama Pegawai</th>
            <th colspan="{{ count($dates) }}" style="font-weight: bold; text-align: center;">Tanggal</th>
        </tr>
        <tr>
            @foreach ($dates as $date)
                @php
                    $day = $date->format('j');
                    $holiday = $holidays[$day] ?? null;
                    $isSunday = $date->isWeekend();
                    $bgColor = '';
                    $textColor = '#000000';
                    if ($holiday) {
                        $bgColor = $holiday->jenis === 'cuti_bersama' ? '#fff9db' : '#ffe5e5';
                        $textColor = '#ff0000';
                    } elseif ($isSunday) {
                        $bgColor = '#fff5f5';
                        $textColor = '#ff0000';
                    }
                @endphp
                <th style="font-weight: bold; text-align: center; {{ $bgColor ? 'background-color: '.$bgColor.';' : '' }} color: {{ $textColor }};">
                    {{ $day }}
                </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @forelse($pegawais as $p)
            <tr>
                <td style="vertical-align: middle;">
                    <strong>{{ $p->nama }}</strong>
                    <br>
                    <small>{{ $p->nip }} ({{ $p->kategori_kerja == 'non_shift_5_hari' ? 'Non Shift 5 Hari' : ucfirst($p->kategori_kerja) }})</small>
                </td>
                @foreach ($dates as $date)
                    @php
                        $day = $date->format('j');
                        $item = $jadwal[$p->id][$day][0] ?? null;
                        $holiday = $holidays[$day] ?? null;
                        $isSunday = $date->isWeekend();
                        
                        $bgColor = '';
                        $textColor = '#000000';
                        
                        if ($item && $item->shift) {
                            $bgColor = $item->shift->warna;
                            $textColor = '#ffffff';
                        } elseif ($holiday) {
                            $bgColor = $holiday->jenis === 'cuti_bersama' ? '#fff9db' : '#ffe5e5';
                            $textColor = '#ff0000';
                        } elseif ($isSunday) {
                            $bgColor = '#fff5f5';
                            $textColor = '#ff0000';
                        }
                    @endphp
                    <td style="text-align: center; vertical-align: middle; {{ $bgColor ? 'background-color: '.$bgColor.';' : '' }} color: {{ $textColor }};">
                        @if ($item)
                            {{ $item->shift->kode_shift ?? '-' }}
                        @else
                            -
                        @endif
                    </td>
                @endforeach
            </tr>
        @empty
            <tr>
                <td colspan="{{ count($dates) + 1 }}" style="text-align: center;">
                    Tidak ada data pegawai.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<br>

<table>
    <tr>
        <td colspan="4" style="font-weight: bold;">Keterangan Warna Shift:</td>
    </tr>
    @foreach ($shifts as $s)
        <tr>
            <td style="background-color: {{ $s->warna }}; color: #ffffff; text-align: center;">{{ $s->kode_shift }}</td>
            <td colspan="3">{{ $s->nama_shift }} ({{ substr($s->jam_masuk, 0, 5) }} - {{ substr($s->jam_pulang, 0, 5) }})</td>
        </tr>
    @endforeach
    <tr>
        <td style="background-color: #ffffff; text-align: center;">-</td>
        <td colspan="3">Libur/Belum diatur</td>
    </tr>
</table>
