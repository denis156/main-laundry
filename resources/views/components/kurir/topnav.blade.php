<nav class="fixed top-0 left-0 right-0 navbar bg-primary text-primary-content px-4 z-50 shadow-lg">
    <div class="navbar-start">
        {{-- Logout Button --}}
        <form method="POST" action="{{ route('kurir.logout') }}" id="logout-form">
            @csrf
            <button type="submit" class="btn btn-circle btn-ghost" title="Logout">
                <x-icon name="o-arrow-left-start-on-rectangle" class="w-6 h-6" />
            </button>
        </form>
    </div>

    <div class="navbar-center">
        <span class="font-bold text-lg">{{ config('app.name') }}</span>
    </div>

    <div class="navbar-end">
        @auth('courier')
            <div class="avatar">
                <div class="w-10 rounded-full">
                    <img src="{{ Auth::guard('courier')->user()->getFilamentAvatarUrl() }}"
                         alt="{{ Auth::guard('courier')->user()->name }}" />
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
