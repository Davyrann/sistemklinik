<?php

use App\Models\Kunjungan;
use App\Models\Obat;
use App\Models\Pasien;
use App\Models\User;
use Database\Seeders\KlinikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(KlinikSeeder::class);
});

test('guest cannot access dokter routes', function () {
    $this->get(route('dokter.dashboard'))->assertRedirect(route('login'));
});

test('resepsionis cannot access dokter routes', function () {
    $resepsionis = User::where('role', 'resepsionis')->first();
    $this->actingAs($resepsionis)->get(route('dokter.dashboard'))->assertStatus(403);
});

test('dokter is redirected to dokter dashboard from default dashboard', function () {
    $dokter = User::where('role', 'dokter')->first();
    $this->actingAs($dokter)->get(route('dashboard'))->assertRedirect(route('dokter.dashboard'));
});

test('dokter can view dashboard and antrean pasien', function () {
    $dokterUser = User::where('role', 'dokter')->first();
    $response = $this->actingAs($dokterUser)->get(route('dokter.dashboard'));

    $response->assertOk();
    $response->assertSee('Dashboard Dokter');
    $response->assertSee('Daftar Antrean Pasien');
});

test('dokter can panggil pasien and update status to dipanggil', function () {
    $dokterUser = User::where('role', 'dokter')->first();
    $kunjungan = Kunjungan::where('status', 'menunggu')->first();

    $this->actingAs($dokterUser)
        ->patch(route('dokter.pemeriksaan.panggil', $kunjungan))
        ->assertRedirect();

    expect($kunjungan->fresh()->status)->toBe('dipanggil');
});

test('opening examination form automatically sets status to diperiksa', function () {
    $dokterUser = User::where('role', 'dokter')->first();
    $kunjungan = Kunjungan::where('status', 'menunggu')->first();

    $this->actingAs($dokterUser)
        ->get(route('dokter.pemeriksaan.edit', $kunjungan))
        ->assertOk();

    expect($kunjungan->fresh()->status)->toBe('diperiksa');
});

test('dokter can save draft SOAP and vital signs without locking', function () {
    $dokterUser = User::where('role', 'dokter')->first();
    $kunjungan = Kunjungan::where('status', 'menunggu')->first();

    $this->actingAs($dokterUser)->put(route('dokter.pemeriksaan.update', $kunjungan), [
        'td_sistolik' => 120,
        'td_diastolik' => 80,
        'nadi' => 76,
        'suhu' => 36.6,
        'rr' => 18,
        'spo2' => 99,
        'bb' => 65,
        'tb' => 170,
        'subjective' => 'Pasien mengeluh pusing sejak 2 hari.',
        'objective' => 'Keadaan umum baik, tensi stabil.',
        'assessment' => 'Cephalea tension type',
        'plan' => 'Istirahat cukup dan hindari stres.',
        'diagnoses' => [
            [
                'kode_icd10' => 'G44.2',
                'nama_diagnosis' => 'Tension-type headache',
                'is_primary' => 1,
            ],
        ],
        'action' => 'draft',
    ])->assertRedirect(route('dokter.dashboard'));

    $kunjungan->refresh();
    expect($kunjungan->status)->toBe('diperiksa');
    expect($kunjungan->tandaVital)->not->toBeNull();
    expect($kunjungan->tandaVital->td_sistolik)->toBe(120);
    expect($kunjungan->tandaVital->imt)->toBe(22.5);
    expect($kunjungan->rekamMedis)->not->toBeNull();
    expect($kunjungan->rekamMedis->is_locked)->toBeFalse();
    expect($kunjungan->rekamMedis->diagnoses)->toHaveCount(1);
    expect($kunjungan->rekamMedis->diagnoses->first()->kode_icd10)->toBe('G44.2');
});

