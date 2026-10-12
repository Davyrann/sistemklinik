# Hak Akses Per Role

Referensi kapabilitas per role. Sumber desain: `DESIGN.md` §2. Fokus pengerjaan sekarang: **Resepsionis**.

## Status kode saat ini

- Role di DB: enum `users.role` = `resepsionis`, `dokter` (belum ada `admin`).
- Middleware: `role` alias → `App\Http\Middleware\EnsureRole`, dipakai di `routes/web.php`.
- Controller baru ada di namespace `App\Http\Controllers\Resepsionis`.
- Route `dokter.dashboard` sudah direferensikan di `routes/web.php` tapi belum dibuat.

## Matriks kapabilitas

| Modul | Admin | Resepsionis | Dokter | Apoteker | Kasir |
|---|---|---|---|---|---|
| Pasien (CRUD) | CRUD | CRUD | Read | Read | Read |
| Kunjungan (CRUD + status) | CRUD | CRUD (tanpa edit/update) | Read (antrean sendiri) + update status pemeriksaan | — | Read |
| Antrean (panggil/lewati/tunda) | Read | Update status | Update status | — | — |
| Rekam medis / anamnesis / diagnosis | Read | Read | CRUD | — | — |
| Tanda vital | Read | Read | Read | — | — |
| Resep (verifikasi & serah) | Read | Read | Tulis resep | CRUD verifikasi + serah | — |
| Obat / stok | CRUD | Read | Read | CRUD | Read |
| Tagihan | Read | Read + terima pembayaran + kuitansi | Read | Read | CRUD + bayar |
| Pembayaran / kas | Read | Terima bayar | — | — | CRUD |
| User & role | CRUD | — | — | — | — |
| Poli, dokter, tarif, jadwal | CRUD | Read | Read | Read | Read |
| Laporan & dashboard | Full | Dashboard operasional | Dashboard antrean | — | Dashboard kas |

Legenda: CRUD = create/read/update/delete; `—` = tidak ada akses.

## 1. Admin

- CRUD pasien.
- CRUD kunjungan, poli, dokter, tarif, jadwal.
- Manajemen user & role.
- Laporan penuh (keuangan, kinerja, stok).
- Belum ada di enum role → butuh migrasi tambah nilai `admin` + route group `role:admin`.

## 2. Resepsionis (prioritas sekarang)

Sudah jalan di `routes/web.php` (middleware `auth`, `role:resepsionis`):

| Aksi | Route | Controller |
|---|---|---|
| Dashboard | `GET /resepsionis` | `DashboardController` |
| CRUD pasien (auto `no_rm`) | `Route::resource('pasien')` | `PasienController` |
| Buat kunjungan + no antrean + tagihan awal | `kunjungan.create/store` | `KunjunganController` |
| Lihat kunjungan | `kunjungan.index/show` | `KunjunganController` |
| Ubah status kunjungan (menunggu → dipanggil → diperiksa → selesai/batal) | `PATCH kunjungan/{id}/status` | `KunjunganController::updateStatus` |
| Hapus kunjungan | `kunjungan.destroy` | `KunjunganController` |
| Daftar tagihan, detail, terima bayar, kuitansi | `tagihan.*` | `TagihanController` |
| Daftar & detail obat (read-only) | `obat.index/show` | `ObatController` |

Catatan:

- `kunjungan` sengaja tanpa `edit`/`update` (`.except(['edit','update'])`): data kunjungan diubah lewat ubah status saja.
- `destroy` pasien menghapus seluruh kunjungan terkait (hard delete).

Belum dikerjakan untuk resepsionis:

- Pembatalan pembayaran / void tagihan.
- Cetak antrean (view kuitansi sudah ada, antrean belum).

## 3. Dokter

- Lihat antrean sendiri, buka detail kunjungan pasien.
- Tulis anamnesis, diagnosis, tanda vital, resep (SOAP).
- Ubah status kunjungan ke `selesai`.
- Butuh: namespace `App\Http\Controllers\Dokter`, route group `role:dokter`, view `dokter/*`.

## 4. Apoteker

- Antrean resep, verifikasi & serah resep.
- CRUD obat, stok, peringatan interaksi/alergi.
- Butuh: `role:apoteker` + enum role.

## 5. Kasir

- Tagihan, pembayaran multi metode, kuitansi, laporan kas.
- Saat ini tumpang tindih dengan resepsionis (`tagihan.*`) → pisah route saat role `kasir` dibuat.

## Urutan pengerjaan

1. Resepsionis (selesai paling akhir di sprint ini): pasien ✓, kunjungan ✓, tagihan ✓, obat read ✓.
2. Admin: tambah enum role `admin`, route group `role:admin`, CRUD master data.
3. Dokter: namespace + route + view dokter.
4. Apoteker, Kasir.
