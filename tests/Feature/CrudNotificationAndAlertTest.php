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
use App\Livewire\Finance\ManajemenTagihan;
use App\Livewire\Finance\TabunganSiswa;
use App\Livewire\Guru\AbsensiSiswa;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

    $roleFounder = Role::where('nama', 'super_admin')->first();
    $roleFinance = Role::where('nama', 'finance')->first();
    $roleGuru = Role::where('nama', 'guru')->first();
    $roleMurid = Role::where('nama', 'murid')->first();

    $this->founder = User::create([
        'nama' => 'Founder Yayasan',
        'username' => 'founder_notif',
        'password' => bcrypt('password123'),
        'role_id' => $roleFounder->id,
        'status' => 'aktif',
    ]);

    $this->finance = User::create([
        'nama' => 'Bendahara Sekolah',
        'username' => 'finance_notif',
        'password' => bcrypt('password123'),
        'role_id' => $roleFinance->id,
        'status' => 'aktif',
    ]);

    $this->guruUser = User::create([
        'nama' => 'Guru Pengajar',
        'username' => 'guru_notif',
        'password' => bcrypt('password123'),
        'role_id' => $roleGuru->id,
        'status' => 'aktif',
    ]);

    $this->guru = \App\Models\Guru::create([
        'user_id' => $this->guruUser->id,
        'nip' => 'GUR-999',
        'status_aktif' => true,
        'tanggal_masuk' => date('Y-m-d'),
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

    $this->kelas = Kelas::create([
        'nama_kelas' => '8A',
        'tingkat' => 8,
        'semester_id' => $this->semester->id,
        'guru_umum_id' => $this->guru->id,
    ]);

    $this->siswaUser = User::create([
        'nama' => 'Budi Santri',
        'username' => 'budi_santri',
        'password' => bcrypt('password123'),
        'role_id' => $roleMurid->id,
        'status' => 'aktif',
    ]);

    $this->siswa = Siswa::create([
        'user_id' => $this->siswaUser->id,
        'nis' => '8801',
        'kelas_id' => $this->kelas->id,
        'status' => 'aktif',
        'tanggal_masuk' => date('Y-m-d'),
    ]);

    $this->jenisTagihan = JenisTagihan::create([
        'nama' => 'SPP Terpadu',
        'default_nominal' => 200000.00,
        'tipe' => 'bulanan',
    ]);
});

test('crud operations in tagihan produce flash notifications, dispatched alert events, and audit logs', function () {
    $this->actingAs($this->founder);

    // 1. CREATE Tagihan -> Flash message and dispatched show-alert
    Livewire::test(ManajemenTagihan::class)
        ->set('single_siswa_id', $this->siswa->id)
        ->set('jenis_tagihan_id', $this->jenisTagihan->id)
        ->set('bulan', 'Juli')
        ->set('nominal', 200000.00)
        ->set('jatuh_tempo', date('Y-m-d', strtotime('+30 days')))
        ->call('createSingleTagihan')
        ->assertHasNoErrors()
        ->assertDispatched('show-alert')
        ->assertSee('Berhasil merilis tagihan');

    $tagihan = Tagihan::where('siswa_id', $this->siswa->id)->first();
    expect($tagihan)->not->toBeNull();

    // 2. UPDATE / EDIT Tagihan -> Flash message and dispatched show-alert
    Livewire::test(ManajemenTagihan::class)
        ->call('openEditModal', $tagihan->id)
        ->set('edit_nominal', 220000.00)
        ->call('saveEditTagihan')
        ->assertHasNoErrors()
        ->assertDispatched('show-alert')
        ->assertSee('Tagihan berhasil diperbarui');

    // 3. DELETE Tagihan -> Flash message and dispatched show-alert
    Livewire::test(ManajemenTagihan::class)
        ->call('deleteTagihan', $tagihan->id)
        ->assertHasNoErrors()
        ->assertDispatched('show-alert')
        ->assertSee('Tagihan berhasil dihapus');

    // Check activity_log entries for Tagihan CRUD
    $logs = DB::table('activity_log')
        ->where('subject_type', Tagihan::class)
        ->where('subject_id', $tagihan->id)
        ->get();

    expect($logs->pluck('event')->toArray())->toContain('created', 'updated', 'deleted');
});

