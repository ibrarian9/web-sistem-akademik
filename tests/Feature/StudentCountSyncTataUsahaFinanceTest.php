<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\JenisTagihan;
use App\Models\Tagihan;
use App\Models\Tabungan;
use App\Livewire\SuperAdmin\TataKelola\ManajemenSiswa;
use App\Livewire\Finance\ManajemenTagihan;
use App\Livewire\Finance\TabunganSiswa;
use App\Livewire\Finance\DetailTagihanSiswa;
use App\Livewire\Finance\InputPembayaran;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    Tagihan::query()->forceDelete();
    Tabungan::query()->forceDelete();
    Siswa::query()->forceDelete();
    Kelas::query()->forceDelete();
    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    $roleSuperAdmin = Role::where('nama', 'super_admin')->first();
    $roleFinance = Role::where('nama', 'finance')->first();
    $roleTU = Role::where('nama', 'tata_usaha')->first();
    $roleMurid = Role::where('nama', 'murid')->first();

    $this->tuUser = User::create([
        'nama' => 'Staff Tata Usaha',
        'username' => 'staff_tu',
        'email' => 'tu@siakad.or.id',
        'password' => bcrypt('password123'),
        'role_id' => $roleTU->id,
        'status' => 'aktif',
    ]);

    $this->financeUser = User::create([
        'nama' => 'Staff Keuangan',
        'username' => 'staff_finance',
        'email' => 'finance@siakad.or.id',
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

    $this->jenisTagihan = JenisTagihan::create([
        'nama' => 'SPP Bulanan',
        'default_nominal' => 350000.00,
    ]);
});

test('jumlah murid di tabungan finance sama persis dengan jumlah murid di data master tata usaha', function () {
    // 1. Tata Usaha creates 5 students across Class 7A and 7B
    for ($i = 1; $i <= 3; $i++) {
        $u = User::create([
            'nama' => "Siswa 7A-{$i}",
            'username' => "siswa_7a_{$i}",
            'email' => "siswa7a{$i}@test.com",
            'password' => bcrypt('password123'),
            'role_id' => Role::where('nama', 'murid')->first()->id,
            'status' => 'aktif',
        ]);
        Siswa::create([
            'user_id' => $u->id,
            'nis' => "100{$i}",
            'nisn' => "000100{$i}",
            'kelas_id' => $this->kelas7A->id,
            'nama_wali' => "Wali {$i}",
            'no_hp_wali' => '08123456789',
            'status' => 'aktif',
            'tanggal_masuk' => date('Y-m-d'),
        ]);
    }

    for ($i = 4; $i <= 5; $i++) {
        $u = User::create([
            'nama' => "Siswa 7B-{$i}",
            'username' => "siswa_7b_{$i}",
            'email' => "siswa7b{$i}@test.com",
            'password' => bcrypt('password123'),
            'role_id' => Role::where('nama', 'murid')->first()->id,
            'status' => 'aktif',
        ]);
        Siswa::create([
            'user_id' => $u->id,
            'nis' => "100{$i}",
            'nisn' => "000100{$i}",
            'kelas_id' => $this->kelas7B->id,
            'nama_wali' => "Wali {$i}",
            'no_hp_wali' => '08123456789',
            'status' => 'aktif',
            'tanggal_masuk' => date('Y-m-d'),
        ]);
    }

    $totalSiswaTU = Siswa::count();
    $totalSiswa7A = Siswa::where('kelas_id', $this->kelas7A->id)->count();
    $totalSiswa7B = Siswa::where('kelas_id', $this->kelas7B->id)->count();

    expect($totalSiswaTU)->toBe(5);
    expect($totalSiswa7A)->toBe(3);
    expect($totalSiswa7B)->toBe(2);

    // 2. Authenticate as Finance and check Tabungan Siswa view
    $this->actingAs($this->financeUser);

    $component = Livewire::test(TabunganSiswa::class);
    $component->assertOk();

    // Verify all 5 students are rendered in Tabungan Finance
    for ($i = 1; $i <= 3; $i++) {
        $component->assertSee("Siswa 7A-{$i}");
    }
    for ($i = 4; $i <= 5; $i++) {
        $component->assertSee("Siswa 7B-{$i}");
    }

    // Verify class filtering matches exactly
    $component->set('filterKelas', $this->kelas7A->id)
        ->assertSee("Siswa 7A-1")
        ->assertSee("Siswa 7A-2")
        ->assertSee("Siswa 7A-3")
        ->assertDontSee("Siswa 7B-4")
        ->assertDontSee("Siswa 7B-5");

    $component->set('filterKelas', $this->kelas7B->id)
        ->assertSee("Siswa 7B-4")
        ->assertSee("Siswa 7B-5")
        ->assertDontSee("Siswa 7A-1")
        ->assertDontSee("Siswa 7A-2")
        ->assertDontSee("Siswa 7A-3");
});

