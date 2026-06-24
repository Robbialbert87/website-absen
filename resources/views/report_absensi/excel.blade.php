<!DOCTYPE html>
<html>
<head>
    <title>Report Kegiatan</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .kegiatan-header { background-color: #e8f4f2; font-weight: bold; font-size: 13px; }
        .ruangan-header { background-color: #f8f9fa; font-weight: bold; }
        .success { color: #155724; }
        .warning { color: #856404; }
        .muted { color: #6c757d; }
    </style>
</head>
<body>
    <h2 class="text-center">Laporan Kegiatan</h2>
    <p class="text-center">Periode: {{ request('bulan', date('m')) }}/{{ request('tahun', date('Y')) }}</p>

    @forelse ($data as $group)
        <table>
            <tr class="kegiatan-header">
                <td colspan="4">
                    {{ $group->kegiatan->nama_kegiatan }} —
                    {{ \Carbon\Carbon::parse($group->kegiatan->tanggal_kegiatan)->translatedFormat('d M Y') }}
                    ({{ $group->kegiatan->jam_mulai }} - {{ $group->kegiatan->jam_selesai }})
                    | Lokasi: {{ $group->kegiatan->lokasi }}
                </td>
            </tr>
            @foreach ($group->ruanganData as $ruang)
                <tr class="ruangan-header">
                    <td colspan="4">{{ $ruang->ruangan->nama_ruangan ?? 'Tanpa Ruangan' }}
                        — H:{{ $ruang->hadir }} T:{{ $ruang->terlambat }} X:{{ $ruang->tidak_hadir }}
                    </td>
                </tr>
                <tr>
                    <th style="width: 20%;">NIP</th>
                    <th style="width: 30%;">Nama Pegawai</th>
                    <th style="width: 15%;">Status</th>
                    <th style="width: 35%;">Waktu Absen</th>
                </tr>
                @foreach ($ruang->pegawais as $p)
                    <tr>
                        <td>{{ $p->nip }}</td>
                        <td>{{ $p->nama }}</td>
                        <td>
                            @if ($p->status === 'hadir') Hadir
                            @elseif ($p->status === 'terlambat') Terlambat
                            @else Tidak Hadir
                            @endif
                        </td>
                        <td>
                            @if ($p->waktu_absen)
                                {{ \Carbon\Carbon::parse($p->waktu_absen)->format('d M Y H:i:s') }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </table>
        <br>
    @empty
        <p class="text-center">Tidak ada data absensi untuk periode ini.</p>
    @endforelse

    @if(!isset($isExcel))
    <script>
        window.print();
    </script>
    @endif
</body>
</html>
