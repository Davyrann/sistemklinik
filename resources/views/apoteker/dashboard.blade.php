<x-app-layout>
    <x-slot name="title">Dashboard Farmasi & Kasir</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h1 class="font-semibold text-xl text-text leading-tight">
                    Dashboard Farmasi & Kasir
                </h1>
                <p class="text-xs text-text-muted mt-0.5">Pengelolaan resep masuk, dispensing obat, kasir penagihan, dan ketersediaan stok.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('apoteker.resep.index') }}" class="h-9 px-4 inline-flex items-center justify-center rounded-md text-xs font-semibold bg-primary-700 text-white hover:bg-primary-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 transition">
                    Semua Resep Masuk
                </a>
                <a href="{{ route('tagihan.index') }}" class="h-9 px-4 inline-flex items-center justify-center rounded-md text-xs font-semibold bg-surface border border-border text-text hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 transition">
                    Kelola Kasir
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-primary-50 border border-primary-200 text-primary-900 px-4 py-3 rounded-md text-sm font-medium" role="status">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-danger px-4 py-3 rounded-md text-sm font-medium" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Stat Cards (Honest Metrics) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-stat-card
                    label="Resep Menunggu"
                    :value="$resepMenungguCount"
                    helper="Perlu telaah & dispensing"
                />
                <x-stat-card
                    label="Resep Diserahkan Hari Ini"
                    :value="$resepDiserahkanHariIni"
                    helper="Selesai diserahkan ke pasien"
                />
                <x-stat-card
                    label="Tagihan Belum Lunas"
                    :value="$tagihanBelumLunasCount"
                    helper="Menunggu pembayaran kasir"
                />
                <x-stat-card
                    label="Stok Perlu Perhatian"
                    :value="$obatMenipisCount"
                    helper="Stok di bawah batas minimum"
                />
            </div>

            {{-- Decision-First: Resep Masuk Terlama Menunggu --}}
            @php
                $resepPrioritas = $resepMenunggu->first();
            @endphp
            @if ($resepPrioritas)
                <div class="bg-surface border border-border rounded-lg p-4 sm:p-6 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-border">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-warning">
                                Resep Menunggu Terlama (Prioritas)
                            </span>
                            <div class="flex flex-wrap items-baseline gap-2 sm:gap-3 mt-1">
                                <h2 class="text-lg font-semibold text-text">{{ $resepPrioritas->kunjungan->pasien->nama }}</h2>
                                <span class="font-mono text-xs px-2 py-0.5 rounded bg-bg border border-border text-text">
                                    No. RM: <strong>{{ $resepPrioritas->kunjungan->pasien->no_rm }}</strong>
                                </span>
                                <span class="text-xs text-text-muted">
                                    Dokter: <strong>{{ $resepPrioritas->dokter->nama ?? 'Dokter Pemeriksa' }}</strong>
                                </span>
                            </div>
                            @if ($resepPrioritas->kunjungan->pasien->alergi)
                                <div class="mt-2 text-xs font-medium text-danger bg-red-50 border border-red-200 px-2.5 py-1 rounded inline-block">
                                    Alergi Pasien: {{ $resepPrioritas->kunjungan->pasien->alergi }}
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('apoteker.resep.show', $resepPrioritas) }}" class="h-11 sm:h-9 px-4 inline-flex items-center justify-center rounded-md text-sm font-semibold bg-primary-700 text-white hover:bg-primary-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 transition">
                                Verifikasi & Siapkan Resep
                            </a>
                        </div>
                    </div>
                    <div class="text-xs text-text-muted flex flex-wrap items-center gap-4">
                        <span>Jumlah Obat: <strong class="text-text font-mono">{{ $resepPrioritas->details->count() }} item</strong></span>
                        <span>Dibuat: <strong class="text-text font-mono">{{ $resepPrioritas->created_at->format('H:i') }} ({{ $resepPrioritas->created_at->diffForHumans() }})</strong></span>
                    </div>
                </div>
            @endif

            {{-- 2 Kolom: Resep Masuk & Tagihan Kasir --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Daftar Resep Masuk --}}
                <div class="bg-surface border border-border rounded-lg overflow-hidden flex flex-col">
                    <div class="px-4 py-3 bg-bg border-b border-border flex items-center justify-between">
                        <div>
                            <h2 class="font-semibold text-sm text-text">Resep Masuk Menunggu</h2>
                            <p class="text-xs text-text-muted">Urutan terlama menunggu di posisi teratas</p>
                        </div>
                        <a href="{{ route('apoteker.resep.index', ['status' => 'menunggu']) }}" class="text-xs font-semibold text-primary-700 hover:underline">
                            Lihat Semua
                        </a>
                    </div>
                    <x-table-container class="border-0 rounded-none flex-1">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 border-b border-border">
                                <tr class="text-left text-text-muted text-xs">
                                    <th class="px-3 py-2.5 pl-4 font-medium">Pasien & No. RM</th>
                                    <th class="px-3 py-2.5 font-medium">Dokter</th>
                                    <th class="px-3 py-2.5 font-medium">Item</th>
                                    <th class="px-3 py-2.5 font-medium">Status</th>
                                    <th class="px-3 py-2.5 pr-4 font-medium text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border-subtle">
                                @forelse ($resepMenunggu as $r)
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <td class="px-3 py-3 pl-4">
                                            <div class="font-medium text-text">{{ $r->kunjungan->pasien->nama }}</div>
                                            <div class="font-mono text-xs text-text-muted">{{ $r->kunjungan->pasien->no_rm }}</div>
                                        </td>
                                        <td class="px-3 py-3 text-xs text-text">
                                            {{ $r->dokter->nama ?? '-' }}
                                        </td>
                                        <td class="px-3 py-3 text-xs font-mono text-text">
                                            {{ $r->details->count() }} obat
                                        </td>
                                        <td class="px-3 py-3">
                                            @php
                                                $token = match ($r->status) {
                                                    'menunggu', 'draft' => 'warning',
                                                    'diproses' => 'info',
                                                    'diserahkan' => 'success',
                                                    default => 'neutral',
                                                };
                                                $label = match ($r->status) {
                                                    'menunggu', 'draft' => 'Menunggu',
                                                    'diproses' => 'Disiapkan',
                                                    'diserahkan' => 'Diserahkan',
                                                    default => ucfirst($r->status),
                                                };
                                            @endphp
                                            <x-status-pill :status="$token" :label="$label" />
                                        </td>
                                        <td class="px-3 py-3 pr-4 text-right">
                                            <a href="{{ route('apoteker.resep.show', $r) }}" class="inline-flex items-center text-xs font-semibold text-primary-700 hover:underline">
                                                Proses
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-xs text-text-muted">
                                            Belum ada resep masuk yang menunggu.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </x-table-container>
                </div>

                {{-- Antrean Tagihan Kasir --}}
                <div class="bg-surface border border-border rounded-lg overflow-hidden flex flex-col">
                    <div class="px-4 py-3 bg-bg border-b border-border flex items-center justify-between">
                        <div>
                            <h2 class="font-semibold text-sm text-text">Tagihan Kasir Belum Bayar</h2>
                            <p class="text-xs text-text-muted">Pembayaran sebelum penyerahan obat</p>
                        </div>
                        <a href="{{ route('tagihan.index') }}" class="text-xs font-semibold text-primary-700 hover:underline">
                            Kelola Kasir
                        </a>
                    </div>
                    <x-table-container class="border-0 rounded-none flex-1">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 border-b border-border">
                                <tr class="text-left text-text-muted text-xs">
                                    <th class="px-3 py-2.5 pl-4 font-medium">No. Tagihan & Pasien</th>
                                    <th class="px-3 py-2.5 font-medium">Poli</th>
                                    <th class="px-3 py-2.5 font-medium text-right">Total Bayar</th>
                                    <th class="px-3 py-2.5 pr-4 font-medium text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border-subtle">
                                @forelse ($tagihanBelumLunas as $t)
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <td class="px-3 py-3 pl-4">
                                            <div class="font-mono text-xs font-semibold text-text">{{ $t->no_tagihan }}</div>
                                            <div class="text-xs text-text-muted">{{ $t->kunjungan->pasien->nama }} ({{ $t->kunjungan->pasien->no_rm }})</div>
                                        </td>
                                        <td class="px-3 py-3 text-xs text-text">
                                            {{ $t->kunjungan->poli->nama ?? '-' }}
                                        </td>
                                        <td class="px-3 py-3 text-xs font-mono font-semibold text-text text-right">
                                            Rp {{ number_format($t->total_bayar, 0, ',', '.') }}
                                        </td>
                                        <td class="px-3 py-3 pr-4 text-right">
                                            <a href="{{ route('tagihan.show', $t) }}" class="inline-flex items-center text-xs font-semibold text-primary-700 hover:underline">
                                                Bayar Kasir
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-xs text-text-muted">
                                            Semua tagihan telah lunas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </x-table-container>
                </div>
            </div>

            {{-- Peringatan Stok Obat Menipis / Habis --}}
            @if ($obatPerluPerhatian->isNotEmpty())
                <div class="bg-surface border border-warning/50 rounded-lg overflow-hidden">
                    <div class="px-4 py-3 bg-amber-50/50 border-b border-warning/30 flex items-center justify-between">
                        <div>
                            <h2 class="font-semibold text-sm text-text">Peringatan Ketersediaan Obat</h2>
                            <p class="text-xs text-text-muted">Stok obat berada di bawah atau sama dengan batas minimum</p>
                        </div>
                        <a href="{{ route('apoteker.obat.index', ['filter' => 'menipis']) }}" class="text-xs font-semibold text-primary-700 hover:underline">
                            Kelola Stok Obat
                        </a>
                    </div>
                    <x-table-container class="border-0 rounded-none">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 border-b border-border">
                                <tr class="text-left text-text-muted text-xs">
                                    <th class="px-3 py-2.5 pl-4 font-medium">Kode & Nama Obat</th>
                                    <th class="px-3 py-2.5 font-medium">Nama Dagang</th>
                                    <th class="px-3 py-2.5 font-medium font-mono text-center">Stok Saat Ini</th>
                                    <th class="px-3 py-2.5 font-medium font-mono text-center">Batas Min</th>
                                    <th class="px-3 py-2.5 font-medium">Status</th>
                                    <th class="px-3 py-2.5 pr-4 font-medium text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border-subtle">
                                @foreach ($obatPerluPerhatian as $o)
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <td class="px-3 py-3 pl-4">
                                            <div class="font-medium text-text">{{ $o->nama_generik }}</div>
                                            <div class="font-mono text-xs text-text-muted">{{ $o->kode }}</div>
                                        </td>
                                        <td class="px-3 py-3 text-xs text-text-muted">
                                            {{ $o->nama_dagang ?: '-' }}
                                        </td>
                                        <td class="px-3 py-3 font-mono text-xs font-semibold text-center {{ $o->stok <= 0 ? 'text-danger' : 'text-warning' }}">
                                            {{ $o->stok }} {{ $o->satuan }}
                                        </td>
                                        <td class="px-3 py-3 font-mono text-xs text-text-muted text-center">
                                            {{ $o->stok_minimum }} {{ $o->satuan }}
                                        </td>
                                        <td class="px-3 py-3">
                                            @if ($o->stok <= 0)
                                                <x-status-pill status="danger" label="Habis" />
                                            @else
                                                <x-status-pill status="warning" label="Menipis" />
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 pr-4 text-right">
                                            <a href="{{ route('apoteker.obat.index', ['q' => $o->nama_generik]) }}" class="inline-flex items-center text-xs font-semibold text-primary-700 hover:underline">
                                                Restock
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </x-table-container>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