test('crud operations in tabungan produce flash notifications, dispatched alert events, and audit logs', function () {
    $this->actingAs($this->founder);

    // 1. CREATE / SETOR Tabungan -> Flash message and dispatched show-alert
    Livewire::test(TabunganSiswa::class)
        ->call('openTransactionModal', $this->siswa->id, 'setor')
        ->set('nominal', 50000.00)
        ->set('tanggal', date('Y-m-d'))
        ->set('keterangan', 'Setoran Awal')
        ->call('saveTransaction')
        ->assertHasNoErrors()
        ->assertDispatched('show-alert')
        ->assertSee('Transaksi tabungan setoran berhasil dicatat');

    $tx = Tabungan::where('siswa_id', $this->siswa->id)->first();
    expect($tx)->not->toBeNull();

    // 2. UPDATE / EDIT Tabungan -> Flash message and dispatched show-alert
    Livewire::test(TabunganSiswa::class)
        ->call('openEditTransaction', $tx->id)
        ->set('edit_nominal', 60000.00)
        ->call('saveEditTransaction')
        ->assertHasNoErrors()
        ->assertDispatched('show-alert')
        ->assertSee('Transaksi tabungan berhasil diperbarui');

    // 3. DELETE Tabungan -> Flash message and dispatched show-alert
    Livewire::test(TabunganSiswa::class)
        ->call('deleteTransaction', $tx->id)
        ->assertHasNoErrors()
        ->assertDispatched('show-alert')
        ->assertSee('Catatan transaksi tabungan berhasil dihapus');

    // Check activity_log entries for Tabungan CRUD
    $logs = DB::table('activity_log')
        ->where('subject_type', Tabungan::class)
        ->where('subject_id', $tx->id)
        ->get();

    expect($logs->pluck('event')->toArray())->toContain('created', 'updated', 'deleted');
});

test('attendance save operation produces success notification, dispatched alert event, and audit logs', function () {
    $this->actingAs($this->guruUser);

    Livewire::test(AbsensiSiswa::class)
        ->set('kelas_id', $this->kelas->id)
        ->set('tanggal', date('Y-m-d'))
        ->call('setStatus', 0, 'hadir')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('show-alert')
        ->assertSee('Kehadiran siswa berhasil disimpan');

    $attendanceRecord = \App\Models\AbsensiSiswa::where('siswa_id', $this->siswa->id)->first();
    expect($attendanceRecord)->not->toBeNull();

    $logs = DB::table('activity_log')
        ->where('subject_type', \App\Models\AbsensiSiswa::class)
        ->get();

    expect($logs->count())->toBeGreaterThanOrEqual(1);
});

test('bulk tagihan release per kelas and all students works with duplicate prevention', function () {
    $this->actingAs($this->founder);

    // Create a 2nd student in the same class
    $user2 = User::create([
        'nama' => 'Ahmad Santri 2',
        'username' => 'ahmad_santri',
        'password' => bcrypt('password123'),
        'role_id' => Role::where('nama', 'murid')->first()->id,
        'status' => 'aktif',
    ]);

    $siswa2 = Siswa::create([
        'user_id' => $user2->id,
        'nis' => '8802',
        'kelas_id' => $this->kelas->id,
        'status' => 'aktif',
        'tanggal_masuk' => date('Y-m-d'),
    ]);

    // 1. Bulk release for class $this->kelas->id
    Livewire::test(ManajemenTagihan::class)
        ->set('releaseMode', 'bulk')
        ->set('bulkTarget', 'class')
        ->set('release_kelas_id', $this->kelas->id)
        ->set('jenis_tagihan_id', $this->jenisTagihan->id)
        ->set('bulan', 'Agustus')
        ->set('nominal', 250000.00)
        ->set('jatuh_tempo', date('Y-m-d', strtotime('+30 days')))
        ->call('createBulkTagihan')
        ->assertHasNoErrors()
        ->assertDispatched('show-alert')
        ->assertSee('Berhasil merilis tagihan untuk 2 siswa');

    expect(Tagihan::where('bulan', 'Agustus')->count())->toBe(2);

    // 2. Run bulk release again with same parameters -> should skip duplicates safely
    Livewire::test(ManajemenTagihan::class)
        ->set('releaseMode', 'bulk')
        ->set('bulkTarget', 'class')
        ->set('release_kelas_id', $this->kelas->id)
        ->set('jenis_tagihan_id', $this->jenisTagihan->id)
        ->set('bulan', 'Agustus')
        ->set('nominal', 250000.00)
        ->set('jatuh_tempo', date('Y-m-d', strtotime('+30 days')))
        ->call('createBulkTagihan')
        ->assertHasNoErrors()
        ->assertDispatched('show-alert')
        ->assertSee('2 siswa dilewati karena sudah memiliki tagihan ini');

    // Count should remain 2
    expect(Tagihan::where('bulan', 'Agustus')->count())->toBe(2);
});

