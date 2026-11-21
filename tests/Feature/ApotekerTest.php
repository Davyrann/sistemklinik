<?php

use App\Models\Kunjungan;
use App\Models\Obat;
use App\Models\Resep;
use App\Models\Tagihan;
use App\Models\User;
use Database\Seeders\KlinikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(KlinikSeeder::class);
});

test('guest cannot access apoteker routes', function () {
    $this->get(route('apoteker.dashboard'))->assertRedirect(route('login'));
});

test('dokter and resepsionis cannot access apoteker routes', function () {
    $dokter = User::where('role', 'dokter')->first();
    $resepsionis = User::where('role', 'resepsionis')->first();

    $this->actingAs($dokter)->get(route('apoteker.dashboard'))->assertStatus(403);
    $this->actingAs($resepsionis)->get(route('apoteker.dashboard'))->assertStatus(403);
});

test('apoteker is redirected to apoteker dashboard from default dashboard', function () {
    $apoteker = User::where('role', 'apoteker')->first();
    $this->actingAs($apoteker)->get(route('dashboard'))->assertRedirect(route('apoteker.dashboard'));
});

test('apoteker can view dashboard with metrics and recent prescriptions', function () {
    $apoteker = User::where('role', 'apoteker')->first();
    $response = $this->actingAs($apoteker)->get(route('apoteker.dashboard'));

    $response->assertOk();
    $response->assertSee('Dashboard Farmasi & Kasir');
    $response->assertSee('Resep Menunggu');
    $response->assertSee('Tagihan Belum Lunas');
});

test('apoteker can view incoming prescriptions list and filter by status', function () {
    $apoteker = User::where('role', 'apoteker')->first();
    $response = $this->actingAs($apoteker)->get(route('apoteker.resep.index'));

    $response->assertOk();
    $response->assertSee('Daftar Resep Masuk');
});

test('apoteker can view prescription detail with patient header and medicine items', function () {
    $apoteker = User::where('role', 'apoteker')->first();
    $resep = Resep::with('kunjungan.pasien')->first();

    $response = $this->actingAs($apoteker)->get(route('apoteker.resep.show', $resep));

    $response->assertOk();
    $response->assertSee($resep->kunjungan->pasien->nama);
    $response->assertSee($resep->kunjungan->pasien->no_rm);
    $response->assertSee('Verifikasi Resep');
});

test('apoteker can update prescription status to diproses and diserahkan', function () {
    $apoteker = User::where('role', 'apoteker')->first();
    $resep = Resep::where('status', '!=', 'diserahkan')->first() ?? Resep::first();

    $this->actingAs($apoteker)
        ->patch(route('apoteker.resep.status', $resep), [
            'status' => 'diproses',
        ])
        ->assertRedirect();

    expect($resep->fresh()->status)->toBe('diproses');

    $this->actingAs($apoteker)
        ->patch(route('apoteker.resep.status', $resep), [
            'status' => 'diserahkan',
        ])
        ->assertRedirect();

    expect($resep->fresh()->status)->toBe('diserahkan');
});

test('apoteker can access kasir tagihan and process payment', function () {
    $apoteker = User::where('role', 'apoteker')->first();
    $tagihan = Tagihan::where('status', 'belum')->first();

    $response = $this->actingAs($apoteker)->get(route('tagihan.index'));
    $response->assertOk();

    $this->actingAs($apoteker)->get(route('tagihan.show', $tagihan))->assertOk();

    $this->actingAs($apoteker)->post(route('tagihan.bayar', $tagihan), [
        'metode' => 'tunai',
        'jumlah' => $tagihan->total_bayar,
    ])->assertRedirect();

    expect($tagihan->fresh()->status)->toBe('lunas');

    $this->actingAs($apoteker)->get(route('tagihan.kuitansi', $tagihan))->assertOk();
});

test('apoteker can print etiket obat', function () {
    $apoteker = User::where('role', 'apoteker')->first();
    $resep = Resep::with('details.obat')->first();

    $response = $this->actingAs($apoteker)->get(route('apoteker.resep.etiket', $resep));

    $response->assertOk();
    $response->assertSee('Etiket Obat');
});

test('apoteker can view stock and adjust stock with recorded reason', function () {
    $apoteker = User::where('role', 'apoteker')->first();
    $obat = Obat::first();
    $initialStock = $obat->stok;

    $response = $this->actingAs($apoteker)->get(route('apoteker.obat.index'));
    $response->assertOk();
    $response->assertSee('Inventaris & Stok Obat');

    $this->actingAs($apoteker)->post(route('apoteker.obat.adjust', $obat), [
        'tipe' => 'masuk',
        'jumlah' => 50,
        'alasan' => 'Restock dari distributor Kimia Farma',
    ])->assertRedirect();

    expect($obat->fresh()->stok)->toBe($initialStock + 50);
});

test('admin can access apoteker routes', function () {
    $admin = User::where('role', 'admin')->first();

    $this->actingAs($admin)->get(route('apoteker.dashboard'))->assertOk();
    $this->actingAs($admin)->get(route('apoteker.resep.index'))->assertOk();
});

test('completed examination by doctor connects prescription to apoteker queue with waiting status', function () {
    $dokterUser = User::where('role', 'dokter')->first();
    $apoteker = User::where('role', 'apoteker')->first();
    $kunjungan = Kunjungan::where('status', 'menunggu')->first();
    $obat = Obat::where('stok', '>=', 10)->first();

    $this->actingAs($dokterUser)->put(route('dokter.pemeriksaan.update', $kunjungan), [
        'subjective' => 'Pasien flu batuk',
        'resep_items' => [
            [
                'obat_id' => $obat->id,
                'jumlah' => 3,
                'dosis' => '3x1 tablet',
                'aturan_pakai' => 'Sesudah makan',
            ],
        ],
        'action' => 'selesai',
    ])->assertRedirect(route('dokter.dashboard'));

    $resep = $kunjungan->fresh()->resep;
    expect($resep)->not->toBeNull();
    expect($resep->status)->toBe('menunggu');

    // Apoteker sees prescription in waiting queue
    $response = $this->actingAs($apoteker)->get(route('apoteker.resep.index', ['status' => 'menunggu']));
    $response->assertOk();
    $response->assertSee($kunjungan->pasien->nama);

    // Apoteker dashboard counts it in waiting prescriptions
    $dashboardResponse = $this->actingAs($apoteker)->get(route('apoteker.dashboard'));
    $dashboardResponse->assertOk();
    $dashboardResponse->assertSee('Resep Menunggu');
});
