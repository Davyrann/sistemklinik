<x-app-layout>
    <x-slot name="title">Tagihan — {{ $tagihan->kunjungan->pasien->nama }}</x-slot>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-text leading-tight">Tagihan — {{ $tagihan->kunjungan->pasien->nama }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('tagihan.kuitansi', $tagihan) }}" target="_blank"><x-secondary-button>Kuitansi</x-secondary-button></a>
                <a href="{{ route('tagihan.index') }}"><x-secondary-button>Kembali</x-secondary-button></a>
            </div>
        </div>
    </x-slot>

    <x-datatable />

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-primary-50 border border-primary-100 text-primary-900 px-4 py-3 rounded-md text-sm">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-danger px-4 py-3 rounded-md text-sm">{{ $errors->first() }}</div>
            @endif

            <div class="bg-surface border border-border shadow-sm sm:rounded-lg p-6">
                <div class="text-sm text-text-muted">Kunjungan <span class="font-mono text-text">{{ $tagihan->kunjungan->no_antrean }}</span> · {{ $tagihan->kunjungan->tgl_kunjungan->format('d M Y') }} · {{ $tagihan->kunjungan->poli->nama }} · {{ $tagihan->kunjungan->dokter->nama ?? '-' }}</div>
                <div class="mt-4 overflow-x-auto">
                    <table class="datatable w-full text-sm">
                        <thead class="bg-bg border-y border-border"><tr class="text-left text-text-muted"><th class="px-4 py-2">Deskripsi</th><th class="px-4 py-2">Qty</th><th class="px-4 py-2">Harga</th><th class="px-4 py-2">Subtotal</th></tr></thead>
                        <tbody class="divide-y divide-border">
                            @foreach ($tagihan->details as $d)
                                <tr><td class="px-4 py-2 text-text">{{ $d->deskripsi }} <span class="text-xs text-text-muted">({{ $d->jenis }})</span></td><td class="px-4 py-2 font-mono">{{ $d->qty }}</td><td class="px-4 py-2 font-mono">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td><td class="px-4 py-2 font-mono">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 flex justify-between items-center border-t border-border pt-4">
                    <span class="text-sm">Status: <span class="font-medium {{ $tagihan->status === 'lunas' ? 'text-success' : 'text-warning' }}">{{ ucfirst($tagihan->status) }}</span></span>
                    <span class="font-semibold font-mono text-text">Total Rp {{ number_format($tagihan->total_bayar, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="bg-surface border border-border shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-text">Pembayaran</h3>
                @if ($tagihan->pembayarans->isNotEmpty())
                    <ul class="mt-3 space-y-2 text-sm">
                        @foreach ($tagihan->pembayarans as $by)
                            <li class="flex justify-between border border-border rounded-md px-4 py-2 bg-bg">
                                <span>{{ strtoupper($by->metode) }} · {{ $by->tgl_bayar->format('d M Y H:i') }} · {{ $by->kasir->name ?? '-' }}</span>
                                <span class="font-mono font-medium">Rp {{ number_format($by->jumlah, 0, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="mt-3 text-sm text-text-muted">Belum ada pembayaran.</p>
                @endif

                @if ($tagihan->status !== 'lunas')
                    <form method="POST" action="{{ route('tagihan.bayar', $tagihan) }}" class="mt-4 space-y-3">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <x-input-label value="Metode *" />
                                <x-select-input name="metode" required class="mt-1 block w-full">
                                    @foreach (['tunai','transfer','qris','kartu','penjamin'] as $m)
                                        <option value="{{ $m }}">{{ strtoupper($m) }}</option>
                                    @endforeach
                                </x-select-input>
                            </div>
                            <div class="sm:col-span-2">
                                <x-input-label value="Jumlah *" />
                                <x-text-input name="jumlah" type="number" min="0" step="0.01" required :value="old('jumlah', $tagihan->total_bayar - $tagihan->pembayarans->sum('jumlah'))" class="mt-1 block w-full font-mono" />
                            </div>
                        </div>
                        <div>
                            <x-input-label value="Keterangan" />
                            <x-text-input name="keterangan" :value="old('keterangan')" class="mt-1 block w-full" placeholder="Opsional" />
                        </div>
                        <x-primary-button>Catat Pembayaran</x-primary-button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>


