<x-app-layout>
    <x-slot name="title">Pasien</x-slot>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-text leading-tight">Pasien</h2>
            <a href="{{ route('pasien.create') }}"><x-primary-button>Pasien Baru</x-primary-button></a>
        </div>
    </x-slot>

    <x-datatable />

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-primary-50 border border-primary-100 text-primary-900 px-4 py-3 rounded-md text-sm">{{ session('status') }}</div>
            @endif

            <form method="GET" action="{{ route('pasien.index') }}" class="bg-surface border border-border shadow-sm rounded-lg p-4 flex gap-3">
                <x-text-input name="q" value="{{ $q }}" placeholder="Cari nama / No. RM / NIK" class="flex-1" />
                <x-primary-button type="submit">Cari</x-primary-button>
                @if ($q !== '')
                    <a href="{{ route('pasien.index') }}"><x-secondary-button type="button">Reset</x-secondary-button></a>
                @endif
            </form>

            <div class="bg-surface border border-border shadow-sm sm:rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="datatable w-full text-sm">
                        <thead class="bg-bg border-b border-border">
                            <tr class="text-left text-text-muted">
                                <th class="px-6 py-3 font-medium">No. RM</th>
                                <th class="px-6 py-3 font-medium">Nama</th>
                                <th class="px-6 py-3 font-medium">NIK / JK / Tgl Lahir</th>
                                <th class="px-6 py-3 font-medium">Penjamin</th>
                                <th class="px-6 py-3 font-medium" data-orderable="false">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach ($pasiens as $p)
                                <tr class="hover:bg-primary-50/40">
                                    <td class="px-6 py-3 font-mono text-text">{{ $p->no_rm }}</td>
                                    <td class="px-6 py-3">
                                        <div class="font-medium text-text">{{ $p->nama }}</div>
                                        @if ($p->alergi)
                                            <span class="inline-flex mt-1 text-xs bg-red-50 text-danger border border-red-200 px-2 py-0.5 rounded-full">Alergi: {{ $p->alergi }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3">
                                        <div class="font-mono text-xs text-text">{{ $p->nik ?? '-' }}</div>
                                        <div class="text-xs text-text-muted">{{ $p->jk }} · {{ $p->tgl_lahir->format('d M Y') }}</div>
                                    </td>
                                    <td class="px-6 py-3"><span class="text-xs px-2 py-1 rounded-full bg-bg border border-border">{{ strtoupper($p->penjamin) }}</span></td>
                                    <td class="px-6 py-3">
                                        <div class="flex gap-2">
                                            <a href="{{ route('pasien.show', $p) }}" class="text-sm text-primary-700 hover:text-primary-600 underline">Lihat</a>
                                            <a href="{{ route('pasien.edit', $p) }}" class="text-sm text-text-muted hover:text-text underline">Edit</a>
                                            <a href="{{ route('kunjungan.create', ['pasien_id' => $p->id]) }}" class="text-sm text-success hover:text-success underline">Kunjungan</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
