<div>
    <x-card class="bg-base-300 shadow-lg hover:shadow-xl transition-shadow" title="Status Pesanan Anda"
        subtitle="Distribusi pesanan berdasarkan status" shadow separator>
        <div class="flex justify-center items-center min-h-[300px]">
            @if (empty($this->chartData['data']) || array_sum($this->chartData['data']) === 0)
                {{-- Empty State --}}
                <div class="text-center py-8">
                    <x-icon name="solar.chart-bold-duotone" class="w-16 h-16 text-base-content/20 mx-auto mb-4" />
                    <h3 class="font-bold text-lg">Belum Ada Data</h3>
                    <p class="text-base-content/60 text-sm">
                        Belum ada pesanan untuk ditampilkan di grafik.
                    </p>
                </div>
            @else
                {{-- Chart --}}
                <div class="w-full max-w-md">
                    <x-chart wire:model="chart" />
                </div>
            @endif
        </div>

        {{-- Summary Stats --}}
        @if (!empty($this->chartData['data']))
            <div class="divider my-2"></div>
            <div class="grid grid-cols-2 gap-2 text-center">
                @php
                    $total = array_sum($this->chartData['data']);
                @endphp
                @foreach ($this->chartData['labels'] as $index => $label)
                    <div class="stat bg-base-200 rounded-lg p-3">
                        <div class="stat-title text-xs">{{ $label }}</div>
                        <div class="stat-value text-2xl">{{ $this->chartData['data'][$index] }}</div>
                        <div class="stat-desc text-xs">
                            {{ number_format(($this->chartData['data'][$index] / $total) * 100, 1) }}%
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-card>
</div>
