# Design System: Sistem Klinik (KlinikKu)

Dokumentasi desain antarmuka aplikasi manajemen operasional klinik: pendaftaran & antrean, pemeriksaan & rekam medis elektronik (EHR), resep & apotek, kasir, serta administrasi. Dipakai oleh **empat peran: Resepsionis, Dokter, Apoteker, dan Admin**. Dilengkapi standar ketat **Anti-Slop UI**.

---

## 0. Cara Memakai Dokumen Ini (untuk Developer & Agen AI)

1. Semua warna, radius, bayangan, font, dan spasi **wajib** diambil dari token di Bagian 3-7. Jangan menambah warna, ukuran, atau nilai spasi baru tanpa memperbarui dokumen ini.
2. Setiap layar dirancang untuk **satu peran utama**. Tentukan dulu peran, lalu ikuti Bagian 10 (Layar per Peran).
3. Jika ada konflik: **Keselamatan pasien (Bagian 9) > Aksesibilitas (Bagian 13) > Aturan Anti-Slop (Bagian 16) > estetika**.
4. Sebelum menyerahkan pekerjaan, jalankan Delivery Gate (Bagian 17).
5. Kode `R-xx` mengacu pada rule Anti-Slop yang sudah ada dan dipertahankan dari versi sebelumnya.

---

## 1. Visual Theme, Purpose & Dials

Desain mengutamakan fungsi klinis: cepat dibaca, minim beban kognitif, bebas ornamen palsu, dan tahan terhadap shift kerja yang padat. Tampilan harus terasa tenang, tepercaya, dan konsisten di semua peran.

### Prinsip Inti
1. **Keselamatan sebelum kecepatan.** Alergi, identitas pasien, dan dosis tidak pernah tersembunyi.
2. **Satu layar, satu keputusan.** Elemen terpenting (pasien berikutnya, resep berikutnya, tagihan berikutnya) selalu tampil tanpa scroll.
3. **Konsisten antar-modul.** Pola tabel, form, dan status identik di semua peran agar petugas yang berpindah modul tidak belajar ulang.
4. **Jujur.** Tidak ada angka, tren, atau data tiruan yang tidak berasal dari sistem.

### Deklarasi Parameter (Taste & Anti-Slop Dials)
- **Density: 7 / 10 (Daily App Balanced ke Cockpit).** Informasi klinis (No. RM, antrean, status kunjungan, dosis) ringkas dan terstruktur. Layar dokter boleh sedikit lebih longgar (6/10) karena banyak teks bebas.
- **Rhythm / Variance: 3 / 10 (Predictable Structured).** Layout simetris dan seragam antar-modul.
- **Motion Dial: 1 / 10 (Static Functional).** Transisi hanya pada hover, focus, dan buka/tutup dialog (150ms). **Dilarang keras animasi looping, floating elements, atau perpetual pulsing.**

---

## 2. Pengguna, Peran & Hak Akses

### Ringkasan Peran

| Peran | Tugas utama | Perangkat umum | Pertanyaan yang harus terjawab saat login |
|---|---|---|---|
| **Resepsionis** | Daftar pasien, buat kunjungan, kelola antrean, kasir | Desktop | "Siapa yang menunggu dan ke poli mana?" |
| **Dokter** | Panggil pasien, periksa, isi rekam medis, tulis resep | Desktop / tablet | "Siapa pasien saya berikutnya, apa alerginya?" |
| **Apoteker** | Verifikasi resep, siapkan & serahkan obat, kelola stok | Desktop | "Resep mana yang menunggu paling lama?" |
| **Admin** | Kelola pengguna, master data, laporan, audit | Desktop | "Apa yang butuh perhatian hari ini?" |

### Matriks Akses (usulan awal, sesuaikan dengan kebijakan klinik)

Prinsip: **data minimum sesuai peran**. Isi rekam medis hanya tampil untuk yang membutuhkannya.

| Modul | Resepsionis | Dokter | Apoteker | Admin |
|---|---|---|---|---|
| Data demografi pasien | Buat, ubah | Lihat | Lihat | Lihat, ubah |
| Alergi pasien | Isi, lihat | Isi, ubah, lihat | Lihat | Lihat |
| Kunjungan & antrean | Buat, ubah, batalkan | Lihat poli sendiri, ubah status | Tidak tampil | Lihat |
| Rekam medis (SOAP, diagnosis) | Tidak tampil | Buat, ubah | Ringkasan diagnosis + alergi saja | Tidak tampil (akses via audit) |
| Resep | Tidak tampil | Buat, ubah | Verifikasi, siapkan, serahkan | Lihat |
| Stok obat | Tidak tampil | Lihat ketersediaan | Kelola | Kelola |
| Tagihan & kasir | Kelola | Tidak tampil | Tidak tampil | Lihat, batalkan dengan alasan |
| Master data (poli, tarif, jadwal) | Tidak tampil | Lihat jadwal sendiri | Tidak tampil | Kelola |
| Pengguna, peran, audit log | Tidak tampil | Tidak tampil | Tidak tampil | Kelola |

Catatan: ketentuan rekam medis dan pelindungan data pribadi (mis. Permenkes tentang Rekam Medis dan UU Pelindungan Data Pribadi) perlu diverifikasi dengan pihak legal klinik sebelum matriks ini dikunci.

---

## 3. Color Palette & Restraint Rules

Aturan pembatasan warna: **2 Core Neutrals + 1 Single Deliberate Accent** (R-29).

### Core Neutrals (Slate Base)
| Token | Nilai | Pakai untuk |
|---|---|---|
| `--color-bg` | `#F1F5F9` (Slate-100) | Canvas halaman |
| `--color-surface` | `#FFFFFF` | Kartu, tabel, modal, sidebar, topbar. Selalu solid (matte) |
| `--color-text` | `#0F172A` (Slate-900) | Teks judul, angka, konten utama. **Dilarang `#000000`** |
| `--color-text-muted` | `#475569` (Slate-600) | Label form, petunjuk, timestamp |
| `--color-text-subtle` | `#64748B` (Slate-500) | **Hanya** placeholder dan ikon dekoratif di atas permukaan putih |
| `--color-border` | `#CBD5E1` (Slate-300) | Garis pemisah 1px solid |
| `--color-border-subtle` | `#E2E8F0` (Slate-200) | Pemisah baris tabel |

> Perubahan: teks muted dinaikkan dari Slate-500 ke Slate-600 karena Slate-500 di atas canvas `#F1F5F9` kurang dari 4.5:1 (lihat tabel kontras).

### The Single Accent: Medical Teal
Aksen hanya untuk elemen penentu keputusan (R-01). Dilarang menyebar ke seluruh layar.

