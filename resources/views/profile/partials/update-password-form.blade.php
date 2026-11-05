<section class="space-y-6">
    <header class="space-y-1">
        <h2 class="text-base font-semibold text-text">
            Perbarui Password
        </h2>
        <p class="text-xs text-text-muted">
            Pastikan akun Anda menggunakan password yang kuat dan aman untuk menjaga keamanan sistem.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <div class="space-y-4">
            <div class="space-y-1 max-w-md">
                <x-input-label for="update_password_current_password" value="Password Saat Ini" :required="true" />
                <x-text-input id="update_password_current_password" name="current_password" type="password" class="w-full" autocomplete="current-password" />
                <x-input-error :messages="$errors->updatePassword->get('current_password')" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <x-input-label for="update_password_password" value="Password Baru" :required="true" />
                    <x-text-input id="update_password_password" name="password" type="password" class="w-full" autocomplete="new-password" />
                    <x-input-error :messages="$errors->updatePassword->get('password')" />
                </div>

                <div class="space-y-1">
                    <x-input-label for="update_password_password_confirmation" value="Konfirmasi Password Baru" :required="true" />
                    <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="w-full" autocomplete="new-password" />
                    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" />
                </div>
            </div>
        </div>

        <div class="border-t border-border pt-6 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3">
            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="text-xs font-medium text-success text-center sm:text-right"
                >Password berhasil diperbarui.</p>
            @endif

            <x-primary-button class="w-full sm:w-auto">
                Perbarui Password
            </x-primary-button>
        </div>
    </form>
</section>
