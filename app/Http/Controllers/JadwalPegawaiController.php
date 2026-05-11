<?php

namespace App\Http\Controllers;

use App\Models\JadwalPegawai;
use App\Models\Pegawai;
use App\Models\Ruangan;
use App\Models\Shift;
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

        if (!$user->hasRole('admin') && !$user->hasRole('super-admin')) {
            $query->where(function($q) use ($user) {
                $q->where('kepala_pegawai_id', $user->pegawai_id)
                  ->orWhere('id', $user->ruangan_id);
            });
        }

        $ruangans = $query->get();
        $selected_ruangan_id = $request->get('ruangan_id', $ruangans->first()?->id);

        $daysInMonth = Carbon::create($tahun, $bulan)->daysInMonth;
        $dates = [];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $dates[] = Carbon::create($tahun, $bulan, $i);
        }

        $pegawais = Pegawai::where('ruangan_id', $selected_ruangan_id)->get();
        
        $jadwal = JadwalPegawai::with('shift')
            ->where('ruangan_id', $selected_ruangan_id)
            ->whereMonth('tanggal_masuk', $bulan)
            ->whereYear('tanggal_masuk', $tahun)
            ->get()
            ->groupBy(['pegawai_id', function ($item) {
                return Carbon::parse($item->tanggal_masuk)->format('j');
            }]);

        $shifts = Shift::all();

        return view('jadwal.index', compact('ruangans', 'selected_ruangan_id', 'bulan', 'tahun', 'dates', 'pegawais', 'jadwal', 'shifts'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $query = Ruangan::query();

        if (!$user->hasRole('admin') && !$user->hasRole('super-admin')) {
            $query->where(function($q) use ($user) {
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

        return view('jadwal.create', compact('ruangans', 'shifts', 'bulan', 'tahun', 'ruangan_id', 'dates', 'pegawais', 'jadwal_existing', 'prefillShiftByDay'));
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
            foreach ($request->jadwal as $pegawai_id => $days) {
                foreach ($days as $day => $shift_id) {
                    if (!$shift_id) {
                        // If no shift selected, delete existing record for that day
                        JadwalPegawai::where('pegawai_id', $pegawai_id)
                            ->where('tanggal_masuk', Carbon::create($tahun, $bulan, $day)->format('Y-m-d'))
                            ->delete();
                        continue;
                    }

                    $shift = Shift::find($shift_id);
                    if (!$shift) continue;

                    $tanggal_masuk = Carbon::create($tahun, $bulan, $day);
                    $tanggal_pulang = clone $tanggal_masuk;

                    // Logic for night shift: if jam_pulang < jam_masuk, it ends next day
                    // Or if kode_shift is 'M'
                    if ($shift->jam_pulang < $shift->jam_masuk || $shift->kode_shift == 'M') {
                        if ($shift->kode_shift != 'L') { // Libur is usually 00:00 - 00:00
                             $tanggal_pulang->addDay();
                        }
                    }

                    JadwalPegawai::updateOrCreate(
                        [
                            'pegawai_id' => $pegawai_id,
                            'tanggal_masuk' => $tanggal_masuk->format('Y-m-d'),
                        ],
                        [
                            'ruangan_id' => $ruangan_id,
                            'shift_id' => $shift_id,
                            'jam_masuk' => $shift->jam_masuk,
                            'jam_pulang' => $shift->jam_pulang,
                            'tanggal_pulang' => $tanggal_pulang->format('Y-m-d'),
                            'kode_shift' => $shift->kode_shift,
                        ]
                    );
                }
            }
            DB::commit();
            return redirect()->route('jadwal.index', ['ruangan_id' => $ruangan_id, 'bulan' => $bulan, 'tahun' => $tahun])
                ->with('success', 'Jadwal berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan jadwal: ' . $e->getMessage());
        }
    }
}
