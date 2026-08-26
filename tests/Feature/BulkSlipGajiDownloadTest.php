<?php

use App\Models\GajiGuru;
use App\Models\Guru;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->roleFinance = Role::firstOrCreate(['nama' => 'finance'], ['deskripsi' => 'Finance']);
    $this->roleGuru = Role::firstOrCreate(['nama' => 'guru'], ['deskripsi' => 'Guru']);

    $this->financeUser = User::create([
        'nama' => 'Staff Finance Test',
        'username' => 'finance_test_slip',
        'email' => 'finance_test_slip@test.com',
        'password' => bcrypt('password123'),
        'role_id' => $this->roleFinance->id,
        'status' => 'aktif',
    ]);

    $this->guruUser = User::create([
        'nama' => 'Ustadz Abdullah Test',
        'username' => 'guru_test_abdullah',
        'email' => 'abdullah_test@test.com',
        'password' => bcrypt('password123'),
        'role_id' => $this->roleGuru->id,
        'status' => 'aktif',
    ]);

    $this->guru = Guru::create([
        'user_id' => $this->guruUser->id,
        'nip' => '198501012010011001',
        'niy' => 'NIY-TEST-1234',
        'jenis_guru' => 'umum',
        'status_kepegawaian' => 'tetap_yayasan',
        'pendidikan' => 'S1 Pendidikan Agama Islam',
        'tanggal_masuk' => '2020-01-01',
        'status_aktif' => true,
    ]);

    $this->gaji = GajiGuru::create([
        'guru_id' => $this->guru->id,
        'bulan' => 'Agustus',
        'tahun' => 2026,
        'gaji_pokok' => 2500000.00,
        'insentif_bpjs' => 150000.00,
        'insentif_maghrib_mengaji' => 200000.00,
        'potongan_peminjaman' => 0.00,
        'potongan_lainnya' => 0.00,
        'total_diterima' => 2850000.00,
        'status' => 'dibayar',
        'tanggal_bayar' => '2026-08-25',
    ]);
});

test('bulk slip gaji mengunduh file pdf biner yang valid tanpa string header http di bodinya', function () {
    $this->actingAs($this->financeUser);

    $response = $this->get('/finance/gaji-guru/bulk-slip?tahun=2026');

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/pdf');
    $response->assertHeader('content-disposition', 'attachment; filename=bulk_slip_gaji_2026.pdf');

    // Get the streamed response content
    $content = $response->streamedContent();

    // Verify it starts with standard PDF magic bytes
    expect($content)->toStartWith('%PDF-');

    // Verify it NEVER contains raw HTTP status lines dumped into body
    expect($content)->not->toContain('HTTP/1.0 200 OK');
    expect($content)->not->toContain('Cache-Control: no-cache, private');
    expect($content)->not->toContain('Content-Disposition: inline');
});

test('single slip gaji mengunduh file pdf murni tanpa korupsi header', function () {
    $this->actingAs($this->financeUser);

    $response = $this->get(route('finance.gaji-guru.slip', [
        'id' => $this->gaji->id,
        'download' => 1,
    ]));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/pdf');

    $content = $response->streamedContent();
    expect($content)->toStartWith('%PDF-');
    expect($content)->not->toContain('HTTP/1.0 200 OK');
    expect($content)->not->toContain('Cache-Control: no-cache, private');
});
