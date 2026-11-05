<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Sistem Klinik') }}</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('logo.svg') }}">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-bg text-text min-h-screen flex items-center justify-center p-4">
        <div class="max-w-md w-full bg-surface border border-border shadow-sm rounded-lg p-6 text-center space-y-4">
            <div class="flex justify-center">
                <img src="{{ asset('logo.svg') }}" alt="Logo Klinik" class="h-14 w-auto">
            </div>
            <h1 class="text-xl font-semibold text-text">Sistem Klinik (KlinikKu)</h1>
            <p class="text-sm text-text-muted">Aplikasi manajemen operasional klinik terintegrasi.</p>
            <div class="pt-2">
                <a href="{{ route('login') }}" class="inline-flex">
                    <x-primary-button class="w-full justify-center">Masuk ke Sistem</x-primary-button>
                </a>
            </div>
        </div>
    </body>
</html>
