<?php

use App\Models\User;
use App\Models\Role;
use Livewire\Livewire;
use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    // Roles are seeded before each test
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    
    $this->adminRole = Role::where('nama', 'super_admin')->first();
    $this->guruRole = Role::where('nama', 'guru')->first();
    $this->muridRole = Role::where('nama', 'murid')->first();
    $this->financeRole = Role::where('nama', 'finance')->first();
});

it('redirects guest to login page from root', function () {
    $response = $this->get('/');
    $response->assertRedirect('/login');
});

it('renders login page successfully', function () {
    $response = $this->get('/login');
    $response->assertStatus(200);
    $response->assertSee('Sistem Akademik Yayasan');
});

it('allows active users to login and redirects to correct dashboard', function () {
    $user = User::create([
        'nama' => 'Pak Guru Budi',
        'username' => 'budi_guru',
        'email' => 'budi@guru.com',
        'password' => Hash::make('secret123'),
        'role_id' => $this->guruRole->id,
        'status' => 'aktif',
    ]);

    Livewire::test(Login::class)
        ->set('username', 'budi_guru')
        ->set('password', 'secret123')
        ->call('login')
        ->assertRedirect(route('guru.dashboard'));

    $this->assertAuthenticatedAs($user);
});

it('prevents non-active users from logging in', function () {
    $user = User::create([
        'nama' => 'Nonaktif User',
        'username' => 'nonaktif',
        'email' => 'nonaktif@mail.com',
        'password' => Hash::make('secret123'),
        'role_id' => $this->guruRole->id,
        'status' => 'nonaktif',
    ]);

    Livewire::test(Login::class)
        ->set('username', 'nonaktif')
        ->set('password', 'secret123')
        ->call('login')
        ->assertHasErrors(['username']);

    $this->assertGuest();
});

it('denies access to unauthorized dashboards based on role', function () {
    $user = User::create([
        'nama' => 'Siswa Joko',
        'username' => 'joko_siswa',
        'email' => 'joko@siswa.com',
        'password' => Hash::make('secret123'),
        'role_id' => $this->muridRole->id,
        'status' => 'aktif',
    ]);

    $this->actingAs($user);

    // Try accessing super admin dashboard
    $response = $this->get(route('super-admin.dashboard'));
    
    // Should redirect to murid default dashboard since role is murid
    $response->assertRedirect(route('murid.dashboard'));
});
