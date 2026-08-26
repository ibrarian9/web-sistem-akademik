<?php

use App\Models\Kelas;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Livewire\Finance\Laporan\LaporanTunggakan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->roleFinance = Role::firstOrCreate(['nama' => 'finance'], ['deskripsi' => 'Finance']);
    $this->roleMurid = Role::firstOrCreate(['nama' => 'murid'], ['deskripsi' => 'Murid']);

    $this->financeUser = User::create([
        'nama' => 'Staff Keuangan',
        'username' => 'finance_user',
        'email' => 'finance@test.com',
        'password' => bcrypt('password123'),
        'role_id' => $this->roleFinance->id,
        'status' => 'aktif',
    ]);

    TahunAjaran::query()->update(['status_aktif' => false]);

    $this->tahunAjaran = TahunAjaran::create([
        'nama' => '2026/2027',
        'status_aktif' => true,
    ]);

    $this->semester = \App\Models\Semester::create([
        'tahun_ajaran_id' => $this->tahunAjaran->id,
        'semester' => 'ganjil',
        'tanggal_mulai' => date('Y-m-d'),
        'tanggal_selesai' => date('Y-m-d', strtotime('+6 months')),
        'status_aktif' => true,
    ]);

    $this->kelas = Kelas::create([
        'nama_kelas' => '7A',
        'tingkat' => 7,
        'tahun_ajaran_id' => $this->tahunAjaran->id,
        'semester_id' => $this->semester->id,
    ]);

    // Setup Siswa 1
    $u1 = User::create([
        'nama' => 'Santri Tunggakan Hari Ini',
        'username' => 'santri_hari_ini',
        'email' => 'hariini@test.com',
        'password' => bcrypt('password123'),
        'role_id' => $this->roleMurid->id,
        'status' => 'aktif',
    ]);
    $this->siswa1 = Siswa::create([
        'user_id' => $u1->id,
        'nis' => '8001',
        'nisn' => '0008001',
        'kelas_id' => $this->kelas->id,
        'nama_wali' => 'Wali Santri 1',
        'no_hp_wali' => '08123456789',
        'status' => 'aktif',
        'tanggal_masuk' => date('Y-m-d'),
    ]);

    // Setup Siswa 2
    $u2 = User::create([
        'nama' => 'Santri Tunggakan Bulan Depan',
        'username' => 'santri_bulandepan',
        'email' => 'bulandepan@test.com',
        'password' => bcrypt('password123'),
        'role_id' => $this->roleMurid->id,
        'status' => 'aktif',
    ]);
    $this->siswa2 = Siswa::create([
        'user_id' => $u2->id,
        'nis' => '8002',
        'nisn' => '0008002',
        'kelas_id' => $this->kelas->id,
        'nama_wali' => 'Wali Santri 2',
        'no_hp_wali' => '08123456789',
        'status' => 'aktif',
        'tanggal_masuk' => date('Y-m-d'),
    ]);

    $this->jenisTagihan = \App\Models\JenisTagihan::create([
        'nama' => 'SPP Bulanan',
        'kategori' => 'rutin',
        'default_nominal' => 350000.00,
        'is_blocking' => true,
    ]);

    // Tagihan 1: Jatuh tempo Hari Ini (Bulan Agustus)
    $this->tagihan1 = Tagihan::create([
        'siswa_id' => $this->siswa1->id,
        'tahun_ajaran_id' => $this->tahunAjaran->id,
        'jenis_tagihan_id' => $this->jenisTagihan->id,
        'nama_tagihan' => 'SPP Agustus 2026',
        'bulan' => 'Agustus',
        'nominal' => 350000.00,
        'nominal_terbayar' => 0.00,
        'total_dibayar' => 0.00,
        'status' => 'belum_bayar',
        'jatuh_tempo' => date('Y-m-d'),
    ]);

    // Tagihan 2: Jatuh tempo Bulan Depan (Bulan September)
    $this->tagihan2 = Tagihan::create([
        'siswa_id' => $this->siswa2->id,
        'tahun_ajaran_id' => $this->tahunAjaran->id,
        'jenis_tagihan_id' => $this->jenisTagihan->id,
        'nama_tagihan' => 'SPP September 2026',
        'bulan' => 'September',
        'nominal' => 350000.00,
        'nominal_terbayar' => 0.00,
        'total_dibayar' => 0.00,
        'status' => 'belum_bayar',
        'jatuh_tempo' => date('Y-m-d', strtotime('+30 days')),
    ]);
});

test('laporan tunggakan dapat difilter per hari ini menggunakan date filter', function () {
    $this->actingAs($this->financeUser);

    Livewire::test(LaporanTunggakan::class)
        ->set('tahun_ajaran_id', $this->tahunAjaran->id)
        ->assertSee('Santri Tunggakan Hari Ini')
        ->assertSee('Santri Tunggakan Bulan Depan')
        ->set('filterPeriode', 'hari_ini')
        ->assertSee('Santri Tunggakan Hari Ini')
        ->assertDontSee('Santri Tunggakan Bulan Depan');
});

test('laporan tunggakan dapat difilter per bulan tagihan', function () {
    $this->actingAs($this->financeUser);

    Livewire::test(LaporanTunggakan::class)
        ->set('tahun_ajaran_id', $this->tahunAjaran->id)
        ->set('bulan', 'September')
        ->assertSee('Santri Tunggakan Bulan Depan')
        ->assertDontSee('Santri Tunggakan Hari Ini')
        ->set('bulan', 'Agustus')
        ->assertSee('Santri Tunggakan Hari Ini')
        ->assertDontSee('Santri Tunggakan Bulan Depan');
});

test('laporan tunggakan dapat difilter rentang kustom tanggal jatuh tempo', function () {
    $this->actingAs($this->financeUser);

    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    $nextTwoMonths = date('Y-m-d', strtotime('+60 days'));

    Livewire::test(LaporanTunggakan::class)
        ->set('tahun_ajaran_id', $this->tahunAjaran->id)
        ->set('filterPeriode', 'custom')
        ->set('startDate', $tomorrow)
        ->set('endDate', $nextTwoMonths)
        ->assertSee('Santri Tunggakan Bulan Depan')
        ->assertDontSee('Santri Tunggakan Hari Ini');
});

test('ekspor pdf dan excel laporan tunggakan berjalan sukses dengan filter', function () {
    $this->actingAs($this->financeUser);

    // 1. Ekspor CSV dari Livewire
    $component = Livewire::test(LaporanTunggakan::class)
        ->set('tahun_ajaran_id', $this->tahunAjaran->id)
        ->set('bulan', 'Agustus');

    $csvResponse = $component->call('exportCsv');
    expect($csvResponse)->not->toBeNull();

    // 2. Ekspor PDF dari Livewire
    $pdfResponse = $component->call('exportPdf');
    expect($pdfResponse)->not->toBeNull();

    // 3. Ekspor Excel / CSV dari Controller endpoint
    $res = $this->get(route('finance.export.tunggakan', [
        'bulan' => 'Agustus',
        'filter_periode' => 'hari_ini'
    ]));
    $res->assertStatus(200);
    expect($res->headers->get('content-type'))->toContain('text/csv');
});
