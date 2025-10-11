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
        $memberCustomers = Customer::whereHas('member')->count();
        $nonMemberCustomers = Customer::whereDoesntHave('member')->count();

        // Ambil data 6 bulan terakhir
        $totalCustomersChart = $this->getCustomersChartData(6);
        $memberCustomersChart = $this->getMemberCustomersChartData(6);
        $nonMemberCustomersChart = $this->getNonMemberCustomersChartData(6);

        return [
            Stat::make('Total Pelanggan', $totalCustomers . ' Pelanggan')
                ->description('Semua Pelanggan')
                ->descriptionIcon('solar-users-group-rounded-bold-duotone')
                ->color('primary')
                ->chart($totalCustomersChart),
            Stat::make('Pelanggan Member', $memberCustomers . ' Pelanggan')
                ->description('Sudah Member')
                ->descriptionIcon('solar-medal-star-bold-duotone')
                ->color('success')
                ->chart($memberCustomersChart),
            Stat::make('Pelanggan Reguler', $nonMemberCustomers . ' Pelanggan')
                ->description('Belum Member')
                ->descriptionIcon('solar-user-bold-duotone')
                ->color('warning')
                ->chart($nonMemberCustomersChart),
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
     * Ambil data member customers per bulan untuk 6 bulan terakhir
     */
    private function getMemberCustomersChartData(int $months): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Customer::whereHas('member', function ($query) use ($date) {
                $query->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month);
            })->count();
            $data[] = $count;
        }
        return $data;
    }

    /**
     * Ambil data non-member customers per bulan untuk 6 bulan terakhir
     */
    private function getNonMemberCustomersChartData(int $months): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            // Hitung customer yang dibuat di bulan tersebut DAN belum punya member
            $count = Customer::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->whereDoesntHave('member')
                ->count();
            $data[] = $count;
        }
        return $data;
    }
}
