<?php

use App\Models\Dokter;
use App\Models\Kunjungan;
use App\Models\Obat;
use App\Models\Pasien;
use App\Models\Tagihan;
use App\Models\User;
use Database\Seeders\KlinikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(KlinikSeeder::class);
});

test('guest cannot access resepsionis routes', function () {
    $this->get(route('pasien.index'))->assertRedirect(route('login'));
    $this->get(route('kunjungan.index'))->assertRedirect(route('login'));
});

test('dokter cannot access resepsionis routes', function () {
    $dokter = User::where('role', 'dokter')->first();
    $this->actingAs($dokter)->get(route('pasien.index'))->assertStatus(403);
});

test('resepsionis can CRUD pasien and global search auto-redirects', function () {
    $user = User::where('role', 'resepsionis')->first();
    $this->actingAs($user)->get(route('pasien.index'))->assertOk();

    $response = $this->actingAs($user)->post(route('pasien.store'), [
        'nama' => 'Pasien Tes',
        'tgl_lahir' => '1990-01-01',
        'jk' => 'L',
        'penjamin' => 'umum',
        'nik' => '3201990101900001',
    ]);
    $pasien = Pasien::where('nik', '3201990101900001')->first();
    expect($pasien)->not->toBeNull();
    $response->assertRedirect(route('pasien.show', $pasien));

    $this->actingAs($user)->get(route('pasien.index', ['q' => $pasien->no_rm]))->assertRedirect(route('pasien.show', $pasien));

    $this->actingAs($user)->put(route('pasien.update', $pasien), [
        'nama' => 'Pasien Tes Updated',
        'tgl_lahir' => '1990-01-01',
        'jk' => 'L',
        'penjamin' => 'bpjs',
        'nik' => '3201990101900001',
    ])->assertRedirect(route('pasien.show', $pasien));
    expect($pasien->fresh()->nama)->toBe('Pasien Tes Updated');

    $this->actingAs($user)->delete(route('pasien.destroy', $pasien))->assertRedirect(route('pasien.index'));
    expect(Pasien::find($pasien->id))->toBeNull();
});

test('resepsionis can create kunjungan and antrean is internal', function () {
    $user = User::where('role', 'resepsionis')->first();
    $pasien = Pasien::first();
    $dokter = Dokter::first();
    $this->actingAs($user)->post(route('kunjungan.store'), [
        'pasien_id' => $pasien->id,
        'poli_id' => $dokter->poli_id,
        'dokter_id' => $dokter->id,
        'tgl_kunjungan' => now()->toDateString(),
        'penjamin' => 'umum',
        'keluhan_utama' => 'Demam',
    ])->assertRedirect();
    $k = Kunjungan::latest('id')->first();
    expect($k->no_antrean)->not->toBeEmpty();
    expect($k->tagihan)->not->toBeNull();
});

test('resepsionis can view obat read-only and pay tagihan', function () {
    $user = User::where('role', 'resepsionis')->first();
    $this->actingAs($user)->get(route('obat.index'))->assertOk();
    $obat = Obat::first();
    $this->actingAs($user)->get(route('obat.show', $obat))->assertOk();

    $tagihan = Tagihan::where('status', 'belum')->first();
    $this->actingAs($user)->get(route('tagihan.index'))->assertOk();
    $this->actingAs($user)->post(route('tagihan.bayar', $tagihan), [
        'metode' => 'tunai',
        'jumlah' => $tagihan->total_bayar,
    ])->assertRedirect();
    expect($tagihan->fresh()->status)->toBe('lunas');
});
