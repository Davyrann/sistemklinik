<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text leading-tight">Dashboard Resepsionis</h2>
    </x-slot>

    <x-datatable />

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-primary-50 border border-primary-100 text-primary-900 px-4 py-3 rounded-md text-sm">{{ session('status') }}</div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-surface border border-border shadow-sm rounded-lg p-6">
                    <p class="text-sm text-text-muted">Kunjungan Hari Ini</p>
                    <p class="mt-2 text-2xl font-semibold font-mono text-text">{{ $totalHariIni }}</p>
                </div>
                <div class="bg-surface border border-border shadow-sm rounded-lg p-6">
                    <p class="text-sm text-text-muted">Menunggu / Dipanggil</p>
                    <p class="mt-2 text-2xl font-semibold font-mono text-warning">{{ $menunggu }}</p>
                </div>
                <div class="bg-surface border border-border shadow-sm rounded-lg p-6">
                    <p class="text-sm text-text-muted">Selesai Hari Ini</p>
                    <p class="mt-2 text-2xl font-semibold font-mono text-success">{{ $selesai }}</p>
                </div>
                <div class="bg-surface border border-border shadow-sm rounded-lg p-6">
                    <p class="text-sm text-text-muted">Pendapatan Hari Ini</p>
                    <p class="mt-2 text-lg font-semibold font-mono text-text">Rp {{ number_format($pendapatan, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('pasien.create') }}"><x-primary-button>Pasien Baru</x-primary-button></a>
                <a href="{{ route('kunjungan.create') }}"><x-secondary-button>Buat Kunjungan</x-secondary-button></a>
            </div>

            <div class="bg-surface border border-border shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-border flex justify-between items-center">
                    <h3 class="font-semibold text-text">Kunjungan Hari Ini</h3>
                    <a href="{{ route('kunjungan.index', ['tgl' => now()->toDateString()]) }}" class="text-sm text-primary-700 hover:text-primary-600 underline">Lihat semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="datatable w-full text-sm">
                        <thead class="bg-bg border-b border-border">
                            <tr class="text-left text-text-muted">
                                <th class="px-6 py-3 font-medium">No</th>
                                <th class="px-6 py-3 font-medium">Pasien</th>
                                <th class="px-6 py-3 font-medium">Poli / Dokter</th>
                                <th class="px-6 py-3 font-medium">Status</th>
                                <th class="px-6 py-3 font-medium" data-orderable="false">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach ($kunjunganHariIni as $k)
                                <tr class="hover:bg-primary-50/50">
                                    <td class="px-6 py-3 font-mono text-text">{{ $k->no_antrean }}</td>
                                    <td class="px-6 py-3">
                                        <div class="font-medium text-text">{{ $k->pasien->nama }}</div>
                                        <div class="text-xs text-text-muted font-mono">{{ $k->pasien->no_rm }}</div>
                                        @if ($k->pasien->alergi)
                                            <span class="inline-flex mt-1 text-xs bg-red-50 text-danger border border-red-200 px-2 py-0.5 rounded-full">Alergi: {{ $k->pasien->alergi }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3">
                                        <div class="text-text">{{ $k->poli->nama ?? $k->dokter->poli->nama }}</div>
                                        <div class="text-xs text-text-muted">{{ $k->dokter->nama ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-3">
                                        @php $map = ['menunggu'=>'bg-amber-50 text-warning border-amber-200','dipanggil'=>'bg-blue-50 text-info border-blue-200','diperiksa'=>'bg-primary-50 text-primary-700 border-primary-100','selesai'=>'bg-green-50 text-success border-green-200','batal'=>'bg-gray-100 text-text-muted border-border']; @endphp
                                        <span class="inline-flex text-xs px-2 py-1 rounded-full border font-medium {{ $map[$k->status] ?? 'bg-bg' }}">{{ ucfirst($k->status) }}</span>
                                    </td>
                                    <td class="px-6 py-3"><a href="{{ route('kunjungan.show', $k) }}" class="text-primary-700 hover:text-primary-600 text-sm underline">Detail</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
