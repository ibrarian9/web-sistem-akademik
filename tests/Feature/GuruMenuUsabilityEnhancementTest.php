<?php

use App\Models\User;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\MataPelajaran;
use App\Models\GuruMapelKelas;
use App\Models\LingkupMateri;
use App\Models\TujuanPembelajaran;
use Livewire\Livewire;
use App\Livewire\Guru\ManajemenKurikulumMerdeka;
use App\Livewire\Guru\InputNilaiSumatif;
use App\Livewire\Guru\AbsensiSiswa;
use App\Livewire\Shared\TutorialDanFaq;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    $this->artisan('db:seed', ['--class' => 'KomponenNilaiSeeder']);
    $this->artisan('db:seed', ['--class' => 'PengaturanSeeder']);
    $this->artisan('db:seed', ['--class' => 'DemoDataSeeder']);

    $this->userGuru = User::whereHas('role', function ($q) {
        $q->where('nama', 'guru');
    })->first();

    $this->guru = $this->userGuru->guru;
    $this->gmk = GuruMapelKelas::where('guru_id', $this->guru->id)->first();
    $this->kelas = $this->gmk->kelas;
    $this->mapel = $this->gmk->mapel;
    $this->semester = $this->gmk->semester;
    $this->siswa = Siswa::where('kelas_id', $this->kelas->id)->first();
});

test('manajemen kurikulum merdeka prioritizes assigned subjects and supports collapsible template frasa', function () {
    $this->actingAs($this->userGuru);

    Livewire::test(ManajemenKurikulumMerdeka::class)
        ->assertStatus(200)
        ->assertSee('Setup Bab & Tujuan Pembelajaran')
        ->assertSee('Template Frasa Auto-Narasi Rapor')
        ->assertSee('openTemplate')
        ->assertSee('Mata Pelajaran Diampu');
});

test('input nilai sumatif validates bounds 0-100 and dispatches scores-saved', function () {
    $this->actingAs($this->userGuru);

    // Create a LM and TP for testing
    $lm = LingkupMateri::firstOrCreate([
        'mapel_id' => $this->mapel->id,
        'nama_lingkup_materi' => 'Bab Uji Coba Nilai',
        'urutan' => 99,
    ]);

    $tp = TujuanPembelajaran::firstOrCreate([
        'lingkup_materi_id' => $lm->id,
        'deskripsi_tp' => 'Memahami konsep dasar uji coba',
        'urutan' => 1,
    ]);

    // Test bound error > 100
    Livewire::test(InputNilaiSumatif::class)
        ->set('kelas_id', $this->kelas->id)
        ->set('mapel_id', $this->mapel->id)
        ->set('semester_id', $this->semester->id)
        ->set("nilaiTpMatrix.{$this->siswa->id}.{$tp->id}", 999)
        ->call('saveAllScores')
        ->assertSee('Semua nilai TP harus berupa angka antara 0 sampai 100.');

    // Test valid score saving
    Livewire::test(InputNilaiSumatif::class)
        ->set('kelas_id', $this->kelas->id)
        ->set('mapel_id', $this->mapel->id)
        ->set('semester_id', $this->semester->id)
        ->set("nilaiTpMatrix.{$this->siswa->id}.{$tp->id}", 88)
        ->set("nilaiSasMatrix.{$this->siswa->id}", 90)
        ->call('saveAllScores')
        ->assertDispatched('scores-saved')
        ->assertSee('Matriks Nilai Sumatif TP & SAS berhasil disimpan.');
});

test('absensi siswa supports bulk set and dispatches attendance-saved', function () {
    $this->actingAs($this->userGuru);

    Livewire::test(AbsensiSiswa::class)
        ->set('kelas_id', $this->kelas->id)
        ->set('tanggal', date('Y-m-d'))
        ->call('setStatusAll', 'hadir')
        ->call('save')
        ->assertDispatched('attendance-saved')
        ->assertSee('Kehadiran siswa berhasil disimpan.');
});

test('tutorial dan faq contains teacher tutorials and faqs', function () {
    $this->actingAs($this->userGuru);

    Livewire::test(TutorialDanFaq::class)
        ->set('selectedCategory', 'guru')
        ->assertStatus(200)
        ->assertSee('Panduan Input Nilai Sumatif TP & SAS Format Spreadsheet')
        ->assertSee('Panduan Presensi Kehadiran Siswa Harian & Fitur Set Masal')
        ->assertSee('Apakah nilai di tabel Sumatif otomatis tersimpan saat saya mengetik angka?')
        ->assertSee('Mengapa setelah saya klik "Semua Hadir" muncul peringatan bahwa presensi belum tersimpan?');
});

test('guru modules contain driver js interactive tour triggers and element targets', function () {
    $this->actingAs($this->userGuru);

    // 1. Kurikulum Merdeka Tour
    Livewire::test(ManajemenKurikulumMerdeka::class)
        ->assertStatus(200)
        ->assertSee('tour-km-help-btn')
        ->assertSee('tour-km-mapel')
        ->assertSee('tour-km-tambah-bab')
        ->assertSee('tour-km-frasa-rapor')
        ->assertSee('runKurmerTour');

    // 2. Input Nilai Sumatif Tour
    Livewire::test(InputNilaiSumatif::class)
        ->assertStatus(200)
        ->assertSee('tour-sumatif-help-btn')
        ->assertSee('tour-sumatif-filter')
        ->assertSee('tour-sumatif-tabel')
        ->assertSee('tour-sumatif-simpan')
        ->assertSee('runSumatifTour');

    // 3. Absensi Siswa Tour
    Livewire::test(AbsensiSiswa::class)
        ->assertStatus(200)
        ->assertSee('tour-absen-help-btn')
        ->assertSee('tour-absen-tanggal')
        ->assertSee('runAbsensiTour');
});

