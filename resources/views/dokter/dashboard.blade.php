<x-app-layout>
    <x-slot name="title">Dashboard Dokter</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h1 class="font-semibold text-xl text-text leading-tight">
                    Dashboard Dokter{{ $dokter ? ' — ' . $dokter->nama : '' }}
                </h1>
                <p class="text-xs text-text-muted mt-0.5">Antrean konsultasi dan ruang periksa pasien poli.</p>
            </div>
        </div>
    </x-slot>

    <x-datatable />

    @php
        $pasienAktif = $kunjungans->firstWhere('status', 'diperiksa')
            ?? $kunjungans->firstWhere('status', 'dipanggil')
            ?? $kunjungans->firstWhere('status', 'menunggu');
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-primary-50 border border-primary-100 text-primary-900 px-4 py-3 rounded-md text-sm" role="status">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-danger px-4 py-3 rounded-md text-sm" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Kartu Pasien Berikutnya / Sedang Berjalan (Decision-First Layout per DESIGN.md:498-499) --}}
            @if ($pasienAktif)
                <div class="bg-surface border border-border shadow-sm rounded-lg p-4 sm:p-6 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-border">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-primary-700">
                                {{ $pasienAktif->status === 'diperiksa' ? 'Sedang Dalam Pemeriksaan' : ($pasienAktif->status === 'dipanggil' ? 'Pasien Sedang Dipanggil' : 'Pasien Berikutnya') }}
                            </span>
                            <div class="flex flex-wrap items-baseline gap-2 sm:gap-3 mt-1">
                                <h2 class="text-lg font-semibold text-text">{{ $pasienAktif->pasien->nama }}</h2>
                                <span class="font-mono text-xs px-2 py-0.5 rounded bg-bg border border-border text-text">
                                    Antrean: <strong>{{ $pasienAktif->no_antrean }}</strong>
                                </span>
                                <span class="font-mono text-xs text-text-muted">
                                    RM: <strong class="text-text">{{ $pasienAktif->pasien->no_rm }}</strong>
                                </span>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                            @if ($pasienAktif->status === 'menunggu')
                                <form method="POST" action="{{ route('dokter.pemeriksaan.panggil', $pasienAktif) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <x-secondary-button type="submit">Panggil Pasien</x-secondary-button>
                                </form>
                                <a href="{{ route('dokter.pemeriksaan.edit', $pasienAktif) }}">
                                    <x-primary-button>Mulai Pemeriksaan</x-primary-button>
                                </a>
                            @elseif ($pasienAktif->status === 'dipanggil')
                                <a href="{{ route('dokter.pemeriksaan.edit', $pasienAktif) }}">
                                    <x-primary-button>Mulai Pemeriksaan</x-primary-button>
                                </a>
                            @else
                                <a href="{{ route('dokter.pemeriksaan.edit', $pasienAktif) }}">
                                    <x-primary-button>Lanjutkan Pemeriksaan</x-primary-button>
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-xs text-text-muted block">Usia & Penjamin:</span>
                            <span class="font-medium text-text">
                                {{ \Carbon\Carbon::parse($pasienAktif->pasien->tgl_lahir)->age }} th ({{ $pasienAktif->pasien->jk }}) · <span class="font-mono uppercase">{{ $pasienAktif->penjamin }}</span>
                            </span>
                        </div>
                        <div>
                            <span class="text-xs text-text-muted block">Keluhan Awal:</span>
                            <span class="text-text">{{ $pasienAktif->keluhan_utama ?: '-' }}</span>
                        </div>
                        <div class="sm:col-span-2 lg:col-span-1">
                            <span class="text-xs text-text-muted block mb-1">Status Alergi:</span>
                            <x-allergy-banner :alergi="$pasienAktif->pasien->alergi" />
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-surface border border-border shadow-sm rounded-lg p-6 text-center text-text-muted text-sm">
                    <p class="font-medium text-text">Tidak ada antrean aktif saat ini.</p>
                    <p class="text-xs text-text-muted mt-1">Pasien berikutnya akan tampil di sini secara otomatis setelah terdaftar di poli.</p>
                </div>
            @endif

            {{-- Stat Cards Grid (Honest Metrics) --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <x-stat-card label="Total Pasien Hari Ini" :value="$totalHariIni" />
                <x-stat-card label="Menunggu / Dipanggil" :value="$menunggu" status="warning" />
                <x-stat-card label="Sedang Diperiksa" :value="$diperiksa" status="primary" />
                <x-stat-card label="Selesai Hari Ini" :value="$selesai" status="success" />
            </div>

            {{-- Kartu Tabel Antrean Saya (Template A: p-0 overflow-hidden) --}}
            <x-table-container :scrollable="false">
                {{-- Toolbar Tabel --}}
                <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-border flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h2 class="font-semibold text-text text-base">Daftar Antrean Pasien</h2>
                        <p class="text-xs text-text-muted mt-0.5">
                            Tanggal: <span class="font-mono font-medium text-text">{{ \Carbon\Carbon::parse($tgl)->translatedFormat('d M Y') }}</span>
                        </p>
                    </div>

                    <form method="GET" action="{{ route('dokter.dashboard') }}" class="flex items-center gap-2">
                        <x-input-label value="Tanggal:" class="sr-only" />
                        <x-text-input type="text" name="tgl" value="{{ $tgl }}" data-datepicker class="w-36 text-xs font-mono" placeholder="YYYY-MM-DD" />
                        <x-secondary-button type="submit" class="text-xs">Filter</x-secondary-button>
                        @if ($tgl !== now()->toDateString())
                            <a href="{{ route('dokter.dashboard') }}">
                                <x-secondary-button type="button" class="text-xs">Hari Ini</x-secondary-button>
                            </a>
                        @endif
                    </form>
                </div>

                <div class="overflow-x-auto w-full" style="-webkit-overflow-scrolling: touch;">
                    <table class="datatable w-full text-sm">
                        <thead class="bg-bg border-b border-border">
                            <tr class="text-left text-text-muted text-xs font-medium">
                                <th scope="col" class="pl-4 pr-3 py-2 font-medium">No. Antrean</th>
                                <th scope="col" class="px-3 py-2 font-medium">Pasien & No. RM</th>
                                <th scope="col" class="px-3 py-2 font-medium">Keluhan Utama</th>
                                <th scope="col" class="px-3 py-2 font-medium">Tanda Vital</th>
                                <th scope="col" class="px-3 py-2 font-medium">Status</th>
                                <th scope="col" class="pl-3 pr-4 py-2 font-medium" data-orderable="false">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border-subtle">
                            @forelse ($kunjungans as $k)
                                <tr class="hover:bg-primary-50/40 transition-colors">
                                    <td class="pl-4 pr-3 py-3 font-mono font-semibold text-text whitespace-nowrap">{{ $k->no_antrean }}</td>
                                    <td class="px-3 py-3">
                                        <div class="font-medium text-text">{{ $k->pasien->nama }}</div>
                                        <div class="text-xs text-text-muted font-mono mt-0.5">
                                            {{ $k->pasien->no_rm }} · {{ $k->pasien->jk }} · {{ strtoupper($k->penjamin) }}
                                        </div>
                                        @if ($k->pasien->alergi)
                                            <div class="mt-1">
                                                <x-status-pill status="danger" :label="'Alergi: ' . $k->pasien->alergi" />
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-text">{{ $k->keluhan_utama ?: '-' }}</td>
                                    <td class="px-3 py-3 font-mono text-xs text-text whitespace-nowrap">
                                        @if ($k->tandaVital)
                                            <div>TD: {{ $k->tandaVital->td_sistolik && $k->tandaVital->td_diastolik ? "{$k->tandaVital->td_sistolik}/{$k->tandaVital->td_diastolik}" : '-' }} mmHg</div>
                                            <div class="text-text-muted">Suhu: {{ $k->tandaVital->suhu ? "{$k->tandaVital->suhu}°C" : '-' }} · Nadi: {{ $k->tandaVital->nadi ? "{$k->tandaVital->nadi}x" : '-' }}</div>
                                        @else
                                            <span class="text-text-muted">-</span>
                                        @endif
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
                                    <td class="pl-3 pr-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            @if ($k->status === 'menunggu')
                                                <form method="POST" action="{{ route('dokter.pemeriksaan.panggil', $k) }}" class="inline">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="text-sm text-warning font-medium hover:underline">Panggil</button>
                                                </form>
                                            @endif
                                            <a href="{{ route('dokter.pemeriksaan.edit', $k) }}" class="text-sm text-primary-700 font-medium hover:underline">
                                                {{ in_array($k->status, ['menunggu', 'dipanggil'], true) ? 'Periksa' : ($k->status === 'diperiksa' ? 'Lanjutkan' : 'Lihat SOAP') }}
                                            </a>
                                            <a href="{{ route('dokter.pasien.show', $k->pasien) }}" class="text-sm text-text-muted font-medium hover:text-text hover:underline">Riwayat</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 sm:px-6 py-12 text-center text-text-muted text-sm">
                                        Tidak ada antrean pasien untuk tanggal ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-table-container>
        </div>
    </div>
</x-app-layout>
