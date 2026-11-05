<?php

use App\Models\Dokter;
use App\Models\Poli;
use App\Models\User;
use Database\Seeders\KlinikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(KlinikSeeder::class);
    $this->admin = User::where('role', 'admin')->first();
    $this->resepsionis = User::where('role', 'resepsionis')->first();
    $this->dokter = User::where('role', 'dokter')->first();
});

test('guest and non-admin cannot access admin poli routes', function () {
    $this->get(route('admin.poli.index'))->assertRedirect(route('login'));
    $this->actingAs($this->resepsionis)->get(route('admin.poli.index'))->assertStatus(403);
    $this->actingAs($this->dokter)->get(route('admin.poli.index'))->assertStatus(403);
});

test('admin can view poli index with stat cards and doctor assignments', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.poli.index'));

    $response->assertOk();
    $response->assertSee('Master Data Unit Poli');
    $response->assertSee('Total Unit Poli');
    $response->assertSee('Poli Aktif');
    $response->assertSee('Total Dokter Bertugas');

    // Check that existing polis and their doctors are listed
    $poliUmum = Poli::where('kode', 'UMUM')->first();
    expect($poliUmum)->not->toBeNull();
    $response->assertSee($poliUmum->nama);
    $response->assertSee($poliUmum->kode);
});

test('admin can view poli show with doctors in that specific poli', function () {
    $poliUmum = Poli::where('kode', 'UMUM')->with('dokters')->first();
    expect($poliUmum)->not->toBeNull();

    $response = $this->actingAs($this->admin)->get(route('admin.poli.show', $poliUmum));

    $response->assertOk();
    $response->assertSee($poliUmum->nama);
    $response->assertSee('Daftar Dokter Bertugas di '.$poliUmum->nama);

    // Assert that the doctor in Poli Umum is visible
    foreach ($poliUmum->dokters as $dok) {
        $response->assertSee($dok->nama);
    }
});

test('admin can create a user with role dokter and assign to a poli', function () {
    $poliGigi = Poli::where('kode', 'GIGI')->first();
    expect($poliGigi)->not->toBeNull();

    $response = $this->actingAs($this->admin)->post(route('admin.user.store'), [
        'name' => 'Dr. Dokter Baru Gigi',
        'email' => 'dr.gigi@klinik.com',
        'password' => 'password123',
        'role' => 'dokter',
        'poli_id' => $poliGigi->id,
        'spesialisasi' => 'Dokter Gigi Spesialis',
        'nip' => 'SIP.999888/2026',
        'no_hp' => '081234567890',
    ]);

    $response->assertRedirect(route('admin.user.index'));

    // Verify User created
    $user = User::where('email', 'dr.gigi@klinik.com')->first();
    expect($user)->not->toBeNull();
    expect($user->role)->toBe('dokter');

    // Verify Dokter profile created and linked to Poli Gigi
    $dokterProfile = Dokter::where('user_id', $user->id)->first();
    expect($dokterProfile)->not->toBeNull();
    expect($dokterProfile->poli_id)->toBe($poliGigi->id);
    expect($dokterProfile->spesialisasi)->toBe('Dokter Gigi Spesialis');
    expect($dokterProfile->nip)->toBe('SIP.999888/2026');

    // Verify assigned poli is displayed on the user index page
    $indexResponse = $this->actingAs($this->admin)->get(route('admin.user.index'));
    $indexResponse->assertSee('Dr. Dokter Baru Gigi');
    $indexResponse->assertSee($poliGigi->nama);
});

test('admin can create and update poli master data', function () {
    // Create new Poli
    $response = $this->actingAs($this->admin)->post(route('admin.poli.store'), [
        'kode' => 'MATA',
        'nama' => 'Poli Mata',
        'deskripsi' => 'Layanan kesehatan mata dan refraksi',
        'is_aktif' => '1',
    ]);

    $response->assertRedirect(route('admin.poli.index'));

    $poliMata = Poli::where('kode', 'MATA')->first();
    expect($poliMata)->not->toBeNull();
    expect($poliMata->nama)->toBe('Poli Mata');
    expect($poliMata->is_aktif)->toBeTrue();

    // Update Poli
    $updateResponse = $this->actingAs($this->admin)->put(route('admin.poli.update', $poliMata), [
        'kode' => 'MATA',
        'nama' => 'Poli Kesehatan Mata',
        'deskripsi' => 'Layanan kesehatan mata diperbarui',
        'is_aktif' => '1',
    ]);

    $updateResponse->assertRedirect(route('admin.poli.index'));
    expect($poliMata->fresh()->nama)->toBe('Poli Kesehatan Mata');

    // Deactivate Poli
    $this->actingAs($this->admin)->delete(route('admin.poli.destroy', $poliMata))
        ->assertRedirect(route('admin.poli.index'));
    expect($poliMata->fresh()->is_aktif)->toBeFalse();
});
