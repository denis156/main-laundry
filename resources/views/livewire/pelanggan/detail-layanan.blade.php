<section class="bg-base-100">
    <x-header icon="solar.file-text-bold-duotone" icon-classes="text-primary w-6 h-6" title="Detail Layanan"
        subtitle="Informasi Layanan {{ $service->name }}" separator>
        <x-slot:actions>
            <x-button icon="solar.undo-left-linear" link="{{ route('pelanggan.beranda') }}"
                class="btn-circle btn-secondary" />
        </x-slot:actions>
    </x-header>

    <div class="space-y-4">
        {{-- Service Header Card --}}
        <x-card class="bg-base-300 shadow">
            {{-- Featured Badge --}}
            @if ($this->isFeatured())
                @php
                    $badgeSettings = $this->getBadgeSettings();
                @endphp
                <x-slot:menu>
                    <x-badge value="{{ $badgeSettings['text'] ?? 'Unggulan' }}" class="badge-md badge-warning" />
                </x-slot:menu>
            @endif

            <div class="space-y-3">
                {{-- Service Name & Price --}}
                <div>
                    <h2 class="text-2xl font-bold text-base-content">{{ $service->name }}</h2>
                    <p class="text-xl text-accent font-semibold mt-2">
                        {{ App\Helper\Database\ServiceHelper::getFormattedPriceWithUnit($service) }}
                    </p>
                </div>

                <div class="divider my-2"></div>

                {{-- Duration --}}
                <div class="flex items-center gap-2">
                    <x-icon name="solar.clock-circle-bold-duotone" class="size-5 text-primary" />
                    <span class="text-base font-medium">
                        Durasi: {{ App\Helper\Database\ServiceHelper::getFormattedDuration($service) }}
                    </span>
                </div>

                {{-- Pricing Unit --}}
                <div class="flex items-center gap-2">
                    <x-icon name="solar.bill-list-bold-duotone" class="size-5 text-primary" />
                    <span class="text-base font-medium">
                        Satuan:
                        {{ App\Helper\Database\ServiceHelper::isPerKg($service) ? 'Per Kilogram (Kg)' : 'Per Item/Lembar' }}
                    </span>
                </div>
            </div>
        </x-card>

        {{-- Pricing Tiers (if per_kg) --}}
        @php
            $pricingTiers = $this->getPricingTiers();
        @endphp
        @if (App\Helper\Database\ServiceHelper::isPerKg($service) && !empty($pricingTiers))
            <x-card title="Harga Bertingkat" subtitle="Semakin banyak semakin murah" class="bg-base-300 shadow"
                separator>
                <div class="space-y-3">
                    @foreach ($pricingTiers as $tier)
                        <div class="flex justify-between items-center p-3 bg-base-100 rounded-lg">
                            <div>
                                <span class="font-semibold">
                                    @if ($tier['min_kg'] == 0 && $tier['max_kg'])
                                        {{ $tier['min_kg'] }} - {{ $tier['max_kg'] }} kg
                                    @elseif ($tier['max_kg'])
                                        {{ $tier['min_kg'] }} - {{ $tier['max_kg'] }} kg
                                    @else
                                        {{ $tier['min_kg'] }} kg ke atas
                                    @endif
                                </span>
                            </div>
                            <span class="text-accent font-bold">
                                Rp {{ number_format($tier['price_per_kg'], 0, ',', '.') }}/kg
                            </span>
                        </div>
                    @endforeach
                </div>
            </x-card>
        @endif

        {{-- Fitur Layanan --}}
        @php
            $features = $this->getFeatures();
        @endphp
        @if (!empty($features))
            <x-card title="Fitur Layanan" subtitle="Apa saja yang termasuk dalam layanan ini" class="bg-base-300 shadow"
                separator>
                <ul class="flex flex-col gap-3">
                    @foreach ($features as $feature)
                        <li class="flex items-start gap-3">
                            <x-icon name="solar.check-circle-bold-duotone"
                                class="size-5 text-success shrink-0 mt-0.5" />
                            <span class="text-base">{{ $feature }}</span>
                        </li>
                    @endforeach
                </ul>
            </x-card>
        @endif

        {{-- Yang Termasuk --}}
        @php
            $includes = $this->getIncludes();
        @endphp
        @if (!empty($includes))
            <x-card title="Yang Termasuk" subtitle="Benefit yang Anda dapatkan" class="bg-base-300 shadow" separator>
                <ul class="flex flex-col gap-3">
                    @foreach ($includes as $include)
                        <li class="flex items-start gap-3">
                            <x-icon name="solar.check-circle-bold-duotone" class="size-5 text-success shrink-0 mt-0.5" />
                            <span class="text-base">{{ $include }}</span>
                        </li>
                    @endforeach
                </ul>
            </x-card>
        @endif

        {{-- Material yang Digunakan --}}
        @php
            $materials = $this->getMaterials();
        @endphp
        @if (!empty($materials))
            <x-card title="Material yang Digunakan" subtitle="Produk berkualitas untuk cucian Anda"
                class="bg-base-300 shadow" separator>
                <ul class="flex flex-col gap-3">
                    @foreach ($materials as $material)
                        <li class="flex items-start gap-3">
                            <x-icon name="solar.check-circle-bold-duotone" class="size-5 text-success shrink-0 mt-0.5" />
                            <span class="text-base">{{ $material }}</span>
                        </li>
                    @endforeach
                </ul>
            </x-card>
        @endif

        {{-- Batasan --}}
        @php
            $restrictions = $this->getRestrictions();
        @endphp
        @if (!empty($restrictions))
            <x-card title="Batasan" subtitle="Hal yang perlu diperhatikan" class="bg-base-300 shadow" separator>
                <ul class="flex flex-col gap-3">
                    @foreach ($restrictions as $restriction)
                        <li class="flex items-start gap-3">
                            <x-icon name="solar.close-circle-bold-duotone" class="size-5 text-error shrink-0 mt-0.5" />
                            <span class="text-base">{{ $restriction }}</span>
                        </li>
                    @endforeach
                </ul>
            </x-card>
        @endif

        {{-- Action Buttons --}}
        <x-button label="Pesan Sekarang" icon="solar.add-circle-bold-duotone" class="btn-primary btn-lg btn-block"
            link="{{ route('pelanggan.buat-pesanan') }}" />
    </div>
</section>
