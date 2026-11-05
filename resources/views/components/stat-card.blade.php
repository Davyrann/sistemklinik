@props([
    'label',
    'value',
    'status' => 'default',
    'helper' => null,
])

@php
    $valueColorClass = match ($status) {
        'warning' => 'text-warning',
        'danger' => 'text-danger',
        'success' => 'text-success',
        'info' => 'text-info',
        'primary' => 'text-primary-700',
        default => 'text-text',
    };
@endphp

<div {{ $attributes->merge(['class' => 'bg-surface border border-border shadow-sm rounded-lg p-4 space-y-1']) }}>
    <p class="text-xs font-medium text-text-muted">{{ $label }}</p>
    <p class="text-2xl font-semibold font-mono {{ $valueColorClass }}">{{ $value }}</p>
    @if($helper)
        <p class="text-xs text-text-subtle pt-0.5">{{ $helper }}</p>
    @endif
    {{ $slot }}
</div>
