<?php

declare(strict_types=1);

namespace App\Filament\Resources\Resorts\Widgets;

use App\Models\Resort;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewResorts extends StatsOverviewWidget
{
    protected ?string $heading = 'Statistik Resort';

    protected ?string $description = 'Ringkasan data resort dan tren 6 bulan terakhir.';

    protected function getStats(): array
    {
        // Total resort
        $totalResorts = Resort::count();

        // Resort aktif
        $activeResorts = Resort::where('is_active', true)->count();

        // Resort tidak aktif
        $inactiveResorts = Resort::where('is_active', false)->count();

        // Hitung total pos yang terkait dengan resort
        $totalPosWithResort = \App\Models\Pos::whereNotNull('resort_id')->count();

        // Ambil data 6 bulan terakhir
        $totalResortsChart = $this->getResortsChartData(6);
        $activeResortsChart = $this->getActiveResortsChartData(6);

        return [
            Stat::make('Total Resort', $totalResorts . ' Resort')
                ->description('Resort Induk')
                ->descriptionIcon('solar-buildings-2-bold-duotone')
                ->color('primary')
                ->chart($totalResortsChart),
            Stat::make('Resort Aktif', $activeResorts . ' Resort')
                ->description($inactiveResorts . ' resort tidak aktif')
                ->descriptionIcon('solar-check-circle-bold-duotone')
                ->color('success')
                ->chart($activeResortsChart),
            Stat::make('Total Pos Terkait', $totalPosWithResort . ' Pos')
                ->description('Pos yang terkait dengan resort')
                ->descriptionIcon('solar-map-point-bold-duotone')
                ->color('warning'),
        ];
    }

    /**
     * Ambil data total resort per bulan untuk 6 bulan terakhir
     */
    private function getResortsChartData(int $months): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Resort::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    /**
     * Ambil data resort aktif per bulan untuk 6 bulan terakhir
     */
    private function getActiveResortsChartData(int $months): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Resort::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->where('is_active', true)
                ->count();
            $data[] = $count;
        }
        return $data;
    }
}
