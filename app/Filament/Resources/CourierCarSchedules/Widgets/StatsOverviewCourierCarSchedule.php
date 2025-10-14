<?php

declare(strict_types=1);

namespace App\Filament\Resources\CourierCarSchedules\Widgets;

use App\Models\CourierCarSchedule;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewCourierCarSchedule extends StatsOverviewWidget
{
    protected ?string $heading = 'Statistik Jadwal Kurir Mobil';

    protected ?string $description = 'Ringkasan jadwal kurir mobil berdasarkan status dan tren 6 bulan terakhir.';

    protected function getStats(): array
    {
        // Total jadwal
        $totalSchedules = CourierCarSchedule::count();

        // Jadwal berdasarkan status
        $scheduledCount = CourierCarSchedule::where('status', 'scheduled')->count();
        $inProgressCount = CourierCarSchedule::where('status', 'in_progress')->count();
        $completedCount = CourierCarSchedule::where('status', 'completed')->count();
        $cancelledCount = CourierCarSchedule::where('status', 'cancelled')->count();

        // Chart data untuk total jadwal 6 bulan terakhir
        $totalSchedulesChart = $this->getSchedulesChartData(6);

        // Chart data untuk jadwal selesai 6 bulan terakhir
        $completedSchedulesChart = $this->getCompletedSchedulesChartData(6);

        // Jadwal bulan ini
        $thisMonthSchedules = CourierCarSchedule::whereYear('trip_date', now()->year)
            ->whereMonth('trip_date', now()->month)
            ->count();

        return [
            Stat::make('Total Jadwal', $totalSchedules . ' Trip')
                ->description($thisMonthSchedules . ' jadwal bulan ini')
                ->descriptionIcon('solar-calendar-bold-duotone')
                ->color('primary')
                ->chart($totalSchedulesChart),
            Stat::make('Dijadwalkan & Berlangsung', $scheduledCount + $inProgressCount . ' Trip')
                ->description($scheduledCount . ' dijadwalkan, ' . $inProgressCount . ' berlangsung')
                ->descriptionIcon('solar-clock-circle-bold-duotone')
                ->color('warning')
                ->chart($completedSchedulesChart),
            Stat::make('Selesai vs Dibatalkan', $completedCount . ' Selesai')
                ->description($cancelledCount . ' trip dibatalkan')
                ->descriptionIcon('solar-check-circle-bold-duotone')
                ->color('success'),
        ];
    }

    /**
     * Ambil data total jadwal per bulan untuk 6 bulan terakhir
     */
    private function getSchedulesChartData(int $months): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = CourierCarSchedule::whereYear('trip_date', $date->year)
                ->whereMonth('trip_date', $date->month)
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    /**
     * Ambil data jadwal selesai per bulan untuk 6 bulan terakhir
     */
    private function getCompletedSchedulesChartData(int $months): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = CourierCarSchedule::whereYear('trip_date', $date->year)
                ->whereMonth('trip_date', $date->month)
                ->where('status', 'completed')
                ->count();
            $data[] = $count;
        }
        return $data;
    }
}
