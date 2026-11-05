<x-app-layout>
    <x-slot name="title">Poli {{ $poli->nama }} — Dokter Bertugas</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-semibold text-text leading-tight">{{ $poli->nama }}</h1>
                    <span class="font-mono text-xs font-semibold px-2 py-0.5 rounded bg-bg border border-border text-text">
                        {{ $poli->kode }}
                    </span>
                    <x-status-pill :status="$poli->is_aktif ? 'success' : 'neutral'" :label="$poli->is_aktif ? 'Aktif' : 'Nonaktif'" />
                </div>
                <p class="text-xs text-text-muted mt-1">{{ $poli->deskripsi ?: 'Tidak ada deskripsi layanan' }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.poli.edit', $poli) }}">
                    <x-secondary-button>Edit Poli</x-secondary-button>
                </a>
                <a href="{{ route('admin.user.create') }}">
                    <x-primary-button>+ Tambah Dokter</x-primary-button>
                </a>
            </div>
        </div>
    </x-slot>

    <x-datatable />

    @if (session('status'))
        <div class="bg-primary-50 border border-primary-200 text-primary-900 px-4 py-3 rounded-md text-sm font-medium" role="status">
            {{ session('status') }}
        </div>
    @endif

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
        <x-stat-card label="Total Dokter di Poli Ini" :value="$poli->dokters->count()" status="info" />
        <x-stat-card label="Dokter Aktif Bertugas" :value="$poli->dokters->where('is_aktif', true)->count()" status="success" />
        <x-stat-card label="Total Riwayat Kunjungan" :value="$poli->kunjungans_count" />
    </div>

    {{-- Doctors in this Poli Table Card --}}
    <x-table-container :scrollable="false">
        <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-border flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="text-base font-semibold text-text">Daftar Dokter Bertugas di {{ $poli->nama }}</h2>
                <p class="text-xs text-text-muted">Dokter yang memiliki hak akses memeriksa antrean pasien pada unit layanan ini</p>
            </div>
            <span class="text-xs font-mono text-text-muted">
                {{ $poli->dokters->count() }} dokter terdaftar
            </span>
        </div>

        <div class="overflow-x-auto w-full" style="-webkit-overflow-scrolling: touch;">
            <table class="datatable w-full text-sm">
                <thead class="bg-bg border-b border-border sticky top-0">
                    <tr class="text-left text-text-muted text-xs uppercase tracking-wider font-semibold">
                        <th scope="col" class="pl-4 sm:pl-6 pr-3 py-3 font-medium">Nama Dokter</th>
                        <th scope="col" class="px-3 py-3 font-medium">NIP / SIP</th>
                        <th scope="col" class="px-3 py-3 font-medium">Spesialisasi</th>
                        <th scope="col" class="px-3 py-3 font-medium">Akun Pengguna</th>
                        <th scope="col" class="px-3 py-3 font-medium">No. Telepon</th>
                        <th scope="col" class="px-3 py-3 font-medium">Status Praktik</th>
                        <th scope="col" class="pl-3 pr-4 sm:pr-6 py-3 font-medium text-right" data-orderable="false">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-subtle">
                    @forelse ($poli->dokters as $dok)
                        <tr class="hover:bg-primary-50/40 transition-colors">
                            <td class="pl-4 sm:pl-6 pr-3 py-3 font-medium text-text whitespace-nowrap">
                                {{ $dok->nama }}
                            </td>
                            <td class="px-3 py-3 font-mono text-xs text-text-muted whitespace-nowrap">
                                {{ $dok->nip ?: '-' }}
                            </td>
                            <td class="px-3 py-3 text-text">
                                {{ $dok->spesialisasi ?: 'Dokter Umum' }}
                            </td>
                            <td class="px-3 py-3 font-mono text-xs text-text-muted whitespace-nowrap">
                                @if ($dok->user)
                                    <div>{{ $dok->user->email }}</div>
                                    <div class="text-[11px] text-text-subtle capitalize">Role: {{ $dok->user->role }}</div>
                                @else
                                    <span class="italic text-text-subtle">Tanpa akun login</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 font-mono text-xs text-text whitespace-nowrap">
                                {{ $dok->no_hp ?: '-' }}
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap">
                                <x-status-pill :status="$dok->is_aktif ? 'success' : 'neutral'" :label="$dok->is_aktif ? 'Aktif Praktik' : 'Nonaktif'" />
                            </td>
                            <td class="pl-3 pr-4 sm:pr-6 py-3 whitespace-nowrap text-right">
                                @if ($dok->user)
                                    <a href="{{ route('admin.user.edit', $dok->user) }}" class="text-primary-700 hover:underline text-sm font-medium">
                                        Edit Akun
                                    </a>
                                @else
                                    <span class="text-xs text-text-subtle">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 sm:px-6 py-12 text-center text-text-muted text-sm">
                                Belum ada dokter yang ditugaskan di poli {{ $poli->nama }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-table-container>
</x-app-layout>
