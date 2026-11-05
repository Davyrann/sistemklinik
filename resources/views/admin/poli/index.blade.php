<x-app-layout>
    <x-slot name="title">Master Data Unit Poli</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-text leading-tight">Master Data Unit Poli</h1>
                <p class="text-xs text-text-muted mt-0.5">Kelola data poliklinik dan penugasan dokter di setiap unit layanan</p>
            </div>
            <a href="{{ route('admin.poli.create') }}">
                <x-primary-button>Poli Baru</x-primary-button>
            </a>
        </div>
    </x-slot>

    <x-datatable />

    @if (session('status'))
        <div class="bg-primary-50 border border-primary-200 text-primary-900 px-4 py-3 rounded-md text-sm font-medium" role="status">
            {{ session('status') }}
        </div>
    @endif

    {{-- Stat Cards Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card label="Total Unit Poli" :value="$totalPoli" />
        <x-stat-card label="Poli Aktif" :value="$poliAktif" status="success" />
        <x-stat-card label="Total Dokter Bertugas" :value="$totalDokter" status="info" />
        <x-stat-card label="Total Kunjungan" :value="$totalKunjungan" />
    </div>

    {{-- Table Card (Template A) --}}
    <x-table-container>
        <table class="datatable w-full text-sm">
            <thead class="bg-bg border-b border-border sticky top-0">
                <tr class="text-left text-text-muted text-xs uppercase tracking-wider font-semibold">
                    <th scope="col" class="pl-4 sm:pl-6 pr-3 py-3 font-medium">Kode</th>
                    <th scope="col" class="px-3 py-3 font-medium">Nama Poli</th>
                    <th scope="col" class="px-3 py-3 font-medium">Dokter yang Bertugas</th>
                    <th scope="col" class="px-3 py-3 font-medium text-right">Kunjungan</th>
                    <th scope="col" class="px-3 py-3 font-medium">Status</th>
                    <th scope="col" class="pl-3 pr-4 sm:pr-6 py-3 font-medium text-right" data-orderable="false">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border-subtle">
                @forelse ($polis as $poli)
                    <tr class="hover:bg-primary-50/40 transition-colors">
                        <td class="pl-4 sm:pl-6 pr-3 py-3 font-mono font-semibold text-text whitespace-nowrap">
                            {{ $poli->kode }}
                        </td>
                        <td class="px-3 py-3">
                            <div class="font-medium text-text">{{ $poli->nama }}</div>
                            @if ($poli->deskripsi)
                                <div class="text-xs text-text-muted mt-0.5 line-clamp-1">{{ $poli->deskripsi }}</div>
                            @endif
                        </td>
                        <td class="px-3 py-3">
                            @if ($poli->dokters->isNotEmpty())
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($poli->dokters as $dok)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full border text-xs font-medium font-sans {{ $dok->is_aktif ? 'bg-primary-50 text-primary-700 border-primary-100' : 'bg-slate-50 text-text-muted border-border' }}">
                                            {{ $dok->nama }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-xs text-text-subtle italic">Belum ada dokter ditugaskan</span>
                            @endif
                        </td>
                        <td class="px-3 py-3 font-mono text-text text-right whitespace-nowrap">
                            {{ $poli->kunjungans_count }}
                        </td>
                        <td class="px-3 py-3 whitespace-nowrap">
                            <x-status-pill :status="$poli->is_aktif ? 'success' : 'neutral'" :label="$poli->is_aktif ? 'Aktif' : 'Nonaktif'" />
                        </td>
                        <td class="pl-3 pr-4 sm:pr-6 py-3 whitespace-nowrap text-right">
                            <div class="inline-flex items-center justify-end gap-3">
                                <a href="{{ route('admin.poli.show', $poli) }}" class="text-primary-700 hover:underline text-sm font-medium">
                                    Lihat Dokter
                                </a>
                                <a href="{{ route('admin.poli.edit', $poli) }}" class="text-text-muted hover:text-text hover:underline text-sm font-medium">
                                    Edit
                                </a>
                                @if ($poli->is_aktif)
                                    <button
                                        type="button"
                                        x-data=""
                                        x-on:click.prevent="$dispatch('open-modal', 'deactivate-poli-{{ $poli->id }}')"
                                        class="text-danger hover:underline text-sm font-medium"
                                    >
                                        Nonaktifkan
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 sm:px-6 py-12 text-center text-text-muted text-sm">
                            Belum ada data unit poli terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-container>

    {{-- Deactivation Confirmation Dialogs --}}
    @foreach ($polis as $poli)
        @if ($poli->is_aktif)
            <x-confirm-dialog
                :name="'deactivate-poli-' . $poli->id"
                title="Konfirmasi Nonaktifkan Poli"
                :item="$poli->nama . ' (Kode: ' . $poli->kode . ')'"
                :action="route('admin.poli.destroy', $poli)"
                method="DELETE"
                confirm-text="Nonaktifkan Poli"
            >
                Apakah Anda yakin ingin menonaktifkan unit poli ini? Unit poli yang nonaktif tidak dapat dipilih saat pendaftaran kunjungan baru.
            </x-confirm-dialog>
        @endif
    @endforeach
</x-app-layout>
