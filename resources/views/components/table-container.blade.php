@props([
    'header' => null,
    'scrollable' => true,
])

<div {{ $attributes->merge(['class' => 'table-container bg-surface border border-border shadow-sm rounded-lg p-0 overflow-hidden w-full']) }}>
    @if (isset($header) && (is_string($header) ? !empty(trim($header)) : !$header->isEmpty()))
        <div class="border-b border-border">
            {{ $header }}
        </div>
    @endif

    @if ($scrollable)
        <div class="overflow-x-auto w-full" style="-webkit-overflow-scrolling: touch;">
            {{ $slot }}
        </div>
    @else
        {{ $slot }}
    @endif
</div>
