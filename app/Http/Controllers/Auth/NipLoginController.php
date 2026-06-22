<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NipLoginController extends Controller
{
    public function create(): RedirectResponse
    {
        return redirect()->route('login');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nip' => ['required'],
            'password' => ['required'],
        ]);

        $nip = $request->nip;
        $password = $request->password;
        $ip = $request->ip();

        $nipMatch = Auth::attempt(['nip' => $nip, 'password' => $password], $request->boolean('remember'));
        $usernameMatch = !$nipMatch && Auth::attempt(['username' => $nip, 'password' => $password], $request->boolean('remember'));

        if ($nipMatch || $usernameMatch) {
            $request->session()->regenerate();
            $user = Auth::user();

            Log::channel('login')->info('Login NIP berhasil', [
                'nip_input' => $nip,
                'matched_by' => $nipMatch ? 'nip' : 'username',
                'ip' => $ip,
                'user_id' => $user->id,
            ]);

            if (!$user->nip && ctype_digit($user->username)) {
                $user->forceFill(['nip' => $user->username])->save();
            }

            if ($user->isPegawaiBiasa()) {
                return redirect()->intended(route('user.kegiatan.index'));
            }

            return redirect()->intended(route('dashboard'));
        }

        Log::channel('login')->warning('Login NIP gagal', [
            'nip_input' => $nip,
            'ip' => $ip,
        ]);

        return back()->withErrors([
            'nip' => 'Kredensial NIP tidak cocok dengan catatan kami.',
        ])->onlyInput('nip');
    }
}
