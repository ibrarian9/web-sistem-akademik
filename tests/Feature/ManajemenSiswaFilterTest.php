<?php

use App\Livewire\SuperAdmin\TataKelola\ManajemenSiswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\User;
use App\Models\TahunAjaran;
use App\Models\Semester;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    $this->artisan('db:seed', ['--class' => 'PengaturanSeeder']);

    $roleTu = Role::firstOrCreate(['nama' => 'tata_usaha'], ['deskripsi' => 'Tata Usaha']);
    $roleGuru = Role::firstOrCreate(['nama' => 'guru'], ['deskripsi' => 'Guru']);
    $roleMurid = Role::firstOrCreate(['nama' => 'murid'], ['deskripsi' => 'Murid']);

    $this->userTu = User::factory()->create([
        'role_id' => $roleTu->id,
        'status' => 'aktif',
    ]);

    $this->tahunAjaran = TahunAjaran::firstOrCreate(['nama' => '2026/2027'], ['status_aktif' => true]);
    $this->semester = Semester::firstOrCreate(['tahun_ajaran_id' => $this->tahunAjaran->id, 'semester' => '1'], ['status_aktif' => true, 'tanggal_mulai' => now(), 'tanggal_selesai' => now()->addMonths(6)]);

    // Create Classes
    $this->kelas1A = Kelas::create([
        'nama_kelas' => '1A Testing',
        'jenis_kelas' => 'umum',
        'tingkat' => 1,
        'semester_id' => $this->semester->id,
    ]);

    $this->kelas2A = Kelas::create([
        'nama_kelas' => '2A Testing',
        'jenis_kelas' => 'umum',
        'tingkat' => 2,
        'semester_id' => $this->semester->id,
    ]);

    $this->kelasTahfidz = Kelas::create([
        'nama_kelas' => 'Halaqah Abu Bakar',
        'jenis_kelas' => 'tahfidz',
        'tingkat' => 1,
        'semester_id' => $this->semester->id,
    ]);

    // Create Shadow Teacher
    $userGpk = User::factory()->create([
        'nama' => 'Ustadz Pembimbing Khusus',
        'role_id' => $roleGuru->id,
        'status' => 'aktif',
    ]);
    $this->shadowTeacher = Guru::create([
        'user_id' => $userGpk->id,
        'nip' => '199901012025011001',
        'jenis_guru' => 'pendamping',
        'status_kepegawaian' => 'tetap',
        'tanggal_masuk' => now()->toDateString(),
        'status_aktif' => true,
    ]);

    // Create Student 1: Tingkat 1, Kelas 1A, Tahfidz, Laki-laki, Aktif, Inklusi
    $userSiswa1 = User::factory()->create([
        'nama' => 'Ahmad Santri Satu',
        'username' => 'ahmad123',
        'role_id' => $roleMurid->id,
    ]);
    $this->siswa1 = Siswa::create([
        'user_id' => $userSiswa1->id,
        'nis' => '1001',
        'nisn' => '0011223344',
        'jenis_kelamin' => 'L',
        'kelas_id' => $this->kelas1A->id,
        'kelas_tahfidz_id' => $this->kelasTahfidz->id,
        'shadow_teacher_id' => $this->shadowTeacher->id,
        'status' => 'aktif',
        'nama_wali' => 'Bapak Abdullah',
        'tanggal_masuk' => now()->toDateString(),
    ]);

    // Create Student 2: Tingkat 2, Kelas 2A, No Tahfidz, Perempuan, Lulus, Reguler
    $userSiswa2 = User::factory()->create([
        'nama' => 'Fatimah Santri Dua',
        'username' => 'fatimah456',
        'role_id' => $roleMurid->id,
    ]);
    $this->siswa2 = Siswa::create([
        'user_id' => $userSiswa2->id,
        'nis' => '2002',
        'nisn' => '0022334455',
        'jenis_kelamin' => 'P',
        'kelas_id' => $this->kelas2A->id,
        'kelas_tahfidz_id' => null,
        'shadow_teacher_id' => null,
        'status' => 'lulus',
        'nama_wali' => 'Ibu Khadijah',
        'tanggal_masuk' => now()->toDateString(),
    ]);

    // Create Student 3: Belum Masuk Kelas (kelas_id null), Pindah, Laki-laki
    $userSiswa3 = User::factory()->create([
        'nama' => 'Zubair Santri Tiga',
        'username' => 'zubair789',
        'role_id' => $roleMurid->id,
    ]);
    $this->siswa3 = Siswa::create([
        'user_id' => $userSiswa3->id,
        'nis' => '3003',
        'jenis_kelamin' => 'L',
        'kelas_id' => null,
        'kelas_tahfidz_id' => null,
        'shadow_teacher_id' => null,
        'status' => 'pindah',
        'nama_wali' => 'Bapak Awwam',
        'tanggal_masuk' => now()->toDateString(),
    ]);
});

test('manajemen siswa component renders filter controls properly', function () {
    $this->actingAs($this->userTu);

    Livewire::test(ManajemenSiswa::class)
        ->assertStatus(200)
        ->assertSee('Semua Tingkat')
        ->assertSee('Semua Kelas')
        ->assertSee('Semua Halaqah')
        ->assertSee('Semua Status')
        ->assertSee('Semua Gender')
        ->assertSee('Siswa Inklusi (Ada GPK)');
});