| Token | Nilai | Pakai untuk |
|---|---|---|
| `--primary-50` | `#F0FDFA` | Hover baris tabel, latar item sidebar aktif, latar notifikasi sukses |
| `--primary-100` | `#CCFBF1` | `::selection` |
| `--primary-600` | `#0D9488` | **Focus ring** dan outline `focus-visible` saja |
| `--primary-700` | `#0F766E` | Tombol primer, tab/menu aktif, tautan penting, header & hari terpilih Flatpickr |
| `--primary-800` | `#115E59` | Hover tombol primer |
| `--primary-900` | `#134E4A` | Active (ditekan) tombol primer |

> Perubahan: hover tombol primer berpindah dari 600 ke 800 (makin gelap, bukan makin terang) karena teks putih di atas `#0D9488` hanya sekitar 3.7:1. Warna 600 tidak dipakai sebagai latar teks.

### Semantic Status Tokens (Fungsional, Bukan Dekorasi)
Hanya dipakai bila ada status nyata pada data (R-31). **Status tidak boleh disampaikan hanya lewat warna**: selalu sertakan teks label.

| Status | Teks | Latar | Border |
|---|---|---|---|
| Success | `#15803D` | `#F0FDF4` | `#BBF7D0` |
| Warning | `#B45309` | `#FFFBEB` | `#FDE68A` |
| Danger | `#B91C1C` | `#FEF2F2` | `#FECACA` |
| Info | `#1D4ED8` | `#EFF6FF` | `#BFDBFE` |
| Neutral | `#334155` | `#F8FAFC` | `#CBD5E1` |

Token solid untuk tombol: `--danger: #B91C1C` (hover `#991B1B`, active `#7F1D1D`).

### Perkiraan Rasio Kontras (verifikasi ulang dengan alat sebelum rilis)
| Kombinasi | Perkiraan | Hasil |
|---|---|---|
| Text `#0F172A` di `#F1F5F9` | sekitar 16:1 | Lulus |
| Muted `#475569` di `#FFFFFF` / `#F1F5F9` | sekitar 7.6 / 6.9:1 | Lulus |
| Slate-500 `#64748B` di `#F1F5F9` | sekitar 4.3:1 | Gagal, jangan untuk teks di canvas |
| Putih di Primary 700 `#0F766E` | sekitar 5.5:1 | Lulus |
| Putih di Primary 600 `#0D9488` | sekitar 3.7:1 | Gagal untuk teks kecil |
| Teks status di latar status | sekitar 4.7 sampai 6.5:1 | Lulus |

### Token CSS (sumber kebenaran)
```css
:root {
  --color-bg: #F1F5F9;
  --color-surface: #FFFFFF;
  --color-text: #0F172A;
  --color-text-muted: #475569;
  --color-text-subtle: #64748B;
  --color-border: #CBD5E1;
  --color-border-subtle: #E2E8F0;

  --primary-50: #F0FDFA;  --primary-100: #CCFBF1; --primary-600: #0D9488;
  --primary-700: #0F766E; --primary-800: #115E59; --primary-900: #134E4A;

  --success-fg: #15803D; --success-bg: #F0FDF4; --success-bd: #BBF7D0;
  --warning-fg: #B45309; --warning-bg: #FFFBEB; --warning-bd: #FDE68A;
  --danger-fg:  #B91C1C; --danger-bg:  #FEF2F2; --danger-bd:  #FECACA;
  --info-fg:    #1D4ED8; --info-bg:    #EFF6FF; --info-bd:    #BFDBFE;
  --neutral-fg: #334155; --neutral-bg: #F8FAFC; --neutral-bd: #CBD5E1;

  --shadow-sm: 0 1px 3px rgba(15, 23, 42, .10);
  --shadow-md: 0 4px 12px rgba(15, 23, 42, .12);
  --shadow-lg: 0 12px 32px rgba(15, 23, 42, .18);
}
```

```js
// tailwind.config.js (extend)
colors: {
  bg: 'var(--color-bg)', surface: 'var(--color-surface)',
  text: 'var(--color-text)', 'text-muted': 'var(--color-text-muted)',
  border: 'var(--color-border)',
  primary: { 50:'#F0FDFA', 100:'#CCFBF1', 600:'#0D9488', 700:'#0F766E', 800:'#115E59', 900:'#134E4A' },
  danger: '#B91C1C',
},
fontFamily: {
  sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
  mono: ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
},
```

---

## 4. Surface, Elevation & Radii Discipline

### Elevasi & Bayangan (R-10, R-12, R-13)
- **Zero Glassmorphism:** dilarang `backdrop-filter: blur(...)`. Semua kontainer solid.
- **Zero Glow / Ambient Shadows.**
- **Flat by Default:** elemen datar dengan `border: 1px solid var(--color-border)`.
- **Tingkat terukur:**
  - `shadow-sm`: kartu data dan form wrapper.
  - `shadow-md`: dropdown, datepicker popover, tooltip.
  - `shadow-lg`: dialog konfirmasi.

### Radii Hierarchy (R-11)
- **Small `6px` (`rounded`):** badge kotak, hari Flatpickr, tombol aksi tabel.
- **Medium `8px` (`rounded-md`):** tombol, input, select, dropdown.
- **Large `12px` (`rounded-lg`):** kartu metrik, kontainer tabel, modal.
- **Pill (`rounded-full`):** **hanya** status pill dan penanda alergi. Dilarang untuk tombol atau input.

### Skala Spasi
Seluruh aturan padding, margin, gap, dan tinggi kontrol ada di **Bagian 6 (Spacing & Layout Rhythm)**.

### Skala z-index
`sticky header 10 / sidebar 20 / dropdown & popover 30 / modal backdrop 40 / modal 50 / toast 60`.

---

## 5. Typographic Architecture & Format Data

### Font Stacks
1. **Sans (`Inter, system-ui, -apple-system, sans-serif`):** label, paragraf, heading, navigasi. `font-variant-ligatures: common-ligatures`.
2. **Mono (`"JetBrains Mono", ui-monospace, monospace`):** **wajib** untuk No. RM, No. Antrean, NIK, nominal Rupiah, dosis, jumlah stok, dan timestamp log. Menjamin karakter tabular tidak bergeser (R-06).
   - Untuk angka biasa dalam teks Inter (usia, jumlah), gunakan `font-variant-numeric: tabular-nums`.

### Skala Tipografi
| Peran | Kelas | Ukuran |
|---|---|---|
| H1 / Judul halaman | `text-xl font-semibold` | 20px |
| Section heading | `text-base font-semibold` | 16px |
| Body & sel tabel | `text-sm leading-5` | 14px |
| Angka metrik besar | `text-2xl font-semibold font-mono` | 24px |
| Label & microcopy | `text-xs font-medium text-text-muted` | 12px |

**Aturan keselamatan teks:** informasi kritis (alergi, dosis, peringatan interaksi) **tidak boleh** memakai 12px. Minimal `text-sm font-semibold`.

