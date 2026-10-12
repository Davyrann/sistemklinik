<x-app-layout>
    <x-slot name="title">Kunjungan {{ $kunjungan->no_antrean }}</x-slot>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-text leading-tight">Kunjungan {{ $kunjungan->no_antrean }}</h2>
            <a href="{{ route('kunjungan.index') }}"><x-secondary-button>Kembali</x-secondary-button></a>
        </div>
    </x-slot>

    <x-datatable />

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-primary-50 border border-primary-100 text-primary-900 px-4 py-3 rounded-md text-sm">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-danger px-4 py-3 rounded-md text-sm">{{ $errors->first() }}</div>
            @endif

            <div class="bg-surface border border-border shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-wrap gap-4 items-start justify-between">
                    <div>
                        <h3 class="font-semibold text-text">{{ $kunjungan->pasien->nama }}</h3>
                        <p class="text-sm text-text-muted font-mono">{{ $kunjungan->pasien->no_rm }} · {{ $kunjungan->pasien->nik ?? '-' }} · {{ $kunjungan->pasien->jk }} · {{ $kunjungan->pasien->tgl_lahir->format('d M Y') }}</p>
                        @if ($kunjungan->pasien->alergi)
                            <span class="inline-flex mt-2 text-sm bg-red-50 text-danger border border-red-200 px-3 py-1 rounded-full font-medium">Alergi: {{ $kunjungan->pasien->alergi }}</span>
                        @endif
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-text-muted">Gol. Darah {{ $kunjungan->pasien->gol_darah ?? '-' }} · {{ strtoupper($kunjungan->penjamin) }}</div>
                        <div class="mt-1 text-sm text-text">{{ $kunjungan->poli->nama }} · {{ $kunjungan->dokter->nama ?? '-' }}</div>
                        <div class="text-sm text-text-muted">{{ $kunjungan->tgl_kunjungan->format('d M Y') }} · <span class="font-mono">{{ $kunjungan->no_antrean }}</span></div>
                    </div>
                </div>
                @if ($kunjungan->keluhan_utama)
                    <p class="mt-4 text-sm text-text bg-bg border border-border rounded-md p-3">Keluhan: {{ $kunjungan->keluhan_utama }}</p>
                @endif
                <div class="mt-4 flex flex-wrap gap-2 items-center">
                    @php $map = ['menunggu'=>'bg-amber-50 text-warning border-amber-200','dipanggil'=>'bg-blue-50 text-info border-blue-200','diperiksa'=>'bg-primary-50 text-primary-700 border-primary-100','selesai'=>'bg-green-50 text-success border-green-200','batal'=>'bg-gray-100 text-text-muted border-border']; @endphp
                    <span class="inline-flex text-sm px-3 py-1 rounded-full border font-medium {{ $map[$kunjungan->status] ?? '' }}">{{ ucfirst($kunjungan->status) }}</span>
                    <form method="POST" action="{{ route('kunjungan.status', $kunjungan) }}" class="flex gap-2 items-center min-w-fit">
                        @csrf @method('PATCH')
                        <x-select-input name="status" class="block w-full">
                            @foreach (['menunggu','dipanggil','diperiksa','selesai','batal'] as $s)
                                <option value="{{ $s }}" @selected($kunjungan->status === $s)>{{ ucfirst($s) }}</option>
                            @endforeach
                        </x-select-input>
                        <x-primary-button class="whitespace-nowrap">Ubah Status</x-primary-button>
                    </form>
                    <form method="POST" action="{{ route('kunjungan.destroy', $kunjungan) }}" onsubmit="return confirm('Hapus kunjungan ini? Tagihan ikut terhapus.')">
                        @csrf @method('DELETE')
                        <x-danger-button>Hapus</x-danger-button>
                    </form>
                </div>
            </div>

            @if ($kunjungan->rekamMedis)
                <div class="bg-surface border border-border shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-text">Rekam Medis (oleh dokter)</h3>
                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div><span class="text-text-muted">Subjective</span><p class="text-text mt-1">{{ $kunjungan->rekamMedis->subjective ?? '-' }}</p></div>
                        <div><span class="text-text-muted">Objective</span><p class="text-text mt-1">{{ $kunjungan->rekamMedis->objective ?? '-' }}</p></div>
                        <div><span class="text-text-muted">Assessment</span><p class="text-text mt-1">{{ $kunjungan->rekamMedis->assessment ?? '-' }}</p></div>
                        <div><span class="text-text-muted">Plan</span><p class="text-text mt-1">{{ $kunjungan->rekamMedis->plan ?? '-' }}</p></div>
                    </div>
                </div>
            @endif

            <div class="bg-surface border border-border shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center">
                    <h3 class="font-semibold text-text">Tagihan</h3>
                    @if ($kunjungan->tagihan)
                        <a href="{{ route('tagihan.show', $kunjungan->tagihan) }}" class="text-sm text-primary-700 underline">Lihat tagihan</a>
                    @endif
                </div>
                @if ($kunjungan->tagihan)
                    <div class="mt-3 overflow-x-auto">
                        <table class="datatable w-full text-sm">
                            <thead class="bg-bg border-y border-border"><tr class="text-left text-text-muted"><th class="px-4 py-2">Deskripsi</th><th class="px-4 py-2">Qty</th><th class="px-4 py-2">Harga</th><th class="px-4 py-2">Subtotal</th></tr></thead>
                            <tbody class="divide-y divide-border">
                                @foreach ($kunjungan->tagihan->details as $d)
                                    <tr><td class="px-4 py-2 text-text">{{ $d->deskripsi }}</td><td class="px-4 py-2 font-mono text-text">{{ $d->qty }}</td><td class="px-4 py-2 font-mono text-text">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td><td class="px-4 py-2 font-mono text-text">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex justify-between items-center">
                        <span class="text-sm text-text-muted">Status: <span class="font-medium {{ $kunjungan->tagihan->status === 'lunas' ? 'text-success' : 'text-warning' }}">{{ ucfirst($kunjungan->tagihan->status) }}</span></span>
                        <span class="font-semibold font-mono text-text">Total Rp {{ number_format($kunjungan->tagihan->total_bayar, 0, ',', '.') }}</span>
                    </div>
                @else
                    <p class="mt-3 text-sm text-text-muted">Belum ada tagihan.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>


