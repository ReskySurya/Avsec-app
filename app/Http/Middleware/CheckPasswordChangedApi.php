<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class CheckPasswordChangedApi
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->must_change_password) {
            // Allow access to change password endpoint
            if ($request->route()->getName() === 'api.password.change' || $request->is('api/v1/auth/change-password') || $request->is('api/v1/auth/logout')) {
                return $next($request);
            }

            return $this->errorResponse('You must change your password before accessing the API.', 403, ['must_change_password' => true]);
        }

        return $next($request);
    }
}
