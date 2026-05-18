<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Jadwal Kerja Pegawai {{ Carbon\Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h3 {
            margin: 0;
            padding: 0;
            font-size: 16px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 12px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }
        .table th {
            background-color: #f2f2f2;
        }
        .text-left {
            text-align: left !important;
        }
        .text-center {
            text-align: center !important;
        }
        .fw-bold {
            font-weight: bold;
        }
        .small {
            font-size: 8px;
            color: #555;
        }
        .weekend {
            background-color: #fff5f5;
        }
        .holiday {
            background-color: #ffe5e5;
        }
        .cuti-bersama {
            background-color: #fff9db;
        }
        /* Legend styles */
        .legend-table {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        .legend-table td {
            padding: 2px 5px;
            border: none;
        }
        .color-box {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            vertical-align: middle;
            margin-right: 5px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>

    <div class="header">
        <h3>Jadwal Kerja Pegawai</h3>
        <p>Bulan: {{ Carbon\Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y') }}</p>
        @if($selected_ruangan_id && $selected_ruangan_id !== 'all')
            @php $ruangan = $ruangans->firstWhere('id', $selected_ruangan_id); @endphp
            <p>Ruangan: {{ $ruangan ? $ruangan->nama_ruangan : 'Semua Ruangan' }}</p>
        @else
            <p>Ruangan: Semua Ruangan</p>
        @endif
    </div>

    <table class="table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 150px;">Nama Pegawai</th>
                <th colspan="{{ count($dates) }}">Tanggal</th>
            </tr>
            <tr>
                @foreach ($dates as $date)
                    @php
                        $day = $date->format('j');
                        $holiday = $holidays[$day] ?? null;
                        $isSunday = $date->isWeekend();
                        $class = '';
                        if ($holiday) {
                            $class = $holiday->jenis === 'cuti_bersama' ? 'cuti-bersama' : 'holiday';
                        } elseif ($isSunday) {
                            $class = 'weekend';
                        }
                    @endphp
                    <th class="{{ $class }}">{{ $day }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($pegawais as $p)
                <tr>
                    <td class="text-left">
                        <div class="fw-bold" style="font-size: 11px;">{{ $p->nama }}</div>
                        <div class="small">{{ $p->nip }}</div>
                    </td>
                    @foreach ($dates as $date)
                        @php
                            $day = $date->format('j');
                            $item = $jadwal[$p->id][$day][0] ?? null;
                            $holiday = $holidays[$day] ?? null;
                            $isSunday = $date->isWeekend();
                            
                            $class = '';
                            $bgColor = '';
                            $textColor = '#000';
                            
                            if ($item && $item->shift) {
                                $bgColor = $item->shift->warna;
                                $textColor = '#fff';
                            } elseif ($holiday) {
                                $class = $holiday->jenis === 'cuti_bersama' ? 'cuti-bersama' : 'holiday';
                                $textColor = '#dc3545';
                            } elseif ($isSunday) {
                                $class = 'weekend';
                                $textColor = '#dc3545';
                            }
                        @endphp
                        <td class="{{ $class }}" style="{{ $bgColor ? 'background-color: '.$bgColor.';' : '' }} color: {{ $textColor }};">
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
                    <td colspan="{{ count($dates) + 1 }}" class="text-center">
                        Tidak ada data pegawai di ruangan ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="legend-table">
        <tr>
            <td colspan="4" style="font-weight: bold; font-size: 10px;">Keterangan Warna Shift:</td>
        </tr>
        <tr>
            @php $count = 0; @endphp
            @foreach ($shifts as $s)
                <td>
                    <span class="color-box" style="background-color: {{ $s->warna }};"></span>
                    <strong>{{ $s->kode_shift }}</strong>: {{ $s->nama_shift }} ({{ substr($s->jam_masuk, 0, 5) }} - {{ substr($s->jam_pulang, 0, 5) }})
                </td>
                @php $count++; @endphp
                @if($count % 4 == 0)
                    </tr><tr>
                @endif
            @endforeach
            <td>
                <span class="color-box" style="background-color: #fff;"></span>
                <strong>-</strong>: Libur/Belum diatur
            </td>
        </tr>
    </table>

</body>
</html>
