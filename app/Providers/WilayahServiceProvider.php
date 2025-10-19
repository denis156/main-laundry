<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\WilayahService;
use Illuminate\Support\ServiceProvider;

class WilayahServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register WilayahService as singleton
        $this->app->singleton(WilayahService::class, function () {
            return new WilayahService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
