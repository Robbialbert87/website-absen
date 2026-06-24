<!DOCTYPE html>
<html>
<head>
    <title>Data User</title>
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
    <h2 class="text-center">Data Master User</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama User</th>
                <th>Pegawai Terhubung</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $index => $u)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $u->name }}</td>
                <td>{{ $u->pegawai->nama ?? 'Belum Terhubung' }} ({{ $u->pegawai->nip ?? '-' }})</td>
                <td>{{ $u->roles->pluck('name')->implode(', ') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
