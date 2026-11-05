@props([
    'status' => 'neutral',
    'label' => null,
])

@php
$statusClasses = match ($status) {
    'success' => 'bg-emerald-50 text-success border-emerald-200',
    'warning' => 'bg-amber-50 text-warning border-amber-200',
    'danger' => 'bg-red-50 text-danger border-red-200',
    'info' => 'bg-blue-50 text-info border-blue-200',
    default => 'bg-slate-50 text-text-muted border-border',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2 py-0.5 rounded-full border text-xs font-medium font-sans {$statusClasses}"]) }}>
    {{ $label ?? $slot }}
</span>
