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
use App\Livewire\Finance\InputPembayaran;
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
        'username' => 'finance_test_yearly',
        'email' => 'finance_yearly@siakad.or.id',
        'password' => bcrypt('password123'),
        'role_id' => $roleFinance->id,
        'status' => 'aktif',
    ]);

    $this->superAdminUser = User::create([
        'nama' => 'Super Admin',
        'username' => 'superadmin_yearly',
        'email' => 'superadmin_yearly@siakad.or.id',
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

    $this->jenisSPP = JenisTagihan::create([
        'nama' => 'SPP',
        'kategori' => 'rutin',
        'default_nominal' => 350000,
        'is_blocking' => true,
    ]);

    $this->jenisGedung = JenisTagihan::create([
        'nama' => 'Uang Pembangunan',
        'kategori' => 'one_time',
        'default_nominal' => 1500000,
        'is_blocking' => true,
    ]);

    $this->jenisSeragam = JenisTagihan::create([
        'nama' => 'Uang Seragam',
        'kategori' => 'one_time',
        'default_nominal' => 500000,
        'is_blocking' => false,
    ]);

    // Create Student 1
    $u1 = User::create([
        'nama' => 'Fulan bin Ahmad',
        'username' => 'fulan_ahmad',
        'email' => 'fulan@siakad.or.id',
        'password' => bcrypt('password123'),
        'role_id' => $this->roleMurid->id,
        'status' => 'aktif',
    ]);
    $this->siswa1 = Siswa::create([
        'user_id' => $u1->id,
        'kelas_id' => $this->kelas7A->id,
        'nis' => '10001',
        'nisn' => '2000000001',
        'status' => 'aktif',
        'tanggal_masuk' => '2025-07-01',
    ]);

    // Create Student 2
    $u2 = User::create([
        'nama' => 'Zaid bin Tsabit',
        'username' => 'zaid_tsabit',
        'email' => 'zaid@siakad.or.id',
        'password' => bcrypt('password123'),
        'role_id' => $this->roleMurid->id,
        'status' => 'aktif',
    ]);
    $this->siswa2 = Siswa::create([
        'user_id' => $u2->id,
        'kelas_id' => $this->kelas7A->id,
        'nis' => '10002',
        'nisn' => '2000000002',
        'status' => 'aktif',
        'tanggal_masuk' => '2025-07-01',
    ]);
});

test('single mode can generate 12 months SPP (Januari - Desember) with 1 nominal input and duplicate protection', function () {
    $this->actingAs($this->financeUser);

    Livewire::test(ManajemenTagihan::class)
        ->call('openCreateModal', $this->siswa1->id)
        ->set('periodeTipe', 'full_year_jan_des')
        ->set('jenis_tagihan_id', $this->jenisSPP->id)
        ->set('nominal', 350000)
        ->set('jatuh_tempo', '2026-01-10')
        ->call('createSingleTagihan')
        ->assertHasNoErrors()
        ->assertDispatched('show-alert');

    // Verify 12 bills are created for student 1
    $tagihans = Tagihan::where('siswa_id', $this->siswa1->id)->get();
    expect($tagihans)->toHaveCount(12);

    $monthsExpected = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    foreach ($monthsExpected as $m) {
        $found = $tagihans->firstWhere('bulan', $m);
        expect($found)->not->toBeNull();
        expect((float) $found->nominal)->toEqual(350000.0);
        expect($found->status)->toEqual('belum_bayar');
    }

    // Call again to test duplicate protection: should not create duplicates
    Livewire::test(ManajemenTagihan::class)
        ->call('openCreateModal', $this->siswa1->id)
        ->set('periodeTipe', 'full_year_jan_des')
        ->set('jenis_tagihan_id', $this->jenisSPP->id)
        ->set('nominal', 350000)
        ->set('jatuh_tempo', '2026-01-10')
        ->call('createSingleTagihan')
        ->assertDispatched('show-alert');

    expect(Tagihan::where('siswa_id', $this->siswa1->id)->count())->toBe(12);
});

