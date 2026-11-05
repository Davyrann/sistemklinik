<x-guest-layout>
    <x-slot name="title">Atur Ulang Password</x-slot>

    <div class="mb-6 space-y-1">
        <h1 class="text-xl font-semibold text-text leading-tight">Atur Ulang Password</h1>
        <p class="text-xs text-text-muted">Masukkan password baru Anda untuk mengamankan akun.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        {{-- Password Reset Token --}}
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        {{-- Email Address --}}
        <div class="space-y-1">
            <x-input-label for="email" value="Email" :required="true" />
            <x-text-input id="email" class="w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        {{-- Password --}}
        <div class="space-y-1">
            <x-input-label for="password" value="Password Baru" :required="true" />
            <x-text-input id="password" class="w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        {{-- Confirm Password --}}
        <div class="space-y-1">
            <x-input-label for="password_confirmation" value="Konfirmasi Password Baru" :required="true" />
            <x-text-input id="password_confirmation" class="w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full justify-center">
                Atur Ulang Password
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
