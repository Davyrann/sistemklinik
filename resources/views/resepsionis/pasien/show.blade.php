<x-app-layout>
    <x-slot name="title">{{ $pasien->nama }}</x-slot>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-text leading-tight">{{ $pasien->nama }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('kunjungan.create', ['pasien_id' => $pasien->id]) }}"><x-primary-button>Buat Kunjungan</x-primary-button></a>
                <a href="{{ route('pasien.edit', $pasien) }}"><x-secondary-button>Edit</x-secondary-button></a>
            </div>
        </div>
    </x-slot>

    <x-datatable />

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-primary-50 border border-primary-100 text-primary-900 px-4 py-3 rounded-md text-sm">{{ session('status') }}</div>
            @endif

            <div class="bg-surface border border-border shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-wrap gap-6 text-sm">
                    <div><span class="text-text-muted">No. RM</span><div class="font-mono font-medium text-text">{{ $pasien->no_rm }}</div></div>
                    <div><span class="text-text-muted">NIK</span><div class="font-mono text-text">{{ $pasien->nik ?? '-' }}</div></div>
                    <div><span class="text-text-muted">JK / Tgl Lahir</span><div class="text-text">{{ $pasien->jk }} · {{ $pasien->tgl_lahir->format('d M Y') }}</div></div>
                    <div><span class="text-text-muted">Gol. Darah</span><div class="text-text">{{ $pasien->gol_darah ?? '-' }}</div></div>
                    <div><span class="text-text-muted">Penjamin</span><div class="text-text">{{ strtoupper($pasien->penjamin) }}</div></div>
                    <div><span class="text-text-muted">Telp</span><div class="text-text">{{ $pasien->telp ?? '-' }}</div></div>
                </div>
                @if ($pasien->alergi)
                    <div class="mt-4"><span class="inline-flex text-sm bg-red-50 text-danger border border-red-200 px-3 py-1 rounded-full font-medium">Alergi: {{ $pasien->alergi }}</span></div>
                @endif
                @if ($pasien->alamat)
                    <p class="mt-4 text-sm text-text-muted">{{ $pasien->alamat }}</p>
                @endif
                <div class="mt-6 flex gap-3">
                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'hapus-pasien')" class="inline-flex items-center px-4 py-2 bg-danger border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">Hapus Pasien</button>
                </div>
            </div>

            <div class="bg-surface border border-border shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-border">
                    <h3 class="font-semibold text-text">Riwayat Kunjungan</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="datatable w-full text-sm">
                        <thead class="bg-bg border-b border-border">
                            <tr class="text-left text-text-muted"><th class="px-6 py-3">Tanggal</th><th class="px-6 py-3">Poli / Dokter</th><th class="px-6 py-3">Status</th><th class="px-6 py-3"></th></tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach ($pasien->kunjungans->sortByDesc('tgl_kunjungan') as $k)
                                <tr class="hover:bg-primary-50/30">
                                    <td class="px-6 py-3"><div class="font-mono text-text">{{ $k->no_antrean }}</div><div class="text-xs text-text-muted">{{ $k->tgl_kunjungan->format('d M Y') }}</div></td>
                                    <td class="px-6 py-3"><div class="text-text">{{ $k->poli->nama ?? $k->dokter->poli->nama }}</div><div class="text-xs text-text-muted">{{ $k->dokter->nama ?? '-' }}</div></td>
                                    <td class="px-6 py-3"><span class="text-xs px-2 py-1 rounded-full border bg-bg">{{ ucfirst($k->status) }}</span></td>
                                    <td class="px-6 py-3"><a href="{{ route('kunjungan.show', $k) }}" class="text-primary-700 underline text-sm">Detail</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <x-modal name="hapus-pasien" :show="$errors->isNotEmpty()" focusable>
        <form method="POST" action="{{ route('pasien.destroy', $pasien) }}" class="p-6">
            @csrf @method('DELETE')
            <h2 class="text-lg font-medium text-text">Hapus pasien {{ $pasien->nama }}?</h2>
            <p class="mt-1 text-sm text-text-muted">Semua kunjungan, rekam medis, resep, dan tagihan pasien ini akan ikut terhapus. Aksi tidak dapat dibatalkan.</p>
            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')" type="button">Batal</x-secondary-button>
                <x-danger-button>Hapus Permanen</x-danger-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
