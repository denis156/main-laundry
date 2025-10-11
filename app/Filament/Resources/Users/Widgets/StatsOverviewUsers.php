<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewUsers extends StatsOverviewWidget
{
    protected ?string $heading = 'Statistik Pengguna';

    protected ?string $description = 'Ringkasan data pengguna dan tren 6 bulan terakhir.';

    protected function getStats(): array
    {
        $totalUsers = User::count();
        $deletedUsers = User::onlyTrashed()->count();
        $regularUsers = User::where('super_admin', false)->count();
        $superAdmins = User::where('super_admin', true)->count();

        // Ambil data 6 bulan terakhir
        $totalUsersChart = $this->getUsersChartData(6);
        $regularUsersChart = $this->getRegularUsersChartData(6);
        $superAdminsChart = $this->getSuperAdminsChartData(6);

        return [
            Stat::make('Total Pengguna', $totalUsers . ' Pengguna')
                ->description('Semua Pengguna')
                ->descriptionIcon('solar-users-group-two-rounded-bold-duotone')
                ->color('primary')
                ->chart($totalUsersChart),
            Stat::make('Karyawan', $regularUsers . ' Pengguna')
                ->description('Pengguna Biasa')
                ->descriptionIcon('solar-user-id-bold-duotone')
                ->color('warning')
                ->chart($regularUsersChart),
            Stat::make('Super Admin', $superAdmins . ' Pengguna')
                ->description('Akses Penuh')
                ->descriptionIcon('solar-shield-user-bold-duotone')
                ->color('success')
                ->chart($superAdminsChart),
        ];
    }

    /**
     * Ambil data total users per bulan untuk 6 bulan terakhir
     */
    private function getUsersChartData(int $months): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    /**
     * Ambil data deleted users per bulan untuk 6 bulan terakhir
     */
    private function getDeletedUsersChartData(int $months): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = User::onlyTrashed()
                ->whereYear('deleted_at', $date->year)
                ->whereMonth('deleted_at', $date->month)
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    /**
     * Ambil data regular users per bulan untuk 6 bulan terakhir
     */
    private function getRegularUsersChartData(int $months): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = User::where('super_admin', false)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    /**
     * Ambil data super admins per bulan untuk 6 bulan terakhir
     */
    private function getSuperAdminsChartData(int $months): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = User::where('super_admin', true)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $data[] = $count;
        }
        return $data;
    }
}
