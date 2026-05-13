<?php

namespace App\Http\Controllers;

use App\Models\JadwalPegawai;
use App\Models\Pegawai;
use App\Models\Ruangan;
use App\Models\Shift;
use App\Models\KalenderNasional;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalPegawaiController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        $user = auth()->user();
        $query = Ruangan::query();

        if (! $user->hasRole('admin') && ! $user->hasRole('super-admin')) {
            $query->where(function ($q) use ($user) {
                $q->where('kepala_pegawai_id', $user->pegawai_id)
                    ->orWhere('id', $user->ruangan_id);
            });
        }

        $ruangans = $query->get();
        
        $default_ruangan = ($user->isAdmin() || $user->hasRole('super-admin')) ? 'all' : $ruangans->first()?->id;
        $selected_ruangan_id = $request->get('ruangan_id', $default_ruangan);

        $search = $request->get('search');

        $daysInMonth = Carbon::create($tahun, $bulan)->daysInMonth;

        $dates = [];

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $dates[] = Carbon::create($tahun, $bulan, $i);
        }

        $pegawaiQuery = Pegawai::with('ruangan');

        // jika bukan admin/super-admin
        if (! $user->hasRole('admin') && ! $user->hasRole('super-admin')) {
            // Ambil ID ruangan yang diperbolehkan (ruangan sendiri + ruangan yang dikepalai)
            $allowedRoomIds = $ruangans->pluck('id')->toArray();
            $pegawaiQuery->whereIn('ruangan_id', $allowedRoomIds);
        }

        // filter dropdown ruangan
        if (
            $selected_ruangan_id &&
            $selected_ruangan_id !== 'all'
        ) {
            $pegawaiQuery->where('ruangan_id', $selected_ruangan_id);
        }

        // search nama
        if ($search) {
            $pegawaiQuery->where('nama', 'like', '%'.$search.'%');
        }
        
        $pegawais = $pegawaiQuery->get();

        $pegawaiIds = $pegawais->pluck('id')->toArray();

        $jadwal = JadwalPegawai::with(['shift' => function($q) {
                $q->select('id', 'nama_shift', 'kode_shift', 'jam_masuk', 'jam_pulang', 'warna');
            }])
            ->select('id', 'pegawai_id', 'shift_id', 'tanggal_masuk', 'jam_masuk', 'jam_pulang', 'kode_shift')
            ->whereIn('pegawai_id', $pegawaiIds)
            ->whereMonth('tanggal_masuk', $bulan)
            ->whereYear('tanggal_masuk', $tahun)
            ->get()
            ->groupBy(['pegawai_id', function ($item) {
                return Carbon::parse($item->tanggal_masuk)->format('j');
            }]);

        $shifts = Shift::all();
        
        $holidays = KalenderNasional::aktif()
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get()
            ->keyBy(function($item) {
                return $item->tanggal->format('j');
            });

        return view('jadwal.index', compact('ruangans', 'selected_ruangan_id', 'pegawais', 'shifts', 'bulan', 'tahun', 'dates', 'jadwal', 'search', 'holidays'));
    }

    public function getEvents(Request $request, $pegawai_id)
    {
        $start = $request->get('start');
        $end = $request->get('end');

        $jadwals = JadwalPegawai::with('shift')
            ->where('pegawai_id', $pegawai_id)
            ->whereBetween('tanggal_masuk', [
                Carbon::parse($start)->format('Y-m-d'),
                Carbon::parse($end)->format('Y-m-d'),
            ])
            ->get();

        $events = $jadwals->map(function ($j) {
            return [
                'id' => $j->id,
                'title' => $j->shift->nama_shift.' ('.substr($j->jam_masuk, 0, 5).')',
                'start' => $j->tanggal_masuk.'T'.$j->jam_masuk,
                'end' => $j->tanggal_pulang.'T'.$j->jam_pulang,
                'backgroundColor' => $j->shift->warna,
                'borderColor' => $j->shift->warna,
                'textColor' => '#fff',
                'extendedProps' => [
                    'shift_id' => $j->shift_id,
                    'kode_shift' => $j->kode_shift,
                    'jam_masuk' => $j->jam_masuk,
                    'jam_pulang' => $j->jam_pulang,
                    'tanggal_masuk' => $j->tanggal_masuk,
                ],
            ];
        });

        return response()->json($events);
    }

    public function saveSingle(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'shift_id' => 'required|exists:shifts,id',
            'tanggal' => 'required|date',
        ]);

        $pegawai = Pegawai::findOrFail($request->pegawai_id);
        $shift = Shift::findOrFail($request->shift_id);

        $tanggal_masuk = Carbon::parse($request->tanggal);
        $tanggal_pulang = clone $tanggal_masuk;

        // Night shift logic: if jam_pulang < jam_masuk, it ends next day
        // Or if kode_shift is 'M' or kategori is 'malam'
        if ($shift->jam_pulang < $shift->jam_masuk || $shift->kode_shift == 'M' || strtolower($shift->kategori_jadwal) == 'malam') {
            $tanggal_pulang->addDay();
        }

        $jadwal = JadwalPegawai::updateOrCreate(
            [
                'pegawai_id' => $pegawai->id,
                'tanggal_masuk' => $tanggal_masuk->format('Y-m-d'),
            ],
            [
                'ruangan_id' => $pegawai->ruangan_id,
                'shift_id' => $shift->id,
                'jam_masuk' => $shift->jam_masuk,
                'jam_pulang' => $shift->jam_pulang,
                'tanggal_pulang' => $tanggal_pulang->format('Y-m-d'),
                'kode_shift' => $shift->kode_shift,
            ]
        );

        return response()->json(['success' => true, 'message' => 'Jadwal berhasil disimpan']);
    }

    public function deleteSingle(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'tanggal' => 'required|date',
        ]);

        JadwalPegawai::where('pegawai_id', $request->pegawai_id)
            ->where('tanggal_masuk', $request->tanggal)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Jadwal berhasil dihapus']);
    }

    public function autoFill(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'pegawai_id' => 'nullable|exists:pegawai,id',
            'ruangan_id' => 'nullable|exists:ruangan,id',
            'bulan' => 'required',
            'tahun' => 'required',
        ]);

        $bulan = $request->bulan;
        $tahun = $request->tahun;

        // Permission check
        if (!$user->isAdmin() && !$user->hasRole('super-admin')) {
            $allowedRoomIds = Ruangan::where('kepala_pegawai_id', $user->pegawai_id)
                ->orWhere('id', $user->ruangan_id)
                ->pluck('id')
                ->toArray();
            
            if ($request->ruangan_id && !in_array($request->ruangan_id, $allowedRoomIds)) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses ke ruangan ini.'], 403);
            }
            
            if ($request->pegawai_id) {
                $pegawai = Pegawai::find($request->pegawai_id);
                if (!$pegawai || !in_array($pegawai->ruangan_id, $allowedRoomIds)) {
                    return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses ke pegawai ini.'], 403);
                }
            }
        }

        // Fetch non-shift shifts mapping
        $dayFieldMap = [
            0 => 'is_minggu',
            1 => 'is_senin',
            2 => 'is_selasa',
            3 => 'is_rabu',
            4 => 'is_kamis',
            5 => 'is_jumat',
            6 => 'is_sabtu',
        ];

        $nonShiftShifts = Shift::where('kategori_jadwal', 'non_shift')->get();
        $prefillShiftByDay = [];
        foreach ($dayFieldMap as $dayNum => $field) {
            foreach ($nonShiftShifts as $s) {
                if ($s->$field) {
                    $prefillShiftByDay[$dayNum] = $s;
                    break;
                }
            }
        }

        if (empty($prefillShiftByDay)) {
            return response()->json(['success' => false, 'message' => 'Master jadwal non-shift belum dikonfigurasi.'], 422);
        }

        $query = Pegawai::where('kategori_kerja', 'non_shift');
        
        if ($request->pegawai_id) {
            $query->where('id', $request->pegawai_id);
        } elseif ($request->ruangan_id && $request->ruangan_id !== 'all') {
            $query->where('ruangan_id', $request->ruangan_id);
        } elseif ($request->ruangan_id === 'all') {
            if (!$user->isAdmin() && !$user->hasRole('super-admin')) {
                $query->whereIn('ruangan_id', $allowedRoomIds);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Pegawai atau Ruangan harus dipilih.'], 422);
        }

        $pegawais = $query->get();
        if ($pegawais->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Tidak ada pegawai non-shift yang ditemukan.'], 404);
        }

        // Fetch holidays for the month
        $holidays = KalenderNasional::aktif()
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get()
            ->pluck('tanggal')
            ->map(fn($d) => $d->format('Y-m-d'))
            ->toArray();

        // Find Libur shift for non_shift
        $liburShift = Shift::where('kategori_jadwal', 'non_shift')
            ->where(function($q) {
                $q->where('kode_shift', 'L')
                  ->orWhere('kode_shift', 'Libur')
                  ->orWhere('nama_shift', 'like', '%Libur%');
            })
            ->first();

        if (!$liburShift) {
            // Fallback to any shift with 'L' or 'Libur' if non_shift specific one not found
            $liburShift = Shift::where('kode_shift', 'L')
                ->orWhere('kode_shift', 'Libur')
                ->orWhere('nama_shift', 'like', '%Libur%')
                ->first();
        }

        $daysInMonth = Carbon::create($tahun, $bulan)->daysInMonth;
        $pegawaiIds = $pegawais->pluck('id')->toArray();

        DB::beginTransaction();
        try {
            // Bulk delete existing non-manual schedules if necessary, 
            // or just overwrite. To be safe and fast, we delete first.
            JadwalPegawai::whereIn('pegawai_id', $pegawaiIds)
                ->whereMonth('tanggal_masuk', $bulan)
                ->whereYear('tanggal_masuk', $tahun)
                ->delete();

            $dataToInsert = [];
            $now = now();

            foreach ($pegawais as $pegawai) {
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = Carbon::create($tahun, $bulan, $day);
                    $dateString = $date->format('Y-m-d');
                    $dayOfWeek = $date->dayOfWeek;

                    $shift = null;

                    // Prioritize Holiday
                    if (in_array($dateString, $holidays) && $liburShift) {
                        $shift = $liburShift;
                    } elseif (isset($prefillShiftByDay[$dayOfWeek])) {
                        $shift = $prefillShiftByDay[$dayOfWeek];
                    }

                    if ($shift) {
                        $tanggal_masuk = $date->format('Y-m-d');
                        $tanggal_pulang = $date->format('Y-m-d');

                        if ($shift->jam_pulang < $shift->jam_masuk) {
                            $tanggal_pulang = $date->copy()->addDay()->format('Y-m-d');
                        }

                        $dataToInsert[] = [
                            'pegawai_id' => $pegawai->id,
                            'ruangan_id' => $pegawai->ruangan_id,
                            'shift_id' => $shift->id,
                            'tanggal_masuk' => $tanggal_masuk,
                            'jam_masuk' => $shift->jam_masuk,
                            'tanggal_pulang' => $tanggal_pulang,
                            'jam_pulang' => $shift->jam_pulang,
                            'kode_shift' => $shift->kode_shift,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            }

            // Bulk insert in chunks of 500 to avoid query size limits
            foreach (array_chunk($dataToInsert, 500) as $chunk) {
                JadwalPegawai::insert($chunk);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Auto input jadwal non-shift (disesuaikan libur nasional) berhasil dilakukan.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal melakukan auto input: '.$e->getMessage()], 500);
        }
    }

    public function resetJadwal(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'pegawai_id' => 'nullable|exists:pegawai,id',
            'ruangan_id' => 'nullable|exists:ruangan,id',
            'bulan' => 'required',
            'tahun' => 'required',
        ]);

        $query = JadwalPegawai::whereMonth('tanggal_masuk', $request->bulan)
            ->whereYear('tanggal_masuk', $request->tahun);

        if ($request->pegawai_id) {
            // Permission check for individual reset
            if (!$user->isAdmin() && !$user->hasRole('super-admin')) {
                $pegawai = Pegawai::find($request->pegawai_id);
                $allowedRoomIds = Ruangan::where('kepala_pegawai_id', $user->pegawai_id)
                    ->orWhere('id', $user->ruangan_id)
                    ->pluck('id')
                    ->toArray();
                
                if (!$pegawai || !in_array($pegawai->ruangan_id, $allowedRoomIds)) {
                    return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses ke pegawai ini.'], 403);
                }
            }
            $query->where('pegawai_id', $request->pegawai_id);
        } elseif ($request->ruangan_id) {
            if ($request->ruangan_id !== 'all') {
                // Permission check for room reset
                if (!$user->isAdmin() && !$user->hasRole('super-admin')) {
                    $allowedRoomIds = Ruangan::where('kepala_pegawai_id', $user->pegawai_id)
                        ->orWhere('id', $user->ruangan_id)
                        ->pluck('id')
                        ->toArray();
                    
                    if (!in_array($request->ruangan_id, $allowedRoomIds)) {
                        return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses ke ruangan ini.'], 403);
                    }
                }
                $query->where('ruangan_id', $request->ruangan_id);
            } else {
                // Handle 'all' rooms
                if (!$user->isAdmin() && !$user->hasRole('super-admin')) {
                    $allowedRoomIds = Ruangan::where('kepala_pegawai_id', $user->pegawai_id)
                        ->orWhere('id', $user->ruangan_id)
                        ->pluck('id')
                        ->toArray();
                    $query->whereIn('ruangan_id', $allowedRoomIds);
                }
                // If admin and 'all', no extra where needed
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Pegawai atau Ruangan harus dipilih.'], 422);
        }

        try {
            $count = $query->delete();
            return response()->json(['success' => true, 'message' => "Jadwal berhasil dikosongkan ($count data dihapus)."]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal meriset jadwal: '.$e->getMessage()], 500);
        }
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $query = Ruangan::query();

        if (! $user->hasRole('admin') && ! $user->hasRole('super-admin')) {
            $query->where(function ($q) use ($user) {
                $q->where('kepala_pegawai_id', $user->pegawai_id)
                    ->orWhere('id', $user->ruangan_id);
            });
        }

        $ruangans = $query->get();
        $shifts = Shift::all();

        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        $ruangan_id = $request->get('ruangan_id');

        $dates = [];
        $pegawais = [];
        $jadwal_existing = [];

        if ($ruangan_id) {
            $daysInMonth = Carbon::create($tahun, $bulan)->daysInMonth;
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $dates[] = Carbon::create($tahun, $bulan, $i);
            }

            $pegawais = Pegawai::where('ruangan_id', $ruangan_id)->get();

            $jadwal_existing = JadwalPegawai::where('ruangan_id', $ruangan_id)
                ->whereMonth('tanggal_masuk', $bulan)
                ->whereYear('tanggal_masuk', $tahun)
                ->get()
                ->groupBy(['pegawai_id', function ($item) {
                    return Carbon::parse($item->tanggal_masuk)->format('j');
                }]);
        }

        // Build pre-fill map: PHP dayOfWeek (0=Sun,1=Mon,...,6=Sat) => Shift
        // This is used to auto-fill non-shift employee schedules
        $dayFieldMap = [
            0 => 'is_minggu',
            1 => 'is_senin',
            2 => 'is_selasa',
            3 => 'is_rabu',
            4 => 'is_kamis',
            5 => 'is_jumat',
            6 => 'is_sabtu',
        ];

        $nonShiftShifts = Shift::where('kategori_jadwal', 'non_shift')->get();

        // Build: dayOfWeek => Shift model
        $prefillShiftByDay = [];
        foreach ($dayFieldMap as $dayNum => $field) {
            foreach ($nonShiftShifts as $s) {
                if ($s->$field) {
                    $prefillShiftByDay[$dayNum] = $s;
                    break; // Take the first matching non-shift for that day
                }
            }
        }

        $holidays = KalenderNasional::aktif()
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get()
            ->keyBy(function($item) {
                return $item->tanggal->format('j');
            });

        return view('jadwal.create', compact('ruangans', 'shifts', 'bulan', 'tahun', 'ruangan_id', 'dates', 'pegawais', 'jadwal_existing', 'prefillShiftByDay', 'holidays'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ruangan_id' => 'required|exists:ruangan,id',
            'bulan' => 'required',
            'tahun' => 'required',
            'jadwal' => 'required|array',
        ]);

        $ruangan_id = $request->ruangan_id;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        DB::beginTransaction();
        try {
            $now = now();
            $dataToInsert = [];
            $pegawaiIds = array_keys($request->jadwal);

            // Fetch all shifts once to avoid repeated queries
            $shifts = Shift::all()->keyBy('id');

            foreach ($request->jadwal as $pegawai_id => $days) {
                foreach ($days as $day => $shift_id) {
                    $tanggal_masuk_date = Carbon::create($tahun, $bulan, $day);
                    $tanggal_masuk = $tanggal_masuk_date->format('Y-m-d');

                    if (!$shift_id) {
                        // Delete if empty
                        JadwalPegawai::where('pegawai_id', $pegawai_id)
                            ->where('tanggal_masuk', $tanggal_masuk)
                            ->delete();
                        continue;
                    }

                    $shift = $shifts->get($shift_id);
                    if (!$shift) continue;

                    $tanggal_pulang = clone $tanggal_masuk_date;
                    if ($shift->jam_pulang < $shift->jam_masuk || $shift->kode_shift == 'M') {
                        if ($shift->kode_shift != 'L') {
                            $tanggal_pulang->addDay();
                        }
                    }

                    $dataToInsert[] = [
                        'pegawai_id' => $pegawai_id,
                        'tanggal_masuk' => $tanggal_masuk,
                        'ruangan_id' => $ruangan_id,
                        'shift_id' => $shift_id,
                        'jam_masuk' => $shift->jam_masuk,
                        'jam_pulang' => $shift->jam_pulang,
                        'tanggal_pulang' => $tanggal_pulang->format('Y-m-d'),
                        'kode_shift' => $shift->kode_shift,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if (!empty($dataToInsert)) {
                // Delete existing records for the same employees and dates to avoid duplicates
                foreach ($request->jadwal as $pegawai_id => $days) {
                    $dates = array_map(fn($day) => Carbon::create($tahun, $bulan, $day)->format('Y-m-d'), array_keys($days));
                    JadwalPegawai::where('pegawai_id', $pegawai_id)
                        ->whereIn('tanggal_masuk', $dates)
                        ->delete();
                }

                // Bulk insert
                foreach (array_chunk($dataToInsert, 500) as $chunk) {
                    JadwalPegawai::insert($chunk);
                }
            }

            DB::commit();

            return redirect()->route('jadwal.index', ['ruangan_id' => $ruangan_id, 'bulan' => $bulan, 'tahun' => $tahun])
                ->with('success', 'Jadwal berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan jadwal: '.$e->getMessage());
        }
    }
}
