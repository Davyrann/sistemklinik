# DESIGN.md — Sistem Informasi Klinik Profesional

> Dokumen acuan desain produk, UX, dan design system untuk sistem manajemen klinik (pendaftaran, antrean, rekam medis elektronik, farmasi, kasir, dan laporan). Dokumen ini ditulis agar bisa dipakai oleh desainer, developer, maupun AI coding agent sebagai sumber kebenaran tunggal untuk tampilan dan perilaku antarmuka.

---

## 1. Ringkasan Produk

**Nama kerja:** KlinikKu
**Tujuan:** Membantu klinik pratama/rawat jalan melayani pasien lebih cepat, akurat, dan aman, dengan alur kerja yang jelas dari pendaftaran sampai pembayaran.

**Sasaran utama**
- Mengurangi waktu tunggu pasien dan antrean manual.
- Rekam medis elektronik (RME) yang lengkap, terstruktur, dan mudah dicari.
- Meminimalkan kesalahan input (alergi, dosis, identitas pasien).
- Laporan operasional dan keuangan yang real-time.
- Menjaga kerahasiaan data pasien.

**Bukan sasaran (v1):** rawat inap penuh, telemedicine video, integrasi alat laboratorium otomatis.

---

## 2. Pengguna & Peran (Role)

Role disimpan di kolom enum `users.role` dan dijaga middleware alias `role` (`App\Http\Middleware\EnsureRole`).

| Peran (nilai `role`) | Tugas utama | Status di kode | Kebutuhan UI |
|---|---|---|---|
| **resepsionis** | Daftar & CRUD pasien, buat kunjungan + nomor antrean, ubah status antrean, terima pembayaran & kuitansi, lihat data obat | Aktif. Route di `routes/web.php` grup `role:resepsionis`, controller `App\Http\Controllers\Resepsionis` | Form cepat, pencarian pasien instan, shortcut keyboard |
| **dokter** | Anamnesis, diagnosis, tanda vital, resep, status pemeriksaan | Aktif di enum; route `dokter.dashboard` sudah direferensikan, controller/view menyusul | Tampilan ringkas, riwayat pasien, template SOAP |

**Peran berikut belum ada di kode** — tugasnya saat ini dikerjakan oleh resepsionis, dan dipisah menjadi role sendiri pada tahap berikutnya:

| Peran rencana | Tugas utama | Kebutuhan UI |
|---|---|---|
| **Admin** | CRUD pasien & kunjungan, master data (poli, dokter, tarif), manajemen user & role, laporan penuh | Manajemen role, tabel master, laporan |
| **Apoteker** | Verifikasi dan serah resep, stok obat | Antrean resep, peringatan interaksi/alergi, stok |
| **Kasir** | Tagihan, pembayaran, kuitansi (saat ini digabung di resepsionis) | Rincian biaya jelas, banyak metode bayar |
| **Perawat / Bidan** | Tanda vital, triase, asuhan keperawatan | Input vital cepat, indikator nilai abnormal |
| **Manajer / Pemilik** | Pantau kinerja, laporan, pengaturan | Dashboard, grafik, ekspor |

Hak akses mengikuti prinsip **least privilege**: setiap peran hanya melihat data yang diperlukan. Rincian kapabilitas per role: `ROLES.md`.

---

## 3. Prinsip Desain

1. **Keamanan pasien lebih utama dari estetika.** Informasi kritis (alergi, golongan darah, kondisi darurat) selalu terlihat jelas dan tidak boleh tersembunyi.
2. **Cepat dipindai.** Staf klinik bekerja di bawah tekanan waktu; hierarki visual harus membuat informasi penting terbaca dalam hitungan detik.
3. **Tenang dan tepercaya.** Palet kalem, ruang putih cukup, tanpa elemen dekoratif yang mengganggu.
4. **Konsisten.** Pola yang sama untuk aksi yang sama di semua modul.
5. **Cegah kesalahan, bukan hanya laporkan.** Validasi inline, konfirmasi untuk aksi berisiko, dan nilai default yang aman.
6. **Inklusif.** Aksesibel untuk pengguna dengan keterbatasan penglihatan, motorik, dan perangkat lama.
7. **Efisien untuk pengguna rutin.** Keyboard-first, bulk action, dan pengisian otomatis dari data sebelumnya.

---

## 4. Identitas Visual

### 4.1 Warna

Palet berbasis teal (kesan medis, bersih, dan tenang) dengan warna semantik yang tegas.

