<?php

declare(strict_types=1);

namespace App\Filament\Resources\Members\Widgets;

use App\Models\Member;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewMembers extends StatsOverviewWidget
{
    protected ?string $heading = 'Statistik Member';

    protected ?string $description = 'Ringkasan data keanggotaan dan tren 6 bulan terakhir.';

    protected function getStats(): array
    {
        $totalMembers = Member::count();
        $activeMembers = Member::where('is_active', true)->count();
        $inactiveMembers = Member::where('is_active', false)->count();

        // Ambil data 6 bulan terakhir
        $totalMembersChart = $this->getMembersChartData(6);
        $activeMembersChart = $this->getActiveMembersChartData(6);
        $inactiveMembersChart = $this->getInactiveMembersChartData(6);

        return [
            Stat::make('Total Member', $totalMembers . ' Member')
                ->description('Semua Member')
                ->descriptionIcon('solar-medal-star-bold-duotone')
                ->color('primary')
                ->chart($totalMembersChart),
            Stat::make('Member Aktif', $activeMembers . ' Member')
                ->description('Sedang Aktif')
                ->descriptionIcon('solar-check-circle-bold-duotone')
                ->color('success')
                ->chart($activeMembersChart),
            Stat::make('Member Nonaktif', $inactiveMembers . ' Member')
                ->description('Tidak Aktif')
                ->descriptionIcon('solar-close-circle-bold-duotone')
                ->color('danger')
                ->chart($inactiveMembersChart),
        ];
    }

    /**
     * Ambil data total members per bulan untuk 6 bulan terakhir
     */
    private function getMembersChartData(int $months): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Member::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    /**
     * Ambil data active members per bulan untuk 6 bulan terakhir
     */
    private function getActiveMembersChartData(int $months): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Member::where('is_active', true)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    /**
     * Ambil data inactive members per bulan untuk 6 bulan terakhir
     */
    private function getInactiveMembersChartData(int $months): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Member::where('is_active', false)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $data[] = $count;
        }
        return $data;
    }
}
