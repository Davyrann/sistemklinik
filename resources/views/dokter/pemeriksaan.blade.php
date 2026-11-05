<x-app-layout>
    <x-slot name="title">Pemeriksaan — {{ $kunjungan->pasien->nama }}</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="font-semibold text-xl text-text leading-tight">Pemeriksaan Pasien</h1>
                <p class="text-xs text-text-muted mt-0.5">Dokumentasi rekam medis elektronik dan peresepan terapi.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dokter.dashboard') }}">
                    <x-secondary-button>Kembali ke Antrean</x-secondary-button>
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $tv = $kunjungan->tandaVital;
        $rm = $kunjungan->rekamMedis;
        $isLocked = (bool) ($rm?->is_locked);
        $pasienAlergi = $kunjungan->pasien->alergi;
        $alergiList = array_values(array_filter(array_map('trim', explode(',', (string) $pasienAlergi))));

        $initialDiagnoses = [];
        if ($rm && $rm->diagnoses->isNotEmpty()) {
            foreach ($rm->diagnoses as $d) {
                $initialDiagnoses[] = [
                    'kode_icd10' => $d->kode_icd10 ?? '',
                    'nama_diagnosis' => $d->nama_diagnosis ?? '',
                    'is_primary' => (bool) $d->is_primary,
                ];
            }
        } else {
            $initialDiagnoses[] = [
                'kode_icd10' => '',
                'nama_diagnosis' => '',
                'is_primary' => true,
            ];
        }

        $initialResepItems = [];
        if ($kunjungan->resep && $kunjungan->resep->details->isNotEmpty()) {
            foreach ($kunjungan->resep->details as $rd) {
                $initialResepItems[] = [
                    'obat_id' => (string) $rd->obat_id,
                    'jumlah' => $rd->jumlah,
                    'dosis' => $rd->dosis ?? '',
                    'frekuensi' => $rd->frekuensi ?? '',
                    'aturan_pakai' => $rd->aturan_pakai ?? '',
                ];
            }
        }
    @endphp

    <div
        class="py-6"
        x-data="{
            activeTab: 'soap',
            bb: '{{ old('bb', $tv?->bb ?? '') }}',
            tb: '{{ old('tb', $tv?->tb ?? '') }}',
            get imt() {
                const b = parseFloat(this.bb);
                const t = parseFloat(this.tb);
                if (b > 0 && t > 0) {
                    return (b / Math.pow(t / 100, 2)).toFixed(1);
                }
                return '{{ $tv?->imt ?? '-' }}';
            },
            diagnoses: {{ Js::from(old('diagnoses', $initialDiagnoses)) }},
            addDiagnosis() {
                this.diagnoses.push({ kode_icd10: '', nama_diagnosis: '', is_primary: false });
            },
            removeDiagnosis(index) {
                if (this.diagnoses.length > 1) {
                    this.diagnoses.splice(index, 1);
                }
            },
            allergens: {{ Js::from($alergiList) }},
            obats: {{ Js::from($obats->map(fn($o) => [
                'id' => $o->id,
                'kode' => $o->kode,
                'nama_generik' => $o->nama_generik,
                'nama_dagang' => $o->nama_dagang,
                'satuan' => $o->satuan,
                'stok' => $o->stok,
                'harga' => $o->harga,
                'formatted_harga' => number_format($o->harga, 0, ',', '.'),
            ])) }},
            resepItems: {{ Js::from(old('resep_items', $initialResepItems)) }},
            addResepItem() {
                this.resepItems.push({ obat_id: '', jumlah: 1, dosis: '', frekuensi: '', aturan_pakai: '' });
            },
            removeResepItem(index) {
                this.resepItems.splice(index, 1);
            },
            getAllergyWarning(obatId) {
                if (!obatId || this.allergens.length === 0) return null;
                const o = this.obats.find(item => String(item.id) === String(obatId));
                if (!o) return null;
                const fullName = (o.nama_generik + ' ' + (o.nama_dagang || '')).toLowerCase();
                for (let al of this.allergens) {
                    if (al && fullName.includes(al.toLowerCase())) {
                        return al;
                    }
                }
                return null;
            },
            get hasAllergyConflict() {
                return this.resepItems.some(item => this.getAllergyWarning(item.obat_id) !== null);
            },
            allergyJustification: '{{ addslashes(old('resep_catatan', $kunjungan->resep?->catatan ?? '')) }}',
            get canSubmit() {
                if (!this.hasAllergyConflict) return true;
                return this.allergyJustification.trim().length > 0;
            },
            bukaModalKonfirmasi() {
                const form = document.getElementById('form-pemeriksaan');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }
                if (this.hasAllergyConflict && !this.canSubmit) {
                    this.activeTab = 'resep';
                    alert('Peringatan Keselamatan Pasien: Obat yang dipilih berpotensi memicu alergi pasien. Wajib mengisi justifikasi klinis tertulis sebelum menyelesaikan pemeriksaan.');
                    this.$nextTick(() => {
                        const el = document.getElementById('resep_catatan');
                        if (el) el.focus();
                    });
                    return;
                }
                $dispatch('open-modal', 'modal-konfirmasi-selesai');
            },
            submitDraft() {
                if (this.hasAllergyConflict && !this.canSubmit) {
                    this.activeTab = 'resep';
                    alert('Peringatan Keselamatan Pasien: Obat yang dipilih berpotensi memicu alergi pasien. Wajib mengisi justifikasi klinis tertulis sebelum menyimpan draf.');
                    this.$nextTick(() => {
                        const el = document.getElementById('resep_catatan');
                        if (el) el.focus();
                    });
                    return;
                }
                document.getElementById('action-input').value = 'draft';
                document.getElementById('form-pemeriksaan').submit();
            }
        }"
    >
        {{-- Main Container with pb-28 so content is never covered by sticky action footer --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 pb-28">
            @if (session('status'))
                <div class="bg-primary-50 border border-primary-100 text-primary-900 px-4 py-3 rounded-md text-sm" role="status">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-danger px-4 py-3 rounded-md text-sm" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Top Sticky Patient Header (Template C per DESIGN.md:500-506) --}}
            <x-patient-header :kunjungan="$kunjungan" />

            {{-- Horizontal Scrollable Tab Navigation on Mobile --}}
            <div class="border-b border-border overflow-x-auto scrollbar-none -mx-4 sm:mx-0 px-4 sm:px-0">
                <nav class="flex space-x-2 min-w-max" aria-label="Tabs Pemeriksaan">
                    <button
                        type="button"
                        @click="activeTab = 'soap'"
                        :class="activeTab === 'soap'
                            ? 'text-primary-700 border-b-2 border-primary-700 font-semibold'
                            : 'text-text-muted hover:text-text border-b-2 border-transparent font-medium'"
                        class="min-h-11 px-4 py-3 text-sm whitespace-nowrap transition-colors focus:outline-none focus:text-primary-700"
                    >
                        Pemeriksaan (SOAP)
                    </button>
                    <button
                        type="button"
                        @click="activeTab = 'resep'"
                        :class="activeTab === 'resep'
                            ? 'text-primary-700 border-b-2 border-primary-700 font-semibold'
                            : 'text-text-muted hover:text-text border-b-2 border-transparent font-medium'"
                        class="min-h-11 px-4 py-3 text-sm whitespace-nowrap transition-colors focus:outline-none focus:text-primary-700 flex items-center gap-2"
                    >
                        <span>Resep Obat</span>
                        <span
                            x-show="resepItems.length > 0"
                            class="font-mono text-xs px-2 py-0.5 rounded-full bg-primary-50 text-primary-700 font-semibold"
                            x-text="resepItems.length"
                        ></span>
                        <span
                            x-show="hasAllergyConflict"
                            class="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded bg-red-100 text-danger"
                            style="display: none;"
                        >
                            Alergi
                        </span>
                    </button>
                    <button
                        type="button"
                        @click="activeTab = 'riwayat'"
                        :class="activeTab === 'riwayat'
                            ? 'text-primary-700 border-b-2 border-primary-700 font-semibold'
                            : 'text-text-muted hover:text-text border-b-2 border-transparent font-medium'"
                        class="min-h-11 px-4 py-3 text-sm whitespace-nowrap transition-colors focus:outline-none focus:text-primary-700 flex items-center gap-2"
                    >
                        <span>Riwayat</span>
                        @if ($riwayatKunjungan->isNotEmpty())
                            <span class="font-mono text-xs px-2 py-0.5 rounded-full bg-bg text-text-muted font-medium">
                                {{ $riwayatKunjungan->count() }}
                            </span>
                        @endif
                    </button>
                </nav>
            </div>

            {{-- Form Pemeriksaan --}}
            <form
                id="form-pemeriksaan"
                method="POST"
                action="{{ route('dokter.pemeriksaan.update', $kunjungan) }}"
            >
                @csrf
                @method('PUT')
                <input type="hidden" name="action" id="action-input" value="draft">

                {{-- TAB 1: Pemeriksaan (SOAP) & Diagnosis --}}
                <div x-show="activeTab === 'soap'" class="space-y-6">
                    {{-- Tanda Vital (1-col mobile, 4-col desktop, fixed units in mono) --}}
                    <div class="bg-surface border border-border shadow-sm rounded-lg p-4 sm:p-6 space-y-4">
                        <div>
                            <h2 class="font-semibold text-text text-base">Tanda Vital</h2>
                            <p class="text-xs text-text-muted mt-0.5">Pengukuran fisik objektif tanda-tanda vital pasien.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <x-input-label for="td_sistolik" value="TD Sistolik" />
                                <div class="relative mt-1">
                                    <input
                                        id="td_sistolik"
                                        name="td_sistolik"
                                        type="number"
                                        :value="'{{ old('td_sistolik', $tv?->td_sistolik) }}'"
                                        placeholder="120"
                                        @disabled($isLocked)
                                        class="block w-full h-9 px-3 pr-16 border border-border rounded-md shadow-sm bg-surface text-sm text-text font-mono focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-none disabled:opacity-50 disabled:bg-bg"
                                    />
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-mono text-text-muted pointer-events-none">mmHg</span>
                                </div>
                                <x-input-error :messages="$errors->get('td_sistolik')" class="mt-1" />
                            </div>

                            <div>
                                <x-input-label for="td_diastolik" value="TD Diastolik" />
                                <div class="relative mt-1">
                                    <input
                                        id="td_diastolik"
                                        name="td_diastolik"
                                        type="number"
                                        :value="'{{ old('td_diastolik', $tv?->td_diastolik) }}'"
                                        placeholder="80"
                                        @disabled($isLocked)
                                        class="block w-full h-9 px-3 pr-16 border border-border rounded-md shadow-sm bg-surface text-sm text-text font-mono focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-none disabled:opacity-50 disabled:bg-bg"
                                    />
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-mono text-text-muted pointer-events-none">mmHg</span>
                                </div>
                                <x-input-error :messages="$errors->get('td_diastolik')" class="mt-1" />
                            </div>

                            <div>
                                <x-input-label for="nadi" value="Nadi" />
                                <div class="relative mt-1">
                                    <input
                                        id="nadi"
                                        name="nadi"
                                        type="number"
                                        :value="'{{ old('nadi', $tv?->nadi) }}'"
                                        placeholder="80"
                                        @disabled($isLocked)
                                        class="block w-full h-9 px-3 pr-14 border border-border rounded-md shadow-sm bg-surface text-sm text-text font-mono focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-none disabled:opacity-50 disabled:bg-bg"
                                    />
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-mono text-text-muted pointer-events-none">x/mnt</span>
                                </div>
                                <x-input-error :messages="$errors->get('nadi')" class="mt-1" />
                            </div>

                            <div>
                                <x-input-label for="suhu" value="Suhu Tubuh" />
                                <div class="relative mt-1">
                                    <input
                                        id="suhu"
                                        name="suhu"
                                        type="number"
                                        step="0.1"
                                        :value="'{{ old('suhu', $tv?->suhu) }}'"
                                        placeholder="36.5"
                                        @disabled($isLocked)
                                        class="block w-full h-9 px-3 pr-10 border border-border rounded-md shadow-sm bg-surface text-sm text-text font-mono focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-none disabled:opacity-50 disabled:bg-bg"
                                    />
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-mono text-text-muted pointer-events-none">°C</span>
                                </div>
                                <x-input-error :messages="$errors->get('suhu')" class="mt-1" />
                            </div>

                            <div>
                                <x-input-label for="rr" value="Respirasi (RR)" />
                                <div class="relative mt-1">
                                    <input
                                        id="rr"
                                        name="rr"
                                        type="number"
                                        :value="'{{ old('rr', $tv?->rr) }}'"
                                        placeholder="20"
                                        @disabled($isLocked)
                                        class="block w-full h-9 px-3 pr-14 border border-border rounded-md shadow-sm bg-surface text-sm text-text font-mono focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-none disabled:opacity-50 disabled:bg-bg"
                                    />
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-mono text-text-muted pointer-events-none">x/mnt</span>
                                </div>
                                <x-input-error :messages="$errors->get('rr')" class="mt-1" />
                            </div>

                            <div>
                                <x-input-label for="spo2" value="Saturasi (SpO2)" />
                                <div class="relative mt-1">
                                    <input
                                        id="spo2"
                                        name="spo2"
                                        type="number"
                                        :value="'{{ old('spo2', $tv?->spo2) }}'"
                                        placeholder="98"
                                        @disabled($isLocked)
                                        class="block w-full h-9 px-3 pr-8 border border-border rounded-md shadow-sm bg-surface text-sm text-text font-mono focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-none disabled:opacity-50 disabled:bg-bg"
                                    />
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-mono text-text-muted pointer-events-none">%</span>
                                </div>
                                <x-input-error :messages="$errors->get('spo2')" class="mt-1" />
                            </div>

                            <div>
                                <x-input-label for="bb" value="Berat Badan" />
                                <div class="relative mt-1">
                                    <input
                                        id="bb"
                                        name="bb"
                                        type="number"
                                        step="0.1"
                                        x-model="bb"
                                        placeholder="60"
                                        @disabled($isLocked)
                                        class="block w-full h-9 px-3 pr-10 border border-border rounded-md shadow-sm bg-surface text-sm text-text font-mono focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-none disabled:opacity-50 disabled:bg-bg"
                                    />
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-mono text-text-muted pointer-events-none">kg</span>
                                </div>
                                <x-input-error :messages="$errors->get('bb')" class="mt-1" />
                            </div>

                            <div>
                                <x-input-label for="tb" value="Tinggi Badan" />
                                <div class="relative mt-1">
                                    <input
                                        id="tb"
                                        name="tb"
                                        type="number"
                                        step="0.1"
                                        x-model="tb"
                                        placeholder="165"
                                        @disabled($isLocked)
                                        class="block w-full h-9 px-3 pr-10 border border-border rounded-md shadow-sm bg-surface text-sm text-text font-mono focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-none disabled:opacity-50 disabled:bg-bg"
                                    />
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-mono text-text-muted pointer-events-none">cm</span>
                                </div>
                                <x-input-error :messages="$errors->get('tb')" class="mt-1" />
                            </div>
                        </div>

                        <div class="p-3 bg-bg border border-border rounded-md flex items-center justify-between text-xs">
                            <span class="text-text-muted font-medium">Indeks Massa Tubuh (IMT):</span>
                            <span class="font-mono font-semibold text-text" x-text="imt + ' kg/m²'"></span>
                        </div>
                    </div>

                    {{-- SOAP (Textareas min-h-24 px-3 py-2) --}}
                    <div class="bg-surface border border-border shadow-sm rounded-lg p-4 sm:p-6 space-y-4">
                        <div>
                            <h2 class="font-semibold text-text text-base">Rekam Medis (SOAP)</h2>
                            <p class="text-xs text-text-muted mt-0.5">Catatan klinis Subjektif, Objektif, Asesmen, dan Rencana Terapi.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="subjective" value="Subjektif (S) — Keluhan & Anamnesis" />
                                <textarea
                                    id="subjective"
                                    name="subjective"
                                    rows="3"
                                    @disabled($isLocked)
                                    placeholder="Keluhan utama, riwayat penyakit saat ini..."
                                    class="mt-1 block w-full min-h-24 px-3 py-2 border border-border rounded-md shadow-sm bg-surface text-sm text-text placeholder:text-text-subtle focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-none disabled:opacity-50 disabled:bg-bg"
                                >{{ old('subjective', $rm?->subjective ?? $kunjungan->keluhan_utama) }}</textarea>
                                <x-input-error :messages="$errors->get('subjective')" class="mt-1" />
                            </div>

                            <div>
                                <x-input-label for="objective" value="Objektif (O) — Pemeriksaan Fisik" />
                                <textarea
                                    id="objective"
                                    name="objective"
                                    rows="3"
                                    @disabled($isLocked)
                                    placeholder="Hasil pemeriksaan fisik umum dan status lokalis..."
                                    class="mt-1 block w-full min-h-24 px-3 py-2 border border-border rounded-md shadow-sm bg-surface text-sm text-text placeholder:text-text-subtle focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-none disabled:opacity-50 disabled:bg-bg"
                                >{{ old('objective', $rm?->objective) }}</textarea>
                                <x-input-error :messages="$errors->get('objective')" class="mt-1" />
                            </div>

                            <div>
                                <x-input-label for="assessment" value="Asesmen (A) — Diagnosis Kerja" />
                                <textarea
                                    id="assessment"
                                    name="assessment"
                                    rows="3"
                                    @disabled($isLocked)
                                    placeholder="Diagnosis kerja atau evaluasi klinis..."
                                    class="mt-1 block w-full min-h-24 px-3 py-2 border border-border rounded-md shadow-sm bg-surface text-sm text-text placeholder:text-text-subtle focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-none disabled:opacity-50 disabled:bg-bg"
                                >{{ old('assessment', $rm?->assessment) }}</textarea>
                                <x-input-error :messages="$errors->get('assessment')" class="mt-1" />
                            </div>

                            <div>
                                <x-input-label for="plan" value="Rencana (P) — Rencana Terapi & Edukasi" />
                                <textarea
                                    id="plan"
                                    name="plan"
                                    rows="3"
                                    @disabled($isLocked)
                                    placeholder="Rencana tindakan medik, edukasi, jadwal kontrol..."
                                    class="mt-1 block w-full min-h-24 px-3 py-2 border border-border rounded-md shadow-sm bg-surface text-sm text-text placeholder:text-text-subtle focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-none disabled:opacity-50 disabled:bg-bg"
                                >{{ old('plan', $rm?->plan) }}</textarea>
                                <x-input-error :messages="$errors->get('plan')" class="mt-1" />
                            </div>
                        </div>

                        {{-- Diagnosis ICD-10 --}}
                        <div class="pt-4 border-t border-border space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <div>
                                    <h3 class="font-semibold text-text text-base">Diagnosis ICD-10</h3>
                                    <p class="text-xs text-text-muted mt-0.5">Tentukan diagnosis kerja. Minimal satu diagnosis primer bertanda centang.</p>
                                </div>
                                @if (! $isLocked)
                                    <x-secondary-button type="button" @click="addDiagnosis()" class="self-start sm:self-auto text-xs">
                                        + Tambah Diagnosis
                                    </x-secondary-button>
                                @endif
                            </div>

                            <div class="space-y-3">
                                <template x-for="(diag, index) in diagnoses" :key="index">
                                    <div class="p-3 sm:p-4 bg-bg border border-border rounded-md space-y-3">
                                        <div class="flex items-center justify-between pb-2 border-b border-border text-xs">
                                            <span class="font-medium text-text-muted">
                                                Diagnosis #<span x-text="index + 1"></span>
                                            </span>
                                            @if (! $isLocked)
                                                <button
                                                    type="button"
                                                    @click="removeDiagnosis(index)"
                                                    x-show="diagnoses.length > 1"
                                                    class="inline-flex items-center gap-1 text-xs text-danger hover:underline font-medium"
                                                    title="Hapus diagnosis"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    <span>Hapus</span>
                                                </button>
                                            @endif
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                                            <div class="sm:col-span-3">
                                                <x-input-label value="Kode ICD-10" />
                                                <input
                                                    type="text"
                                                    :name="'diagnoses[' + index + '][kode_icd10]'"
                                                    x-model="diag.kode_icd10"
                                                    placeholder="J06.9"
                                                    @disabled($isLocked)
                                                    class="mt-1 block w-full h-9 px-3 border border-border rounded-md shadow-sm bg-surface text-sm text-text font-mono uppercase focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-none disabled:opacity-50 disabled:bg-bg"
                                                />
                                            </div>
                                            <div class="sm:col-span-6">
                                                <x-input-label value="Nama Diagnosis" />
                                                <input
                                                    type="text"
                                                    :name="'diagnoses[' + index + '][nama_diagnosis]'"
                                                    x-model="diag.nama_diagnosis"
                                                    placeholder="Nama diagnosis penyakit"
                                                    @disabled($isLocked)
                                                    class="mt-1 block w-full h-9 px-3 border border-border rounded-md shadow-sm bg-surface text-sm text-text placeholder:text-text-subtle focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-none disabled:opacity-50 disabled:bg-bg"
                                                />
                                            </div>
                                            <div class="sm:col-span-3">
                                                <div class="h-9 flex items-center mt-1">
                                                    <label class="inline-flex items-center gap-2 text-sm text-text cursor-pointer select-none">
                                                        <input
                                                            type="checkbox"
                                                            :name="'diagnoses[' + index + '][is_primary]'"
                                                            value="1"
                                                            x-model="diag.is_primary"
                                                            @disabled($isLocked)
                                                            class="rounded border-border text-primary-700 focus:ring-primary-600"
                                                        />
                                                        <span class="font-medium text-xs sm:text-sm">Diagnosis Primer</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB 2: Resep Obat --}}
                <div x-show="activeTab === 'resep'" class="space-y-6" style="display: none;">
                    <div class="bg-surface border border-border shadow-sm rounded-lg p-4 sm:p-6 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div>
                                <h2 class="font-semibold text-text text-base">Resep Obat</h2>
                                <p class="text-xs text-text-muted mt-0.5">Pilih obat dari inventaris aktif klinik. Pengurangan stok dan tagihan diproses saat pemeriksaan selesai.</p>
                            </div>
                            @if (! $isLocked)
                                <x-secondary-button type="button" @click="addResepItem()" class="self-start sm:self-auto text-xs">
                                    + Tambah Obat
                                </x-secondary-button>
                            @endif
                        </div>

                        {{-- Alert Potensi Alergi Seluruh Resep --}}
                        <div
                            x-show="hasAllergyConflict"
                            class="p-4 rounded-md bg-red-50 border border-red-300 text-danger text-xs space-y-2"
                            style="display: none;"
                        >
                            <div class="flex items-center gap-2 font-semibold text-sm">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span>Peringatan Keselamatan Klinis: Obat Memiliki Potensi Alergi Pasien!</span>
                            </div>
                            <p class="text-text">
                                Satu atau lebih obat yang dipilih cocok dengan riwayat alergi pasien. Sesuai standar keselamatan medis, Anda <strong>wajib mengisi justifikasi klinis tertulis</strong> pada kolom catatan di bawah sebelum dapat menyimpan draf atau menyelesaikan pemeriksaan.
                            </p>
                        </div>

                        <div class="space-y-4">
                            <template x-for="(item, index) in resepItems" :key="index">
                                <div class="p-3 sm:p-4 bg-bg border border-border rounded-md space-y-3">
                                    <div class="flex items-center justify-between pb-2 border-b border-border text-xs">
                                        <span class="font-medium text-text-muted">
                                            Obat #<span x-text="index + 1"></span>
                                        </span>
                                        @if (! $isLocked)
                                            <button
                                                type="button"
                                                @click="removeResepItem(index)"
                                                class="inline-flex items-center gap-1 text-xs text-danger hover:underline font-medium"
                                                title="Hapus obat ini"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span>Hapus</span>
                                            </button>
                                        @endif
                                    </div>

                                    {{-- Inline Clinical Allergy Warning Alert --}}
                                    <div
                                        x-show="getAllergyWarning(item.obat_id)"
                                        x-transition
                                        class="p-2.5 rounded bg-red-50 border border-red-200 text-danger text-xs font-semibold flex items-center gap-2"
                                        style="display: none;"
                                    >
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        <span>Peringatan Klinis: Obat ini cocok dengan riwayat alergi pasien (<span x-text="getAllergyWarning(item.obat_id)"></span>)!</span>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                                        {{-- Autocomplete Obat --}}
                                        <div
                                            class="sm:col-span-9 relative"
                                            x-data="{
                                                open: false,
                                                search: '',
                                                init() {
                                                    const found = $data.obats.find(o => String(o.id) === String(item.obat_id));
                                                    if (found) {
                                                        this.search = found.nama_generik + (found.nama_dagang ? ' (' + found.nama_dagang + ')' : '');
                                                    }
                                                },
                                                get filtered() {
                                                    if (!this.search) return $data.obats;
                                                    const q = this.search.toLowerCase();
                                                    return $data.obats.filter(o =>
                                                        (o.nama_generik && o.nama_generik.toLowerCase().includes(q)) ||
                                                        (o.nama_dagang && o.nama_dagang.toLowerCase().includes(q)) ||
                                                        (o.kode && o.kode.toLowerCase().includes(q))
                                                    );
                                                },
                                                select(o) {
                                                    item.obat_id = o.id;
                                                    this.search = o.nama_generik + (o.nama_dagang ? ' (' + o.nama_dagang + ')' : '');
                                                    this.open = false;
                                                },
                                                clear() {
                                                    item.obat_id = '';
                                                    this.search = '';
                                                }
                                            }"
                                            @click.outside="open = false"
                                        >
                                            <x-input-label value="Cari Obat" />
                                            <input type="hidden" :name="'resep_items[' + index + '][obat_id]'" :value="item.obat_id" required>

                                            <div class="relative mt-1">
                                                <input
                                                    type="text"
                                                    x-model="search"
                                                    @focus="open = true"
                                                    @input="open = true; if (!search) item.obat_id = ''"
                                                    @keydown.escape="open = false"
                                                    placeholder="Ketik nama atau kode obat..."
                                                    @disabled($isLocked)
                                                    autocomplete="off"
                                                    class="block w-full h-9 px-3 pr-9 border border-border rounded-md shadow-sm bg-surface text-sm text-text placeholder:text-text-subtle focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-none disabled:opacity-50 disabled:bg-bg"
                                                />
                                                <button
                                                    type="button"
                                                    x-show="search && !{{ $isLocked ? 'true' : 'false' }}"
                                                    @click="clear()"
                                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 w-5 h-5 flex items-center justify-center rounded-full text-text-muted hover:text-text hover:bg-slate-200 transition"
                                                    title="Bersihkan pilihan obat"
                                                    aria-label="Bersihkan pilihan obat"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </div>

                                            {{-- Dropdown Hasil Pencarian --}}
                                            <div
                                                x-show="open && !{{ $isLocked ? 'true' : 'false' }}"
                                                x-transition
                                                class="absolute z-50 left-0 right-0 mt-1 max-h-56 overflow-y-auto bg-surface border border-border rounded-md shadow-lg divide-y divide-border"
                                                style="display: none;"
                                            >
                                                <template x-for="o in filtered" :key="o.id">
                                                    <div
                                                        @click="select(o)"
                                                        class="px-4 py-3 hover:bg-primary-50 cursor-pointer transition duration-150"
                                                        :class="String(item.obat_id) === String(o.id) ? 'bg-primary-50/70' : ''"
                                                    >
                                                        <div class="flex justify-between items-center">
                                                            <div class="font-medium text-sm text-text">
                                                                <span x-text="o.nama_generik"></span>
                                                                <span x-show="o.nama_dagang" class="text-text-muted font-normal text-xs" x-text="o.nama_dagang ? '(' + o.nama_dagang + ')' : ''"></span>
                                                            </div>
                                                            <div class="font-mono text-xs font-medium text-text" x-text="'Rp ' + o.formatted_harga"></div>
                                                        </div>
                                                        <div class="flex justify-between items-center text-xs text-text-muted mt-1">
                                                            <span class="font-mono" x-text="o.kode"></span>
                                                            <span :class="o.stok <= 10 ? 'text-danger font-semibold' : ''" x-text="'Stok: ' + o.stok + ' ' + o.satuan"></span>
                                                        </div>
                                                    </div>
                                                </template>
                                                <div x-show="filtered.length === 0" class="px-4 py-4 text-xs text-text-muted text-center">
                                                    Obat tidak ditemukan dalam inventaris aktif.
                                                </div>
                                            </div>
                                        </div>

                                        <div class="sm:col-span-3">
                                            <x-input-label value="Jumlah" />
                                            <input
                                                type="number"
                                                min="1"
                                                :name="'resep_items[' + index + '][jumlah]'"
                                                x-model="item.jumlah"
                                                placeholder="10"
                                                @disabled($isLocked)
                                                required
                                                class="mt-1 block w-full h-9 px-3 border border-border rounded-md shadow-sm bg-surface text-sm text-text font-mono focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-none disabled:opacity-50 disabled:bg-bg"
                                            />
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div>
                                            <x-input-label value="Dosis" />
                                            <input
                                                type="text"
                                                :name="'resep_items[' + index + '][dosis]'"
                                                x-model="item.dosis"
                                                placeholder="Contoh: 500 mg / 1 tablet"
                                                @disabled($isLocked)
                                                class="mt-1 block w-full h-9 px-3 border border-border rounded-md shadow-sm bg-surface text-sm text-text font-mono placeholder:text-text-subtle focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-none disabled:opacity-50 disabled:bg-bg"
                                            />
                                        </div>
                                        <div>
                                            <x-input-label value="Frekuensi" />
                                            <input
                                                type="text"
                                                :name="'resep_items[' + index + '][frekuensi]'"
                                                x-model="item.frekuensi"
                                                placeholder="Contoh: 3 × sehari / Tiap 8 jam"
                                                @disabled($isLocked)
                                                class="mt-1 block w-full h-9 px-3 border border-border rounded-md shadow-sm bg-surface text-sm text-text placeholder:text-text-subtle focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-none disabled:opacity-50 disabled:bg-bg"
                                            />
                                        </div>
                                        <div>
                                            <x-input-label value="Aturan Pakai" />
                                            <input
                                                type="text"
                                                :name="'resep_items[' + index + '][aturan_pakai]'"
                                                x-model="item.aturan_pakai"
                                                placeholder="Contoh: Sesudah makan / Habiskan"
                                                @disabled($isLocked)
                                                class="mt-1 block w-full h-9 px-3 border border-border rounded-md shadow-sm bg-surface text-sm text-text placeholder:text-text-subtle focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-none disabled:opacity-50 disabled:bg-bg"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <div x-show="resepItems.length === 0" class="text-sm text-text-muted py-2">
                                Belum ada obat yang diresepkan. Klik <strong>+ Tambah Obat</strong> jika pasien membutuhkan terapi medikamentosa.
                            </div>

                            {{-- Catatan Resep / Justifikasi Klinis Alergi Tertulis --}}
                            <div class="pt-2">
                                <label for="resep_catatan" class="block text-xs font-medium text-text">
                                    <span x-show="!hasAllergyConflict">Catatan Resep / Instruksi Farmasi</span>
                                    <span x-show="hasAllergyConflict" class="text-danger font-semibold">
                                        Justifikasi Klinis Tertulis (Wajib karena ada potensi alergi) <span class="text-danger font-normal">(wajib)</span>
                                    </span>
                                </label>
                                <textarea
                                    id="resep_catatan"
                                    name="resep_catatan"
                                    x-model="allergyJustification"
                                    rows="3"
                                    @disabled($isLocked)
                                    :placeholder="hasAllergyConflict ? 'Tuliskan pertimbangan dan alasan klinis meresepkan obat yang memiliki potensi alergi ini...' : 'Instruksi tambahan bagi pasien atau bagian farmasi...'"
                                    :class="hasAllergyConflict ? 'border-red-300 focus:border-danger focus:ring-red-500/20' : 'border-border focus:border-primary-600 focus:ring-primary-600/20'"
                                    class="mt-1 block w-full min-h-24 px-3 py-2 border rounded-md shadow-sm bg-surface text-sm text-text placeholder:text-text-subtle focus:ring-2 focus:outline-none disabled:opacity-50 disabled:bg-bg"
                                >{{ old('resep_catatan', $kunjungan->resep?->catatan) }}</textarea>
                                <x-input-error :messages="$errors->get('resep_catatan')" class="mt-1" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB 3: Riwayat Kunjungan Sebelumnya --}}
                <div x-show="activeTab === 'riwayat'" class="space-y-6" style="display: none;">
                    <div class="bg-surface border border-border shadow-sm rounded-lg p-4 sm:p-6 space-y-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <h2 class="font-semibold text-text text-base">Riwayat Kunjungan Pasien</h2>
                                <p class="text-xs text-text-muted mt-0.5">Daftar kunjungan terdahulu yang telah diselesaikan.</p>
                            </div>
                            <a href="{{ route('dokter.pasien.show', $kunjungan->pasien) }}" target="_blank" class="text-xs text-primary-700 hover:underline font-medium">
                                Buka Rekam Medis Lengkap
                            </a>
                        </div>

                        @if ($riwayatKunjungan->isNotEmpty())
                            <div class="divide-y divide-border">
                                @foreach ($riwayatKunjungan as $rk)
                                    <div class="py-4 first:pt-0 last:pb-0 text-sm space-y-2">
                                        <div class="flex flex-wrap justify-between items-center text-text-muted gap-2">
                                            <div class="flex items-center gap-2">
                                                <span class="font-mono font-semibold text-text">{{ $rk->tgl_kunjungan->translatedFormat('d M Y') }}</span>
                                                <span>·</span>
                                                <span class="font-mono text-xs">{{ $rk->no_antrean }}</span>
                                            </div>
                                            <span class="text-xs">{{ $rk->poli->nama ?? '-' }} · {{ $rk->dokter->nama ?? '-' }}</span>
                                        </div>
                                        <div class="text-text">
                                            <span class="text-text-muted text-xs block">Keluhan / Asesmen:</span>
                                            <span>{{ $rk->rekamMedis?->assessment ?? ($rk->keluhan_utama ?: '-') }}</span>
                                        </div>
                                        @if ($rk->rekamMedis?->diagnoses && $rk->rekamMedis->diagnoses->isNotEmpty())
                                            <div class="text-xs text-primary-700 font-mono">
                                                Diagnosis: {{ $rk->rekamMedis->diagnoses->pluck('nama_diagnosis')->join(', ') }}
                                            </div>
                                        @endif
                                        @if ($rk->resep && $rk->resep->details->isNotEmpty())
                                            <div class="text-xs text-text-muted">
                                                Resep: {{ $rk->resep->details->map(fn($d) => $d->obat->nama_generik . ' (' . $d->jumlah . ' ' . $d->obat->satuan . ')')->join(', ') }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-text-muted text-sm py-4 text-center">
                                Belum ada riwayat kunjungan sebelumnya untuk pasien ini.
                            </p>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- Sticky Action Footer (Template C per DESIGN.md:500-506) --}}
        <div class="fixed sm:sticky bottom-0 left-0 right-0 z-10 bg-surface/95 border-t border-border p-3 sm:px-6 sm:py-3 shadow-lg flex items-center justify-between gap-3">
            <a href="{{ route('dokter.dashboard') }}">
                <x-secondary-button type="button">Kembali</x-secondary-button>
            </a>

            @if ($isLocked)
                <span class="text-xs sm:text-sm text-text-muted font-medium">
                    Rekam medis terkunci pada {{ $rm->locked_at?->translatedFormat('d M Y H:i') ?? '-' }}.
                </span>
            @else
                <div class="flex items-center gap-2 sm:gap-3">
                    <x-secondary-button type="button" @click="submitDraft()">
                        Simpan Draf
                    </x-secondary-button>
                    <x-primary-button type="button" @click="bukaModalKonfirmasi()">
                        Selesaikan Pemeriksaan
                    </x-primary-button>
                </div>
            @endif
        </div>

        {{-- Modal Konfirmasi Selesaikan Pemeriksaan --}}
        <x-modal name="modal-konfirmasi-selesai" maxWidth="md" focusable>
            <div class="p-6 space-y-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-amber-50 border border-amber-200 flex items-center justify-center shrink-0 text-warning">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-text">Selesaikan & Kunci Pemeriksaan?</h3>
                        <p class="text-xs text-text-muted mt-1">
                            Pasien: <strong class="text-text">{{ $kunjungan->pasien->nama }}</strong> · Antrean: <strong class="font-mono text-text">{{ $kunjungan->no_antrean }}</strong>
                        </p>
                    </div>
                </div>

                <div class="text-xs text-text-muted space-y-2 bg-bg p-3.5 rounded-md border border-border">
                    <p>
                        Rekam medis ini akan ditandatangani dan <strong>dikunci permanen</strong>. Tindakan ini tidak dapat dibatalkan:
                    </p>
                    <ul class="list-disc list-inside space-y-1 text-text">
                        <li>Data fisik, catatan SOAP, dan diagnosis ICD-10 akan terkunci.</li>
                        <li>Stok obat yang diresepkan otomatis dipotong dari inventaris farmasi.</li>
                        <li>Rincian biaya obat dan jasa konsultasi langsung diteruskan ke kasir.</li>
                    </ul>
                </div>

                <div class="pt-2 flex justify-end gap-3">
                    <x-secondary-button type="button" x-on:click="$dispatch('close')">
                        Periksa Kembali
                    </x-secondary-button>
                    <x-primary-button
                        type="button"
                        x-on:click="document.getElementById('action-input').value = 'selesai'; document.getElementById('form-pemeriksaan').submit()"
                    >
                        Kunci & Selesaikan
                    </x-primary-button>
                </div>
            </div>
        </x-modal>
    </div>
</x-app-layout>
