<x-app-layout>
    <x-slot name="title">Inventaris & Stok Obat</x-slot>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h1 class="font-semibold text-xl text-text leading-tight">
                    Inventaris & Stok Obat
                </h1>
                <p class="text-xs text-text-muted mt-0.5">Pemantauan ketersediaan obat, penyesuaian stok masuk (restock), dan peringatan stok kritis.</p>
            </div>
            <div>
                <a href="{{ route('apoteker.resep.index') }}" class="h-9 px-4 inline-flex items-center justify-center rounded-md text-xs font-semibold bg-surface border border-border text-text hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 transition">
                    Lihat Resep Masuk
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6" x-data="{ adjustModalOpen: false, selectedObat: null, adjustActionUrl: '' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-primary-50 border border-primary-200 text-primary-900 px-4 py-3 rounded-md text-sm font-medium" role="status">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-danger px-4 py-3 rounded-md text-sm font-medium" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-danger px-4 py-3 rounded-md text-sm" role="alert">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Stat Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-stat-card
                    label="Total Jenis Obat"
                    :value="$totalObat"
                    helper="Katalog obat aktif"
                />
                <x-stat-card
                    label="Stok Menipis"
                    :value="$menipisCount"
                    helper="Di bawah stok minimum"
                />
                <x-stat-card
                    label="Stok Habis"
                    :value="$habisCount"
                    helper="Stok bernilai nol"
                />
            </div>

            {{-- Filter & Pencarian Bar --}}
            <div class="bg-surface border border-border rounded-lg p-4 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                {{-- Status Filter Pills --}}
                <div class="flex flex-wrap items-center gap-1.5">
                    @php
                        $currentFilter = request('filter', 'semua');
                    @endphp
                    <a
                        href="{{ route('apoteker.obat.index', array_filter(['filter' => 'semua', 'q' => request('q')])) }}"
                        class="h-9 px-3 inline-flex items-center text-xs font-semibold rounded-md transition {{ $currentFilter === 'semua' ? 'bg-primary-700 text-white' : 'bg-bg text-text-muted hover:text-text hover:bg-slate-200/60' }}"
                    >
                        Semua Obat
                    </a>
                    <a
                        href="{{ route('apoteker.obat.index', array_filter(['filter' => 'menipis', 'q' => request('q')])) }}"
                        class="h-9 px-3 inline-flex items-center text-xs font-semibold rounded-md transition {{ $currentFilter === 'menipis' ? 'bg-primary-700 text-white' : 'bg-bg text-text-muted hover:text-text hover:bg-slate-200/60' }}"
                    >
                        Stok Menipis ({{ $menipisCount }})
                    </a>
                    <a
                        href="{{ route('apoteker.obat.index', array_filter(['filter' => 'habis', 'q' => request('q')])) }}"
                        class="h-9 px-3 inline-flex items-center text-xs font-semibold rounded-md transition {{ $currentFilter === 'habis' ? 'bg-primary-700 text-white' : 'bg-bg text-text-muted hover:text-text hover:bg-slate-200/60' }}"
                    >
                        Stok Habis ({{ $habisCount }})
                    </a>
                </div>

                {{-- Search Box --}}
                <form method="GET" action="{{ route('apoteker.obat.index') }}" class="flex items-center gap-2">
                    @if (request('filter'))
                        <input type="hidden" name="filter" value="{{ request('filter') }}">
                    @endif
                    <div class="relative w-full md:w-64">
                        <x-text-input
                            type="search"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Cari obat / nama dagang / kode"
                            class="w-full text-xs h-9 pl-3 pr-8"
                        />
                    </div>
                    <x-secondary-button type="submit" class="h-9 px-3 text-xs">
                        Cari
                    </x-secondary-button>
                    @if (request('q'))
                        <a href="{{ route('apoteker.obat.index', array_filter(['filter' => request('filter')])) }}" class="h-9 px-2.5 inline-flex items-center text-xs text-text-muted hover:text-danger">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- Tabel Daftar Obat --}}
            <x-table-container>
                <table class="w-full text-sm">
                    <thead class="bg-bg border-b border-border">
                        <tr class="text-left text-text-muted text-xs">
                            <th class="px-3 py-3 pl-4 font-medium">Kode & Nama Generik</th>
                            <th class="px-3 py-3 font-medium">Nama Dagang</th>
                            <th class="px-3 py-3 font-medium">Satuan</th>
                            <th class="px-3 py-3 font-medium font-mono text-center">Stok Saat Ini</th>
                            <th class="px-3 py-3 font-medium font-mono text-center">Batas Min</th>
                            <th class="px-3 py-3 font-medium font-mono text-right">Harga Jual</th>
                            <th class="px-3 py-3 font-medium">Status</th>
                            <th class="px-3 py-3 pr-4 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-subtle">
                        @forelse ($obats as $obat)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-3 py-3.5 pl-4">
                                    <div class="font-medium text-text">{{ $obat->nama_generik }}</div>
                                    <div class="font-mono text-xs text-text-muted">{{ $obat->kode }}</div>
                                </td>
                                <td class="px-3 py-3.5 text-xs text-text-muted">
                                    {{ $obat->nama_dagang ?: '-' }}
                                </td>
                                <td class="px-3 py-3.5 text-xs text-text capitalize">
                                    {{ $obat->satuan }}
                                </td>
                                <td class="px-3 py-3.5 font-mono text-xs font-semibold text-center {{ $obat->stok <= 0 ? 'text-danger' : ($obat->stok <= $obat->stok_minimum ? 'text-warning' : 'text-text') }}">
                                    {{ $obat->stok }}
                                </td>
                                <td class="px-3 py-3.5 font-mono text-xs text-text-muted text-center">
                                    {{ $obat->stok_minimum }}
                                </td>
                                <td class="px-3 py-3.5 font-mono text-xs text-right text-text font-medium">
                                    Rp {{ number_format($obat->harga, 0, ',', '.') }}
                                </td>
                                <td class="px-3 py-3.5">
                                    @if ($obat->stok <= 0)
                                        <x-status-pill status="danger" label="Habis" />
                                    @elseif ($obat->stok <= $obat->stok_minimum)
                                        <x-status-pill status="warning" label="Menipis" />
                                    @else
                                        <x-status-pill status="success" label="Tersedia" />
                                    @endif
                                </td>
                                <td class="px-3 py-3.5 pr-4 text-right">
                                    <button
                                        type="button"
                                        @click="
                                            selectedObat = { id: {{ $obat->id }}, nama: '{{ addslashes($obat->nama_generik) }}', stok: {{ $obat->stok }}, satuan: '{{ $obat->satuan }}' };
                                            adjustActionUrl = '{{ route('apoteker.obat.adjust', $obat) }}';
                                            adjustModalOpen = true;
                                        "
                                        class="h-9 px-3 inline-flex items-center justify-center rounded-md text-xs font-semibold bg-surface border border-border text-primary-700 hover:bg-primary-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 transition"
                                    >
                                        Ubah Stok
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center text-sm text-text-muted">
                                    Tidak ada data obat yang sesuai dengan filter atau pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </x-table-container>

            {{-- Pagination Links --}}
            @if ($obats->hasPages())
                <div class="pt-2">
                    {{ $obats->links() }}
                </div>
            @endif

            {{-- Modal Penyesuaian / Restock Obat --}}
            <div
                x-show="adjustModalOpen"
                class="fixed inset-0 z-50 overflow-y-auto"
                style="display: none;"
                aria-labelledby="modal-title"
                role="dialog"
                aria-modal="true"
            >
                <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                    <div
                        x-show="adjustModalOpen"
                        x-transition:enter="ease-out duration-200"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed inset-0 bg-slate-900/50 transition-opacity"
                        @click="adjustModalOpen = false"
                        aria-hidden="true"
                    ></div>

                    <div
                        x-show="adjustModalOpen"
                        x-transition:enter="ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="relative bg-surface rounded-lg border border-border text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full p-6 space-y-4"
                    >
                        <div class="border-b border-border pb-3">
                            <h3 class="text-base font-semibold text-text" id="modal-title">
                                Penyesuaian Stok Obat
                            </h3>
                            <p class="text-xs text-text-muted mt-1" x-text="selectedObat ? selectedObat.nama + ' (Stok saat ini: ' + selectedObat.stok + ' ' + selectedObat.satuan + ')' : ''"></p>
                        </div>

                        <form :action="adjustActionUrl" method="POST" class="space-y-4">
                            @csrf
                            <div class="space-y-1">
                                <x-input-label for="modal-tipe" value="Jenis Penyesuaian (wajib)" />
                                <x-select-input id="modal-tipe" name="tipe" required class="w-full text-xs">
                                    <option value="masuk">Stok Masuk / Restock (+)</option>
                                    <option value="koreksi">Koreksi Stok (Ubah ke angka baru)</option>
                                    <option value="keluar">Stok Keluar / Rusak / Expired (-)</option>
                                </x-select-input>
                            </div>

                            <div class="space-y-1">
                                <x-input-label for="modal-jumlah" value="Jumlah Unit (wajib)" />
                                <x-text-input
                                    id="modal-jumlah"
                                    name="jumlah"
                                    type="number"
                                    min="1"
                                    required
                                    placeholder="Contoh: 50"
                                    class="w-full text-xs font-mono"
                                />
                            </div>

                            <div class="space-y-1">
                                <x-input-label for="modal-alasan" value="Alasan Penyesuaian (wajib)" />
                                <x-text-input
                                    id="modal-alasan"
                                    name="alasan"
                                    type="text"
                                    required
                                    placeholder="Contoh: Penerimaan faktur distributor Kimia Farma No. 123"
                                    class="w-full text-xs"
                                />
                                <p class="text-[11px] text-text-muted">Setiap perubahan stok tercatat untuk kebutuhan audit mutu & stok opname.</p>
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-3 border-t border-border">
                                <x-secondary-button type="button" @click="adjustModalOpen = false" class="text-xs h-9">
                                    Batal
                                </x-secondary-button>
                                <x-primary-button type="submit" class="text-xs h-9">
                                    Simpan Perubahan Stok
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
