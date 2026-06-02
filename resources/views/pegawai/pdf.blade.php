<!DOCTYPE html>
<html>
<head>
    <title>Data Pegawai</title>
    @if(!isset($isExcel))
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
    </style>
    @endif
</head>
<body>
    <h2 class="text-center">Data Master Pegawai</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIP</th>
                <th>Nama Pegawai</th>
                <th>Kategori Kerja</th>
                <th>Ruangan</th>
                <th>Jabatan</th>
                <th>Status Aktif</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pegawai as $index => $p)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $p->nip }}</td>
                <td>{{ $p->nama }}</td>
                <td>{{ $p->kategori_kerja == 'shift' ? 'Shift' : ($p->kategori_kerja == 'non_shift_5_hari' ? 'Non Shift 5 Hari' : 'Non Shift') }}</td>
                <td>{{ $p->ruangan->nama_ruangan ?? '-' }}</td>
                <td>{{ $p->jabatan }}</td>
                <td>{{ $p->status_aktif ? 'Aktif' : 'Non-Aktif' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
