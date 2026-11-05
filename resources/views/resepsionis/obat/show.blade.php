<x-app-layout>
    <x-slot name="title">Detail Obat — {{ $obat->nama_generik }}</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-semibold text-xl text-text leading-tight">
                    {{ $obat->nama_generik }}
                </h1>
                <p class="text-xs text-text-muted mt-0.5">
                    Informasi detail obat, dosis, dan monitoring ketersediaan stok
                </p>
            </div>
            <a href="{{ route('obat.index') }}">
                <x-secondary-button>
                    Kembali ke Daftar Obat
                </x-secondary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            {{-- Status & Summary Card --}}
            <div class="bg-surface border border-border shadow-sm rounded-lg p-4 sm:p-6 space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-border">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs px-2 py-0.5 rounded bg-bg border border-border font-semibold text-text">
                                {{ $obat->kode }}
                            </span>
                            @if ($obat->stok <= 0)
                                <x-status-pill status="danger" label="Stok Habis" />
                            @elseif ($obat->stok <= $obat->stok_minimum)
                                <x-status-pill status="warning" label="Stok Menipis" />
                            @else
                                <x-status-pill status="success" label="Stok Tersedia" />
                            @endif
                        </div>
                        <h2 class="text-lg font-bold text-text">{{ $obat->nama_generik }}</h2>
                        @if ($obat->nama_dagang)
                            <p class="text-xs text-text-muted">Nama Dagang: <span class="font-medium text-text">{{ $obat->nama_dagang }}</span></p>
                        @endif
                    </div>

                    <div class="bg-bg border border-border rounded-lg p-3 text-right shrink-0">
                        <div class="text-xs text-text-muted">Harga Satuan</div>
                        <div class="font-mono text-lg font-bold text-primary-700">
                            Rp {{ number_format($obat->harga, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                {{-- Detail Specifications Grid --}}
                <div>
                    <h3 class="text-xs uppercase tracking-wider font-semibold text-text-muted mb-3">Spesifikasi Obat</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-bg/50 border border-border/80 rounded-lg p-3 space-y-1">
                            <span class="text-xs text-text-muted">Bentuk / Satuan</span>
                            <div class="font-medium text-text">{{ $obat->satuan }}</div>
                        </div>

                        <div class="bg-bg/50 border border-border/80 rounded-lg p-3 space-y-1">
                            <span class="text-xs text-text-muted">Status Katalog</span>
                            <div>
                                @if ($obat->is_aktif)
                                    <x-status-pill status="success" label="Aktif Diresepkan" />
                                @else
                                    <x-status-pill status="neutral" label="Nonaktif" />
                                @endif
                            </div>
                        </div>

                        <div class="bg-bg/50 border border-border/80 rounded-lg p-3 space-y-1">
                            <span class="text-xs text-text-muted">Sisa Stok Fisik</span>
                            <div class="font-mono text-base font-semibold {{ $obat->stok <= 0 ? 'text-danger' : ($obat->stok <= $obat->stok_minimum ? 'text-warning' : 'text-text') }}">
                                {{ $obat->stok }} <span class="text-xs font-sans text-text-muted font-normal">{{ $obat->satuan }}</span>
                            </div>
                        </div>

                        <div class="bg-bg/50 border border-border/80 rounded-lg p-3 space-y-1">
                            <span class="text-xs text-text-muted">Ambang Batas Minimum</span>
                            <div class="font-mono text-base font-semibold text-text">
                                {{ $obat->stok_minimum }} <span class="text-xs font-sans text-text-muted font-normal">{{ $obat->satuan }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Batch / Batch History & Audit Note --}}
                <div class="border-t border-border pt-4 space-y-2">
                    <h3 class="text-xs uppercase tracking-wider font-semibold text-text-muted">Pengawasan Batch & Inventaris</h3>
                    <div class="bg-bg border border-border rounded-lg p-3 text-xs text-text space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-text-muted">Terakhir diperbarui:</span>
                            <span class="font-mono text-text">{{ $obat->updated_at ? $obat->updated_at->format('d M Y H:i') : '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-text-muted">Status Pengadaan Batch:</span>
                            <span class="font-medium text-text">Terkonsolidasi Sistem Farmasi</span>
                        </div>
                    </div>
                    <p class="text-xs text-text-muted italic pt-1">
                        Catatan: Resepsionis memiliki hak akses baca untuk memeriksa ketersediaan obat saat melayani penagihan atau pertanyaan pasien. Penyesuaian stok dan pencatatan batch penerimaan dilakukan oleh Dokter atau Petugas Farmasi.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
