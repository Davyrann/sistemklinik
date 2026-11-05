<x-app-layout>
    <x-slot name="title">Tagihan #{{ str_pad($tagihan->id, 5, '0', STR_PAD_LEFT) }} — {{ $tagihan->kunjungan->pasien->nama }}</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-semibold text-xl text-text leading-tight">
                    Tagihan #{{ str_pad($tagihan->id, 5, '0', STR_PAD_LEFT) }}
                </h1>
                <p class="text-xs text-text-muted mt-0.5">
                    Rincian layanan, obat, dan pencatatan pembayaran kasir
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('tagihan.kuitansi', $tagihan) }}" target="_blank">
                    <x-secondary-button>
                        Cetak Kuitansi
                    </x-secondary-button>
                </a>
                <a href="{{ route('tagihan.index') }}">
                    <x-secondary-button>
                        Kembali ke Tagihan
                    </x-secondary-button>
                </a>
            </div>
        </div>
    </x-slot>

    <x-datatable />

    <div class="py-6 pb-28">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            {{-- Patient Header Component --}}
            <x-patient-header :kunjungan="$tagihan->kunjungan" />

            @if (session('status'))
                <div class="bg-primary-50 border border-primary-100 text-primary-900 px-4 py-3 rounded-md text-sm" role="status">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-danger px-4 py-3 rounded-md text-sm" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Info Ringkas Kunjungan & Status --}}
            <div class="bg-surface border border-border shadow-sm rounded-lg p-4 sm:p-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-sm pb-3 border-b border-border">
                    <div class="space-y-0.5">
                        <span class="text-xs text-text-muted">Layanan / Poli</span>
                        <div class="font-medium text-text">
                            {{ $tagihan->kunjungan->poli->nama ?? 'Poli Umum' }}
                            @if ($tagihan->kunjungan->dokter)
                                · <span class="text-text-muted">Dokter:</span> {{ $tagihan->kunjungan->dokter->nama }}
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-text-muted">Status Tagihan:</span>
                        @if ($tagihan->status === 'lunas')
                            <x-status-pill status="success" label="Lunas" />
                        @elseif ($tagihan->status === 'belum')
                            <x-status-pill status="warning" label="Belum Lunas" />
                        @else
                            <x-status-pill status="neutral" :label="ucfirst($tagihan->status)" />
                        @endif
                    </div>
                </div>

                {{-- Itemized Bill Table --}}
                <div class="mt-4">
                    <h3 class="text-sm font-semibold text-text mb-2">Rincian Tagihan</h3>
                    <x-table-container>
                        <table class="w-full text-sm">
                            <thead class="bg-bg border-b border-border">
                                <tr class="text-left text-text-muted text-xs uppercase tracking-wider font-semibold">
                                    <th scope="col" class="pl-4 sm:pl-6 pr-3 py-3">Deskripsi</th>
                                    <th scope="col" class="px-3 py-3 text-center">Jenis</th>
                                    <th scope="col" class="px-3 py-3 text-right">Qty</th>
                                    <th scope="col" class="px-3 py-3 text-right">Harga</th>
                                    <th scope="col" class="pl-3 pr-4 sm:pr-6 py-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @forelse ($tagihan->details as $d)
                                    <tr>
                                        <td class="pl-4 sm:pl-6 pr-3 py-2.5 text-text font-medium">
                                            {{ $d->deskripsi }}
                                        </td>
                                        <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                            <span class="text-xs px-2 py-0.5 rounded bg-bg border border-border font-mono uppercase text-text-muted">
                                                {{ $d->jenis }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2.5 font-mono text-text text-right whitespace-nowrap">
                                            {{ $d->qty }}
                                        </td>
                                        <td class="px-3 py-2.5 font-mono text-text text-right whitespace-nowrap">
                                            Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}
                                        </td>
                                        <td class="pl-3 pr-4 sm:pr-6 py-2.5 font-mono font-medium text-text text-right whitespace-nowrap">
                                            Rp {{ number_format($d->subtotal, 0, ',', '.') }}
                                        </td>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 sm:px-6 py-8 text-center text-text-muted text-sm">
                                            Tidak ada rincian tagihan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-bg/60 border-t border-border font-semibold text-text">
                                @if ((float) $tagihan->diskon > 0)
                                    <tr>
                                        <td colspan="4" class="px-3 py-2 text-right text-xs uppercase tracking-wider text-text-muted">
                                            Subtotal
                                        </td>
                                        <td class="pl-3 pr-4 sm:pr-6 py-2 font-mono text-right whitespace-nowrap">
                                            Rp {{ number_format($tagihan->total, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="px-3 py-2 text-right text-xs uppercase tracking-wider text-danger">
                                            Diskon
                                        </td>
                                        <td class="pl-3 pr-4 sm:pr-6 py-2 font-mono text-danger text-right whitespace-nowrap">
                                            - Rp {{ number_format($tagihan->diskon, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td colspan="4" class="px-3 py-3 text-right text-sm uppercase tracking-wider">
                                        Total Akhir
                                    </td>
                                    <td class="pl-3 pr-4 sm:pr-6 py-3 font-mono text-base font-bold text-primary-700 text-right whitespace-nowrap">
                                        Rp {{ number_format($tagihan->total_bayar, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </x-table-container>
                </div>
            </div>

            {{-- Riwayat Pembayaran --}}
            <div class="bg-surface border border-border shadow-sm rounded-lg p-4 sm:p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-text">Riwayat Pembayaran</h3>
                    @php
                        $terbayar = (float) $tagihan->pembayarans->sum('jumlah');
                        $sisa = max(0, (float) $tagihan->total_bayar - $terbayar);
                    @endphp
                    <div class="text-xs text-text-muted font-mono">
                        Terbayar: <strong class="text-text font-semibold">Rp {{ number_format($terbayar, 0, ',', '.') }}</strong>
                        @if ($sisa > 0)
                            · Sisa: <strong class="text-danger font-semibold">Rp {{ number_format($sisa, 0, ',', '.') }}</strong>
                        @endif
                    </div>
                </div>

                @if ($tagihan->pembayarans->isNotEmpty())
                    <x-table-container>
                        <table class="w-full text-sm">
                            <thead class="bg-bg border-b border-border">
                                <tr class="text-left text-text-muted text-xs uppercase tracking-wider font-semibold">
                                    <th scope="col" class="pl-4 sm:pl-6 pr-3 py-3">Waktu</th>
                                    <th scope="col" class="px-3 py-3">Metode</th>
                                    <th scope="col" class="px-3 py-3">Kasir</th>
                                    <th scope="col" class="px-3 py-3">Keterangan</th>
                                    <th scope="col" class="pl-3 pr-4 sm:pr-6 py-3 text-right">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @foreach ($tagihan->pembayarans as $by)
                                    <tr>
                                        <td class="pl-4 sm:pl-6 pr-3 py-2.5 font-mono text-xs text-text whitespace-nowrap">
                                            {{ $by->tgl_bayar->format('d M Y H:i') }}
                                        </td>
                                        <td class="px-3 py-2.5 whitespace-nowrap">
                                            <span class="text-xs px-2 py-0.5 rounded bg-bg border border-border font-mono uppercase font-semibold">
                                                {{ strtoupper($by->metode) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2.5 text-text">
                                            {{ $by->kasir->name ?? '-' }}
                                        </td>
                                        <td class="px-3 py-2.5 text-xs text-text-muted">
                                            {{ $by->keterangan ?? '-' }}
                                        </td>
                                        <td class="pl-3 pr-4 sm:pr-6 py-2.5 font-mono font-semibold text-text text-right whitespace-nowrap">
                                            Rp {{ number_format($by->jumlah, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </x-table-container>
                @else
                    <p class="text-sm text-text-muted py-2">Belum ada catatan pembayaran untuk tagihan ini.</p>
                @endif
            </div>

            {{-- Form Terima Pembayaran (Jika belum lunas) --}}
            @if ($tagihan->status !== 'lunas')
                <div class="bg-surface border border-border shadow-sm rounded-lg p-4 sm:p-5">
                    <div class="border-b border-border pb-3 mb-4">
                        <h3 class="text-base font-semibold text-text">Pencatatan Pembayaran</h3>
                        <p class="text-xs text-text-muted mt-0.5">Input nominal dan metode pembayaran yang diterima dari pasien</p>
                    </div>

                    <form method="POST" action="{{ route('tagihan.bayar', $tagihan) }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="metode" value="Metode Pembayaran" :required="true" />
                                <x-select-input id="metode" name="metode" required class="mt-1 block w-full">
                                    @foreach (['tunai' => 'TUNAI', 'transfer' => 'TRANSFER', 'qris' => 'QRIS', 'kartu' => 'KARTU DEBIT/KREDIT', 'penjamin' => 'PENJAMIN / ASURANSI'] as $val => $lbl)
                                        <option value="{{ $val }}" @selected(old('metode') === $val)>{{ $lbl }}</option>
                                    @endforeach
                                </x-select-input>
                                <x-input-error :messages="$errors->get('metode')" />
                            </div>

                            <div class="sm:col-span-2">
                                <x-input-label for="jumlah" value="Jumlah Bayar (Rp)" :required="true" />
                                <x-text-input 
                                    id="jumlah" 
                                    name="jumlah" 
                                    type="number" 
                                    min="0" 
                                    step="0.01" 
                                    required 
                                    :value="old('jumlah', $sisa)" 
                                    class="mt-1 block w-full font-mono font-semibold" 
                                    placeholder="0" 
                                />
                                <x-input-error :messages="$errors->get('jumlah')" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="keterangan" value="Keterangan Transaksi" />
                            <x-text-input 
                                id="keterangan" 
                                name="keterangan" 
                                :value="old('keterangan')" 
                                class="mt-1 block w-full" 
                                placeholder="Nomor referensi transfer, nama bank, atau catatan kasir (opsional)" 
                            />
                            <x-input-error :messages="$errors->get('keterangan')" />
                        </div>

                        <div class="pt-3 border-t border-border flex justify-end">
                            <x-primary-button class="w-full sm:w-auto">
                                Terima Pembayaran
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
