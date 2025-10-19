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
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