### Format Data (seragam di seluruh aplikasi)
| Data | Format tampil | Catatan |
|---|---|---|
| Mata uang | `Rp 1.250.000` | Titik pemisah ribuan, mono, rata kanan di tabel |
| Tanggal (tampil) | `24 Okt 2026` | Bulan tiga huruf bahasa Indonesia |
| Tanggal (input) | `24/10/2026` | Flatpickr `d/m/Y` |
| Waktu | `14:05` | 24 jam, zona WIB/WITA/WIT disebut hanya bila relevan |
| No. Antrean | `A-012` | Prefiks poli + 3 digit, mono |
| No. RM | Sesuai kebijakan klinik, mono | Selalu ditampilkan penuh, tidak dipotong |
| NIK | `3374••••••••0001` | Disamarkan default; tombol "Tampilkan" hanya untuk peran berwenang dan tercatat di audit log |
| Dosis | `500 mg`, `3 × 1 tablet sesudah makan` | Mono, satuan selalu eksplisit |
| Usia | `34 th`, `8 bln`, `12 hr` | Bayi < 1 tahun ditampilkan dalam bulan/hari |

---

## 6. Spacing & Layout Rhythm

Tujuan: semua menu (Resepsionis, Dokter, Apoteker, Admin) terasa satu keluarga. **Pola yang sama selalu memakai jarak yang sama**, di layar mana pun.

### 6.1 Prinsip
1. **Basis 4px.** Hanya nilai dari skala 6.2 yang boleh dipakai.
2. **Padding = ruang di dalam komponen. Gap = ruang antar-komponen.** Keduanya tidak dicampur.
3. **Komponen tidak membawa margin luar.** Jarak antar-elemen diatur oleh parent. Prioritas: `gap-*` (flex/grid), lalu `space-y-*` / `space-x-*`, margin hanya sebagai pengecualian (6.6).
4. **Pola sama, jarak sama.** Dua layar dengan struktur yang sama (mis. Antrean dan Resep Masuk) wajib identik jaraknya.
5. **Tidak ada mode kepadatan terpisah.** Perbedaan kerapatan antar-peran hanya lewat pilihan kartu `p-4` (padat) atau `p-6` (longgar), bukan nilai spasi baru.

### 6.2 Skala Token Spasi
| Token | Nilai | Tailwind | Pemakaian utama |
|---|---|---|---|
| `space-0.5` | 2px | `0.5` | Hanya `py-0.5` pada pill dan jarak antar-baris dalam sel multi-baris |
| `space-1` | 4px | `1` | Label ke input, antar-item menu, antar-pill rapat |
| `space-2` | 8px | `2` | Ikon ke teks, antar-pill alergi, antar-paragraf, padding item dropdown |
| `space-3` | 12px | `3` | Antar-tombol, judul section ke isi, padding vertikal toolbar/alert/sel tabel |
| `space-4` | 16px | `4` | Padding kartu padat, antar-field, antar-kartu dalam grid |
| `space-6` | 24px | `6` | Padding halaman, kartu form, dialog; antar-section; antar-grup form |
| `space-8` | 32px | `8` | Pemisah blok besar pada halaman panjang (mis. laporan) |
| `space-12` | 48px | `12` | Empty state dan ruang kosong besar |

Nilai di luar skala (mis. 5, 10, 14, 20, 28, 40 atau nilai arbitrary seperti `mt-[13px]`) **dilarang**.

### 6.3 Tinggi & Padding Kontrol (menggantikan spesifikasi lama)
Semua kontrol memakai **tinggi eksplisit** agar sejajar saat berdampingan (mis. input pencarian di sebelah tombol). Border sudah termasuk dalam tinggi (`box-sizing: border-box`).

| Kontrol | Tinggi | Kelas dasar |
|---|---|---|
| Tombol standar (primary, secondary, danger) | 36px | `h-9 px-4 gap-2` |
| Tombol kecil / aksi tabel | 32px | `h-8 px-3 gap-2` |
| Tombol ikon | 36 x 36px | `size-9` |
| Input teks, select, datepicker | 36px | `h-9 px-3` |
| Textarea | min 96px | `min-h-24 px-3 py-2` |
| Checkbox / radio | kontrol 16px, area klik 36px | `min-h-9 gap-2` |
| Item menu sidebar | 36px | `h-9 px-3 gap-3` |
| Item dropdown | 36px | `px-3 py-2` |
| Tab | 44px | `px-4 py-3` |
| Status pill | 20px | `px-2 py-0.5 text-xs leading-4` |

**Perangkat sentuh** (`@media (pointer: coarse)`): kontrol 32px dan 36px naik ke `h-11` (44px), jarak antar-aksi minimal `gap-2`.

Perbaikan: versi lama memakai `min-h-9` + `py-2.5` yang sebenarnya menghasilkan sekitar 40px. Sekarang tinggi ditetapkan eksplisit (36px).

### 6.4 Padding per Komponen
| Komponen | Ukuran / padding | Kelas |
|---|---|---|
| Area konten halaman | 24px (desktop), 16px (< 768px) | `px-6 py-6` / `px-4 py-4` |
| Topbar | tinggi 56px | `h-14 px-6` (`px-4` di mobile), isi `gap-4` |
| Sidebar (kontainer) | lebar 240px | `w-60 p-3` |
| Area logo sidebar | tinggi 56px, sejajar topbar | `h-14 px-4` |
| Grup menu sidebar | label grup `px-3 pb-2 text-xs`; item `space-y-1`; antar-grup `space-y-6` | |
| Blok pengguna sidebar | border atas | `p-3` |
| Kartu padat (metrik, ringkasan) | 16px | `p-4` |
| Kartu form / detail | 24px | `p-6` |
| Header kartu (judul + aksi) | border bawah | `px-4 py-3` (padat) / `px-6 py-4` (form) |
| Kartu tabel | tanpa padding luar | `p-0 overflow-hidden rounded-lg` |
| Toolbar tabel (pencarian, filter) | border bawah, `gap-3` | `px-4 py-3` |
| Sel tabel | baris satu-baris sekitar 44px; kolom pertama `pl-4`, terakhir `pr-4` | `px-3 py-3` |
| Header tabel (sticky) | sekitar 32px | `px-3 py-2` |
| Footer tabel (pagination) | border atas | `px-4 py-3` |
| Sel multi-baris (nama, No. RM, alergi) | antar-baris 2px | `space-y-0.5` |
| Alert / banner | ikon ke teks 12px | `px-4 py-3 gap-3` |
| Dialog | header ke body `space-y-4`; footer `pt-6`, tombol rata kanan `gap-3` | `p-6` |
| Dropdown / popover | container | `p-1` |
| Tooltip | | `px-2 py-1` |
| Patient Header (sticky) | baris `space-y-2`; kolom info `gap-x-6 gap-y-1` | `px-6 py-3` |
| Footer aksi sticky (pemeriksaan) | border atas, tombol `gap-3` | `px-6 py-3` |
| Empty state | judul-deskripsi `space-y-1`; ke tombol `space-y-4` | `py-12` |
| Timeline riwayat | antar-item `space-y-6` | `pl-6` |

