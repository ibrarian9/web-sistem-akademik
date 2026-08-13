<?php

use App\Models\Guru;
use App\Models\JadwalPiketGuru;
use App\Models\Role;
use App\Models\Semester;
use App\Models\User;
use App\Livewire\SuperAdmin\TataKelola\ManajemenGuru;
use App\Livewire\TataUsaha\ManajemenKaryawan;
use App\Livewire\TataUsaha\ManajemenPiketGuru;
use Livewire\Livewire;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    $this->artisan('db:seed', ['--class' => 'DemoDataSeeder']);

    $this->userTu = User::where('username', 'tatausaha')->first() 
        ?? User::whereHas('role', fn($q) => $q->where('nama', 'tata_usaha'))->first();

    if (!$this->userTu) {
        $roleTu = Role::where('nama', 'tata_usaha')->first();
        $this->userTu = User::factory()->create([
            'username' => 'tu_test_user',
            'role_id'  => $roleTu->id,
            'status'   => 'aktif',
        ]);
    }
});

test('tata usaha can view and add teacher piket schedule', function () {
    $this->actingAs($this->userTu);

    $guru = Guru::first();
    $semester = Semester::first();

    Livewire::test(ManajemenPiketGuru::class)
        ->assertStatus(200)
        ->set('selectedGuruId', $guru->id)
        ->set('selectedHari', 'senin')
        ->call('addPiket')
        ->assertHasNoErrors()
        ->assertSee('Jadwal piket guru berhasil ditambahkan.');

    $piket = JadwalPiketGuru::where('guru_id', $guru->id)->where('hari', 'senin')->first();
    expect($piket)->not->toBeNull();
});

test('tata usaha can create and edit teacher data in manajemen guru', function () {
    $this->actingAs($this->userTu);

    // Create new teacher
    Livewire::test(ManajemenGuru::class)
        ->call('openCreate')
        ->set('nama', 'Guru Baru TU Test')
        ->set('username', 'guru_tu_test_' . rand(1000, 9999))
        ->set('password', 'password123')
        ->set('status_kepegawaian', 'gtt')
        ->set('status_pernikahan', 'menikah')
        ->set('tanggal_masuk', date('Y-m-d'))
        ->set('status_aktif', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('berhasil');

    $userNew = User::where('nama', 'Guru Baru TU Test')->first();
    expect($userNew)->not->toBeNull();
    expect($userNew->guru)->not->toBeNull();

    // Edit teacher
    Livewire::test(ManajemenGuru::class)
        ->call('openEdit', $userNew->guru->id)
        ->set('nama', 'Guru Baru TU Test Updated')
        ->call('save')
        ->assertHasNoErrors();

    expect($userNew->fresh()->nama)->toEqual('Guru Baru TU Test Updated');
});

test('tata usaha can create and edit employee in manajemen karyawan', function () {
    $this->actingAs($this->userTu);

    $roleStaff = Role::where('nama', 'tata_usaha')->first();

    // Create employee
    Livewire::test(ManajemenKaryawan::class)
        ->call('openCreate')
        ->set('nama', 'Karyawan Baru TU Test')
        ->set('username', 'karyawan_tu_' . rand(1000, 9999))
        ->set('password', 'password123')
        ->set('role_id', $roleStaff->id)
        ->set('status', 'aktif')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('berhasil');

    $userStaff = User::where('nama', 'Karyawan Baru TU Test')->first();
    expect($userStaff)->not->toBeNull();

    // Edit employee
    Livewire::test(ManajemenKaryawan::class)
        ->call('openEdit', $userStaff->id)
        ->set('nama', 'Karyawan Baru TU Test Updated')
        ->call('save')
        ->assertHasNoErrors();

    expect($userStaff->fresh()->nama)->toEqual('Karyawan Baru TU Test Updated');
});

test('tata usaha can create new class in manajemen kelas even if semester was empty', function () {
    $this->actingAs($this->userTu);

    Livewire::test(\App\Livewire\SuperAdmin\TataKelola\ManajemenKelas::class)
        ->call('openCreate')
        ->set('jenis_kelas', 'umum')
        ->set('tingkat', '1')
        ->set('nama_kelas', '1-Z-TEST')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('berhasil');

    $kelas = \App\Models\Kelas::where('nama_kelas', '1-Z-TEST')->first();
    expect($kelas)->not->toBeNull();
});
