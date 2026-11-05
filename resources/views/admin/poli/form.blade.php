<x-app-layout>
    <x-slot name="title">{{ $poli->exists ? 'Edit Unit Poli' : 'Poli Baru' }}</x-slot>
    <x-slot name="header">
        <div class="space-y-1">
            <h1 class="text-xl font-semibold text-text leading-tight">{{ $poli->exists ? 'Edit Unit Poli' : 'Poli Baru' }}</h1>
            <p class="text-xs text-text-muted">
                {{ $poli->exists ? 'Perbarui informasi kode, nama, dan status operasional unit layanan poli.' : 'Daftarkan unit poli baru ke dalam sistem operasional klinik.' }}
            </p>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        <div class="bg-surface border border-border shadow-sm rounded-lg p-4 sm:p-6">
            <form method="POST" action="{{ $poli->exists ? route('admin.poli.update', $poli) : route('admin.poli.store') }}" class="space-y-6">
                @csrf
                @if ($poli->exists) @method('PUT') @endif

                {{-- Grup 1: Informasi Unit Poli --}}
                <div class="space-y-3">
                    <h2 class="text-base font-semibold text-text">Informasi Unit Layanan</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <x-input-label for="kode" value="Kode Poli" :required="true" />
                            <x-text-input id="kode" name="kode" :value="old('kode', $poli->kode)" required autofocus class="w-full font-mono uppercase" placeholder="UMUM / GIGI / KIA" />
                            <span class="text-xs text-text-muted">Maksimal 20 karakter, digunakan sebagai prefiks antrean.</span>
                            <x-input-error :messages="$errors->get('kode')" />
                        </div>

                        <div class="space-y-1">
                            <x-input-label for="nama" value="Nama Unit Poli" :required="true" />
                            <x-text-input id="nama" name="nama" :value="old('nama', $poli->nama)" required class="w-full" placeholder="Contoh: Poli Umum, Poli Gigi, Poli Anak" />
                            <x-input-error :messages="$errors->get('nama')" />
                        </div>

                        <div class="space-y-1 sm:col-span-2">
                            <x-input-label for="deskripsi" value="Deskripsi Layanan & Fasilitas" />
                            <textarea id="deskripsi" name="deskripsi" rows="3" class="w-full min-h-24 px-3 py-2 border border-border rounded-md shadow-xs bg-surface text-sm text-text placeholder:text-text-subtle focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-none transition-colors" placeholder="Keterangan cakupan layanan medis pada poli ini...">{{ old('deskripsi', $poli->deskripsi) }}</textarea>
                            <x-input-error :messages="$errors->get('deskripsi')" />
                        </div>

                        <div class="space-y-1 sm:col-span-2">
                            <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                <input type="hidden" name="is_aktif" value="0">
                                <input type="checkbox" name="is_aktif" value="1" @checked(old('is_aktif', $poli->is_aktif ?? true)) class="rounded border-border text-primary-700 focus:ring-primary-600">
                                <span class="text-sm font-medium text-text">Unit Poli Aktif Beroperasi</span>
                            </label>
                            <p class="text-xs text-text-muted">Poli nonaktif tidak akan muncul pada pilihan pendaftaran kunjungan baru resepsionis.</p>
                        </div>
                    </div>
                </div>

                {{-- Footer Aksi: border-t pt-6, rata kanan, gap-3 [Batal] [Simpan] --}}
                <div class="border-t border-border pt-6 flex flex-col-reverse sm:flex-row justify-end items-stretch sm:items-center gap-3">
                    <a href="{{ route('admin.poli.index') }}" class="w-full sm:w-auto">
                        <x-secondary-button type="button" class="w-full sm:w-auto">Batal</x-secondary-button>
                    </a>
                    <x-primary-button class="w-full sm:w-auto">
                        {{ $poli->exists ? 'Simpan Perubahan' : 'Simpan Poli' }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