**Brand**
| Token | Hex | Penggunaan |
|---|---|---|
| `--color-primary-50` | `#F0FDFA` | Latar highlight halus |
| `--color-primary-100` | `#CCFBF1` | Hover ringan, badge |
| `--color-primary-500` | `#14B8A6` | Aksen, ikon aktif |
| `--color-primary-600` | `#0D9488` | Tombol sekunder, link |
| `--color-primary-700` | `#0F766E` | **Tombol utama**, header aktif |
| `--color-primary-900` | `#134E4A` | Teks di atas latar primary muda |

**Netral (slate)**
| Token | Hex | Penggunaan |
|---|---|---|
| `--color-bg` | `#F8FAFC` | Latar halaman |
| `--color-surface` | `#FFFFFF` | Kartu, tabel, modal |
| `--color-border` | `#E2E8F0` | Garis pemisah, input |
| `--color-text-muted` | `#64748B` | Teks sekunder |
| `--color-text` | `#0F172A` | Teks utama |

**Semantik**
| Token | Hex | Makna |
|---|---|---|
| `--color-success` | `#15803D` | Selesai, normal, lunas |
| `--color-warning` | `#B45309` | Perlu perhatian, menunggu |
| `--color-danger` | `#B91C1C` | Alergi, nilai kritis, hapus |
| `--color-info` | `#1D4ED8` | Informasi netral |

**Aturan warna**
- Jangan mengandalkan warna saja: selalu sertakan ikon atau teks untuk status.
- Rasio kontras teks minimal **4.5:1** (teks besar 3:1), sesuai WCAG 2.1 AA.
- Merah hanya untuk bahaya/kritis; jangan dipakai sebagai dekorasi.
- Mode gelap bersifat opsional pada v1; bila ditambahkan, definisikan ulang semua token, bukan sekadar membalik warna.

### 4.2 Tipografi

- **Font utama:** Inter (fallback: `system-ui, -apple-system, "Segoe UI", Roboto, sans-serif`)
- **Font angka/kode (no. RM, kode obat):** `"JetBrains Mono", ui-monospace, monospace`
- Aktifkan `font-variant-numeric: tabular-nums` untuk kolom angka (harga, vital sign).

| Gaya | Ukuran / Line-height | Weight | Penggunaan |
|---|---|---|---|
| Display | 30 / 38 px | 700 | Judul halaman utama |
| H1 | 24 / 32 px | 600 | Judul halaman |
| H2 | 20 / 28 px | 600 | Judul seksi |
| H3 | 16 / 24 px | 600 | Judul kartu |
| Body | 14 / 22 px | 400 | Teks umum, tabel |
| Body-lg | 16 / 24 px | 400 | Form, konten baca |
| Caption | 12 / 16 px | 500 | Label, metadata |

Ukuran teks minimum antarmuka: **12 px**; teks input minimum **14 px** (16 px di mobile agar tidak zoom otomatis).

### 4.3 Spasi, Radius, Bayangan

- **Skala spasi (basis 4 px):** 4, 8, 12, 16, 24, 32, 48, 64.
- **Radius:** `sm 6px` (input, badge), `md 8px` (tombol, kartu), `lg 12px` (modal), `full` (avatar, pill).
- **Bayangan:**
  - `shadow-sm`: `0 1px 2px rgba(15,23,42,.06)` — kartu
  - `shadow-md`: `0 4px 12px rgba(15,23,42,.10)` — dropdown, popover
  - `shadow-lg`: `0 12px 32px rgba(15,23,42,.16)` — modal
- Gunakan border tipis + bayangan ringan; hindari bayangan tebal.

### 4.4 Ikonografi

- Set ikon garis (stroke 1.5–2 px), misalnya Lucide atau Heroicons Outline.
- Ukuran standar 16 / 20 / 24 px; ikon selalu diberi label teks atau `aria-label`.

---

## 5. Layout & Navigasi

### 5.1 Kerangka Aplikasi

```
┌──────────────────────────────────────────────────────┐
│ Topbar: pencarian pasien global · notifikasi · profil │
├───────────┬──────────────────────────────────────────┤
│ Sidebar   │  Breadcrumb + Judul Halaman + Aksi Utama │
│ (modul)   │  ──────────────────────────────────────  │
│           │  Konten (kartu / tabel / form)           │
│           │                                          │
└───────────┴──────────────────────────────────────────┘
```

