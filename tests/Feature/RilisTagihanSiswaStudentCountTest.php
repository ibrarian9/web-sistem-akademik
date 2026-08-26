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

    $roleSuperAdmin = Role::where('nama', 'super_admin')->first();
    $roleFinance = Role::where('nama', 'finance')->first();
    $roleTU = Role::where('nama', 'tata_usaha')->first();
    $this->roleMurid = Role::where('nama', 'murid')->first();

    $this->tuUser = User::create([
        'nama' => 'Staff Tata Usaha',
        'username' => 'staff_tu_test',
        'email' => 'tu_rilis@siakad.or.id',
        'password' => bcrypt('password123'),
        'role_id' => $roleTU->id,
        'status' => 'aktif',
    ]);

    $this->financeUser = User::create([
        'nama' => 'Staff Keuangan',
        'username' => 'staff_finance_test',
        'email' => 'finance_rilis@siakad.or.id',
        'password' => bcrypt('password123'),
        'role_id' => $roleFinance->id,
        'status' => 'aktif',
    ]);

    $this->ta = TahunAjaran::create([
        'nama' => '2026/2027',
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
    $this->kelas7B = Kelas::create(['nama_kelas' => '7B', 'tingkat' => 7, 'semester_id' => $this->semester->id]);

    $this->jenisSPP = JenisTagihan::create([
        'nama' => 'SPP Bulanan',
        'default_nominal' => 350000.00,
    ]);
});

test('modal rilis tagihan (semua siswa) menampilkan estimasi jumlah yang persis sama dengan total murid aktif di tata usaha', function () {
    // 1. Tata Usaha inputs 8 active students (5 in 7A, 3 in 7B)
    for ($i = 1; $i <= 5; $i++) {
        $u = User::create([
            'nama' => "Santri 7A-{$i}",
            'username' => "santri_7a_{$i}",
            'email' => "santri7a{$i}@test.com",
            'password' => bcrypt('password123'),
            'role_id' => $this->roleMurid->id,
            'status' => 'aktif',
        ]);
        Siswa::create([
            'user_id' => $u->id,
            'nis' => "700{$i}",
            'nisn' => "000700{$i}",
            'kelas_id' => $this->kelas7A->id,
            'nama_wali' => "Wali 7A-{$i}",
            'no_hp_wali' => '08123456789',
            'status' => 'aktif',
            'tanggal_masuk' => date('Y-m-d'),
        ]);
    }

    for ($i = 6; $i <= 8; $i++) {
        $u = User::create([
            'nama' => "Santri 7B-{$i}",
            'username' => "santri_7b_{$i}",
            'email' => "santri7b{$i}@test.com",
            'password' => bcrypt('password123'),
            'role_id' => $this->roleMurid->id,
            'status' => 'aktif',
        ]);
        Siswa::create([
            'user_id' => $u->id,
            'nis' => "700{$i}",
            'nisn' => "000700{$i}",
            'kelas_id' => $this->kelas7B->id,
            'nama_wali' => "Wali 7B-{$i}",
            'no_hp_wali' => '08123456789',
            'status' => 'aktif',
            'tanggal_masuk' => date('Y-m-d'),
        ]);
    }

    $totalAktifTU = Siswa::where('status', 'aktif')->count();
    expect($totalAktifTU)->toBe(8);

    // 2. Finance opens modal Rilis Tagihan with target 'all'
    $this->actingAs($this->financeUser);

    Livewire::test(ManajemenTagihan::class)
        ->set('showCreateModal', true)
        ->set('releaseMode', 'bulk')
        ->set('bulkTarget', 'all')
        ->assertSee('8 Siswa')
        ->assertSee('Terbitkan Tagihan (8 Siswa)')
        ->set('jenis_tagihan_id', $this->jenisSPP->id)
        ->set('bulan', 'Juli')
        ->set('nominal', 350000.00)
        ->set('jatuh_tempo', date('Y-m-d', strtotime('+30 days')))
        ->call('createBulkTagihan')
        ->assertHasNoErrors();

    // Verify exactly 8 tagihans were created
    expect(Tagihan::where('jenis_tagihan_id', $this->jenisSPP->id)->where('bulan', 'Juli')->count())
        ->toBe(8);
});

