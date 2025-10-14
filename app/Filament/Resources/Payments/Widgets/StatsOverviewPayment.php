<?php

declare(strict_types=1);

namespace App\Filament\Resources\Payments\Widgets;

use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewPayment extends StatsOverviewWidget
{
    protected ?string $heading = 'Statistik Pembayaran';

    protected ?string $description = 'Ringkasan pembayaran dan tren 6 bulan terakhir.';

    protected function getStats(): array
    {
        // Total pembayaran
        $totalPayments = Payment::count();

        // Total nilai pembayaran
        $totalAmount = (float) Payment::sum('amount');

        // Pembayaran bulan ini
        $thisMonthPayments = Payment::whereYear('payment_date', now()->year)
            ->whereMonth('payment_date', now()->month)
            ->count();

        // Total nilai pembayaran bulan ini
        $thisMonthAmount = (float) Payment::whereYear('payment_date', now()->year)
            ->whereMonth('payment_date', now()->month)
            ->sum('amount');

        // Rata-rata nilai pembayaran
        $averageAmount = $totalPayments > 0 ? $totalAmount / $totalPayments : 0;

        // Chart data untuk total pembayaran 6 bulan terakhir
        $totalPaymentsChart = $this->getPaymentsChartData(6);

        // Chart data untuk total nilai pembayaran 6 bulan terakhir
        $amountPaymentsChart = $this->getAmountChartData(6);

        return [
            Stat::make('Total Pembayaran', number_format($totalPayments, 0, ',', '.') . ' Transaksi')
                ->description($thisMonthPayments . ' pembayaran bulan ini')
                ->descriptionIcon('solar-wallet-money-bold-duotone')
                ->color('primary')
                ->chart($totalPaymentsChart),
            Stat::make('Total Nilai Pembayaran', 'Rp ' . number_format($totalAmount, 0, ',', '.'))
                ->description('Rp ' . number_format($thisMonthAmount, 0, ',', '.') . ' bulan ini')
                ->descriptionIcon('solar-money-bag-bold-duotone')
                ->color('success')
                ->chart($amountPaymentsChart),
            Stat::make('Rata-Rata Pembayaran', 'Rp ' . number_format($averageAmount, 0, ',', '.'))
                ->description('Per transaksi pembayaran')
                ->descriptionIcon('solar-chart-2-bold-duotone')
                ->color('warning'),
        ];
    }

    /**
     * Ambil data total pembayaran per bulan untuk 6 bulan terakhir
     */
    private function getPaymentsChartData(int $months): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Payment::whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    /**
     * Ambil data total nilai pembayaran per bulan untuk 6 bulan terakhir
     */
    private function getAmountChartData(int $months): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $amount = (float) Payment::whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->sum('amount');
            $data[] = $amount;
        }
        return $data;
    }
}
