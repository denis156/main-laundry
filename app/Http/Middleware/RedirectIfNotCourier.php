<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotCourier
{
    /**
     * Handle an incoming request.
     * Redirect ke login kurir jika belum authenticated
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('courier')->check()) {
            return redirect()->route('kurir.login');
        }

        return $next($request);
    }
}
