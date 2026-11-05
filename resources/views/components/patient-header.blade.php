@props(['kunjungan'])

@php
    $pasien = $kunjungan->pasien ?? null;
    $nama = $pasien->nama ?? '-';
    $noRm = $pasien->no_rm ?? '-';
    $noAntrean = $kunjungan->no_antrean ?? '-';
    
    // Usia calculation
    $usia = '-';
    if ($pasien && $pasien->tgl_lahir) {
        $usia = \Carbon\Carbon::parse($pasien->tgl_lahir)->age . ' th';
    }

    // Jenis Kelamin label
    $jk = '-';
    if ($pasien && $pasien->jk) {
        $jk = match(strtoupper($pasien->jk)) {
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
            default => $pasien->jk,
        };
    }

    // Penjamin
    $penjamin = $kunjungan->penjamin ?? ($pasien->penjamin ?? '-');

    // Keluhan Utama
    $keluhanUtama = $kunjungan->keluhan_utama ?? null;

    // Alergi
    $alergi = $pasien ? $pasien->alergi : null;
@endphp

<section {{ $attributes->merge(['class' => 'sticky top-14 z-10 bg-surface border border-border shadow-sm rounded-lg p-3 sm:p-4 space-y-2.5']) }} aria-label="Identitas Pasien">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1">
            <h2 class="text-base font-semibold text-text tracking-tight">{{ $nama }}</h2>
            <span class="font-mono text-sm text-text-muted">
                RM: <strong class="text-text font-semibold">{{ $noRm }}</strong>
            </span>
        </div>

        <div class="flex flex-wrap items-center gap-2 text-xs">
            @if($noAntrean && $noAntrean !== '-')
                <span class="inline-flex items-center px-2 py-0.5 rounded bg-primary-50 text-primary-700 border border-primary-100 font-mono font-medium">
                    Antrean: {{ $noAntrean }}
                </span>
            @endif

            <span class="text-text-muted">
                {{ $usia }} ({{ $jk }})
            </span>

            @if($penjamin && $penjamin !== '-')
                <span class="px-2 py-0.5 rounded bg-bg border border-border text-text font-mono uppercase text-[11px] font-medium">
                    {{ strtoupper($penjamin) }}
                </span>
            @endif
        </div>
    </div>

    <x-allergy-banner :alergi="$alergi" />

    @if($keluhanUtama)
        <div class="text-xs text-text border-t border-border-subtle pt-2 flex items-baseline gap-2">
            <span class="text-text-muted font-medium shrink-0">Keluhan Utama:</span>
            <span class="text-text">{{ $keluhanUtama }}</span>
        </div>
    @endif
</section>
