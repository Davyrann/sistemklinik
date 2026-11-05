<?php

use App\Models\Kunjungan;
use App\Models\Pasien;
use App\Models\User;
use Database\Seeders\KlinikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(KlinikSeeder::class);
});

test('resepsionis dashboard renders Template A layout with stat cards grid and quick actions', function () {
    $user = User::where('role', 'resepsionis')->first();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Dashboard Resepsionis');
    $response->assertSee('Daftar Pasien Baru');
    $response->assertSee('Buat Kunjungan');

    // Stat cards grid and labels
    $response->assertSee('grid-cols-2 lg:grid-cols-4 gap-4', false);
    $response->assertSee('Kunjungan Hari Ini');
    $response->assertSee('Menunggu / Dipanggil');
    $response->assertSee('Selesai Hari Ini');
    $response->assertSee('Pendapatan Hari Ini');

    // Antrean Hari Ini table container
    $response->assertSee('Antrean Hari Ini');
    $response->assertSee('No. Antrean');

    // Zero emojis check
    $content = $response->getContent();
    expect(preg_match('/[\x{1F300}-\x{1FAD6}\x{1F600}-\x{1F64F}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', $content))->toBe(0);
    // Anti-slop: zero decorative arrow characters in buttons
    expect($content)->not->toContain('→');
    expect($content)->not->toContain('←');
});

test('resepsionis pasien index renders Template A search toolbar and table container', function () {
    $user = User::where('role', 'resepsionis')->first();

    $response = $this->actingAs($user)->get(route('pasien.index'));

    $response->assertOk();
    $response->assertSee('Daftar Pasien');
    $response->assertSee('placeholder="Cari nama / No. RM / NIK"', false);
    $response->assertSee('No. RM');
    $response->assertSee('NIK / JK / Tgl Lahir');
    $response->assertSee('Penjamin');

    $content = $response->getContent();
    expect(preg_match('/[\x{1F300}-\x{1FAD6}\x{1F600}-\x{1F64F}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', $content))->toBe(0);
});

test('resepsionis pasien form renders Template B grouped layout with explicit (wajib) labels', function () {
    $user = User::where('role', 'resepsionis')->first();

    $response = $this->actingAs($user)->get(route('pasien.create'));

    $response->assertOk();
    $response->assertSee('Pendaftaran Pasien Baru');
    $response->assertSee('Data Pribadi');
    $response->assertSee('Kontak & Alamat', false);
    $response->assertSee('Alergi & Penjamin', false);

    // Required fields with (wajib) tag from x-input-label
    $response->assertSee('(wajib)');
    $response->assertSee('Nama Lengkap');
    $response->assertSee('Tanggal Lahir');
    $response->assertSee('Jenis Kelamin');
    $response->assertSee('Penjamin');

    // Datepicker attribute
    $response->assertSee('data-datepicker', false);

    // Batal and Simpan footer buttons
    $response->assertSee('Batal');
    $response->assertSee('Simpan Pasien');

    // Anti-slop verification: no asterisk in input label
    expect($response->getContent())->not->toContain('Nama Lengkap *');
    expect($response->getContent())->not->toContain('Tanggal Lahir *');
});

test('resepsionis pasien show renders structured card and allergy banner', function () {
    $user = User::where('role', 'resepsionis')->first();
    $pasien = Pasien::first();

    $response = $this->actingAs($user)->get(route('pasien.show', $pasien));

    $response->assertOk();
    $response->assertSee($pasien->nama);
    $response->assertSee($pasien->no_rm);
    $response->assertSee('Riwayat Kunjungan');
    $response->assertSee('Hapus Pasien');
});

test('resepsionis kunjungan form renders Template B grouped layout with required fields', function () {
    $user = User::where('role', 'resepsionis')->first();

    $response = $this->actingAs($user)->get(route('kunjungan.create'));

    $response->assertOk();
    $response->assertSee('Pendaftaran Kunjungan Baru');
    $response->assertSee('Identitas Pasien');
    $response->assertSee('Unit Layanan & Dokter', false);
    $response->assertSee('Waktu, Penjamin & Keluhan', false);

    // (wajib) labels
    $response->assertSee('(wajib)');
    $response->assertSee('Pasien');
    $response->assertSee('Poli Tujuan');
    $response->assertSee('Dokter Pemeriksa');
    $response->assertSee('Tanggal Kunjungan');

    // Buttons
    $response->assertSee('Batal');
    $response->assertSee('Simpan Kunjungan');

    expect($response->getContent())->not->toContain('Pasien *');
    expect($response->getContent())->not->toContain('Poli *');
});

test('resepsionis kunjungan index and show render cleanly with status pills', function () {
    $user = User::where('role', 'resepsionis')->first();
    $kunjungan = Kunjungan::first();

    // Index
    $responseIndex = $this->actingAs($user)->get(route('kunjungan.index'));
    $responseIndex->assertOk();
    $responseIndex->assertSee('Daftar Kunjungan');
    $responseIndex->assertSee('Status Antrean');
    $responseIndex->assertSee('Tanggal Kunjungan');

    // Show
    $responseShow = $this->actingAs($user)->get(route('kunjungan.show', $kunjungan));
    $responseShow->assertOk();
    $responseShow->assertSee($kunjungan->no_antrean);
    $responseShow->assertSee('Informasi Kunjungan & Status', false);
    $responseShow->assertSee('Ubah Status');
    $responseShow->assertSee('Hapus Kunjungan');
});
