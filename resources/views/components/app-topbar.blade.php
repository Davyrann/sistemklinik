<header class="h-14 bg-surface border-b border-border sticky top-0 z-20 px-4 md:px-6 flex items-center justify-between gap-4">
    <!-- Left: Hamburger button (mobile) + Brand logo/title -->
    <div class="flex items-center gap-3">
        <button
            type="button"
            @click="sidebarOpen = true"
            class="size-11 flex items-center justify-center md:hidden text-text-muted hover:text-text hover:bg-slate-100 rounded-md focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 transition"
            aria-label="Buka menu navigasi"
        >
            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>

        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
            <img src="{{ asset('logo.svg') }}" alt="Logo Klinik" class="h-8 w-auto">
            <span class="font-semibold text-base text-text">KlinikKu</span>
        </a>
    </div>

    <!-- Center/Right: Desktop search input with '/' shortcut, live date/time, profile dropdown -->
    <div class="flex items-center gap-3 lg:gap-4">
        @auth
            <!-- Global Search Input -->
            <form
                method="GET"
                action="{{ in_array(auth()->user()->role, ['resepsionis', 'admin'], true) ? route('pasien.index') : route('dashboard') }}"
                class="hidden md:flex items-center"
                x-data
                @keydown.window="if ($event.key === '/' && $event.target.tagName !== 'INPUT' && $event.target.tagName !== 'TEXTAREA' && $event.target.tagName !== 'SELECT') { $event.preventDefault(); $refs.globalSearchInput?.focus(); }"
            >
                <div class="relative w-60 lg:w-72">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-text-subtle">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </div>
                    <input
                        x-ref="globalSearchInput"
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari nama / No. RM / NIK"
                        class="h-9 w-full pl-9 pr-8 text-sm bg-bg border border-border rounded-md text-text placeholder:text-text-subtle focus:bg-surface focus:outline-none focus:border-primary-600 focus:ring-1 focus:ring-primary-600 transition"
                        aria-label="Cari nama / No. RM / NIK"
                    />
                    <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">
                        <kbd class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-mono font-medium text-text-muted bg-surface border border-border rounded shadow-xs">
                            /
                        </kbd>
                    </div>
                </div>
            </form>

            <!-- Live Date & Time Display -->
            <div
                x-data="{
                    time: '',
                    date: '',
                    update() {
                        const now = new Date();
                        this.time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false });
                        this.date = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                    }
                }"
                x-init="update(); setInterval(() => update(), 1000)"
                class="hidden lg:flex items-center gap-2 text-xs font-mono text-text-muted pl-2 border-l border-border"
                aria-label="Waktu sistem saat ini"
            >
                <svg class="size-4 text-text-subtle" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <span x-text="date">{{ now()->locale('id')->isoFormat('D MMM Y') }}</span>
                <span class="text-border">|</span>
                <span x-text="time">{{ now()->format('H:i') }}</span>
            </div>

            <!-- Profile Dropdown -->
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button
                        type="button"
                        class="inline-flex items-center h-9 px-3 border border-border text-sm font-medium rounded-md text-text bg-surface hover:bg-slate-50 focus:outline-none focus:ring-1 focus:ring-primary-600 transition"
                        aria-label="Menu profil pengguna"
                    >
                        <div class="text-left leading-tight">
                            <span class="font-medium text-text block truncate max-w-[120px] sm:max-w-none">{{ auth()->user()->name }}</span>
                            <span class="text-text-muted text-[11px] block capitalize font-mono">({{ auth()->user()->role }})</span>
                        </div>
                        <div class="ms-2 text-text-muted">
                            <svg class="size-4 fill-current" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <div class="px-4 py-2 border-b border-border">
                        <p class="text-xs font-medium text-text-muted">Login sebagai</p>
                        <p class="text-sm font-semibold text-text truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-text-muted capitalize font-mono">{{ auth()->user()->role }}</p>
                    </div>

                    <x-dropdown-link :href="route('profile.edit')">
                        {{ __('Profil Saya') }}
                    </x-dropdown-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Keluar') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        @endauth
    </div>
</header>
