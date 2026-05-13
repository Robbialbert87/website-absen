<?php

namespace App\Http\Controllers;

use App\Models\KalenderNasional;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KalenderNasionalController extends Controller
{
    /**
     * Admin CRUD: List all holidays.
     */
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $jenis = $request->get('jenis');

        $query = KalenderNasional::whereYear('tanggal', $tahun)
            ->orderBy('tanggal');

        if ($jenis) {
            $query->where('jenis', $jenis);
        }

        $kalenders = $query->get();

        return view('kalender-nasional.index', compact('kalenders', 'tahun', 'jenis'));
    }

    /**
     * Store new holiday.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal'         => 'required|date|unique:kalender_nasional,tanggal',
            'nama_hari_libur' => 'required|string|max:100',
            'jenis'           => 'required|in:nasional,cuti_bersama',
            'status_aktif'    => 'sometimes|boolean',
        ]);

        KalenderNasional::create([
            'tanggal'         => $request->tanggal,
            'nama_hari_libur' => $request->nama_hari_libur,
            'jenis'           => $request->jenis,
            'warna'           => $request->jenis === 'cuti_bersama' ? '#ffc107' : '#e74c3c',
            'status_aktif'    => $request->boolean('status_aktif', true),
        ]);

        return redirect()->route('kalender-nasional.index', ['tahun' => date('Y', strtotime($request->tanggal))])
            ->with('success', 'Hari libur berhasil ditambahkan.');
    }

    /**
     * Update holiday.
     */
    public function update(Request $request, KalenderNasional $kalenderNasional)
    {
        $request->validate([
            'nama_hari_libur' => 'required|string|max:100',
            'jenis'           => 'required|in:nasional,cuti_bersama',
            'status_aktif'    => 'sometimes|boolean',
        ]);

        $kalenderNasional->update([
            'nama_hari_libur' => $request->nama_hari_libur,
            'jenis'           => $request->jenis,
            'warna'           => $request->jenis === 'cuti_bersama' ? '#ffc107' : '#e74c3c',
            'status_aktif'    => $request->boolean('status_aktif', true),
        ]);

        return redirect()->back()->with('success', 'Data berhasil diperbarui.');
    }

    /**
     * Delete holiday.
     */
    public function destroy(KalenderNasional $kalenderNasional)
    {
        $kalenderNasional->delete();
        return redirect()->back()->with('success', 'Hari libur berhasil dihapus.');
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus(KalenderNasional $kalenderNasional)
    {
        $kalenderNasional->update(['status_aktif' => !$kalenderNasional->status_aktif]);
        return response()->json(['success' => true, 'status' => $kalenderNasional->status_aktif]);
    }

    /**
     * API: Returns events for FullCalendar (background events).
     * Also includes Sundays automatically.
     */
    public function apiEvents(Request $request)
    {
        $start = $request->get('start');
        $end   = $request->get('end');

        $events = [];

        // --- 1. Sundays in range ---
        if ($start && $end) {
            $current = Carbon::parse($start);
            $endDate = Carbon::parse($end);
            while ($current->lt($endDate)) {
                if ($current->dayOfWeek === Carbon::SUNDAY) {
                    $events[] = [
                        'title'           => 'Minggu',
                        'start'           => $current->format('Y-m-d'),
                        'allDay'          => true,
                        'display'         => 'background',
                        'backgroundColor' => 'rgba(220, 53, 69, 0.08)',
                        'classNames'      => ['sunday-bg'],
                        'extendedProps'   => [
                            'is_holiday'   => true,
                            'jenis'        => 'minggu',
                            'label'        => 'Hari Minggu',
                        ],
                    ];
                }
                $current->addDay();
            }
        }

        // --- 2. National holidays from DB ---
        $query = KalenderNasional::aktif()->orderBy('tanggal');
        if ($start) $query->where('tanggal', '>=', Carbon::parse($start)->format('Y-m-d'));
        if ($end)   $query->where('tanggal', '<=', Carbon::parse($end)->format('Y-m-d'));

        foreach ($query->get() as $k) {
            $bgColor = $k->jenis === 'cuti_bersama'
                ? 'rgba(255, 255, 0, 0.2)'
                : 'rgba(255, 192, 203, 0.4)';

            $events[] = [
                'title'           => $k->nama_hari_libur,
                'start'           => $k->tanggal->format('Y-m-d'),
                'allDay'          => true,
                'display'         => 'background',
                'backgroundColor' => $bgColor,
                'classNames'      => [$k->jenis === 'cuti_bersama' ? 'cuti-bersama-bg' : 'holiday-bg'],
                'extendedProps'   => [
                    'is_holiday'   => true,
                    'jenis'        => $k->jenis,
                    'label'        => $k->nama_hari_libur,
                    'db_id'        => $k->id,
                ],
            ];
        }

        return response()->json($events);
    }

    /**
     * API: Returns list of holiday dates for a given month/year
     * Used by monitoring to determine off-days for non-shift employees.
     */
    public function apiOffDays(Request $request)
    {
        $month = (int) $request->get('month', now()->month);
        $year  = (int) $request->get('year',  now()->year);

        $holidays = KalenderNasional::aktif()
            ->forMonth($month, $year)
            ->get(['tanggal', 'nama_hari_libur', 'jenis'])
            ->map(fn($k) => [
                'date'  => $k->tanggal->format('Y-m-d'),
                'day'   => (int) $k->tanggal->format('j'),
                'label' => $k->nama_hari_libur,
                'jenis' => $k->jenis,
            ]);

        return response()->json($holidays);
    }
}
