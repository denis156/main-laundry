{{-- Layanan Kami --}}

<div class="flex gap-4 overflow-x-auto snap-x snap-mandatory scrollbar-hide pb-4">
    @foreach ($this->services as $service)
        <x-card title="{{ $service->name }}"
            subtitle="{{ App\Helper\Database\ServiceHelper::getFormattedPriceWithUnit($service) }}"
            class="bg-base-300 shadow-md min-w-[84%] snap-center" separator wire:key="service-{{ $service->id }}">

            {{-- Features List --}}
            @php
                $features = $this->getFeatures($service);
                $displayFeatures = array_slice($features, 0, 3); // Limit 3 features
                $remainingCount = count($features) - 3;
            @endphp
            @if (!empty($displayFeatures))
                <div class="mb-2">
                    <p class="text-xs font-semibold text-base-content/80 mb-2">Fitur Layanan:</p>
                    <ul class="flex flex-col gap-2 text-xs">
                        @foreach ($displayFeatures as $feature)
                            <li>
                                <x-icon name="solar.check-circle-bold-duotone"
                                    class="size-4 me-2 inline-block text-success" />
                                <span>{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>k

                    {{-- Show remaining features count --}}
                    @if ($remainingCount > 0)
                        <p class="text-xs text-base-content/50 mt-2 italic">
                            +{{ $remainingCount }} fitur lainnya
                        </p>
                    @endif
                </div>
            @endif

            {{-- Duration Info --}}
            <div class="mt-3 text-xs text-base-content/70 flex items-center gap-1">
                <x-icon name="solar.clock-circle-bold-duotone" class="size-4" />
                <span>{{ App\Helper\Database\ServiceHelper::getFormattedDuration($service) }}</span>
            </div>

            {{-- Featured Badge --}}
            @php
                // Debug: Check is_featured value
                $isFeatured = (bool) ($service->is_featured ?? false);
            @endphp
            @if ($isFeatured)
                <x-slot:menu>
                    <x-badge value="Unggulan" class="badge-sm md:badge-md badge-warning" />
                </x-slot:menu>
            @endif

            {{-- Action Button --}}
            <x-slot:actions separator>
                <div class="w-full grid grid-cols-2 gap-2">
                    <x-button label="Detail" icon="solar.eye-bold-duotone" class="btn-secondary btn-block"
                        link="{{ route('pelanggan.layanan.detail', $service->id) }}" />
                    <x-button label="Pesan" icon="solar.add-circle-bold-duotone" class="btn-accent btn-block"
                        link="{{ route('pelanggan.buat-pesanan') }}" />
                </div>
            </x-slot:actions>
        </x-card>
    @endforeach
</div>
