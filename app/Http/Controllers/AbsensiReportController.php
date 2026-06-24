<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Pegawai;
use App\Models\Ruangan;
use App\Models\AbsensiKegiatan;
use App\Models\JadwalPegawai;
use App\Exports\AbsensiExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AbsensiReportController extends Controller
{
    public function index()
    {
        $kegiatans = Kegiatan::orderBy('tanggal_kegiatan', 'desc')->get();
        $ruangans = Ruangan::all();

        return view('report_absensi.index', compact('kegiatans', 'ruangans'));
    }

    public function preview(Request $request)
    {
        $data = $this->getReportData($request);

        return view('report_absensi.preview', compact('data'))->render();
    }

    public function export(Request $request, $format = 'excel')
    {
        $filters = $request->only(['bulan', 'tahun', 'kegiatan_id', 'ruangan_id']);
        $data = $this->getReportData($request);

        if ($format === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('report_absensi.excel', compact('data'))
                ->setPaper('a4', 'landscape');
            return $pdf->download('report_absensi_' . $filters['bulan'] . '_' . $filters['tahun'] . '.pdf');
        }

        return Excel::download(
            new AbsensiExport($data),
            'report_absensi_' . $filters['bulan'] . '_' . $filters['tahun'] . '.xlsx',
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    private function getReportData(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        $kegiatanId = $request->get('kegiatan_id');
        $ruanganId = $request->get('ruangan_id');

        $kegiatans = Kegiatan::whereYear('tanggal_kegiatan', $tahun)
            ->whereMonth('tanggal_kegiatan', $bulan)
            ->when($kegiatanId, fn($q) => $q->where('id', $kegiatanId))
            ->orderBy('tanggal_kegiatan')
            ->orderBy('jam_mulai')
            ->get();

        if ($kegiatans->isEmpty()) {
            return collect();
        }

        $pegawais = Pegawai::with('ruangan')
            ->where('status_aktif', 1)
            ->when($ruanganId, fn($q) => $q->where('ruangan_id', $ruanganId))
            ->orderBy('ruangan_id')
            ->orderBy('nama')
            ->get();

        $absensis = AbsensiKegiatan::whereIn('kegiatan_id', $kegiatans->pluck('id'))
            ->whereIn('pegawai_id', $pegawais->pluck('id'))
            ->get()
            ->groupBy('kegiatan_id');

        $jadwals = JadwalPegawai::whereIn('pegawai_id', $pegawais->pluck('id'))
            ->whereIn('tanggal_masuk', $kegiatans->pluck('tanggal_kegiatan'))
            ->get()
            ->groupBy('pegawai_id');

        $result = collect();

        foreach ($kegiatans as $kegiatan) {
            $kegiatanAbsensis = $absensis->get($kegiatan->id, collect());

            $pegawaiByRuangan = $pegawais->groupBy('ruangan_id');

            $ruanganData = collect();

            foreach ($pegawaiByRuangan as $ruangId => $pegawaiList) {
                $ruangan = $pegawaiList->first()->ruangan;

                $pegawaiData = $pegawaiList->map(function ($pegawai) use ($kegiatanAbsensis, $jadwals, $kegiatan) {
                    $absen = $kegiatanAbsensis->firstWhere('pegawai_id', $pegawai->id);
                    $jadwal = $jadwals->get($pegawai->id)?->firstWhere('tanggal_masuk', $kegiatan->tanggal_kegiatan);

                    return (object) [
                        'nip' => $pegawai->nip,
                        'nama' => $pegawai->nama,
                        'status' => $absen ? $absen->status : 'tidak_hadir',
                        'waktu_absen' => $absen ? $absen->waktu_absen : null,
                        'jam_masuk' => $jadwal ? $jadwal->jam_masuk : null,
                        'foto' => $absen ? $absen->foto : null,
                    ];
                });

                $hadir = $pegawaiData->where('status', 'hadir')->count();
                $terlambat = $pegawaiData->where('status', 'terlambat')->count();
                $tidakHadir = $pegawaiData->where('status', 'tidak_hadir')->count();

                $ruanganData->push((object) [
                    'ruangan' => $ruangan,
                    'pegawais' => $pegawaiData,
                    'total' => $pegawaiData->count(),
                    'hadir' => $hadir,
                    'terlambat' => $terlambat,
                    'tidak_hadir' => $tidakHadir,
                ]);
            }

            $result->push((object) [
                'kegiatan' => $kegiatan,
                'ruanganData' => $ruanganData,
            ]);
        }

        return $result;
    }
}