### 6.5 Ritme Antar-Elemen
| Hubungan | Jarak | Kelas |
|---|---|---|
| Antar-section halaman (page header, stat cards, alert, kartu utama) | 24px | `space-y-6` pada kontainer halaman |
| Judul halaman ke deskripsi singkat | 4px | `space-y-1` |
| Page header: judul (kiri) dan aksi (kanan) | 16px minimum | `flex justify-between gap-4` |
| Antar-tombol (page header, footer form, dialog) | 12px | `gap-3` |
| Grid stat card | 16px | `grid grid-cols-2 lg:grid-cols-4 gap-4` |
| Antar-kartu sejajar | 16px | `gap-4` |
| Judul section ke isi | 12px | `space-y-3` |
| Label, input, teks bantuan/error | 4px | `space-y-1` pada pembungkus field |
| Antar-field | 16px | `gap-4` (grid maksimal 2 kolom) |
| Judul grup form ke field | 12px | `space-y-3` |
| Antar-grup form | 24px | `space-y-6` |
| Footer form | 24px dari grup terakhir, border atas | `pt-6`, tombol `gap-3` rata kanan |
| Ikon ke teks (tombol, label) | 8px | `gap-2` |
| Ikon ke teks (menu sidebar, alert) | 12px | `gap-3` |
| Antar-pill alergi | 8px | `gap-2` |
| Antar-pill rapat dalam sel | 4px | `gap-1` |
| Antar-paragraf | 8px | `space-y-2` |
| Antar-item menu | 4px | `space-y-1` |
| Antar-tab | 8px | `gap-2` |

### 6.6 Aturan Margin
1. **Root komponen (Blade/React) tidak boleh punya `m*` luar.** Komponen yang dipakai di dua tempat dengan jarak berbeda berarti jaraknya salah ditaruh di komponen.
2. Margin hanya untuk pengecualian yang disengaja: `mx-auto` (memusatkan kontainer), `mt-auto` (mendorong blok pengguna ke dasar sidebar), `ml-auto` (mendorong aksi ke kanan bila `justify-between` tidak cukup).
3. Dilarang margin negatif.
4. Dilarang nilai arbitrary (`mt-[13px]`, `p-[18px]`) dan spacer kosong (`<div class="h-4">`, `<br>` untuk jarak).
5. Dilarang menumpuk margin dan gap pada elemen yang sama.
6. Padding responsif mengikuti 6.4: halaman `px-6` turun ke `px-4`, kartu `p-6` turun ke `p-4` di bawah 768px.

### 6.7 Template Tata Letak Baku
Setiap halaman baru wajib memakai salah satu template berikut. Dashboard Resepsionis, Dokter (Antrean Saya), Apoteker (Resep Masuk), dan Admin (Ringkasan) memakai **Template A**.

**Template A: Halaman Daftar** (Antrean, Pasien, Resep Masuk, Tagihan, Stok, Pengguna, Audit Log, Dashboard)
```
Konten (px-6 py-6, space-y-6)
|- Page header: H1 (+ deskripsi, space-y-1) ........ [Sekunder] [Primer]   gap-3
|- Stat cards (opsional): grid gap-4
|- Alert (opsional)
`- Kartu tabel (p-0)
    |- Toolbar: [Cari...] [Filter...] ............ px-4 py-3, gap-3
    |- Tabel (sel px-3 py-3)
    `- Footer: info + pagination ................ px-4 py-3
```

**Template B: Halaman Form** (Daftar Pasien Baru, Buat Kunjungan, Master Data, Pengguna)
```
Konten (px-6 py-6, space-y-6)
|- Page header: H1 (+ deskripsi)
`- Kartu form (p-6, space-y-6)
    |- Grup 1: judul + grid 2 kolom gap-4
    |- Grup 2: ...
    `- Footer aksi: border-t pt-6, rata kanan, gap-3 [Batal] [Simpan]
```

**Template C: Halaman Detail / Pemeriksaan** (Pemeriksaan Aktif Dokter, Verifikasi Resep Apoteker, Detail Tagihan)
```
Patient Header (sticky di bawah topbar, px-6 py-3)
Konten (px-6 py-6, space-y-6)
|- Tabs (px-4 py-3 per tab, gap-2)
`- Panel: kartu p-6 (space-y-6)
Footer aksi sticky (px-6 py-3, gap-3)
```

### 6.8 Penegakan di Kode (opsional, disarankan)
Batasi skala Tailwind agar nilai di luar dokumen ini tidak bisa dipakai:
```js
// tailwind.config.js
theme: {
  spacing: {
    px: '1px', 0: '0', 0.5: '2px', 1: '4px', 2: '8px', 3: '12px', 4: '16px',
    6: '24px', 8: '32px', 9: '36px', 11: '44px', 12: '48px', 14: '56px',
    24: '96px', 60: '240px',
  },
}
```
Catatan: mengganti `theme.spacing` menghapus kelas lain seperti `h-10` atau `p-5`. Tambahkan nilai baru hanya setelah dokumen ini diperbarui.

---

## 7. Layout, Navigasi & Responsif

### App Shell
- **Sidebar kiri** 240px, solid putih, border kanan 1px. Berisi logo klinik, menu sesuai peran, dan blok pengguna (nama, peran, keluar) di bawah.
- **Topbar** 56px, solid putih, border bawah 1px. Berisi judul halaman, pencarian global `"Cari nama / No. RM / NIK"` (pintasan `/`), dan tanggal-jam.
- **Konten:** `max-width: 1440px`; padding dan jarak antar-section mengikuti Bagian 6 (`px-6 py-6`, `space-y-6`).
- Item menu aktif: latar `primary-50`, teks `primary-700`, border kiri 3px `primary-700`. Item lain teks `text`. Tiap item punya ikon outline 20px **dan** label teks.

### Menu per Peran
- **Resepsionis:** Dashboard Resepsionis, Daftar Pasien, Kunjungan & Antrean, Tagihan & Kasir.
- **Dokter:** Antrean Saya, Pemeriksaan Aktif, Riwayat Pasien, Jadwal Saya.
- **Apoteker:** Resep Masuk, Stok Obat, Riwayat Penyerahan.
- **Admin:** Ringkasan Operasional, Pengguna & Peran, Master Data (Poli, Dokter & Jadwal, Tarif Layanan, Obat), Laporan, Audit Log, Pengaturan Klinik.

### Breakpoint
| Lebar | Perilaku |
|---|---|
| >= 1280px | Sidebar penuh, tabel semua kolom |
| 1024-1279px | Sidebar penuh, kolom sekunder tabel disembunyikan |
| 768-1023px (tablet) | Sidebar menjadi rail ikon 64px dengan tooltip; dioptimalkan untuk Dokter |
| < 768px | Sidebar off-canvas dengan tombol menu terlihat; tabel scroll horizontal di kontainernya sendiri |

Pada perangkat sentuh (`@media (pointer: coarse)`): tinggi kontrol naik dari `h-9` ke `h-11` (44px) dan jarak antar-tombol aksi minimal 8px (`gap-2`). Detail di Bagian 6.

### Ikon
Satu set ikon outline (mis. Lucide atau Heroicons Outline), stroke 1.5, ukuran 16/20px. Hanya ikon fungsional yang menyertai label. Dilarang emoji sebagai ikon.

---

## 8. Alur Kerja & Status

### Siklus Pasien
```
Daftar Pasien -> Buat Kunjungan -> Antrean -> Dipanggil -> Pemeriksaan (SOAP)
   -> Resep -> Verifikasi Apoteker -> Disiapkan -> Diserahkan -> Tagihan -> Lunas
