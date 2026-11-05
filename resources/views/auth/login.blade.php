<x-guest-layout>
    <x-slot name="title">Masuk</x-slot>

    <div class="mb-6 space-y-1">
        <h1 class="text-xl font-semibold text-text leading-tight">Masuk ke Akun</h1>
        <p class="text-xs text-text-muted">Masukkan kredensial Anda untuk mengakses sistem manajemen klinik.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        {{-- Email Address --}}
        <div class="space-y-1">
            <x-input-label for="email" value="Email" :required="true" />
            <x-text-input id="email" class="w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        {{-- Password --}}
        <div class="space-y-1">
            <div class="flex items-center justify-between">
                <x-input-label for="password" value="Password" :required="true" />
                @if (Route::has('password.request'))
                    <a class="text-xs text-primary-700 hover:underline focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-offset-1 rounded" href="{{ route('password.request') }}">
                        Lupa password?
                    </a>
                @endif
            </div>

            <x-text-input id="password" class="w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" />
        </div>

        {{-- Remember Me --}}
        <div class="flex items-center">
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer select-none">
                <input id="remember_me" type="checkbox" class="rounded border-border text-primary-700 shadow-sm focus:ring-primary-600 size-4" name="remember">
                <span class="text-sm text-text-muted">Ingat saya</span>
            </label>
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full justify-center">
                Masuk
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
