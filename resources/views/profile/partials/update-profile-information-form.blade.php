<section class="space-y-6">
    <header class="space-y-1">
        <h2 class="text-base font-semibold text-text">
            Informasi Profil
        </h2>
        <p class="text-xs text-text-muted">
            Perbarui nama lengkap dan alamat email akun Anda.
        </p>
    </header>
    @if (Route::has('verification.send'))
        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>
    @endif
    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-1">
                <x-input-label for="name" value="Nama Lengkap" :required="true" />
                <x-text-input id="name" name="name" type="text" class="w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" />
            </div>

            <div class="space-y-1">
                <x-input-label for="email" value="Email" :required="true" />
                <x-text-input id="email" name="email" type="email" class="w-full" :value="old('email', $user->email)" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-2">
                        <p class="text-xs text-warning">
                            Alamat email Anda belum diverifikasi.
                            <button form="send-verification" class="text-xs text-primary-700 underline hover:text-primary-800 focus:outline-none">
                                Klik di sini untuk mengirim ulang email verifikasi.
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 text-xs font-medium text-success">
                                Tautan verifikasi baru telah dikirim ke alamat email Anda.
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="border-t border-border pt-6 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3">
            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="text-xs font-medium text-success text-center sm:text-right"
                >Perubahan profil berhasil disimpan.</p>
            @endif

            <x-primary-button class="w-full sm:w-auto">
                Simpan Perubahan
            </x-primary-button>
        </div>
    </form>
</section>
