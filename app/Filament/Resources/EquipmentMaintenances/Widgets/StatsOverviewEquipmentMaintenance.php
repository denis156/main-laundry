<?php

declare(strict_types=1);

namespace App\Filament\Resources\EquipmentMaintenances\Widgets;

use App\Models\EquipmentMaintenance;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewEquipmentMaintenance extends StatsOverviewWidget
{
    protected ?string $heading = 'Statistik Perawatan Peralatan';

    protected ?string $description = 'Ringkasan riwayat perawatan dan biaya';

    protected function getStats(): array
    {
        // Total perawatan
        $totalMaintenances = EquipmentMaintenance::count();

        // Total biaya perawatan semua tahun
        $totalAllCost = (float) EquipmentMaintenance::sum('cost');

        // Total biaya perawatan tahun ini
        $thisYearCost = (float) EquipmentMaintenance::whereYear('maintenance_date', now()->year)
            ->sum('cost');

        // Biaya perawatan bulan ini
        $thisMonthCost = (float) EquipmentMaintenance::whereYear('maintenance_date', now()->year)
            ->whereMonth('maintenance_date', now()->month)
            ->sum('cost');

        // Perawatan bulan ini
        $thisMonthMaintenances = EquipmentMaintenance::whereYear('maintenance_date', now()->year)
            ->whereMonth('maintenance_date', now()->month)
            ->count();

        return [
            Stat::make('Total Perawatan', number_format($totalMaintenances, 0, ',', '.') . ' Kali')
                ->description('Total riwayat perawatan')
                ->descriptionIcon('solar-clipboard-list-bold-duotone')
                ->color('primary')
                ->chart($this->getMaintenanceChartData(6)),

            Stat::make('Total Biaya', 'Rp ' . number_format($totalAllCost, 0, ',', '.'))
                ->description('Tahun ini: Rp ' . number_format($thisYearCost, 0, ',', '.'))
                ->descriptionIcon('solar-wallet-money-bold-duotone')
                ->color('success'),

            Stat::make('Perawatan Bulan Ini', number_format($thisMonthMaintenances, 0, ',', '.') . ' Kali')
                ->description('Biaya bulan ini: Rp ' . number_format($thisMonthCost, 0, ',', '.'))
                ->descriptionIcon('solar-calendar-bold-duotone')
                ->color('info'),
        ];
    }

    /**
     * Ambil data total maintenance per bulan untuk chart
     */
    private function getMaintenanceChartData(int $months): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = EquipmentMaintenance::whereYear('maintenance_date', $date->year)
                ->whereMonth('maintenance_date', $date->month)
                ->count();
            $data[] = $count;
        }
        return $data;
    }
}
