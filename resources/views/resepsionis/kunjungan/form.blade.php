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

    @php
        $initialPasienId = (string) old('pasien_id', $pasien?->id ?? '');
        $pasiensData = $pasiens->map(fn($p) => [
            'id' => (string) $p->id,
            'no_rm' => $p->no_rm,
            'nama' => $p->nama,
            'tgl_lahir' => $p->tgl_lahir ? $p->tgl_lahir->format('d M Y') : '-',
            'display' => $p->no_rm . ' — ' . $p->nama,
        ])->values();
    @endphp

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

                        <div
                            class="relative"
                            x-data="{
                                open: false,
                                search: '',
                                selectedId: '{{ $initialPasienId }}',
                                pasiens: {{ Js::from($pasiensData) }},
                                init() {
                                    if (this.selectedId) {
                                        const found = this.pasiens.find(p => String(p.id) === String(this.selectedId));
                                        if (found) {
                                            this.search = found.display;
                                        }
                                    }
                                },
                                get filtered() {
                                    if (!this.search) return this.pasiens;
                                    const q = this.search.toLowerCase();
                                    return this.pasiens.filter(p =>
                                        (p.nama && p.nama.toLowerCase().includes(q)) ||
                                        (p.no_rm && p.no_rm.toLowerCase().includes(q))
                                    );
                                },
                                select(p) {
                                    this.selectedId = p.id;
                                    this.search = p.display;
                                    this.open = false;
                                },
                                clear() {
                                    this.selectedId = '';
                                    this.search = '';
                                }
                            }"
                            @click.outside="open = false"
                        >
                            <x-input-label for="pasien_search" value="Pasien" :required="true" />
                            <input type="hidden" name="pasien_id" id="pasien_id" :value="selectedId" required>

                            <div class="relative mt-1">
                                <input
                                    id="pasien_search"
                                    type="text"
                                    x-model="search"
                                    @focus="open = true"
                                    @input="open = true; if (!search) selectedId = ''"
                                    @keydown.escape="open = false"
                                    placeholder="Ketik nama atau No. RM pasien..."
                                    autocomplete="off"
                                    class="block w-full h-9 px-3 pr-9 border border-border rounded-md shadow-xs bg-surface text-sm text-text placeholder:text-text-subtle focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-none transition pointer-coarse:h-11"
                                />
                                <button
                                    type="button"
                                    x-show="search"
                                    @click="clear()"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 w-5 h-5 flex items-center justify-center rounded-full text-text-muted hover:text-text hover:bg-slate-200 transition"
                                    title="Bersihkan pilihan pasien"
                                    aria-label="Bersihkan pilihan pasien"
                                    style="display: none;"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            {{-- Dropdown Hasil Pencarian Pasien --}}
                            <div
                                x-show="open"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 translate-y-1 scale-98"
                                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                x-transition:leave-end="opacity-0 translate-y-1 scale-98"
                                class="absolute z-50 left-0 right-0 mt-1 max-h-56 overflow-y-auto bg-surface border border-border rounded-md shadow-lg divide-y divide-border"
                                style="display: none;"
                            >
                                <template x-for="p in filtered" :key="p.id">
                                    <div
                                        @click="select(p)"
                                        class="px-4 py-2.5 hover:bg-primary-50 cursor-pointer transition duration-150"
                                        :class="String(selectedId) === String(p.id) ? 'bg-primary-50/70 font-semibold' : ''"
                                    >
                                        <div class="flex justify-between items-center">
                                            <div class="font-medium text-sm text-text">
                                                <span x-text="p.nama"></span>
                                            </div>
                                            <div class="font-mono text-xs font-semibold text-primary-700" x-text="p.no_rm"></div>
                                        </div>
                                        <div class="text-xs text-text-muted mt-0.5 flex items-center gap-2">
                                            <span>Tgl. Lahir: <span x-text="p.tgl_lahir"></span></span>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="filtered.length === 0" class="px-4 py-3 text-xs text-text-muted text-center">
                                    Pasien tidak ditemukan.
                                    <a href="{{ route('pasien.create') }}" class="underline text-primary-700 hover:text-primary-800 font-semibold ms-1">
                                        Daftarkan pasien baru
                                    </a>
                                </div>
                            </div>

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
