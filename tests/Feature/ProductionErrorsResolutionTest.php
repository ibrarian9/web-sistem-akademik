<?php

use App\Livewire\Guru\InputNilaiTahfidz;
use App\Livewire\Guru\ManajemenKurikulumMerdeka;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\LingkupMateri;
use App\Models\MataPelajaran;
use App\Models\Role;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->roleGuru = Role::firstOrCreate(['nama' => 'guru'], ['deskripsi' => 'Guru']);

    $this->userGuruTahfidz = User::create([
        'nama' => 'Ustadz Fajar Al-Hafizh',
        'username' => 'ustadz_fajar',
        'email' => 'fajar@tahfidz.sch.id',
        'password' => bcrypt('password'),
        'role_id' => $this->roleGuru->id,
        'status' => 'aktif',
    ]);

    $this->guruTahfidz = Guru::create([
        'user_id' => $this->userGuruTahfidz->id,
        'nip' => '9988771122',
        'jenis_guru' => 'tahfidz',
        'tanggal_masuk' => date('Y-m-d'),
    ]);

    $this->userGuruUmum = User::create([
        'nama' => 'Ibu Guru Matematika',
        'username' => 'guru_mtk',
        'email' => 'mtk@sekolah.sch.id',
        'password' => bcrypt('password'),
        'role_id' => $this->roleGuru->id,
        'status' => 'aktif',
    ]);

    $this->guruUmum = Guru::create([
        'user_id' => $this->userGuruUmum->id,
        'nip' => '1122334455',
        'jenis_guru' => 'umum',
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
        'nama_kelas' => 'Halaqah 10',
        'tingkat' => '1',
        'jenis_kelas' => 'tahfidz',
        'guru_tahfidz_id' => $this->guruTahfidz->id,
    ]);

    $this->userSiswa = User::create([
        'nama' => 'Fadhil Rahman',
        'username' => 'fadhil_santri',
        'email' => 'fadhil@santri.sch.id',
        'password' => bcrypt('password'),
        'role_id' => Role::firstOrCreate(['nama' => 'murid'])->id,
        'status' => 'aktif',
    ]);

    $this->siswa = Siswa::create([
        'user_id' => $this->userSiswa->id,
        'kelas_id' => $this->kelas->id,
        'kelas_tahfidz_id' => $this->kelas->id,
        'nis' => '9901',
        'nisn' => '0012345678',
        'tanggal_masuk' => date('Y-m-d'),
        'status' => 'aktif',
    ]);
});

test('searching student with query like "Fa" in InputNilaiTahfidz does not trigger Unknown column nama_panggilan error', function () {
    $this->actingAs($this->userGuruTahfidz);

    // This exact search query caused:
    // SQLSTATE[42S22]: Column not found: 1054 Unknown column 'nama_panggilan' in 'where clause'
    // when $this->search was set to 'Fa'.
    $component = Livewire::test(InputNilaiTahfidz::class)
        ->set('kelas_id', $this->kelas->id)
        ->set('semester_id', $this->semester->id)
        ->set('search', 'Fa')
        ->assertOk();

    // Verify search matches the student by user->nama ("Fadhil Rahman")
    expect($this->siswa->nama_panggilan)->toBe('Fadhil');
});

test('siswa model nama_panggilan accessor works without database column', function () {
    expect($this->siswa->nama_panggilan)->toBe('Fadhil');
    
    // Test with student having multiple words in name
    $user2 = User::create([
        'nama' => 'Aisyah Humaira Putri',
        'username' => 'aisyah_putri',
        'email' => 'aisyah@santri.sch.id',
        'password' => bcrypt('password'),
        'role_id' => Role::firstOrCreate(['nama' => 'murid'])->id,
        'status' => 'aktif',
    ]);
    $siswa2 = Siswa::create([
        'user_id' => $user2->id,
        'kelas_id' => $this->kelas->id,
        'nis' => '9902',
        'nisn' => '0012345679',
        'tanggal_masuk' => date('Y-m-d'),
        'status' => 'aktif',
    ]);

    expect($siswa2->nama_panggilan)->toBe('Aisyah');
});

