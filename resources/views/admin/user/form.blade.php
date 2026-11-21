<x-app-layout>
    <x-slot name="title">{{ $user->exists ? 'Edit Pengguna' : 'Pengguna Baru' }}</x-slot>
    <x-slot name="header">
        <div class="space-y-1">
            <h1 class="text-xl font-semibold text-text leading-tight">{{ $user->exists ? 'Edit Pengguna' : 'Pengguna Baru' }}</h1>
            <p class="text-xs text-text-muted">
                {{ $user->exists ? 'Perbarui informasi profil, hak akses peran, dan kredensial pengguna.' : 'Daftarkan pengguna baru dengan kredensial dan hak akses sistem.' }}
            </p>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6" x-data="{ role: '{{ old('role', $user->role ?? 'admin') }}' }">
        <div class="bg-surface border border-border shadow-sm rounded-lg p-4 sm:p-6">
            <form method="POST" action="{{ $user->exists ? route('admin.user.update', $user) : route('admin.user.store') }}" class="space-y-6">
                @csrf
                @if ($user->exists) @method('PUT') @endif

                {{-- Grup 1: Informasi Pengguna --}}
                <div class="space-y-3">
                    <h2 class="text-base font-semibold text-text">Informasi Akun</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <x-input-label for="name" value="Nama Lengkap" :required="true" />
                            <x-text-input id="name" name="name" :value="old('name', $user->name)" required autofocus class="w-full" placeholder="Nama lengkap petugas / dokter" />
                            <x-input-error :messages="$errors->get('name')" />
                        </div>

                        <div class="space-y-1">
                            <x-input-label for="email" value="Email" :required="true" />
                            <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" required class="w-full" placeholder="user@klinik.com" />
                            <x-input-error :messages="$errors->get('email')" />
                        </div>
                    </div>
                </div>

                {{-- Grup 2: Hak Akses & Kredensial --}}
                <div class="space-y-3">
                    <h2 class="text-base font-semibold text-text">Hak Akses & Kredensial</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <x-input-label for="role" value="Peran" :required="true" />
                            <x-select-input id="role" name="role" x-model="role" required class="w-full">
                                @foreach (['admin' => 'Admin', 'dokter' => 'Dokter', 'resepsionis' => 'Resepsionis', 'apoteker' => 'Apoteker / Kasir'] as $key => $label)
                                    <option value="{{ $key }}" @selected(old('role', $user->role) === $key)>{{ $label }}</option>
                                @endforeach
                            </x-select-input>
                            <x-input-error :messages="$errors->get('role')" />
                        </div>

                        <div class="space-y-1">
                            <x-input-label for="password" :value="$user->exists ? 'Password Baru' : 'Password'" :required="!$user->exists" />
                            <x-text-input id="password" name="password" type="password" :required="!$user->exists" autocomplete="new-password" class="w-full" />
                            @if ($user->exists)
                                <p class="text-xs text-text-muted">Kosongkan bila tidak ingin mengubah kata sandi.</p>
                            @endif
                            <x-input-error :messages="$errors->get('password')" />
                        </div>
                    </div>
                </div>

                {{-- Grup 3: Penugasan Poli & Profil Dokter (Khusus Peran Dokter) --}}
                <div x-show="role === 'dokter'" x-transition class="space-y-3 border-t border-border pt-6">
                    <div>
                        <h2 class="text-base font-semibold text-text">Penugasan Poli & Profil Praktik</h2>
                        <p class="text-xs text-text-muted">Tentukan unit poli layanan tempat dokter bertugas memeriksa pasien.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1 sm:col-span-2">
                            <x-input-label for="poli_id" value="Unit Poli Penugasan" :required="true" />
                            <x-select-input id="poli_id" name="poli_id" class="w-full">
                                <option value="">-- Pilih Unit Poli Layanan --</option>
                                @foreach ($polis as $poli)
                                    <option value="{{ $poli->id }}" @selected(old('poli_id', $user->dokter?->poli_id) == $poli->id)>
                                        {{ $poli->nama }} (Kode: {{ $poli->kode }})
                                    </option>
                                @endforeach
                            </x-select-input>
                            <p class="text-xs text-text-muted">Dokter akan menerima antrean pasien yang terdaftar di poli ini.</p>
                            <x-input-error :messages="$errors->get('poli_id')" />
                        </div>

                        <div class="space-y-1">
                            <x-input-label for="spesialisasi" value="Spesialisasi / Gelar" />
                            <x-text-input id="spesialisasi" name="spesialisasi" :value="old('spesialisasi', $user->dokter?->spesialisasi ?? 'Dokter Umum')" class="w-full" placeholder="Contoh: Dokter Umum, Dokter Gigi, Spesialis Anak" />
                            <x-input-error :messages="$errors->get('spesialisasi')" />
                        </div>

                        <div class="space-y-1">
                            <x-input-label for="nip" value="NIP / No. SIP (Izin Praktik)" />
                            <x-text-input id="nip" name="nip" :value="old('nip', $user->dokter?->nip)" class="w-full font-mono" placeholder="SIP.12345/DISKES/2026" />
                            <x-input-error :messages="$errors->get('nip')" />
                        </div>

                        <div class="space-y-1 sm:col-span-2">
                            <x-input-label for="no_hp" value="No. Telepon / WhatsApp Dokter" />
                            <x-text-input id="no_hp" name="no_hp" :value="old('no_hp', $user->dokter?->no_hp)" class="w-full font-mono" placeholder="08xxxxxxxxxx" />
                            <x-input-error :messages="$errors->get('no_hp')" />
                        </div>
                    </div>
                </div>

                {{-- Footer Aksi: border-t pt-6, rata kanan, gap-3 [Batal] [Simpan] --}}
                <div class="border-t border-border pt-6 flex flex-col-reverse sm:flex-row justify-end items-stretch sm:items-center gap-3">
                    <a href="{{ route('admin.user.index') }}" class="w-full sm:w-auto">
                        <x-secondary-button type="button" class="w-full sm:w-auto">Batal</x-secondary-button>
                    </a>
                    <x-primary-button class="w-full sm:w-auto">
                        {{ $user->exists ? 'Simpan Perubahan' : 'Simpan Pengguna' }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
