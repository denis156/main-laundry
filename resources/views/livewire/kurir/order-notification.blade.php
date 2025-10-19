{{--
    Order Notification Component
    Melakukan polling setiap 30 detik untuk cek pesanan baru
    Trigger ringtone ketika ada pesanan pending_confirmation baru
--}}
<div wire:poll.30s="checkNewOrders" class="hidden">
    {{-- Component ini tersembunyi, hanya untuk background polling --}}
</div>
