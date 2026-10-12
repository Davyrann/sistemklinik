<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2.5 min-h-9 bg-primary-700 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-primary-600 focus:bg-primary-600 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:pointer-events-none']) }}>
    {{ $slot }}
</button>
