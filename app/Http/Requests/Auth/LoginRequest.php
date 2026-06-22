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
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $ip = $this->ip();
        $email = $this->email;

        $success = Auth::attempt(['email' => $email, 'password' => $this->password], $this->boolean('remember'));

        if ($success) {
            RateLimiter::clear($this->throttleKey());
            Log::channel('login')->info('Login email berhasil', [
                'email' => $email,
                'ip' => $ip,
                'user_id' => Auth::id(),
            ]);
        } else {
            RateLimiter::hit($this->throttleKey());
            Log::channel('login')->warning('Login email gagal', [
                'email' => $email,
                'ip' => $ip,
            ]);

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        Log::channel('login')->warning('Login email terkunci (rate limit)', [
            'email' => $this->email,
            'ip' => $this->ip(),
            'seconds' => RateLimiter::availableIn($this->throttleKey()),
        ]);

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate($this->string('email').'|'.$this->ip());
    }
}
