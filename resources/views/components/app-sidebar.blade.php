@php
    $role = auth()->user()?->role;
    $menuItems = [];

    if ($role === 'resepsionis') {
        $menuItems = [
            [
                'label' => 'Dashboard',
                'route' => route('dashboard'),
                'active' => request()->routeIs('dashboard') || request()->routeIs('resepsionis.dashboard'),
                'icon' => 'home',
            ],
            [
                'label' => 'Pasien',
                'route' => route('pasien.index'),
                'active' => request()->routeIs('pasien.*'),
                'icon' => 'users',
            ],
            [
                'label' => 'Kunjungan & Antrean',
                'route' => route('kunjungan.index'),
                'active' => request()->routeIs('kunjungan.*'),
                'icon' => 'queue',
            ],
            [
                'label' => 'Tagihan & Kasir',
                'route' => route('tagihan.index'),
                'active' => request()->routeIs('tagihan.*'),
                'icon' => 'billing',
            ],
            [
                'label' => 'Stok Obat',
                'route' => route('obat.index'),
                'active' => request()->routeIs('obat.*'),
                'icon' => 'medicine',
            ],
        ];
    } elseif ($role === 'dokter') {
        $menuItems = [
            [
                'label' => 'Antrean Pasien',
                'route' => route('dokter.dashboard'),
                'active' => request()->routeIs('dokter.*') || request()->routeIs('dashboard'),
                'icon' => 'stethoscope',
            ],
        ];
    } elseif ($role === 'apoteker') {
        $menuItems = [
            [
                'label' => 'Dashboard',
                'route' => route('apoteker.dashboard'),
                'active' => request()->routeIs('apoteker.dashboard') || (request()->routeIs('dashboard') && $role === 'apoteker'),
                'icon' => 'home',
            ],
            [
                'label' => 'Resep Masuk',
                'route' => route('apoteker.resep.index'),
                'active' => request()->routeIs('apoteker.resep.*'),
                'icon' => 'queue',
            ],
            [
                'label' => 'Tagihan & Kasir',
                'route' => route('tagihan.index'),
                'active' => request()->routeIs('tagihan.*'),
                'icon' => 'billing',
            ],
            [
                'label' => 'Stok Obat',
                'route' => route('apoteker.obat.index'),
                'active' => request()->routeIs('apoteker.obat.*'),
                'icon' => 'medicine',
            ],
        ];
    } elseif ($role === 'admin') {
        $menuItems = [
            [
                'label' => 'Dashboard',
                'route' => route('dashboard'),
                'active' => request()->routeIs('dashboard') || request()->routeIs('resepsionis.dashboard'),
                'icon' => 'home',
            ],
            [
                'label' => 'Pasien',
                'route' => route('pasien.index'),
                'active' => request()->routeIs('pasien.*'),
                'icon' => 'users',
            ],
            [
                'label' => 'Kunjungan & Antrean',
                'route' => route('kunjungan.index'),
                'active' => request()->routeIs('kunjungan.*'),
                'icon' => 'queue',
            ],
            [
                'label' => 'Resep & Apotek',
                'route' => route('apoteker.resep.index'),
                'active' => request()->routeIs('apoteker.resep.*'),
                'icon' => 'queue',
            ],
            [
                'label' => 'Tagihan & Kasir',
                'route' => route('tagihan.index'),
                'active' => request()->routeIs('tagihan.*'),
                'icon' => 'billing',
            ],
            [
                'label' => 'Stok Obat',
                'route' => route('obat.index'),
                'active' => request()->routeIs('obat.*'),
                'icon' => 'medicine',
            ],
            [
                'label' => 'Pemeriksaan Dokter',
                'route' => route('dokter.dashboard'),
                'active' => request()->routeIs('dokter.*'),
                'icon' => 'stethoscope',
            ],
            [
                'label' => 'Master Data Poli',
                'route' => route('admin.poli.index'),
                'active' => request()->routeIs('admin.poli.*'),
                'icon' => 'building-office',
            ],
            [
                'label' => 'Kelola Pengguna',
                'route' => route('admin.user.index'),
                'active' => request()->routeIs('admin.user.*'),
                'icon' => 'users-cog',
            ],
            [
                'label' => 'Log Aktivitas',
                'route' => route('admin.log.index'),
                'active' => request()->routeIs('admin.log.*'),
                'icon' => 'clipboard-log',
            ],
        ];
    } else {
        $menuItems = [
            [
                'label' => 'Dashboard',
                'route' => route('dashboard'),
                'active' => request()->routeIs('dashboard'),
                'icon' => 'home',
            ],
        ];
    }

    $icons = [
        'home' => '<svg class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>',
        'users' => '<svg class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>',
        'queue' => '<svg class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>',
        'billing' => '<svg class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6H2.25m0 0v8.25m0-8.25a3 3 0 0 1 3-3h13.5a3 3 0 0 1 3 3v8.25m-19.5 0A3 3 0 0 0 4.5 17.25h15a3 3 0 0 0 3-3m-19.5 0h19.5m-16.5-7.5h.008v.008H6V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm6 0h.008v.008H12V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>',
        'medicine' => '<svg class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.943A4.5 4.5 0 0 1 15.9 16.5H8.1a4.5 4.5 0 0 1-2.33-.657L5 14.5m14.8.8A2.25 2.25 0 0 1 21 17.25v.75a4.5 4.5 0 0 1-4.5 4.5h-9A4.5 4.5 0 0 1 3 18v-.75a2.25 2.25 0 0 1 1.2-1.95" /></svg>',
        'stethoscope' => '<svg class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>',
        'users-cog' => '<svg class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" /></svg>',
        'clipboard-log' => '<svg class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>',
        'building-office' => '<svg class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.75a1.5 1.5 0 0 1 1.5-1.5h1.5a1.5 1.5 0 0 1 1.5 1.5V21m6-9h.75m-.75 3h.75m-.75 3h.75" /></svg>',
    ];
