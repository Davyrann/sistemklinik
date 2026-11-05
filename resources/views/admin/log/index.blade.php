<x-app-layout>
    <x-slot name="title">Log Aktivitas</x-slot>
    <x-slot name="header">
        <div class="space-y-1">
            <h1 class="text-xl font-semibold text-text leading-tight">Log Aktivitas</h1>
            <p class="text-xs text-text-muted">Audit jejak aktivitas sistem, permintaan HTTP, dan riwayat kesalahan.</p>
        </div>
    </x-slot>

    <x-datatable />

    <div class="px-4 py-4 sm:px-6 sm:py-6 space-y-6 max-w-7xl mx-auto">
        {{-- Stat Cards Grid (Template A) --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card label="Total Log" :value="$logs->count()" status="default" />
            <x-stat-card label="Berhasil (2xx)" :value="$logs->whereBetween('status', [200, 299])->count()" status="success" />
            <x-stat-card label="Pengalihan (3xx)" :value="$logs->whereBetween('status', [300, 399])->count()" status="default" />
            <x-stat-card label="Error / Masalah (&ge;400)" :value="$logs->where('status', '>=', 400)->count()" :status="$logs->where('status', '>=', 400)->count() > 0 ? 'danger' : 'default'" />
        </div>

        {{-- Alert --}}
        @if (session('status'))
            <div class="bg-primary-50 border border-primary-200 text-primary-900 px-4 py-3 rounded-md text-sm font-medium">
                {{ session('status') }}
            </div>
        @endif

        {{-- Table Card with Toolbar --}}
        <x-table-container>
            {{-- Toolbar: Filter & Search --}}
            <form method="GET" action="{{ route('admin.log.index') }}" class="px-4 py-3 border-b border-border bg-bg/40 flex flex-col sm:flex-row gap-3 items-stretch sm:items-end">
                <div class="flex-1 min-w-[200px] space-y-1">
                    <x-input-label value="Cari user / URL / route" class="text-xs" />
                    <x-text-input name="q" value="{{ $q }}" placeholder="Nama user, /path, route" class="w-full text-xs" />
                </div>
                <div class="w-full sm:w-44 space-y-1">
                    <x-input-label value="Status HTTP" class="text-xs" />
                    <x-select-input name="status" class="w-full text-xs">
                        <option value="">Semua Status</option>
                        <option value="200" @selected($status === '200')>200 OK</option>
                        <option value="302" @selected($status === '302')>302 Redirect</option>
                        <option value="403" @selected($status === '403')>403 Ditolak</option>
                        <option value="404" @selected($status === '404')>404 Tidak Ada</option>
                        <option value="419" @selected($status === '419')>419 Session</option>
                        <option value="422" @selected($status === '422')>422 Validasi</option>
                        <option value="500" @selected($status === '500')>500 Error</option>
                    </x-select-input>
                </div>
                <div class="flex items-center gap-2 pt-1 sm:pt-0">
                    <x-primary-button type="submit" class="flex-1 sm:flex-none">
                        Filter
                    </x-primary-button>
                    <a href="{{ route('admin.log.index') }}" class="flex-1 sm:flex-none">
                        <x-secondary-button type="button" class="w-full">
                            Reset
                        </x-secondary-button>
                    </a>
                </div>
            </form>

            <table class="datatable w-full text-sm" data-default-order="0,desc">
                <thead class="bg-bg border-b border-border">
                    <tr class="text-left text-text-muted">
                        <th class="px-3 py-3 pl-4 font-medium">Waktu</th>
                        <th class="px-3 py-3 font-medium">Pengguna</th>
                        <th class="px-3 py-3 font-medium">Peran</th>
                        <th class="px-3 py-3 font-medium">Metode</th>
                        <th class="px-3 py-3 font-medium">Halaman</th>
                        <th class="px-3 py-3 font-medium">Status</th>
                        <th class="px-3 py-3 pr-4 font-medium" data-orderable="false">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-subtle">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-primary-50/40 transition-colors {{ $log->status >= 400 ? 'bg-red-50/20' : '' }}">
                            <td class="px-3 py-3 pl-4 font-mono text-xs text-text-muted whitespace-nowrap" data-order="{{ $log->created_at->format('Y-m-d H:i:s') }}">
                                {{ $log->created_at->format('d M Y H:i:s') }}
                            </td>
                            <td class="px-3 py-3 font-medium text-text whitespace-nowrap">
                                {{ $log->user_name ?? 'Tamu' }}
                            </td>
                            <td class="px-3 py-3 text-text-muted text-xs whitespace-nowrap">
                                {{ $log->role ? ucfirst($log->role) : '-' }}
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap">
                                @php
                                    $methodClass = match ($log->method) {
                                        'GET' => 'bg-slate-100 text-slate-700 border-border',
                                        'POST' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'PUT', 'PATCH' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'DELETE' => 'bg-red-50 text-danger border-red-200',
                                        default => 'bg-slate-100 text-slate-700 border-border',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded border text-xs font-mono font-medium {{ $methodClass }}">
                                    {{ $log->method }}
                                </span>
                            </td>
                            <td class="px-3 py-3 font-mono text-xs text-text-muted max-w-[200px] truncate" title="{{ $log->path }}">
                                {{ $log->path }}
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap">
                                @php
                                    $statusType = match (true) {
                                        $log->status < 300 => 'success',
                                        $log->status < 400 => 'info',
                                        $log->status < 500 => 'warning',
                                        default => 'danger',
                                    };
                                @endphp
                                <x-status-pill :status="$statusType" :label="(string) $log->status" class="font-mono" />
                            </td>
                            <td class="px-3 py-3 pr-4 text-xs text-danger max-w-[200px] truncate" title="{{ $log->error }}">
                                {{ $log->error ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-text-muted text-sm">
                                Tidak ada log aktivitas yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-table-container>
    </div>
</x-app-layout>
