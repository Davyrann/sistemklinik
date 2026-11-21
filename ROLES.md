# Hak Akses Per Role

Referensi kapabilitas per role. Sumber desain: `DESIGN.md` §2.

## Status Kode Saat Ini

- Role di DB: enum `users.role` = `admin`, `resepsionis`, `dokter`.
- Middleware: `role` alias → `App\Http\Middleware\EnsureRole`, dipakai di `routes/web.php` untuk membatasi akses per modul.
- Controller aktif:
  - `App\Http\Controllers\Admin`: `UserController`, `LogController`, `PoliController`.
  - `App\Http\Controllers\Resepsionis`: `DashboardController`, `PasienController`, `KunjunganController`, `TagihanController`, `ObatController`.
  - `App\Http\Controllers\Dokter`: `DashboardController`, `PemeriksaanController`.
- Seluruh antarmuka telah dimigrasikan ke standar **Mobile-First Responsive** dengan verifikasi otomatis via Pest (`DesignGateAuditTest`).

## Matriks Kapabilitas

| Modul | Admin | Resepsionis | Dokter | Apoteker (Backlog) | Kasir (Backlog) |
|---|---|---|---|---|---|
| Pasien (CRUD) | CRUD | CRUD | Read | Read | Read |
| Kunjungan (CRUD + status) | CRUD | CRUD (tanpa edit/update langsung) | Read (antrean poli sendiri) + update status periksa | — | Read |
| Antrean (panggil / periksa / selesai) | Read | Update status antrean | Panggil antrean & ubah status periksa | — | — |
| Rekam medis / SOAP / diagnosis ICD-10 | Read | Read | CRUD (dengan mekanisme lock) | — | — |
| Tanda vital & IMT | Read | Read | Input & kalkulasi otomatis | — | — |
| Resep obat (e-prescribing) | Read | Read | Tulis resep + auto tagihan | CRUD verifikasi & dispensing | — |
| Obat / stok | CRUD (via DB/Admin) | Read (katalog & stok) | Read (saat meresepkan) | CRUD inventaris & restock | Read |
| Tagihan & pembayaran kasir | Read | Terima bayar + cetak kuitansi | Read | Read | CRUD + void bayar |
| User & role | CRUD | — | — | — | — |
| Master poli | CRUD | Read | Read | Read | Read |
| Audit activity log | Read (filter & search) | — | — | — | — |
| Laporan & dashboard | Dashboard admin & audit log | Dashboard operasional antrean | Dashboard antrean poli | — | Dashboard rekap kas |

Legenda: CRUD = create/read/update/delete; `—` = tidak ada akses.

## 1. Admin (Selesai ✓)

- CRUD User (admin, dokter, resepsionis) dengan validasi role dan proteksi penghapusan akun sendiri.
- CRUD Master Data Poli (kode poli, nama poli, deskripsi, status aktif) terintegrasi penugasan dokter dan antrean kunjungan.
- Audit Activity Log HTTP: pencatatan otomatis request dengan filter role, status kode, dan pencarian URL/IP.

## 2. Resepsionis & Kasir (Selesai ✓)

- Dashboard operasional kunjungan hari ini.
- CRUD Pasien dengan generator otomatis format `no_rm` unik & pencarian cepat NIK/No. RM.
- Pembuatan kunjungan poli: otomatis generate nomor antrean unik per poli/tanggal dan tagihan biaya pendaftaran dasar.
- Manajemen siklus status kunjungan: `menunggu` → `dipanggil` → `diperiksa` → `selesai` / `batal`.
- Kasir & Pembayaran: rincian tagihan (registrasi + obat dari dokter), pembayaran (tunai/transfer/QRIS), dan cetak kuitansi resmi.
- Katalog obat read-only untuk pemantauan ketersediaan stok di loket pendaftaran.

## 3. Dokter (Selesai ✓)

- Dashboard antrean poli dokter: daftar pasien hari ini berdasarkan poli penugasan dengan indikator status antrean.
- Pemanggilan pasien antrean (`panggil`).
- Pemeriksaan klinis terpadu:
  - Input tanda vital (tensi, nadi, suhu, RR, SpO2, BB, TB) dengan kalkulasi otomatis IMT.
  - Form rekam medis format SOAP (Subjective, Objective, Assessment, Plan).
  - Diagnosis ICD-10 (kode + deskripsi, klasifikasi primer & sekunder).
  - E-prescribing: resep obat dengan jumlah, dosis, frekuensi, aturan pakai.
  - Mekanisme finalisasi & penguncian catatan medis (`is_locked` & `locked_at`).
  - Integrasi otomatis: pembuatan resep otomatis menambahkan item obat ke `tagihan_detail` kasir dan mengurangi stok obat saat pemeriksaan diselesaikan.
  - Akses riwayat kunjungan dan rekam medis lampau pasien (riwayat medis).

## 4. Rencana Pengembangan Selanjutnya (Backlog)

1. **Apoteker / Farmasi Khusus**:
   - Pemisahan alur dispensing resep: `draft` → `diproses` (peracikan/telaah) → `diserahkan` (penyerahan obat).
   - Verifikasi telaah klinis resep oleh farmasis sebelum obat diserahkan.
   - Manajemen inventaris obat (stok masuk/restock, supplier, tanggal kedaluwarsa, alert stok menipis).
2. **Fitur Tambahan Kasir**:
   - Cetak struk/tiket nomor antrean pasien saat pendaftaran selesai.
   - Mekanisme pembatalan / void pembayaran tagihan kasir dengan otorisasi supervisor.
   - Penambahan tarif tindakan medis non-konsultasi ke tagihan.
3. **Laporan & Ekspor**:
   - Laporan rekapitulasi kunjungan harian/bulanan.
   - Laporan rekonsiliasi kas kasir harian.
   - Laporan 10 besar penyakit (morbiditas ICD-10).
   - Laporan mutasi obat dan ekspor data (Excel/CSV/PDF).
