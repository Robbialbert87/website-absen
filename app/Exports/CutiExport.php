<?php

namespace App\Exports;

use App\Models\JadwalPegawai;
use App\Models\Ruangan;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CutiExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $bulan = $this->request->get('bulan', date('m'));
        $tahun = $this->request->get('tahun', date('Y'));
        $ruangan_id = $this->request->get('ruangan_id');
        $shift_id = $this->request->get('shift_id');
        $search = $this->request->get('search');

        $user = auth()->user();
        
        $query = JadwalPegawai::with(['pegawai.ruangan', 'shift'])
            ->whereHas('shift', function($q) {
                $q->where('kategori_jadwal', 'cuti')
                  ->orWhere('nama_shift', 'like', '%cuti%')
                  ->orWhere('kode_shift', 'like', '%cuti%');
            });

        if ($bulan && $tahun) {
            $query->whereMonth('tanggal_masuk', $bulan)
                  ->whereYear('tanggal_masuk', $tahun);
        }

        if ($ruangan_id && $ruangan_id !== 'all') {
            $query->where('ruangan_id', $ruangan_id);
        } elseif (!$user->hasRole('admin') && !$user->hasRole('super-admin')) {
            $allowedRoomIds = Ruangan::where('kepala_pegawai_id', $user->pegawai_id)
                ->orWhere('id', $user->ruangan_id)
                ->pluck('id')
                ->toArray();
            $query->whereIn('ruangan_id', $allowedRoomIds);
        }

        if ($shift_id && $shift_id !== 'all') {
            $query->where('shift_id', $shift_id);
        }

        if ($search) {
            $query->whereHas('pegawai', function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('nip', 'like', '%' . $search . '%');
            });
        }

        $records = $query->orderBy('pegawai_id')
                         ->orderBy('tanggal_masuk')
                         ->get();

        return collect($this->groupConsecutiveLeave($records));
    }

    private function groupConsecutiveLeave($records)
    {
        $grouped = [];
        if ($records->isEmpty()) return $grouped;

        $currentGroup = null;

        foreach ($records as $record) {
            $date = Carbon::parse($record->tanggal_masuk);
            
            if ($currentGroup && 
                $currentGroup['pegawai_id'] == $record->pegawai_id && 
                $currentGroup['shift_id'] == $record->shift_id &&
                Carbon::parse($currentGroup['end_date'])->addDay()->format('Y-m-d') == $date->format('Y-m-d')
            ) {
                $currentGroup['end_date'] = $date->format('Y-m-d');
                $currentGroup['total_days']++;
            } else {
                if ($currentGroup) {
                    $grouped[] = $currentGroup;
                }
                
                $currentGroup = [
                    'pegawai_id' => $record->pegawai_id,
                    'nip' => $record->pegawai->nip,
                    'nama_pegawai' => $record->pegawai->nama,
                    'ruangan' => $record->pegawai->ruangan->nama_ruangan ?? '-',
                    'jenis_cuti' => $record->shift->nama_shift,
                    'start_date' => $date->format('Y-m-d'),
                    'end_date' => $date->format('Y-m-d'),
                    'total_days' => $record->shift_id, // temporarily use for shift identification if needed, but not exported
                    'total_days_count' => 1,
                    'keterangan' => $record->keterangan ?? '-',
                    'shift_id' => $record->shift_id
                ];
            }
        }

        if ($currentGroup) {
            $grouped[] = $currentGroup;
        }

        return $grouped;
    }

    public function headings(): array
    {
        return [
            'NIP',
            'Nama Pegawai',
            'Ruangan',
            'Jenis Cuti',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Total Hari',
            'Keterangan'
        ];
    }

    public function map($row): array
    {
        $start = Carbon::parse($row['start_date'])->translatedFormat('d F Y');
        $end = Carbon::parse($row['end_date'])->translatedFormat('d F Y');
        
        return [
            $row['nip'],
            $row['nama_pegawai'],
            $row['ruangan'],
            $row['jenis_cuti'],
            $start,
            $end,
            $row['total_days_count'] . ' Hari',
            $row['keterangan']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
