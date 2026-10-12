<x-app-layout>
    <x-slot name="title">{{ $user->exists ? 'Edit Pengguna' : 'Pengguna Baru' }}</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text leading-tight">{{ $user->exists ? 'Edit Pengguna' : 'Pengguna Baru' }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-surface border border-border shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ $user->exists ? route('admin.user.update', $user) : route('admin.user.store') }}">
                    @csrf
                    @if ($user->exists) @method('PUT') @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="name" value="Nama Lengkap *" />
                            <x-text-input id="name" name="name" :value="old('name', $user->name)" required class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="email" value="Email *" />
                            <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" required class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="password" :value="$user->exists ? 'Password Baru (kosongkan jika tetap)' : 'Password *'" />
                            <x-text-input id="password" name="password" type="password" :required="!$user->exists" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="role" value="Role *" />
                            <x-select-input id="role" name="role" required class="mt-1 block w-full">
                                @foreach (['admin', 'resepsionis', 'dokter'] as $r)
                                    <option value="{{ $r }}" @selected(old('role', $user->role) === $r)>{{ ucfirst($r) }}</option>
                                @endforeach
                            </x-select-input>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex justify-between items-center mt-6">
                        <a href="{{ route('admin.user.index') }}"><x-secondary-button type="button">Kembali</x-secondary-button></a>
                        <x-primary-button>{{ $user->exists ? 'Simpan Perubahan' : 'Simpan Pengguna' }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
