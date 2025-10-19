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
use Illuminate\Support\ServiceProvider;
use App\Observers\EquipmentMaintenanceObserver;
use App\Observers\MaterialStockHistoryObserver;
use EragLaravelPwa\EragLaravelPwaServiceProvider;

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
        Customer::observe(CustomerObserver::class);
        Pos::observe(PosObserver::class);
        Resort::observe(ResortObserver::class);
        MaterialStockHistory::observe(MaterialStockHistoryObserver::class);
        EquipmentMaintenance::observe(EquipmentMaintenanceObserver::class);
        Transaction::observe(TransactionObserver::class);

        EragLaravelPwaServiceProvider::class;
    }
}