test('student live search autocomplete and selection works in release tagihan modal', function () {
    $this->actingAs($this->finance);

    Livewire::test(ManajemenTagihan::class)
        ->call('openCreateModal')
        ->set('release_kelas_id', $this->kelas->id)
        ->set('studentSearch', 'Budi')
        ->assertSee('Budi Santri')
        ->call('selectStudent', $this->siswa->id)
        ->assertSet('single_siswa_id', $this->siswa->id)
        ->assertSet('selectedStudentName', 'Budi Santri')
        ->set('jenis_tagihan_id', $this->jenisTagihan->id)
        ->set('bulan', 'September')
        ->set('nominal', 300000.00)
        ->set('jatuh_tempo', date('Y-m-d', strtotime('+30 days')))
        ->call('createSingleTagihan')
        ->assertHasNoErrors()
        ->assertDispatched('show-alert');

    $tagihan = Tagihan::where('siswa_id', $this->siswa->id)->where('bulan', 'September')->first();
    expect($tagihan)->not->toBeNull();
    expect(floatval($tagihan->nominal))->toBe(300000.00);
});

test('multi student custom selection across different classes with same nominal works', function () {
    $this->actingAs($this->finance);

    // Create 2nd class and students in different classes
    $kelasB = Kelas::create([
        'nama_kelas' => '8B',
        'tingkat' => '8',
        'wali_kelas_id' => $this->guru->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
    ]);

    $userSiswaB = User::create([
        'nama' => 'Santri Beda Kelas',
        'username' => 'santri_b',
        'password' => bcrypt('password123'),
        'role_id' => Role::where('nama', 'murid')->first()->id,
        'status' => 'aktif',
    ]);

    $siswaB = Siswa::create([
        'user_id' => $userSiswaB->id,
        'nis' => '8803',
        'kelas_id' => $kelasB->id,
        'status' => 'aktif',
        'tanggal_masuk' => date('Y-m-d'),
    ]);

    // Test selecting 2 students from 2 different classes ($this->siswa in Kelas 7A, $siswaB in Kelas 8B)
    Livewire::test(ManajemenTagihan::class)
        ->call('openCreateModal')
        ->set('releaseMode', 'bulk')
        ->set('bulkTarget', 'custom')
        ->call('addSiswaToBulk', $this->siswa->id)
        ->call('addSiswaToBulk', $siswaB->id)
        ->assertCount('bulkSelectedSiswaIds', 2)
        ->set('jenis_tagihan_id', $this->jenisTagihan->id)
        ->set('bulan', 'Oktober')
        ->set('nominal', 375000.00)
        ->set('jatuh_tempo', date('Y-m-d', strtotime('+30 days')))
        ->call('createBulkTagihan')
        ->assertHasNoErrors()
        ->assertDispatched('show-alert')
        ->assertSee('Berhasil merilis tagihan untuk 2 siswa');

    $tagihanA = Tagihan::where('siswa_id', $this->siswa->id)->where('bulan', 'Oktober')->first();
    $tagihanB = Tagihan::where('siswa_id', $siswaB->id)->where('bulan', 'Oktober')->first();

    expect($tagihanA)->not->toBeNull();
    expect(floatval($tagihanA->nominal))->toBe(375000.00);
    expect($tagihanB)->not->toBeNull();
    expect(floatval($tagihanB->nominal))->toBe(375000.00);
});


