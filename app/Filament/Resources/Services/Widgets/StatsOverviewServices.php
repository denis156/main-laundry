<?php

declare(strict_types=1);

namespace App\Filament\Resources\Services\Widgets;

use App\Models\Service;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewServices extends StatsOverviewWidget
{
    protected ?string $heading = 'Statistik Layanan';

    protected ?string $description = 'Top 3 layanan terpopuler berdasarkan jumlah transaksi 6 bulan terakhir.';

    protected function getStats(): array
    {
        // Ambil top 3 layanan berdasarkan jumlah penggunaan di transactions
        $topServices = Service::withCount('transactions')
            ->orderBy('transactions_count', 'desc')
            ->take(3)
            ->get();

        $stats = [];

        // Jika belum ada data layanan
        if ($topServices->isEmpty()) {
            return [
                Stat::make('Belum Ada Data', 'Belum ada transaksi layanan')
                    ->description('Silakan tambah layanan dan transaksi')
                    ->descriptionIcon('solar-info-circle-bold-duotone')
                    ->color('gray'),
            ];
        }

        // Top 1
        if (isset($topServices[0])) {
            $service = $topServices[0];
            $chartData = $this->getServiceChartData($service->id, 6);
            $stats[] = Stat::make('Top 1: ' . $service->name, $service->transactions_count . ' Transaksi')
                ->description('Layanan Terpopuler')
                ->descriptionIcon('solar-crown-bold-duotone')
                ->color('warning')
                ->chart($chartData);
        }

        // Top 2
        if (isset($topServices[1])) {
            $service = $topServices[1];
            $chartData = $this->getServiceChartData($service->id, 6);
            $stats[] = Stat::make('Top 2: ' . $service->name, $service->transactions_count . ' Transaksi')
                ->description('Layanan Populer')
                ->descriptionIcon('solar-star-bold-duotone')
                ->color('success')
                ->chart($chartData);
        }

        // Top 3
        if (isset($topServices[2])) {
            $service = $topServices[2];
            $chartData = $this->getServiceChartData($service->id, 6);
            $stats[] = Stat::make('Top 3: ' . $service->name, $service->transactions_count . ' Transaksi')
                ->description('Layanan Favorit')
                ->descriptionIcon('solar-medal-star-bold-duotone')
                ->color('primary')
                ->chart($chartData);
        }

        return $stats;
    }

    /**
     * Ambil data penggunaan layanan per bulan untuk 6 bulan terakhir
     */
    private function getServiceChartData(int $serviceId, int $months): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Service::where('id', $serviceId)
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
