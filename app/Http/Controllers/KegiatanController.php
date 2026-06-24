<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Kegiatan;
use App\Models\Pegawai;
use App\Models\Ruangan;

class KegiatanController extends Controller
{
    public function index()
    {
        $kegiatans = Kegiatan::latest()->paginate(10);
        return view('kegiatan.index', compact('kegiatans'));
    }

    public function create()
    {
        $ruangans = Ruangan::with('pegawai')->orderBy('nama_ruangan')->get();
        return view('kegiatan.create', compact('ruangans'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'tanggal_kegiatan' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'lokasi' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius_meter' => 'required|integer',
            'tipe' => 'required|in:apel,kegiatan',
            'pegawai_ids' => 'required_if:tipe,kegiatan|array',
            'pegawai_ids.*' => 'exists:pegawai,id',
        ]);

        $validated['created_by'] = \Illuminate\Support\Facades\Auth::id();
        $validated['status'] = 'aktif';

        $kegiatan = Kegiatan::create($validated);

        if ($request->tipe === 'kegiatan') {
            $kegiatan->pegawais()->sync($request->pegawai_ids);
        }

        return redirect()->route('kegiatan.index')->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function show(Kegiatan $kegiatan)
    {
        $absensis = $kegiatan->absensiKegiatan()->with('pegawai')->get();
        return view('kegiatan.show', compact('kegiatan', 'absensis'));
    }

    public function edit(Kegiatan $kegiatan)
    {
        $ruangans = Ruangan::with('pegawai')->orderBy('nama_ruangan')->get();
        $selectedPegawaiIds = $kegiatan->pegawais->pluck('id')->toArray();
        return view('kegiatan.edit', compact('kegiatan', 'ruangans', 'selectedPegawaiIds'));
    }

    public function update(\Illuminate\Http\Request $request, Kegiatan $kegiatan)
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'tanggal_kegiatan' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'lokasi' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius_meter' => 'required|integer',
            'status' => 'required|in:aktif,selesai',
            'tipe' => 'required|in:apel,kegiatan',
            'pegawai_ids' => 'required_if:tipe,kegiatan|array',
            'pegawai_ids.*' => 'exists:pegawai,id',
        ]);

        $kegiatan->update($validated);

        if ($request->tipe === 'kegiatan') {
            $kegiatan->pegawais()->sync($request->pegawai_ids);
        } else {
            $kegiatan->pegawais()->sync([]);
        }

        return redirect()->route('kegiatan.index')->with('success', 'Kegiatan berhasil diupdate.');
    }

    public function destroy(Kegiatan $kegiatan)
    {
        abort_unless(auth()->user()->hasAnyRole(['super_admin', 'admin']), 403, 'Akses ditolak.');
        $kegiatan->delete();
        return redirect()->route('kegiatan.index')->with('success', 'Kegiatan berhasil dihapus.');
    }
}
