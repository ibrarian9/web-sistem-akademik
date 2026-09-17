<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\JenisTagihan;
use App\Models\Tagihan;
use App\Livewire\Finance\ManajemenTagihan;
use App\Livewire\Finance\DetailTagihanSiswa;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    Tagihan::query()->forceDelete();
    Siswa::query()->forceDelete();
    Kelas::query()->forceDelete();
    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    $roleFinance = Role::where('nama', 'finance')->first();
    $roleSuperAdmin = Role::where('nama', 'super_admin')->first();
    $this->roleMurid = Role::where('nama', 'murid')->first();

    $this->financeUser = User::create([
        'nama' => 'Staff Keuangan',
        'username' => 'finance_test_one_time',
        'email' => 'finance_onetime@siakad.or.id',
        'password' => bcrypt('password123'),
        'role_id' => $roleFinance->id,
        'status' => 'aktif',
    ]);

    $this->superAdminUser = User::create([
        'nama' => 'Super Admin',
        'username' => 'superadmin_onetime',
        'email' => 'superadmin_onetime@siakad.or.id',
        'password' => bcrypt('password123'),
        'role_id' => $roleSuperAdmin->id,
        'status' => 'aktif',
    ]);

    TahunAjaran::query()->update(['status_aktif' => false]);
    $this->ta = TahunAjaran::create([
        'nama' => '2025/2026',
        'status_aktif' => true,
    ]);

    $this->semester = Semester::create([
        'tahun_ajaran_id' => $this->ta->id,
        'semester' => 'ganjil',
        'tanggal_mulai' => date('Y-m-d'),
        'tanggal_selesai' => date('Y-m-d', strtotime('+6 months')),
        'status_aktif' => true,
    ]);

    $this->kelas7A = Kelas::create(['nama_kelas' => '7A', 'tingkat' => 7, 'semester_id' => $this->semester->id]);

    $this->jenisUjianSemester = JenisTagihan::create([
        'nama' => 'Uang Ujian Semester',
        'kategori' => 'semester',
        'default_nominal' => 250000,
        'is_blocking' => true,
    ]);

    $this->jenisSeragam = JenisTagihan::create([
        'nama' => 'Uang Seragam',
        'kategori' => 'one_time',
        'default_nominal' => 450000,
        'is_blocking' => false,
    ]);

    $this->jenisSPP = JenisTagihan::create([
        'nama' => 'SPP',
        'kategori' => 'rutin',
        'default_nominal' => 300000,
        'is_blocking' => true,
    ]);

    // Create 2 active students in kelas 7A
    $u1 = User::create([
        'nama' => 'Ahmad Fulan',
        'username' => 'ahmad_fulan',
        'email' => 'ahmad@example.com',
        'password' => bcrypt('password123'),
        'role_id' => $this->roleMurid->id,
        'status' => 'aktif',
    ]);
    $this->siswa1 = Siswa::create([
        'user_id' => $u1->id,
        'nis' => '1001',
        'nisn' => '001001',
        'kelas_id' => $this->kelas7A->id,
        'status' => 'aktif',
        'tanggal_masuk' => date('Y-m-d'),
    ]);

    $u2 = User::create([
        'nama' => 'Budi Santoso',
        'username' => 'budi_santoso',
        'email' => 'budi@example.com',
        'password' => bcrypt('password123'),
        'role_id' => $this->roleMurid->id,
        'status' => 'aktif',
    ]);
    $this->siswa2 = Siswa::create([
        'user_id' => $u2->id,
        'nis' => '1002',
        'nisn' => '001002',
        'kelas_id' => $this->kelas7A->id,
        'status' => 'aktif',
        'tanggal_masuk' => date('Y-m-d'),
    ]);
});

test('selecting a semester or one_time tagihan category auto-switches periodeTipe to single', function () {
    $this->actingAs($this->financeUser);

    Livewire::test(ManajemenTagihan::class)
        ->set('periodeTipe', 'full_year_jan_des')
        ->set('jenis_tagihan_id', $this->jenisUjianSemester->id)
        ->assertSet('periodeTipe', 'single')
        ->assertSet('nominal', 250000.0);

    Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa1->id])
        ->set('periodeTipe', 'full_year_juli_juni')
        ->set('jenis_tagihan_id', $this->jenisSeragam->id)
        ->assertSet('periodeTipe', 'single')
        ->assertSet('nominal', 450000.0);
});

