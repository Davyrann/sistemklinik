<x-app-layout>
    <x-slot name="title">Obat</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text leading-tight">Obat (Lihat — kelola oleh Dokter)</h2>
    </x-slot>

    <x-datatable />

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <form method="GET" action="{{ route('obat.index') }}" class="bg-surface border border-border shadow-sm rounded-lg p-4 flex gap-3">
                <x-text-input name="q" value="{{ $q }}" placeholder="Cari kode / nama generik / dagang" class="flex-1" />
                <x-primary-button type="submit">Cari</x-primary-button>
                @if ($q !== '')
                    <a href="{{ route('obat.index') }}"><x-secondary-button type="button">Reset</x-secondary-button></a>
                @endif
            </form>

            <div class="bg-surface border border-border shadow-sm sm:rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="datatable w-full text-sm">
                        <thead class="bg-bg border-b border-border">
                            <tr class="text-left text-text-muted">
                                <th class="px-6 py-3 font-medium">Kode</th>
                                <th class="px-6 py-3 font-medium">Nama</th>
                                <th class="px-6 py-3 font-medium">Satuan</th>
                                <th class="px-6 py-3 font-medium">Stok</th>
                                <th class="px-6 py-3 font-medium">Harga</th>
                                <th class="px-6 py-3 font-medium" data-orderable="false">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach ($obats as $o)
                                <tr class="hover:bg-primary-50/40">
                                    <td class="px-6 py-3 font-mono text-text">{{ $o->kode }}</td>
                                    <td class="px-6 py-3">
                                        <div class="font-medium text-text">{{ $o->nama_generik }}</div>
                                        @if ($o->nama_dagang)<div class="text-xs text-text-muted">{{ $o->nama_dagang }}</div>@endif
                                    </td>
                                    <td class="px-6 py-3 text-text-muted">{{ $o->satuan }}</td>
                                    <td class="px-6 py-3">
                                        <span class="font-mono {{ $o->stok <= $o->stok_minimum ? 'text-danger font-semibold' : 'text-text' }}">{{ $o->stok }}</span>
                                        @if ($o->stok <= $o->stok_minimum)<span class="ml-2 text-xs bg-red-50 text-danger border border-red-200 px-2 py-0.5 rounded-full">Menipis</span>@endif
                                    </td>
                                    <td class="px-6 py-3 font-mono text-text">Rp {{ number_format($o->harga, 0, ',', '.') }}</td>
                                    <td class="px-6 py-3"><a href="{{ route('obat.show', $o) }}" class="text-primary-700 underline text-sm">Detail</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
