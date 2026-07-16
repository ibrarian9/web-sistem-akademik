<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\GuruMapelKelas;
use App\Models\AbsensiGuru;
use App\Models\AbsensiSiswa;
use App\Models\Nilai;
use App\Models\KomponenNilai;
use Livewire\Livewire;
use App\Livewire\Guru\Dashboard;
use App\Livewire\Guru\InputNilaiSiswa;
use App\Livewire\Guru\AbsensiSiswa as LivewireAbsensiSiswa;
use App\Livewire\Guru\AbsensiDiri;
use App\Livewire\Guru\JadwalMengajar;

beforeEach(function () {
    // Seed everything
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    $this->artisan('db:seed', ['--class' => 'KomponenNilaiSeeder']);
    $this->artisan('db:seed', ['--class' => 'PengaturanSeeder']);
    $this->artisan('db:seed', ['--class' => 'DemoDataSeeder']);
    
    // Find the guru user seeded in DemoDataSeeder
    $this->userGuru = User::whereHas('role', function ($q) {
        $q->where('nama', 'guru');
    })->first();

    $this->guru = $this->userGuru->guru;

    // Find the class assigned to this guru
    $this->gmk = GuruMapelKelas::where('guru_id', $this->guru->id)->first();
    $this->kelas = $this->gmk->kelas;
    $this->mapel = $this->gmk->mapel;
    $this->semester = $this->gmk->semester;

    // Find a student in this class
    $this->siswa = Siswa::where('kelas_id', $this->kelas->id)->first();

    // Get a component
    $this->komponen = KomponenNilai::first();
});

test('guru can render dashboard', function () {
    $this->actingAs($this->userGuru);

    Livewire::test(Dashboard::class)
        ->assertStatus(200)
        ->assertSee('Selamat Datang');
});

test('guru can record self attendance checkin and checkout', function () {
    $this->actingAs($this->userGuru);

    Livewire::test(AbsensiDiri::class)
        ->assertStatus(200)
        ->call('checkIn')
        ->assertHasNoErrors();

    $attendance = AbsensiGuru::where('guru_id', $this->guru->id)->first();
    expect($attendance)->not->toBeNull();
    expect($attendance->waktu_datang)->not->toBeNull();

    Livewire::test(AbsensiDiri::class)
        ->call('checkOut')
        ->assertHasNoErrors();

    $attendance->refresh();
    expect($attendance->waktu_pulang)->not->toBeNull();
});

test('guru can input student grades', function () {
    $this->actingAs($this->userGuru);

    $lw = Livewire::test(InputNilaiSiswa::class);
    
    $lw->set('kelas_id', $this->kelas->id)
       ->set('mapel_id', $this->mapel->id)
       ->set('komponen_nilai_id', $this->komponen->id)
       ->assertSet('grades.0.siswa_id', $this->siswa->id);

    $grades = $lw->get('grades');
    foreach ($grades as $index => $g) {
        $lw->set("grades.{$index}.nilai", 85)
           ->set("grades.{$index}.catatan", 'Sangat Baik');
    }

    $lw->call('save')
       ->assertHasNoErrors();

    $nilai = Nilai::where('siswa_id', $this->siswa->id)->first();
    expect($nilai)->not->toBeNull();
    expect(floatval($nilai->nilai))->toEqual(85.00);
});

test('guru can input student attendance', function () {
    $this->actingAs($this->userGuru);

    $lw = Livewire::test(LivewireAbsensiSiswa::class);

    $lw->set('kelas_id', $this->kelas->id)
       ->assertSet('attendance.0.siswa_id', $this->siswa->id);

    $lw->set('attendance.0.status', 'sakit')
       ->set('attendance.0.catatan', 'Demam')
       ->call('save')
       ->assertHasNoErrors();

    $absensi = AbsensiSiswa::where('siswa_id', $this->siswa->id)->first();
    expect($absensi)->not->toBeNull();
    expect($absensi->status)->toEqual('izin');
});