```
Setiap perpindahan tahap harus terlihat di semua peran terkait tanpa muat ulang manual (lihat "Pembaruan Antrean" di Bagian 11).

### Pemetaan Status
Pill status memakai token semantik. Teks label **selalu ada**.

**Kunjungan**
| Status | Token |
|---|---|
| Menunggu | Warning |
| Dipanggil | Info |
| Dalam Pemeriksaan | Info |
| Selesai | Success |
| Tidak Hadir | Neutral |
| Batal | Danger |

**Resep**
| Status | Token |
|---|---|
| Menunggu Verifikasi | Warning |
| Perlu Klarifikasi (ke dokter) | Warning |
| Disiapkan | Info |
| Siap Diserahkan | Info |
| Diserahkan | Success |
| Dibatalkan | Danger |

**Tagihan**
| Status | Token |
|---|---|
| Belum Bayar | Warning |
| Dibayar Sebagian | Info |
| Lunas | Success |
| Dibatalkan | Danger |

**Stok Obat**
| Kondisi | Token |
|---|---|
| Tersedia | tanpa pill (angka biasa) |
| Menipis (di bawah stok minimum) | Warning |
| Habis | Danger |
| Kedaluwarsa / <= 30 hari | Danger / Warning |

---

## 9. Pola Keselamatan Pasien & Privasi

### Identitas Pasien (Patient Header)
Layar dokter, apoteker, dan kasir yang menyangkut satu pasien wajib menampilkan **Patient Header** yang sticky di bawah topbar:
- Nama lengkap (`text-base font-semibold`), **No. RM (mono)**, usia, jenis kelamin, tanggal lahir.
- Minimal **dua pengenal** (nama + No. RM atau tanggal lahir) selalu terlihat saat melakukan tindakan yang mengubah data.
- Banner alergi (di bawah).

### Banner Alergi (tiga kondisi berbeda)
| Kondisi | Tampilan |
|---|---|
| Ada alergi tercatat | Danger: `Alergi: Penisilin, Sulfa`. `text-sm font-semibold`, pill per alergen |
| Dipastikan tidak ada alergi | Neutral: `Tidak ada alergi tercatat` |
| Belum pernah ditanyakan | **Warning**: `Alergi belum ditanyakan. Konfirmasi ke pasien.` |

Membedakan "tidak ada" dan "belum ditanyakan" mencegah kesan aman palsu.

### Peringatan Klinis
- Saat dokter menambah obat yang cocok dengan alergen pasien, tampilkan alert Danger **inline** di baris resep dan blokir simpan sampai dokter memilih "Tetap lanjutkan" dengan **alasan wajib diisi**. Keputusan masuk audit log.
- Apoteker melihat peringatan yang sama saat verifikasi.
- Peringatan **tidak pernah** berupa toast yang hilang sendiri.

### Tindakan Berisiko
- Hapus, batalkan, atau ubah data yang sudah final memakai **dialog konfirmasi** yang menyebut objeknya: `Batalkan kunjungan A-012 atas nama [nama pasien]?` Tombol: `Batalkan Kunjungan` (danger) dan `Kembali` (secondary).
- Rekam medis yang sudah ditandatangani **tidak dihapus**; koreksi lewat addendum bertanda waktu dan nama pengguna.
- Pembatalan tagihan dan penyesuaian stok mewajibkan alasan.

### Privasi & Sesi
- Data sensitif (NIK, isi rekam medis) hanya tampil pada peran yang berwenang (Bagian 2).
- Layar pemeriksaan tidak menampilkan data pasien lain.
- Sesi otomatis terkunci setelah tidak aktif (usulan 15 menit); layar kunci menyembunyikan seluruh data dan meminta kata sandi.
- Semua pembuatan, perubahan, penghapusan, dan pembukaan data sensitif dicatat: siapa, kapan, apa (tampil di Audit Log admin).
- Placeholder dan seeder tidak memakai data pasien nyata.

---

## 10. Layar per Peran

Semua layar mengikuti **Decision-First Layout**: elemen penentu keputusan berada di bagian atas tanpa scroll.

### 9.1 Resepsionis: Dashboard Resepsionis
- **Baris atas:** 4 stat card absolut: `Total Hari Ini`, `Menunggu`, `Selesai`, `Pendapatan` (nominal mono). Pada kartu `Pendapatan`, label hanya "Pendapatan Hari Ini".
- **Konten utama:** tabel **Antrean Hari Ini** langsung di layar utama. Kolom: `No. Antrean`, `Nama Pasien & No. RM` (+ penanda alergi), `Poli`, `Dokter`, `Waktu Daftar`, `Status`, `Aksi` (`Detail`, `Edit`, `Batalkan` sebagai tautan terlihat).
- **Aksi cepat:** tombol primer `Buat Kunjungan` dan secondary `Daftar Pasien Baru` di header halaman. Hanya **satu** tombol primer per layar.
- Pendaftaran: cari pasien dulu (`Cari nama / No. RM / NIK`) sebelum membuat pasien baru, untuk mencegah duplikat. Jika hasil mirip ditemukan, tampilkan "Mungkin pasien yang sama" sebelum menyimpan.

### 9.2 Dokter: Antrean Saya & Pemeriksaan
- **Antrean Saya:** kartu **Pasien Berikutnya** di atas (nama, No. RM, usia, keluhan awal, alergi) dengan tombol `Panggil Pasien`. Di bawahnya tabel antrean poli dokter tersebut.
- **Pemeriksaan Aktif:** Patient Header sticky, lalu tab: `Pemeriksaan (SOAP)`, `Resep`, `Riwayat`, `Ringkasan`.
  - **SOAP:** empat blok berlabel Subjektif, Objektif, Asesmen, Rencana. Field tanda vital (TD, nadi, suhu, napas, SpO2, BB, TB) memakai input mono dengan satuan tetap di kanan field.
  - **Diagnosis:** pencarian kode ICD-10 dengan kode mono; satu diagnosis primer.
  - **Resep:** baris per obat: nama, dosis, aturan pakai, jumlah, catatan. Peringatan alergi inline (Bagian 9).
  - **Riwayat:** timeline kunjungan terdahulu, terbaru di atas, dapat dibuka per kunjungan.
- Aksi utama di footer sticky: `Simpan Draf` (secondary) dan `Selesaikan Pemeriksaan` (primer). Perubahan belum tersimpan ditandai teks "Belum disimpan" dan ada peringatan sebelum meninggalkan halaman.

### 9.3 Apoteker: Resep Masuk & Stok
- **Resep Masuk:** tabel diurutkan **terlama menunggu di atas**. Kolom: `No. Resep`, `Nama Pasien & No. RM` (+ alergi), `Dokter`, `Jumlah Item`, `Menunggu Sejak`, `Status`, `Aksi` (`Verifikasi`).
- **Verifikasi:** Patient Header + daftar obat. Per baris tampil nama, dosis, aturan pakai, **stok tersedia**, dan peringatan (alergi, stok kurang). Tombol: `Verifikasi Resep` (primer), `Minta Klarifikasi Dokter` (secondary).
- **Penyerahan:** checklist verifikasi dua pengenal pasien sebelum `Serahkan Obat`. Cetak etiket obat (monokrom, mono untuk dosis).
- **Stok Obat:** tabel dengan filter `Menipis`, `Habis`, `Segera Kedaluwarsa`. Kolom: `Nama Obat`, `Stok` (mono), `Satuan`, `Batch`, `Kedaluwarsa`, `Status`. Penyesuaian stok wajib alasan.

### 9.4 Admin: Ringkasan Operasional & Kelola
- **Ringkasan Operasional:** stat card absolut (kunjungan hari ini, resep menunggu, tagihan belum bayar, pengguna aktif). Di bawahnya daftar **Perlu Perhatian**: stok menipis/kedaluwarsa, dokter tanpa jadwal minggu ini, tagihan belum lunas melewati batas klinik. Tiap item punya tautan aksi langsung. Daftar ini kosong dengan pesan jujur bila tidak ada masalah.
- **Pengguna & Peran:** tabel pengguna (`Nama`, `Peran`, `Status`, `Login Terakhir`, `Aksi`). Menonaktifkan pengguna memakai dialog konfirmasi; **nonaktifkan, bukan hapus**.
- **Master Data:** tabel + form dengan pola yang sama persis seperti modul lain.
- **Laporan:** filter periode di atas, hasil tabel, ekspor. Grafik hanya bila ada seri data historis nyata (satu warna Teal + Slate, tanpa gradien, tanpa 3D).
- **Audit Log:** tabel read-only, waktu mono, filter pengguna/modul/tindakan, tidak dapat diubah.

### 9.5 Kasir (Resepsionis, Admin lihat)
- Daftar tagihan: `No. Tagihan`, `Pasien & No. RM`, `Total` (mono, kanan), `Status`, `Aksi`.
- Detail tagihan: rincian layanan dan obat, subtotal, total, metode bayar, kembalian (mono, kanan). Tombol primer `Terima Pembayaran`.
- Kuitansi mengikuti aturan cetak di Bagian 12.

---

## 11. App & Dashboard Rules (Anti-Slop Standard)

### Decision-First Layout (C-3, R-20)
Dashboard tidak dibuat sekadar memenuhi "template dashboard". Setiap peran punya satu pertanyaan utama (Bagian 2) dan layarnya menjawab itu dulu.

### Honest Metrics Only (R-17, R-38)
- Stat card hanya angka absolut aktual (`$totalHariIni`, `$menunggu`, `$selesai`, `$pendapatan`).
- **Dilarang delta persentase buatan** (`+12% minggu ini`, panah hijau tren) tanpa seri data historis nyata.
- Data kosong ditampilkan sebagai `0`.

### Table Columns & Information Ordering (R-26)
- Kolom pertama = penentu keputusan:
  - Antrean: `No. Antrean`, lalu `Nama Pasien & No. RM`.
  - Pasien: `No. RM`, lalu `Nama Pasien & Penanda Alergi`.
  - Resep: `No. Resep`, lalu `Nama Pasien & No. RM`.
- Penanda alergi diletakkan di bawah nama pasien, warna Danger.
- Padding sel `px-3 py-3` (baris satu-baris sekitar 44px). Header sticky `px-3 py-2`, teks `text-xs font-medium text-text-muted`. Tanpa zebra; pemisah baris `border-subtle`; hover `primary-50`.
- Kolom angka (Rupiah, jumlah) rata kanan; teks rata kiri.
- Pagination di bawah tabel (`25` baris default) dengan info "Menampilkan 1-25 dari N". Pengurutan hanya pada kolom yang bermakna, dengan indikator arah yang jelas.

### Pembaruan Antrean (tanpa animasi looping)
- Data antrean diperbarui otomatis berkala (usulan 15 detik). Tampilkan teks statis `Diperbarui 14:05:32` di header tabel.
- Baris baru atau berubah status ditandai label `Baru` atau perubahan pill, **tetap terlihat sampai dibuka atau 60 detik**; bukan kedip atau pulsing.
- Daerah yang diperbarui memakai `aria-live="polite"`.

### Placeholder & Data Hygiene (R-23, R-38)
- **Dilarang** `John Doe`, `Acme Corp`, `johndoe@example.com`, atau nama pasien fiktif generik pada form dan seeder produksi.
- Placeholder diizinkan:
  - Pencarian: `"Cari nama / No. RM / NIK"`
  - Alamat: `"Nama jalan, RT/RW, kelurahan"`
  - No. telp: `"08xxxxxxxxxx"`
  - Tanggal: `"hh/bb/tttt"`
  - Field wajib lain dibiarkan kosong tanpa placeholder.
- Data demo/seeder memakai nama jelas fiktif yang ditandai `[DEMO]` dan hanya di lingkungan non-produksi.

### Empty, Error & Loading States (R-27)
- **Empty state informatif:** alasan + solusi + aksi.
  - Antrean kosong: `"Belum ada kunjungan terdaftar untuk hari ini. Klik 'Buat Kunjungan' untuk mendaftarkan pasien ke antrean."`
  - Resep kosong (apoteker): `"Belum ada resep masuk. Resep muncul di sini setelah dokter menyelesaikan pemeriksaan."`
  - Hasil pencarian kosong: `"Tidak ada pasien yang cocok dengan 'xxx'. Periksa ejaan atau daftarkan sebagai pasien baru."`
- **Inline error:** pesan validasi persis di bawah field (`text-xs text-danger mt-1`) dan ringkasan error di atas form bila lebih dari satu. Fokus pindah ke field error pertama. Bukan toast yang hilang sendiri.
- **Loading:** skeleton **statis** (blok `bg-slate-200`, tanpa shimmer). Operasi > 1 detik menampilkan teks `Memuat...`. Tombol saat menyimpan menjadi nonaktif dengan teks `Menyimpan...` untuk mencegah klik ganda.
- **Gagal koneksi/server:** banner Danger persisten dengan tombol `Coba Lagi`; data yang sedang diisi tidak hilang.

---

## 12. Component Specifications

### Buttons
- **Primary (`<x-primary-button>`):** `bg-primary-700` hover `bg-primary-800`, active `bg-primary-900`. `h-9 px-4 gap-2` (flex, rata tengah), `rounded-md`, teks putih `font-semibold text-sm`. Tanpa panah dekoratif. **Maksimal satu per area aksi.**
- **Secondary (`<x-secondary-button>`):** latar putih, border 1px `primary-700`, teks `primary-700`, hover `bg-primary-50`.
- **Danger (`<x-danger-button>`):** `bg-danger` hover `#991B1B`, active `#7F1D1D`, teks putih, focus ring merah. Khusus pembatalan atau hapus.
- **Tautan aksi tabel:** teks `primary-700 text-sm font-medium`, garis bawah saat hover/focus. Aksi destruktif memakai teks `danger`.
- **Disabled:** latar `#E2E8F0`, teks `#64748B`, `cursor-not-allowed`, disertai penjelasan kenapa nonaktif bila tidak jelas.
- Label tombol berupa kata kerja spesifik: `Simpan Pasien`, `Buat Kunjungan`, `Serahkan Obat`.

