<x-app-layout>
    <x-slot name="title">Daftar Resep Masuk</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h1 class="font-semibold text-xl text-text leading-tight">
                    Daftar Resep Masuk
                </h1>
                <p class="text-xs text-text-muted mt-0.5">Antrean peresepan obat dari dokter untuk ditelaah, disiapkan, dan diserahkan.</p>
            </div>
            <div>
                <a href="{{ route('tagihan.index') }}" class="h-9 px-4 inline-flex items-center justify-center rounded-md text-xs font-semibold bg-surface border border-border text-text hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 transition">
                    Buka Kasir Tagihan
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-primary-50 border border-primary-200 text-primary-900 px-4 py-3 rounded-md text-sm font-medium" role="status">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Filter & Pencarian Bar --}}
            <div class="bg-surface border border-border rounded-lg p-4 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                {{-- Status Filter Pills --}}
                <div class="flex flex-wrap items-center gap-1.5">
                    @php
                        $currentStatus = request('status', '');
                    @endphp
                    <a
                        href="{{ route('apoteker.resep.index', array_filter(['q' => request('q')])) }}"
                        class="h-9 px-3 inline-flex items-center text-xs font-semibold rounded-md transition {{ $currentStatus === '' ? 'bg-primary-700 text-white' : 'bg-bg text-text-muted hover:text-text hover:bg-slate-200/60' }}"
                    >
                        Semua Resep
                    </a>
                    <a
                        href="{{ route('apoteker.resep.index', array_filter(['status' => 'menunggu', 'q' => request('q')])) }}"
                        class="h-9 px-3 inline-flex items-center text-xs font-semibold rounded-md transition {{ $currentStatus === 'menunggu' ? 'bg-primary-700 text-white' : 'bg-bg text-text-muted hover:text-text hover:bg-slate-200/60' }}"
                    >
                        Menunggu
                    </a>
                    <a
                        href="{{ route('apoteker.resep.index', array_filter(['status' => 'diproses', 'q' => request('q')])) }}"
                        class="h-9 px-3 inline-flex items-center text-xs font-semibold rounded-md transition {{ $currentStatus === 'diproses' ? 'bg-primary-700 text-white' : 'bg-bg text-text-muted hover:text-text hover:bg-slate-200/60' }}"
                    >
                        Disiapkan
                    </a>
                    <a
                        href="{{ route('apoteker.resep.index', array_filter(['status' => 'diserahkan', 'q' => request('q')])) }}"
                        class="h-9 px-3 inline-flex items-center text-xs font-semibold rounded-md transition {{ $currentStatus === 'diserahkan' ? 'bg-primary-700 text-white' : 'bg-bg text-text-muted hover:text-text hover:bg-slate-200/60' }}"
                    >
                        Diserahkan
                    </a>
                </div>

                {{-- Search Box --}}
                <form method="GET" action="{{ route('apoteker.resep.index') }}" class="flex items-center gap-2">
                    @if (request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <div class="relative w-full md:w-64">
                        <x-text-input
                            type="search"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Cari pasien / No. RM / dokter"
                            class="w-full text-xs h-9 pl-3 pr-8"
                        />
                    </div>
                    <x-secondary-button type="submit" class="h-9 px-3 text-xs">
                        Cari
                    </x-secondary-button>
                    @if (request('q'))
                        <a href="{{ route('apoteker.resep.index', array_filter(['status' => request('status')])) }}" class="h-9 px-2.5 inline-flex items-center text-xs text-text-muted hover:text-danger">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- Table Container --}}
            <x-table-container>
                <table class="w-full text-sm">
                    <thead class="bg-bg border-b border-border">
                        <tr class="text-left text-text-muted text-xs">
                            <th class="px-3 py-3 pl-4 font-medium">No. Resep & Waktu</th>
                            <th class="px-3 py-3 font-medium">Pasien & No. RM</th>
                            <th class="px-3 py-3 font-medium">Dokter & Poli</th>
                            <th class="px-3 py-3 font-medium">Rincian Obat</th>
                            <th class="px-3 py-3 font-medium">Status Resep</th>
                            <th class="px-3 py-3 pr-4 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-subtle">
                        @forelse ($reseps as $resep)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-3 py-3.5 pl-4">
                                    <div class="font-mono text-xs font-semibold text-text">RSP-{{ str_pad($resep->id, 5, '0', STR_PAD_LEFT) }}</div>
                                    <div class="font-mono text-xs text-text-muted">{{ $resep->created_at->format('d/m/Y H:i') }}</div>
                                    <div class="text-[11px] text-text-subtle">({{ $resep->created_at->diffForHumans() }})</div>
                                </td>
                                <td class="px-3 py-3.5">
                                    <div class="font-semibold text-text">{{ $resep->kunjungan->pasien->nama }}</div>
                                    <div class="font-mono text-xs text-text-muted">No. RM: {{ $resep->kunjungan->pasien->no_rm }}</div>
                                    @if ($resep->kunjungan->pasien->alergi)
                                        <div class="mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-red-50 text-danger border border-red-200">
                                                Alergi: {{ $resep->kunjungan->pasien->alergi }}
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-3 py-3.5 text-xs">
                                    <div class="font-medium text-text">{{ $resep->dokter->nama ?? 'Dokter Pemeriksa' }}</div>
                                    <div class="text-text-muted">{{ $resep->kunjungan->poli->nama ?? 'Poli Layanan' }}</div>
                                </td>
                                <td class="px-3 py-3.5 text-xs">
                                    <div class="font-mono font-medium text-text">{{ $resep->details->count() }} item obat</div>
                                    <div class="text-text-muted truncate max-w-xs">
                                        {{ $resep->details->pluck('obat.nama_generik')->filter()->implode(', ') }}
                                    </div>
                                </td>
                                <td class="px-3 py-3.5">
                                    @php
                                        $token = match ($resep->status) {
                                            'menunggu', 'draft' => 'warning',
                                            'diproses' => 'info',
                                            'diserahkan' => 'success',
                                            default => 'neutral',
                                        };
                                        $label = match ($resep->status) {
                                            'menunggu', 'draft' => 'Menunggu',
                                            'diproses' => 'Disiapkan',
                                            'diserahkan' => 'Diserahkan',
                                            default => ucfirst($resep->status),
                                        };
                                    @endphp
                                    <x-status-pill :status="$token" :label="$label" />
                                </td>
                                <td class="px-3 py-3.5 pr-4 text-right">
                                    <a
                                        href="{{ route('apoteker.resep.show', $resep) }}"
                                        class="h-9 px-3 inline-flex items-center justify-center rounded-md text-xs font-semibold bg-primary-700 text-white hover:bg-primary-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 transition"
                                    >
                                        Verifikasi
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-sm text-text-muted">
                                    Belum ada resep masuk. Resep muncul di sini setelah dokter menyelesaikan pemeriksaan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </x-table-container>

            {{-- Pagination Links --}}
            @if ($reseps->hasPages())
                <div class="pt-2">
                    {{ $reseps->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
