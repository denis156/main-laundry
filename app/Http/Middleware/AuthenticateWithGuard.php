<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateWithGuard
{
    /**
     * Handle an incoming request.
     * Redirect ke halaman login yang sesuai berdasarkan guard
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        if (empty($guards)) {
            $guards = [null];
        }

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return $next($request);
            }
        }

        // Redirect berdasarkan guard
        $guard = $guards[0];

        return match ($guard) {
            'courier' => redirect()->route('kurir.login'),
            'customer' => redirect()->route('pelanggan.login'),
            default => redirect()->route('filament.admin.auth.login'),
        };
    }
}
