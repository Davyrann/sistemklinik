<x-app-layout>
    <x-slot name="title">Daftar Kunjungan</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-semibold text-xl text-text leading-tight">Daftar Kunjungan</h1>
                <p class="text-xs text-text-muted mt-0.5">Kelola data antrean dan status kunjungan harian pasien</p>
            </div>
            <div>
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

    {{-- Filter Toolbar --}}
    <form method="GET" action="{{ route('kunjungan.index') }}" class="bg-surface border border-border shadow-sm rounded-lg p-3 sm:p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
        <div>
            <x-input-label value="Cari" />
            <x-text-input name="q" value="{{ $q }}" placeholder="Nama / RM / no. antrean" class="mt-1 w-full" />
        </div>
        <div>
            <x-input-label value="Status Antrean" />
            <x-select-input name="status" class="mt-1 block w-full">
                <option value="">Semua Status</option>
                @foreach (['menunggu','dipanggil','diperiksa','selesai','batal'] as $s)
                    <option value="{{ $s }}" @selected($status === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </x-select-input>
        </div>
        <div>
            <x-input-label value="Tanggal Kunjungan" />
            <x-text-input type="text" name="tgl" value="{{ $tgl }}" data-datepicker class="mt-1 w-full font-mono" placeholder="YYYY-MM-DD" />
        </div>
        <div class="flex gap-2">
            <x-primary-button type="submit" class="flex-1 justify-center">Filter</x-primary-button>
            <a href="{{ route('kunjungan.index') }}" class="flex-1">
                <x-secondary-button type="button" class="w-full justify-center">Reset</x-secondary-button>
            </a>
        </div>
    </form>

    {{-- Antrean Table Card (Template A) --}}
    <x-table-container>
        <table class="datatable w-full text-sm">
            <thead class="bg-bg border-b border-border sticky top-0">
                <tr class="text-left text-text-muted text-xs uppercase tracking-wider font-semibold">
                    <th scope="col" class="pl-4 sm:pl-6 pr-3 py-3 font-medium">No. Antrean</th>
                    <th scope="col" class="px-3 py-3 font-medium">Pasien</th>
                    <th scope="col" class="px-3 py-3 font-medium">Poli / Dokter</th>
                    <th scope="col" class="px-3 py-3 font-medium">Tgl Kunjungan</th>
                    <th scope="col" class="px-3 py-3 font-medium">Status</th>
                    <th scope="col" class="pl-3 pr-4 sm:pr-6 py-3 font-medium text-right" data-orderable="false">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border-subtle">
                @forelse ($kunjungans as $k)
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
                            <div class="text-text font-medium">{{ $k->poli->nama }}</div>
                            <div class="text-xs text-text-muted">{{ $k->dokter->nama ?? '-' }}</div>
                        </td>
                        <td class="px-3 py-3 font-mono text-xs text-text whitespace-nowrap">
                            {{ $k->tgl_kunjungan ? $k->tgl_kunjungan->format('d M Y') : '-' }}
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
                        <td colspan="6" class="px-4 sm:px-6 py-12 text-center text-text-muted text-sm">
                            Tidak ada data kunjungan yang cocok dengan filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-container>
</x-app-layout>
