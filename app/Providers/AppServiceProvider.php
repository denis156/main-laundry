<?php

namespace App\Providers;

use App\Models\Pos;
use App\Models\Resort;
use App\Models\Customer;
use App\Models\Transaction;
use App\Observers\PosObserver;
use App\Observers\ResortObserver;
use App\Observers\CustomerObserver;
use App\Models\EquipmentMaintenance;
use App\Models\MaterialStockHistory;
use App\Observers\TransactionObserver;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use App\Observers\EquipmentMaintenanceObserver;
use App\Observers\MaterialStockHistoryObserver;
use App\Helper\InvoiceHelper;
use App\Helper\OrderRateLimiterHelper;
use App\Helper\WilayahHelper;
use App\Helper\TransactionAreaFilter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register WilayahHelper as singleton
        $this->app->singleton(WilayahHelper::class, function () {
            return new WilayahHelper();
        });

        // Register InvoiceHelper as singleton
        $this->app->singleton(InvoiceHelper::class, function () {
            return new InvoiceHelper();
        });

        // Register OrderRateLimiterHelper as singleton
        $this->app->singleton(OrderRateLimiterHelper::class, function () {
            return new OrderRateLimiterHelper();
        });

        // Register TransactionAreaFilter as singleton
        $this->app->singleton(TransactionAreaFilter::class, function () {
            return new TransactionAreaFilter();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS URL generation when behind reverse proxy (Cloudflared)
        // Check if request is coming through HTTPS proxy
        if (request()->header('X-Forwarded-Proto') === 'https' || request()->header('CF-Visitor')) {
            URL::forceScheme('https');
        }

        // Also force HTTPS in production environment
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Pos::observe(PosObserver::class);
        Resort::observe(ResortObserver::class);
        Customer::observe(CustomerObserver::class);
        Transaction::observe(TransactionObserver::class);
        MaterialStockHistory::observe(MaterialStockHistoryObserver::class);
        EquipmentMaintenance::observe(EquipmentMaintenanceObserver::class);
    }
}