### Inputs & Selects
- `<x-text-input>` & `<x-select-input>`: latar `#FFFFFF`, border `#CBD5E1`, teks `#0F172A`, placeholder `#64748B`; `h-9 px-3`, `rounded-md`.
- Fokus: `focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20`.
- Label selalu statis di atas input (`<x-input-label>`). **Dilarang floating label.**
- Field wajib ditandai teks `(wajib)` pada label, bukan hanya tanda bintang merah.
- Teks bantuan (`text-xs text-text-muted`) di bawah field; pesan error menggantikannya.
- Field numerik klinis (tanda vital, dosis) memakai satuan tetap di dalam atau di kanan field, mono, dan validasi rentang masuk akal dengan peringatan (bukan blokir) untuk nilai ekstrem.
- Form panjang dipecah ke grup berjudul (`Data Pribadi`, `Kontak`, `Alergi`) dengan jarak 24px; grid maksimal 2 kolom di desktop.

### Status Pills
- `inline-flex text-xs px-2 py-0.5 rounded-full border font-medium font-sans`.
- Isi: teks status + warna latar/border dari token. **Dilarang pulsing dot.**

### Penanda & Banner Alergi
- Pill per alergen: Danger, `rounded-full`, `text-xs font-semibold`.
- Banner di Patient Header: lihat Bagian 9.

