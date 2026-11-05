<x-app-layout>
    <x-slot name="title">Daftar Tagihan</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-semibold text-xl text-text leading-tight">Daftar Tagihan & Kasir</h1>
                <p class="text-xs text-text-muted mt-0.5">Kelola penagihan layanan klinis, rincian obat, dan verifikasi pembayaran</p>
            </div>
        </div>
    </x-slot>

    <x-datatable />

    @if (session('status'))
        <div class="bg-primary-50 border border-primary-100 text-primary-900 px-4 py-3 rounded-md text-sm" role="status">{{ session('status') }}</div>
    @endif

    {{-- Stat Cards Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card label="Total Tagihan" :value="$tagihans->count()" />
        <x-stat-card label="Belum Lunas" :value="$tagihans->where('status', 'belum')->count()" status="warning" />
        <x-stat-card label="Lunas" :value="$tagihans->where('status', 'lunas')->count()" status="success" />
        <x-stat-card label="Total Nominal" :value="'Rp ' . number_format($tagihans->sum('total_bayar'), 0, ',', '.')" />
    </div>

    {{-- Filter Toolbar --}}
    <form method="GET" action="{{ route('tagihan.index') }}" class="bg-surface border border-border shadow-sm rounded-lg p-3 sm:p-4 grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
        <div>
            <x-input-label value="Cari Tagihan" />
            <x-text-input name="q" value="{{ $q }}" placeholder="No. Tagihan / Pasien / RM" class="mt-1 w-full" />
        </div>
        <div>
            <x-input-label value="Status Pembayaran" />
            <x-select-input name="status" class="mt-1 block w-full">
                <option value="">Semua Status</option>
                <option value="belum" @selected($status === 'belum')>Belum Lunas</option>
                <option value="lunas" @selected($status === 'lunas')>Lunas</option>
                <option value="batal" @selected($status === 'batal')>Dibatalkan</option>
            </x-select-input>
        </div>
        <div class="flex gap-2">
            <x-primary-button type="submit" class="flex-1 justify-center">Filter</x-primary-button>
            <a href="{{ route('tagihan.index') }}" class="flex-1">
                <x-secondary-button type="button" class="w-full justify-center">Reset</x-secondary-button>
            </a>
        </div>
    </form>

    {{-- Table in Table Container (Template A) --}}
    <x-table-container>
        <table class="datatable w-full text-sm" data-default-order="0,desc">
            <thead class="bg-bg border-b border-border sticky top-0">
                <tr class="text-left text-text-muted text-xs uppercase tracking-wider font-semibold">
                    <th scope="col" class="pl-4 sm:pl-6 pr-3 py-3 font-medium">No. Tagihan</th>
                    <th scope="col" class="px-3 py-3 font-medium">Pasien & No. RM</th>
                    <th scope="col" class="px-3 py-3 font-medium text-right">Total</th>
                    <th scope="col" class="px-3 py-3 font-medium">Status</th>
                    <th scope="col" class="pl-3 pr-4 sm:pr-6 py-3 font-medium text-right" data-orderable="false">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border-subtle">
                @forelse ($tagihans as $t)
                    <tr class="hover:bg-primary-50/40 transition-colors">
                        <td class="pl-4 sm:pl-6 pr-3 py-3 font-mono font-medium text-text whitespace-nowrap" data-order="{{ $t->id }}">
                            #{{ str_pad($t->id, 5, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-3 py-3">
                            <div class="font-medium text-text">{{ $t->kunjungan->pasien->nama }}</div>
                            <div class="text-xs text-text-muted font-mono mt-0.5">
                                {{ $t->kunjungan->pasien->no_rm }} · Antrean: {{ $t->kunjungan->no_antrean }} · {{ $t->kunjungan->tgl_kunjungan->format('d M Y') }}
                            </div>
                            @if ($t->kunjungan->pasien->alergi)
                                <div class="mt-1">
                                    <x-status-pill status="danger" :label="'Alergi: ' . $t->kunjungan->pasien->alergi" />
                                </div>
                            @endif
                        </td>
                        <td class="px-3 py-3 font-mono font-semibold text-text text-right whitespace-nowrap">
                            Rp {{ number_format($t->total_bayar, 0, ',', '.') }}
                        </td>
                        <td class="px-3 py-3 whitespace-nowrap">
                            @if ($t->status === 'lunas')
                                <x-status-pill status="success" label="Lunas" />
                            @elseif ($t->status === 'belum')
                                <x-status-pill status="warning" label="Belum Lunas" />
                            @else
                                <x-status-pill status="neutral" :label="ucfirst($t->status)" />
                            @endif
                        </td>
                        <td class="pl-3 pr-4 sm:pr-6 py-3 whitespace-nowrap text-right">
                            <a href="{{ route('tagihan.show', $t) }}" class="inline-flex items-center text-xs font-semibold text-primary-700 hover:text-primary-800 underline focus:outline-none focus:ring-2 focus:ring-primary-600 rounded">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 sm:px-6 py-12 text-center text-text-muted text-sm">
                            Tidak ada data tagihan yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-container>
</x-app-layout>
