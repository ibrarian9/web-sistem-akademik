<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Guru;
use Livewire\Livewire;
use App\Livewire\TataUsaha\ManajemenKaryawan;
use App\Livewire\SuperAdmin\TataKelola\ManajemenUser;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

    $roleTU = Role::where('nama', 'tata_usaha')->first();
    $this->userTU = User::firstOrCreate([
        'username' => 'tu_test_karyawan',
    ], [
        'nama' => 'Staff Tata Usaha',
        'email' => 'tu_karyawan@test.com',
        'password' => bcrypt('password'),
        'role_id' => $roleTU->id,
    ]);
});

test('tata usaha can view employee directory', function () {
    $this->actingAs($this->userTU);

    Livewire::test(ManajemenKaryawan::class)
        ->assertStatus(200)
        ->assertSee('Direktori Karyawan & Staff')
        ->assertSee('Tambah Karyawan Baru');
});

test('tata usaha can create a new employee and user account', function () {
    $this->actingAs($this->userTU);
    $roleFinance = Role::where('nama', 'finance')->first();

    Livewire::test(ManajemenKaryawan::class)
        ->call('openCreate')
        ->set('nama', 'Karyawan Keuangan Baru')
        ->set('username', 'staff_finance_new')
        ->set('email', 'finance_new@test.com')
        ->set('password', 'password123')
        ->set('role_id', $roleFinance->id)
        ->set('nip', 'FIN-2026-001')
        ->set('no_hp', '081299998888')
        ->set('status', 'aktif')
        ->call('save')
        ->assertHasNoErrors();

    $newUser = User::where('username', 'staff_finance_new')->first();
    expect($newUser)->not->toBeNull();
    expect($newUser->nama)->toEqual('Karyawan Keuangan Baru');
    expect($newUser->role_id)->toEqual($roleFinance->id);

    // Profile record in guru table should be synced as well
    $guruProfile = Guru::where('user_id', $newUser->id)->first();
    expect($guruProfile)->not->toBeNull();
    expect($guruProfile->nip)->toEqual('FIN-2026-001');
});

test('tata usaha can edit an existing employee and user account', function () {
    $this->actingAs($this->userTU);
    $roleGuru = Role::where('nama', 'guru')->first();

    $guruUser = User::create([
        'nama' => 'Guru Lama',
        'username' => 'guru_lama',
        'email' => 'guru_lama@test.com',
        'password' => bcrypt('password'),
        'role_id' => $roleGuru->id,
        'status' => 'aktif',
    ]);

    $guruProfile = Guru::create([
        'user_id' => $guruUser->id,
        'nip' => 'GURU-100',
        'jenis_guru' => 'umum',
        'status_kepegawaian' => 'honorer',
        'tanggal_masuk' => now()->toDateString(),
        'status_aktif' => true,
    ]);

    Livewire::test(ManajemenKaryawan::class)
        ->call('openEdit', $guruUser->id)
        ->set('nama', 'Guru Lama Diperbarui')
        ->set('nip', 'GURU-100-EDITED')
        ->set('jenis_guru', 'tahfidz')
        ->call('save')
        ->assertHasNoErrors();

    $guruUser->refresh();
    expect($guruUser->nama)->toEqual('Guru Lama Diperbarui');

    $guruProfile->refresh();
    expect($guruProfile->nip)->toEqual('GURU-100-EDITED');
    expect($guruProfile->jenis_guru)->toEqual('tahfidz');
});

test('tata usaha can delete an employee account except self or super admin', function () {
    $this->actingAs($this->userTU);
    $roleGuru = Role::where('nama', 'guru')->first();

    $guruUser = User::create([
        'nama' => 'Guru Hapus',
        'username' => 'guru_hapus',
        'password' => bcrypt('password'),
        'role_id' => $roleGuru->id,
    ]);

    Livewire::test(ManajemenKaryawan::class)
        ->call('delete', $guruUser->id)
        ->assertHasNoErrors();

    expect(User::find($guruUser->id))->toBeNull();
});

test('tata usaha can manage staff accounts in manajemen user component', function () {
    $this->actingAs($this->userTU);
    $roleFinance = Role::where('nama', 'finance')->first();

    Livewire::test(ManajemenUser::class)
        ->call('openCreate')
        ->set('nama', 'Finance Account From User Management')
        ->set('username', 'finance_user_mgmt')
        ->set('password', 'password123')
        ->set('role_id', $roleFinance->id)
        ->call('save')
        ->assertHasNoErrors();

    expect(User::where('username', 'finance_user_mgmt')->exists())->toBeTrue();
});
