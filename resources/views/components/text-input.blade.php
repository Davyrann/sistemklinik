@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'block w-full px-3 py-2.5 min-h-9 border border-border rounded-md shadow-sm bg-surface text-sm text-text placeholder:text-text-muted focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-none disabled:opacity-50 disabled:bg-bg']) }}>
