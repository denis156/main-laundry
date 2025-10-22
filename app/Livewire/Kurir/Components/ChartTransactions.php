<?php

declare(strict_types=1);

namespace App\Livewire\Kurir\Components;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class ChartTransactions extends Component
{
    public array $chart = [];

    /**
     * Refresh chart - dipanggil dari JavaScript saat menerima broadcast event
     */
    #[On('refresh-chart')]
    public function refreshChart(): void
    {
        // Refresh computed properties untuk load data terbaru
        unset($this->chartData);
        $this->updateChart();
    }

    /**
     * Computed property untuk data chart
     */
    #[Computed]
    public function chartData(): array
    {
        $courier = Auth::guard('courier')->user();

        if (!$courier) {
            return [
                'labels' => [],
                'data' => [],
                'colors' => [],
            ];
        }

        // Hitung transaksi per status untuk kurir ini
        $transactions = $courier->transactions()
            ->whereIn('workflow_status', [
                'confirmed',         // Terkonfirmasi (perlu dijemput)
                'washing_completed', // Siap Antar
                'in_washing',       // Dicuci
                'delivered',        // Selesai
            ])
            ->selectRaw('workflow_status, COUNT(*) as count')
            ->groupBy('workflow_status')
            ->pluck('count', 'workflow_status')
            ->toArray();

        // Map status ke label yang user-friendly
        $statusMap = [
            'confirmed' => 'Terkonfirmasi',
            'washing_completed' => 'Siap Antar',
            'in_washing' => 'Dicuci',
            'delivered' => 'Selesai',
        ];

        // Warna untuk setiap status (sesuai dengan DaisyUI theme)
        $colorMap = [
            'confirmed' => 'rgba(59, 130, 246, 0.8)',        // info blue
            'washing_completed' => 'rgba(34, 197, 94, 0.8)', // success green
            'in_washing' => 'rgba(168, 85, 247, 0.8)',       // primary purple
            'delivered' => 'rgba(16, 185, 129, 0.8)',        // success emerald
        ];

        // Border colors (lebih gelap)
        $borderColorMap = [
            'confirmed' => 'rgba(59, 130, 246, 1)',
            'washing_completed' => 'rgba(34, 197, 94, 1)',
            'in_washing' => 'rgba(168, 85, 247, 1)',
            'delivered' => 'rgba(16, 185, 129, 1)',
        ];

        // Prepare data untuk chart (maintain order dan hanya tampilkan yang ada data)
        $labels = [];
        $data = [];
        $backgroundColor = [];
        $borderColor = [];

        foreach ($statusMap as $status => $label) {
            if (isset($transactions[$status]) && $transactions[$status] > 0) {
                $labels[] = $label;
                $data[] = $transactions[$status];
                $backgroundColor[] = $colorMap[$status];
                $borderColor[] = $borderColorMap[$status];
            }
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'backgroundColor' => $backgroundColor,
            'borderColor' => $borderColor,
        ];
    }

    /**
     * Update chart configuration
     */
    public function updateChart(): void
    {
        $chartData = $this->chartData;

        $this->chart = [
            'type' => 'doughnut',
            'data' => [
                'labels' => $chartData['labels'],
                'datasets' => [
                    [
                        'label' => 'Jumlah Pesanan',
                        'data' => $chartData['data'],
                        'backgroundColor' => $chartData['backgroundColor'],
                        'borderColor' => $chartData['borderColor'],
                        'borderWidth' => 2,
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => true,
                'plugins' => [
                    'legend' => [
                        'position' => 'bottom',
                        'labels' => [
                            'padding' => 15,
                            'font' => [
                                'size' => 12,
                            ],
                        ],
                    ],
                    'tooltip' => [
                        'callbacks' => [
                            'label' => null, // Will be set via JS to show percentage
                        ],
                    ],
                ],
                'cutout' => '60%', // Untuk doughnut effect
            ],
        ];
    }

    public function mount(): void
    {
        $this->updateChart();
    }

    public function render()
    {
        return view('livewire.kurir.components.chart-transactions');
    }
}
