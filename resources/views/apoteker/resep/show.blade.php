<x-app-layout>
    <x-slot name="title">Verifikasi Resep — {{ $pasien->nama }}</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h1 class="font-semibold text-xl text-text leading-tight">
                    Verifikasi & Penyerahan Resep
                </h1>
                <p class="text-xs text-text-muted mt-0.5">Telaah obat, pengecekan alergi, penyiapan etiket, dan pembayaran kasir.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('apoteker.resep.index') }}" class="h-9 px-3 inline-flex items-center text-xs font-medium text-text-muted hover:text-text bg-surface border border-border rounded-md hover:bg-slate-50 transition">
                    Kembali ke Daftar Resep
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Patient Header --}}
    <x-patient-header :kunjungan="$resep->kunjungan" />

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-primary-50 border border-primary-200 text-primary-900 px-4 py-3 rounded-md text-sm font-medium" role="status">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-danger px-4 py-3 rounded-md text-sm font-medium" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            {{-- 3-State Allergy Banner --}}
            <x-allergy-banner :pasien="$pasien" />

            {{-- Ringkasan Resep & Dokter --}}
            <div class="bg-surface border border-border rounded-lg p-4 sm:p-6 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-border">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs font-semibold text-text-muted">No. Resep:</span>
                            <span class="font-mono text-sm font-semibold text-text">RSP-{{ str_pad($resep->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="text-xs text-text-muted mt-1">
                            Poli: <span class="font-medium text-text">{{ $resep->kunjungan->poli->nama ?? '-' }}</span>
                            <span class="mx-1.5 text-border">|</span>
                            Dokter: <span class="font-medium text-text">{{ $resep->dokter->nama ?? '-' }}</span>
                            <span class="mx-1.5 text-border">|</span>
                            Waktu Dibuat: <span class="font-mono font-medium text-text">{{ $resep->created_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @php
                            $token = match ($resep->status) {
                                'menunggu', 'draft' => 'warning',
                                'diproses' => 'info',
                                'diserahkan' => 'success',
                                default => 'neutral',
                            };
                            $label = match ($resep->status) {
                                'menunggu', 'draft' => 'Menunggu Verifikasi',
                                'diproses' => 'Sedang Disiapkan',
                                'diserahkan' => 'Telah Diserahkan',
                                default => ucfirst($resep->status),
                            };
                        @endphp
                        <x-status-pill :status="$token" :label="$label" />
                        <a
                            href="{{ route('apoteker.resep.etiket', $resep) }}"
                            target="_blank"
                            class="h-9 px-3.5 inline-flex items-center justify-center rounded-md text-xs font-semibold bg-surface border border-border text-text hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 transition"
                        >
                            Cetak Etiket Obat
                        </a>
                    </div>
                </div>

                @if ($resep->catatan)
                    <div class="bg-bg border border-border-subtle rounded-md p-3 text-xs text-text">
                        <span class="font-semibold text-text-muted block mb-1">Catatan Resep Dokter:</span>
                        {{ $resep->catatan }}
                    </div>
                @endif
            </div>

            {{-- Daftar Obat Resep --}}
            <div class="bg-surface border border-border rounded-lg overflow-hidden">
                <div class="px-4 py-3 bg-bg border-b border-border flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold text-sm text-text">Rincian Obat & Verifikasi Telaah</h2>
                        <p class="text-xs text-text-muted">Pastikan kesesuaian obat, dosis, aturan pakai, dan ketersediaan stok fisik.</p>
                    </div>
                </div>
                <x-table-container class="border-0 rounded-none">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b border-border">
                            <tr class="text-left text-text-muted text-xs">
                                <th class="px-3 py-3 pl-4 font-medium">Nama Obat</th>
                                <th class="px-3 py-3 font-medium">Dosis & Frekuensi</th>
                                <th class="px-3 py-3 font-medium">Aturan Pakai</th>
                                <th class="px-3 py-3 font-medium font-mono text-center">Jumlah</th>
                                <th class="px-3 py-3 font-medium font-mono text-center">Stok Gudang</th>
                                <th class="px-3 py-3 pr-4 font-medium text-right">Status Telaah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border-subtle">
                            @forelse ($itemsWithWarning as $item)
                                @php
                                    $detail = $item['detail'];
                                    $hasWarning = $item['has_allergy_warning'];
                                    $stokCukup = $item['stok_cukup'];
                                @endphp
                                <tr @class([
                                    'hover:bg-slate-50/70 transition-colors',
                                    'bg-red-50/40' => $hasWarning,
                                ])>
                                    <td class="px-3 py-3.5 pl-4">
                                        <div class="font-medium text-text">{{ $detail->obat->nama_generik ?? 'Obat tidak ditemukan' }}</div>
                                        @if ($detail->obat?->nama_dagang)
                                            <div class="text-xs text-text-muted">({{ $detail->obat->nama_dagang }})</div>
                                        @endif
                                        <div class="font-mono text-xs text-text-subtle">Kode: {{ $detail->obat->kode ?? '-' }} | Satuan: {{ $detail->obat->satuan ?? '-' }}</div>
                                        @if ($hasWarning)
                                            <div class="mt-1.5 inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-danger text-white">
                                                Peringatan: Pasien memiliki riwayat alergi terhadap obat ini!
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3.5 text-xs">
                                        <div class="font-medium text-text">{{ $detail->dosis ?: '-' }}</div>
                                        <div class="text-text-muted">{{ $detail->frekuensi ?: '-' }}</div>
                                    </td>
                                    <td class="px-3 py-3.5 text-xs text-text">
                                        {{ $detail->aturan_pakai ?: 'Sesuai anjuran' }}
                                    </td>
                                    <td class="px-3 py-3.5 font-mono text-xs font-semibold text-center text-text">
                                        {{ $detail->jumlah }} {{ $detail->obat->satuan ?? '' }}
                                    </td>
                                    <td class="px-3 py-3.5 font-mono text-xs text-center">
                                        <span @class([
                                            'font-semibold',
                                            'text-success' => $stokCukup,
                                            'text-danger' => ! $stokCukup,
                                        ])>
                                            {{ $detail->obat->stok ?? 0 }} {{ $detail->obat->satuan ?? '' }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3.5 pr-4 text-right">
                                        @if ($hasWarning)
                                            <x-status-pill status="danger" label="Bahaya Alergi" />
                                        @elseif (! $stokCukup)
                                            <x-status-pill status="warning" label="Stok Kurang" />
                                        @else
                                            <x-status-pill status="success" label="Sesuai" />
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-xs text-text-muted">
                                        Tidak ada item obat dalam resep ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </x-table-container>
            </div>

            {{-- Bagian Kasir & Pembayaran Terpadu (Apoteker sekaligus Kasir) --}}
            @php
                $tagihan = $resep->kunjungan->tagihan;
            @endphp
            @if ($tagihan)
                <div class="bg-surface border border-border rounded-lg p-4 sm:p-6 space-y-4" x-data="{
                    isLunas: {{ $tagihan->status === 'lunas' ? 'true' : 'false' }},
                    loading: false,
                    successMsg: '',
                    errorMsg: '',
                    kuitansiUrl: '{{ route('tagihan.kuitansi', $tagihan) }}',
                    async submitPayment(event) {
                        this.loading = true;
                        this.successMsg = '';
                        this.errorMsg = '';
                        const form = event.target;
                        try {
                            const res = await fetch(form.action, {
                                method: 'POST',
                                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                                body: new FormData(form),
                            });
                            const data = await res.json();
                            if (!res.ok) {
                                this.errorMsg = data.message || 'Terjadi kesalahan saat memproses pembayaran.';
                                this.loading = false;
                                return;
                            }
                            this.successMsg = data.message || 'Pembayaran berhasil dicatat.';
                            if (data.status === 'lunas') {
                                this.isLunas = true;
                            }
                            this.loading = false;
                        } catch (err) {
                            this.errorMsg = 'Gagal menghubungi server. Silakan coba lagi.';
                            this.loading = false;
                        }
                    }
                }">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-border">
                        <div>
                            <h2 class="font-semibold text-base text-text">Pemeriksaan Tagihan & Kasir</h2>
                            <p class="text-xs text-text-muted">Status pelunasan biaya konsultasi, administrasi, dan obat sebelum penyerahan.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-mono font-medium text-text-muted">No. Tagihan:</span>
                            <span class="text-xs font-mono font-bold text-text">{{ $tagihan->no_tagihan }}</span>
                            <template x-if="isLunas">
                                <x-status-pill status="success" label="Lunas" />
                            </template>
                            <template x-if="!isLunas">
                                <x-status-pill status="warning" label="Belum Bayar" />
                            </template>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Rincian Tagihan --}}
                        <div class="bg-bg border border-border-subtle rounded-md p-3.5 space-y-2">
                            <h3 class="text-xs font-semibold text-text-muted uppercase tracking-wider">Rincian Komponen Biaya</h3>
                            <div class="space-y-1.5 text-xs divide-y divide-border-subtle">
                                @foreach ($tagihan->details as $d)
                                    <div class="flex items-center justify-between pt-1 text-text">
                                        <span>{{ $d->deskripsi }} ({{ $d->qty }}x)</span>
                                        <span class="font-mono">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="border-t border-border pt-2 flex items-center justify-between text-sm font-semibold text-text">
                                <span>Total Tagihan Pasien:</span>
                                <span class="font-mono text-primary-700">Rp {{ number_format($tagihan->total_bayar, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        {{-- Form Pembayaran Langsung (Jika Belum Lunas) --}}
                        <div>
                            <div x-show="successMsg" x-transition class="mb-3 p-3 bg-primary-50 border border-primary-200 text-primary-900 rounded-md text-xs font-medium" x-text="successMsg" style="display: none;"></div>
                            <div x-show="errorMsg" x-transition class="mb-3 p-3 bg-red-50 border border-red-200 text-danger rounded-md text-xs font-medium" x-text="errorMsg" style="display: none;"></div>

                            <div x-show="isLunas" x-transition class="bg-emerald-50 border border-emerald-200 rounded-md p-4 text-xs text-emerald-950 space-y-2" style="{{ $tagihan->status === 'lunas' ? '' : 'display: none;' }}">
                                <div class="flex items-center gap-2 font-semibold">
                                    <span>Tagihan Pasien Telah Lunas</span>
                                </div>
                                <p class="text-text-muted">Pembayaran telah diterima oleh kasir. Obat dapat diserahkan kepada pasien.</p>
                                <div class="pt-2">
                                    <a
                                        :href="kuitansiUrl"
                                        target="_blank"
                                        class="h-9 px-3.5 inline-flex items-center justify-center rounded-md text-xs font-semibold bg-surface border border-border text-text hover:bg-slate-50 transition"
                                    >
                                        Cetak Kuitansi Kasir
                                    </a>
                                </div>
                            </div>

                            <div x-show="!isLunas" class="border border-border rounded-md p-4 space-y-3 bg-surface">
                                <h3 class="text-xs font-semibold text-text">Terima Pembayaran Kasir Sekarang</h3>
                                <form method="POST" action="{{ route('tagihan.bayar', $tagihan) }}" @submit.prevent="submitPayment" class="space-y-3">
                                    @csrf
                                    <div class="space-y-1">
                                        <x-input-label for="metode" value="Metode Pembayaran (wajib)" />
                                        <x-payment-method-select id="metode" name="metode" required class="w-full text-xs" />
                                        <x-input-error :messages="$errors->get('metode')" />
                                    </div>
                                    <div class="space-y-1">
                                        <x-input-label for="jumlah" value="Nominal Diterima (wajib)" />
                                        <x-text-input
                                            id="jumlah"
                                            name="jumlah"
                                            type="number"
                                            min="{{ $tagihan->total_bayar }}"
                                            value="{{ old('jumlah', $tagihan->total_bayar) }}"
                                            required
                                            class="w-full text-xs font-mono"
                                        />
                                    </div>
                                    <x-primary-button type="submit" class="w-full justify-center text-xs h-10" ::disabled="loading">
                                        <span x-show="!loading">Proses Pembayaran & Cetak Kuitansi</span>
                                        <span x-show="loading" style="display: none;">Memproses...</span>
                                    </x-primary-button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Tombol Aksi Apoteker --}}
            <div class="bg-surface border border-border rounded-lg p-4 sm:p-6 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                <div>
                    <div class="text-sm font-semibold text-text">Siklus Dispensing Farmasi</div>
                    <div class="text-xs text-text-muted">Ubah status resep saat obat mulai disiapkan atau saat diserahkan ke tangan pasien.</div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    @if ($resep->status === 'menunggu' || $resep->status === 'draft')
                        <form method="POST" action="{{ route('apoteker.resep.status', $resep) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="diproses">
                            <x-secondary-button type="submit" class="h-11 sm:h-9 text-xs">
                                Mulai Siapkan Obat
                            </x-secondary-button>
                        </form>
                    @endif

                    @if ($resep->status !== 'diserahkan')
                        <form method="POST" action="{{ route('apoteker.resep.status', $resep) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="diserahkan">
                            <x-primary-button type="submit" class="h-11 sm:h-9 text-xs">
                                Serahkan Obat ke Pasien
                            </x-primary-button>
                        </form>
                    @else
                        <div class="text-xs font-medium text-success bg-green-50 border border-green-200 px-3 py-1.5 rounded">
                            Obat telah diserahkan kepada pasien.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
