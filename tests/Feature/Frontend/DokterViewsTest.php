<?php

use App\Models\Kunjungan;
use App\Models\Pasien;
use App\Models\User;
use Database\Seeders\KlinikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(KlinikSeeder::class);
});

test('dashboard decision first layout renders next patient card with mono RM, allergy banner, and action buttons', function () {
    $dokterUser = User::where('role', 'dokter')->first();
    $dokter = $dokterUser->dokter;
    $pasien = Pasien::first();
    Kunjungan::create([
        'pasien_id' => $pasien->id,
        'dokter_id' => $dokter->id,
        'poli_id' => $dokter->poli_id,
        'tgl_kunjungan' => now()->toDateString(),
        'no_antrean' => 'UMUM-099',
        'status' => 'menunggu',
        'keluhan_utama' => 'Batuk pilek',
        'penjamin' => 'umum',
    ]);

    $response = $this->actingAs($dokterUser)->get(route('dokter.dashboard'));
    $response->assertOk();
    $response->assertSee('Dashboard Dokter');

    // Decision-first top card
    $response->assertSee('Pasien Berikutnya');
    $response->assertSee('No. Antrean', false);
    $response->assertSee('RM:', false);
    $response->assertSee('font-mono', false);
    $response->assertSee('Keluhan Awal:');
    $response->assertSee('Status Alergi:');

    // Action button
    $response->assertSee('Pemeriksaan');

    // Stat cards
    $response->assertSee('Total Pasien Hari Ini');
    $response->assertSee('Menunggu / Dipanggil');
    $response->assertSee('Sedang Diperiksa');
    $response->assertSee('Selesai Hari Ini');
});

test('dashboard antrean table uses table container and status pills without slop arrows', function () {
    $dokterUser = User::where('role', 'dokter')->first();

    $response = $this->actingAs($dokterUser)->get(route('dokter.dashboard'));

    $response->assertOk();
    $response->assertSee('Daftar Antrean Pasien');
    $response->assertSee('overflow-x-auto', false);

    // Status pill check
    $response->assertSee('rounded-full', false);

    // Anti-slop: zero decorative arrows
    $response->assertDontSee('&rarr;', false);
    $response->assertDontSee('&larr;', false);
    $response->assertDontSee('→');
    $response->assertDontSee('←');
});

test('pemeriksaan view implements template C with sticky patient header, tabs, and pb-28 container', function () {
    $dokterUser = User::where('role', 'dokter')->first();
    $kunjungan = Kunjungan::where('status', 'menunggu')->first();

    $response = $this->actingAs($dokterUser)->get(route('dokter.pemeriksaan.edit', $kunjungan));

    $response->assertOk();

    // Template C: Sticky Patient Header with 2 identifiers & allergy banner
    $response->assertSee('aria-label="Identitas Pasien"', false);
    $response->assertSee('sticky top-14', false);
    $response->assertSee($kunjungan->pasien->nama);
    $response->assertSee($kunjungan->pasien->no_rm);

    // Horizontal scrollable tabs on mobile
    $response->assertSee('aria-label="Tabs Pemeriksaan"', false);
    $response->assertSee('overflow-x-auto', false);
    $response->assertSee('min-h-11', false);
    $response->assertSee('Pemeriksaan (SOAP)');
    $response->assertSee('Resep Obat');
    $response->assertSee('Riwayat');

    // Main container has pb-28 so content is never covered by footer
    $response->assertSee('pb-28', false);

    // Sticky action footer
    $response->assertSee('bottom-0', false);
    $response->assertSee('Simpan Draf');
    $response->assertSee('Selesaikan Pemeriksaan');
});

test('pemeriksaan view vital signs have fixed mono units and icd-10 primary checkbox', function () {
    $dokterUser = User::where('role', 'dokter')->first();
    $kunjungan = Kunjungan::where('status', 'menunggu')->first();

    $response = $this->actingAs($dokterUser)->get(route('dokter.pemeriksaan.edit', $kunjungan));

    $response->assertOk();

    // Vital signs responsive grid and fixed units in font-mono
    $response->assertSee('mmHg');
    $response->assertSee('x/mnt');
    $response->assertSee('°C');
    $response->assertSee('%');
    $response->assertSee('kg');
    $response->assertSee('cm');
    $response->assertSee('pointer-events-none', false);

    // IMT calculation
    $response->assertSee("imt + ' kg/m²'", false);

    // SOAP textareas with min-h-24 px-3 py-2
    $response->assertSee('min-h-24 px-3 py-2', false);
    $response->assertSee('subjective');
    $response->assertSee('objective');
    $response->assertSee('assessment');
    $response->assertSee('plan');

    // ICD-10 diagnosis items with primary checkbox
    $response->assertSee('Diagnosis ICD-10');
    $response->assertSee('Diagnosis Primer');
    $response->assertSee('kode_icd10');
    $response->assertSee('nama_diagnosis');
});

test('pemeriksaan view contains allergy warning and clinical justification requirement', function () {
    $dokterUser = User::where('role', 'dokter')->first();
    $kunjungan = Kunjungan::first();

    $response = $this->actingAs($dokterUser)->get(route('dokter.pemeriksaan.edit', $kunjungan));

    $response->assertOk();

    // Inline allergy warning & clinical justification logic in Alpine
    $response->assertSee('getAllergyWarning');
    $response->assertSee('hasAllergyConflict');
    $response->assertSee('allergyJustification');
    $response->assertSee('Justifikasi Klinis Tertulis');
    $response->assertSee('Peringatan Klinis: Obat ini cocok dengan riwayat alergi pasien');
});

test('dokter views blade source files contain no slop decorative arrows or emojis', function () {
    $views = [
        resource_path('views/dokter/dashboard.blade.php'),
        resource_path('views/dokter/pemeriksaan.blade.php'),
        resource_path('views/dokter/pasien.blade.php'),
    ];

    foreach ($views as $viewPath) {
        $content = File::get($viewPath);

        // Check for decorative arrows
        expect($content)->not->toContain('&rarr;')
            ->not->toContain('&larr;')
            ->not->toContain('→')
            ->not->toContain('←');

        // Check for emojis (R-04)
        expect(preg_match('/[\x{1F300}-\x{1F9FF}]/u', $content))->toBe(0);
    }
});

test('pasien history view displays timeline with mono dates, masked NIK, and diagnoses', function () {
    $dokterUser = User::where('role', 'dokter')->first();
    $pasien = Pasien::first();

    $response = $this->actingAs($dokterUser)->get(route('dokter.pasien.show', $pasien));

    $response->assertOk();
    $response->assertSee('Riwayat Medis Pasien');
    $response->assertSee($pasien->nama);
    $response->assertSee($pasien->no_rm);

    // Disguised NIK with bullet characters
    $response->assertSee('•');

    // Mono dates and diagnosis
    $response->assertSee('font-mono', false);
    $response->assertSee('Urutan kronologis');

    // Zero slop arrows
    $response->assertDontSee('&rarr;', false);
    $response->assertDontSee('&larr;', false);
});