test('bulk mode can generate 12 months SPP for multiple students with single nominal input', function () {
    $this->actingAs($this->financeUser);

    Livewire::test(ManajemenTagihan::class)
        ->call('openCreateModal')
        ->set('releaseMode', 'bulk')
        ->set('bulkTarget', 'class')
        ->set('release_kelas_id', $this->kelas7A->id)
        ->set('periodeTipe', 'full_year_jan_des')
        ->set('jenis_tagihan_id', $this->jenisSPP->id)
        ->set('nominal', 300000)
        ->set('jatuh_tempo', '2026-01-10')
        ->call('createBulkTagihan')
        ->assertHasNoErrors()
        ->assertDispatched('show-alert');

    // Both siswa1 and siswa2 in kelas 7A should each have 12 bills = 24 total
    expect(Tagihan::where('siswa_id', $this->siswa1->id)->count())->toBe(12);
    expect(Tagihan::where('siswa_id', $this->siswa2->id)->count())->toBe(12);
    expect(Tagihan::count())->toBe(24);
});

test('DetailTagihanSiswa displays 12-month SPP matrix and both SPP and non-SPP bills', function () {
    $this->actingAs($this->financeUser);

    // Create 12 months SPP for siswa1
    foreach (['Januari', 'Februari', 'Maret'] as $b) {
        Tagihan::create([
            'siswa_id' => $this->siswa1->id,
            'tahun_ajaran_id' => $this->ta->id,
            'jenis_tagihan_id' => $this->jenisSPP->id,
            'bulan' => $b,
            'nominal' => 350000,
            'total_dibayar' => ($b === 'Januari' ? 350000 : 0),
            'status' => ($b === 'Januari' ? 'lunas' : 'belum_bayar'),
            'jatuh_tempo' => '2026-01-10',
        ]);
    }

    // Create non-SPP bills for siswa1
    Tagihan::create([
        'siswa_id' => $this->siswa1->id,
        'tahun_ajaran_id' => $this->ta->id,
        'jenis_tagihan_id' => $this->jenisGedung->id,
        'bulan' => 'Tahunan',
        'nominal' => 1500000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => '2026-01-10',
    ]);
    Tagihan::create([
        'siswa_id' => $this->siswa1->id,
        'tahun_ajaran_id' => $this->ta->id,
        'jenis_tagihan_id' => $this->jenisSeragam->id,
        'bulan' => 'Tahunan',
        'nominal' => 500000,
        'total_dibayar' => 500000,
        'status' => 'lunas',
        'jatuh_tempo' => '2026-01-10',
    ]);

    // Test component rendering
    $test = Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa1->id])
        ->assertSee('Matriks Status SPP 12 Bulan')
        ->assertSee('Semua Tagihan (5)')
        ->assertSee('Tagihan SPP Bulanan (3)')
        ->assertSee('Non-SPP dan Biaya Lainnya (2)')
        ->assertSee('Uang Pembangunan')
        ->assertSee('Uang Seragam')
        ->assertSee('SPP');

    // Test category tab filter to SPP only
    $test->call('setCategoryTab', 'spp')
        ->assertSet('activeCategoryTab', 'spp');
    $tagihansInView = $test->viewData('tagihans');
    expect($tagihansInView->count())->toBe(3);
    foreach ($tagihansInView as $item) {
        expect($item->jenisTagihan->nama)->toContain('SPP');
    }

    // Test category tab filter to Non-SPP only
    $test->call('setCategoryTab', 'non_spp')
        ->assertSet('activeCategoryTab', 'non_spp');
    $nonSppInView = $test->viewData('tagihans');
    expect($nonSppInView->count())->toBe(2);
    foreach ($nonSppInView as $item) {
        expect($item->jenisTagihan->nama)->not->toContain('SPP');
    }

    // Test reset filter returns to all
    $test->call('resetFilters')
        ->assertSet('activeCategoryTab', 'all');
    expect($test->viewData('tagihans')->count())->toBe(5);
});

