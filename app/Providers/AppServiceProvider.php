<?php

namespace App\Providers;

use App\Helper\InvoiceHelper;
use App\Helper\OrderRateLimiterHelper;
use App\Helper\TransactionAreaFilter;
use App\Helper\WilayahHelper;
use App\Models\Customer;
use App\Models\Transaction;
use App\Observers\CustomerObserver;
use App\Observers\TransactionObserver;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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

        // Register model observers
        Customer::observe(CustomerObserver::class);
        Transaction::observe(TransactionObserver::class);
    }
}
