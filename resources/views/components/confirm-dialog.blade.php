@props([
    'name',
    'title' => 'Konfirmasi Tindakan',
    'item' => '',
    'confirmText' => 'Hapus',
    'cancelText' => 'Batal',
    'action' => '',
    'method' => 'POST',
    'maxWidth' => 'md',
])

<x-modal :name="$name" :maxWidth="$maxWidth" focusable>
    <form method="{{ strtoupper($method) === 'GET' ? 'GET' : 'POST' }}" action="{{ $action }}" class="p-0">
        @if(strtoupper($method) !== 'GET')
            @csrf
            @if(!in_array(strtoupper($method), ['POST']))
                @method($method)
            @endif
        @endif

        <div class="space-y-3">
            <h3 class="text-base font-semibold text-text">
                {{ $title }}
            </h3>

            <p class="text-sm text-text-muted">
                {{ $slot->isNotEmpty() ? $slot : 'Apakah Anda yakin ingin memproses item ini?' }}
                @if($item)
                    <span class="block mt-1 font-medium text-text bg-bg px-2 py-1 rounded border border-border-subtle">{{ $item }}</span>
                @endif
            </p>

            <div class="mt-6 flex justify-end gap-2 pt-2">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ $cancelText }}
                </x-secondary-button>

                <x-danger-button type="submit">
                    {{ $confirmText }}
                </x-danger-button>
            </div>
        </div>
    </form>
</x-modal>
