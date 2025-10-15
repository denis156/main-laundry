<?php

namespace App\Providers;

use App\Models\MaterialStockHistory;
use App\Observers\MaterialStockHistoryObserver;
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
        MaterialStockHistory::observe(MaterialStockHistoryObserver::class);
    }
}
