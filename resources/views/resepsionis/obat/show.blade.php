<x-app-layout>
    <x-slot name="title">{{ $obat->nama_generik }}</x-slot>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-text leading-tight">{{ $obat->nama_generik }}</h2>
            <a href="{{ route('obat.index') }}"><x-secondary-button>Kembali</x-secondary-button></a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-surface border border-border shadow-sm sm:rounded-lg p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="text-text-muted">Kode</span><div class="font-mono font-medium text-text">{{ $obat->kode }}</div></div>
                    <div><span class="text-text-muted">Satuan</span><div class="text-text">{{ $obat->satuan }}</div></div>
                    <div><span class="text-text-muted">Nama Generik</span><div class="font-medium text-text">{{ $obat->nama_generik }}</div></div>
                    <div><span class="text-text-muted">Nama Dagang</span><div class="text-text">{{ $obat->nama_dagang ?? '-' }}</div></div>
                    <div><span class="text-text-muted">Stok</span><div class="font-mono text-text">{{ $obat->stok }} <span class="text-xs text-text-muted">(min {{ $obat->stok_minimum }})</span></div></div>
                    <div><span class="text-text-muted">Harga</span><div class="font-mono font-medium text-text">Rp {{ number_format($obat->harga, 0, ',', '.') }}</div></div>
                </div>
                <p class="text-xs text-text-muted">Obat dikelola oleh Dokter. Resepsionis hanya melihat.</p>
            </div>
        </div>
    </div>
</x-app-layout>
