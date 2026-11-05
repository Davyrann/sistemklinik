<section class="space-y-4">
    <header class="space-y-1">
        <h2 class="text-base font-semibold text-danger">
            Hapus Akun
        </h2>
        <p class="text-xs text-text-muted">
            Setelah akun Anda dihapus, semua sumber daya dan data terkait akan dihapus secara permanen. Pastikan Anda telah mengunduh data penting sebelum melanjutkan.
        </p>
    </header>

    <div>
        <x-danger-button
            type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        >
            Hapus Akun
        </x-danger-button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-0 space-y-4">
            @csrf
            @method('delete')

            <div class="space-y-2">
                <h3 class="text-base font-semibold text-text">
                    Apakah Anda yakin ingin menghapus akun?
                </h3>
                <p class="text-xs text-text-muted">
                    Setelah akun Anda dihapus, seluruh akses dan data akan dihapus secara permanen. Masukkan password Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun secara permanen.
                </p>
            </div>

            <div class="space-y-1">
                <x-input-label for="delete_account_password" value="Password" :required="true" />
                <x-text-input
                    id="delete_account_password"
                    name="password"
                    type="password"
                    class="w-full"
                    placeholder="Masukkan password konfirmasi"
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" />
            </div>

            <div class="border-t border-border pt-4 flex justify-end gap-3">
                <x-secondary-button type="button" x-on:click="$dispatch('close')">
                    Batal
                </x-secondary-button>

                <x-danger-button type="submit">
                    Hapus Akun
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
