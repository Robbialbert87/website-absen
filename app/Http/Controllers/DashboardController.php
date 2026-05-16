<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Ruangan;
use App\Models\Shift;
use App\Models\JadwalPegawai;
use App\Models\KalenderNasional;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

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

        // Get holidays & Sundays for non-shift auto-off
        $offDays = $this->getHolidaysAndOffDays($month, $year, $daysInMonth);
        $offDayNumbers = $offDays['off_days'];       // array of day numbers [1,7,8,...]
        $holidayNames  = $offDays['holiday_names'];  // [day => name]

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
            'ruangan_lengkap'      => 0,
            'ruangan_belum_lengkap' => 0,
            'pegawai_belum_lengkap' => 0,
        ];

        foreach ($allRuangan as $ruangan) {
            $totalPegawai = $ruangan->pegawai->count();
            if ($totalPegawai == 0) continue;

            $pegawaiLengkap        = 0;
            $pegawaiBelumLengkap   = 0;
            $totalFilledSlots      = 0; // actual filled slots (out of requiredSlots)
            $totalRequiredSlots    = 0; // total required working days across all employees

            $offCount       = count($offDayNumbers);
            $workingDays    = $daysInMonth - $offCount; // working days for non-shift

            foreach ($ruangan->pegawai as $pegawai) {
                $isNonShift = ($pegawai->kategori_kerja === 'non_shift');

                // Required days: non-shift only needs to fill working days
                $requiredDays = $isNonShift ? max($workingDays, 0) : $daysInMonth;

                $countJadwal = JadwalPegawai::where('pegawai_id', $pegawai->id)
                    ->whereMonth('tanggal_masuk', $month)
                    ->whereYear('tanggal_masuk', $year)
                    ->count();

                // Accumulate for room-level percentage
                $totalRequiredSlots += $requiredDays;
                $filled = min($countJadwal, $requiredDays); // cap at required
                $totalFilledSlots   += $filled;

                // Complete check: actual entries must reach required days
                if ($countJadwal >= $requiredDays) {
                    $pegawaiLengkap++;
                } else {
                    $pegawaiBelumLengkap++;
                    $summary['pegawai_belum_lengkap']++;
                }
            }

            // Percentage based on actual working-day slots only
            $persentase = $totalRequiredSlots > 0
                ? round(($totalFilledSlots / $totalRequiredSlots) * 100, 1)
                : 0;
            $persentase = min($persentase, 100);


            $isLengkap = ($pegawaiBelumLengkap == 0);
            if ($isLengkap) {
                $summary['ruangan_lengkap']++;
            } else {
                $summary['ruangan_belum_lengkap']++;
            }

            $monitoringData[] = [
                'id'                   => $ruangan->id,
                'nama_ruangan'         => $ruangan->nama_ruangan,
                'total_pegawai'        => $totalPegawai,
                'pegawai_lengkap'      => $pegawaiLengkap,
                'pegawai_belum_lengkap' => $pegawaiBelumLengkap,
                'persentase'           => $persentase,
                'is_lengkap'           => $isLengkap,
            ];
        }

        // Sorting for charts
        $sortedByPersentase = collect($monitoringData)->sortByDesc('persentase')->values();
        $topRuangan    = $sortedByPersentase->take(5);
        $bottomRuangan = collect($monitoringData)->sortBy('persentase')->take(5);

        $stats = [
            'monitoring' => [
                'summary'        => $summary,
                'data'           => $monitoringData,
                'top'            => $topRuangan,
                'bottom'         => $bottomRuangan,
                'days_in_month'  => $daysInMonth,
                'selected_month' => $month,
                'selected_year'  => $year,
            ]
        ];

        $listRuangan = Ruangan::all();

        return view('monitoring.index', compact('stats', 'listRuangan'));
    }


    public function getMonitoringDetail(Request $request)
    {
        $ruanganId   = $request->ruangan_id;
        $month       = $request->bulan;
        $year        = $request->tahun;
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

        // Fetch holidays & off-days
        $offDays      = $this->getHolidaysAndOffDays($month, $year, $daysInMonth);
        $offDayNumbers = $offDays['off_days'];      // [1, 7, 14, ...]
        $holidayNames  = $offDays['holiday_names']; // [day => name]

        $ruangan = Ruangan::with('pegawai')->findOrFail($ruanganId);
        $details = [];

        $offCount    = count($offDayNumbers);
        $workingDays = $daysInMonth - $offCount;

        foreach ($ruangan->pegawai as $pegawai) {
            $isNonShift = ($pegawai->kategori_kerja === 'non_shift');

            // Required days: non-shift only needs to fill working days
            $requiredDays = $isNonShift ? max($workingDays, 0) : $daysInMonth;

            $jadwal = JadwalPegawai::where('pegawai_id', $pegawai->id)
                ->whereMonth('tanggal_masuk', $month)
                ->whereYear('tanggal_masuk', $year)
                ->pluck('tanggal_masuk')
                ->map(fn($date) => (int) Carbon::parse($date)->format('j'))
                ->unique()
                ->toArray();

            $dayStatus = [];
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $isOff    = $isNonShift && in_array($i, $offDayNumbers);
                $isFilled = in_array($i, $jadwal);
                $label    = null;

                if ($isOff) {
                    $label = $holidayNames[$i] ?? 'Minggu';
                }

                $dayStatus[] = [
                    'day'       => $i,
                    'is_filled' => $isFilled,
                    'is_off'    => $isOff,
                    'label'     => $label,
                ];
            }

            // Missing = required working days - actual schedule entries
            $missingCount = max(0, $requiredDays - count($jadwal));
            $autoOffCount = $isNonShift ? $offCount : 0;

            if ($missingCount > 0) {
                $details[] = [
                    'nama_pegawai'  => $pegawai->nama,
                    'kategori'      => $pegawai->kategori_kerja,
                    'total_input'   => count($jadwal),
                    'auto_off'      => $autoOffCount,
                    'missing_count' => $missingCount,
                    'day_status'    => $dayStatus,
                ];
            }
        }

        return response()->json([
            'ruangan' => $ruangan->nama_ruangan,
            'details' => $details,
        ]);
    }

    /**
     * API endpoint: returns holiday events for FullCalendar as background events.
     * Called via AJAX from the jadwal calendar modal.
     */
    public function getHolidaysApi(Request $request)
    {
        $year  = (int) $request->get('year',  now()->year);
        $month = (int) $request->get('month', now()->month);

        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
        $offDays     = $this->getHolidaysAndOffDays($month, $year, $daysInMonth);

        $events = [];
        foreach ($offDays['off_days'] as $day) {
            $date  = Carbon::create($year, $month, $day)->format('Y-m-d');
            $label = $offDays['holiday_names'][$day] ?? 'Libur';
            $color = $offDays['holiday_colors'][$day] ?? '#dc3545';

            $events[] = [
                'title'           => $label,
                'start'           => $date,
                'allDay'          => true,
                'display'         => 'background',
                'backgroundColor' => $color,
                'classNames'      => ['holiday-bg'],
                'extendedProps'   => [
                    'is_holiday' => true,
                    'label'      => $label,
                    'color'      => $color,
                ],
            ];
        }

        return response()->json($events);
    }

    /**
     * Get all off-days for a month:
     *   - Every Sunday
     *   - Indonesian national holidays (fetched from public API, cached 24h)
     *
     * Returns:
     *   ['off_days' => [1,7,8,...], 'holiday_names' => [7 => 'Waisak', ...]]
     */
    private function getHolidaysAndOffDays(int $month, int $year, int $daysInMonth): array
    {
        // Get holidays from DB
        $dbHolidays = KalenderNasional::aktif()
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->get();

        $offDays      = [];
        $holidayNames = [];
        $holidayColors = [];

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = Carbon::create($year, $month, $i);

            // Sunday
            if ($date->dayOfWeek === Carbon::SUNDAY) {
                $offDays[]      = $i;
                $holidayNames[$i] = 'Minggu';
                $holidayColors[$i] = '#dc3545';
                continue;
            }

            // Check DB holiday
            $h = $dbHolidays->first(fn($item) => $item->tanggal->day === $i);
            if ($h) {
                $offDays[]        = $i;
                $holidayNames[$i] = $h->nama_hari_libur;
                $holidayColors[$i] = $h->jenis === 'cuti_bersama' ? '#ffc107' : '#dc3545';
            }
        }

        return [
            'off_days'      => $offDays,
            'holiday_names' => $holidayNames,
            'holiday_colors' => $holidayColors,
        ];
    }

}
