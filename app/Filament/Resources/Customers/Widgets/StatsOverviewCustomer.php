<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers\Widgets;

use App\Models\Customer;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewCustomer extends StatsOverviewWidget
{
    protected ?string $heading = 'Statistik Pelanggan';

    protected ?string $description = 'Ringkasan data pelanggan dan tren 6 bulan terakhir.';

    protected function getStats(): array
    {
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::has('transactions')->count();
        $inactiveCustomers = Customer::doesntHave('transactions')->count();

        // Ambil data 6 bulan terakhir
        $totalCustomersChart = $this->getCustomersChartData(6);
        $activeCustomersChart = $this->getActiveCustomersChartData(6);

        return [
            Stat::make('Total Pelanggan', $totalCustomers . ' Pelanggan')
                ->description('Semua Pelanggan')
                ->descriptionIcon('solar-users-group-rounded-bold-duotone')
                ->color('primary')
                ->chart($totalCustomersChart),
            Stat::make('Pelanggan Aktif', $activeCustomers . ' Pelanggan')
                ->description('Pernah Transaksi')
                ->descriptionIcon('solar-user-check-rounded-bold-duotone')
                ->color('success')
                ->chart($activeCustomersChart),
            Stat::make('Pelanggan Baru', $inactiveCustomers . ' Pelanggan')
                ->description('Belum Transaksi')
                ->descriptionIcon('solar-user-bold-duotone')
                ->color('warning'),
        ];
    }

    /**
     * Ambil data total customers per bulan untuk 6 bulan terakhir
     */
    private function getCustomersChartData(int $months): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Customer::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    /**
     * Ambil data active customers per bulan untuk 6 bulan terakhir
     */
    private function getActiveCustomersChartData(int $months): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Customer::whereYear('created_at', '<=', $date->year)
                ->whereMonth('created_at', '<=', $date->month)
                ->has('transactions')
                ->count();
            $data[] = $count;
        }
        return $data;
    }
}
