<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Paksa skema HTTPS jika diakses melalui reverse proxy HTTPS (seperti Ngrok)
        if (
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
            (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
            request()->header('x-forwarded-proto') === 'https' ||
            str_starts_with(config('app.url'), 'https://')
        ) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}