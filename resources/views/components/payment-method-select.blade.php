@props([
    'name' => 'metode',
    'id' => 'metode',
    'selected' => null,
])

@php
    $options = \App\Models\Pembayaran::METODE;
    $descriptions = \App\Models\Pembayaran::METODE_DESKRIPSI ?? [];
    $selectedVal = $selected ?? old($name, 'tunai');
    $optionsJson = collect($options)->map(function ($lbl, $val) use ($descriptions) {
        return [
            'value' => $val,
            'label' => $lbl,
            'desc' => $descriptions[$val] ?? '',
        ];
    })->values();
@endphp

<div
    {{ $attributes->merge(['class' => 'relative select-input-custom']) }}
    x-data="{
        open: false,
        selectedVal: '{{ $selectedVal }}',
        options: {{ Js::from($optionsJson) }},
        get currentOption() {
            return this.options.find(o => o.value === this.selectedVal) || this.options[0];
        },
        select(opt) {
            this.selectedVal = opt.value;
            this.open = false;
            $dispatch('input', opt.value);
            $dispatch('change', opt.value);
        }
    }"
    x-modelable="selectedVal"
    @click.outside="open = false"
    @keydown.escape.window="open = false"
>
    {{-- Hidden input for form submission (HTML form & FormData) --}}
    <input type="hidden" name="{{ $name }}" id="{{ $id }}" :value="selectedVal">

    {{-- Dropdown Trigger Button --}}
    <button
        type="button"
        @click="open = !open"
        class="w-full h-9 px-3 flex items-center justify-between border border-border rounded-md shadow-xs bg-surface text-left text-sm text-text hover:bg-slate-50 focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-none transition pointer-coarse:h-11"
        :aria-expanded="open"
        aria-haspopup="listbox"
    >
        <div class="flex items-center gap-2 truncate">
            <span class="font-mono font-bold text-xs px-2 py-0.5 rounded bg-bg border border-border text-text" x-text="currentOption.label">
                {{ $options[$selectedVal] ?? 'TUNAI' }}
            </span>
            <span class="text-xs text-text-muted truncate hidden sm:inline" x-text="currentOption.desc">
                {{ $descriptions[$selectedVal] ?? '' }}
            </span>
        </div>
        <div class="ms-2 text-text-subtle shrink-0">
            <svg class="size-4 transition-transform duration-150" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
            </svg>
        </div>
    </button>

    {{-- Floating Custom Dropdown Panel (identical to doctor's medicine dropdown) --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1 scale-98"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-1 scale-98"
        class="absolute z-50 left-0 right-0 mt-1 max-h-56 overflow-y-auto bg-surface border border-border rounded-md shadow-lg divide-y divide-border"
        style="display: none;"
        role="listbox"
    >
        <template x-for="opt in options" :key="opt.value">
            <div
                @click="select(opt)"
                class="px-4 py-2.5 hover:bg-primary-50 cursor-pointer transition duration-150 flex items-center justify-between"
                :class="selectedVal === opt.value ? 'bg-primary-50/70' : ''"
                role="option"
                :aria-selected="selectedVal === opt.value"
            >
                <div class="space-y-0.5">
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-xs font-bold text-text uppercase" x-text="opt.label"></span>
                        <template x-if="selectedVal === opt.value">
                            <span class="text-[10px] text-primary-700 bg-primary-100 px-1.5 py-0.5 rounded font-semibold">Aktif</span>
                        </template>
                    </div>
                    <p class="text-xs text-text-muted" x-text="opt.desc"></p>
                </div>
                <div class="text-primary-700 shrink-0" x-show="selectedVal === opt.value">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                </div>
            </div>
        </template>
    </div>
</div>
