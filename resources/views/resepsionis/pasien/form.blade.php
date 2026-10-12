<x-app-layout>
    <x-slot name="title">{{ $pasien->exists ? 'Edit Pasien' : 'Pasien Baru' }}</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text leading-tight">{{ $pasien->exists ? 'Edit Pasien' : 'Pasien Baru' }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-surface border border-border shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ $pasien->exists ? route('pasien.update', $pasien) : route('pasien.store') }}">
                    @csrf
                    @if ($pasien->exists) @method('PUT') @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <x-input-label for="nama" value="Nama Lengkap *" />
                            <x-text-input id="nama" name="nama" :value="old('nama', $pasien->nama)" required class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="nik" value="NIK (16 digit)" />
                            <x-text-input id="nik" name="nik" :value="old('nik', $pasien->nik)" maxlength="16" class="mt-1 block w-full font-mono" placeholder="3201..." />
                            <x-input-error :messages="$errors->get('nik')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="tgl_lahir" value="Tanggal Lahir *" />
                            <x-text-input id="tgl_lahir" name="tgl_lahir" type="text" data-datepicker data-max-today :value="old('tgl_lahir', $pasien->tgl_lahir?->format('Y-m-d'))" required class="mt-1 block w-full" placeholder="YYYY-MM-DD" />
                            <x-input-error :messages="$errors->get('tgl_lahir')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="jk" value="Jenis Kelamin *" />
                            <x-select-input id="jk" name="jk" required class="mt-1 block w-full">
                                <option value="L" @selected(old('jk', $pasien->jk) === 'L')>Laki-laki</option>
                                <option value="P" @selected(old('jk', $pasien->jk) === 'P')>Perempuan</option>
                            </x-select-input>
                            <x-input-error :messages="$errors->get('jk')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="gol_darah" value="Gol. Darah" />
                            <x-select-input id="gol_darah" name="gol_darah" class="mt-1 block w-full">
                                <option value="">-</option>
                                @foreach (['A','B','AB','O'] as $g)
                                    <option value="{{ $g }}" @selected(old('gol_darah', $pasien->gol_darah) === $g)>{{ $g }}</option>
                                @endforeach
                            </x-select-input>
                        </div>
                        <div>
                            <x-input-label for="telp" value="No. HP" />
                            <x-text-input id="telp" name="telp" :value="old('telp', $pasien->telp)" class="mt-1 block w-full" placeholder="08..." />
                            <x-input-error :messages="$errors->get('telp')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="penjamin" value="Penjamin *" />
                            <x-select-input id="penjamin" name="penjamin" required class="mt-1 block w-full">
                                @foreach (['umum','bpjs','asuransi'] as $pj)
                                    <option value="{{ $pj }}" @selected(old('penjamin', $pasien->penjamin ?? 'umum') === $pj)>{{ strtoupper($pj) }}</option>
                                @endforeach
                            </x-select-input>
                        </div>
                        <div class="sm:col-span-2">
                            <x-input-label for="alamat" value="Alamat" />
                            <textarea id="alamat" name="alamat" rows="2" class="mt-1 block w-full px-3 py-2.5 border border-border rounded-md shadow-sm bg-surface text-sm text-text placeholder:text-text-muted focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-none">{{ old('alamat', $pasien->alamat) }}</textarea>
                        </div>
                        <div class="sm:col-span-2">
                            <x-input-label for="alergi" value="Alergi (tampilkan chip merah jika ada)" />
                            <x-text-input id="alergi" name="alergi" :value="old('alergi', $pasien->alergi)" class="mt-1 block w-full" placeholder="Contoh: Amoxicillin, Udang" />
                            <p class="text-xs text-text-muted mt-1">Kosongkan jika tidak ada alergi.</p>
                        </div>
                    </div>

                    <div class="flex justify-between items-center mt-6">
                        <a href="{{ route('pasien.index') }}"><x-secondary-button type="button">Kembali</x-secondary-button></a>
                        <x-primary-button>{{ $pasien->exists ? 'Simpan Perubahan' : 'Simpan Pasien' }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>


