<?php

use App\Livewire\Guru\InputNilaiTahfidz;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\NilaiTahfidz;
use App\Models\Role;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->roleGuru = Role::firstOrCreate(['nama' => 'guru'], ['deskripsi' => 'Guru']);

    $this->userGuruTahfidz = User::create([
        'nama' => 'Ustadz Ahmad Tahfizh',
        'username' => 'ustadz_ahmad',
        'email' => 'ahmad@tahfidz.sch.id',
        'password' => bcrypt('password'),
        'role_id' => $this->roleGuru->id,
        'status' => 'aktif',
    ]);

    $this->guruTahfidz = Guru::create([
        'user_id' => $this->userGuruTahfidz->id,
        'nip' => '9988776655',
        'jenis_guru' => 'tahfidz',
        'tanggal_masuk' => date('Y-m-d'),
    ]);

    $this->ta = TahunAjaran::create([
        'nama' => '2026/2027',
        'status_aktif' => true,
    ]);

    $this->semester = Semester::create([
        'tahun_ajaran_id' => $this->ta->id,
        'semester' => '1',
        'tanggal_mulai' => date('Y-01-01'),
        'tanggal_selesai' => date('Y-06-30'),
        'status_aktif' => true,
    ]);

    $this->kelas = Kelas::create([
        'semester_id' => $this->semester->id,
        'nama_kelas' => 'Halaqah Al-Fatih',
        'tingkat' => '1',
        'jenis_kelas' => 'tahfidz',
        'guru_tahfidz_id' => $this->guruTahfidz->id,
    ]);

    $this->userSiswa = User::create([
        'nama' => 'Muhammad Zaky',
        'username' => 'zaky_santri',
        'email' => 'zaky@santri.sch.id',
        'password' => bcrypt('password'),
        'role_id' => Role::firstOrCreate(['nama' => 'murid'])->id,
        'status' => 'aktif',
    ]);

    $this->siswa = Siswa::create([
        'user_id' => $this->userSiswa->id,
        'kelas_id' => $this->kelas->id,
        'kelas_tahfidz_id' => $this->kelas->id,
        'nis' => '1001',
        'nisn' => '0099887766',
        'tanggal_masuk' => date('Y-m-d'),
        'status' => 'aktif',
    ]);
});

test('guru tahfidz can input daily mutabaah with specific date', function () {
    $this->actingAs($this->userGuruTahfidz);

    $testDate = '2026-08-20';

    Livewire::test(InputNilaiTahfidz::class)
        ->set('kelas_id', $this->kelas->id)
        ->set('semester_id', $this->semester->id)
        ->set('tanggal', $testDate)
        ->set('siswa_id', $this->siswa->id)
        ->set('materi_tahsin', 'Al-Baqarah 1-10')
        ->set('nilai_tahsin', 90)
        ->set('materi_ziyadah', 'Al-Baqarah 11-20')
        ->set('nilai_ziyadah', 88)
        ->set('catatan_ustadz', 'MasyaAllah lancar dan makhraj fasih')
        ->call('saveScore')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('nilai_tahfidz', [
        'siswa_id' => $this->siswa->id,
        'semester_id' => $this->semester->id,
        'tanggal' => $testDate,
        'materi_tahsin' => 'Al-Baqarah 1-10',
        'nilai_tahsin' => 90,
        'materi_ziyadah' => 'Al-Baqarah 11-20',
        'nilai_ziyadah' => 88,
    ]);
});

test('guru tahfidz can switch between daily, weekly, and monthly tabs', function () {
    $this->actingAs($this->userGuruTahfidz);

    Livewire::test(InputNilaiTahfidz::class)
        ->assertSet('viewTab', 'daily')
        ->call('selectTab', 'weekly')
        ->assertSet('viewTab', 'weekly')
        ->call('selectTab', 'monthly')
        ->assertSet('viewTab', 'monthly');
});
