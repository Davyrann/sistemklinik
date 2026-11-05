<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' — ' . config('app.name') : config('app.name') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon-96x96.png') }}" sizes="96x96">
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
        <meta name="apple-mobile-web-app-title" content="Sistem Klinik">
        <link rel="manifest" href="{{ asset('site.webmanifest') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-bg text-text selection:bg-primary-100">
        <div class="min-h-screen flex flex-col justify-center items-center px-4 py-8 bg-bg">
            <div class="mb-6 flex flex-col items-center">
                <a href="/" class="flex flex-col items-center gap-2">
                    <img src="{{ asset('logo.svg') }}" alt="Logo Klinik" class="h-14 w-auto">
                    <span class="text-lg font-semibold text-text tracking-tight">KlinikKu</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md bg-surface border border-border shadow-sm rounded-lg p-6 sm:p-8 overflow-hidden">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
