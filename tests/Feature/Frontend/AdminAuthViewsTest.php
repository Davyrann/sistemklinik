<?php

use App\Models\ActivityLog;
use App\Models\User;
use Database\Seeders\KlinikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(KlinikSeeder::class);
});

test('admin user index renders template A with stat cards, table container, and confirm dialog instead of browser confirm', function () {
    $admin = User::where('role', 'admin')->first();
    $otherUser = User::where('role', 'dokter')->first();

    $response = $this->actingAs($admin)->get(route('admin.user.index'));

    $response->assertOk();

    // Template A Page Header
    $response->assertSee('Kelola Pengguna');
    $response->assertSee('Pengguna Baru');

    // Stat cards
    $response->assertSee('Total Pengguna');
    $response->assertSee('Dokter');
    $response->assertSee('Resepsionis');

    // Table container and datatable
    $response->assertSee('bg-surface border border-border shadow-sm rounded-lg', false);
    $response->assertSee('overflow-x-auto', false);
    $response->assertSee('datatable', false);

    // Users listed
    $response->assertSee($admin->name);
    $response->assertSee($otherUser->name);

    // Native browser confirm is BANNED
    $response->assertDontSee('onsubmit="return confirm', false);
    $response->assertDontSee("onsubmit='return confirm", false);

    // Modal confirm dialog is rendered naming the target user
    $response->assertSee('confirm-delete-user-'.$otherUser->id, false);
    $response->assertSee('Konfirmasi Hapus Pengguna');
    $response->assertSee($otherUser->name);
    $response->assertSee(route('admin.user.destroy', $otherUser), false);
    $response->assertSee('Hapus Pengguna');
});

test('admin user form renders template B with grouped inputs and explicit wajib labels', function () {
    $admin = User::where('role', 'admin')->first();

    // Create user form
    $createResponse = $this->actingAs($admin)->get(route('admin.user.create'));
    $createResponse->assertOk();
    $createResponse->assertSee('Pengguna Baru');
    $createResponse->assertSee('Informasi Akun');
    $createResponse->assertSee('Hak Akses & Kredensial', false);

    // (wajib) label rendering instead of asterisk
    $createResponse->assertSee('(wajib)');
    $createResponse->assertDontSee('Nama Lengkap *');
    $createResponse->assertDontSee('Email *');

    // Form inputs & action buttons
    $createResponse->assertSee('name="name"', false);
    $createResponse->assertSee('name="email"', false);
    $createResponse->assertSee('name="role"', false);
    $createResponse->assertSee('name="password"', false);
    $createResponse->assertSee('Batal');
    $createResponse->assertSee('Simpan Pengguna');

    // Edit user form
    $editUser = User::where('role', 'dokter')->first();
    $editResponse = $this->actingAs($admin)->get(route('admin.user.edit', $editUser));
    $editResponse->assertOk();
    $editResponse->assertSee('Edit Pengguna');
    $editResponse->assertSee($editUser->name);
    $editResponse->assertSee('Simpan Perubahan');
});

test('admin log index renders template A with stat cards, mono timestamps, method badges, and status pills', function () {
    $admin = User::where('role', 'admin')->first();

    ActivityLog::create([
        'user_id' => $admin->id,
        'user_name' => $admin->name,
        'role' => 'admin',
        'method' => 'POST',
        'path' => '/admin/user',
        'route_name' => 'admin.user.store',
        'status' => 200,
        'error' => null,
        'ip' => '127.0.0.1',
        'created_at' => now(),
    ]);

    ActivityLog::create([
        'user_id' => null,
        'user_name' => null,
        'role' => null,
        'method' => 'GET',
        'path' => '/login',
        'route_name' => 'login',
        'status' => 403,
        'error' => 'Akses ditolak',
        'ip' => '127.0.0.1',
        'created_at' => now()->subMinute(),
    ]);

    $response = $this->actingAs($admin)->get(route('admin.log.index'));

    $response->assertOk();
    $response->assertSee('Log Aktivitas');

    // Stat cards
    $response->assertSee('Total Log');
    $response->assertSee('Berhasil (2xx)');
    $response->assertSee('Error / Masalah');

    // Table container and datatable
    $response->assertSee('overflow-x-auto', false);
    $response->assertSee('bg-surface border border-border shadow-sm rounded-lg', false);

    // Filter toolbar
    $response->assertSee('Cari user / URL / route');
    $response->assertSee('Status HTTP');
    $response->assertSee('Filter');

    // Method badge with mono font
    $response->assertSee('font-mono', false);
    $response->assertSee('POST');
    $response->assertSee('GET');

    // Status pill
    $response->assertSee('200');
    $response->assertSee('403');
    $response->assertSee('Akses ditolak');
});

test('auth login view renders clean mobile-first form with accessible labels and zero emojis', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
    $response->assertSee('Masuk ke Akun');
    $response->assertSee('(wajib)');
    $response->assertSee('name="email"', false);
    $response->assertSee('name="password"', false);
    $response->assertSee('Ingat saya');
    $response->assertSee('Masuk');

    // Anti-slop: zero emojis
    $content = $response->getContent();
    expect(preg_match('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', $content))->toBe(0);
});

test('auth forgot and reset password views render clean mobile-first forms without emojis', function () {
    $forgotResponse = $this->get(route('password.request'));
    $forgotResponse->assertOk();
    $forgotResponse->assertSee('Lupa Password');
    $forgotResponse->assertSee('name="email"', false);
    $forgotResponse->assertSee('Kirim Tautan Reset Password');

    $forgotContent = $forgotResponse->getContent();
    expect(preg_match('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', $forgotContent))->toBe(0);

    $resetResponse = $this->get('/reset-password/sample-token?email=user@klinik.com');
    $resetResponse->assertOk();
    $resetResponse->assertSee('Atur Ulang Password');
    $resetResponse->assertSee('name="token"', false);
    $resetResponse->assertSee('name="password"', false);
    $resetResponse->assertSee('name="password_confirmation"', false);

    $resetContent = $resetResponse->getContent();
    expect(preg_match('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', $resetContent))->toBe(0);
});

test('profile view renders template B grouped sections with input labels and delete account modal', function () {
    $user = User::where('role', 'admin')->first();

    $response = $this->actingAs($user)->get(route('profile.edit'));

    $response->assertOk();
    $response->assertSee('Pengaturan Profil');
    $response->assertSee('Informasi Profil');
    $response->assertSee('Perbarui Password');
    $response->assertSee('Hapus Akun');

    // (wajib) label rendering
    $response->assertSee('(wajib)');

    // Text inputs and primary buttons
    $response->assertSee('name="name"', false);
    $response->assertSee('name="email"', false);
    $response->assertSee('Simpan Perubahan');
    $response->assertSee('Perbarui Password');

    // Modal confirmation for account deletion
    $response->assertSee('confirm-user-deletion', false);
    $response->assertSee('Apakah Anda yakin ingin menghapus akun?');

    // Anti-slop: zero emojis
    $content = $response->getContent();
    expect(preg_match('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', $content))->toBe(0);
});