- **Sidebar** lebar 248 px (ciut ke 72 px); menu dikelompokkan: *Operasional*, *Klinis*, *Penunjang*, *Keuangan*, *Pengaturan*. Menu yang tidak boleh diakses peran tertentu disembunyikan.
- **Topbar** memuat pencarian global (`Ctrl/Cmd + K`) untuk nama, No. RM, atau NIK.
- **Konten** maksimum lebar 1280 px pada layar besar; padding 24 px.

### 5.2 Breakpoint

| Nama | Lebar | Perilaku |
|---|---|---|
| `sm` | ≥ 640 px | Satu kolom, sidebar jadi drawer |
| `md` | ≥ 768 px | Tablet: dua kolom, sidebar ciut |
| `lg` | ≥ 1024 px | Desktop: sidebar penuh |
| `xl` | ≥ 1280 px | Panel samping detail pasien |

Prioritas perangkat: **desktop/laptop** (pendaftaran, pembayaran, dokter) dan **tablet** (perawat di ruang periksa). Mobile mendapat pengalaman dasar (lihat antrean, notifikasi).

### 5.3 Banner Pasien (Patient Header)

Ditampilkan permanen (sticky) di semua halaman klinis pasien:

- Foto/inisial, **nama**, usia & jenis kelamin, **No. RM**
- **Chip alergi** berwarna merah (jika ada), golongan darah
- Status kunjungan aktif dan penjamin (Umum/BPJS/Asuransi)

Tujuannya: mengurangi risiko salah pasien dan salah obat.

---

## 6. Komponen UI

### 6.1 Tombol
| Varian | Gaya | Contoh |
|---|---|---|
| Primary | Latar `primary-700`, teks putih | Simpan, Daftarkan |
| Secondary | Border `primary-700`, latar putih | Batal, Kembali |
| Ghost | Tanpa border | Aksi tersier |
| Danger | Latar `danger` | Hapus, Batalkan kunjungan |

- Tinggi: 36 px (default), 44 px (touch/tablet). Target sentuh minimal **44×44 px**.
- Satu tombol primary per area. Tampilkan *loading state* dan nonaktifkan saat proses.

### 6.2 Form
- Label di atas input, selalu terlihat (bukan hanya placeholder).
- Tanda wajib: `*` + teks bantu bila perlu.
- Validasi **inline** saat blur; pesan spesifik ("NIK harus 16 digit", bukan "Input salah").
- Kelompokkan field berdasarkan konteks (Identitas, Kontak, Penjamin).
- Dukungan: autocomplete wilayah, penanda alergi, input tanggal lahir dengan masker.
- Simpan draf otomatis pada form panjang (anamnesis, SOAP).

### 6.3 Tabel & Daftar
- Header sticky, baris tinggi 48 px, zebra opsional.
- Kolom bisa diurutkan; filter di atas tabel; pencarian dengan debounce.
- Aksi per baris di kolom terakhir (menu `⋯`); aksi massal muncul saat baris dipilih.
- Paginasi atau infinite scroll untuk > 50 data; tampilkan total hasil.
- *Empty state* informatif dengan ajakan aksi.

### 6.4 Status Badge

| Status kunjungan | Warna | Ikon |
|---|---|---|
| Menunggu | Warning | jam |
| Dipanggil | Info | megafon |
| Sedang diperiksa | Primary | stetoskop |
| Menunggu obat | Warning | pil |
| Menunggu bayar | Warning | dompet |
| Selesai | Success | centang |
| Batal | Netral/Danger | silang |

### 6.5 Umpan Balik
- **Toast** (4–6 detik) untuk keberhasilan non-kritis.
- **Alert inline** untuk peringatan di dalam konteks (mis. interaksi obat).
- **Modal konfirmasi** untuk aksi tidak dapat dibatalkan; sebutkan akibatnya secara eksplisit ("Hapus resep untuk Budi S.?").
- **Skeleton** untuk pemuatan > 300 ms; hindari spinner layar penuh.

### 6.6 Komponen Khusus Klinik
- **Vital Sign Input:** TD, nadi, suhu, RR, SpO₂, BB/TB (otomatis hitung IMT); nilai di luar rentang ditandai.
- **SOAP Editor:** empat seksi (Subjective, Objective, Assessment, Plan) dengan template dan teks cepat.
- **ICD-10 / ICD-9-CM Picker:** pencarian dengan kode + deskripsi, favorit per dokter.
- **Drug Picker:** nama generik/dagang, kekuatan, stok tersedia, peringatan alergi & interaksi.
- **Antrean Board:** kartu per pasien dengan nomor, poli, waktu tunggu.
- **Timeline Riwayat:** kunjungan, diagnosis, dan resep secara kronologis.