test('jumlah murid target rilis tagihan finance (seluruh siswa & per kelas) sinkron 100% dengan data siswa aktif tata usaha', function () {
    // 1. Create 4 active students and 1 nonactive student in Tata Usaha
    for ($i = 1; $i <= 4; $i++) {
        $u = User::create([
            'nama' => "Siswa Aktif {$i}",
            'username' => "siswa_aktif_{$i}",
            'email' => "aktif{$i}@test.com",
            'password' => bcrypt('password123'),
            'role_id' => Role::where('nama', 'murid')->first()->id,
            'status' => 'aktif',
        ]);
        Siswa::create([
            'user_id' => $u->id,
            'nis' => "200{$i}",
            'nisn' => "000200{$i}",
            'kelas_id' => $i <= 2 ? $this->kelas7A->id : $this->kelas7B->id,
            'nama_wali' => "Wali {$i}",
            'no_hp_wali' => '08123456789',
            'status' => 'aktif',
            'tanggal_masuk' => date('Y-m-d'),
        ]);
    }

    // 1 keluar/pindah student
    $uNonAktif = User::create([
        'nama' => "Siswa Keluar 5",
        'username' => "siswa_keluar_5",
        'email' => "keluar5@test.com",
        'password' => bcrypt('password123'),
        'role_id' => Role::where('nama', 'murid')->first()->id,
        'status' => 'nonaktif',
    ]);
    Siswa::create([
        'user_id' => $uNonAktif->id,
        'nis' => "2005",
        'nisn' => "0002005",
        'kelas_id' => $this->kelas7A->id,
        'nama_wali' => "Wali 5",
        'no_hp_wali' => '08123456789',
        'status' => 'keluar',
        'tanggal_masuk' => date('Y-m-d'),
    ]);

    $totalAktifTU = Siswa::where('status', 'aktif')->count();
    $totalAktif7A = Siswa::where('status', 'aktif')->where('kelas_id', $this->kelas7A->id)->count();
    $totalAktif7B = Siswa::where('status', 'aktif')->where('kelas_id', $this->kelas7B->id)->count();

    expect($totalAktifTU)->toBe(4);
    expect($totalAktif7A)->toBe(2);
    expect($totalAktif7B)->toBe(2);

    // 2. Finance releases bulk bill for 'all' active students
    $this->actingAs($this->financeUser);

    Livewire::test(ManajemenTagihan::class)
        ->set('showCreateModal', true)
        ->set('releaseMode', 'bulk')
        ->set('bulkTarget', 'all')
        ->set('jenis_tagihan_id', $this->jenisTagihan->id)
        ->set('bulan', 'Juli')
        ->set('nominal', 350000.00)
        ->set('jatuh_tempo', date('Y-m-d', strtotime('+30 days')))
        ->call('createBulkTagihan')
        ->assertHasNoErrors();

    // Verify exactly 4 active students got the bill, nonaktif student did NOT get billed
    $totalTagihanGenerated = Tagihan::where('jenis_tagihan_id', $this->jenisTagihan->id)->where('bulan', 'Juli')->count();
    expect($totalTagihanGenerated)->toBe($totalAktifTU)->toBe(4);

    // Nonactive student was excluded
    $nonAktifSiswa = Siswa::where('status', 'keluar')->first();
    expect(Tagihan::where('siswa_id', $nonAktifSiswa->id)->exists())->toBeFalse();
});

