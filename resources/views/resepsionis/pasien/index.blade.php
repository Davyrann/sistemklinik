<x-app-layout>
    <x-slot name="title">Daftar Pasien</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-semibold text-xl text-text leading-tight">Daftar Pasien</h1>
                <p class="text-xs text-text-muted mt-0.5">Kelola data rekam medis demografi dan penjamin pasien</p>
            </div>
            <a href="{{ route('pasien.create') }}">
                <x-primary-button>Pasien Baru</x-primary-button>
            </a>
        </div>
    </x-slot>

    <x-datatable />

    @if (session('status'))
        <div class="bg-primary-50 border border-primary-100 text-primary-900 px-4 py-3 rounded-md text-sm" role="status">{{ session('status') }}</div>
    @endif

    {{-- Search Toolbar --}}
    <form method="GET" action="{{ route('pasien.index') }}" class="bg-surface border border-border shadow-sm rounded-lg p-3 sm:p-4 flex flex-col sm:flex-row gap-3">
        <div class="flex-1">
            <x-text-input name="q" value="{{ $q }}" placeholder="Cari nama / No. RM / NIK" class="w-full" />
        </div>
        <div class="flex gap-2">
            <x-primary-button type="submit" class="w-full sm:w-auto justify-center">Cari</x-primary-button>
            @if ($q !== '')
                <a href="{{ route('pasien.index') }}" class="w-full sm:w-auto">
                    <x-secondary-button type="button" class="w-full sm:w-auto justify-center">Reset</x-secondary-button>
                </a>
            @endif
        </div>
    </form>

    {{-- Table in Table Container (Template A) --}}
    <x-table-container>
        <table class="datatable w-full text-sm">
            <thead class="bg-bg border-b border-border sticky top-0">
                <tr class="text-left text-text-muted text-xs uppercase tracking-wider font-semibold">
                    <th scope="col" class="pl-4 sm:pl-6 pr-3 py-3 font-medium">No. RM</th>
                    <th scope="col" class="px-3 py-3 font-medium">Nama</th>
                    <th scope="col" class="px-3 py-3 font-medium">NIK / JK / Tgl Lahir</th>
                    <th scope="col" class="px-3 py-3 font-medium">Penjamin</th>
                    <th scope="col" class="pl-3 pr-4 sm:pr-6 py-3 font-medium text-right" data-orderable="false">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border-subtle">
                @forelse ($pasiens as $p)
                    <tr class="hover:bg-primary-50/40 transition-colors">
                        <td class="pl-4 sm:pl-6 pr-3 py-3 font-mono font-medium text-text whitespace-nowrap">{{ $p->no_rm }}</td>
                        <td class="px-3 py-3">
                            <div class="font-medium text-text">{{ $p->nama }}</div>
                            @if ($p->alergi)
                                <div class="mt-1">
                                    <x-status-pill status="danger" :label="'Alergi: ' . $p->alergi" />
                                </div>
                            @endif
                        </td>
                        <td class="px-3 py-3">
                            <div class="font-mono text-xs text-text">{{ $p->nik ?? '-' }}</div>
                            <div class="text-xs text-text-muted">{{ $p->jk === 'L' ? 'Laki-laki' : ($p->jk === 'P' ? 'Perempuan' : $p->jk) }} · {{ $p->tgl_lahir ? $p->tgl_lahir->format('d M Y') : '-' }}</div>
                        </td>
                        <td class="px-3 py-3 whitespace-nowrap">
                            <span class="text-xs px-2 py-0.5 rounded bg-bg border border-border font-mono uppercase font-medium">
                                {{ strtoupper($p->penjamin) }}
                            </span>
                        </td>
                        <td class="pl-3 pr-4 sm:pr-6 py-3 whitespace-nowrap text-right">
                            <div class="inline-flex items-center justify-end gap-3">
                                <a href="{{ route('pasien.show', $p) }}" class="text-xs font-medium text-primary-700 hover:text-primary-600 underline">Lihat</a>
                                <a href="{{ route('pasien.edit', $p) }}" class="text-xs font-medium text-text-muted hover:text-text underline">Edit</a>
                                <a href="{{ route('kunjungan.create', ['pasien_id' => $p->id]) }}" class="text-xs font-medium text-success hover:text-success underline">Kunjungan</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 sm:px-6 py-12 text-center text-text-muted text-sm">
                            Tidak ada data pasien yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-container>
</x-app-layout>
