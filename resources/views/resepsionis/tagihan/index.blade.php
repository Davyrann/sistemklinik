<x-app-layout>
    <x-slot name="title">Tagihan</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text leading-tight">Tagihan</h2>
    </x-slot>

    <x-datatable />

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <form method="GET" action="{{ route('tagihan.index') }}" class="bg-surface border border-border shadow-sm rounded-lg p-4 flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-45">
                    <x-input-label value="Cari pasien" />
                    <x-text-input name="q" value="{{ $q }}" placeholder="Nama / No. RM" class="mt-1 w-full" />
                </div>
                <div>
                    <x-input-label value="Status" />
                    <x-select-input name="status" class="mt-1 block w-full">
                        <option value="">Semua</option>
                        <option value="belum" @selected($status === 'belum')>Belum</option>
                        <option value="lunas" @selected($status === 'lunas')>Lunas</option>
                        <option value="batal" @selected($status === 'batal')>Batal</option>
                    </x-select-input>
                </div>
                <x-primary-button type="submit">Filter</x-primary-button>
                <a href="{{ route('tagihan.index') }}"><x-secondary-button type="button">Reset</x-secondary-button></a>
            </form>

            <div class="bg-surface border border-border shadow-sm sm:rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="datatable w-full text-sm" data-default-order="0,desc">
                        <thead class="bg-bg border-b border-border">
                            <tr class="text-left text-text-muted">
                                <th class="px-6 py-3 font-medium">Pasien / Kunjungan</th>
                                <th class="px-6 py-3 font-medium">Total</th>
                                <th class="px-6 py-3 font-medium">Status</th>
                                <th class="px-6 py-3 font-medium" data-orderable="false">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach ($tagihans as $t)
                                <tr class="hover:bg-primary-50/40">
                                    <td class="px-6 py-3" data-order="{{ $t->kunjungan->tgl_kunjungan->format('Y-m-d H:i:s') }}">
                                        <div class="font-medium text-text">{{ $t->kunjungan->pasien->nama }}</div>
                                        <div class="text-xs text-text-muted font-mono">{{ $t->kunjungan->pasien->no_rm }} · {{ $t->kunjungan->no_antrean }} · {{ $t->kunjungan->tgl_kunjungan->format('d M Y') }}</div>
                                    </td>
                                    <td class="px-6 py-3 font-mono text-text">Rp {{ number_format($t->total_bayar, 0, ',', '.') }}</td>
                                    <td class="px-6 py-3">
                                        <span class="text-xs px-2 py-1 rounded-full border font-medium {{ $t->status === 'lunas' ? 'bg-green-50 text-success border-green-200' : 'bg-amber-50 text-warning border-amber-200' }}">{{ ucfirst($t->status) }}</span>
                                    </td>
                                    <td class="px-6 py-3"><a href="{{ route('tagihan.show', $t) }}" class="text-primary-700 underline text-sm">Detail</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


