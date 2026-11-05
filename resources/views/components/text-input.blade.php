@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'block w-full h-9 px-3 rounded-md border border-border bg-surface text-text text-sm placeholder:text-text-subtle focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-none transition-colors duration-150 pointer-coarse:h-11 disabled:bg-bg disabled:text-text-subtle disabled:cursor-not-allowed']) }}>
