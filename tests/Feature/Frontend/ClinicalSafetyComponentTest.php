<?php

use App\Models\Kunjungan;
use App\Models\Pasien;
use Illuminate\Support\Carbon;

test('allergy banner renders 3 discrete states accurately', function () {
    // 1. Recorded allergy (count > 0)
    $viewWithAllergens = $this->blade('<x-allergy-banner :alergi="$alergi" />', [
        'alergi' => 'Amoxicillin, Paracetamol',
    ]);

    $viewWithAllergens->assertSee('Alergi:');
    $viewWithAllergens->assertSee('Amoxicillin');
    $viewWithAllergens->assertSee('Paracetamol');
    $viewWithAllergens->assertSee('bg-red-50');
    $viewWithAllergens->assertSee('text-danger');
    $viewWithAllergens->assertSee('rounded-full');

    // 2. Confirmed no allergy (empty string or recognized none marker)
    $viewNone = $this->blade('<x-allergy-banner :alergi="$alergi" />', [
        'alergi' => '',
    ]);
    $viewNone->assertSee('Tidak ada alergi tercatat');
    $viewNone->assertSee('bg-bg');
    $viewNone->assertSee('text-text-muted');

    $viewNoneExplicit = $this->blade('<x-allergy-banner :alergi="$alergi" />', [
        'alergi' => 'tidak ada',
    ]);
    $viewNoneExplicit->assertSee('Tidak ada alergi tercatat');

    // 3. Not yet asked ($alergi === null)
    $viewNotAsked = $this->blade('<x-allergy-banner :alergi="$alergi" />', [
        'alergi' => null,
    ]);
    $viewNotAsked->assertSee('Alergi belum ditanyakan. Konfirmasi ke pasien.');
    $viewNotAsked->assertSee('bg-amber-50');
    $viewNotAsked->assertSee('text-warning');
});

test('patient header displays two patient identifiers, queue, age, penjamin, allergy and complaint', function () {
    $pasien = new Pasien([
        'nama' => 'Siti Rahmawati',
        'no_rm' => 'RM-202611-0042',
        'nik' => '3201010101010001',
        'tgl_lahir' => Carbon::now()->subYears(28)->format('Y-m-d'),
        'jk' => 'P',
        'penjamin' => 'bpjs',
        'alergi' => 'Penisilin',
    ]);

    $kunjungan = new Kunjungan([
        'no_antrean' => 'A-012',
        'penjamin' => 'bpjs',
        'keluhan_utama' => 'Demam tinggi dan sakit kepala sejak 3 hari',
    ]);
    $kunjungan->setRelation('pasien', $pasien);

    $view = $this->blade('<x-patient-header :kunjungan="$kunjungan" />', [
        'kunjungan' => $kunjungan,
    ]);

    // Primary patient identifiers
    $view->assertSee('Siti Rahmawati');
    $view->assertSee('RM-202611-0042');

    // Queue badge and demographics
    $view->assertSee('A-012');
    $view->assertSee('28 th');
    $view->assertSee('Perempuan');
    $view->assertSee('BPJS');

    // Keluhan utama
    $view->assertSee('Demam tinggi dan sakit kepala sejak 3 hari');

    // Integrated allergy banner
    $view->assertSee('Alergi:');
    $view->assertSee('Penisilin');

    // Sticky styling
    $view->assertSee('sticky top-14');
});

test('patient header handles patient with unknown allergy status gracefully', function () {
    $pasien = new Pasien([
        'nama' => 'Budi Santoso',
        'no_rm' => 'RM-202611-0099',
        'tgl_lahir' => Carbon::now()->subYears(45)->format('Y-m-d'),
        'jk' => 'L',
        'penjamin' => 'umum',
        'alergi' => null,
    ]);

    $kunjungan = new Kunjungan([
        'no_antrean' => 'U-005',
        'penjamin' => 'umum',
        'keluhan_utama' => null,
    ]);
    $kunjungan->setRelation('pasien', $pasien);

    $view = $this->blade('<x-patient-header :kunjungan="$kunjungan" />', [
        'kunjungan' => $kunjungan,
    ]);

    $view->assertSee('Budi Santoso');
    $view->assertSee('RM-202611-0099');
    $view->assertSee('45 th');
    $view->assertSee('Laki-laki');
    $view->assertSee('Alergi belum ditanyakan. Konfirmasi ke pasien.');
    $view->assertDontSee('Keluhan Utama:');
});

test('stat card renders honest metric value and label without fake trends or emojis', function () {
    // Default status
    $defaultCard = $this->blade('<x-stat-card label="Total Pasien Hari Ini" :value="42" />');
    $defaultCard->assertSee('Total Pasien Hari Ini');
    $defaultCard->assertSee('42');
    $defaultCard->assertSee('font-mono');
    $defaultCard->assertSee('text-text');
    $defaultCard->assertDontSee('+');
    $defaultCard->assertDontSee('↑');
    $defaultCard->assertDontSee('↓');

    // Semantic status (warning)
    $warningCard = $this->blade('<x-stat-card label="Menunggu Antrean" value="15" status="warning" />');
    $warningCard->assertSee('Menunggu Antrean');
    $warningCard->assertSee('15');
    $warningCard->assertSee('text-warning');

    // Semantic status (success)
    $successCard = $this->blade('<x-stat-card label="Selesai Diperiksa" value="27" status="success" />');
    $successCard->assertSee('Selesai Diperiksa');
    $successCard->assertSee('27');
    $successCard->assertSee('text-success');
});
