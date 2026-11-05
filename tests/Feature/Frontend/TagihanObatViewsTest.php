<?php

use App\Models\Dokter;
use App\Models\Kunjungan;
use App\Models\Obat;
use App\Models\Pasien;
use App\Models\Pembayaran;
use App\Models\Poli;
use App\Models\Tagihan;
use App\Models\TagihanDetail;
use App\Models\User;
use Database\Seeders\KlinikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(KlinikSeeder::class);
    $this->resepsionis = User::where('role', 'resepsionis')->first();
});

test('tagihan index renders with table container, mono numbers, and status pills', function () {
    $pasien = Pasien::first();
    $dokter = Dokter::first();
    $poli = Poli::first();

    $kunjungan = Kunjungan::create([
        'pasien_id' => $pasien->id,
        'dokter_id' => $dokter->id,
        'poli_id' => $poli->id,
        'tgl_kunjungan' => now(),
        'no_antrean' => 'A-099',
        'status' => 'selesai',
        'penjamin' => 'umum',
    ]);

    $tagihan = Tagihan::create([
        'kunjungan_id' => $kunjungan->id,
        'total' => 150000,
        'diskon' => 0,
        'total_bayar' => 150000,
        'status' => 'belum',
    ]);

    $response = $this->actingAs($this->resepsionis)->get(route('tagihan.index'));

    $response->assertOk()
        ->assertSee('Daftar Tagihan')
        ->assertSee('table-container')
        ->assertSee('#'.str_pad($tagihan->id, 5, '0', STR_PAD_LEFT))
        ->assertSee($pasien->nama)
        ->assertSee($pasien->no_rm)
        ->assertSee('Rp 150.000')
        ->assertSee('Belum Lunas');
});

test('tagihan show renders patient header, itemized bill, and payment form', function () {
    $pasien = Pasien::first();
    $dokter = Dokter::first();
    $poli = Poli::first();

    $kunjungan = Kunjungan::create([
        'pasien_id' => $pasien->id,
        'dokter_id' => $dokter->id,
        'poli_id' => $poli->id,
        'tgl_kunjungan' => now(),
        'no_antrean' => 'B-042',
        'status' => 'diperiksa',
        'penjamin' => 'umum',
    ]);

    $tagihan = Tagihan::create([
        'kunjungan_id' => $kunjungan->id,
        'total' => 200000,
        'diskon' => 0,
        'total_bayar' => 200000,
        'status' => 'belum',
    ]);

    TagihanDetail::create([
        'tagihan_id' => $tagihan->id,
        'deskripsi' => 'Konsultasi Dokter Umum',
        'qty' => 1,
        'harga_satuan' => 100000,
        'subtotal' => 100000,
        'jenis' => 'jasa',
    ]);

    TagihanDetail::create([
        'tagihan_id' => $tagihan->id,
        'deskripsi' => 'Amoxicillin 500mg',
        'qty' => 10,
        'harga_satuan' => 10000,
        'subtotal' => 100000,
        'jenis' => 'obat',
    ]);

    $response = $this->actingAs($this->resepsionis)->get(route('tagihan.show', $tagihan));

    $response->assertOk()
        ->assertSee('Identitas Pasien')
        ->assertSee($pasien->nama)
        ->assertSee($pasien->no_rm)
        ->assertSee('Konsultasi Dokter Umum')
        ->assertSee('Amoxicillin 500mg')
        ->assertSee('Rp 200.000')
        ->assertSee('Terima Pembayaran');
});

test('tagihan kuitansi renders monochrome printable layout without buttons in media print', function () {
    $pasien = Pasien::first();
    $dokter = Dokter::first();
    $poli = Poli::first();

    $kunjungan = Kunjungan::create([
        'pasien_id' => $pasien->id,
        'dokter_id' => $dokter->id,
        'poli_id' => $poli->id,
        'tgl_kunjungan' => now(),
        'no_antrean' => 'K-001',
        'status' => 'selesai',
        'penjamin' => 'umum',
    ]);

    $tagihan = Tagihan::create([
        'kunjungan_id' => $kunjungan->id,
        'total' => 75000,
        'diskon' => 0,
        'total_bayar' => 75000,
        'status' => 'lunas',
    ]);

    TagihanDetail::create([
        'tagihan_id' => $tagihan->id,
        'deskripsi' => 'Biaya Administrasi Rawat Jalan',
        'qty' => 1,
        'harga_satuan' => 75000,
        'subtotal' => 75000,
        'jenis' => 'admin',
    ]);

    Pembayaran::create([
        'tagihan_id' => $tagihan->id,
        'metode' => 'qris',
        'jumlah' => 75000,
        'tgl_bayar' => now(),
        'kasir_id' => $this->resepsionis->id,
    ]);

    $response = $this->actingAs($this->resepsionis)->get(route('tagihan.kuitansi', $tagihan));

    $response->assertOk()
        ->assertSee('KUITANSI PEMBAYARAN')
        ->assertSee('Klinik Pratama Rawat Jalan')
        ->assertSee($pasien->nama)
        ->assertSee($pasien->no_rm)
        ->assertSee('Rp 75.000')
        ->assertSee('QRIS')
        ->assertSee('@media print', false)
        ->assertSee('display: none !important', false);
});

test('obat index renders with table container and stock status pills', function () {
    $obatAman = Obat::create([
        'kode' => 'OBT-AMAN',
        'nama_generik' => 'Paracetamol 500mg',
        'nama_dagang' => 'Panadol',
        'satuan' => 'tablet',
        'stok' => 100,
        'stok_minimum' => 10,
        'harga' => 5000,
        'is_aktif' => true,
    ]);

    $obatMenipis = Obat::create([
        'kode' => 'OBT-TIPIS',
        'nama_generik' => 'Ibuprofen 400mg',
        'nama_dagang' => 'Proris',
        'satuan' => 'tablet',
        'stok' => 5,
        'stok_minimum' => 10,
        'harga' => 8000,
        'is_aktif' => true,
    ]);

    $obatHabis = Obat::create([
        'kode' => 'OBT-HABIS',
        'nama_generik' => 'Cefadroxil 500mg',
        'nama_dagang' => 'Cefat',
        'satuan' => 'kapsul',
        'stok' => 0,
        'stok_minimum' => 15,
        'harga' => 15000,
        'is_aktif' => true,
    ]);

    $response = $this->actingAs($this->resepsionis)->get(route('obat.index'));

    $response->assertOk()
        ->assertSee('Daftar Stok Obat')
        ->assertSee('table-container')
        ->assertSee('Paracetamol 500mg')
        ->assertSee('Tersedia')
        ->assertSee('Ibuprofen 400mg')
        ->assertSee('Menipis')
        ->assertSee('Cefadroxil 500mg')
        ->assertSee('Habis');
});

test('obat show renders medicine details and stock threshold', function () {
    $obat = Obat::create([
        'kode' => 'OBT-SHOW',
        'nama_generik' => 'Amoxicillin Trihydrate',
        'nama_dagang' => 'Amoxil',
        'satuan' => 'kaplet',
        'stok' => 25,
        'stok_minimum' => 10,
        'harga' => 12000,
        'is_aktif' => true,
    ]);

    $response = $this->actingAs($this->resepsionis)->get(route('obat.show', $obat));

    $response->assertOk()
        ->assertSee('Amoxicillin Trihydrate')
        ->assertSee('OBT-SHOW')
        ->assertSee('Amoxil')
        ->assertSee('kaplet')
        ->assertSee('25')
        ->assertSee('10')
        ->assertSee('Rp 12.000')
        ->assertSee('Pengawasan Batch & Inventaris', false);
});
