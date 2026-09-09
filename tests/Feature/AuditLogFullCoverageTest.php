<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Semester;
use App\Models\TahunAjaran;
use App\Models\JenisTagihan;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\GajiGuru;
use App\Models\Guru;
use App\Models\Rapor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Logout;
use Illuminate\Http\Request;
use App\Http\Controllers\FinanceExportController;
use App\Http\Controllers\FinanceReportController;
use App\Http\Controllers\RaporPdfController;
use App\Http\Controllers\DocumentVerificationController;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    $this->artisan('db:seed', ['--class' => 'PengaturanSeeder']);
    $this->artisan('db:seed', ['--class' => 'DemoDataSeeder']);
    $this->artisan('db:seed', ['--class' => 'JenisTagihanSeeder']);

    $this->adminUser = User::whereHas('role', function ($q) {
        $q->where('nama', 'super_admin');
    })->first();

    $this->financeUser = User::whereHas('role', function ($q) {
        $q->where('nama', 'finance');
    })->first();

    $this->guruUser = User::whereHas('role', function ($q) {
        $q->where('nama', 'guru');
    })->first();
});

/*
|--------------------------------------------------------------------------
| A. Authentication Events (Login, Failed, Logout)
|--------------------------------------------------------------------------
*/

test('successful login records audit log with role and user context', function () {
    event(new Login('web', $this->financeUser, false));

    $log = DB::table('activity_log')
        ->where('event', 'login')
        ->where('causer_id', $this->financeUser->id)
        ->latest('id')
        ->first();

    expect($log)->not->toBeNull();
    expect($log->log_name)->toBe('autentikasi');
    expect($log->description)->toContain($this->financeUser->nama);
    expect($log->description)->toContain('finance');
});

test('failed login attempts record security audit log without storing plaintext password', function () {
    $attemptedUser = 'hacker_or_typo_user';
    
    event(new Failed('web', null, [
        'username' => $attemptedUser,
        'password' => 'secret_plain_password_123',
    ]));

    $log = DB::table('activity_log')
        ->where('event', 'failed_login')
        ->latest('id')
        ->first();

    expect($log)->not->toBeNull();
    expect($log->log_name)->toBe('keamanan');
    expect($log->description)->toContain($attemptedUser);

    // Ensure password is never recorded in description, changes, or properties
    expect($log->description)->not->toContain('secret_plain_password_123');
    expect($log->attribute_changes)->toBeNull();
    expect($log->properties)->not->toContain('secret_plain_password_123');
});

test('logout records audit log with user details', function () {
    event(new Logout('web', $this->financeUser));

    $log = DB::table('activity_log')
        ->where('event', 'logout')
        ->where('causer_id', $this->financeUser->id)
        ->latest('id')
        ->first();

    expect($log)->not->toBeNull();
    expect($log->log_name)->toBe('autentikasi');
    expect($log->description)->toContain($this->financeUser->nama);
});

/*
|--------------------------------------------------------------------------
| B. Financial Export & Report Downloads
|--------------------------------------------------------------------------
*/

test('financial export to CSV records audit log with filter properties', function () {
    $this->actingAs($this->financeUser);

    $siswa = Siswa::first();
    $ta = TahunAjaran::first();
    $jt = JenisTagihan::first();

    Tagihan::create([
        'siswa_id' => $siswa->id,
        'tahun_ajaran_id' => $ta->id,
        'jenis_tagihan_id' => $jt->id,
        'bulan' => 'September',
        'nominal' => 250000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => date('Y-m-d'),
    ]);

    $controller = new FinanceExportController();
    $request = Request::create('/finance/export/tunggakan', 'GET', [
        'bulan' => 'September',
        'filter_periode' => 'bulan_ini',
    ]);

    $response = $controller->exportTunggakan($request);
    expect($response->getStatusCode())->toBe(200);

    $log = DB::table('activity_log')
        ->where('event', 'export')
        ->where('causer_id', $this->financeUser->id)
        ->latest('id')
        ->first();

    expect($log)->not->toBeNull();
    expect($log->log_name)->toBe('keuangan');
    expect($log->description)->toContain('Mengekspor Laporan Tunggakan');
    
    $props = json_decode($log->properties, true);
    expect($props['format'] ?? null)->toBe('csv');
});

test('slip gaji download records audit log with teacher and period context', function () {
    $this->actingAs($this->financeUser);

    $guru = Guru::first();
    $gaji = GajiGuru::create([
        'guru_id' => $guru->id,
        'bulan' => 'September',
        'tahun' => 2026,
        'tanggal_bayar' => date('Y-m-d'),
        'gaji_pokok' => 2500000,
        'total_diterima' => 2500000,
        'status' => 'dibayar',
    ]);

    $controller = new FinanceReportController();
    $request = Request::create("/finance/laporan/slip-gaji/{$gaji->id}", 'GET');

    $response = $controller->slipGaji($request, $gaji->id);
    expect($response->getStatusCode())->toBe(200);

    $log = DB::table('activity_log')
        ->where('event', 'download')
        ->where('causer_id', $this->financeUser->id)
        ->latest('id')
        ->first();

    expect($log)->not->toBeNull();
    expect($log->log_name)->toBe('keuangan');
    expect($log->description)->toContain('slip gaji');
    expect($log->description)->toContain('September 2026');
});

