<?php

use App\Models\JadwalRemedial;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\User;
use App\Livewire\Guru\ManajemenRemedial;
use App\Livewire\Murid\JadwalRemedial as MuridJadwalRemedial;
use App\Livewire\Murid\RaporNilai;
use App\Livewire\Murid\SetoranTahfidz;
use Livewire\Livewire;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    $this->artisan('db:seed', ['--class' => 'KomponenNilaiSeeder']);
    $this->artisan('db:seed', ['--class' => 'PengaturanSeeder']);
    $this->artisan('db:seed', ['--class' => 'DemoDataSeeder']);

    // Fetch seeded Guru Umum user (budi)
    $this->userGuruUmum = User::where('username', 'budi')->first();
    $this->guruUmum = $this->userGuruUmum->guru;

    // Fetch seeded Guru Tahfidz user (hasan)
    $this->userGuruTahfidz = User::where('username', 'hasan')->first();
    $this->guruTahfidz = $this->userGuruTahfidz->guru;

    // Fetch Murid User & Siswa
    $this->siswa = Siswa::first();
    $this->userMurid = $this->siswa->user;
    $this->kelas = $this->siswa->kelas;
    $this->mapel = MataPelajaran::where('jenis', 'umum')->first();
});

test('guru umum can create and manage jadwal remedial', function () {
    $this->actingAs($this->userGuruUmum);

    $rem = JadwalRemedial::create([
        'guru_id' => $this->guruUmum->id,
        'kelas_id' => $this->kelas->id,
        'mapel_id' => $this->mapel->id,
        'siswa_id' => $this->siswa->id,
        'topik_tp' => 'TP 1: Operasi Hitung Perkalian',
        'kategori' => 'harian_tp',
        'tanggal' => date('Y-m-d'),
        'waktu_mulai' => '13:00',
        'waktu_selesai' => '14:00',
        'ruangan' => 'Ruang Kelas 3A',
        'catatan' => 'Membawa modul latihan matematika',
        'status' => 'dijadwalkan',
    ]);

    Livewire::test(ManajemenRemedial::class)
        ->assertStatus(200)
        ->assertSee('TP 1: Operasi Hitung Perkalian');

    expect($rem)->not->toBeNull();
    expect($rem->kelas_id)->toEqual($this->kelas->id);
});

test('guru tahfidz cannot access remedial management component', function () {
    $this->actingAs($this->userGuruTahfidz);

    Livewire::test(ManajemenRemedial::class)
        ->assertStatus(403);
});

test('murid can view assigned jadwal remedial', function () {
    JadwalRemedial::create([
        'guru_id' => $this->guruUmum->id,
        'kelas_id' => $this->siswa->kelas_id,
        'mapel_id' => $this->mapel->id,
        'siswa_id' => $this->siswa->id,
        'topik_tp' => 'Remedial Mid STS Matematika',
        'kategori' => 'mid_sts',
        'tanggal' => date('Y-m-d'),
        'waktu_mulai' => '13:00',
        'waktu_selesai' => '14:30',
        'ruangan' => 'Ruang Kelas 3A',
        'status' => 'dijadwalkan',
    ]);

    $this->actingAs($this->userMurid);

    Livewire::test(MuridJadwalRemedial::class)
        ->assertStatus(200)
        ->assertSee('Jadwal Remedial Saya');
});

test('murid sees separated academic and tahfidz evaluation components', function () {
    \App\Models\Tagihan::where('siswa_id', $this->siswa->id)->delete();

    $this->actingAs($this->userMurid);

    Livewire::test(RaporNilai::class)
        ->assertStatus(200)
        ->assertSee('Nilai Akademik');

    Livewire::test(SetoranTahfidz::class)
        ->assertStatus(200);
});
