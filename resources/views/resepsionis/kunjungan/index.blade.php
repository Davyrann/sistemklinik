<x-app-layout>
    <x-slot name="title">Kunjungan</x-slot>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-text leading-tight">Kunjungan</h2>
            <a href="{{ route('kunjungan.create') }}"><x-primary-button>Buat Kunjungan</x-primary-button></a>
        </div>
    </x-slot>

    <x-datatable />

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-primary-50 border border-primary-100 text-primary-900 px-4 py-3 rounded-md text-sm">{{ session('status') }}</div>
            @endif

            <form method="GET" action="{{ route('kunjungan.index') }}" class="bg-surface border border-border shadow-sm rounded-lg p-4 flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[180px]">
                    <x-input-label value="Cari" />
                    <x-text-input name="q" value="{{ $q }}" placeholder="Nama / RM / no. antrean" class="mt-1 w-full" />
                </div>
                <div>
                    <x-input-label value="Status" />
                    <x-select-input name="status" class="mt-1 block w-full">
                        <option value="">Semua</option>
                        @foreach (['menunggu','dipanggil','diperiksa','selesai','batal'] as $s)
                            <option value="{{ $s }}" @selected($status === $s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </x-select-input>
                </div>
                <div>
                    <x-input-label value="Tanggal" />
                    <x-text-input type="text" name="tgl" value="{{ $tgl }}" data-datepicker class="mt-1" placeholder="YYYY-MM-DD" />
                </div>
                <x-primary-button type="submit">Filter</x-primary-button>
                <a href="{{ route('kunjungan.index') }}"><x-secondary-button type="button">Reset</x-secondary-button></a>
            </form>

            <div class="bg-surface border border-border shadow-sm sm:rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="datatable w-full text-sm">
                        <thead class="bg-bg border-b border-border">
                            <tr class="text-left text-text-muted">
                                <th class="px-6 py-3 font-medium">No</th>
                                <th class="px-6 py-3 font-medium">Pasien</th>
                                <th class="px-6 py-3 font-medium">Poli / Dokter</th>
                                <th class="px-6 py-3 font-medium">Tgl</th>
                                <th class="px-6 py-3 font-medium">Status</th>
                                <th class="px-6 py-3 font-medium" data-orderable="false">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach ($kunjungans as $k)
                                <tr class="hover:bg-primary-50/40">
                                    <td class="px-6 py-3 font-mono text-text">{{ $k->no_antrean }}</td>
                                    <td class="px-6 py-3">
                                        <div class="font-medium text-text">{{ $k->pasien->nama }}</div>
                                        <div class="text-xs text-text-muted font-mono">{{ $k->pasien->no_rm }}</div>
                                        @if ($k->pasien->alergi)
                                            <span class="inline-flex mt-1 text-xs bg-red-50 text-danger border border-red-200 px-2 py-0.5 rounded-full">Alergi: {{ $k->pasien->alergi }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3">
                                        <div class="text-text">{{ $k->poli->nama }}</div>
                                        <div class="text-xs text-text-muted">{{ $k->dokter->nama ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-3 text-text-muted">{{ $k->tgl_kunjungan->format('d M Y') }}</td>
                                    <td class="px-6 py-3">
                                        @php $map = ['menunggu'=>'bg-amber-50 text-warning border-amber-200','dipanggil'=>'bg-blue-50 text-info border-blue-200','diperiksa'=>'bg-primary-50 text-primary-700 border-primary-100','selesai'=>'bg-green-50 text-success border-green-200','batal'=>'bg-gray-100 text-text-muted border-border']; @endphp
                                        <span class="inline-flex text-xs px-2 py-1 rounded-full border font-medium {{ $map[$k->status] ?? '' }}">{{ ucfirst($k->status) }}</span>
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


