<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Kegiatan;
use App\Models\AbsensiKegiatan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserKegiatanController extends Controller
{
    public function index()
    {
        $pegawai_id = Auth::user()->pegawai_id;

        $kegiatans = Kegiatan::where('status', 'aktif')
            ->where(function ($q) use ($pegawai_id) {
                $q->where('tipe', 'apel');
                if ($pegawai_id) {
                    $q->orWhereHas('pegawais', fn($q2) => $q2->where('pegawai_id', $pegawai_id));
                }
            })
            ->latest()
            ->get();

        return view('user_kegiatan.index', compact('kegiatans'));
    }

    public function absenForm($id)
    {
        $kegiatan = Kegiatan::findOrFail($id);

        $pegawai_id = Auth::user()->pegawai_id;

        if ($kegiatan->tipe === 'kegiatan' && $pegawai_id) {
            $terdaftar = $kegiatan->pegawais()->where('pegawai_id', $pegawai_id)->exists();
            if (!$terdaftar) {
                abort(403, 'Anda tidak terdaftar sebagai peserta kegiatan ini.');
            }
        }

        $sudahAbsen = AbsensiKegiatan::where('kegiatan_id', $kegiatan->id)
            ->where('pegawai_id', $pegawai_id)
            ->first();

        return view('user_kegiatan.absen', compact('kegiatan', 'sudahAbsen'));
    }

    public function absen(\Illuminate\Http\Request $request, $id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        
        $request->validate([
            'foto' => 'required|string|max:5242880',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $pegawai_id = Auth::user()->pegawai_id;
        if (!$pegawai_id) {
            return response()->json(['success' => false, 'message' => 'Akun Anda tidak terkait dengan data pegawai.'], 403);
        }

        $sudahAbsen = AbsensiKegiatan::where('kegiatan_id', $kegiatan->id)
            ->where('pegawai_id', $pegawai_id)
            ->first();

        if ($sudahAbsen) {
            return response()->json(['success' => false, 'message' => 'Anda sudah melakukan absensi.'], 400);
        }

        // Hitung jarak (Haversine formula)
        $latFrom = deg2rad($kegiatan->latitude);
        $lonFrom = deg2rad($kegiatan->longitude);
        $latTo = deg2rad($request->latitude);
        $lonTo = deg2rad($request->longitude);
        
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        $distance = $angle * 6371000;

        $toleransi = 25;
        if ($distance > $kegiatan->radius_meter + $toleransi) {
            return response()->json(['success' => false, 'message' => 'Anda berada di luar radius (' . round($distance) . ' meter dari lokasi). Maksimal ' . $kegiatan->radius_meter . ' + ' . $toleransi . ' meter.'], 400);
        }

        $status = 'hadir';
        $now = now()->format('H:i:s');
        if ($now > $kegiatan->jam_mulai) {
            $status = 'terlambat';
        }

        // Validate base64 image format and type
        $image_parts = explode(";base64,", $request->foto);
        if (count($image_parts) != 2) {
            return response()->json(['success' => false, 'message' => 'Format gambar tidak valid.'], 422);
        }

        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1] ?? 'png';
        
        // Validate image type
        $allowed_types = ['jpeg', 'jpg', 'png', 'gif', 'webp'];
        if (!in_array(strtolower($image_type), $allowed_types)) {
            return response()->json(['success' => false, 'message' => 'Tipe gambar harus: ' . implode(', ', $allowed_types)], 422);
        }

        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array(strtolower($image_type), $allowed)) {
            return response()->json(['success' => false, 'message' => 'Tipe file foto tidak diizinkan.'], 422);
        }

        $image_base64 = base64_decode($image_parts[1]);
        $fileName = 'absensi/' . uniqid() . '.' . $image_type;
        
        Storage::disk('public')->put($fileName, $image_base64);

        AbsensiKegiatan::create([
            'kegiatan_id' => $kegiatan->id,
            'pegawai_id' => $pegawai_id,
            'waktu_absen' => now(),
            'foto' => $fileName,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'status' => $status
        ]);

        return response()->json(['success' => true, 'message' => 'Absensi berhasil disimpan!']);
    }
}
