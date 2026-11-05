@props(['alergi' => null])

@php
    $allergens = [];
    $hasAllergy = false;
    $isConfirmedNone = false;
    $isUnknown = is_null($alergi);

    if (! $isUnknown) {
        $trimmed = is_string($alergi) ? trim($alergi) : '';

        // Check if explicitly recorded as none (empty, or common none markers like 'tidak ada', '-', 'none')
        if ($trimmed === '' || in_array(strtolower($trimmed), ['tidak ada', 'tidak', '-', 'none', 't/a', 'nihil'], true)) {
            $isConfirmedNone = true;
        } else {
            // Split allergens by comma or semicolon
            $parts = array_filter(array_map('trim', preg_split('/[,;]+/', $trimmed)));
            if (count($parts) > 0) {
                $allergens = $parts;
                $hasAllergy = true;
            } else {
                $isConfirmedNone = true;
            }
        }
    }
@endphp

@if($hasAllergy)
    <div {{ $attributes->merge(['class' => 'bg-red-50 border border-red-200 text-danger text-xs p-2.5 rounded-md flex flex-wrap items-center gap-2']) }} role="alert">
        <span class="font-semibold">Alergi:</span>
        <div class="flex flex-wrap items-center gap-2">
            @foreach($allergens as $item)
                <span class="rounded-full bg-surface border border-red-300 text-danger font-semibold px-2 py-0.5">{{ $item }}</span>
            @endforeach
        </div>
    </div>
@elseif($isConfirmedNone)
    <div {{ $attributes->merge(['class' => 'bg-bg border border-border text-text-muted text-xs p-2 rounded-md']) }}>
        <span>Tidak ada alergi tercatat</span>
    </div>
@else
    <div {{ $attributes->merge(['class' => 'bg-amber-50 border border-amber-200 text-warning text-xs p-2.5 rounded-md font-medium']) }} role="alert">
        <span>Alergi belum ditanyakan. Konfirmasi ke pasien.</span>
    </div>
@endif