test('dokter can complete examination, lock rekam medis, decrement medicine stock and update tagihan', function () {
    $dokterUser = User::where('role', 'dokter')->first();
    $kunjungan = Kunjungan::where('status', 'menunggu')->first();
    $obat = Obat::where('stok', '>=', 10)->first();
    $initialStock = $obat->stok;

    $this->actingAs($dokterUser)->put(route('dokter.pemeriksaan.update', $kunjungan), [
        'td_sistolik' => 130,
        'td_diastolik' => 85,
        'nadi' => 80,
        'suhu' => 37.0,
        'rr' => 20,
        'spo2' => 98,
        'bb' => 70,
        'tb' => 175,
        'subjective' => 'Demam dan radang tenggorokan.',
        'objective' => 'Faring hiperemis (+)',
        'assessment' => 'Faringitis Akut',
        'plan' => 'Antibiotik dan antipiretik.',
        'diagnoses' => [
            [
                'kode_icd10' => 'J02.9',
                'nama_diagnosis' => 'Acute pharyngitis',
                'is_primary' => 1,
            ],
        ],
        'resep_catatan' => 'Minum sesudah makan',
        'resep_items' => [
            [
                'obat_id' => $obat->id,
                'jumlah' => 5,
                'dosis' => '3x1 tablet',
                'frekuensi' => 'Tiap 8 jam',
                'aturan_pakai' => 'Sesudah makan',
            ],
        ],
        'action' => 'selesai',
    ])->assertRedirect(route('dokter.dashboard'));

    $kunjungan->refresh();
    expect($kunjungan->status)->toBe('selesai');
    expect($kunjungan->rekamMedis->is_locked)->toBeTrue();
    expect($kunjungan->rekamMedis->locked_at)->not->toBeNull();

    // Stok obat terkurangi
    expect($obat->fresh()->stok)->toBe($initialStock - 5);

    // Tagihan pasien bertambah dengan item obat
    $tagihan = $kunjungan->tagihan;
    expect($tagihan)->not->toBeNull();
    $obatDetail = $tagihan->details()->where('jenis', 'obat')->first();
    expect($obatDetail)->not->toBeNull();
    expect((int) $obatDetail->qty)->toBe(5);
});

test('dokter can complete examination when frekuensi is omitted in resep items', function () {
    $dokterUser = User::where('role', 'dokter')->first();
    $kunjungan = Kunjungan::where('status', 'menunggu')->first();
    $obat = Obat::where('stok', '>=', 10)->first();

    $this->actingAs($dokterUser)->put(route('dokter.pemeriksaan.update', $kunjungan), [
        'subjective' => 'Batuk pilek',
        'resep_items' => [
            [
                'obat_id' => $obat->id,
                'jumlah' => 2,
                'dosis' => '3 x 1',
                'aturan_pakai' => 'Sesudah makan',
            ],
        ],
        'action' => 'selesai',
    ])->assertRedirect(route('dokter.dashboard'));

    $resepDetail = $kunjungan->fresh()->resep->details->first();
    expect($resepDetail)->not->toBeNull();
    expect($resepDetail->dosis)->toBe('3 x 1');
});

test('locked rekam medis cannot be updated', function () {
    $dokterUser = User::where('role', 'dokter')->first();
    $kunjungan = Kunjungan::where('status', 'selesai')->whereHas('rekamMedis')->first();

    // Pastikan rekam medis terkunci
    $kunjungan->rekamMedis()->update(['is_locked' => true, 'locked_at' => now()]);

    $this->actingAs($dokterUser)
        ->put(route('dokter.pemeriksaan.update', $kunjungan), [
            'subjective' => 'Mencoba ubah data terkunci',
            'action' => 'draft',
        ])
        ->assertRedirect(route('dokter.pemeriksaan.edit', $kunjungan))
        ->assertSessionHas('error');
});

test('dokter can view full patient medical history', function () {
    $dokterUser = User::where('role', 'dokter')->first();
    $pasien = Pasien::first();

    $this->actingAs($dokterUser)
        ->get(route('dokter.pasien.show', $pasien))
        ->assertOk()
        ->assertSee($pasien->nama);
});
