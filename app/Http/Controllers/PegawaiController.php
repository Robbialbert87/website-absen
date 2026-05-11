<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Ruangan;
use App\Models\Shift;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    public function index(Request $request, \App\Filters\PegawaiFilter $filters)
    {
        $user = auth()->user();
        $query = Pegawai::with(['ruangan', 'jadwal'])->filter($filters);

        // Filter for non-admin roles
        if (! $user->hasAnyRole(['super_admin', 'admin'])) {
            $roomIds = Ruangan::where('kepala_pegawai_id', $user->pegawai_id)
                ->orWhere('id', $user->ruangan_id)
                ->pluck('id');

            $query->whereIn('ruangan_id', $roomIds);
        }

        if ($request->export === 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PegawaiExport($query->get()), 'pegawai.xlsx');
        }

        if ($request->export === 'pdf') {
            $pegawai = $query->get();
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pegawai.pdf', compact('pegawai'))->setPaper('a4', 'landscape');
            return $pdf->download('pegawai.pdf');
        }

        $perPage = $request->get('per_page', 10);
        $pegawai = $query->paginate($perPage)->withQueryString();

        if ($request->ajax()) {
            return view('pegawai._table', compact('pegawai'))->render();
        }

        $ruangans = Ruangan::all();

        return view('pegawai.index', compact('pegawai', 'ruangans'));
    }

    public function create()
    {
        abort_unless(auth()->user()->hasAnyRole(['super_admin', 'admin']), 403, 'Akses ditolak.');
        $shifts = Shift::all();
        return view('pegawai.create', compact('shifts'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasAnyRole(['super_admin', 'admin']), 403, 'Akses ditolak.');
        $validated = $request->validate([
            'nip' => 'required|unique:pegawai,nip',
            'nama' => 'required',
            'ruangan_id' => 'required|exists:ruangan,id',
            'jabatan' => 'required',
            'kategori_kerja' => 'required|in:non_shift,shift',
            'shift_id' => 'nullable|exists:shifts,id',
            'status_aktif' => 'required|boolean',
        ]);

        Pegawai::create($validated);

        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil ditambahkan.');
    }

    public function edit(Pegawai $pegawai)
    {
        abort_unless(auth()->user()->hasAnyRole(['super_admin', 'admin']), 403, 'Akses ditolak.');
        $shifts = Shift::all();
        return view('pegawai.edit', compact('pegawai', 'shifts'));
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        abort_unless(auth()->user()->hasAnyRole(['super_admin', 'admin']), 403, 'Akses ditolak.');
        $validated = $request->validate([
            'nip' => 'required|unique:pegawai,nip,'.$pegawai->id,
            'nama' => 'required',
            'ruangan_id' => 'required|exists:ruangan,id',
            'jabatan' => 'required',
            'kategori_kerja' => 'required|in:non_shift,shift',
            'shift_id' => 'nullable|exists:shifts,id',
            'status_aktif' => 'required|boolean',
        ]);

        $pegawai->update($validated);

        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil diperbarui.');
    }

    public function destroy(Pegawai $pegawai)
    {
        abort_unless(auth()->user()->hasAnyRole(['super_admin', 'admin']), 403, 'Akses ditolak.');
        $pegawai->delete();

        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil dihapus.');
    }
}
