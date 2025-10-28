<nav class="fixed top-0 left-0 right-0 navbar bg-primary text-primary-content px-4 z-50 shadow-lg">
    <div class="navbar-start">
        <x-theme-toggle class="btn btn-circle" />
    </div>
    <div class="navbar-center">
        <span class="font-bold text-lg">{{ config('app.name') }}</span>
    </div>
    <div class="navbar-end">
        <div class="dropdown dropdown-end">
            <div tabindex="0" role="button" class="btn btn-outline btn-circle avatar">
                <div class="w-10 rounded-full">
                    <img src="https://ui-avatars.com/api/?name=John+Doe&background=000000&color=ffffff"
                         alt="User Avatar" />
                </div>
            </div>
            <div tabindex="0" class="card card-compact dropdown-content bg-base-200 z-50 mt-1 w-64 shadow-lg">
                <div class="card-body text-base-content">
                    {{-- User Info --}}
                    <div class="text-center pb-2">
                        <h3 class="font-semibold text-lg">John Doe</h3>
                        <div class="flex justify-center gap-2 mt-2">
                            <x-badge value="5 Pesanan Selesai" class="badge-xs badge-success" />
                            <x-badge value="0 Pesanan Batal" class="badge-xs badge-error" />
                        </div>
                    </div>

                    <div class="divider my-0"></div>

                    {{-- Contact Info --}}
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2">
                            <x-icon name="solar.letter-bold-duotone" class="w-4 h-4 text-primary shrink-0" />
                            <span class="text-xs truncate">johndoe@example.com</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-icon name="solar.phone-bold-duotone" class="w-4 h-4 text-secondary shrink-0" />
                            <span class="text-xs">+62 81234567890</span>
                        </div>
                    </div>

                    <div class="divider my-0"></div>

                    {{-- Logout Button --}}
                    <button class="btn btn-error btn-sm btn-block">
                        <x-icon name="o-arrow-right-start-on-rectangle" class="w-4 h-4" />
                        Keluar
                    </button>
                </div>
            </div>
        </div>
    </div>
</nav>
