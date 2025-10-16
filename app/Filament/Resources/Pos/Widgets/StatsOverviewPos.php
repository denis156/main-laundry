<?php

declare(strict_types=1);

namespace App\Filament\Resources\Pos\Widgets;

use App\Models\Pos;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewPos extends StatsOverviewWidget
{
    protected ?string $heading = 'Statistik Pos';

    protected ?string $description = 'Ringkasan data pos dan tren 6 bulan terakhir.';

    protected function getStats(): array
    {
        // Total pos
        $totalPos = Pos::count();

        // Pos aktif
        $activePos = Pos::where('is_active', true)->count();

        // Pos tidak aktif
        $inactivePos = Pos::where('is_active', false)->count();

        // Pos yang terkait dengan resort
        $posWithResort = Pos::whereNotNull('resort_id')->count();

        // Pos mandiri (tidak terkait resort)
        $standAlonePos = Pos::whereNull('resort_id')->count();

        // Pos yang memiliki area layanan
        $posWithArea = Pos::whereNotNull('area')
            ->where('area', '!=', '')
            ->count();

        // Ambil data 6 bulan terakhir
        $totalPosChart = $this->getPosChartData(6);
        $activePosChart = $this->getActivePosChartData(6);

        return [
            Stat::make('Total Pos', $totalPos . ' Pos')
                ->description($posWithResort . ' terkait resort, ' . $standAlonePos . ' mandiri')
                ->descriptionIcon('solar-map-point-wave-bold-duotone')
                ->color('primary')
                ->chart($totalPosChart),
            Stat::make('Pos Aktif', $activePos . ' Pos')
                ->description($inactivePos . ' pos tidak aktif')
                ->descriptionIcon('solar-check-circle-bold-duotone')
                ->color('success')
                ->chart($activePosChart),
            Stat::make('Pos dengan Area', $posWithArea . ' Pos')
                ->description('Pos yang memiliki area layanan')
                ->descriptionIcon('solar-map-bold-duotone')
                ->color('warning'),
        ];
    }

    /**
     * Ambil data total pos per bulan untuk 6 bulan terakhir
     */
    private function getPosChartData(int $months): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Pos::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    /**
     * Ambil data pos aktif per bulan untuk 6 bulan terakhir
     */
    private function getActivePosChartData(int $months): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Pos::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->where('is_active', true)
                ->count();
            $data[] = $count;
        }
        return $data;
    }
}
