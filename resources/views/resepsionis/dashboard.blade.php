<x-app-layout>
    <x-slot name="title">Dashboard Resepsionis</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-text leading-tight">Dashboard Resepsionis</h1>
                <p class="text-xs text-text-muted mt-0.5">Kelola antrean kunjungan dan pendaftaran pasien klinik</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('pasien.create') }}">
                    <x-secondary-button>Daftar Pasien Baru</x-secondary-button>
                </a>
                <a href="{{ route('kunjungan.create') }}">
                    <x-primary-button>Buat Kunjungan</x-primary-button>
                </a>
            </div>
        </div>
    </x-slot>

    <x-datatable />

    @if (session('status'))
        <div class="bg-primary-50 border border-primary-100 text-primary-900 px-4 py-3 rounded-md text-sm" role="status">{{ session('status') }}</div>
    @endif

    {{-- Stat Cards Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card label="Kunjungan Hari Ini" :value="$totalHariIni" />
        <x-stat-card label="Menunggu / Dipanggil" :value="$menunggu" status="warning" />
        <x-stat-card label="Selesai Hari Ini" :value="$selesai" status="success" />
        <x-stat-card label="Pendapatan Hari Ini" :value="'Rp ' . number_format($pendapatan, 0, ',', '.')" />
    </div>

    {{-- Antrean Hari Ini Table Card (Template A) --}}
    <x-table-container :scrollable="false">
        <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-border flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="text-base font-semibold text-text">Antrean Hari Ini</h2>
                <p class="text-xs text-text-muted">Daftar pasien terjadwal dan berjalan hari ini</p>
            </div>
            <a href="{{ route('kunjungan.index', ['tgl' => now()->toDateString()]) }}" class="text-xs font-medium text-primary-700 hover:text-primary-600 underline">
                Lihat semua
            </a>
        </div>

        <div class="overflow-x-auto w-full" style="-webkit-overflow-scrolling: touch;">
            <table class="datatable w-full text-sm">
                <thead class="bg-bg border-b border-border sticky top-0">
                    <tr class="text-left text-text-muted text-xs uppercase tracking-wider font-semibold">
                        <th scope="col" class="pl-4 sm:pl-6 pr-3 py-3 font-medium">No. Antrean</th>
                        <th scope="col" class="px-3 py-3 font-medium">Pasien</th>
                        <th scope="col" class="px-3 py-3 font-medium">Poli / Dokter</th>
                        <th scope="col" class="px-3 py-3 font-medium">Status</th>
                        <th scope="col" class="pl-3 pr-4 sm:pr-6 py-3 font-medium text-right" data-orderable="false">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-subtle">
                    @forelse ($kunjunganHariIni as $k)
                        <tr class="hover:bg-primary-50/40 transition-colors">
                            <td class="pl-4 sm:pl-6 pr-3 py-3 font-mono font-semibold text-text whitespace-nowrap">{{ $k->no_antrean }}</td>
                            <td class="px-3 py-3">
                                <div class="font-medium text-text">{{ $k->pasien->nama }}</div>
                                <div class="text-xs text-text-muted font-mono">{{ $k->pasien->no_rm }}</div>
                                @if ($k->pasien->alergi)
                                    <div class="mt-1">
                                        <x-status-pill status="danger" :label="'Alergi: ' . $k->pasien->alergi" />
                                    </div>
                                @endif
                            </td>
                            <td class="px-3 py-3">
                                <div class="text-text font-medium">{{ $k->poli->nama ?? $k->dokter->poli->nama }}</div>
                                <div class="text-xs text-text-muted">{{ $k->dokter->nama ?? '-' }}</div>
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap">
                                @php
                                    $statusMap = [
                                        'menunggu' => 'warning',
                                        'dipanggil' => 'info',
                                        'diperiksa' => 'info',
                                        'selesai' => 'success',
                                        'batal' => 'neutral',
                                    ];
                                    $pillStatus = $statusMap[$k->status] ?? 'neutral';
                                @endphp
                                <x-status-pill :status="$pillStatus" :label="ucfirst($k->status)" />
                            </td>
                            <td class="pl-3 pr-4 sm:pr-6 py-3 whitespace-nowrap text-right">
                                <a href="{{ route('kunjungan.show', $k) }}" class="text-xs font-medium text-primary-700 hover:text-primary-600 underline">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 sm:px-6 py-12 text-center text-text-muted text-sm">
                                Belum ada kunjungan untuk hari ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-table-container>
</x-app-layout>