test('payment receipt print/download records audit log with student id and receipt number', function () {
    $this->actingAs($this->financeUser);

    $siswa = Siswa::first();
    $ta = TahunAjaran::first();
    $jt = JenisTagihan::first();

    $tagihan = Tagihan::create([
        'siswa_id' => $siswa->id,
        'tahun_ajaran_id' => $ta->id,
        'jenis_tagihan_id' => $jt->id,
        'bulan' => 'September',
        'nominal' => 300000,
        'total_dibayar' => 300000,
        'status' => 'lunas',
        'jatuh_tempo' => date('Y-m-d'),
    ]);

    $pembayaran = Pembayaran::create([
        'no_resi' => 'KW-AUDIT-TEST-99',
        'tagihan_id' => $tagihan->id,
        'tanggal_bayar' => date('Y-m-d'),
        'nominal_dibayar' => 300000,
        'metode_bayar' => 'Tunai',
        'petugas_id' => $this->financeUser->id,
    ]);

    $controller = new FinanceReportController();
    $request = Request::create("/finance/laporan/cetak-resi/{$pembayaran->id}", 'GET');

    $response = $controller->cetakResi($request, $pembayaran->id);
    expect($response)->not->toBeNull();

    $log = DB::table('activity_log')
        ->where('event', 'download')
        ->where('causer_id', $this->financeUser->id)
        ->latest('id')
        ->first();

    expect($log)->not->toBeNull();
    expect($log->log_name)->toBe('keuangan');
    expect($log->description)->toContain('KW-AUDIT-TEST-99');
    expect($log->siswa_id)->toBe($siswa->id);
});

/*
|--------------------------------------------------------------------------
| C. Academic & Rapor Downloads
|--------------------------------------------------------------------------
*/

test('rapor PDF download records academic audit log with student id', function () {
    $this->actingAs($this->adminUser);

    $siswa = Siswa::first();
    $activeSemId = DB::table('semester')
        ->join('tahun_ajaran', 'semester.tahun_ajaran_id', '=', 'tahun_ajaran.id')
        ->where('tahun_ajaran.status_aktif', true)
        ->where('semester.status_aktif', true)
        ->value('semester.id') ?? (Semester::first()->id ?? 1);

    $rapor = Rapor::create([
        'siswa_id' => $siswa->id,
        'semester_id' => $activeSemId,
        'kelas_id' => $siswa->kelas_id,
        'qr_code_hash' => 'RAP-AUDIT-TEST-HASH',
        'tanggal_terbit' => date('Y-m-d'),
        'status' => 'diterbitkan',
    ]);

    $controller = new RaporPdfController();
    $response = $controller->previewPdf($siswa->id);
    expect($response->getStatusCode())->toBe(200);

    $log = DB::table('activity_log')
        ->where('event', 'download')
        ->where('causer_id', $this->adminUser->id)
        ->latest('id')
        ->first();

    expect($log)->not->toBeNull();
    expect($log->log_name)->toBe('akademik');
    expect($log->siswa_id)->toBe($siswa->id);
    expect($log->description)->toContain('Rapor Kurikulum Merdeka');
});

/*
|--------------------------------------------------------------------------
| D. Public Document Verification Scans
|--------------------------------------------------------------------------
*/

test('public QR code verification records verify audit log for valid and invalid scans', function () {
    $siswa = Siswa::first();
    $activeSem = Semester::where('status_aktif', true)->first() ?? Semester::first();

    $validHash = 'RAP-VERIFY-TEST-12345';
    Rapor::create([
        'siswa_id' => $siswa->id,
        'semester_id' => $activeSem->id,
        'kelas_id' => $siswa->kelas_id,
        'qr_code_hash' => $validHash,
        'tanggal_terbit' => date('Y-m-d'),
        'status' => 'diterbitkan',
    ]);

    $controller = new DocumentVerificationController();

    // 1. Valid scan
    $controller->verify($validHash);
    $validLog = DB::table('activity_log')
        ->where('event', 'verify')
        ->latest('id')
        ->first();

    expect($validLog)->not->toBeNull();
    expect($validLog->description)->toContain('Berhasil');
    $validProps = json_decode($validLog->properties, true);
    expect($validProps['is_valid'])->toBeTrue();

    // 2. Invalid scan
    $invalidHash = 'FAKE-INVALID-QR-CODE-999';
    $controller->verify($invalidHash);
    $invalidLog = DB::table('activity_log')
        ->where('event', 'verify')
        ->latest('id')
        ->first();

    expect($invalidLog)->not->toBeNull();
    expect($invalidLog->description)->toContain('Gagal');
    $invalidProps = json_decode($invalidLog->properties, true);
    expect($invalidProps['is_valid'])->toBeFalse();
});
