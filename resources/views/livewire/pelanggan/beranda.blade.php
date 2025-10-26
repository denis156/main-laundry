<section class="bg-base-100">
    <x-header icon="solar.home-bold-duotone" icon-classes="text-primary w-6 h-6" title="Beranda"
        subtitle="Selamat datang di {{ config('app.name') }}" separator />

    <div class="space-y-4">
        {{-- Stats Cards using DaisyUI stats component --}}
        <div class="stats stats-vertical lg:stats-horizontal shadow-lg hover:shadow-xl transition-shadow w-full">
            <div class="stat bg-warning">
                <div class="stat-figure text-base-content">
                    <x-icon name="solar.clock-circle-bold-duotone" class="inline-block h-8 stroke-current" />
                </div>
                <div class="stat-title text-base-content">Pesanan Aktif</div>
                <div class="stat-value text-base-content">2</div>
                <div class="stat-desc text-base-content">Sedang diproses</div>
            </div>
            <div class="stat bg-success">
                <div class="stat-figure text-base-content">
                    <x-icon name="solar.check-circle-bold-duotone" class="inline-block h-8 stroke-current" />
                </div>
                <div class="stat-title text-base-content">Selesai</div>
                <div class="stat-value text-base-content">10</div>
                <div class="stat-desc text-base-content">Total pesanan selesai</div>
            </div>
        </div>

        {{-- Welcome Card --}}
        <x-card class="bg-base-300 shadow-lg hover:shadow-xl transition-shadow" title="Hai Ahmad"
            subtitle="Selamat datang kembali!" shadow separator>
            <x-slot:menu>
                <x-badge value="Member Silver" class="badge-primary badge-xs md:badge-sm" />
            </x-slot:menu>
            <x-avatar image="https://ui-avatars.com/api/?name=Ahmad+Rizki&background=3b82f6&color=fff" class="w-24">
                <x-slot:title class="text-xl text-base-content font-bold pl-2">
                    Ahmad Rizki
                </x-slot:title>

                <x-slot:subtitle class="grid gap-0 mt-2 pl-2 text-xs md:text-sm">
                    <p class="text-secondary font-mono">ahmad.rizki@email.com</p>
                    <p class="text-secondary font-mono">+62 812-3456-7890</p>
                </x-slot:subtitle>
            </x-avatar>
        </x-card>

        {{-- Quick Actions --}}
        <x-card class="bg-base-300 shadow-lg hover:shadow-xl transition-shadow" body-class="grid grid-cols-2 gap-4"
            title="Aksi Cepat" subtitle="Pilih aksi yang ingin kamu lakukan sekarang" shadow separator>
            <x-button link="#" icon="solar.add-circle-bold-duotone" label="Pesan Sekarang"
                class="btn-accent btn-sm btn-block col-span-2" />
            <x-button link="{{ route('pelanggan.pesanan') }}" icon="solar.bill-list-bold-duotone"
                label="Lihat Pesanan" class="btn-primary btn-sm" />
            <x-button link="#" icon="solar.ticket-bold-duotone" label="Promo Saya" class="btn-success btn-sm" />
        </x-card>

        {{-- Pesanan Aktif --}}
        <div class="space-y-3">
            {{-- Active Order 1 --}}
            <div class="card bg-base-300 shadow-lg hover:shadow-xl transition-shadow">
                <div class="card-body p-4">
                    {{-- Header: Invoice & Status --}}
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="font-bold text-primary text-lg">
                                #ORD-002
                            </h3>
                            <p class="text-xs text-base-content/60">
                                18 Okt 2025, 10:00
                            </p>
                        </div>
                        <span class="badge badge-info gap-1">
                            Sedang Dicuci
                        </span>
                    </div>

                    <div class="divider my-2"></div>

                    {{-- Order Info --}}
                    <div class="bg-base-200 rounded-lg p-3 space-y-2">
                        {{-- Layanan --}}
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-base-content/70">Layanan</span>
                            <span class="font-semibold">Cuci Kering</span>
                        </div>

                        {{-- Berat --}}
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-base-content/70">Berat</span>
                            <span class="font-semibold">5 kg</span>
                        </div>

                        <div class="divider my-1"></div>

                        {{-- Total --}}
                        <div>
                            <p class="text-xs text-base-content/70 mb-1">Total Pembayaran</p>
                            <p class="text-sm font-semibold text-primary">Rp 20.000</p>
                        </div>
                    </div>

                    {{-- Action Button --}}
                    <div class="mt-3">
                        <a href="{{ route('pelanggan.pesanan.detail', 2) }}" class="btn btn-primary btn-sm w-full">
                            <x-icon name="solar.bill-list-bold-duotone" class="w-4 h-4" />
                            Detail Pesanan
                        </a>
                    </div>
                </div>
            </div>

            {{-- Active Order 2 --}}
            <div class="card bg-base-300 shadow-lg hover:shadow-xl transition-shadow">
                <div class="card-body p-4">
                    {{-- Header: Invoice & Status --}}
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="font-bold text-primary text-lg">
                                #ORD-001
                            </h3>
                            <p class="text-xs text-base-content/60">
                                20 Okt 2025, 14:30
                            </p>
                        </div>
                        <span class="badge badge-warning gap-1">
                            Menunggu Penjemputan
                        </span>
                    </div>

                    <div class="divider my-2"></div>

                    {{-- Kurir Info --}}
                    <div class="flex items-center gap-3 mb-2">
                        <div class="avatar avatar-placeholder">
                            <div
                                class="bg-primary text-primary-content w-10 h-10 flex items-center justify-center rounded-full">
                                <span class="text-sm font-semibold">BS</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold">Budi Santoso</p>
                            <p class="text-xs text-base-content/60">Kurir</p>
                        </div>
                    </div>

                    {{-- Order Info --}}
                    <div class="bg-base-200 rounded-lg p-3 space-y-2">
                        {{-- Layanan --}}
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-base-content/70">Layanan</span>
                            <span class="font-semibold">Cuci Komplit + Setrika</span>
                        </div>

                        {{-- Berat --}}
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-base-content/70">Berat</span>
                            <span class="font-semibold">3 kg</span>
                        </div>

                        <div class="divider my-1"></div>

                        {{-- Total --}}
                        <div>
                            <p class="text-xs text-base-content/70 mb-1">Total Pembayaran</p>
                            <p class="text-sm font-semibold text-primary">Rp 15.000</p>
                        </div>
                    </div>

                    {{-- Action Button --}}
                    <div class="mt-3">
                        <a href="{{ route('pelanggan.pesanan.detail', 1) }}" class="btn btn-primary btn-sm w-full">
                            <x-icon name="solar.bill-list-bold-duotone" class="w-4 h-4" />
                            Detail Pesanan
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- View All Orders Button --}}
        <x-button link="{{ route('pelanggan.pesanan') }}" label="Lihat Semua Pesanan" icon="o-arrow-right"
            class="btn-block btn-sm btn-outline" />

        {{-- Layanan Kami --}}
        <x-card class="bg-base-300 shadow-lg hover:shadow-xl transition-shadow" title="Layanan Kami"
            subtitle="Pilih layanan sesuai kebutuhan kamu" shadow separator>
            <div class="grid grid-cols-2 gap-3">
                <div class="p-4 bg-base-200 rounded-lg text-center hover:shadow-md transition-all cursor-pointer">
                    <x-icon name="solar.washing-machine-bold-duotone" class="w-12 h-12 mx-auto text-primary mb-2" />
                    <p class="font-bold text-sm mb-1">Cuci Kering</p>
                    <p class="text-xs text-base-content/60">Mulai dari Rp 4.000/kg</p>
                </div>
                <div class="p-4 bg-base-200 rounded-lg text-center hover:shadow-md transition-all cursor-pointer">
                    <x-icon name="solar.t-shirt-bold-duotone" class="w-12 h-12 mx-auto text-success mb-2" />
                    <p class="font-bold text-sm mb-1">Cuci Setrika</p>
                    <p class="text-xs text-base-content/60">Mulai dari Rp 5.000/kg</p>
                </div>
                <div class="p-4 bg-base-200 rounded-lg text-center hover:shadow-md transition-all cursor-pointer">
                    <x-icon name="solar.hanger-bold-duotone" class="w-12 h-12 mx-auto text-warning mb-2" />
                    <p class="font-bold text-sm mb-1">Setrika Saja</p>
                    <p class="text-xs text-base-content/60">Mulai dari Rp 3.000/kg</p>
                </div>
                <div class="p-4 bg-base-200 rounded-lg text-center hover:shadow-md transition-all cursor-pointer">
                    <x-icon name="solar.star-bold-duotone" class="w-12 h-12 mx-auto text-info mb-2" />
                    <p class="font-bold text-sm mb-1">Premium</p>
                    <p class="text-xs text-base-content/60">Mulai dari Rp 7.000/kg</p>
                </div>
            </div>
        </x-card>
    </div>
</section>
