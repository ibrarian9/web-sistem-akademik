<?php

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Role;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Models\AbsensiGuru;
use App\Models\AbsensiSiswa;
use App\Models\KalenderAkademik;
use App\Livewire\Shared\Laporan\RekapAbsensiGuru;
use App\Livewire\Shared\Laporan\RekapAbsensiSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->roleAdmin = Role::firstOrCreate(['nama' => 'super_admin'], ['deskripsi' => 'Super Admin']);
    $this->roleGuru = Role::firstOrCreate(['nama' => 'guru'], ['deskripsi' => 'Guru']);
    $this->roleMurid = Role::firstOrCreate(['nama' => 'murid'], ['deskripsi' => 'Murid']);

    $this->adminUser = User::create([
        'nama' => 'Super Admin Test',
        'username' => 'admin_test',
        'email' => 'admin@test.com',
        'password' => bcrypt('password123'),
        'role_id' => $this->roleAdmin->id,
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

    // Create 10 Guru with User
    $this->gurus = [];
    for ($i = 1; $i <= 10; $i++) {
        $u = User::create([
            'nama' => "Guru Test {$i}",
            'username' => "guru_test_{$i}",
            'email' => "guru{$i}@test.com",
            'password' => bcrypt('password'),
            'role_id' => $this->roleGuru->id,
            'status' => 'aktif',
        ]);
        $this->gurus[] = Guru::create([
            'user_id' => $u->id,
            'nip' => "19800101201001100{$i}",
            'jenis_guru' => 'umum',
            'status_kepegawaian' => 'gty',
            'pendidikan' => 'S1',
            'tanggal_masuk' => '2024-01-01',
            'status_aktif' => true,
        ]);
    }

    // Create a holiday
    KalenderAkademik::create([
        'tahun_ajaran_id' => $this->ta->id,
        'nama_kegiatan' => 'Libur Nasional Kemerdekaan',
        'jenis' => 'hari_libur',
        'tanggal_mulai' => '2026-08-17',
        'tanggal_selesai' => '2026-08-17',
        'liburkan_presensi' => true,
    ]);

    // Create some teacher attendance
    foreach ($this->gurus as $g) {
        AbsensiGuru::create([
            'guru_id' => $g->id,
            'tanggal' => '2026-08-01',
            'status' => 'hadir',
            'waktu_datang' => '06:55:00',
        ]);
    }
});

test('rekap absensi guru getMatrixData tidak memiliki masalah n+1 dan menjalankan minimal query', function () {
    $this->actingAs($this->adminUser);

    $component = new RekapAbsensiGuru();
    $component->bulan = 8;
    $component->tahun = 2026;

    // Enable DB query logging
    DB::flushQueryLog();
    DB::enableQueryLog();

    $data = $component->getMatrixData();

    $queries = DB::getQueryLog();
    DB::disableQueryLog();

    expect(count($data['matrix']))->toBe(10);
    // Previously, 10 gurus * 31 days caused 300+ queries.
    // Now with batch pre-fetching, it should be at most 3 queries!
    expect(count($queries))->toBeLessThanOrEqual(4);
});

test('rekap absensi siswa getMatrixData tidak memiliki masalah n+1', function () {
    $this->actingAs($this->adminUser);

    $kelas = Kelas::create([
        'nama_kelas' => '8A',
        'tingkat' => 8,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
    ]);

    // Create 15 students
    for ($i = 1; $i <= 15; $i++) {
        $u = User::create([
            'nama' => "Siswa Test {$i}",
            'username' => "siswa_test_{$i}",
            'email' => "siswa{$i}@test.com",
            'password' => bcrypt('password'),
            'role_id' => $this->roleMurid->id,
            'status' => 'aktif',
        ]);
        $s = Siswa::create([
            'user_id' => $u->id,
            'nis' => "800{$i}",
            'nisn' => "00800{$i}",
            'kelas_id' => $kelas->id,
            'nama_wali' => "Wali {$i}",
            'no_hp_wali' => "081234567{$i}",
            'status' => 'aktif',
            'tanggal_masuk' => '2024-07-01',
        ]);

        AbsensiSiswa::create([
            'siswa_id' => $s->id,
            'guru_id' => $this->gurus[0]->id,
            'kelas_id' => $kelas->id,
            'tanggal' => '2026-08-01',
            'status' => 'hadir',
        ]);
    }

    $component = new RekapAbsensiSiswa();
    $component->kelasId = $kelas->id;
    $component->bulan = 8;
    $component->tahun = 2026;

    DB::flushQueryLog();
    DB::enableQueryLog();

    $data = $component->getMatrixData();

    $queries = DB::getQueryLog();
    DB::disableQueryLog();

    expect(count($data['matrix']))->toBe(15);
    // Previously, 15 students * 31 days caused 450+ queries.
    // Now with batch pre-fetching, it should be at most 4 queries!
    expect(count($queries))->toBeLessThanOrEqual(5);
});
