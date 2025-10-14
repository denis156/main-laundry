<?php

declare(strict_types=1);

namespace App\Filament\Resources\Transactions\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewTransactions extends StatsOverviewWidget
{
    protected ?string $heading = 'Statistik Transaksi';

    protected ?string $description = 'Ringkasan data transaksi dan tren revenue 6 bulan terakhir.';

    protected function getStats(): array
    {
        // Total transaksi
        $totalTransactions = Transaction::count();

        // Total revenue (semua transaksi yang paid)
        $totalRevenue = (float) Transaction::where('payment_status', 'paid')
            ->sum('total_price');

        // Average transaction value
        $averageTransaction = $totalTransactions > 0
            ? (float) Transaction::avg('total_price')
            : 0;

        // Ambil data 6 bulan terakhir
        $transactionsChart = $this->getTransactionsChartData(6);
        $revenueChart = $this->getRevenueChartData(6);
        $averageChart = $this->getAverageChartData(6);

        return [
            Stat::make('Total Revenue', 'Rp ' . number_format($totalRevenue, 0, ',', '.'))
                ->description('Revenue Lunas')
                ->descriptionIcon('solar-wallet-money-bold-duotone')
                ->color('success')
                ->chart($revenueChart),
            Stat::make('Total Transaksi', number_format($totalTransactions, 0, ',', '.') . ' Transaksi')
                ->description('Semua Transaksi')
                ->descriptionIcon('solar-bill-list-bold-duotone')
                ->color('primary')
                ->chart($transactionsChart),
            Stat::make('Rata-rata Transaksi', 'Rp ' . number_format($averageTransaction, 0, ',', '.'))
                ->description('Nilai Rata-rata')
                ->descriptionIcon('solar-chart-2-bold-duotone')
                ->color('warning')
                ->chart($averageChart),
        ];
    }

    /**
     * Ambil data jumlah transaksi per bulan untuk 6 bulan terakhir
     */
    private function getTransactionsChartData(int $months): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Transaction::whereYear('order_date', $date->year)
                ->whereMonth('order_date', $date->month)
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    /**
     * Ambil data revenue per bulan untuk 6 bulan terakhir (hanya yang paid)
     */
    private function getRevenueChartData(int $months): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenue = Transaction::where('payment_status', 'paid')
                ->whereYear('order_date', $date->year)
                ->whereMonth('order_date', $date->month)
                ->sum('total_price');
            $data[] = (float) $revenue;
        }
        return $data;
    }

    /**
     * Ambil data rata-rata nilai transaksi per bulan untuk 6 bulan terakhir
     */
    private function getAverageChartData(int $months): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $average = Transaction::whereYear('order_date', $date->year)
                ->whereMonth('order_date', $date->month)
                ->avg('total_price');
            $data[] = $average ? (float) $average : 0;
        }
        return $data;
    }
}