test('quick detail modal in ManajemenTagihan displays all bills of a student', function () {
    $this->actingAs($this->financeUser);

    Tagihan::create([
        'siswa_id' => $this->siswa1->id,
        'tahun_ajaran_id' => $this->ta->id,
        'jenis_tagihan_id' => $this->jenisSPP->id,
        'bulan' => 'Januari',
        'nominal' => 350000,
        'total_dibayar' => 350000,
        'status' => 'lunas',
        'jatuh_tempo' => '2026-01-10',
    ]);
    Tagihan::create([
        'siswa_id' => $this->siswa1->id,
        'tahun_ajaran_id' => $this->ta->id,
        'jenis_tagihan_id' => $this->jenisGedung->id,
        'bulan' => 'Tahunan',
        'nominal' => 1500000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => '2026-01-10',
    ]);

    Livewire::test(ManajemenTagihan::class)
        ->call('openQuickDetail', $this->siswa1->id)
        ->assertSet('showQuickDetailModal', true)
        ->assertSee('Rincian Seluruh Tagihan Siswa')
        ->assertSee('SPP')
        ->assertSee('Uang Pembangunan')
        ->call('closeQuickDetailModal')
        ->assertSet('showQuickDetailModal', false);
});

test('InputPembayaran loads all unpaid bills for selected student and allows switching', function () {
    $this->actingAs($this->financeUser);

    $tSpp = Tagihan::create([
        'siswa_id' => $this->siswa1->id,
        'tahun_ajaran_id' => $this->ta->id,
        'jenis_tagihan_id' => $this->jenisSPP->id,
        'bulan' => 'Februari',
        'nominal' => 350000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => '2026-02-10',
    ]);

    $tGedung = Tagihan::create([
        'siswa_id' => $this->siswa1->id,
        'tahun_ajaran_id' => $this->ta->id,
        'jenis_tagihan_id' => $this->jenisGedung->id,
        'bulan' => 'Tahunan',
        'nominal' => 1500000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => '2026-01-10',
    ]);

    Livewire::test(InputPembayaran::class)
        ->call('pilihSiswaAndTagihan', $this->siswa1->id, $tSpp->id)
        ->assertSet('siswa_id', $this->siswa1->id)
        ->assertSet('tagihan_id', $tSpp->id)
        ->assertSee('2 Tagihan Belum Lunas')
        ->assertSee('SPP')
        ->assertSee('Uang Pembangunan')
        ->call('switchTagihan', $tGedung->id)
        ->assertSet('tagihan_id', $tGedung->id)
        ->assertSet('nominal_dibayar', 1500000.0);
});

test('ManajemenTagihan can release tagihan with custom month range and skip existing duplicates', function () {
    $this->actingAs($this->financeUser);

    // Release custom range Juli s.d. Desember (6 bulan) untuk Siswa 1
    Livewire::test(ManajemenTagihan::class)
        ->call('openCreateModal', $this->siswa1->id)
        ->set('periodeTipe', 'custom_range')
        ->set('bulan_mulai', 'Juli')
        ->set('bulan_selesai', 'Desember')
        ->set('jenis_tagihan_id', $this->jenisSPP->id)
        ->set('nominal', '350.000')
        ->set('jatuh_tempo', '2025-07-10')
        ->call('createSingleTagihan')
        ->assertHasNoErrors()
        ->assertSee('Berhasil menerbitkan 6 tagihan (Juli - Desember)');

    $bills = Tagihan::where('siswa_id', $this->siswa1->id)
        ->where('jenis_tagihan_id', $this->jenisSPP->id)
        ->get();

    expect($bills)->toHaveCount(6);
    expect($bills->pluck('bulan')->toArray())->toEqual([
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ]);

    // Verifikasi jatuh tempo tiap bulan: Juli 2025 hingga Desember 2025
    $julyBill = $bills->firstWhere('bulan', 'Juli');
    $decBill = $bills->firstWhere('bulan', 'Desember');
    expect(date('Y-m-d', strtotime($julyBill->jatuh_tempo)))->toBe('2025-07-10');
    expect(date('Y-m-d', strtotime($decBill->jatuh_tempo)))->toBe('2025-12-10');

    // Coba rilis ulang rentang yang sama -> harus aman (skip all existing, no crash)
    Livewire::test(ManajemenTagihan::class)
        ->call('openCreateModal', $this->siswa1->id)
        ->set('periodeTipe', 'custom_range')
        ->set('bulan_mulai', 'Juli')
        ->set('bulan_selesai', 'Desember')
        ->set('jenis_tagihan_id', $this->jenisSPP->id)
        ->set('nominal', '350.000')
        ->set('jatuh_tempo', '2025-07-10')
        ->call('createSingleTagihan')
        ->assertHasNoErrors()
        ->assertSee('Tidak ada tagihan baru dibuat. Siswa ini sudah memiliki tagihan');

    // Total tagihan tetap 6, tidak terduplikasi
    expect(Tagihan::where('siswa_id', $this->siswa1->id)->where('jenis_tagihan_id', $this->jenisSPP->id)->count())->toBe(6);
});

