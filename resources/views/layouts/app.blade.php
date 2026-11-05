<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' | ' . config('app.name') : config('app.name') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon-96x96.png') }}" sizes="96x96">
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
        <meta name="apple-mobile-web-app-title" content="Sistem Klinik">
        <link rel="manifest" href="{{ asset('site.webmanifest') }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-bg text-text selection:bg-primary-100">
        <div class="min-h-screen bg-bg flex flex-col md:flex-row" x-data="{ sidebarOpen: false }">
            <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-3 focus:left-3 focus:z-50 focus:px-4 focus:py-2 focus:bg-primary-700 focus:text-white focus:rounded-md">Lewati ke konten utama</a>

            <x-app-sidebar />

            <div class="flex-1 flex flex-col min-w-0">
                <x-app-topbar :title="$title ?? null" />

                @isset($header)
                    <header class="bg-surface border-b border-border shadow-xs">
                        <div class="max-w-[1440px] mx-auto px-4 py-4 md:px-6 md:py-4">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main id="main-content" class="flex-1 w-full max-w-[1440px] mx-auto px-4 py-4 md:px-6 md:py-6 space-y-6">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
