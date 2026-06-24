<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nip' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $ip = $this->ip();
        $nip = $this->nip;

        $success = Auth::attempt(['nip' => $nip, 'password' => $this->password], $this->boolean('remember'))
                || Auth::attempt(['username' => $nip, 'password' => $this->password], $this->boolean('remember'));

        if ($success) {
            RateLimiter::clear($this->throttleKey());
            Log::channel('login')->info('Login NIP berhasil', [
                'nip' => $nip,
                'ip' => $ip,
                'user_id' => Auth::id(),
            ]);
        } else {
            RateLimiter::hit($this->throttleKey());
            Log::channel('login')->warning('Login NIP gagal', [
                'nip' => $nip,
                'ip' => $ip,
            ]);

            throw ValidationException::withMessages([
                'nip' => trans('auth.failed'),
            ]);
        }
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        Log::channel('login')->warning('Login NIP terkunci (rate limit)', [
            'nip' => $this->nip,
            'ip' => $this->ip(),
            'seconds' => RateLimiter::availableIn($this->throttleKey()),
        ]);

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'nip' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate($this->string('nip').'|'.$this->ip());
    }
}