@endphp

<!-- Mobile Backdrop -->
<div
    x-show="sidebarOpen"
    x-transition:enter="transition-opacity ease-linear duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-40 bg-slate-900/50 md:hidden"
    @click="sidebarOpen = false"
    aria-hidden="true"
    style="display: none;"
></div>

<!-- Mobile Off-Canvas Slide-Over Drawer -->
<aside
    x-show="sidebarOpen"
    x-transition:enter="transition ease-in-out duration-200 transform"
    x-transition:enter-start="-translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in-out duration-200 transform"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full"
    class="fixed inset-y-0 left-0 z-50 w-60 bg-surface border-r border-border flex flex-col md:hidden"
    @keydown.escape.window="sidebarOpen = false"
    aria-label="Menu navigasi mobile"
    style="display: none;"
>
    <!-- Drawer Header: Logo + Close button (min 44px) -->
    <div class="h-14 px-4 flex items-center justify-between border-b border-border shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
            <img src="{{ asset('logo.svg') }}" alt="Logo Klinik" class="h-8 w-auto">
            <span class="font-semibold text-base text-text">KlinikKu</span>
        </a>
        <button
            type="button"
            @click="sidebarOpen = false"
            class="size-11 -mr-2 flex items-center justify-center rounded-md text-text-muted hover:text-text hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 transition"
            aria-label="Tutup menu navigasi"
        >
            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Mobile Drawer Menu Items (min 44px tap target) -->
    <nav class="flex-1 overflow-y-auto p-3 space-y-1">
        @foreach ($menuItems as $item)
            <a
                href="{{ $item['route'] }}"
                class="h-11 px-3 flex items-center gap-3 rounded-r-md transition {{ $item['active'] ? 'bg-primary-50 text-primary-700 border-l-[3px] border-primary-700 font-semibold' : 'text-text hover:bg-slate-50 hover:text-primary-700 border-l-[3px] border-transparent font-medium' }}"
            >
                {!! $icons[$item['icon']] ?? '' !!}
                <span class="text-sm">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <!-- Mobile Bottom User Block (mt-auto p-3 border-t border-border) -->
    @auth
        <div class="mt-auto p-3 border-t border-border">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-text truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-text-muted capitalize font-mono">{{ auth()->user()->role }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="size-11 flex items-center justify-center rounded-md text-text-muted hover:text-danger hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-danger transition"
                        title="Keluar"
                        aria-label="Keluar"
                    >
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    @endauth
</aside>

<!-- Tablet Rail (md:w-16) & Desktop Sidebar (lg:w-60) -->
<aside
    class="hidden md:flex flex-col shrink-0 md:w-16 lg:w-60 bg-surface border-r border-border sticky top-0 h-screen z-20"
    aria-label="Navigasi Menu Utama"
>
    <!-- Logo area (height 56px / h-14) -->
    <div class="h-14 flex items-center border-b border-border md:justify-center lg:justify-start lg:px-4 shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
            <img src="{{ asset('logo.svg') }}" alt="Logo Klinik" class="h-8 w-auto">
            <span class="font-semibold text-base text-text hidden lg:inline-block">KlinikKu</span>
        </a>
    </div>

    <!-- Navigation Menu Items -->
    <nav class="flex-1 overflow-y-auto p-3 space-y-1">
        @foreach ($menuItems as $item)
            <div class="relative group">
                <a
                    href="{{ $item['route'] }}"
                    class="h-9 px-3 flex items-center gap-3 rounded-r-md transition {{ $item['active'] ? 'bg-primary-50 text-primary-700 border-l-[3px] border-primary-700 font-semibold' : 'text-text hover:bg-slate-50 hover:text-primary-700 border-l-[3px] border-transparent font-medium' }} md:justify-center lg:justify-start"
                    aria-label="{{ $item['label'] }}"
                    title="{{ $item['label'] }}"
                >
                    {!! $icons[$item['icon']] ?? '' !!}
                    <span class="hidden lg:inline text-sm truncate">{{ $item['label'] }}</span>
                </a>
                <!-- Tooltip for collapsed tablet rail (md: only) -->
                <div class="hidden md:group-hover:block lg:group-hover:hidden absolute left-full top-1/2 -translate-y-1/2 ml-2 px-2.5 py-1 bg-slate-900 text-white text-xs font-medium rounded shadow-md whitespace-nowrap z-30 pointer-events-none">
                    {{ $item['label'] }}
                </div>
            </div>
        @endforeach
    </nav>

    <!-- Bottom User Block (mt-auto p-3 border-t border-border) -->
    @auth
        <div class="mt-auto p-3 border-t border-border shrink-0">
            <!-- Tablet rail view (md:flex lg:hidden) -->
            <div class="hidden md:flex lg:hidden flex-col items-center gap-2">
                <div
                    class="size-9 rounded-full bg-primary-50 text-primary-700 flex items-center justify-center text-xs font-semibold font-mono"
                    title="{{ auth()->user()->name }} ({{ auth()->user()->role }})"
                    aria-label="{{ auth()->user()->name }}"
                >
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="size-9 flex items-center justify-center rounded-md text-text-muted hover:text-danger hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-danger transition"
                        title="Keluar"
                        aria-label="Keluar"
                    >
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Desktop view (hidden lg:flex) -->
            <div class="hidden lg:flex items-center justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-text truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-text-muted capitalize font-mono">{{ auth()->user()->role }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="size-9 flex items-center justify-center rounded-md text-text-muted hover:text-danger hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-danger transition"
                        title="Keluar"
                        aria-label="Keluar"
                    >
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    @endauth
</aside>