test('perubahan kelas siswa di tata usaha langsung terupdate secara real-time di seluruh modul keuangan', function () {
    // 1. Create a student in Class 7A
    $u = User::create([
        'nama' => 'Budi Santoso',
        'username' => 'budi_santoso',
        'email' => 'budi@test.com',
        'password' => bcrypt('password123'),
        'role_id' => Role::where('nama', 'murid')->first()->id,
        'status' => 'aktif',
    ]);
    $siswa = Siswa::create([
        'user_id' => $u->id,
        'nis' => '3001',
        'nisn' => '0003001',
        'kelas_id' => $this->kelas7A->id,
        'nama_wali' => 'Santoso',
        'no_hp_wali' => '08123456789',
        'status' => 'aktif',
        'tanggal_masuk' => date('Y-m-d'),
    ]);

    // 2. Finance issues a bill
    $this->actingAs($this->financeUser);
    $tagihan = Tagihan::create([
        'siswa_id' => $siswa->id,
        'tahun_ajaran_id' => $this->ta->id,
        'jenis_tagihan_id' => $this->jenisTagihan->id,
        'bulan' => 'Juli',
        'nominal' => 350000.00,
        'total_dibayar' => 0.00,
        'status' => 'belum_bayar',
        'jatuh_tempo' => date('Y-m-d'),
    ]);

    // Initially in Class 7A
    Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $siswa->id])
        ->assertSee('7A')
        ->assertDontSee('7B');

    // 3. Tata Usaha updates student's class from 7A to 7B
    $siswa->update(['kelas_id' => $this->kelas7B->id]);

    // 4. Finance detail tagihan and tabungan immediately show 7B without cache lag
    Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $siswa->id])
        ->assertSee('7B');

    Livewire::test(TabunganSiswa::class)
        ->set('filterKelas', $this->kelas7B->id)
        ->assertSee('Budi Santoso');
});

test('pencarian autocomplete rilis tagihan single di finance langsung menemukan siswa baru dari tata usaha', function () {
    // 1. Tata Usaha registers a new student
    $u = User::create([
        'nama' => 'Zulhasanah Putri',
        'username' => 'zulhasanah_putri',
        'email' => 'zulha@test.com',
        'password' => bcrypt('password123'),
        'role_id' => Role::where('nama', 'murid')->first()->id,
        'status' => 'aktif',
    ]);
    $siswa = Siswa::create([
        'user_id' => $u->id,
        'nis' => '9901',
        'nisn' => '0009901',
        'kelas_id' => $this->kelas7A->id,
        'nama_wali' => 'Hasan',
        'no_hp_wali' => '08123456789',
        'status' => 'aktif',
        'tanggal_masuk' => date('Y-m-d'),
    ]);

    // 2. Finance searches for student in Single Tagihan release modal
    $this->actingAs($this->financeUser);

    Livewire::test(ManajemenTagihan::class)
        ->set('showCreateModal', true)
        ->set('releaseMode', 'single')
        ->set('studentSearch', 'Zulhasanah')
        ->assertSee('Zulhasanah Putri')
        ->call('selectStudent', $siswa->id)
        ->assertSet('single_siswa_id', $siswa->id)
        ->assertSet('selectedStudentName', 'Zulhasanah Putri')
        ->assertSet('selectedStudentNis', '9901');
});

test('penghapusan siswa di tata usaha otomatis mengupdate data di modul finance', function () {
    // 1. Create a student
    $u = User::create([
        'nama' => 'Siswa Yang Akan Dihapus',
        'username' => 'siswa_hapus',
        'email' => 'hapus@test.com',
        'password' => bcrypt('password123'),
        'role_id' => Role::where('nama', 'murid')->first()->id,
        'status' => 'aktif',
    ]);
    $siswa = Siswa::create([
        'user_id' => $u->id,
        'nis' => '8801',
        'nisn' => '0008801',
        'kelas_id' => $this->kelas7A->id,
        'nama_wali' => 'Wali Hapus',
        'no_hp_wali' => '08123456789',
        'status' => 'aktif',
        'tanggal_masuk' => date('Y-m-d'),
    ]);

    // Finance can see the student
    $this->actingAs($this->financeUser);
    Livewire::test(TabunganSiswa::class)
        ->assertSee('Siswa Yang Akan Dihapus');

    // 2. Tata Usaha / Super Admin deletes the student
    $this->actingAs($this->tuUser);
    Livewire::test(ManajemenSiswa::class)
        ->call('delete', $siswa->id);

    // Verify soft deleted
    expect(Siswa::find($siswa->id))->toBeNull();
    expect(Siswa::withTrashed()->find($siswa->id))->not->toBeNull();

    // 3. Finance tabungan immediately no longer displays the deleted student
    $this->actingAs($this->financeUser);
    Livewire::test(TabunganSiswa::class)
        ->assertDontSee('Siswa Yang Akan Dihapus');
});

