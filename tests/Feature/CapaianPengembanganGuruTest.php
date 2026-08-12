<?php

use App\Models\CapaianGuru;
use App\Models\Guru;
use App\Models\User;
use App\Livewire\Guru\CapaianPengembanganDiri;
use App\Livewire\SuperAdmin\TataKelola\CapaianPengembanganGuru;
use Livewire\Livewire;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    $this->artisan('db:seed', ['--class' => 'KomponenNilaiSeeder']);
    $this->artisan('db:seed', ['--class' => 'PengaturanSeeder']);
    $this->artisan('db:seed', ['--class' => 'DemoDataSeeder']);

    // Fetch seeded Super Admin user
    $this->userAdmin = User::where('username', 'admin')->first();

    // Fetch seeded Guru user (budi)
    $this->userGuru = User::where('username', 'budi')->first();
    $this->guru = $this->userGuru->guru;
});

test('guru can submit achievement and google drive link in menu guru', function () {
    $this->actingAs($this->userGuru);

    Livewire::test(CapaianPengembanganDiri::class)
        ->call('openCreate')
        ->set('judul', 'Pelatihan Kurikulum Merdeka 2026')
        ->set('kategori', 'pelatihan')
        ->set('link_gdrive', 'https://drive.google.com/file/d/example123/view')
        ->set('deskripsi', 'Mengikuti bimtek 3 hari')
        ->call('save')
        ->assertHasNoErrors();

    $record = CapaianGuru::where('guru_id', $this->guru->id)->where('judul', 'Pelatihan Kurikulum Merdeka 2026')->first();
    expect($record)->not->toBeNull();
    expect($record->link_gdrive)->toEqual('https://drive.google.com/file/d/example123/view');
    expect($record->status_penilaian)->toEqual('diajukan');
});

test('super admin can review drive link and evaluate teacher achievement', function () {
    // Create submission from teacher
    $capaian = CapaianGuru::create([
        'guru_id' => $this->guru->id,
        'judul' => 'Sertifikasi Pedagogik Guru',
        'kategori' => 'sertifikasi',
        'link_gdrive' => 'https://drive.google.com/file/d/sertifikat999/view',
        'deskripsi' => 'Lulus sertifikasi tingkat nasional',
        'status_penilaian' => 'diajukan',
    ]);

    $this->actingAs($this->userAdmin);

    Livewire::test(CapaianPengembanganGuru::class)
        ->call('openEvaluateModal', $capaian->id)
        ->set('skor_nilai', 95.5)
        ->set('predikat', 'Sangat Baik')
        ->set('catatan_evaluasi', 'Sertifikat terverifikasi valid, luar biasa.')
        ->set('tanggal_penilaian', date('Y-m-d'))
        ->call('saveEvaluation')
        ->assertHasNoErrors();

    $capaian->refresh();
    expect($capaian->status_penilaian)->toEqual('dinilai');
    expect((float) $capaian->skor_nilai)->toEqual(95.5);
    expect($capaian->predikat)->toEqual('Sangat Baik');
    expect($capaian->penilai_id)->toEqual($this->userAdmin->id);
});

test('guru can view evaluation feedback and score from super admin', function () {
    $capaian = CapaianGuru::create([
        'guru_id' => $this->guru->id,
        'penilai_id' => $this->userAdmin->id,
        'judul' => 'Workshop IT Canva for Education',
        'kategori' => 'pelatihan',
        'link_gdrive' => 'https://drive.google.com/file/d/workshop123/view',
        'deskripsi' => 'Pengembangan media pembelajaran',
        'skor_nilai' => 88.0,
        'predikat' => 'Baik',
        'catatan_evaluasi' => 'Media pembelajaran sangat interaktif.',
        'status_penilaian' => 'dinilai',
        'tanggal_penilaian' => date('Y-m-d'),
    ]);

    $this->actingAs($this->userGuru);

    Livewire::test(CapaianPengembanganDiri::class)
        ->assertStatus(200)
        ->assertSee('Workshop IT Canva for Education')
        ->assertSee('Skor: 88')
        ->assertSee('Baik')
        ->assertSee('Media pembelajaran sangat interaktif.');
});

test('non super admin user cannot evaluate teacher achievements', function () {
    $this->actingAs($this->userGuru);

    Livewire::test(CapaianPengembanganGuru::class)
        ->assertStatus(403);
});
