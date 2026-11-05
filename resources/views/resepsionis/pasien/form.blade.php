<x-app-layout>
    <x-slot name="title">{{ $pasien->exists ? 'Edit Data Pasien' : 'Pendaftaran Pasien Baru' }}</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h1 class="font-semibold text-xl text-text leading-tight">{{ $pasien->exists ? 'Edit Data Pasien' : 'Pendaftaran Pasien Baru' }}</h1>
                <p class="text-xs text-text-muted mt-0.5">{{ $pasien->exists ? 'Perbarui informasi rekam medis pasien' : 'Lengkapi identitas dasar dan riwayat alergi pasien' }}</p>
            </div>
            @if ($pasien->exists)
                <div class="text-xs font-mono text-text-muted">
                    No. RM: <span class="font-semibold text-text">{{ $pasien->no_rm }}</span>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-surface border border-border shadow-sm rounded-lg p-4 md:p-6">
                <form method="POST" action="{{ $pasien->exists ? route('pasien.update', $pasien) : route('pasien.store') }}" class="space-y-6">
                    @csrf
                    @if ($pasien->exists)
                        @method('PUT')
                    @endif

                    {{-- Group 1: Data Pribadi --}}
                    <div class="space-y-4">
                        <div class="border-b border-border-subtle pb-2">
                            <h2 class="text-sm font-semibold text-text uppercase tracking-wider">Data Pribadi</h2>
                            <p class="text-xs text-text-muted">Identitas pokok pasien sesuai KTP atau kartu identitas resmi</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <x-input-label for="nama" value="Nama Lengkap" :required="true" />
                                <x-text-input id="nama" name="nama" :value="old('nama', $pasien->nama)" required class="mt-1 block w-full" placeholder="Nama lengkap sesuai KTP" />
                                <x-input-error :messages="$errors->get('nama')" class="mt-1" />
                            </div>

                            <div>
                                <x-input-label for="nik" value="NIK" />
                                <x-text-input id="nik" name="nik" :value="old('nik', $pasien->nik)" maxlength="16" class="mt-1 block w-full font-mono" placeholder="3201xxxxxxxxxxxx" />
                                <span class="text-xs text-text-subtle mt-0.5 block font-mono">16 digit angka</span>
                                <x-input-error :messages="$errors->get('nik')" class="mt-1" />
                            </div>

                            <div>
                                <x-input-label for="tgl_lahir" value="Tanggal Lahir" :required="true" />
                                <x-text-input id="tgl_lahir" name="tgl_lahir" type="text" data-datepicker data-max-today :value="old('tgl_lahir', $pasien->tgl_lahir?->format('Y-m-d'))" required class="mt-1 block w-full font-mono" placeholder="YYYY-MM-DD" />
                                <x-input-error :messages="$errors->get('tgl_lahir')" class="mt-1" />
                            </div>

                            <div>
                                <x-input-label for="jk" value="Jenis Kelamin" :required="true" />
                                <x-select-input id="jk" name="jk" required class="mt-1 block w-full">
                                    <option value="L" @selected(old('jk', $pasien->jk) === 'L')>Laki-laki</option>
                                    <option value="P" @selected(old('jk', $pasien->jk) === 'P')>Perempuan</option>
                                </x-select-input>
                                <x-input-error :messages="$errors->get('jk')" class="mt-1" />
                            </div>

                            <div>
                                <x-input-label for="gol_darah" value="Golongan Darah" />
                                <x-select-input id="gol_darah" name="gol_darah" class="mt-1 block w-full">
                                    <option value="">- Belum Diketahui -</option>
                                    @foreach (['A','B','AB','O'] as $g)
                                        <option value="{{ $g }}" @selected(old('gol_darah', $pasien->gol_darah) === $g)>Golongan {{ $g }}</option>
                                    @endforeach
                                </x-select-input>
                                <x-input-error :messages="$errors->get('gol_darah')" class="mt-1" />
                            </div>
                        </div>
                    </div>

                    {{-- Group 2: Kontak & Alamat --}}
                    <div class="space-y-4">
                        <div class="border-b border-border-subtle pb-2">
                            <h2 class="text-sm font-semibold text-text uppercase tracking-wider">Kontak & Alamat</h2>
                            <p class="text-xs text-text-muted">Nomor kontak darurat dan alamat domisili pasien</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <x-input-label for="telp" value="No. HP / WhatsApp" />
                                <x-text-input id="telp" name="telp" :value="old('telp', $pasien->telp)" class="mt-1 block w-full font-mono" placeholder="08xxxxxxxxxx" />
                                <x-input-error :messages="$errors->get('telp')" class="mt-1" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="alamat" value="Alamat Lengkap" />
                                <textarea id="alamat" name="alamat" rows="2" class="mt-1 block w-full px-3 py-2.5 border border-border rounded-md shadow-sm bg-surface text-sm text-text placeholder:text-text-muted focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-none" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota">{{ old('alamat', $pasien->alamat) }}</textarea>
                                <x-input-error :messages="$errors->get('alamat')" class="mt-1" />
                            </div>
                        </div>
                    </div>

                    {{-- Group 3: Alergi & Penjamin --}}
                    <div class="space-y-4">
                        <div class="border-b border-border-subtle pb-2">
                            <h2 class="text-sm font-semibold text-text uppercase tracking-wider">Alergi & Penjamin</h2>
                            <p class="text-xs text-text-muted">Status alergi klinis pasien dan metode pembiayaan pelayanan</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="penjamin" value="Penjamin" :required="true" />
                                <x-select-input id="penjamin" name="penjamin" required class="mt-1 block w-full">
                                    @foreach (['umum','bpjs','asuransi'] as $pj)
                                        <option value="{{ $pj }}" @selected(old('penjamin', $pasien->penjamin ?? 'umum') === $pj)>{{ strtoupper($pj) }}</option>
                                    @endforeach
                                </x-select-input>
                                <x-input-error :messages="$errors->get('penjamin')" class="mt-1" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="alergi" value="Riwayat Alergi Obat / Makanan" />
                                <x-text-input id="alergi" name="alergi" :value="old('alergi', $pasien->alergi)" class="mt-1 block w-full" placeholder="Contoh: Amoxicillin, Paracetamol, Seafood, Udang" />
                                <p class="text-xs text-text-muted mt-1 leading-relaxed">
                                    Format 3 status alergi: Tulis nama alergen dipisah koma jika ada alergi; tulis <code class="font-mono bg-bg px-1 py-0.5 rounded text-text">Tidak ada</code> jika sudah dikonfirmasi tidak memiliki alergi; atau kosongkan jika belum sempat ditanyakan.
                                </p>
                                <x-input-error :messages="$errors->get('alergi')" class="mt-1" />
                            </div>
                        </div>
                    </div>

                    {{-- Footer Action --}}
                    <div class="border-t border-border pt-6 flex flex-col-reverse sm:flex-row justify-end items-stretch sm:items-center gap-3">
                        <a href="{{ route('pasien.index') }}" class="w-full sm:w-auto">
                            <x-secondary-button type="button" class="w-full sm:w-auto justify-center">Batal</x-secondary-button>
                        </a>
                        <x-primary-button class="w-full sm:w-auto justify-center">
                            {{ $pasien->exists ? 'Simpan Perubahan' : 'Simpan Pasien' }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