---

## 7. Modul & Layar Utama

### 7.1 Dashboard
- Kartu ringkasan: kunjungan hari ini, pasien menunggu, rata-rata waktu tunggu, pendapatan hari ini.
- Grafik kunjungan 7/30 hari, diagnosis terbanyak, stok obat menipis.
- Dashboard disesuaikan per peran (dokter melihat antrean sendiri; resepsionis melihat tagihan tertunda).

### 7.2 Pendaftaran Pasien
**Alur:** Cari pasien → (jika baru) isi data → pilih poli & dokter → pilih penjamin → cetak/kirim nomor antrean.
- Pencarian dulu untuk mencegah duplikasi pasien (nama + tanggal lahir + NIK).
- Deteksi duplikat dengan peringatan dan opsi gabung.
- Pasien lama: satu klik "Buat Kunjungan Baru".

### 7.3 Antrean
- Papan per poli dengan status real-time.
- Aksi: panggil, lewati, tunda, alihkan dokter.
- Opsional: layar TV antrean dan notifikasi WhatsApp/SMS.

### 7.4 Pemeriksaan (Rekam Medis)
Urutan tab: **Ringkasan · Tanda Vital · Anamnesis (S) · Pemeriksaan (O) · Diagnosis (A) · Rencana & Tindakan (P) · Resep · Lampiran**.
- Riwayat penyakit, alergi, dan obat rutin tampil di panel samping.
- Tombol "Salin dari kunjungan sebelumnya" untuk kasus kontrol.
- Pengunci rekam: setelah selesai, perubahan harus melalui revisi dengan jejak audit (tidak menimpa langsung).

### 7.5 Farmasi
- Antrean resep → verifikasi → racik/siapkan → etiket → serah ke pasien.
- Peringatan: alergi, interaksi, duplikasi terapi, dosis di luar rentang.
- Manajemen stok: batch, kedaluwarsa (penanda 90/30 hari), stok minimum, penerimaan, opname.

### 7.6 Kasir
- Rincian biaya: jasa medis, tindakan, obat, administrasi.
- Metode bayar: tunai, transfer, QRIS, kartu, penjamin.
- Diskon/penyesuaian memerlukan alasan dan tercatat di audit log.
- Cetak/kirim kuitansi dan rekap shift.

### 7.7 Laporan
- Kunjungan, 10 penyakit terbanyak, pendapatan, stok, kinerja dokter.
- Filter periode, ekspor CSV/XLSX/PDF.
- Laporan wajib/eksternal dapat disiapkan sesuai kebutuhan regulasi setempat.

### 7.8 Pengaturan
Pengguna & peran, poli, jadwal dokter, tarif, formularium obat, template dokumen, identitas klinik, integrasi.

---

## 8. Keamanan, Privasi & Kepatuhan

> Catatan: bagian ini panduan desain, bukan nasihat hukum. Verifikasi dengan konsultan hukum/regulator untuk klinik Anda.

- **Autentikasi:** kata sandi kuat + opsi 2FA; sesi otomatis berakhir saat tidak aktif (mis. 15 menit) dengan layar kunci.
- **Otorisasi:** RBAC per modul dan aksi; akses data pasien dicatat.
- **Audit log:** siapa, kapan, apa yang dilihat/diubah (tidak bisa dihapus).
- **Enkripsi:** TLS saat transit, enkripsi data sensitif saat disimpan; cadangan terenkripsi dan diuji pemulihannya.
- **Masking data:** NIK dan kontak dimasking secara default (`3201••••••••0004`), buka dengan aksi sengaja.
- **Persetujuan pasien:** *consent* untuk pengolahan data dan tindakan dicatat.
- **Rujukan regulasi (Indonesia) untuk ditinjau:** UU No. 27 Tahun 2022 tentang Pelindungan Data Pribadi; Permenkes No. 24 Tahun 2022 tentang Rekam Medis (termasuk ketentuan penyimpanan dan kerahasiaan); integrasi SATUSEHAT dan BPJS (P-Care) bila relevan dengan jenis fasilitas.

---

## 9. Aksesibilitas