### Alert / Banner
- Blok `rounded-md border px-4 py-3 text-sm` dengan token semantik. Ikon outline 16px + teks. Alert Danger dan Warning yang terkait keselamatan bersifat persisten.

### Tabs
- Tab aktif: teks `primary-700`, garis bawah 2px `primary-700`. Tab tidak aktif `text-muted`, hover `text`. Mendukung navigasi panah keyboard.

### Dialog / Modal
- `rounded-lg`, `shadow-lg`, lebar 480px (konfirmasi) atau 640px (form). Backdrop `rgba(15,23,42,.5)` **tanpa blur**. Fokus terkunci di dalam dialog, `Esc` menutup (kecuali dialog tindakan berisiko yang mewajibkan pilihan eksplisit), fokus kembali ke pemicu.

### Dropdown & Tooltip
- `shadow-md`, `rounded-md`, solid putih. Tooltip untuk rail ikon dan ikon-saja; tidak dipakai untuk informasi kritis.

### Date & Time Picker (Flatpickr)
- Tema solid Teal:
  - Month Header: latar `#0F766E`, teks putih.
  - Selected Day: latar `#0F766E`, teks putih, `rounded-[6px]`.
  - Today Marker: border `#14B8A6`, teks tebal `#0F766E`.
  - Hari lain teks `#0F172A`; hari nonaktif `#94A3B8`.
- Input tanggal lahir mendukung ketik manual `hh/bb/tttt`, bukan hanya klik kalender.

### Kartu Metrik
- `rounded-lg`, `shadow-sm`, border 1px, padding 16px. Label `text-xs text-muted`, angka `text-2xl font-semibold font-mono`. Tanpa ikon dekoratif, tanpa delta.

### Cetak Dokumen (Kuitansi, Etiket Obat, Resume)
- `@media print`: sembunyikan sidebar, topbar, tombol, dan tautan aksi.
- Layout monokrom bersih, kontras 100%, mono untuk seluruh rincian biaya dan dosis.
- Kuitansi memuat: identitas klinik, No. Tagihan, tanggal-jam, nama + No. RM, rincian, total, metode bayar, nama kasir.
- Ukuran halaman: kuitansi A5 atau termal 80mm; etiket sesuai printer label klinik.

---

## 13. Aksesibilitas (target WCAG 2.2 AA)

- **Keyboard penuh:** semua aksi dapat dijangkau dengan Tab; urutan fokus mengikuti urutan visual. Ring fokus `outline: 2px solid var(--primary-600); outline-offset: 2px` pada semua elemen interaktif (R-34).
- **Skip link** `Lewati ke konten utama` di awal halaman.
- **Pintasan keyboard** (dapat dilihat lewat `?`): `/` fokus ke pencarian, `N` buat kunjungan/entri baru (resepsionis), `Esc` tutup dialog. Pintasan tidak aktif saat mengetik di field.
- **Bukan hanya warna:** status = warna + teks; error = warna + teks + ikon; link di dalam paragraf bergaris bawah.
- **Target sentuh** minimal 44px pada perangkat sentuh.
- **Semantik:** `<table>` asli dengan `<th scope>`; `aria-label` untuk tombol ikon; `aria-live` untuk pembaruan antrean dan error form; `aria-current="page"` pada menu aktif; dialog memakai `role="dialog"` + `aria-labelledby`.
- **Reduced motion:** hormati `prefers-reduced-motion` (transisi dimatikan).
- **Zoom:** layout tetap fungsional pada 200% tanpa scroll horizontal pada halaman (kecuali tabel di kontainernya).

---

## 14. Motion

- Durasi 150ms, easing `ease-out`, hanya untuk `background-color`, `border-color`, `box-shadow`, `opacity`.
- Dialog: fade 100-150ms; tanpa slide/scale berlebihan.
- Dilarang: looping, bouncing, floating, parallax, shimmer, confetti, counter animasi angka.

---

## 15. Microcopy & Bahasa

