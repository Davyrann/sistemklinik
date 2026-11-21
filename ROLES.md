# Hak Akses Per Role

Referensi kapabilitas per role. Sumber desain: `DESIGN.md` §2.

## Status Kode Saat Ini

- Role di DB: enum `users.role` = `admin`, `resepsionis`, `dokter`, `apoteker`.
- Middleware: `role` alias → `App\Http\Middleware\EnsureRole`, dipakai di `routes/web.php` untuk membatasi akses per modul.
- Controller aktif:
  - `App\Http\Controllers\Admin`: `UserController`, `LogController`, `PoliController`.
  - `App\Http\Controllers\Resepsionis`: `DashboardController`, `PasienController`, `KunjunganController`, `TagihanController`, `ObatController`.
  - `App\Http\Controllers\Dokter`: `DashboardController`, `PemeriksaanController`.
  - `App\Http\Controllers\Apoteker`: `DashboardController`, `ResepController`, `ObatController`.
- Seluruh antarmuka telah dimigrasikan ke standar **Mobile-First Responsive** dengan verifikasi otomatis via Pest (`DesignGateAuditTest`).

## Matriks Kapabilitas

| Modul | Admin | Resepsionis | Dokter | Apoteker & Kasir |
|---|---|---|---|---|
| Pasien (CRUD) | CRUD | CRUD | Read | Read |
| Kunjungan (CRUD + status) | CRUD | CRUD (tanpa edit/update langsung) | Read (antrean poli sendiri) + update status periksa | Read |
| Antrean (panggil / periksa / selesai) | Read | Update status antrean | Panggil antrean & ubah status periksa | Read |
| Rekam medis / SOAP / diagnosis ICD-10 | Read | Read | CRUD (dengan mekanisme lock) | Read |
| Tanda vital & IMT | Read | Read | Input & kalkulasi otomatis | Read |
| Resep obat (e-prescribing & dispensing) | Read | Read | Tulis resep + auto tagihan | CRUD verifikasi, dispensing & etiket |
| Obat / stok | CRUD (via DB/Admin) | Read (katalog & stok) | Read (saat meresepkan) | CRUD inventaris, restock & koreksi |
| Tagihan & pembayaran kasir | Read | Terima bayar + cetak kuitansi | Read | CRUD terima bayar + cetak kuitansi |
| User & role | CRUD | — | — | — |
| Master poli | CRUD | Read | Read | Read |
| Audit activity log | Read (filter & search) | — | — | — |
| Laporan & dashboard | Dashboard admin & audit log | Dashboard operasional antrean | Dashboard antrean poli | Dashboard farmasi, resep & kasir |

Legenda: CRUD = create/read/update/delete; `—` = tidak ada akses.

## 1. Admin (Selesai ✓)

- CRUD User (admin, dokter, resepsionis, apoteker) dengan validasi role dan proteksi penghapusan akun sendiri.
- CRUD Master Data Poli (kode poli, nama poli, deskripsi, status aktif) terintegrasi penugasan dokter dan antrean kunjungan.
- Audit Activity Log HTTP: pencatatan otomatis request dengan filter role, status kode, dan pencarian URL/IP.

## 2. Resepsionis (Selesai ✓)

- Dashboard operasional kunjungan hari ini.
- CRUD Pasien dengan generator otomatis format `no_rm` unik & pencarian cepat NIK/No. RM.
- Pembuatan kunjungan poli: otomatis generate nomor antrean unik per poli/tanggal dan tagihan biaya pendaftaran dasar.
- Manajemen siklus status kunjungan: `menunggu` → `dipanggil` → `diperiksa` → `selesai` / `batal`.
- Monitoring katalog obat & stok read-only.

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

## 4. Apoteker & Kasir (Selesai ✓)

- **Dashboard Farmasi & Kasir**: Stat card jujur (resep menunggu, resep diserahkan hari ini, tagihan belum lunas, obat menipis/habis), kartu keputusan resep menunggu terlama, tabel resep masuk, dan ringkasan tagihan kasir.
- **Antrean Resep Masuk**: Daftar resep terurut terlama menunggu di posisi teratas, filter status (`menunggu`, `diproses`, `diserahkan`), dan pencarian pasien/RM/dokter.
- **Verifikasi & Telaah Resep**: Halaman verifikasi dengan Patient Header, 3-state Allergy Banner, indikator stok gudang, dan deteksi otomatis bahaya alergi pasien terhadap obat yang diresepkan.
- **Alur Dispensing**: Siklus perubahan status resep (`menunggu`/`draft` → `diproses`/disiapkan → `diserahkan`).
- **Cetak Etiket Obat**: Cetak label/etiket monokrom resmi untuk bungkus obat (nama pasien, No. RM, dosis, frekuensi, aturan pakai, paraf farmasis).
- **Kasir & Penagihan Terpadu**: Akses penuh modul kasir (`tagihan.*`), pembayaran langsung dari halaman resep maupun menu kasir (tunai/transfer/QRIS), dan cetak kuitansi.
- **Inventaris & Penyesuaian Stok**: Monitoring stok obat, filter menipis/habis, serta aksi penyesuaian stok masuk (restock), pengurangan, atau koreksi dengan alasan wajib tercatat.

## 5. Rencana Pengembangan Selanjutnya (Backlog)

1. **Fitur Pelengkap Kasir**:
   - Cetak struk/tiket nomor antrean pasien saat pendaftaran selesai.
   - Mekanisme pembatalan / void pembayaran tagihan kasir dengan otorisasi supervisor.
   - Penambahan tarif tindakan medis non-konsultasi ke tagihan.
2. **Laporan & Ekspor**:
   - Laporan rekapitulasi kunjungan harian/bulanan.
   - Laporan rekonsiliasi kas kasir harian (tunai vs transfer vs QRIS).
   - Laporan 10 besar penyakit (morbiditas ICD-10).
   - Laporan mutasi stok dan pemakaian obat.
   - Ekspor data laporan ke format Excel/CSV & PDF.
3. **Integrasi Eksternal**:
   - Bridging API BPJS Kesehatan (PCare).
   - Bridging platform Kemenkes SATUSEHAT (FHIR).
