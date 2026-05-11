<?php

namespace App\Exports;

use App\Models\JadwalPegawai;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Carbon\Carbon;

class JadwalExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    use Exportable;

    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = JadwalPegawai::query()
            ->with(['pegawai', 'shift'])
            ->whereMonth('tanggal_masuk', $this->filters['bulan'])
            ->whereYear('tanggal_masuk', $this->filters['tahun']);

        if (!empty($this->filters['ruangan_id'])) {
            $query->where('ruangan_id', $this->filters['ruangan_id']);
        }

        if (!empty($this->filters['pegawai_id'])) {
            $query->where('pegawai_id', $this->filters['pegawai_id']);
        }

        if (!empty($this->filters['kategori_jadwal'])) {
            $query->whereHas('shift', function ($q) {
                $q->where('kategori_jadwal', $this->filters['kategori_jadwal']);
            });
        }

        if (!empty($this->filters['kategori_kerja'])) {
            $query->whereHas('pegawai', function ($q) {
                $q->where('kategori_kerja', $this->filters['kategori_kerja']);
            });
        }

        return $query->orderBy('tanggal_masuk')->orderBy('pegawai_id');
    }

    public function headings(): array
    {
        return [
            'NIP',
            'jam_masuk',
            'jam_pulang',
        ];
    }

    public function map($jadwal): array
    {
        $isCuti = false;
        $isLibur = false;

        if ($jadwal->shift) {
            if ($jadwal->shift->kategori_jadwal === 'cuti' || stripos($jadwal->shift->nama_shift, 'cuti') !== false || stripos($jadwal->kode_shift, 'cuti') !== false) {
                $isCuti = true;
            } elseif (stripos($jadwal->shift->nama_shift, 'libur') !== false || stripos($jadwal->kode_shift, 'libur') !== false || $jadwal->kode_shift === 'L') {
                $isLibur = true;
            }
        } else {
            // Fallback for missing shift relation
            if (stripos($jadwal->kode_shift, 'cuti') !== false) {
                $isCuti = true;
            } elseif (stripos($jadwal->kode_shift, 'libur') !== false || $jadwal->kode_shift === 'L') {
                $isLibur = true;
            }
        }

        if ($isCuti || $isLibur) {
            $statusText = $isCuti ? 'Cuti' : 'Libur';
            $jamMasuk = Carbon::parse($jadwal->tanggal_masuk)->format('Y-m-d') . ' ' . $statusText;
            $jamPulang = Carbon::parse($jadwal->tanggal_masuk)->format('Y-m-d') . ' ' . $statusText;
        } else {
            $jamMasuk = Carbon::parse($jadwal->tanggal_masuk . ' ' . $jadwal->jam_masuk)->format('Y-m-d H:i:s');
            $jamPulang = Carbon::parse($jadwal->tanggal_pulang . ' ' . $jadwal->jam_pulang)->format('Y-m-d H:i:s');
        }

        return [
            $jadwal->pegawai?->nip,
            $jamMasuk,
            $jamPulang,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