- Bahasa Indonesia baku yang ramah dan lugas; hindari singkatan yang tidak umum dan jargon internal.
- Judul halaman = nama modul langsung: `Dashboard Resepsionis`, `Daftar Pasien`, `Tagihan & Kasir`, `Resep Masuk`.
- Tombol = kata kerja + objek. Hindari `OK`, `Submit`, `Klik di sini`.
- Pesan error menjelaskan masalah dan cara memperbaiki: `"No. telepon harus diawali 08 dan berisi 10-13 digit."` bukan `"Input tidak valid."`
- Pesan sukses singkat dan spesifik: `"Kunjungan A-012 berhasil dibuat."` Konfirmasi sukses non-kritis boleh berupa banner statis 5 detik; tidak ada autohide untuk error.
- Istilah baku konsisten: **Pasien, Kunjungan, Antrean, Poli, Rekam Medis, Resep, Tagihan, Kasir**. Jangan bergantian antara "Visit/Kunjungan" atau "Pembayaran/Tagihan".
- Tidak ada seruan, tanda seru berlebihan, atau bahasa promosi.

---

## 16. Banned Patterns (Daftar Larangan Anti-Slop)

Pola berikut **TIDAK BOLEH** muncul dalam antarmuka:
1. **Emoji di UI** (judul, tombol, kartu, alert) (R-04).
2. **Panah dekoratif pada tombol**: `"Simpan Pasien"`, bukan `"Simpan Pasien →"` (R-08).
3. **Status dot berkedip / pulsing** (R-19, R-31).
4. **Gradien ungu-biru AI**; seluruh warna dari palet Slate & Teal (R-01).
5. **Background pola grid / blueprint dots / noise** (R-07).
6. **Eyebrow badge di atas judul halaman** (R-09).
7. **Aksi disembunyikan di menu titik tiga** bila ruang cukup (R-26).
8. **Pure black `#000000`** (gunakan `#0F172A`).
9. **Glassmorphism, glow, bayangan besar lembut** (R-10, R-12, R-13).
10. **Floating label, placeholder sebagai pengganti label.**
11. **Delta persentase, sparkline, atau grafik tren tanpa data historis nyata** (R-17).
12. **Hero section, ilustrasi dekoratif, atau testimoni** di dalam aplikasi.
13. **Toast yang menghilang sendiri untuk error atau peringatan klinis.**
14. **Skeleton shimmer, spinner berputar tanpa batas pada halaman penuh.**
15. **Tombol atau input berbentuk pill.**
16. **Menyampaikan status hanya dengan warna.**
17. **Menampilkan data pasien di luar hak akses peran.**

---

## 17. Delivery Gate & UI Quality Checklist

Verifikasi sebelum menyelesaikan atau mengulas perubahan antarmuka.

**Visual & Anti-Slop**
- [ ] **Palet terkontrol:** hanya Slate + Teal, tanpa gradien default AI (R-01, R-29)
- [ ] **Aksen terbatas:** Teal hanya pada tombol primer, tab/menu aktif, link penting, fokus ring (R-29)
- [ ] **Bebas emoji** (R-04) dan **bebas animasi looping** (R-19)
- [ ] **Radii konsisten:** 8px untuk tombol/input, pill hanya untuk status & alergi (R-11)
- [ ] **Matte surfaces:** tanpa blur/glow (R-10)
- [ ] **Satu tombol primer** per area aksi
- [ ] **Angka monospace:** No. RM, NIK, No. Antrean, Rupiah, dosis, stok (R-06)
- [ ] **Spasi sesuai skala (Bagian 6):** hanya nilai 0.5 / 1 / 2 / 3 / 4 / 6 / 8 / 12, tanpa nilai arbitrary
- [ ] **Tinggi kontrol seragam:** tombol, input, select `h-9` (`h-11` di layar sentuh)
- [ ] **Tanpa margin luar pada komponen:** jarak diatur parent (`gap` / `space-y`)
- [ ] **Ritme halaman identik:** page header, stat cards, kartu tabel/form memakai template 6.7 di semua menu

**Data & Konten**
- [ ] **Data riil & jujur:** tanpa delta palsu (R-17, R-38)
- [ ] **Placeholder sehat:** tanpa `John Doe` (R-23, R-38)
- [ ] **Empty state lengkap:** penyebab + aksi berikutnya (R-27)
- [ ] **Format data seragam:** Rupiah, tanggal, waktu, usia sesuai Bagian 5
- [ ] **Istilah baku konsisten** (Bagian 15)

**Keselamatan & Privasi**
- [ ] **Patient Header** tampil di layar klinis dengan dua pengenal
- [ ] **Alergi** menampilkan tiga kondisi dengan benar (ada / tidak ada / belum ditanyakan)
- [ ] **Peringatan alergi obat** inline, memblokir simpan sampai ada alasan
- [ ] **Tindakan berisiko** memakai dialog konfirmasi yang menyebut objek
- [ ] **Hak akses peran** sesuai matriks; data sensitif disamarkan default
- [ ] **Audit log** mencatat tindakan penting

**Aksesibilitas**
- [ ] **Keyboard:** semua kontrol punya ring fokus jelas saat Tab (R-34)
- [ ] **Kontras** teks >= 4.5:1, komponen UI >= 3:1
- [ ] **Status** = warna + teks
- [ ] **Responsif:** tablet (Dokter) dan mobile diuji; target sentuh 44px
- [ ] **Reduced motion** dihormati

---

## 18. Catatan Revisi dari Versi Sebelumnya

- Menambah **Bagian 2** (peran & matriks akses) dan **Bagian 10** (layar per peran: Resepsionis, Dokter, Apoteker, Admin).
- Menambah **Bagian 8** (siklus pasien & pemetaan status kunjungan, resep, tagihan, stok) dan **Bagian 9** (keselamatan pasien & privasi: Patient Header, banner alergi tiga kondisi, peringatan interaksi, audit).
- Menambah **Bagian 7** (app shell, menu per peran, breakpoint) dan **Bagian 13** (aksesibilitas WCAG 2.2 AA).
- **Memperbaiki kontras:** teks muted menjadi Slate-600; hover tombol primer menjadi Primary 800; Primary 600 hanya untuk focus ring; hari terpilih Flatpickr memakai Primary 700.
- Menambah token **Neutral**, token CSS + konfigurasi Tailwind, skala spasi, z-index, dan **format data** (Rupiah, tanggal, NIK tersamar, dosis).
- Memperluas komponen (tabs, dialog, alert, kartu metrik, disabled state, cetak etiket) serta aturan **loading/error** dan **pembaruan antrean** tanpa animasi looping.
- Memperluas daftar larangan dan Delivery Gate; dipertahankan seluruh kode `R-xx` lama.
- Menambah **Bagian 6 (Spacing & Layout Rhythm)**: skala token spasi, tinggi & padding kontrol, padding per komponen, ritme antar-elemen, aturan margin, tiga template tata letak baku, dan penegakan lewat konfigurasi Tailwind. Memperbaiki konflik tinggi kontrol (`min-h-9` + `py-2.5` sekitar 40px menjadi `h-9` 36px) dan padding sel tabel. Bagian lama 6-17 digeser menjadi 7-18.