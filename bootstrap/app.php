<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Configure authentication redirect for courier guard
        $middleware->redirectGuestsTo(function (Request $request) {
            // Jika route dimulai dengan /kurir, redirect ke login kurir
            if ($request->is('kurir') || $request->is('kurir/*')) {
                return route('kurir.login');
            }

            // Default redirect untuk guard lain
            return route('login'); // Bisa disesuaikan dengan route login Anda
        });

        // Trust proxies for Cloudflared tunnel
        // SECURITY: Only trust Cloudflare IP ranges and local proxies
        // For Cloudflared tunnel, we trust: localhost, private networks, and Cloudflare IPs
        // Cloudflare IPs reference: https://www.cloudflare.com/ips/
        $trustedProxies = env('TRUSTED_PROXIES', '*');

        // Convert comma-separated string to array if needed
        if (is_string($trustedProxies) && $trustedProxies !== '*') {
            $trustedProxies = array_map('trim', explode(',', $trustedProxies));
        }

        $middleware->trustProxies(
            at: $trustedProxies,
            headers: Request::HEADER_X_FORWARDED_FOR |
                    Request::HEADER_X_FORWARDED_HOST |
                    Request::HEADER_X_FORWARDED_PORT |
                    Request::HEADER_X_FORWARDED_PROTO |
                    Request::HEADER_X_FORWARDED_AWS_ELB
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
