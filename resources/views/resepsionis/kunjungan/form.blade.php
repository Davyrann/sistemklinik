<x-app-layout>
    <x-slot name="title">Buat Kunjungan</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text leading-tight">Buat Kunjungan</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-surface border border-border shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('kunjungan.store') }}" id="kunjungan-form">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="pasien_id" value="Pasien *" />
                            <x-select-input id="pasien_id" name="pasien_id" required class="mt-1 block w-full">
                                <option value="">— Pilih pasien —</option>
                                @foreach ($pasiens as $p)
                                    <option value="{{ $p->id }}" @selected((string) old('pasien_id', $pasien?->id) === (string) $p->id)>{{ $p->no_rm }} — {{ $p->nama }} ({{ $p->tgl_lahir->format('d M Y') }})</option>
                                @endforeach
                            </x-select-input>
                            <p class="text-xs text-text-muted mt-1">Cari di <a href="{{ route('pasien.index') }}" class="underline text-primary-700">daftar pasien</a> jika tidak ketemu. No. RM & antrean dibuat otomatis.</p>
                            <x-input-error :messages="$errors->get('pasien_id')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="poli_id" value="Poli *" />
                                <x-select-input id="poli_id" name="poli_id" required class="mt-1 block w-full">
                                    <option value="">— Pilih poli —</option>
                                    @foreach ($polis as $poli)
                                        <option value="{{ $poli->id }}" @selected((string) old('poli_id') === (string) $poli->id)>{{ $poli->nama }} ({{ $poli->kode }})</option>
                                    @endforeach
                                </x-select-input>
                                <x-input-error :messages="$errors->get('poli_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="dokter_id" value="Dokter *" />
                                <x-select-input id="dokter_id" name="dokter_id" required class="mt-1 block w-full">
                                    <option value="">— Pilih dokter —</option>
                                    @foreach ($dokters as $d)
                                        <option value="{{ $d->id }}" data-poli="{{ $d->poli_id }}" @selected((string) old('dokter_id') === (string) $d->id)>{{ $d->nama }} — {{ $d->poli->nama }}</option>
                                    @endforeach
                                </x-select-input>
                                <p class="text-xs text-text-muted mt-1">Otomatis filter sesuai poli.</p>
                                <x-input-error :messages="$errors->get('dokter_id')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="tgl_kunjungan" value="Tanggal Kunjungan *" />
                                <x-text-input id="tgl_kunjungan" name="tgl_kunjungan" type="text" data-datepicker :value="old('tgl_kunjungan', now()->toDateString())" required class="mt-1 block w-full" placeholder="YYYY-MM-DD" />
                                <x-input-error :messages="$errors->get('tgl_kunjungan')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="penjamin" value="Penjamin *" />
                                <x-select-input id="penjamin" name="penjamin" required class="mt-1 block w-full">
                                    @foreach (['umum','bpjs','asuransi'] as $pj)
                                        <option value="{{ $pj }}" @selected(old('penjamin', $pasien?->penjamin ?? 'umum') === $pj)>{{ strtoupper($pj) }}</option>
                                    @endforeach
                                </x-select-input>
                            </div>
                        </div>

                        <div>
                            <x-input-label for="keluhan_utama" value="Keluhan Utama" />
                            <textarea id="keluhan_utama" name="keluhan_utama" rows="3" class="mt-1 block w-full px-3 py-2.5 border border-border rounded-md shadow-sm bg-surface text-sm text-text placeholder:text-text-muted focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-none" placeholder="Contoh: Demam 2 hari, batuk berdahak">{{ old('keluhan_utama') }}</textarea>
                            <x-input-error :messages="$errors->get('keluhan_utama')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex justify-between items-center mt-6">
                        <a href="{{ route('kunjungan.index') }}"><x-secondary-button type="button">Kembali</x-secondary-button></a>
                        <x-primary-button>Simpan Kunjungan</x-primary-button>
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


