<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use App\Helper\StatusTransactionHelper;

class WorkflowStatusChart extends ChartWidget
{
    protected static ?int $sort = 4;

    protected ?string $heading = 'Distribusi Status Workflow';

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
        $data = $this->getWorkflowStatusDistribution();

        return [
            'datasets' => [
                [
                    'label' => 'Transaksi',
                    'data' => $data['counts'],
                    'backgroundColor' => [
                        'rgb(251, 191, 36)',   // pending_confirmation - Amber
                        'rgb(59, 130, 246)',   // confirmed - Blue
                        'rgb(147, 51, 234)',   // picked_up - Purple
                        'rgb(236, 72, 153)',   // at_loading_post - Pink
                        'rgb(249, 115, 22)',   // in_washing - Orange
                        'rgb(34, 197, 94)',    // washing_completed - Green
                        'rgb(14, 165, 233)',   // out_for_delivery - Sky
                        'rgb(16, 185, 129)',   // delivered - Emerald
                        'rgb(239, 68, 68)',    // cancelled - Red
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
                            'size' => 11,
                        ],
                    ],
                ],
            ],
            'maintainAspectRatio' => false,
            'cutout' => '60%',
        ];
    }

    /**
     * Ambil data distribusi workflow status
     */
    private function getWorkflowStatusDistribution(): array
    {
        // Definisi semua status workflow menggunakan helper
        $statuses = StatusTransactionHelper::getAllStatuses();

        $labels = [];
        $counts = [];

        foreach ($statuses as $status => $label) {
            $count = Transaction::where('workflow_status', $status)
                ->whereNotIn('workflow_status', ['delivered', 'cancelled'])
                ->count();

            // Hanya tampilkan status yang ada datanya
            if ($count > 0) {
                $labels[] = $label;
                $counts[] = $count;
            }
        }

        // Jika tidak ada data sama sekali, tampilkan placeholder
        if (empty($counts)) {
            $labels = ['Tidak ada data'];
            $counts = [1];
        }

        return [
            'labels' => $labels,
            'counts' => $counts,
        ];
    }

    /**
     * Get description untuk menampilkan total transaksi aktif
     */
    public function getDescription(): ?string
    {
        $activeCount = Transaction::whereNotIn('workflow_status', ['delivered', 'cancelled'])
            ->count();

        return sprintf(
            'Total %d transaksi sedang diproses',
            $activeCount
        );
    }
}
