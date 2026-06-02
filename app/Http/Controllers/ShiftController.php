<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index(Request $request, \App\Filters\ShiftFilter $filters)
    {
        $query = Shift::filter($filters);

        if ($request->export === 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ShiftExport($query->get()), 'jadwal.xlsx');
        }

        if ($request->export === 'pdf') {
            $shifts = $query->get();
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('shift.pdf', compact('shifts'))->setPaper('a4', 'portrait');
            return $pdf->download('jadwal.pdf');
        }

        $perPage = $request->get('per_page', 10);
        $shifts = $query->paginate($perPage)->withQueryString();

        if ($request->ajax()) {
            return view('shift._table', compact('shifts'))->render();
        }

        return view('shift.index', compact('shifts'));
    }

    public function create()
    {
        return view('shift.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_shift' => 'required|unique:shifts,kode_shift',
            'nama_shift' => 'required',
            'kategori_jadwal' => 'required|in:non_shift,shift,cuti,non_shift_5_hari',
            'jam_masuk' => 'required',
            'jam_pulang' => 'required',
            'warna' => 'nullable',
            'keterangan' => 'nullable',
        ]);

        // Handle day indicator checkboxes (only relevant for non_shift)
        $days = ['is_senin', 'is_selasa', 'is_rabu', 'is_kamis', 'is_jumat', 'is_sabtu', 'is_minggu'];
        foreach ($days as $day) {
            $validated[$day] = $request->has($day) ? true : false;
        }

        Shift::create($validated);

        return redirect()->route('shift.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(Shift $shift)
    {
        return view('shift.edit', compact('shift'));
    }

    public function update(Request $request, Shift $shift)
    {
        $validated = $request->validate([
            'kode_shift' => 'required|unique:shifts,kode_shift,' . $shift->id,
            'nama_shift' => 'required',
            'kategori_jadwal' => 'required|in:non_shift,shift,cuti,non_shift_5_hari',
            'jam_masuk' => 'required',
            'jam_pulang' => 'required',
            'warna' => 'nullable',
            'keterangan' => 'nullable',
        ]);

        $days = ['is_senin', 'is_selasa', 'is_rabu', 'is_kamis', 'is_jumat', 'is_sabtu', 'is_minggu'];
        foreach ($days as $day) {
            $validated[$day] = $request->has($day) ? true : false;
        }

        $shift->update($validated);

        return redirect()->route('shift.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Shift $shift)
    {
        $shift->delete();
        return redirect()->route('shift.index')->with('success', 'Jadwal berhasil dihapus.');
    }
}
