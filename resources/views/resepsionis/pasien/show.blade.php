<x-app-layout>
    <x-slot name="title">{{ $pasien->nama }}</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="font-semibold text-xl text-text leading-tight">{{ $pasien->nama }}</h1>
                    <span class="text-xs px-2 py-0.5 rounded bg-bg border border-border font-mono uppercase font-medium">
                        {{ strtoupper($pasien->penjamin) }}
                    </span>
                </div>
                <p class="text-xs text-text-muted mt-0.5 font-mono">No. RM: <span class="font-semibold text-text">{{ $pasien->no_rm }}</span></p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('kunjungan.create', ['pasien_id' => $pasien->id]) }}">
                    <x-primary-button>Buat Kunjungan</x-primary-button>
                </a>
                <a href="{{ route('pasien.edit', $pasien) }}">
                    <x-secondary-button>Edit Pasien</x-secondary-button>
                </a>
                <a href="{{ route('pasien.index') }}">
                    <x-secondary-button>Kembali</x-secondary-button>
                </a>
            </div>
        </div>
    </x-slot>

    <x-datatable />

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-primary-50 border border-primary-100 text-primary-900 px-4 py-3 rounded-md text-sm" role="status">{{ session('status') }}</div>
            @endif

            {{-- Structured Patient Detail Card --}}
            <div class="bg-surface border border-border shadow-sm rounded-lg p-4 md:p-6 space-y-6">
                {{-- Allergy Banner --}}
                <x-allergy-banner :alergi="$pasien->alergi" />

                {{-- Detail Grid --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 text-sm">
                    <div class="space-y-1">
                        <span class="text-xs font-medium text-text-muted">No. Rekam Medis</span>
                        <div class="font-mono font-semibold text-text text-base">{{ $pasien->no_rm }}</div>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-medium text-text-muted">NIK</span>
                        <div class="font-mono text-text">{{ $pasien->nik ?? '-' }}</div>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-medium text-text-muted">JK / Tgl Lahir</span>
                        <div class="text-text">{{ $pasien->jk === 'L' ? 'Laki-laki' : ($pasien->jk === 'P' ? 'Perempuan' : $pasien->jk) }} · {{ $pasien->tgl_lahir ? $pasien->tgl_lahir->format('d M Y') : '-' }}</div>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-medium text-text-muted">Golongan Darah</span>
                        <div class="text-text font-medium">{{ $pasien->gol_darah ? 'Golongan ' . $pasien->gol_darah : '-' }}</div>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-medium text-text-muted">Penjamin Biaya</span>
                        <div class="text-text font-mono font-medium uppercase">{{ strtoupper($pasien->penjamin) }}</div>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-medium text-text-muted">No. HP / Kontak</span>
                        <div class="font-mono text-text">{{ $pasien->telp ?? '-' }}</div>
                    </div>
                </div>

                @if ($pasien->alamat)
                    <div class="pt-4 border-t border-border-subtle text-sm">
                        <span class="text-xs font-medium text-text-muted block">Alamat Domisili</span>
                        <p class="mt-1 text-text">{{ $pasien->alamat }}</p>
                    </div>
                @endif

                {{-- Action row for delete --}}
                <div class="pt-4 border-t border-border flex justify-end">
                    <x-danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'hapus-pasien')">
                        Hapus Pasien
                    </x-danger-button>
                </div>
            </div>

            {{-- Visit History Section --}}
            <x-table-container :scrollable="false">
                <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-border flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-text">Riwayat Kunjungan</h2>
                        <p class="text-xs text-text-muted">Semua kunjungan dan pemeriksaan yang pernah dilakukan pasien ini</p>
                    </div>
                </div>

                <div class="overflow-x-auto w-full" style="-webkit-overflow-scrolling: touch;">
                    <table class="datatable w-full text-sm">
                        <thead class="bg-bg border-b border-border sticky top-0">
                            <tr class="text-left text-text-muted text-xs uppercase tracking-wider font-semibold">
                                <th scope="col" class="pl-4 sm:pl-6 pr-3 py-3 font-medium">No. Antrean / Tgl</th>
                                <th scope="col" class="px-3 py-3 font-medium">Poli / Dokter</th>
                                <th scope="col" class="px-3 py-3 font-medium">Status</th>
                                <th scope="col" class="pl-3 pr-4 sm:pr-6 py-3 font-medium text-right" data-orderable="false">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse ($pasien->kunjungans->sortByDesc('tgl_kunjungan') as $k)
                                <tr class="hover:bg-primary-50/30 transition-colors">
                                    <td class="pl-4 sm:pl-6 pr-3 py-3 whitespace-nowrap">
                                        <div class="font-mono font-medium text-text">{{ $k->no_antrean }}</div>
                                        <div class="text-xs text-text-muted">{{ $k->tgl_kunjungan ? $k->tgl_kunjungan->format('d M Y') : '-' }}</div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="text-text font-medium">{{ $k->poli->nama ?? $k->dokter->poli->nama }}</div>
                                        <div class="text-xs text-text-muted">{{ $k->dokter->nama ?? '-' }}</div>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        @php
                                            $statusMap = [
                                                'menunggu' => 'warning',
                                                'dipanggil' => 'info',
                                                'diperiksa' => 'info',
                                                'selesai' => 'success',
                                                'batal' => 'neutral',
                                            ];
                                            $pillStatus = $statusMap[$k->status] ?? 'neutral';
                                        @endphp
                                        <x-status-pill :status="$pillStatus" :label="ucfirst($k->status)" />
                                    </td>
                                    <td class="pl-3 pr-4 sm:pr-6 py-3 whitespace-nowrap text-right">
                                        <a href="{{ route('kunjungan.show', $k) }}" class="text-xs font-medium text-primary-700 hover:text-primary-600 underline">Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 sm:px-6 py-12 text-center text-text-muted text-sm">
                                        Belum ada riwayat kunjungan untuk pasien ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-table-container>
        </div>
    </div>

    <x-modal name="hapus-pasien" :show="$errors->isNotEmpty()" focusable>
        <form method="POST" action="{{ route('pasien.destroy', $pasien) }}" class="p-6">
            @csrf
            @method('DELETE')
            <h2 class="text-lg font-semibold text-text">Hapus data pasien {{ $pasien->nama }}?</h2>
            <p class="mt-2 text-sm text-text-muted leading-relaxed">
                Semua data kunjungan, rekam medis klinis, resep, dan tagihan pasien ini akan ikut terhapus dari sistem. Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="mt-6 flex flex-col-reverse sm:flex-row justify-end items-stretch sm:items-center gap-3">
                <x-secondary-button x-on:click="$dispatch('close')" type="button" class="justify-center">
                    Batal
                </x-secondary-button>
                <x-danger-button class="justify-center">
                    Hapus Permanen
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
