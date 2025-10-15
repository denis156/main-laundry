<?php

declare(strict_types=1);

namespace App\Filament\Resources\Equipment\Widgets;

use App\Models\Equipment;
use App\Models\EquipmentMaintenance;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewEquipments extends StatsOverviewWidget
{
    protected ?string $heading = 'Statistik Peralatan';

    protected ?string $description = 'Ringkasan status peralatan dan perawatan';

    protected function getStats(): array
    {
        // Total peralatan
        $totalEquipments = Equipment::count();

        // Total nilai investasi peralatan
        $totalInvestmentValue = (float) Equipment::sum('purchase_price');

        // Peralatan yang perlu perawatan (lebih dari 3 bulan sejak last_maintenance_date)
        $needMaintenanceCount = Equipment::where(function ($query) {
            $query->whereNull('last_maintenance_date')
                ->orWhere('last_maintenance_date', '<=', now()->subMonths(3));
        })->count();

        // Total biaya perawatan bulan ini
        $thisMonthMaintenanceCost = (float) EquipmentMaintenance::whereYear('maintenance_date', now()->year)
            ->whereMonth('maintenance_date', now()->month)
            ->sum('cost');

        // Breakdown berdasarkan status
        $operationalCount = Equipment::where('status', 'operational')->count();
        $underMaintenanceCount = Equipment::where('status', 'under_maintenance')->count();
        $damagedCount = Equipment::where('status', 'damaged')->count();

        return [
            Stat::make('Total Peralatan', number_format($totalEquipments, 0, ',', '.') . ' Unit')
                ->description('Semua peralatan terdaftar')
                ->descriptionIcon('solar-server-2-bold-duotone')
                ->color('primary')
                ->chart($this->getEquipmentChartData(6)),

            Stat::make('Nilai Investasi', 'Rp ' . number_format($totalInvestmentValue, 0, ',', '.'))
                ->description('Total nilai pembelian')
                ->descriptionIcon('solar-wallet-money-bold-duotone')
                ->color('success'),

            Stat::make('Perlu Perawatan', number_format($needMaintenanceCount, 0, ',', '.') . ' Unit')
                ->description($needMaintenanceCount > 0 ? 'Perlu perawatan segera!' : 'Semua peralatan terawat')
                ->descriptionIcon($needMaintenanceCount > 0 ? 'solar-danger-bold-duotone' : 'solar-shield-check-bold-duotone')
                ->color($needMaintenanceCount > 0 ? 'warning' : 'success'),
        ];
    }

    /**
     * Ambil data total equipment per bulan untuk chart
     */
    private function getEquipmentChartData(int $months): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Equipment::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $data[] = $count;
        }
        return $data;
    }
}
