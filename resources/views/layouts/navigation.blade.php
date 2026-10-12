<nav x-data="{ open: false }" class="bg-surface border-b border-border">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('logo.svg') }}" alt="Logo Klinik" class="block h-9 w-auto">
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('resepsionis.dashboard')">
                        Dashboard
                    </x-nav-link>
                    @if (in_array(auth()->user()?->role, ['resepsionis', 'admin'], true))
                        <x-nav-link :href="route('pasien.index')" :active="request()->routeIs('pasien.*')">
                            Pasien
                        </x-nav-link>
                        <x-nav-link :href="route('kunjungan.index')" :active="request()->routeIs('kunjungan.*')">
                            Kunjungan
                        </x-nav-link>
                        <x-nav-link :href="route('tagihan.index')" :active="request()->routeIs('tagihan.*')">
                            Tagihan
                        </x-nav-link>
                        <x-nav-link :href="route('obat.index')" :active="request()->routeIs('obat.*')">
                            Obat
                        </x-nav-link>
                    @endif
                    @if (auth()->user()?->role === 'admin')
                        <x-dropdown align="left" width="48" contentClasses="py-1 bg-surface border border-border">
                            <x-slot name="trigger">
                                <button class="flex h-full w-full items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out {{ request()->routeIs('admin.*') ? 'border-primary-700 text-text focus:border-primary-600' : 'border-transparent text-text-muted hover:text-text hover:border-border focus:text-text focus:border-border' }}">
                                    Lainnya
                                    <svg class="w-3 h-3 ms-1 fill-current text-text-muted" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('admin.user.index')">
                                    Pengguna
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.log.index')">
                                    Log Aktivitas
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center gap-4 sm:ms-6">
                @if (in_array(auth()->user()?->role, ['resepsionis', 'admin'], true))
                    <form method="GET" action="{{ route('pasien.index') }}" class="flex items-center gap-2">
                        <x-text-input name="q" value="{{ request('q') }}" placeholder="Cari pasien (nama/RM/NIK)" class="py-2! w-64" />
                        <x-primary-button type="submit" class="py-2!">Cari</x-primary-button>
                    </form>
                @endif
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-text bg-surface hover:text-primary-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }} <span class="text-text-muted text-xs">({{ Auth::user()->role }})</span></div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-text-muted hover:text-text hover:bg-primary-50 focus:outline-none focus:bg-primary-50 focus:text-text transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-surface border-t border-border">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Dashboard
            </x-responsive-nav-link>
            @if (in_array(auth()->user()?->role, ['resepsionis', 'admin'], true))
                <x-responsive-nav-link :href="route('pasien.index')" :active="request()->routeIs('pasien.*')">Pasien</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('kunjungan.index')" :active="request()->routeIs('kunjungan.*')">Kunjungan</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('tagihan.index')" :active="request()->routeIs('tagihan.*')">Tagihan</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('obat.index')" :active="request()->routeIs('obat.*')">Obat</x-responsive-nav-link>
            @endif
            @if (auth()->user()?->role === 'admin')
                <x-responsive-nav-link :href="route('admin.user.index')" :active="request()->routeIs('admin.user.*')">Pengguna</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.log.index')" :active="request()->routeIs('admin.log.*')">Log</x-responsive-nav-link>
            @endif
        </div>
        <div class="pt-4 pb-1 border-t border-border">
            <div class="px-4">
                <div class="font-medium text-base text-text">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-text-muted">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">{{ __('Profile') }}</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
