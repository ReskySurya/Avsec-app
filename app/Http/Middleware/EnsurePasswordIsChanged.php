<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsurePasswordIsChanged
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Periksa apakah user login DAN harus ganti password
        if (Auth::check() && Auth::user()->must_change_password) {

            // Jika user mencoba mengakses halaman selain halaman ganti password, alihkan!
            if (! $request->routeIs('password.change')) {
                return redirect()->route('password.change')
                       ->with('warning', 'Untuk keamanan, Anda harus mengganti password default Anda terlebih dahulu.');
            }
        }

        return $next($request);
    }
}
