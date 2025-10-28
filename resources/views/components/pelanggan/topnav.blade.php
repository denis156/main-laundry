<nav class="fixed top-0 left-0 right-0 navbar bg-primary text-primary-content px-4 z-50 shadow-lg">
    <div class="navbar-start">
        {{-- Theme Toggle --}}
        <x-theme-toggle class="btn btn-circle" />
    </div>

    <div class="navbar-center">
        <span class="font-bold text-lg">{{ config('app.name') }}</span>
    </div>

    <div class="navbar-end">
        @auth('customer')
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-outline btn-circle avatar">
                    <div class="w-10 rounded-full">
                        <img src="{{ Auth::guard('customer')->user()->getFilamentAvatarUrl() }}"
                            alt="{{ Auth::guard('customer')->user()->name }}" />
                    </div>
                </div>
                <div tabindex="0" class="card card-compact dropdown-content bg-base-200 z-50 mt-1 w-64 shadow-lg">
                    <div class="card-body text-base-content">
                        {{-- User Info --}}
                        <div class="text-center pb-2">
                            <h3 class="font-semibold text-lg">{{ Auth::guard('customer')->user()->name }}</h3>
                            <div class="flex justify-center gap-2 mt-2">
                                @php
                                    $customerId = Auth::guard('customer')->user()->id;
                                    $completedCount = \App\Models\Transaction::where('customer_id', $customerId)->where('workflow_status', 'delivered')->count();
                                    $cancelledCount = \App\Models\Transaction::where('customer_id', $customerId)->where('workflow_status', 'cancelled')->count();
                                @endphp
                                <x-badge
                                    value="{{ $completedCount }} Pesanan Selesai"
                                    class="badge-xs badge-success" />
                                <x-badge
                                    value="{{ $cancelledCount }} Pesanan Batal"
                                    class="badge-xs badge-error" />
                            </div>
                        </div>

                        <div class="divider my-0"></div>

                        {{-- Contact Info --}}
                        <div class="space-y-1.5">
                            @if(Auth::guard('customer')->user()->email)
                                <div class="flex items-center gap-2">
                                    <x-icon name="solar.letter-bold-duotone" class="w-4 h-4 text-primary shrink-0" />
                                    <span class="text-xs truncate">{{ Auth::guard('customer')->user()->email }}</span>
                                </div>
                            @endif
                            <div class="flex items-center gap-2">
                                <x-icon name="solar.phone-bold-duotone" class="w-4 h-4 text-secondary shrink-0" />
                                <span class="text-xs">{{ Auth::guard('customer')->user()->phone }}</span>
                            </div>
                            @if(Auth::guard('customer')->user()->member)
                                <div class="flex items-center gap-2">
                                    <x-icon name="solar.star-bold-duotone" class="w-4 h-4 text-warning shrink-0" />
                                    <span class="text-xs font-semibold">Member</span>
                                </div>
                            @endif
                        </div>

                        <div class="divider my-0"></div>

                        {{-- Logout Button --}}
                        <form method="POST" action="{{ route('pelanggan.logout') }}" id="logout-form">
                            @csrf
                            <x-button type="submit" label="Keluar" class="btn-sm btn-error btn-block"
                                icon="solar.logout-linear" />
                        </form>
                    </div>
                </div>
            </div>
        @else
            <div class="avatar avatar-placeholder">
                <div class="bg-neutral text-neutral-content w-10 rounded-full">
                    <span class="text-md">?</span>
                </div>
            </div>
        @endauth
    </div>
</nav>
