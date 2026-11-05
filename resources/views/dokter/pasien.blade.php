<x-app-layout>
    <x-slot name="title">Riwayat Medis — {{ $pasien->nama }}</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="font-semibold text-xl text-text leading-tight">Riwayat Medis Pasien</h1>
                <p class="text-xs text-text-muted mt-0.5">Rekam medis elektronik kronologis dan riwayat terapi pasien.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dokter.dashboard') }}">
                    <x-secondary-button>Kembali ke Antrean</x-secondary-button>
                </a>
            </div>
        </div>
    </x-slot>

    @php
        // Disguised NIK per DESIGN.md section 5: 3374••••••••0001
        $nik = $pasien->nik;
        $maskedNik = $nik && strlen($nik) >= 8
            ? substr($nik, 0, 4) . str_repeat('•', max(0, strlen($nik) - 8)) . substr($nik, -4)
            : ($nik ?: '-');
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            {{-- Kartu Profil Pasien --}}
            <div class="bg-surface border border-border shadow-sm rounded-lg p-4 sm:p-6 space-y-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 text-sm">
                    <div>
                        <span class="text-xs text-text-muted block">No. RM</span>
                        <div class="font-mono font-semibold text-text text-base">{{ $pasien->no_rm }}</div>
                    </div>
                    <div>
                        <span class="text-xs text-text-muted block">NIK</span>
                        <div class="font-mono text-text text-sm">{{ $maskedNik }}</div>
                    </div>
                    <div>
                        <span class="text-xs text-text-muted block">Nama & JK</span>
                        <div class="font-medium text-text">{{ $pasien->nama }} ({{ $pasien->jk }})</div>
                    </div>
                    <div>
                        <span class="text-xs text-text-muted block">Usia & Tgl Lahir</span>
                        <div class="text-text">{{ \Carbon\Carbon::parse($pasien->tgl_lahir)->age }} th · <span class="font-mono text-xs">{{ $pasien->tgl_lahir->translatedFormat('d M Y') }}</span></div>
                    </div>
                    <div>
                        <span class="text-xs text-text-muted block">Penjamin</span>
                        <div class="font-mono text-text uppercase">{{ $pasien->penjamin }}</div>
                    </div>
                    <div>
                        <span class="text-xs text-text-muted block">No. Telepon</span>
                        <div class="font-mono text-text">{{ $pasien->telp ?? '-' }}</div>
                    </div>
                </div>

                {{-- Banner Alergi (3 discrete states via x-allergy-banner) --}}
                <x-allergy-banner :alergi="$pasien->alergi" />

                @if ($pasien->alamat)
                    <div class="text-xs text-text-muted border-t border-border pt-3">
                        <span class="font-medium text-text">Alamat Tinggal:</span> {{ $pasien->alamat }}
                    </div>
                @endif
            </div>

            {{-- Timeline Rekam Medis --}}
            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                    <h2 class="font-semibold text-text text-base">Riwayat Rekam Medis ({{ $pasien->kunjungans->count() }} Kunjungan)</h2>
                    <span class="text-xs text-text-muted font-normal">Urutan kronologis (terbaru di atas)</span>
                </div>

                @forelse ($pasien->kunjungans as $k)
                    @php
                        $tv = $k->tandaVital;
                        $rm = $k->rekamMedis;
                        $resep = $k->resep;
                        $statusMap = [
                            'menunggu' => 'warning',
                            'dipanggil' => 'info',
                            'diperiksa' => 'info',
                            'selesai' => 'success',
                            'batal' => 'neutral',
                        ];
                        $pillStatus = $statusMap[$k->status] ?? 'neutral';
                    @endphp

                    <div class="bg-surface border border-border shadow-sm rounded-lg p-4 sm:p-6 space-y-4">
                        {{-- Header Kunjungan --}}
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-3 border-b border-border gap-2">
                            <div class="flex flex-wrap items-center gap-2 sm:gap-3 text-sm">
                                <span class="font-mono font-semibold text-text">{{ $k->tgl_kunjungan->translatedFormat('d M Y') }}</span>
                                <span class="text-text-muted">·</span>
                                <span class="text-text font-mono text-xs px-2 py-0.5 rounded bg-bg border border-border">Antrean: {{ $k->no_antrean }}</span>
                                <span class="text-text-muted">·</span>
                                <span class="text-text">{{ $k->poli->nama ?? '-' }} ({{ $k->dokter->nama ?? '-' }})</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <x-status-pill :status="$pillStatus" :label="ucfirst($k->status)" />
                                @if ($k->status !== 'selesai')
                                    <a href="{{ route('dokter.pemeriksaan.edit', $k) }}" class="text-xs text-primary-700 hover:text-primary-800 underline font-medium">
                                        Periksa Pasien
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Tanda Vital & Keluhan --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm bg-bg p-3.5 rounded-md border border-border">
                            <div>
                                <span class="text-xs text-text-muted block">Keluhan Saat Kunjungan:</span>
                                <p class="text-text mt-1">{{ $k->keluhan_utama ?: '-' }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-text-muted block">Tanda Vital:</span>
                                @if ($tv)
                                    <div class="mt-1 font-mono text-xs flex flex-wrap gap-x-4 gap-y-1 text-text">
                                        <span>TD: {{ $tv->td_sistolik && $tv->td_diastolik ? "{$tv->td_sistolik}/{$tv->td_diastolik} mmHg" : '-' }}</span>
                                        <span>Nadi: {{ $tv->nadi ? "{$tv->nadi}x" : '-' }}</span>
                                        <span>Suhu: {{ $tv->suhu ? "{$tv->suhu}°C" : '-' }}</span>
                                        <span>RR: {{ $tv->rr ? "{$tv->rr}x" : '-' }}</span>
                                        <span>SpO2: {{ $tv->spo2 ? "{$tv->spo2}%" : '-' }}</span>
                                        <span>IMT: {{ $tv->imt ? "{$tv->imt} kg/m²" : '-' }}</span>
                                    </div>
                                @else
                                    <p class="text-text-muted mt-1 text-xs">-</p>
                                @endif
                            </div>
                        </div>

                        {{-- Rekam Medis SOAP --}}
                        @if ($rm)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-xs text-text-muted block">Subjektif (S)</span>
                                    <p class="text-text mt-1 text-sm">{{ $rm->subjective ?? '-' }}</p>
                                </div>
                                <div>
                                    <span class="text-xs text-text-muted block">Objektif (O)</span>
                                    <p class="text-text mt-1 text-sm">{{ $rm->objective ?? '-' }}</p>
                                </div>
                                <div>
                                    <span class="text-xs text-text-muted block">Asesmen (A)</span>
                                    <p class="text-text mt-1 text-sm">{{ $rm->assessment ?? '-' }}</p>
                                    @if ($rm->diagnoses->isNotEmpty())
                                        <div class="mt-2 flex flex-wrap gap-1.5">
                                            @foreach ($rm->diagnoses as $d)
                                                <span class="inline-flex items-center text-xs px-2 py-0.5 rounded bg-primary-50 text-primary-700 border border-primary-100 font-mono">
                                                    {{ $d->kode_icd10 ? "[{$d->kode_icd10}] " : '' }}{{ $d->nama_diagnosis }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <span class="text-xs text-text-muted block">Rencana (P)</span>
                                    <p class="text-text mt-1 text-sm">{{ $rm->plan ?? '-' }}</p>
                                </div>
                            </div>
                        @endif

                        {{-- Resep Obat --}}
                        @if ($resep && $resep->details->isNotEmpty())
                            <div class="pt-2 border-t border-border">
                                <span class="text-xs font-semibold text-text block mb-2">Resep Obat yang Diberikan:</span>
                                <x-table-container>
                                    <table class="w-full text-sm">
                                        <thead class="bg-bg border-b border-border text-left text-text-muted text-xs">
                                            <tr>
                                                <th scope="col" class="px-3 py-2 font-medium">Obat</th>
                                                <th scope="col" class="px-3 py-2 font-medium">Jumlah</th>
                                                <th scope="col" class="px-3 py-2 font-medium">Dosis</th>
                                                <th scope="col" class="px-3 py-2 font-medium">Frekuensi</th>
                                                <th scope="col" class="px-3 py-2 font-medium">Aturan Pakai</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-border-subtle">
                                            @foreach ($resep->details as $rd)
                                                <tr>
                                                    <td class="px-3 py-2 text-text font-medium">{{ $rd->obat->nama_generik }} {{ $rd->obat->nama_dagang ? "({$rd->obat->nama_dagang})" : '' }}</td>
                                                    <td class="px-3 py-2 font-mono text-text whitespace-nowrap">{{ $rd->jumlah }} {{ $rd->obat->satuan }}</td>
                                                    <td class="px-3 py-2 font-mono text-text whitespace-nowrap">{{ $rd->dosis ?: '-' }}</td>
                                                    <td class="px-3 py-2 text-text">{{ $rd->frekuensi ?: '-' }}</td>
                                                    <td class="px-3 py-2 text-text-muted">{{ $rd->aturan_pakai ?: '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </x-table-container>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-surface border border-border shadow-sm rounded-lg p-6 text-center text-text-muted text-sm">
                        Belum ada riwayat kunjungan medis untuk pasien ini.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
