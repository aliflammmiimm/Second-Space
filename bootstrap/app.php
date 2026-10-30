<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Percayai reverse proxy seperti Ngrok / Cloudflare agar mendeteksi protokol HTTPS
        $middleware->trustProxies(at: '*');

        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('admin*')) {
                return route('admin.login');
            }
            return route('login');
        });

        $middleware->validateCsrfTokens(except: [
            'midtrans/notification',
            'midtrans/*',
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'seller' => \App\Http\Middleware\EnsureApprovedSeller::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();