test('modal rilis tagihan (target per kelas) menampilkan jumlah siswa sesuai dengan plotting rombel kelas tata usaha', function () {
    // 1. Tata Usaha plots 4 students in 7A and 2 students in 7B
    for ($i = 1; $i <= 4; $i++) {
        $u = User::create([
            'nama' => "Siswa Rombel 7A-{$i}",
            'username' => "rombel_7a_{$i}",
            'email' => "rombel7a{$i}@test.com",
            'password' => bcrypt('password123'),
            'role_id' => $this->roleMurid->id,
            'status' => 'aktif',
        ]);
        Siswa::create([
            'user_id' => $u->id,
            'nis' => "810{$i}",
            'nisn' => "000810{$i}",
            'kelas_id' => $this->kelas7A->id,
            'nama_wali' => "Wali {$i}",
            'no_hp_wali' => '08123456789',
            'status' => 'aktif',
            'tanggal_masuk' => date('Y-m-d'),
        ]);
    }

    for ($i = 5; $i <= 6; $i++) {
        $u = User::create([
            'nama' => "Siswa Rombel 7B-{$i}",
            'username' => "rombel_7b_{$i}",
            'email' => "rombel7b{$i}@test.com",
            'password' => bcrypt('password123'),
            'role_id' => $this->roleMurid->id,
            'status' => 'aktif',
        ]);
        Siswa::create([
            'user_id' => $u->id,
            'nis' => "810{$i}",
            'nisn' => "000810{$i}",
            'kelas_id' => $this->kelas7B->id,
            'nama_wali' => "Wali {$i}",
            'no_hp_wali' => '08123456789',
            'status' => 'aktif',
            'tanggal_masuk' => date('Y-m-d'),
        ]);
    }

    $this->actingAs($this->financeUser);

    // 2. Select Class 7A -> displays 4 students
    Livewire::test(ManajemenTagihan::class)
        ->set('showCreateModal', true)
        ->set('releaseMode', 'bulk')
        ->set('bulkTarget', 'class')
        ->set('release_kelas_id', $this->kelas7A->id)
        ->assertSee('4 Siswa')
        ->assertSee('Terbitkan Tagihan (4 Siswa)')
        ->set('jenis_tagihan_id', $this->jenisSPP->id)
        ->set('bulan', 'Agustus')
        ->set('nominal', 350000.00)
        ->set('jatuh_tempo', date('Y-m-d', strtotime('+30 days')))
        ->call('createBulkTagihan')
        ->assertHasNoErrors();

    // Invoices for Class 7A created (4), Class 7B untouched (0)
    $tagihan7A = Tagihan::whereHas('siswa', fn($q) => $q->where('kelas_id', $this->kelas7A->id))->where('bulan', 'Agustus')->count();
    $tagihan7B = Tagihan::whereHas('siswa', fn($q) => $q->where('kelas_id', $this->kelas7B->id))->where('bulan', 'Agustus')->count();

    expect($tagihan7A)->toBe(4);
    expect($tagihan7B)->toBe(0);

    // 3. Select Class 7B -> displays 2 students
    Livewire::test(ManajemenTagihan::class)
        ->set('showCreateModal', true)
        ->set('releaseMode', 'bulk')
        ->set('bulkTarget', 'class')
        ->set('release_kelas_id', $this->kelas7B->id)
        ->assertSee('2 Siswa')
        ->assertSee('Terbitkan Tagihan (2 Siswa)');
});

test('siswa non-aktif (lulus, pindah, keluar) otomatis dikecualikan dari estimasi dan rilis tagihan', function () {
    // 3 Active students
    for ($i = 1; $i <= 3; $i++) {
        $u = User::create([
            'nama' => "Siswa Aktif {$i}",
            'username' => "siswa_aktif_test_{$i}",
            'email' => "aktif_test{$i}@test.com",
            'password' => bcrypt('password123'),
            'role_id' => $this->roleMurid->id,
            'status' => 'aktif',
        ]);
        Siswa::create([
            'user_id' => $u->id,
            'nis' => "900{$i}",
            'nisn' => "000900{$i}",
            'kelas_id' => $this->kelas7A->id,
            'nama_wali' => "Wali {$i}",
            'no_hp_wali' => '08123456789',
            'status' => 'aktif',
            'tanggal_masuk' => date('Y-m-d'),
        ]);
    }

    // 1 Lulus, 1 Pindah, 1 Keluar
    $statuses = ['lulus', 'pindah', 'keluar'];
    foreach ($statuses as $idx => $st) {
        $u = User::create([
            'nama' => "Siswa Status {$st}",
            'username' => "siswa_status_{$st}",
            'email' => "status_{$st}@test.com",
            'password' => bcrypt('password123'),
            'role_id' => $this->roleMurid->id,
            'status' => 'nonaktif',
        ]);
        Siswa::create([
            'user_id' => $u->id,
            'nis' => "910{$idx}",
            'nisn' => "000910{$idx}",
            'kelas_id' => $this->kelas7A->id,
            'nama_wali' => "Wali {$st}",
            'no_hp_wali' => '08123456789',
            'status' => $st,
            'tanggal_masuk' => date('Y-m-d'),
        ]);
    }

    expect(Siswa::count())->toBe(6);
    expect(Siswa::where('status', 'aktif')->count())->toBe(3);

    $this->actingAs($this->financeUser);

    // Modal shows exactly 3 active students
    Livewire::test(ManajemenTagihan::class)
        ->set('showCreateModal', true)
        ->set('releaseMode', 'bulk')
        ->set('bulkTarget', 'all')
        ->assertSee('3 Siswa')
        ->assertSee('Terbitkan Tagihan (3 Siswa)')
        ->set('jenis_tagihan_id', $this->jenisSPP->id)
        ->set('bulan', 'September')
        ->set('nominal', 350000.00)
        ->set('jatuh_tempo', date('Y-m-d', strtotime('+30 days')))
        ->call('createBulkTagihan')
        ->assertHasNoErrors();

    // Verify only the 3 active students received the bill
    $billedStudents = Tagihan::where('bulan', 'September')->pluck('siswa_id')->toArray();
    expect(count($billedStudents))->toBe(3);

    foreach (Siswa::where('status', '!=', 'aktif')->get() as $inactiveStudent) {
        expect(in_array($inactiveStudent->id, $billedStudents))->toBeFalse();
    }
});

