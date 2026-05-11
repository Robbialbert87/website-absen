<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Ruangan;
use App\Models\Shift;
use App\Models\JadwalPegawai;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today();
        $month = $today->month;
        $year = $today->year;
        
        $queryPegawai = Pegawai::query();
        $queryRuangan = Ruangan::query();
        
        // Base query for Cuti (Checking category OR keywords to handle existing data)
        $baseCutiQuery = JadwalPegawai::whereHas('shift', function($q) {
                $q->where('kategori_jadwal', 'cuti')
                  ->orWhere('nama_shift', 'like', '%cuti%')
                  ->orWhere('kode_shift', 'like', '%cuti%');
            });

        if ($user->hasRole('kepala_ruangan') && $user->pegawai_id) {
            $ruanganIds = Ruangan::where('kepala_pegawai_id', $user->pegawai_id)->pluck('id');
            
            if ($ruanganIds->isNotEmpty()) {
                $queryPegawai->whereIn('ruangan_id', $ruanganIds);
                $queryRuangan->whereIn('id', $ruanganIds);
                $baseCutiQuery->whereIn('ruangan_id', $ruanganIds);
            } else {
                $ruanganId = $user->pegawai->ruangan_id ?? null;
                if ($ruanganId) {
                    $queryPegawai->where('ruangan_id', $ruanganId);
                    $queryRuangan->where('id', $ruanganId);
                    $baseCutiQuery->where('ruangan_id', $ruanganId);
                }
            }
        }

        // Specific counts (Counting unique employees)
        $totalCutiHariIni = (clone $baseCutiQuery)
            ->whereDate('tanggal_masuk', $today->format('Y-m-d'))
            ->distinct()
            ->count('pegawai_id');
            
        $totalCutiBulanIni = (clone $baseCutiQuery)
            ->whereMonth('tanggal_masuk', $month)
            ->whereYear('tanggal_masuk', $year)
            ->distinct()
            ->count('pegawai_id');

        $stats = [
            'total_pegawai' => $queryPegawai->count(),
            'total_ruangan' => $queryRuangan->count(),
            'total_cuti_hari_ini' => $totalCutiHariIni,
            'total_cuti_bulan_ini' => $totalCutiBulanIni,
        ];

        return view('dashboard', compact('stats'));
    }
}
