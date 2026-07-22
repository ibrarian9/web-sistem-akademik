<?php

use App\Models\User;
use App\Models\Role;
use Livewire\Livewire;
use App\Livewire\Shared\ProfilSaya;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    $role = Role::where('nama', 'guru')->first() ?? Role::first();
    
    $this->user = User::create([
        'nama' => 'Guru Test TTD',
        'username' => 'gurutestttd',
        'email' => 'gurutestttd@example.com',
        'password' => bcrypt('password'),
        'role_id' => $role->id,
        'status' => 'aktif',
    ]);
});

test('authenticated user can render profil saya page', function () {
    $this->actingAs($this->user);

    Livewire::test(ProfilSaya::class)
        ->assertStatus(200)
        ->assertSee('Guru Test TTD')
        ->assertSee('Tanda Tangan Digital');
});

test('user can update profile and upload ttd digital', function () {
    $this->actingAs($this->user);
    Storage::fake('public');

    $file = UploadedFile::fake()->image('signature.png', 200, 100);

    Livewire::test(ProfilSaya::class)
        ->set('nama', 'Guru Test TTD Updated')
        ->set('nip', '199001012020011001')
        ->set('jabatan', 'Wali Kelas 7-A')
        ->set('new_ttd', $file)
        ->call('saveProfile')
        ->assertHasNoErrors();

    $this->user->refresh();
    expect($this->user->nama)->toBe('Guru Test TTD Updated');
    expect($this->user->nip)->toBe('199001012020011001');
    expect($this->user->jabatan)->toBe('Wali Kelas 7-A');
    expect($this->user->ttd_digital)->not->toBeNull();
});

test('user can save drawn signature from web canvas', function () {
    $this->actingAs($this->user);

    $base64Png = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

    Livewire::test(ProfilSaya::class)
        ->set('drawn_ttd', $base64Png)
        ->call('saveProfile')
        ->assertHasNoErrors();

    $this->user->refresh();
    expect($this->user->ttd_digital)->not->toBeNull();
    expect(file_exists(public_path($this->user->ttd_digital)))->toBeTrue();
});
