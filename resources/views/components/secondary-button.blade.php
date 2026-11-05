<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center h-9 px-4 gap-2 rounded-md font-medium text-sm bg-surface border border-primary-700 text-primary-700 hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-offset-2 transition-colors duration-150 pointer-coarse:h-11 disabled:opacity-50 disabled:pointer-events-none']) }}>
    {{ $slot }}
</button>