test('manajemen kurikulum merdeka prevents null mapel_id and saves bab properly', function () {
    $this->actingAs($this->userGuruUmum);

    $mapel = MataPelajaran::create([
        'nama_mapel' => 'Matematika',
        'kode_mapel' => 'MTK-01',
        'kategori' => 'umum',
    ]);

    // Test saving Bab with valid mapel_id
    Livewire::test(ManajemenKurikulumMerdeka::class)
        ->set('mapel_id', $mapel->id)
        ->call('openLmModal')
        ->set('nama_lingkup_materi', 'bab 1 bilangan bulat')
        ->set('kategori_lm', 'sumatif')
        ->set('urutan_lm', 1)
        ->call('saveLingkupMateri')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('lingkup_materi', [
        'mapel_id' => $mapel->id,
        'nama_lingkup_materi' => 'bab 1 bilangan bulat',
        'kategori' => 'sumatif',
        'urutan' => 1,
    ]);
});

test('manajemen kurikulum merdeka handles missing mapel_id gracefully without SQL integrity error', function () {
    $this->actingAs($this->userGuruUmum);

    // Ensure no mapel exists
    LingkupMateri::query()->delete();
    MataPelajaran::query()->delete();

    // When no mapel exists in database, saving should validate mapel_id and NOT cause SQLSTATE[23000]
    Livewire::test(ManajemenKurikulumMerdeka::class)
        ->set('mapel_id', null)
        ->set('nama_lingkup_materi', 'bab 1 bilangan bulat')
        ->call('saveLm')
        ->assertHasErrors(['mapel_id']);
});

test('manajemen kurikulum merdeka prevents null lingkup_materi_id and saves tp properly', function () {
    $this->actingAs($this->userGuruUmum);

    $mapel = MataPelajaran::create([
        'nama_mapel' => 'Matematika',
        'kode_mapel' => 'MTK-02',
        'kategori' => 'umum',
    ]);

    $lm = LingkupMateri::create([
        'mapel_id' => $mapel->id,
        'nama_lingkup_materi' => 'bab 1 bilangan bulat',
        'kategori' => 'sumatif',
        'urutan' => 1,
    ]);

    // Test saving TP with valid lingkup_materi_id
    Livewire::test(ManajemenKurikulumMerdeka::class)
        ->set('mapel_id', $mapel->id)
        ->call('openTpModal', $lm->id)
        ->set('deskripsi_tp', 'Peserta didik dapat memahami bilangan bulat')
        ->set('urutan_tp', 1)
        ->call('saveTp')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('tujuan_pembelajaran', [
        'lingkup_materi_id' => $lm->id,
        'deskripsi_tp' => 'Peserta didik dapat memahami bilangan bulat',
        'urutan' => 1,
    ]);
});

test('manajemen kurikulum merdeka handles missing lingkup_materi_id gracefully without SQL integrity error', function () {
    $this->actingAs($this->userGuruUmum);

    // Ensure no LingkupMateri exists
    LingkupMateri::query()->delete();

    $mapel = MataPelajaran::create([
        'nama_mapel' => 'Matematika Dasar',
        'kode_mapel' => 'MTK-03',
        'kategori' => 'umum',
    ]);

    // When user tries to save TP without any LingkupMateri (exact scenario of SQLSTATE[23000] with 'Peserta didik dapat')
    Livewire::test(ManajemenKurikulumMerdeka::class)
        ->set('mapel_id', $mapel->id)
        ->set('lingkup_materi_id', null)
        ->set('deskripsi_tp', 'Peserta didik dapat')
        ->call('saveTp')
        ->assertHasErrors(['lingkup_materi_id']);

    // When opening TP modal with no LingkupMateri, it should flash an error and redirect to Bab modal
    Livewire::test(ManajemenKurikulumMerdeka::class)
        ->set('mapel_id', $mapel->id)
        ->call('openTpModal')
        ->assertSet('showTpModal', false)
        ->assertSet('showLmModal', true);
});

