<x-app-layout>
    <x-slot name="title">Log Aktivitas</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text leading-tight">Log Aktivitas</h2>
    </x-slot>

    <x-datatable />

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-primary-50 border border-primary-100 text-primary-900 px-4 py-3 rounded-md text-sm">{{ session('status') }}</div>
            @endif

            <form method="GET" action="{{ route('admin.log.index') }}" class="bg-surface border border-border shadow-sm rounded-lg p-4 flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-45">
                    <x-input-label value="Cari user / halaman" />
                    <x-text-input name="q" value="{{ $q }}" placeholder="Nama user, /path, route" class="mt-1 w-full" />
                </div>
                <div>
                    <x-input-label value="Status" />
                    <x-select-input name="status" class="mt-1 block w-full">
                        <option value="">Semua</option>
                        <option value="200" @selected($status === '200')>200 OK</option>
                        <option value="302" @selected($status === '302')>302 Redirect</option>
                        <option value="403" @selected($status === '403')>403 Ditolak</option>
                        <option value="404" @selected($status === '404')>404 Tidak Ada</option>
                        <option value="419" @selected($status === '419')>419 Session</option>
                        <option value="422" @selected($status === '422')>422 Validasi</option>
                        <option value="500" @selected($status === '500')>500 Error</option>
                    </x-select-input>
                </div>
                <x-primary-button type="submit">Filter</x-primary-button>
                <a href="{{ route('admin.log.index') }}"><x-secondary-button type="button">Reset</x-secondary-button></a>
            </form>

            <div class="bg-surface border border-border shadow-sm sm:rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="datatable w-full text-sm" data-default-order="0,desc">
                        <thead class="bg-bg border-b border-border">
                            <tr class="text-left text-text-muted">
                                <th class="px-6 py-3 font-medium">Waktu</th>
                                <th class="px-6 py-3 font-medium">User</th>
                                <th class="px-6 py-3 font-medium">Role</th>
                                <th class="px-6 py-3 font-medium">Aksi</th>
                                <th class="px-6 py-3 font-medium">Halaman</th>
                                <th class="px-6 py-3 font-medium">Status</th>
                                <th class="px-6 py-3 font-medium" data-orderable="false"><span class="sr-only">Keterangan</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach ($logs as $log)
                                <tr class="hover:bg-primary-50/40 {{ $log->status >= 400 ? 'bg-red-50/40' : '' }}">
                                    <td class="px-6 py-3 font-mono text-xs text-text-muted" data-order="{{ $log->created_at->format('Y-m-d H:i:s') }}">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                                    <td class="px-6 py-3 font-medium text-text">{{ $log->user_name ?? 'Tamu' }}</td>
                                    <td class="px-6 py-3 text-text-muted">{{ $log->role ?? '-' }}</td>
                                    <td class="px-6 py-3">
                                        <span class="text-xs px-2 py-1 rounded border font-mono font-medium {{ $log->method === 'GET' ? 'bg-slate-50 text-slate-600 border-slate-200' : ($log->method === 'DELETE' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-amber-50 text-amber-700 border-amber-200') }}">{{ $log->method }}</span>
                                    </td>
                                    <td class="px-6 py-3 font-mono text-xs text-text-muted">{{ $log->path }}</td>
                                    <td class="px-6 py-3">
                                        <span class="text-xs px-2 py-1 rounded-full border font-medium {{ $log->status < 400 ? 'bg-green-50 text-success border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">{{ $log->status }}</span>
                                    </td>
                                    <td class="px-6 py-3 text-xs text-red-600">{{ $log->error }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
