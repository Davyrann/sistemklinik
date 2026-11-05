<?php

use App\Models\User;
use Database\Seeders\KlinikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(KlinikSeeder::class);
});

test('app layout renders alpine root container and accessible skip link', function () {
    $user = User::where('role', 'resepsionis')->first();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('x-data="{ sidebarOpen: false }"', false);
    $response->assertSee('<a href="#main-content"', false);
    $response->assertSee('Lewati ke konten utama');
    $response->assertSee('focus:bg-primary-700', false);
    $response->assertSee('focus:text-white', false);
    $response->assertSee('<main id="main-content"', false);
    $response->assertSee('max-w-[1440px]', false);
});

test('app topbar renders height, hamburger button, search input with shortcut, and date-time', function () {
    $user = User::where('role', 'resepsionis')->first();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    // Topbar container
    $response->assertSee('h-14', false);
    $response->assertSee('sticky top-0 z-20', false);
    $response->assertSee('border-b border-border', false);

    // Mobile Hamburger button
    $response->assertSee('@click="sidebarOpen = true"', false);
    $response->assertSee('size-11', false);
    $response->assertSee('md:hidden', false);
    $response->assertSee('aria-label="Buka menu navigasi"', false);

    // Global Search input with "/" shortcut
    $response->assertSee('placeholder="Cari nama / No. RM / NIK"', false);
    $response->assertSee('x-ref="globalSearchInput"', false);
    $response->assertSee('<kbd', false);

    // Live Date & Time display
    $response->assertSee('update()', false);
    $response->assertSee('font-mono', false);

    // Profile dropdown
    $response->assertSee($user->name);
    $response->assertSee('resepsionis');
});

test('app sidebar renders mobile drawer with backdrop and close button', function () {
    $user = User::where('role', 'resepsionis')->first();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    // Mobile backdrop
    $response->assertSee('x-show="sidebarOpen"', false);
    $response->assertSee('bg-slate-900/50', false);

    // Mobile drawer
    $response->assertSee('w-60', false);
    $response->assertSee('-translate-x-full', false);

    // Drawer close button min 44px
    $response->assertSee('@click="sidebarOpen = false"', false);
    $response->assertSee('aria-label="Tutup menu navigasi"', false);
});

test('app sidebar renders tablet rail and desktop sidebar', function () {
    $user = User::where('role', 'resepsionis')->first();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    // Responsive sidebar
    $response->assertSee('md:w-16', false);
    $response->assertSee('lg:w-60', false);

    // Tooltip for collapsed tablet rail
    $response->assertSee('md:group-hover:block', false);
    $response->assertSee('lg:group-hover:hidden', false);

    // Bottom user block
    $response->assertSee('mt-auto p-3 border-t border-border', false);
    $response->assertSee(route('logout'));
});

test('resepsionis navigation links render correctly and active state is applied', function () {
    $user = User::where('role', 'resepsionis')->first();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Dashboard');
    $response->assertSee('Pasien');
    $response->assertSee('Kunjungan & Antrean');
    $response->assertSee('Tagihan & Kasir');
    $response->assertSee('Stok Obat');

    // Active item token
    $response->assertSee('bg-primary-50 text-primary-700 border-l-[3px] border-primary-700 font-semibold', false);

    // Resepsionis should not see Admin links
    $response->assertDontSee('Kelola Pengguna');
    $response->assertDontSee('Log Aktivitas');
});

test('dokter navigation links render correctly and exclude forbidden modules', function () {
    $dokter = User::where('role', 'dokter')->first();

    $response = $this->actingAs($dokter)->get(route('dokter.dashboard'));

    $response->assertOk();
    $response->assertSee('Antrean Pasien');

    // Dokter should not see cashier or admin management links
    $response->assertDontSee('Tagihan & Kasir');
    $response->assertDontSee('Kelola Pengguna');
    $response->assertDontSee('Log Aktivitas');
});

test('admin navigation links render full management modules', function () {
    $admin = User::where('role', 'admin')->first();

    $response = $this->actingAs($admin)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('Dashboard');
    $response->assertSee('Pasien');
    $response->assertSee('Kunjungan & Antrean');
    $response->assertSee('Tagihan & Kasir');
    $response->assertSee('Stok Obat');
    $response->assertSee('Pemeriksaan Dokter');
    $response->assertSee('Kelola Pengguna');
    $response->assertSee('Log Aktivitas');
});

test('guest layout renders clean canvas without gradients and with logo', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
    $response->assertSee('bg-bg text-text', false);
    $response->assertSee('bg-surface border border-border shadow-sm rounded-lg', false);
    $response->assertSee('logo.svg');
    $response->assertSee('KlinikKu');
    $response->assertDontSee('bg-gradient-');
});
