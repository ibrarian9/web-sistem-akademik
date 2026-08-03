<?php

use App\Models\User;
use App\Models\Siswa;
use App\Models\Rapor;
use App\Models\Tagihan;
use Livewire\Livewire;
use App\Livewire\Murid\RaporNilai;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    $this->artisan('db:seed', ['--class' => 'KomponenNilaiSeeder']);
    $this->artisan('db:seed', ['--class' => 'PengaturanSeeder']);
    $this->artisan('db:seed', ['--class' => 'RaporOrangTuaSeeder']);
});

test('rapor orang tua seeder executes cleanly and creates complete published rapor', function () {
    $userSiswa = User::where('username', 'siswa')->first();
    expect($userSiswa)->not->toBeNull();
    expect($userSiswa->role->nama)->toBe('murid');

    $siswa = $userSiswa->siswa;
    expect($siswa)->not->toBeNull();
    expect($siswa->kelas_id)->not->toBeNull();
    expect($siswa->nama_wali)->toBe('Bapak/Ibu Wali Siswa');

    // Check published Rapor record exists
    $rapor = Rapor::where('siswa_id', $siswa->id)->first();
    expect($rapor)->not->toBeNull();
    expect($rapor->catatan_wali_kelas)->toContain('Ananda Siswa Berprestasi');
    expect($rapor->details)->not->toBeEmpty();
});

test('parents and students can view published rapor without blocked access', function () {
    $userSiswa = User::where('username', 'siswa')->first();

    Livewire::actingAs($userSiswa)
        ->test(RaporNilai::class)
        ->assertStatus(200)
        ->assertDontSee('Akses Rapor Terkunci')
        ->assertSee('Laporan Hasil Belajar Semester')
        ->assertSee('Matematika')
        ->assertSee('Bahasa Indonesia')
        ->assertSee('Ananda Siswa Berprestasi');
});

test('parents can switch tab to view tahfizh published report', function () {
    $userSiswa = User::where('username', 'siswa')->first();

    Livewire::actingAs($userSiswa)
        ->test(RaporNilai::class)
        ->set('activeTab', 'tahfidz')
        ->assertStatus(200)
        ->assertDontSee('Akses Rapor Terkunci')
        ->assertSee("Rapor Tahfizh")
        ->assertSee('Tahfidz Al-Quran');
});
