<x-app-layout>
    <x-slot name="title">Kunjungan {{ $kunjungan->no_antrean }}</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="font-semibold text-xl text-text leading-tight">Kunjungan {{ $kunjungan->no_antrean }}</h1>
                    @php
                        $statusMap = [
                            'menunggu' => 'warning',
                            'dipanggil' => 'info',
                            'diperiksa' => 'info',
                            'selesai' => 'success',
                            'batal' => 'neutral',
                        ];
                        $pillStatus = $statusMap[$kunjungan->status] ?? 'neutral';
                    @endphp
                    <x-status-pill :status="$pillStatus" :label="ucfirst($kunjungan->status)" />
                </div>
                <p class="text-xs text-text-muted mt-0.5">Detail kunjungan, status alur layanan, rekam medis, dan rincian tagihan</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('kunjungan.index') }}">
                    <x-secondary-button>Kembali</x-secondary-button>
                </a>
            </div>
        </div>
    </x-slot>

    <x-datatable />

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-primary-50 border border-primary-100 text-primary-900 px-4 py-3 rounded-md text-sm" role="status">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-danger px-4 py-3 rounded-md text-sm" role="alert">{{ $errors->first() }}</div>
            @endif

            {{-- Patient Header Component --}}
            <x-patient-header :kunjungan="$kunjungan" />

            {{-- Visit Overview & Status Management Card --}}
            <div class="bg-surface border border-border shadow-sm rounded-lg p-4 md:p-6 space-y-6">
                <div>
                    <h2 class="text-base font-semibold text-text">Informasi Kunjungan & Status</h2>
                    <p class="text-xs text-text-muted">Poli dan dokter pemeriksa, tanggal kunjungan, serta pembaruan alur status</p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                    <div class="space-y-1">
                        <span class="text-xs font-medium text-text-muted">Poli Tujuan</span>
                        <div class="font-medium text-text">{{ $kunjungan->poli->nama }}</div>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-medium text-text-muted">Dokter Pemeriksa</span>
                        <div class="text-text">{{ $kunjungan->dokter->nama ?? '-' }}</div>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-medium text-text-muted">Tanggal Kunjungan</span>
                        <div class="font-mono text-text">{{ $kunjungan->tgl_kunjungan ? $kunjungan->tgl_kunjungan->format('d M Y') : '-' }}</div>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-medium text-text-muted">No. Antrean</span>
                        <div class="font-mono font-semibold text-text">{{ $kunjungan->no_antrean }}</div>
                    </div>
                </div>

                <div class="pt-4 border-t border-border flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <form method="POST" action="{{ route('kunjungan.status', $kunjungan) }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                        @csrf
                        @method('PATCH')
                        <div class="w-full sm:w-48">
                            <x-select-input name="status" class="block w-full">
                                @foreach (['menunggu','dipanggil','diperiksa','selesai','batal'] as $s)
                                    <option value="{{ $s }}" @selected($kunjungan->status === $s)>{{ ucfirst($s) }}</option>
                                @endforeach
                            </x-select-input>
                        </div>
                        <x-primary-button class="justify-center whitespace-nowrap">Ubah Status</x-primary-button>
                    </form>

                    <div>
                        <x-danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'hapus-kunjungan')" class="w-full sm:w-auto justify-center">
                            Hapus Kunjungan
                        </x-danger-button>
                    </div>
                </div>
            </div>

            {{-- Rekam Medis (jika ada) --}}
            @if ($kunjungan->rekamMedis)
                <div class="bg-surface border border-border shadow-sm rounded-lg p-4 md:p-6 space-y-4">
                    <div class="border-b border-border-subtle pb-2">
                        <h2 class="text-base font-semibold text-text">Rekam Medis (SOAP)</h2>
                        <p class="text-xs text-text-muted">Catatan pemeriksaan oleh dokter pemeriksa</p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="p-3 bg-bg rounded-md border border-border-subtle">
                            <span class="text-xs font-semibold text-text-muted uppercase tracking-wider block">Subjective</span>
                            <p class="text-text mt-1 whitespace-pre-line">{{ $kunjungan->rekamMedis->subjective ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-bg rounded-md border border-border-subtle">
                            <span class="text-xs font-semibold text-text-muted uppercase tracking-wider block">Objective</span>
                            <p class="text-text mt-1 whitespace-pre-line">{{ $kunjungan->rekamMedis->objective ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-bg rounded-md border border-border-subtle">
                            <span class="text-xs font-semibold text-text-muted uppercase tracking-wider block">Assessment</span>
                            <p class="text-text mt-1 whitespace-pre-line">{{ $kunjungan->rekamMedis->assessment ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-bg rounded-md border border-border-subtle">
                            <span class="text-xs font-semibold text-text-muted uppercase tracking-wider block">Plan</span>
                            <p class="text-text mt-1 whitespace-pre-line">{{ $kunjungan->rekamMedis->plan ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Tagihan Card --}}
            <div class="bg-surface border border-border shadow-sm rounded-lg p-4 md:p-6 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-border-subtle pb-2">
                    <div>
                        <h2 class="text-base font-semibold text-text">Rincian Tagihan</h2>
                        <p class="text-xs text-text-muted">Biaya pelayanan, tindakan, dan obat</p>
                    </div>
                    @if ($kunjungan->tagihan)
                        <div>
                            <a href="{{ route('tagihan.show', $kunjungan->tagihan) }}" class="text-xs font-medium text-primary-700 hover:text-primary-600 underline">
                                Lihat Tagihan di Kasir
                            </a>
                        </div>
                    @endif
                </div>

                @if ($kunjungan->tagihan)
                    <x-table-container>
                        <table class="datatable w-full text-sm">
                            <thead class="bg-bg border-b border-border">
                                <tr class="text-left text-text-muted text-xs uppercase tracking-wider font-semibold">
                                    <th scope="col" class="pl-4 sm:pl-6 pr-3 py-3 font-medium">Deskripsi Layanan</th>
                                    <th scope="col" class="px-3 py-3 text-right font-medium">Qty</th>
                                    <th scope="col" class="px-3 py-3 text-right font-medium">Harga Satuan</th>
                                    <th scope="col" class="pl-3 pr-4 sm:pr-6 py-3 text-right font-medium">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @forelse ($kunjungan->tagihan->details as $d)
                                    <tr class="hover:bg-primary-50/20">
                                        <td class="pl-4 sm:pl-6 pr-3 py-3 text-text font-medium">{{ $d->deskripsi }}</td>
                                        <td class="px-3 py-3 font-mono text-text text-right">{{ $d->qty }}</td>
                                        <td class="px-3 py-3 font-mono text-text text-right">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                                        <td class="pl-3 pr-4 sm:pr-6 py-3 font-mono text-text font-medium text-right">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 sm:px-6 py-8 text-center text-text-muted text-sm">
                                            Belum ada item tagihan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </x-table-container>

                    <div class="pt-3 border-t border-border flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-medium text-text-muted">Status Pembayaran:</span>
                            <x-status-pill :status="$kunjungan->tagihan->status === 'lunas' ? 'success' : 'warning'" :label="ucfirst($kunjungan->tagihan->status)" />
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-text-muted mr-2">Total Biaya:</span>
                            <span class="text-base font-semibold font-mono text-text">Rp {{ number_format($kunjungan->tagihan->total_bayar, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-text-muted py-2">Belum ada tagihan dibuat untuk kunjungan ini.</p>
                @endif
            </div>
        </div>
    </div>

    <x-modal name="hapus-kunjungan" focusable>
        <form method="POST" action="{{ route('kunjungan.destroy', $kunjungan) }}" class="p-6">
            @csrf
            @method('DELETE')
            <h2 class="text-lg font-semibold text-text">Hapus data kunjungan {{ $kunjungan->no_antrean }}?</h2>
            <p class="mt-2 text-sm text-text-muted leading-relaxed">
                Data antrean dan tagihan yang terhubung dengan kunjungan ini akan ikut dihapus. Pastikan tindakan ini telah dikonfirmasi.
            </p>
            <div class="mt-6 flex flex-col-reverse sm:flex-row justify-end items-stretch sm:items-center gap-3">
                <x-secondary-button x-on:click="$dispatch('close')" type="button" class="justify-center">
                    Batal
                </x-secondary-button>
                <x-danger-button class="justify-center">
                    Hapus Kunjungan
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