test('fitur cari dan tambah siswa lintas kelas menampilkan data lengkap nama nis kelas dan menerbitkan tagihan sesuai pilihan', function () {
    // 1. Create students across Class 7A and 7B
    $u1 = User::create([
        'nama' => 'Ahmad Santri 7A',
        'username' => 'ahmad_7a',
        'email' => 'ahmad7a@test.com',
        'password' => bcrypt('password123'),
        'role_id' => $this->roleMurid->id,
        'status' => 'aktif',
    ]);
    $siswa1 = Siswa::create([
        'user_id' => $u1->id,
        'nis' => '7101',
        'nisn' => '0007101',
        'kelas_id' => $this->kelas7A->id,
        'nama_wali' => 'Wali Ahmad',
        'no_hp_wali' => '08123456789',
        'status' => 'aktif',
        'tanggal_masuk' => date('Y-m-d'),
    ]);

    $u2 = User::create([
        'nama' => 'Bambang Santri 7B',
        'username' => 'bambang_7b',
        'email' => 'bambang7b@test.com',
        'password' => bcrypt('password123'),
        'role_id' => $this->roleMurid->id,
        'status' => 'aktif',
    ]);
    $siswa2 = Siswa::create([
        'user_id' => $u2->id,
        'nis' => '7201',
        'nisn' => '0007201',
        'kelas_id' => $this->kelas7B->id,
        'nama_wali' => 'Wali Bambang',
        'no_hp_wali' => '08123456789',
        'status' => 'aktif',
        'tanggal_masuk' => date('Y-m-d'),
    ]);

    $this->actingAs($this->financeUser);

    // 2. Open Custom Lintas Kelas mode and verify autocomplete shows full details (Nama, NIS, Kelas)
    Livewire::test(ManajemenTagihan::class)
        ->set('showCreateModal', true)
        ->set('releaseMode', 'bulk')
        ->set('bulkTarget', 'custom')
        ->set('bulkSearchStudent', 'Ahmad')
        ->assertSee('Ahmad Santri 7A')
        ->assertSee('NIS: 7101 • Kelas 7A')
        ->call('addSiswaToBulk', $siswa1->id)
        ->set('bulkSearchStudent', 'Bambang')
        ->assertSee('Bambang Santri 7B')
        ->assertSee('NIS: 7201 • Kelas 7B')
        ->call('addSiswaToBulk', $siswa2->id)
        // Verify 2 students selected
        ->assertSee('Daftar Siswa Dipilih (2 Siswa)')
        ->assertSee('2 Siswa')
        ->assertSee('Terbitkan Tagihan (2 Siswa)')
        // Release bill
        ->set('jenis_tagihan_id', $this->jenisSPP->id)
        ->set('bulan', 'Oktober')
        ->set('nominal', 400000.00)
        ->set('jatuh_tempo', date('Y-m-d', strtotime('+30 days')))
        ->call('createBulkTagihan')
        ->assertHasNoErrors();

    // 3. Verify exactly 2 tagihans created for the 2 selected students from different classes
    $createdTagihan = Tagihan::where('bulan', 'Oktober')->get();
    expect($createdTagihan->count())->toBe(2);
    expect($createdTagihan->pluck('siswa_id')->toArray())->toEqualCanonicalizing([$siswa1->id, $siswa2->id]);
    expect((float)$createdTagihan->first()->nominal)->toBe(400000.00);
});

