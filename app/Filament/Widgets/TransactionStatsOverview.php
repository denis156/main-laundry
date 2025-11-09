<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Helper\Database\PaymentHelper;
use App\Helper\Database\TransactionHelper;
use App\Models\Payment;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class TransactionStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Statistik Transaksi & Keuangan';

    protected ?string $description = 'Ringkasan transaksi harian dan performa keuangan.';

    protected function getStats(): array
    {
        return [
            // 1. Total Transaksi Hari Ini
            Stat::make('Total Transaksi Hari Ini', $this->getTodayTransactionsCount() . ' Transaksi')
                ->description($this->getTodayTransactionsTrend())
                ->descriptionIcon($this->getTodayTransactionsTrendIcon())
                ->chart($this->getLast7DaysTransactionsChart())
                ->color('primary'),

            // 2. Pendapatan Hari Ini
            Stat::make('Pendapatan Hari Ini', 'Rp ' . number_format($this->getTodayRevenue(), 0, ',', '.'))
                ->description('Total pembayaran masuk')
                ->descriptionIcon('solar-wallet-money-bold-duotone')
                ->chart($this->getLast7DaysRevenueChart())
                ->color('success'),

            // 3. Transaksi Belum Selesai
            Stat::make('Transaksi Belum Selesai', $this->getPendingTransactionsCount() . ' Transaksi')
                ->description('Sedang diproses')
                ->descriptionIcon('solar-hourglass-bold-duotone')
                ->color($this->getPendingTransactionsCount() > 50 ? 'danger' : 'warning'),
        ];
    }

    /**
     * Hitung total transaksi hari ini
     */
    private function getTodayTransactionsCount(): int
    {
        return Transaction::whereDate('created_at', today())->count();
    }

    /**
     * Hitung trend transaksi hari ini vs kemarin
     */
    private function getTodayTransactionsTrend(): string
    {
        $today = $this->getTodayTransactionsCount();
        $yesterday = Transaction::whereDate('created_at', today()->subDay())->count();

        if ($yesterday == 0) {
            return $today > 0 ? '+100%' : 'Tidak ada data kemarin';
        }

        $percentage = (($today - $yesterday) / $yesterday) * 100;
        $sign = $percentage >= 0 ? '+' : '';

        return $sign . number_format($percentage, 1) . '% vs kemarin';
    }

    /**
     * Icon trend transaksi
     */
    private function getTodayTransactionsTrendIcon(): string
    {
        $today = $this->getTodayTransactionsCount();
        $yesterday = Transaction::whereDate('created_at', today()->subDay())->count();

        return $today >= $yesterday ? 'solar-graph-up-bold-duotone' : 'solar-graph-down-bold-duotone';
    }

    /**
     * Chart data transaksi 7 hari terakhir
     */
    private function getLast7DaysTransactionsChart(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $data[] = Transaction::whereDate('created_at', $date)->count();
        }
        return $data;
    }

    /**
     * Hitung total pendapatan hari ini
     */
    private function getTodayRevenue(): float
    {
        $payments = Payment::all()->filter(function (Payment $payment) {
            $paymentDate = PaymentHelper::getPaymentDate($payment);
            return $paymentDate && $paymentDate->isSameDay(today());
        });

        return (float) $payments->sum('amount');
    }

    /**
     * Chart data pendapatan 7 hari terakhir
     */
    private function getLast7DaysRevenueChart(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $dailyRevenue = Payment::all()->filter(function (Payment $payment) use ($date) {
                $paymentDate = PaymentHelper::getPaymentDate($payment);
                return $paymentDate && $paymentDate->isSameDay($date);
            })->sum('amount');

            $data[] = (float) $dailyRevenue;
        }
        return $data;
    }

    /**
     * Hitung transaksi yang belum selesai
     */
    private function getPendingTransactionsCount(): int
    {
        return Transaction::whereNotIn('workflow_status', ['delivered', 'cancelled'])
            ->count();
    }
}
