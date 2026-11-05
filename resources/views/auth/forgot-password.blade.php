<x-guest-layout>
    <x-slot name="title">Lupa Password</x-slot>

    <div class="mb-6 space-y-1">
        <h1 class="text-xl font-semibold text-text leading-tight">Lupa Password</h1>
        <p class="text-xs text-text-muted">
            Masukkan alamat email yang terdaftar. Kami akan mengirimkan tautan untuk mengatur ulang password akun Anda.
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        {{-- Email Address --}}
        <div class="space-y-1">
            <x-input-label for="email" value="Email" :required="true" />
            <x-text-input id="email" class="w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="pt-2 flex flex-col gap-3">
            <x-primary-button class="w-full justify-center">
                Kirim Tautan Reset Password
            </x-primary-button>

            <a href="{{ route('login') }}" class="text-center text-xs text-primary-700 hover:underline">
                Kembali ke halaman masuk
            </a>
        </div>
    </form>
</x-guest-layout>
