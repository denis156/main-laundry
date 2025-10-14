<?php

declare(strict_types=1);

namespace App\Filament\Resources\CourierMotorcycles\Widgets;

use App\Models\CourierMotorcycle;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewCourierMotorcycle extends StatsOverviewWidget
{
    protected ?string $heading = 'Statistik Kurir Motor';

    protected ?string $description = 'Top 3 kurir motor terbaik berdasarkan jumlah transaksi 6 bulan terakhir.';

    protected function getStats(): array
    {
        // Ambil top 3 kurir motor berdasarkan jumlah transaksi
        $topCouriers = CourierMotorcycle::withCount('transactions')
            ->orderBy('transactions_count', 'desc')
            ->take(3)
            ->get();

        $stats = [];

        // Jika belum ada data kurir motor
        if ($topCouriers->isEmpty()) {
            return [
                Stat::make('Belum Ada Data', 'Belum ada kurir motor')
                    ->description('Silakan tambah kurir motor dan transaksi')
                    ->descriptionIcon('solar-info-circle-bold-duotone')
                    ->color('gray'),
            ];
        }

        // Top 1
        if (isset($topCouriers[0])) {
            $courier = $topCouriers[0];
            $chartData = $this->getCourierChartData($courier->id, 6);
            $stats[] = Stat::make('Top 1: ' . $courier->name, $courier->transactions_count . ' Transaksi')
                ->description('Kurir Terbaik')
                ->descriptionIcon('solar-crown-bold-duotone')
                ->color('warning')
                ->chart($chartData);
        }

        // Top 2
        if (isset($topCouriers[1])) {
            $courier = $topCouriers[1];
            $chartData = $this->getCourierChartData($courier->id, 6);
            $stats[] = Stat::make('Top 2: ' . $courier->name, $courier->transactions_count . ' Transaksi')
                ->description('Kurir Terpercaya')
                ->descriptionIcon('solar-star-bold-duotone')
                ->color('success')
                ->chart($chartData);
        }

        // Top 3
        if (isset($topCouriers[2])) {
            $courier = $topCouriers[2];
            $chartData = $this->getCourierChartData($courier->id, 6);
            $stats[] = Stat::make('Top 3: ' . $courier->name, $courier->transactions_count . ' Transaksi')
                ->description('Kurir Favorit')
                ->descriptionIcon('solar-medal-star-bold-duotone')
                ->color('primary')
                ->chart($chartData);
        }

        return $stats;
    }

    /**
     * Ambil data transaksi kurir per bulan untuk 6 bulan terakhir
     */
    private function getCourierChartData(int $courierId, int $months): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = CourierMotorcycle::where('id', $courierId)
                ->withCount([
                    'transactions' => function ($query) use ($date) {
                        $query->whereYear('created_at', $date->year)
                            ->whereMonth('created_at', $date->month);
                    }
                ])
                ->first()
                ->transactions_count ?? 0;
            $data[] = $count;
        }
        return $data;
    }
}
