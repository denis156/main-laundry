<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;

class PaymentStatusChart extends ChartWidget
{
    protected static ?int $sort = 5;

    protected ?string $heading = 'Status Pembayaran';

    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 2,
    ];

    protected ?string $maxHeight = '300px';

    /**
     * Tipe chart yang digunakan
     */
    protected function getType(): string
    {
        return 'doughnut';
    }

    /**
     * Get data untuk chart
     */
    protected function getData(): array
    {
        $data = $this->getPaymentStatusDistribution();

        return [
            'datasets' => [
                [
                    'label' => 'Transaksi',
                    'data' => $data['counts'],
                    'backgroundColor' => [
                        'rgb(34, 197, 94)',    // paid - Green
                        'rgb(239, 68, 68)',    // unpaid - Red
                    ],
                    'borderWidth' => 2,
                    'borderColor' => '#ffffff',
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
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 15,
                        'usePointStyle' => true,
                        'font' => [
                            'size' => 12,
                        ],
                    ],
                ],
            ],
            'maintainAspectRatio' => false,
            'cutout' => '60%',
        ];
    }

    /**
     * Ambil data distribusi payment status
     */
    private function getPaymentStatusDistribution(): array
    {
        // Hitung transaksi yang sudah dibayar (paid)
        $paidCount = Transaction::where('payment_status', 'paid')
            ->whereNotIn('workflow_status', ['cancelled'])
            ->count();

        // Hitung transaksi yang belum dibayar (unpaid)
        $unpaidCount = Transaction::where('payment_status', 'unpaid')
            ->whereNotIn('workflow_status', ['cancelled'])
            ->count();

        // Jika tidak ada data sama sekali
        if ($paidCount === 0 && $unpaidCount === 0) {
            return [
                'labels' => ['Tidak ada data'],
                'counts' => [1],
            ];
        }

        return [
            'labels' => ['Lunas', 'Belum Bayar'],
            'counts' => [$paidCount, $unpaidCount],
        ];
    }

    /**
     * Get description untuk menampilkan total dan nilai piutang
     */
    public function getDescription(): ?string
    {
        $unpaidTransactions = Transaction::where('payment_status', 'unpaid')
            ->whereNotIn('workflow_status', ['cancelled'])
            ->get();

        $unpaidCount = $unpaidTransactions->count();

        if ($unpaidCount === 0) {
            return 'Semua transaksi sudah lunas';
        }

        $unpaidAmount = 0;
        foreach ($unpaidTransactions as $transaction) {
            $unpaidAmount += \App\Helper\Database\TransactionHelper::getTotalPrice($transaction);
        }

        return sprintf(
            '%d transaksi belum bayar | Total: Rp %s',
            $unpaidCount,
            number_format($unpaidAmount, 0, ',', '.')
        );
    }
}
