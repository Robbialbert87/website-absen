<?php

namespace App\Http\Controllers;

use App\Models\JadwalPegawai;
use App\Models\Pegawai;
use App\Models\Ruangan;
use App\Exports\JadwalExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request, $type = 'all')
    {
        $ruangans = Ruangan::all();
        $kategori_kerjas = Pegawai::distinct()->whereNotNull('kategori_kerja')->pluck('kategori_kerja');
        
        // Map type to either kategori_kerja or kategori_jadwal
        $default_kategori_kerja = null;
        $default_kategori_jadwal = null;
        
        if ($type === 'shift' || $type === 'non_shift') {
            $default_kategori_kerja = $type;
        } elseif ($type !== 'all') {
            $default_kategori_jadwal = $type;
        }

        $filters = [
            'bulan' => $request->get('bulan', date('m')),
            'tahun' => $request->get('tahun', date('Y')),
            'ruangan_id' => $request->get('ruangan_id'),
            'kategori_kerja' => $request->get('kategori_kerja', $default_kategori_kerja),
            'pegawai_id' => $request->get('pegawai_id'),
            'kategori_jadwal' => $request->get('kategori_jadwal', $default_kategori_jadwal),
        ];

        $pegawais = [];
        if ($filters['ruangan_id']) {
            $pegawais = Pegawai::where('ruangan_id', $filters['ruangan_id'])->get();
        }

        return view('report.index', compact('ruangans', 'kategori_kerjas', 'pegawais', 'filters', 'type'));
    }

    public function preview(Request $request)
    {
        $query = JadwalPegawai::with(['pegawai', 'shift'])
            ->whereMonth('tanggal_masuk', $request->bulan)
            ->whereYear('tanggal_masuk', $request->tahun);

        if ($request->ruangan_id) {
            $query->where('ruangan_id', $request->ruangan_id);
        }

        if ($request->pegawai_id) {
            $query->where('pegawai_id', $request->pegawai_id);
        }

        if ($request->kategori_jadwal) {
            $query->whereHas('shift', function ($q) use ($request) {
                $q->where('kategori_jadwal', $request->kategori_jadwal);
            });
        }

        if ($request->kategori_kerja) {
            $query->whereHas('pegawai', function ($q) use ($request) {
                $q->where('kategori_kerja', $request->kategori_kerja);
            });
        }

        $data = $query->orderBy('tanggal_masuk')->get();

        return view('report.preview', compact('data'))->render();
    }

    public function export(Request $request)
    {
        $filters = $request->only(['bulan', 'tahun', 'ruangan_id', 'kategori_kerja', 'pegawai_id', 'kategori_jadwal']);
        $filename = 'report_jadwal_' . ($filters['kategori_jadwal'] ?? 'all') . '_' . $filters['bulan'] . '_' . $filters['tahun'] . '.csv';

        return Excel::download(new JadwalExport($filters), $filename, \Maatwebsite\Excel\Excel::CSV);
    }

    public function getPegawaiByRuangan(Request $request)
    {
        $pegawais = Pegawai::where('ruangan_id', $request->ruangan_id)->get(['id', 'nama', 'nip']);
        return response()->json($pegawais);
    }
}
