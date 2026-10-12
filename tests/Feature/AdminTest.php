<?php

use App\Models\User;
use Database\Seeders\KlinikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(KlinikSeeder::class);
});

test('guest cannot access admin routes', function () {
    $this->get(route('admin.user.index'))->assertRedirect(route('login'));
});

test('resepsionis cannot access admin routes', function () {
    $user = User::where('role', 'resepsionis')->first();
    $this->actingAs($user)->get(route('admin.user.index'))->assertStatus(403);
});

test('admin can CRUD user', function () {
    $admin = User::where('role', 'admin')->first();
    $this->actingAs($admin)->get(route('admin.user.index'))->assertOk();

    $this->actingAs($admin)->post(route('admin.user.store'), [
        'name' => 'User Tes',
        'email' => 'tes@klinik.com',
        'password' => 'password123',
        'role' => 'dokter',
    ])->assertRedirect(route('admin.user.index'));

    $user = User::where('email', 'tes@klinik.com')->first();
    expect($user)->not->toBeNull();
    expect($user->role)->toBe('dokter');

    $this->actingAs($admin)->put(route('admin.user.update', $user), [
        'name' => 'User Tes Updated',
        'email' => 'tes@klinik.com',
        'password' => null,
        'role' => 'resepsionis',
    ])->assertRedirect(route('admin.user.index'));
    expect($user->fresh()->name)->toBe('User Tes Updated');
    expect($user->fresh()->role)->toBe('resepsionis');

    $other = User::where('role', 'resepsionis')->first();
    $this->actingAs($admin)->delete(route('admin.user.destroy', $other))->assertRedirect(route('admin.user.index'));
    expect(User::find($other->id))->toBeNull();

    $this->actingAs($admin)->delete(route('admin.user.destroy', $admin))->assertRedirect(route('admin.user.index'));
    expect(User::find($admin->id))->not->toBeNull();
});

test('admin can access resepsionis routes', function () {
    $admin = User::where('role', 'admin')->first();
    $this->actingAs($admin)->get(route('pasien.index'))->assertOk();
    $this->actingAs($admin)->get(route('tagihan.index'))->assertOk();
    $this->actingAs($admin)->get(route('obat.index'))->assertOk();
});
