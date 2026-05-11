<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use App\Models\User;
use App\Models\Pegawai;
use Illuminate\Http\Request;

class RuanganController extends Controller
{
    public function index(Request $request, \App\Filters\RuanganFilter $filters)
    {
        $user = auth()->user();

        $query = Ruangan::with('kepalaPegawai')->filter($filters);

        // Filter for non-admin roles
        if (!$user->hasAnyRole(['super_admin', 'admin'])) {
            $query->where(function($q) use ($user) {
                $q->where('kepala_pegawai_id', $user->pegawai_id)
                  ->orWhere('id', $user->ruangan_id);
            });
        }

        if ($request->export === 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\RuanganExport($query->get()), 'ruangan.xlsx');
        }

        if ($request->export === 'pdf') {
            $ruangan = $query->get();
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('ruangan.pdf', compact('ruangan'))->setPaper('a4', 'landscape');
            return $pdf->download('ruangan.pdf');
        }

        $perPage = $request->get('per_page', 10);
        $ruangan = $query->paginate($perPage)->withQueryString();

        if ($request->ajax()) {
            return view('ruangan._table', compact('ruangan'))->render();
        }

        $kepalaRuangan = Pegawai::whereIn('id', Ruangan::whereNotNull('kepala_pegawai_id')->pluck('kepala_pegawai_id'))->get();

        return view('ruangan.index', compact('ruangan', 'kepalaRuangan'));
    }

    public function create()
    {
        return view('ruangan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_ruangan' => 'required|unique:ruangan,kode_ruangan',
            'nama_ruangan' => 'required',
            'keterangan' => 'nullable',
            'kepala_pegawai_id' => 'nullable|exists:pegawai,id',
        ]);

        Ruangan::create($validated);

        return redirect()->route('ruangan.index')->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function edit(Ruangan $ruangan)
    {
        $ruangan->load('kepalaPegawai');
        return view('ruangan.edit', compact('ruangan'));
    }

    public function update(Request $request, Ruangan $ruangan)
    {
        $validated = $request->validate([
            'kode_ruangan' => 'required|unique:ruangan,kode_ruangan,' . $ruangan->id,
            'nama_ruangan' => 'required',
            'keterangan' => 'nullable',
            'kepala_pegawai_id' => 'nullable|exists:pegawai,id',
        ]);

        $ruangan->update($validated);

        return redirect()->route('ruangan.index')->with('success', 'Ruangan berhasil diperbarui.');
    }

    public function destroy(Ruangan $ruangan)
    {
        $ruangan->delete();
        return redirect()->route('ruangan.index')->with('success', 'Ruangan berhasil dihapus.');
    }

    public function search(Request $request)
    {
        $search = $request->get('q');
        
        $ruangan = Ruangan::where('nama_ruangan', 'like', "%{$search}%")
            ->orWhere('kode_ruangan', 'like', "%{$search}%")
            ->limit(20)
            ->get();

        $results = $ruangan->map(function($r) {
            return [
                'id' => $r->id,
                'text' => $r->nama_ruangan . " (" . $r->kode_ruangan . ")"
            ];
        });

        return response()->json($results);
    }

    public function searchKepala(Request $request)
    {
        $search = $request->get('q');
        
        $pegawai = Pegawai::where('nama', 'like', "%{$search}%")
            ->orWhere('nip', 'like', "%{$search}%")
            ->limit(20)
            ->get();

        $results = $pegawai->map(function($p) {
            return [
                'id' => $p->id,
                'text' => $p->nama . " (" . $p->nip . ")"
            ];
        });

        return response()->json($results);
    }
}
