<x-app-layout>
    <x-slot name="title">Daftar Obat</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-semibold text-xl text-text leading-tight">Daftar Stok Obat</h1>
                <p class="text-xs text-text-muted mt-0.5">Pantau persediaan master obat, ambang batas minimum, dan harga satuan</p>
            </div>
        </div>
    </x-slot>

    <x-datatable />

    @if (session('status'))
        <div class="bg-primary-50 border border-primary-100 text-primary-900 px-4 py-3 rounded-md text-sm" role="status">{{ session('status') }}</div>
    @endif

    {{-- Stat Cards Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card label="Total Jenis Obat" :value="$obats->count()" />
        <x-stat-card label="Stok Tersedia" :value="$obats->where('stok', '>', 10)->count()" status="success" />
        <x-stat-card label="Stok Menipis" :value="$obats->filter(fn($o) => $o->stok > 0 && $o->stok <= $o->stok_minimum)->count()" status="warning" />
        <x-stat-card label="Stok Habis" :value="$obats->where('stok', '<=', 0)->count()" status="danger" />
    </div>

    {{-- Filter Toolbar --}}
    <form method="GET" action="{{ route('obat.index') }}" class="bg-surface border border-border shadow-sm rounded-lg p-3 sm:p-4 grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
        <div>
            <x-input-label value="Cari Obat" />
            <x-text-input name="q" value="{{ $q }}" placeholder="Nama obat / kode" class="mt-1 w-full" />
        </div>
        <div>
            <x-input-label value="Filter Stok" />
            <x-select-input name="stok" class="mt-1 block w-full">
                <option value="">Semua Stok</option>
                <option value="menipis" @selected(($filterStok ?? request('stok')) === 'menipis')>Stok Menipis</option>
                <option value="habis" @selected(($filterStok ?? request('stok')) === 'habis')>Stok Habis</option>
            </x-select-input>
        </div>
        <div class="flex gap-2">
            <x-primary-button type="submit" class="flex-1 justify-center">Filter</x-primary-button>
            <a href="{{ route('obat.index') }}" class="flex-1">
                <x-secondary-button type="button" class="w-full justify-center">Reset</x-secondary-button>
            </a>
        </div>
    </form>

    {{-- Table in Table Container (Template A) --}}
    <x-table-container>
        <table class="datatable w-full text-sm">
            <thead class="bg-bg border-b border-border sticky top-0">
                <tr class="text-left text-text-muted text-xs uppercase tracking-wider font-semibold">
                    <th scope="col" class="pl-4 sm:pl-6 pr-3 py-3 font-medium">Nama Obat</th>
                    <th scope="col" class="px-3 py-3 font-medium">Kode</th>
                    <th scope="col" class="px-3 py-3 font-medium">Satuan</th>
                    <th scope="col" class="px-3 py-3 font-medium text-right">Stok</th>
                    <th scope="col" class="px-3 py-3 font-medium text-right">Harga Satuan</th>
                    <th scope="col" class="px-3 py-3 font-medium">Status</th>
                    <th scope="col" class="pl-3 pr-4 sm:pr-6 py-3 font-medium text-right" data-orderable="false">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border-subtle">
                @forelse ($obats as $o)
                    <tr class="hover:bg-primary-50/40 transition-colors">
                        <td class="pl-4 sm:pl-6 pr-3 py-3">
                            <div class="font-medium text-text">{{ $o->nama_generik }}</div>
                            @if ($o->nama_dagang)
                                <div class="text-xs text-text-muted mt-0.5">{{ $o->nama_dagang }}</div>
                            @endif
                        </td>
                        <td class="px-3 py-3 font-mono font-medium text-text whitespace-nowrap">
                            {{ $o->kode }}
                        </td>
                        <td class="px-3 py-3 text-text-muted whitespace-nowrap">
                            {{ $o->satuan }}
                        </td>
                        <td class="px-3 py-3 font-mono text-right whitespace-nowrap">
                            <span class="{{ $o->stok <= 0 ? 'text-danger font-bold' : ($o->stok <= $o->stok_minimum ? 'text-warning font-semibold' : 'text-text') }}">
                                {{ $o->stok }}
                            </span>
                            <span class="text-xs text-text-muted ml-1">(min {{ $o->stok_minimum }})</span>
                        </td>
                        <td class="px-3 py-3 font-mono font-medium text-text text-right whitespace-nowrap">
                            Rp {{ number_format($o->harga, 0, ',', '.') }}
                        </td>
                        <td class="px-3 py-3 whitespace-nowrap">
                            @if ($o->stok <= 0)
                                <x-status-pill status="danger" label="Habis" />
                            @elseif ($o->stok <= $o->stok_minimum)
                                <x-status-pill status="warning" label="Menipis" />
                            @else
                                <x-status-pill status="success" label="Tersedia" />
                            @endif
                        </td>
                        <td class="pl-3 pr-4 sm:pr-6 py-3 whitespace-nowrap text-right">
                            <a href="{{ route('obat.show', $o) }}" class="inline-flex items-center text-xs font-semibold text-primary-700 hover:text-primary-800 underline focus:outline-none focus:ring-2 focus:ring-primary-600 rounded">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 sm:px-6 py-12 text-center text-text-muted text-sm">
                            Tidak ada data obat yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-container>
</x-app-layout>
