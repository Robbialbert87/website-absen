<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'nip' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $nip = $request->nip;
        $password = $request->password;

        // Try authenticate via nip first, then fallback to username
        $success = Auth::attempt(['nip' => $nip, 'password' => $password])
                || Auth::attempt(['username' => $nip, 'password' => $password]);

        if (!$success) {
            Log::channel('login')->warning('API login gagal', [
                'nip_input' => $nip,
                'ip' => $request->ip(),
            ]);

            throw ValidationException::withMessages([
                'nip' => 'Kredensial tidak cocok',
            ]);
        }

        $user = Auth::user();

        Log::channel('login')->info('API login berhasil', [
            'nip_input' => $nip,
            'ip' => $request->ip(),
            'user_id' => $user->id,
        ]);

        return response()->json([
            'token' => $user->createToken('api-token')->plainTextToken,
            'user' => [
                'id' => $user->id,
                'nip' => $user->nip,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames(),
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        Log::channel('login')->info('API logout berhasil', [
            'user_id' => $request->user()->id,
            'ip' => $request->ip(),
        ]);

        return response()->json(['message' => 'Logout berhasil']);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'nip' => $user->nip,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->getRoleNames(),
        ]);
    }
}
