@php use App\Helper\StatusTransactionCustomerHelper; @endphp
<section class="bg-base-100">
    {{-- Header --}}
    <x-header icon="solar.bill-check-bold-duotone" icon-classes="text-primary w-6 h-6" title="Detail Pesanan"
        subtitle="Informasi lengkap pesanan laundry kamu" separator>
        <x-slot:actions>
            <x-button icon="solar.undo-left-linear" link="{{ route('pelanggan.pesanan') }}" class="btn-circle btn-secondary" />
        </x-slot:actions>
    </x-header>

    <div class="space-y-4">
        {{-- Single Card with All Data --}}
        <div class="card bg-base-300 shadow">
            <div class="card-body p-4">
                {{-- Invoice & Status Badge --}}
                <div class="flex items-start justify-between">
                    <div>
                        <p class="font-bold text-lg text-primary">{{ $transaction->invoice_number }}</p>
                        <p class="text-xs text-base-content/60">
                            {{ $transaction->formatted_order_date }}
                        </p>
                    </div>
                    <span class="badge {{ StatusTransactionCustomerHelper::getStatusBadgeColor($transaction->workflow_status) }}">
                        {{ StatusTransactionCustomerHelper::getStatusText($transaction->workflow_status) }}
                    </span>
                </div>

                <div class="divider my-2"></div>

                {{-- Courier Info (if confirmed and has courier) --}}
                @if ($transaction->courierMotorcycle && $transaction->workflow_status !== 'pending_confirmation')
                    <h3 class="font-bold text-base mb-2 flex items-center gap-2">
                        <x-icon name="solar.delivery-bold-duotone" class="w-4 h-4 text-primary" />
                        Informasi Kurir
                    </h3>

                    <div class="bg-base-200 rounded-lg p-3 mb-3">
                        <div class="flex items-center gap-3">
                            <div class="avatar">
                                <div class="ring-accent ring-offset-base-100 w-12 h-12 rounded-full ring-2 ring-offset-2">
                                    <img src="{{ $transaction->courierMotorcycle->getFilamentAvatarUrl() }}"
                                         alt="{{ $transaction->courierMotorcycle->name }}" />
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold">{{ $transaction->courierMotorcycle->name }}</p>
                                <p class="text-xs text-base-content/60">{{ $transaction->courierMotorcycle->phone }}</p>
                                <p class="text-xs text-base-content/60">{{ $transaction->courierMotorcycle->vehicle_number }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="divider my-2"></div>
                @endif

                {{-- Timeline Status (only show if not pending or cancelled) --}}
                @if (!in_array($transaction->workflow_status, ['pending_confirmation', 'cancelled']))
                    <h3 class="font-bold text-base mb-2 flex items-center gap-2">
                        <x-icon name="solar.clock-circle-bold-duotone" class="w-4 h-4 text-primary" />
                        Status Pesanan
                    </h3>

                    @php
                        // Define timeline steps based on helper status
                        $timelineSteps = [
                            ['key' => 'confirmed', 'text' => StatusTransactionCustomerHelper::getStatusText('confirmed')],
                            ['key' => 'picked_up', 'text' => 'Diproses'],
                            ['key' => 'in_washing', 'text' => StatusTransactionCustomerHelper::getStatusText('in_washing')],
                            ['key' => 'out_for_delivery', 'text' => StatusTransactionCustomerHelper::getStatusText('out_for_delivery')],
                            ['key' => 'delivered', 'text' => StatusTransactionCustomerHelper::getStatusText('delivered')]
                        ];
                    @endphp
                    <div class="bg-base-200 rounded-lg p-3">
                        <ul class="timeline timeline-vertical">
                            @foreach ($timelineSteps as $index => $step)
                                @php
                                    // Check if step is active based on workflow status
                                    $isStepActive = false;
                                    $isNextStepActive = false;

                                    switch($step['key']) {
                                        case 'confirmed':
                                            $isStepActive = in_array($transaction->workflow_status, ['confirmed', 'picked_up', 'at_loading_post', 'in_washing', 'washing_completed', 'out_for_delivery', 'delivered']);
                                            $isNextStepActive = in_array($transaction->workflow_status, ['picked_up', 'at_loading_post', 'in_washing', 'washing_completed', 'out_for_delivery', 'delivered']);
                                            break;
                                        case 'picked_up':
                                            $isStepActive = in_array($transaction->workflow_status, ['picked_up', 'at_loading_post', 'in_washing', 'washing_completed', 'out_for_delivery', 'delivered']);
                                            $isNextStepActive = in_array($transaction->workflow_status, ['in_washing', 'washing_completed', 'out_for_delivery', 'delivered']);
                                            break;
                                        case 'in_washing':
                                            $isStepActive = in_array($transaction->workflow_status, ['in_washing', 'washing_completed', 'out_for_delivery', 'delivered']);
                                            $isNextStepActive = in_array($transaction->workflow_status, ['out_for_delivery', 'delivered']);
                                            break;
                                        case 'out_for_delivery':
                                            $isStepActive = in_array($transaction->workflow_status, ['out_for_delivery', 'delivered']);
                                            $isNextStepActive = $transaction->workflow_status === 'delivered';
                                            break;
                                        case 'delivered':
                                            $isStepActive = $transaction->workflow_status === 'delivered';
                                            $isNextStepActive = false;
                                            break;
                                    }

                                    $isEvenIndex = $index % 2 === 0;
                                @endphp

                                <li>
                                    {{-- Previous line (for steps after first) --}}
                                    @if ($index > 0)
                                        <hr class="{{ $isStepActive ? 'bg-success' : 'bg-secondary' }}" />
                                    @endif

                                    @if ($isEvenIndex)
                                        {{-- Start/Top position --}}
                                        <div class="timeline-start timeline-box text-xs">{{ $step['text'] }}</div>
                                        <div class="timeline-middle">
                                            @if ($isStepActive)
                                                <x-icon name="solar.check-circle-bold" class="w-5 h-5 text-success" />
                                            @else
                                                <x-icon name="solar.close-circle-bold-duotone" class="w-5 h-5 text-secondary" />
                                            @endif
                                        </div>
                                    @else
                                        {{-- End/Bottom position --}}
                                        <div class="timeline-middle">
                                            @if ($isStepActive)
                                                <x-icon name="solar.check-circle-bold" class="w-5 h-5 text-success" />
                                            @else
                                                <x-icon name="solar.close-circle-bold-duotone" class="w-5 h-5 text-secondary" />
                                            @endif
                                        </div>
                                        <div class="timeline-end timeline-box text-xs">{{ $step['text'] }}</div>
                                    @endif

                                    {{-- Next line (except for last step) --}}
                                    @if ($index < count($timelineSteps) - 1)
                                        <hr class="{{ $isNextStepActive ? 'bg-success' : 'bg-secondary' }}" />
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="divider my-2"></div>
                @endif

                {{-- Transaction Info --}}
                <h3 class="font-bold text-base mb-2 flex items-center gap-2">
                    <x-icon name="solar.bill-list-bold-duotone" class="w-4 h-4 text-primary" />
                    Informasi Pesanan
                </h3>

                <div class="bg-base-200 rounded-lg p-3 space-y-2 mb-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/70">Tanggal Order</span>
                        <span class="font-semibold">{{ $transaction->order_date->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/70">Jam Order</span>
                        <span class="font-semibold">{{ $transaction->order_date->format('H:i') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/70">Layanan</span>
                        <span class="font-semibold">{{ $transaction->service?->name ?? 'N/A' }}</span>
                    </div>
                    @if ($transaction->weight)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-base-content/70">Berat</span>
                            <span class="font-semibold">{{ $transaction->weight }} kg</span>
                        </div>
                    @endif
                    @if ($transaction->price_per_kg)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-base-content/70">Harga/kg</span>
                            <span class="font-semibold">{{ $transaction->formatted_price_per_kg }}</span>
                        </div>
                    @endif
                    @if ($transaction->payment_timing)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-base-content/70">Metode Pembayaran</span>
                            <span class="font-semibold">{{ $transaction->payment_timing_text }}</span>
                        </div>
                    @endif
                    @if ($transaction->total_price)
                        <div class="divider my-1"></div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-base-content/70">Total</span>
                            <span class="font-semibold text-right text-primary text-base">
                                {{ $transaction->formatted_total_price }}
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Notes --}}
                @if ($transaction->notes)
                    <div class="divider my-2"></div>

                    <h3 class="font-bold text-base mb-2 flex items-center gap-2">
                        <x-icon name="solar.document-text-bold-duotone" class="w-4 h-4 text-primary" />
                        Catatan
                    </h3>

                    <div class="bg-base-200 rounded-lg p-3">
                        <p class="text-sm">{{ $transaction->notes }}</p>
                    </div>
                @endif

                {{-- Action Buttons --}}
                <div class="mt-3">
                    @if ($transaction->workflow_status === 'pending_confirmation')
                        {{-- Status: Pending - Show Cancel button --}}
                        <x-button wire:click="openCancelModal"
                            label="Batalkan Pesanan"
                            icon="solar.close-circle-bold-duotone"
                            class="btn-error btn-sm btn-block" />
                    @elseif (in_array($transaction->workflow_status, ['confirmed', 'picked_up', 'at_loading_post']))
                        {{-- Status: Diproses - Show WA to Kurir --}}
                        @if ($transaction->courierMotorcycle)
                            <x-button link="{{ $this->getWhatsAppKurirUrl() }}"
                                external
                                label="Hubungi Kurir"
                                icon="solar.chat-round-bold-duotone"
                                class="btn-success btn-sm btn-block" />
                        @endif
                    @elseif (in_array($transaction->workflow_status, ['in_washing', 'washing_completed']))
                        {{-- Status: Dicuci - Show WA to Admin --}}
                        <x-button link="{{ $this->getWhatsAppAdminUrl() }}"
                            external
                            label="Hubungi Admin"
                            icon="solar.chat-round-bold-duotone"
                            class="btn-success btn-sm btn-block" />
                    @elseif ($transaction->workflow_status === 'out_for_delivery')
                        {{-- Status: Diantar - Show WA to Kurir --}}
                        @if ($transaction->courierMotorcycle)
                            <x-button link="{{ $this->getWhatsAppKurirUrl() }}"
                                external
                                label="Hubungi Kurir"
                                icon="solar.chat-round-bold-duotone"
                                class="btn-success btn-sm btn-block" />
                        @endif
                    @endif
                    {{-- Status: Delivered & Cancelled - No button --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Batalkan Pesanan --}}
    <x-modal wire:model="showCancelModal" title="Batalkan Pesanan"
        subtitle="Apakah kamu yakin ingin membatalkan pesanan ini?" persistent separator>
        <div class="py-4">
            <p class="text-base-content/70">Pesanan yang dibatalkan tidak dapat dikembalikan.</p>
        </div>
        <x-slot:actions>
            <x-button label="Tidak" wire:click="$set('showCancelModal', false)" />
            <x-button label="Ya, Batalkan" wire:click="cancelOrder" class="btn-error" />
        </x-slot:actions>
    </x-modal>
</section>
