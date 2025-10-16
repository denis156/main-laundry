<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueTrendChart extends ChartWidget
{
    protected ?string $heading = 'Trend Pendapatan 30 Hari Terakhir';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected ?string $maxHeight = '400px';

    /**
     * Tipe chart yang digunakan
     */
    protected function getType(): string
    {
        return 'line';
    }
    
    /**
     * Data untuk chart
     */
    protected function getData(): array
    {
        $data = $this->getRevenueDataLast30Days();

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan (Rp)',
                    'data' => $data['amounts'],
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => 'rgb(34, 197, 94)',
                    'pointBorderColor' => '#fff',
                    'pointHoverBackgroundColor' => '#fff',
                    'pointHoverBorderColor' => 'rgb(34, 197, 94)',
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    /**
     * Konfigurasi options untuk chart
     */
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false,
            ],
            'maintainAspectRatio' => false,
        ];
    }

    /**
     * Ambil data pendapatan 30 hari terakhir
     */
    private function getRevenueDataLast30Days(): array
    {
        $labels = [];
        $amounts = [];

        // Loop 30 hari terakhir
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);

            // Format label tanggal
            $labels[] = $date->format('d M');

            // Hitung total pendapatan per hari
            $dailyRevenue = Payment::whereDate('payment_date', $date)
                ->sum('amount');

            $amounts[] = (float) $dailyRevenue;
        }

        return [
            'labels' => $labels,
            'amounts' => $amounts,
        ];
    }

    /**
     * Get description untuk menampilkan statistik
     */
    public function getDescription(): ?string
    {
        $stats = $this->getRevenueStats();

        return sprintf(
            'Total: Rp %s | Rata-rata: Rp %s | Tertinggi: Rp %s',
            number_format($stats['total'], 0, ',', '.'),
            number_format($stats['average'], 0, ',', '.'),
            number_format($stats['highest'], 0, ',', '.')
        );
    }

    /**
     * Hitung statistik pendapatan
     */
    private function getRevenueStats(): array
    {
        $amounts = $this->getRevenueDataLast30Days()['amounts'];

        return [
            'total' => array_sum($amounts),
            'average' => count($amounts) > 0 ? array_sum($amounts) / count($amounts) : 0,
            'highest' => count($amounts) > 0 ? max($amounts) : 0,
        ];
    }
}