test('single mode allows choosing specific month and creates exactly 1 tagihan for the student', function () {
    $this->actingAs($this->financeUser);

    Livewire::test(ManajemenTagihan::class)
        ->call('openCreateModal', $this->siswa1->id)
        ->set('periodeTipe', 'single')
        ->set('bulan', 'Desember')
        ->set('jenis_tagihan_id', $this->jenisUjianSemester->id)
        ->set('nominal', 250000)
        ->call('createSingleTagihan')
        ->assertDispatched('show-alert');

    // Exactly 1 tagihan should be created for siswa1 with month 'Desember'
    $tagihans = Tagihan::where('siswa_id', $this->siswa1->id)->get();
    expect($tagihans->count())->toBe(1);
    expect($tagihans->first()->bulan)->toBe('Desember');
    expect((float) $tagihans->first()->nominal)->toBe(250000.0);
    // Desember in 2025/2026 is month 12 of 2025
    expect($tagihans->first()->jatuh_tempo->format('Y-m-d'))->toBe('2025-12-10');
});

test('single mode supports custom due date when user provides specific date', function () {
    $this->actingAs($this->financeUser);

    Livewire::test(ManajemenTagihan::class)
        ->call('openCreateModal', $this->siswa1->id)
        ->set('periodeTipe', 'single')
        ->set('bulan', 'Oktober')
        ->set('jatuh_tempo', '2025-10-25')
        ->set('jenis_tagihan_id', $this->jenisSeragam->id)
        ->set('nominal', 450000)
        ->call('createSingleTagihan')
        ->assertDispatched('show-alert');

    $tagihan = Tagihan::where('siswa_id', $this->siswa1->id)->first();
    expect($tagihan)->not->toBeNull();
    expect($tagihan->bulan)->toBe('Oktober');
    expect($tagihan->jatuh_tempo->format('Y-m-d'))->toBe('2025-10-25');
});

test('bulk mode with Satu Kali Bayar creates exactly 1 tagihan per student in class, not 6 or 12', function () {
    $this->actingAs($this->financeUser);

    Livewire::test(ManajemenTagihan::class)
        ->call('openCreateModal')
        ->set('releaseMode', 'bulk')
        ->set('bulkTarget', 'class')
        ->set('release_kelas_id', $this->kelas7A->id)
        ->set('periodeTipe', 'single')
        ->set('bulan', 'Januari')
        ->set('jenis_tagihan_id', $this->jenisUjianSemester->id)
        ->set('nominal', 250000)
        ->call('createBulkTagihan')
        ->assertHasNoErrors()
        ->assertDispatched('show-alert');

    // Both siswa1 and siswa2 in kelas 7A should each have EXACTLY 1 tagihan = 2 total
    expect(Tagihan::where('siswa_id', $this->siswa1->id)->count())->toBe(1);
    expect(Tagihan::where('siswa_id', $this->siswa2->id)->count())->toBe(1);
    expect(Tagihan::count())->toBe(2);

    $t1 = Tagihan::where('siswa_id', $this->siswa1->id)->first();
    expect($t1->bulan)->toBe('Januari');
    // Januari in 2025/2026 is month 1 of 2026
    expect($t1->jatuh_tempo->format('Y-m-d'))->toBe('2026-01-10');
});

test('setSingleMonthPreset helper switches month and updates due date accordingly', function () {
    $this->actingAs($this->financeUser);

    $component = Livewire::test(ManajemenTagihan::class)
        ->call('setSingleMonthPreset', 'Juli');

    expect($component->get('bulan'))->toBe('Juli');
    expect($component->get('periodeTipe'))->toBe('single');
    expect($component->get('jatuh_tempo'))->toBe('2025-07-10');

    $component->call('setSingleMonthPreset', 'Januari');
    expect($component->get('bulan'))->toBe('Januari');
    expect($component->get('jatuh_tempo'))->toBe('2026-01-10');
});

test('supports special semester label like Semester Ganjil with accurate due date calculation', function () {
    $this->actingAs($this->financeUser);

    $component = Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa1->id])
        ->call('openCreateModal')
        ->set('periodeTipe', 'single')
        ->set('bulan', 'Semester Ganjil')
        ->set('jenis_tagihan_id', $this->jenisUjianSemester->id)
        ->set('nominal', 200000)
        ->call('createTagihan')
        ->assertHasNoErrors();

    $tagihan = Tagihan::where('siswa_id', $this->siswa1->id)->first();
    expect($tagihan)->not->toBeNull();
    expect($tagihan->bulan)->toBe('Semester Ganjil');
    // Semester Ganjil maps to month 7 of 2025
    expect($tagihan->jatuh_tempo->format('Y-m-d'))->toBe('2025-07-10');
});
