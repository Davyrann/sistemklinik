@props(['disabled' => false])

<select @disabled($disabled) {{ $attributes->merge(['class' => 'select-input block w-full px-3 pr-9 py-2.5 min-h-9 border border-border rounded-md shadow-sm bg-surface text-sm text-text focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-none disabled:opacity-50 disabled:bg-bg']) }}>
    {{ $slot }}
</select>