test('ManajemenTagihan can release bulk custom range for class with accurate academic year transition', function () {
    $this->actingAs($this->financeUser);

    // Rilis massal untuk Kelas 7A (siswa 1 & siswa 2) rentang Januari s.d. Juni (Semester Genap = 6 bulan)
    Livewire::test(ManajemenTagihan::class)
        ->call('openCreateModal')
        ->set('releaseMode', 'bulk')
        ->set('bulkTarget', 'class')
        ->set('release_kelas_id', $this->kelas7A->id)
        ->set('periodeTipe', 'custom_range')
        ->call('setPresetRange', 'Januari', 'Juni')
        ->set('jenis_tagihan_id', $this->jenisSPP->id)
        ->set('nominal', '350.000')
        ->set('jatuh_tempo', '2025-07-15') // Tanggal 15
        ->call('createBulkTagihan')
        ->assertHasNoErrors()
        ->assertSee('Berhasil merilis 12 data tagihan'); // 2 siswa x 6 bulan = 12 tagihan

    $janBillSiswa1 = Tagihan::where('siswa_id', $this->siswa1->id)
        ->where('jenis_tagihan_id', $this->jenisSPP->id)
        ->where('bulan', 'Januari')
        ->first();

    // Karena jatuh tempo sekarang fix tanggal 10 setiap bulannya, bulan Januari TA 2025/2026 jatuh pada 2026-01-10
    expect(date('Y-m-d', strtotime($janBillSiswa1->jatuh_tempo)))->toBe('2026-01-10');
});

test('DetailTagihanSiswa can create tagihan with custom month range', function () {
    $this->actingAs($this->financeUser);

    Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa2->id])
        ->call('openCreateModal')
        ->set('periodeTipe', 'custom_range')
        ->set('bulan_mulai', 'Juli')
        ->set('bulan_selesai', 'September')
        ->set('jenis_tagihan_id', $this->jenisSPP->id)
        ->set('nominal', '400.000')
        ->set('jatuh_tempo', '2025-07-20')
        ->call('createTagihan')
        ->assertHasNoErrors()
        ->assertSee('Berhasil menerbitkan 3 tagihan (Juli - September)');

    $bills = Tagihan::where('siswa_id', $this->siswa2->id)
        ->where('jenis_tagihan_id', $this->jenisSPP->id)
        ->get();

    expect($bills)->toHaveCount(3);
    expect($bills->pluck('bulan')->toArray())->toEqual(['Juli', 'Agustus', 'September']);
    expect((float) $bills->first()->nominal)->toBe(400000.0);
    expect(date('Y-m-d', strtotime($bills->first()->jatuh_tempo)))->toBe('2025-07-10');
});

test('Rilis tagihan does not require jatuh_tempo input and automatically fixes due date to 10th of every month', function () {
    $this->actingAs($this->financeUser);

    $user3 = User::create([
        'nama' => 'Siswa Tiga',
        'username' => 'siswa_3_fixed',
        'email' => 'siswa3_fixed@siakad.or.id',
        'password' => bcrypt('password123'),
        'role_id' => $this->roleMurid->id,
        'status' => 'aktif',
    ]);
    $siswa3 = Siswa::create([
        'user_id' => $user3->id,
        'kelas_id' => $this->kelas7A->id,
        'nis' => '999903',
        'status' => 'aktif',
        'tanggal_masuk' => '2025-07-01',
    ]);

    // Rilis tanpa input kolom jatuh_tempo
    Livewire::test(ManajemenTagihan::class)
        ->call('openCreateModal', $siswa3->id)
        ->set('periodeTipe', 'single')
        ->set('bulan', 'Oktober')
        ->set('jenis_tagihan_id', $this->jenisSPP->id)
        ->set('nominal', '300.000')
        ->call('createSingleTagihan')
        ->assertHasNoErrors();

    $bill = Tagihan::where('siswa_id', $siswa3->id)->where('bulan', 'Oktober')->first();
    expect($bill)->not->toBeNull();
    expect(date('Y-m-d', strtotime($bill->jatuh_tempo)))->toBe('2025-10-10');
});


