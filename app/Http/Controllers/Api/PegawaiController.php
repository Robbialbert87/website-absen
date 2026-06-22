<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $expectedKey = env('API_KEY_PEGAWAI');
        $sentKey = $request->header('X-API-Key');

        if ($sentKey && $sentKey !== $expectedKey) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $pegawai = Pegawai::with('user.roles')
            ->where('status_aktif', true)
            ->paginate(100);

        $data = $pegawai->map(function (Pegawai $p) {
            $roleNames = $p->user?->getRoleNames()?->toArray() ?? [];

            return [
                'nip' => $p->nip,
                'nama' => $p->nama,
                'jabatan' => $p->jabatan,
                'role_info' => [
                    'roles' => $roleNames,
                ],
            ];
        });

        return response()->json([
            'data' => $data,
            'pagination' => [
                'last_page' => $pegawai->lastPage(),
            ],
        ]);
    }
}
