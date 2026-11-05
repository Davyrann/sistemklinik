<x-app-layout>
    <x-slot name="title">Pendaftaran Kunjungan Baru</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h1 class="font-semibold text-xl text-text leading-tight">Pendaftaran Kunjungan Baru</h1>
                <p class="text-xs text-text-muted mt-0.5">Daftarkan kunjungan pasien ke poli dan dokter tujuan</p>
            </div>
            <div>
                <a href="{{ route('kunjungan.index') }}">
                    <x-secondary-button>Daftar Kunjungan</x-secondary-button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-surface border border-border shadow-sm rounded-lg p-4 md:p-6">
                <form method="POST" action="{{ route('kunjungan.store') }}" id="kunjungan-form" class="space-y-6">
                    @csrf

                    {{-- Group 1: Identitas Pasien --}}
                    <div class="space-y-4">
                        <div class="border-b border-border-subtle pb-2">
                            <h2 class="text-sm font-semibold text-text uppercase tracking-wider">Identitas Pasien</h2>
                            <p class="text-xs text-text-muted">Pilih pasien yang terdaftar di sistem klinik</p>
                        </div>

                        <div>
                            <x-input-label for="pasien_id" value="Pasien" :required="true" />
                            <x-select-input id="pasien_id" name="pasien_id" required class="mt-1 block w-full">
                                <option value="">— Pilih pasien —</option>
                                @foreach ($pasiens as $p)
                                    <option value="{{ $p->id }}" @selected((string) old('pasien_id', $pasien?->id) === (string) $p->id)>
                                        {{ $p->no_rm }} — {{ $p->nama }} ({{ $p->tgl_lahir ? $p->tgl_lahir->format('d M Y') : '-' }})
                                    </option>
                                @endforeach
                            </x-select-input>
                            <p class="text-xs text-text-muted mt-1">
                                Jika pasien baru belum terdaftar, silakan <a href="{{ route('pasien.create') }}" class="underline text-primary-700 hover:text-primary-600">tambahkan pasien baru</a> terlebih dahulu.
                            </p>
                            <x-input-error :messages="$errors->get('pasien_id')" class="mt-1" />
                        </div>
                    </div>

                    {{-- Group 2: Unit Layanan & Dokter --}}
                    <div class="space-y-4">
                        <div class="border-b border-border-subtle pb-2">
                            <h2 class="text-sm font-semibold text-text uppercase tracking-wider">Unit Layanan & Dokter</h2>
                            <p class="text-xs text-text-muted">Tujuan pemeriksaan dan dokter yang menangani</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="poli_id" value="Poli Tujuan" :required="true" />
                                <x-select-input id="poli_id" name="poli_id" required class="mt-1 block w-full">
                                    <option value="">— Pilih poli —</option>
                                    @foreach ($polis as $poli)
                                        <option value="{{ $poli->id }}" @selected((string) old('poli_id') === (string) $poli->id)>
                                            {{ $poli->nama }} ({{ $poli->kode }})
                                        </option>
                                    @endforeach
                                </x-select-input>
                                <x-input-error :messages="$errors->get('poli_id')" class="mt-1" />
                            </div>

                            <div>
                                <x-input-label for="dokter_id" value="Dokter Pemeriksa" :required="true" />
                                <x-select-input id="dokter_id" name="dokter_id" required class="mt-1 block w-full">
                                    <option value="">— Pilih dokter —</option>
                                    @foreach ($dokters as $d)
                                        <option value="{{ $d->id }}" data-poli="{{ $d->poli_id }}" @selected((string) old('dokter_id') === (string) $d->id)>
                                            {{ $d->nama }} — {{ $d->poli->nama }}
                                        </option>
                                    @endforeach
                                </x-select-input>
                                <span class="text-xs text-text-subtle mt-1 block">Daftar dokter menyesuaikan poli yang dipilih</span>
                                <x-input-error :messages="$errors->get('dokter_id')" class="mt-1" />
                            </div>
                        </div>
                    </div>

                    {{-- Group 3: Waktu, Penjamin & Keluhan --}}
                    <div class="space-y-4">
                        <div class="border-b border-border-subtle pb-2">
                            <h2 class="text-sm font-semibold text-text uppercase tracking-wider">Waktu, Penjamin & Keluhan</h2>
                            <p class="text-xs text-text-muted">Jadwal kunjungan, pembiayaan, dan keluhan awal saat mendaftar</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="tgl_kunjungan" value="Tanggal Kunjungan" :required="true" />
                                <x-text-input id="tgl_kunjungan" name="tgl_kunjungan" type="text" data-datepicker :value="old('tgl_kunjungan', now()->toDateString())" required class="mt-1 block w-full font-mono" placeholder="YYYY-MM-DD" />
                                <x-input-error :messages="$errors->get('tgl_kunjungan')" class="mt-1" />
                            </div>

                            <div>
                                <x-input-label for="penjamin" value="Penjamin" :required="true" />
                                <x-select-input id="penjamin" name="penjamin" required class="mt-1 block w-full">
                                    @foreach (['umum','bpjs','asuransi'] as $pj)
                                        <option value="{{ $pj }}" @selected(old('penjamin', $pasien?->penjamin ?? 'umum') === $pj)>
                                            {{ strtoupper($pj) }}
                                        </option>
                                    @endforeach
                                </x-select-input>
                                <x-input-error :messages="$errors->get('penjamin')" class="mt-1" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="keluhan_utama" value="Keluhan Utama" />
                                <textarea id="keluhan_utama" name="keluhan_utama" rows="3" class="mt-1 block w-full px-3 py-2.5 border border-border rounded-md shadow-sm bg-surface text-sm text-text placeholder:text-text-muted focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-none" placeholder="Contoh: Demam sejak 2 hari yang lalu, batuk kering">{{ old('keluhan_utama') }}</textarea>
                                <x-input-error :messages="$errors->get('keluhan_utama')" class="mt-1" />
                            </div>
                        </div>
                    </div>

                    {{-- Footer Action --}}
                    <div class="border-t border-border pt-6 flex flex-col-reverse sm:flex-row justify-end items-stretch sm:items-center gap-3">
                        <a href="{{ route('kunjungan.index') }}" class="w-full sm:w-auto">
                            <x-secondary-button type="button" class="w-full sm:w-auto justify-center">Batal</x-secondary-button>
                        </a>
                        <x-primary-button class="w-full sm:w-auto justify-center">
                            Simpan Kunjungan
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('poli_id')?.addEventListener('change', function() {
            const poli = this.value;
            document.querySelectorAll('#dokter_id option[data-poli]').forEach(o => {
                o.hidden = poli && o.dataset.poli !== poli;
            });
            const sel = document.getElementById('dokter_id');
            if (sel.selectedOptions[0]?.hidden) sel.value = '';
        });
    </script>
</x-app-layout>