test('manajemen siswa filters by search keyword', function () {
    $this->actingAs($this->userTu);

    Livewire::test(ManajemenSiswa::class)
        ->set('search', 'Ahmad')
        ->assertSee('AHMAD SANTRI SATU')
        ->assertDontSee('FATIMAH SANTRI DUA')
        ->assertDontSee('ZUBAIR SANTRI TIGA');
});

test('manajemen siswa filters by tingkat kelas', function () {
    $this->actingAs($this->userTu);

    Livewire::test(ManajemenSiswa::class)
        ->set('filterTingkat', '1')
        ->assertSee('AHMAD SANTRI SATU')
        ->assertDontSee('FATIMAH SANTRI DUA')
        ->assertDontSee('ZUBAIR SANTRI TIGA');
});

test('manajemen siswa filters by kelas umum including belum set', function () {
    $this->actingAs($this->userTu);

    // Filter specific class
    Livewire::test(ManajemenSiswa::class)
        ->set('filterKelas', (string) $this->kelas2A->id)
        ->assertSee('FATIMAH SANTRI DUA')
        ->assertDontSee('AHMAD SANTRI SATU')
        ->assertDontSee('ZUBAIR SANTRI TIGA');

    // Filter unassigned class
    Livewire::test(ManajemenSiswa::class)
        ->set('filterKelas', 'belum_set')
        ->assertSee('ZUBAIR SANTRI TIGA')
        ->assertDontSee('AHMAD SANTRI SATU')
        ->assertDontSee('FATIMAH SANTRI DUA');
});

test('manajemen siswa filters by kelas tahfidz including belum set', function () {
    $this->actingAs($this->userTu);

    // Filter specific tahfidz class
    Livewire::test(ManajemenSiswa::class)
        ->set('filterKelasTahfidz', (string) $this->kelasTahfidz->id)
        ->assertSee('AHMAD SANTRI SATU')
        ->assertDontSee('FATIMAH SANTRI DUA')
        ->assertDontSee('ZUBAIR SANTRI TIGA');

    // Filter unassigned tahfidz class
    Livewire::test(ManajemenSiswa::class)
        ->set('filterKelasTahfidz', 'belum_set')
        ->assertSee('FATIMAH SANTRI DUA')
        ->assertSee('ZUBAIR SANTRI TIGA')
        ->assertDontSee('AHMAD SANTRI SATU');
});

test('manajemen siswa filters by student status', function () {
    $this->actingAs($this->userTu);

    Livewire::test(ManajemenSiswa::class)
        ->set('filterStatus', 'lulus')
        ->assertSee('FATIMAH SANTRI DUA')
        ->assertDontSee('AHMAD SANTRI SATU')
        ->assertDontSee('ZUBAIR SANTRI TIGA');

    Livewire::test(ManajemenSiswa::class)
        ->set('filterStatus', 'pindah')
        ->assertSee('ZUBAIR SANTRI TIGA')
        ->assertDontSee('AHMAD SANTRI SATU')
        ->assertDontSee('FATIMAH SANTRI DUA');
});

test('manajemen siswa filters by gender', function () {
    $this->actingAs($this->userTu);

    Livewire::test(ManajemenSiswa::class)
        ->set('filterJenisKelamin', 'P')
        ->assertSee('FATIMAH SANTRI DUA')
        ->assertDontSee('AHMAD SANTRI SATU')
        ->assertDontSee('ZUBAIR SANTRI TIGA');
});

test('manajemen siswa filters by shadow teacher or inklusi status', function () {
    $this->actingAs($this->userTu);

    // Filter Inklusi only
    Livewire::test(ManajemenSiswa::class)
        ->set('filterShadowTeacher', 'inklusi')
        ->assertSee('AHMAD SANTRI SATU')
        ->assertDontSee('FATIMAH SANTRI DUA')
        ->assertDontSee('ZUBAIR SANTRI TIGA');

    // Filter Reguler only
    Livewire::test(ManajemenSiswa::class)
        ->set('filterShadowTeacher', 'reguler')
        ->assertSee('FATIMAH SANTRI DUA')
        ->assertSee('ZUBAIR SANTRI TIGA')
        ->assertDontSee('AHMAD SANTRI SATU');

    // Filter specific shadow teacher ID
    Livewire::test(ManajemenSiswa::class)
        ->set('filterShadowTeacher', (string) $this->shadowTeacher->id)
        ->assertSee('AHMAD SANTRI SATU')
        ->assertDontSee('FATIMAH SANTRI DUA');
});

test('manajemen siswa can reset filters and individual filter chips', function () {
    $this->actingAs($this->userTu);

    $component = Livewire::test(ManajemenSiswa::class)
        ->set('search', 'Ahmad')
        ->set('filterTingkat', '1')
        ->set('filterStatus', 'aktif');

    expect($component->get('activeFilterCount'))->toBe(3);

    // Reset single filter
    $component->call('resetFilter', 'filterStatus');
    expect($component->get('filterStatus'))->toBe('')
        ->and($component->get('activeFilterCount'))->toBe(2);

    // Reset all filters
    $component->call('resetFilters');
    expect($component->get('search'))->toBe('')
        ->and($component->get('filterTingkat'))->toBe('')
        ->and($component->get('filterStatus'))->toBe('')
        ->and($component->get('activeFilterCount'))->toBe(0);
});