- Target **WCAG 2.1 AA**.
- Semua fungsi dapat dioperasikan lewat keyboard; urutan fokus logis; indikator fokus terlihat (outline 2 px `primary-600` dengan offset).
- Gunakan elemen semantik, `aria-label`, `aria-live` untuk toast dan perubahan status antrean.
- Pesan galat terhubung ke field (`aria-describedby`).
- Hormati `prefers-reduced-motion`; animasi 120–200 ms, hanya untuk memperjelas perubahan.
- Dukung zoom 200% tanpa kehilangan fungsi.

**Pintasan keyboard yang disarankan**
| Pintasan | Aksi |
|---|---|
| `Ctrl/Cmd + K` | Pencarian global |
| `Ctrl/Cmd + S` | Simpan |
| `Alt + N` | Pasien/kunjungan baru |
| `Esc` | Tutup modal/panel |
| `?` | Daftar pintasan |

---

## 10. Konten & Bahasa (UX Writing)

- Bahasa Indonesia baku, ringkas, dan netral; gunakan istilah medis sesuai standar, sertakan awam bila perlu.
- Tombol berupa kata kerja: "Simpan Pemeriksaan", "Cetak Kuitansi".
- Pesan galat: apa yang salah + cara memperbaikinya.
- Format: tanggal `DD MMM YYYY`, waktu 24 jam, mata uang `Rp 1.250.000`, satuan klinis konsisten (mmHg, °C, kg, cm).
- Hindari singkatan ambigu pada area kritis (mis. dosis).

---

## 11. Rekomendasi Teknis (Opsional)

**Front-end:** Tailwind CSS + komponen (DaisyUI/shadcn/ui) atau framework setara; token desain di atas dijadikan konfigurasi tema.
**Back-end:** Django / Laravel / Node.js dengan PostgreSQL; REST atau GraphQL; antrean pekerjaan untuk notifikasi dan cetak.
**Contoh token (CSS):**

```css
:root {
  --color-primary-700: #0F766E;
  --color-primary-600: #0D9488;
  --color-bg: #F8FAFC;
  --color-surface: #FFFFFF;
  --color-border: #E2E8F0;
  --color-text: #0F172A;
  --color-text-muted: #64748B;
  --color-success: #15803D;
  --color-warning: #B45309;
  --color-danger: #B91C1C;
  --color-info: #1D4ED8;

  --radius-sm: 6px;
  --radius-md: 8px;
  --radius-lg: 12px;
  --shadow-sm: 0 1px 2px rgba(15, 23, 42, .06);
  --font-sans: Inter, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
}
```

**Model data inti:** `Pasien`, `Kunjungan`, `Antrean`, `PemeriksaanFisik/TandaVital`, `RekamMedis(SOAP)`, `Diagnosis`, `Tindakan`, `Resep`, `Obat`, `StokBatch`, `Tagihan`, `Pembayaran`, `Pengguna`, `Peran`, `AuditLog`.

---

## 12. Do & Don't

| ✅ Lakukan | ❌ Hindari |
|---|---|
| Tampilkan alergi di setiap layar klinis | Menyembunyikan data kritis di balik tab |
| Konfirmasi aksi tak dapat dibatalkan | Hapus permanen tanpa jejak |
| Validasi inline dan pesan spesifik | Pesan galat generik |
| Gunakan ikon + teks + warna untuk status | Status hanya dengan warna |
| Jaga satu aksi primer per layar | Banyak tombol berwarna mencolok |
| Catat setiap akses dan perubahan data | Berbagi akun antarstaf |

---

## 13. Kriteria Keberhasilan

- Pendaftaran pasien lama ≤ **30 detik**; pasien baru ≤ **2 menit**.
- Pengurangan waktu tunggu rata-rata terukur setelah 3 bulan.
- Kesalahan input kritis (alergi/dosis) turun terukur.
- Skor kegunaan (SUS) ≥ **80** dari staf klinik.
- Tidak ada pelanggaran akses data pada audit berkala.

---

## 14. Roadmap Singkat

1. **MVP:** pendaftaran, antrean, RME dasar (SOAP), resep, kasir, dashboard sederhana.
2. **v1.1:** farmasi & stok lengkap, laporan, audit log, 2FA.
3. **v1.2:** notifikasi WhatsApp, layar antrean TV, janji temu online.
4. **v2:** integrasi SATUSEHAT/BPJS, portal pasien, analitik lanjutan.

---

*Versi dokumen: 1.0 — sesuaikan nama brand, warna, dan kebijakan lokal dengan kebutuhan klinik Anda.*
