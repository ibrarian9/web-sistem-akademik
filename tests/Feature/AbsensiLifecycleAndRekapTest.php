<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\AbsensiSiswa;
use App\Livewire\Guru\AbsensiSiswa as GuruAbsensiSiswa;
use App\Livewire\Shared\Laporan\RekapAbsensiSiswa;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

    $roleGuru = Role::firstOrCreate(['nama' => 'guru']);
    $roleMurid = Role::firstOrCreate(['nama' => 'murid']);

    $this->userGuru = User::create([
        'nama' => 'Ust. Budi Santoso',
        'username' => 'guru_absensi',
        'email' => 'guru_absensi@yayasan.or.id',
        'password' => bcrypt('password123'),
        'role_id' => $roleGuru->id,
        'status' => 'aktif',
    ]);

    $this->guru = Guru::create([
        'user_id' => $this->userGuru->id,
        'nip' => 'GUR-777',
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
        'nama_kelas' => '7B',
        'tingkat' => 7,
        'semester_id' => $this->semester->id,
        'guru_umum_id' => $this->guru->id,
    ]);

    $this->students = [];
    for ($i = 1; $i <= 3; $i++) {
        $u = User::create([
            'nama' => "Santri {$i}",
            'username' => "santri_{$i}",
            'password' => bcrypt('password123'),
            'role_id' => $roleMurid->id,
            'status' => 'aktif',
        ]);
        $this->students[] = Siswa::create([
            'user_id' => $u->id,
            'nis' => "700{$i}",
            'kelas_id' => $this->kelas->id,
            'status' => 'aktif',
            'tanggal_masuk' => date('Y-m-d'),
        ]);
    }
});

test('teacher can record attendance with different statuses and view in rekap', function () {
    $this->actingAs($this->userGuru);

    $today = date('Y-m-d');

    // 1. Test Guru Absensi Input component
    Livewire::test(GuruAbsensiSiswa::class)
        ->set('kelas_id', $this->kelas->id)
        ->set('tanggal', $today)
        ->call('setStatus', 0, 'hadir')
        ->call('setStatus', 1, 'sakit')
        ->call('setStatus', 2, 'alpa')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('Kehadiran siswa berhasil disimpan');

    // Verify records exist in database
    $this->assertDatabaseHas('absensi_siswa', [
        'siswa_id' => $this->students[0]->id,
        'kelas_id' => $this->kelas->id,
        'tanggal' => $today,
        'status' => 'hadir',
    ]);

    $this->assertDatabaseHas('absensi_siswa', [
        'siswa_id' => $this->students[1]->id,
        'kelas_id' => $this->kelas->id,
        'tanggal' => $today,
        'status' => 'izin',
    ]);

    $this->assertDatabaseHas('absensi_siswa', [
        'siswa_id' => $this->students[2]->id,
        'kelas_id' => $this->kelas->id,
        'tanggal' => $today,
        'status' => 'tidak_hadir',
    ]);

    // 2. Test Rekap Absensi Siswa component
    $rekap = Livewire::test(RekapAbsensiSiswa::class)
        ->set('kelasId', $this->kelas->id)
        ->set('bulan', intval(date('m')))
        ->set('tahun', intval(date('Y')))
        ->assertStatus(200)
        ->assertSee('Santri 1')
        ->assertSee('Santri 2')
        ->assertSee('Santri 3');

    $matrixData = $rekap->instance()->getMatrixData()['matrix'];
    expect($matrixData)->toBeArray();
    expect(count($matrixData))->toBe(3);

    // Verify student 1 has 1 hadir
    expect($matrixData[0]['hadir'])->toBe(1);
    // Verify student 2 has 1 izin (sakit)
    expect($matrixData[1]['izin'])->toBe(1);
    // Verify student 3 has 1 alpa
    expect($matrixData[2]['tidak_hadir'])->toBe(1);
});

test('teacher can use date presets in attendance input', function () {
    $this->actingAs($this->userGuru);

    Livewire::test(GuruAbsensiSiswa::class)
        ->call('setPresetDate', 'today')
        ->assertSet('tanggal', Carbon::today()->toDateString())
        ->call('setPresetDate', 'yesterday')
        ->assertSet('tanggal', Carbon::yesterday()->toDateString());
});

test('rekap absensi supports period presets', function () {
    $this->actingAs($this->userGuru);

    Livewire::test(RekapAbsensiSiswa::class)
        ->call('setPeriodPreset', 'this_month')
        ->assertSet('bulan', intval(date('m')))
        ->assertSet('tahun', intval(date('Y')))
        ->call('setPeriodPreset', 'last_month')
        ->assertSet('bulan', intval(Carbon::now()->subMonth()->format('m')));
});
