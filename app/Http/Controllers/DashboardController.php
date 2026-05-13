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

    public function monitoring(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today();
        
        $month = $request->get('bulan', $today->month);
        $year = $request->get('tahun', $today->year);
        $ruangan_filter = $request->get('ruangan_id');

        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
        
        $queryRuangan = Ruangan::query();

        if ($user->hasRole('kepala_ruangan') && $user->pegawai_id) {
            $ruanganIds = Ruangan::where('kepala_pegawai_id', $user->pegawai_id)->pluck('id');
            if ($ruanganIds->isNotEmpty()) {
                $queryRuangan->whereIn('id', $ruanganIds);
            } else {
                $ruanganId = $user->pegawai->ruangan_id ?? null;
                if ($ruanganId) {
                    $queryRuangan->where('id', $ruanganId);
                }
            }
        }

        if ($ruangan_filter) {
            $queryRuangan->where('id', $ruangan_filter);
        }

        // --- Monitoring Logic ---
        $allRuangan = $queryRuangan->with('pegawai')->get();
        $monitoringData = [];
        $summary = [
            'ruangan_lengkap' => 0,
            'ruangan_belum_lengkap' => 0,
            'pegawai_belum_lengkap' => 0,
        ];

        foreach ($allRuangan as $ruangan) {
            $totalPegawai = $ruangan->pegawai->count();
            if ($totalPegawai == 0) continue;

            $pegawaiLengkap = 0;
            $pegawaiBelumLengkap = 0;
            $totalJadwalTerisi = 0;
            $totalJadwalSeharusnya = $totalPegawai * $daysInMonth;

            foreach ($ruangan->pegawai as $pegawai) {
                $countJadwal = JadwalPegawai::where('pegawai_id', $pegawai->id)
                    ->whereMonth('tanggal_masuk', $month)
                    ->whereYear('tanggal_masuk', $year)
                    ->count();
                
                $totalJadwalTerisi += $countJadwal;

                if ($countJadwal >= $daysInMonth) {
                    $pegawaiLengkap++;
                } else {
                    $pegawaiBelumLengkap++;
                    $summary['pegawai_belum_lengkap']++;
                }
            }

            $persentase = $totalJadwalSeharusnya > 0 ? round(($totalJadwalTerisi / $totalJadwalSeharusnya) * 100, 1) : 0;
            
            $isLengkap = ($pegawaiBelumLengkap == 0);
            if ($isLengkap) {
                $summary['ruangan_lengkap']++;
            } else {
                $summary['ruangan_belum_lengkap']++;
            }

            $monitoringData[] = [
                'id' => $ruangan->id,
                'nama_ruangan' => $ruangan->nama_ruangan,
                'total_pegawai' => $totalPegawai,
                'pegawai_lengkap' => $pegawaiLengkap,
                'pegawai_belum_lengkap' => $pegawaiBelumLengkap,
                'persentase' => $persentase,
                'is_lengkap' => $isLengkap
            ];
        }

        // Sorting for charts
        $sortedByPersentase = collect($monitoringData)->sortByDesc('persentase')->values();
        $topRuangan = $sortedByPersentase->take(5);
        $bottomRuangan = collect($monitoringData)->sortBy('persentase')->take(5);

        $stats = [
            'monitoring' => [
                'summary' => $summary,
                'data' => $monitoringData,
                'top' => $topRuangan,
                'bottom' => $bottomRuangan,
                'days_in_month' => $daysInMonth,
                'selected_month' => $month,
                'selected_year' => $year
            ]
        ];

        $listRuangan = Ruangan::all();

        return view('monitoring.index', compact('stats', 'listRuangan'));
    }


    public function getMonitoringDetail(Request $request)
    {
        $ruanganId = $request->ruangan_id;
        $month = $request->bulan;
        $year = $request->tahun;
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

        $ruangan = Ruangan::with('pegawai')->findOrFail($ruanganId);
        $details = [];

        foreach ($ruangan->pegawai as $pegawai) {
            $jadwal = JadwalPegawai::where('pegawai_id', $pegawai->id)
                ->whereMonth('tanggal_masuk', $month)
                ->whereYear('tanggal_masuk', $year)
                ->pluck('tanggal_masuk')
                ->map(function($date) {
                    return (int) Carbon::parse($date)->format('j');
                })
                ->unique()
                ->toArray();

            $dayStatus = [];
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $dayStatus[] = [
                    'day' => $i,
                    'is_filled' => in_array($i, $jadwal)
                ];
            }

            if (count($jadwal) < $daysInMonth) {
                $details[] = [
                    'nama_pegawai' => $pegawai->nama,
                    'total_input' => count($jadwal),
                    'missing_count' => $daysInMonth - count($jadwal),
                    'day_status' => $dayStatus
                ];
            }
        }

        return response()->json([
            'ruangan' => $ruangan->nama_ruangan,
            'details' => $details
        ]);
    }

}
