<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceChangePassword
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (\Illuminate\Support\Facades\Auth::check()) {
            $user = \Illuminate\Support\Facades\Auth::user();
            $nip = $user->nip;
            if ($nip && \Illuminate\Support\Facades\Hash::check($nip, $user->password) && !$request->routeIs('password.change') && !$request->routeIs('password.change.update') && !$request->routeIs('logout')) {
                return redirect()->route('password.change')->with('warning', 'Anda diwajibkan untuk mengganti password default Anda demi keamanan.');
            }
        }
        return $next($request);
    }
}
