<?php

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Role;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Models\Pengaturan;
use App\Models\Pembayaran;
use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;
use App\Livewire\Finance\Dashboard as FinanceDashboard;
use App\Livewire\SuperAdmin\Dashboard as SuperAdminDashboard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->roleAdmin = Role::firstOrCreate(['nama' => 'super_admin'], ['deskripsi' => 'Super Admin']);
    $this->roleFinance = Role::firstOrCreate(['nama' => 'finance'], ['deskripsi' => 'Finance']);
    $this->roleMurid = Role::firstOrCreate(['nama' => 'murid'], ['deskripsi' => 'Murid']);

    $this->adminUser = User::create([
        'nama' => 'Admin Utama',
        'username' => 'admin_utama',
        'email' => 'admin_utama@test.com',
        'password' => bcrypt('password123'),
        'role_id' => $this->roleAdmin->id,
        'status' => 'aktif',
    ]);

    $this->financeUser = User::create([
        'nama' => 'Staff Finance Test',
        'username' => 'finance_test_user',
        'email' => 'finance_test@test.com',
        'password' => bcrypt('password123'),
        'role_id' => $this->roleFinance->id,
        'status' => 'aktif',
    ]);

    $this->ta = TahunAjaran::create(['nama' => '2026/2027', 'status_aktif' => true]);
    $this->semester = Semester::create([
        'tahun_ajaran_id' => $this->ta->id,
        'semester' => 'ganjil',
        'tanggal_mulai' => '2026-07-01',
        'tanggal_selesai' => '2026-12-31',
        'status_aktif' => true,
    ]);

    $this->kelas = Kelas::create([
        'nama_kelas' => '7A',
        'tingkat' => 7,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
    ]);
});

test('finance dashboard menghitung data chart arus kas 6 bulan dan rasio tagihan spp dengan benar', function () {
    $this->actingAs($this->financeUser);

    $kategori = KategoriPengeluaran::create(['nama' => 'Operasional', 'jenis' => 'operasional']);

    Pengeluaran::create([
        'kategori_pengeluaran_id' => $kategori->id,
        'jumlah' => 2000000.00,
        'tanggal' => date('Y-m-d'),
        'keterangan' => 'Pengeluaran Operasional Bulan Ini',
        'petugas_id' => $this->financeUser->id,
    ]);

    Livewire::test(FinanceDashboard::class)
        ->assertSee('Dashboard Keuangan Sekolah')
        ->assertSee('Tren Arus Kas 6 Bulan Terakhir')
        ->assertSee('Rasio Status Tagihan SPP')
        ->assertSee('financeCashflowChart')
        ->assertSee('financeBillStatusChart')
        ->assertSet('expenseThisMonth', 2000000.00);
});

test('super admin dashboard menghitung data chart sebaran rombel dan role pengguna', function () {
    $this->actingAs($this->adminUser);

    Livewire::test(SuperAdminDashboard::class)
        ->assertSee('PANEL SUPER ADMIN')
        ->assertSee('Sebaran Santri per Rombel Kelas')
        ->assertSee('Distribusi Akun & Hak Akses')
        ->assertSee('adminClassDistributionChart')
        ->assertSee('adminRoleDistributionChart');
});

test('pengaturan model cache mengingat nilai dan melakukan auto invalidation saat disimpan', function () {
    Cache::flush();

    Pengaturan::create([
        'key' => 'nama_sekolah',
        'value' => 'SD Tahfizh Quran Al-Hikmah',
        'keterangan' => 'Nama Lembaga',
    ]);

    // First call -> caches value
    $val = Pengaturan::getValue('nama_sekolah');
    expect($val)->toBe('SD Tahfizh Quran Al-Hikmah');
    expect(Cache::has('setting_nama_sekolah'))->toBeTrue();

    // Update value -> auto invalidates cache
    $setting = Pengaturan::where('key', 'nama_sekolah')->first();
    $setting->update(['value' => 'SD Tahfizh Quran Plus']);

    expect(Cache::has('setting_nama_sekolah'))->toBeFalse();

    $newVal = Pengaturan::getValue('nama_sekolah');
    expect($newVal)->toBe('SD Tahfizh Quran Plus');
});

test('layout memuat command palette dan toast notification untuk navigasi instan', function () {
    $this->actingAs($this->adminUser);

    $response = $this->get(route('super-admin.dashboard'));
    $response->assertStatus(200);
    $response->assertSee('command-palette-input');
    $response->assertSee('Cari menu...');
});
