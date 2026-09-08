<?php

use App\Models\Guru;
use App\Models\Role;
use App\Models\User;
use App\Models\AbsensiGuru;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Livewire\TataUsaha\InputAbsensiKaryawan;
use App\Livewire\Shared\Laporan\RekapAbsensiGuru;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->roleSuperAdmin = Role::firstOrCreate(['nama' => 'super_admin'], ['deskripsi' => 'Super Admin']);
    $this->roleTU = Role::firstOrCreate(['nama' => 'tata_usaha'], ['deskripsi' => 'Tata Usaha']);
    $this->roleGuru = Role::firstOrCreate(['nama' => 'guru'], ['deskripsi' => 'Guru']);

    $this->userTU = User::create([
        'nama' => 'Staf Tata Usaha Test',
        'username' => 'tu_test',
        'email' => 'tu@test.com',
        'password' => bcrypt('password123'),
        'role_id' => $this->roleTU->id,
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

    // Create 3 teachers
    $this->teachers = [];
    for ($i = 1; $i <= 3; $i++) {
        $u = User::create([
            'nama' => "Guru Teladan {$i}",
            'username' => "guru_teladan_{$i}",
            'email' => "guru_teladan{$i}@test.com",
            'password' => bcrypt('password'),
            'role_id' => $this->roleGuru->id,
            'status' => 'aktif',
        ]);
        $this->teachers[] = Guru::create([
            'user_id' => $u->id,
            'nip' => "19850101201501100{$i}",
            'jenis_guru' => 'umum',
            'status_kepegawaian' => 'gty',
            'pendidikan' => 'S1',
            'tanggal_masuk' => '2024-01-01',
            'status_aktif' => true,
        ]);
    }
});

test('tata usaha can download attendance template excel/csv', function () {
    $this->actingAs($this->userTU);

    Livewire::test(InputAbsensiKaryawan::class)
        ->set('tanggal', '2026-08-10')
        ->call('downloadTemplate')
        ->assertFileDownloaded('template-absen-guru-karyawan-2026-08-10.csv');
});

test('tata usaha can export daily attendance to excel/csv', function () {
    $this->actingAs($this->userTU);

    // Seed some attendance
    AbsensiGuru::create([
        'guru_id' => $this->teachers[0]->id,
        'tanggal' => '2026-08-10',
        'status' => 'hadir',
        'waktu_datang' => '06:45:00',
        'waktu_pulang' => '15:00:00',
        'catatan' => 'Tepat waktu',
    ]);

    Livewire::test(InputAbsensiKaryawan::class)
        ->set('tanggal', '2026-08-10')
        ->call('exportAttendance')
        ->assertFileDownloaded('data-presensi-karyawan-guru-2026-08-10.csv');
});

test('tata usaha can upload attendance using template csv format', function () {
    $this->actingAs($this->userTU);

    $csvContent = "\xEF\xBB\xBF" . "NIP / Username,Nama Karyawan,Peran / Jabatan,Status Kehadiran,Jam Datang (HH:MM),Jam Pulang (HH:MM),Catatan\n" .
        "198501012015011001,Guru Teladan 1,Guru,hadir,06:50,15:10,Hadir lancar\n" .
        "198501012015011002,Guru Teladan 2,Guru,telat,07:25,15:00,Macet jalan\n" .
        "198501012015011003,Guru Teladan 3,Guru,izin,07:00,15:00,Izin dinas luar\n";

    $file = UploadedFile::fake()->createWithContent('template_filled.csv', $csvContent);

    Livewire::test(InputAbsensiKaryawan::class)
        ->set('tanggal', '2026-08-15')
        ->set('csvFile', $file)
        ->call('uploadCsv')
        ->assertHasNoErrors()
        ->assertSee('Berhasil mengunggah 3 data presensi');

    $record1 = AbsensiGuru::where('guru_id', $this->teachers[0]->id)->where('tanggal', '2026-08-15')->first();
    expect($record1)->not->toBeNull();
    expect($record1->status)->toBe('hadir');
    expect($record1->catatan)->toBe('Hadir lancar');

    $record2 = AbsensiGuru::where('guru_id', $this->teachers[1]->id)->where('tanggal', '2026-08-15')->first();
    expect($record2)->not->toBeNull();
    expect($record2->status)->toBe('telat');

    $record3 = AbsensiGuru::where('guru_id', $this->teachers[2]->id)->where('tanggal', '2026-08-15')->first();
    expect($record3)->not->toBeNull();
    expect($record3->status)->toBe('izin');
});

test('tata usaha can download monthly teacher attendance matrix to excel', function () {
    $this->actingAs($this->userTU);

    // Create attendance on day 1 and 2
    AbsensiGuru::create([
        'guru_id' => $this->teachers[0]->id,
        'tanggal' => '2026-08-01',
        'status' => 'hadir',
        'waktu_datang' => '06:50:00',
    ]);
    AbsensiGuru::create([
        'guru_id' => $this->teachers[0]->id,
        'tanggal' => '2026-08-02',
        'status' => 'telat',
        'waktu_datang' => '07:15:00',
    ]);

    Livewire::test(RekapAbsensiGuru::class)
        ->set('bulan', 8)
        ->set('tahun', 2026)
        ->call('downloadExcel')
        ->assertFileDownloaded('rekap-absensi-guru-8-2026.csv');
});
