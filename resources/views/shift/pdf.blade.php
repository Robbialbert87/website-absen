<!DOCTYPE html>
<html>
<head>
    <title>Data Jadwal</title>
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
    <h2 class="text-center">Data Master Jadwal</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Jadwal</th>
                <th>Kategori</th>
                <th>Jam Masuk</th>
                <th>Jam Pulang</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($shifts as $index => $s)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $s->kode_shift }}</td>
                <td>{{ $s->nama_shift }}</td>
                <td>{{ $s->kategori_jadwal == 'shift' ? 'Shift' : 'Non Shift' }}</td>
                <td>{{ $s->jam_masuk }}</td>
                <td>{{ $s->jam_pulang }}</td>
                <td>{{ $s->keterangan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
