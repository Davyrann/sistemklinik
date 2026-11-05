<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center h-9 px-4 gap-2 rounded-md font-semibold text-sm bg-danger hover:bg-red-800 active:bg-red-900 text-white focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2 transition-colors duration-150 pointer-coarse:h-11 disabled:opacity-50 disabled:pointer-events-none']) }}>
    {{ $slot }}
</button>
