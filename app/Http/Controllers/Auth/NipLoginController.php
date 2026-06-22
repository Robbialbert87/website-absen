<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $success = Auth::attempt(['nip' => $nip, 'password' => $password])
                || Auth::attempt(['username' => $nip, 'password' => $password]);

        if ($success) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (!$user->nip && ctype_digit($user->username)) {
                $user->forceFill(['nip' => $user->username])->save();
            }

            if ($user->isPegawaiBiasa()) {
                return redirect()->intended(route('user.kegiatan.index'));
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'nip' => 'Kredensial NIP tidak cocok dengan catatan kami.',
        ])->onlyInput('nip');
    }
}
