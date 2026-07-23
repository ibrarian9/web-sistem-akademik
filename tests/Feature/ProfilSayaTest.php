<?php

use App\Models\User;
use App\Models\Role;
use Livewire\Livewire;
use App\Livewire\Shared\ProfilSaya;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    $role = Role::where('nama', 'guru')->first() ?? Role::first();
    
    $this->user = User::create([
        'nama' => 'Guru Test Profil',
        'username' => 'gurutestprofil',
        'email' => 'gurutestprofil@example.com',
        'password' => bcrypt('password123'),
        'role_id' => $role->id,
        'status' => 'aktif',
    ]);
});

test('authenticated user can render profil saya page', function () {
    $this->actingAs($this->user);

    Livewire::test(ProfilSaya::class)
        ->assertStatus(200)
        ->assertSee('Guru Test Profil')
        ->assertSee('Informasi Profil Pengguna');
});

test('user can update profile details', function () {
    $this->actingAs($this->user);

    Livewire::test(ProfilSaya::class)
        ->set('nama', 'Guru Test Profil Updated')
        ->set('nip', '199001012020011001')
        ->set('jabatan', 'Wali Kelas 7-A')
        ->call('saveProfile')
        ->assertHasNoErrors();

    $this->user->refresh();
    expect($this->user->nama)->toBe('Guru Test Profil Updated');
    expect($this->user->nip)->toBe('199001012020011001');
    expect($this->user->jabatan)->toBe('Wali Kelas 7-A');
});

test('user can update password', function () {
    $this->actingAs($this->user);

    Livewire::test(ProfilSaya::class)
        ->set('current_password', 'password123')
        ->set('new_password', 'newsecret123')
        ->set('new_password_confirmation', 'newsecret123')
        ->call('updatePassword')
        ->assertHasNoErrors();

    $this->user->refresh();
    expect(Hash::check('newsecret123', $this->user->password))->toBeTrue();
});
