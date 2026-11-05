<x-app-layout>
    <x-slot name="title">Kelola Pengguna</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="space-y-1">
                <h1 class="text-xl font-semibold text-text leading-tight">Kelola Pengguna</h1>
                <p class="text-xs text-text-muted">Daftar akun pengguna sistem klinik, peran, dan pengaturan akses.</p>
            </div>
            <a href="{{ route('admin.user.create') }}" class="w-full sm:w-auto">
                <x-primary-button class="w-full sm:w-auto">
                    Pengguna Baru
                </x-primary-button>
            </a>
        </div>
    </x-slot>

    <x-datatable />

    <div class="px-4 py-4 sm:px-6 sm:py-6 space-y-6 max-w-7xl mx-auto">
        {{-- Stat Cards Grid (Template A) --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card label="Total Pengguna" :value="$users->count()" status="default" />
            <x-stat-card label="Admin" :value="$users->where('role', 'admin')->count()" status="default" />
            <x-stat-card label="Dokter" :value="$users->where('role', 'dokter')->count()" status="default" />
            <x-stat-card label="Resepsionis" :value="$users->where('role', 'resepsionis')->count()" status="default" />
        </div>

        {{-- Alert --}}
        @if (session('status'))
            <div class="bg-primary-50 border border-primary-200 text-primary-900 px-4 py-3 rounded-md text-sm font-medium">
                {{ session('status') }}
            </div>
        @endif

        {{-- Table Card --}}
        <x-table-container>
            <table class="datatable w-full text-sm">
                <thead class="bg-bg border-b border-border">
                    <tr class="text-left text-text-muted">
                        <th class="px-3 py-3 pl-4 font-medium">Nama</th>
                        <th class="px-3 py-3 font-medium">Email</th>
                        <th class="px-3 py-3 font-medium">Peran</th>
                        <th class="px-3 py-3 pr-4 font-medium text-right" data-orderable="false">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-subtle">
                    @forelse ($users as $u)
                        <tr class="hover:bg-primary-50/40 transition-colors">
                            <td class="px-3 py-3 pl-4 font-medium text-text">
                                {{ $u->name }}
                                @if ($u->id === auth()->id())
                                    <span class="ml-1 text-xs text-text-muted font-normal">(Anda)</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-text-muted font-mono text-xs sm:text-sm">{{ $u->email }}</td>
                            <td class="px-3 py-3">
                                @php
                                    $roleStatus = match ($u->role) {
                                        'admin' => 'neutral',
                                        'dokter' => 'info',
                                        'resepsionis' => 'neutral',
                                        default => 'neutral',
                                    };
                                @endphp
                                <x-status-pill :status="$roleStatus" :label="ucfirst($u->role)" @class([
                                    '!bg-primary-50 !text-primary-700 !border-primary-200' => $u->role === 'admin',
                                ]) />
                                @if ($u->role === 'dokter')
                                    <div class="text-xs text-text-muted mt-1">
                                        Poli: <span class="font-medium text-text">{{ $u->dokter?->poli?->nama ?? 'Belum Ditugaskan' }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-3 py-3 pr-4 text-right">
                                <div class="inline-flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.user.edit', $u) }}" class="text-primary-700 hover:underline text-sm font-medium">
                                        Edit
                                    </a>
                                    @if ($u->id !== auth()->id())
                                        <button
                                            type="button"
                                            x-data=""
                                            x-on:click.prevent="$dispatch('open-modal', 'confirm-delete-user-{{ $u->id }}')"
                                            class="text-danger hover:underline text-sm font-medium"
                                        >
                                            Hapus
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-12 text-center text-text-muted text-sm">
                                Belum ada data pengguna.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-table-container>

        {{-- Confirm Deletion Dialogs --}}
        @foreach ($users as $u)
            @if ($u->id !== auth()->id())
                <x-confirm-dialog
                    :name="'confirm-delete-user-' . $u->id"
                    title="Konfirmasi Hapus Pengguna"
                    :item="$u->name . ' (' . $u->email . ')'"
                    :action="route('admin.user.destroy', $u)"
                    method="DELETE"
                    confirm-text="Hapus Pengguna"
                >
                    Apakah Anda yakin ingin menghapus akun pengguna ini? Tindakan ini akan menghapus akses pengguna dari sistem klinik secara permanen.
                </x-confirm-dialog>
            @endif
        @endforeach
    </div>
</x-app-layout>
