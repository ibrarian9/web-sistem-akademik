<?php

use App\Models\CapaianGuru;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Tabungan;
use App\Models\User;
use App\Services\AuditLogger;
use App\Livewire\SuperAdmin\TataKelola\AuditLog;
use App\Livewire\SuperAdmin\TataKelola\SystemErrorLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    $this->artisan('db:seed', ['--class' => 'KomponenNilaiSeeder']);
    $this->artisan('db:seed', ['--class' => 'PengaturanSeeder']);
    $this->artisan('db:seed', ['--class' => 'DemoDataSeeder']);

    $this->userAdmin = User::where('username', 'admin')->first();
    $this->userGuru = User::where('username', 'budi')->first();
    $this->guru = $this->userGuru->guru;
    $this->siswa = Siswa::first();
});

test('model create action automatically logs to activity_log with ip and user agent', function () {
    $this->actingAs($this->userAdmin);

    $tabungan = Tabungan::create([
        'siswa_id' => $this->siswa->id,
        'petugas_id' => $this->userAdmin->id,
        'kode_transaksi' => 'TAB-AUDIT-001',
        'jenis' => 'setor',
        'nominal' => 150000,
        'saldo_akhir' => 150000,
        'tanggal' => date('Y-m-d'),
        'keterangan' => 'Uji Coba Audit Trait',
    ]);

    $log = DB::table('activity_log')
        ->where('subject_type', Tabungan::class)
        ->where('subject_id', $tabungan->id)
        ->where('event', 'created')
        ->first();

    expect($log)->not->toBeNull();
    expect($log->causer_id)->toEqual($this->userAdmin->id);
    expect($log->ip_address)->not->toBeNull();
    expect($log->user_agent)->not->toBeNull();
});

test('model update and delete action automatically logs to activity_log', function () {
    $this->actingAs($this->userAdmin);

    $tabungan = Tabungan::create([
        'siswa_id' => $this->siswa->id,
        'petugas_id' => $this->userAdmin->id,
        'kode_transaksi' => 'TAB-AUDIT-002',
        'jenis' => 'setor',
        'nominal' => 50000,
        'saldo_akhir' => 50000,
        'tanggal' => date('Y-m-d'),
        'keterangan' => 'Sebelum Update',
    ]);

    $tabungan->update(['keterangan' => 'Setelah Update']);

    $updateLog = DB::table('activity_log')
        ->where('subject_type', Tabungan::class)
        ->where('subject_id', $tabungan->id)
        ->where('event', 'updated')
        ->first();

    expect($updateLog)->not->toBeNull();
    expect($updateLog->causer_id)->toEqual($this->userAdmin->id);

    $tabunganId = $tabungan->id;
    $tabungan->delete();

    $deleteLog = DB::table('activity_log')
        ->where('subject_type', Tabungan::class)
        ->where('subject_id', $tabunganId)
        ->where('event', 'deleted')
        ->first();

    expect($deleteLog)->not->toBeNull();
});

test('audit log interface allows super admin to search and filter logs', function () {
    AuditLogger::log('created', 'Tes Manual Logging untuk Audit UI', $this->siswa, [
        'log_name' => 'test',
    ]);

    $this->actingAs($this->userAdmin);

    Livewire::test(AuditLog::class)
        ->assertStatus(200)
        ->set('search', 'Tes Manual Logging')
        ->assertSee('Tes Manual Logging')
        ->set('filterEvent', 'created')
        ->assertSee('Tes Manual Logging');
});

test('system error log viewer parses laravel log file correctly', function () {
    $logPath = storage_path('logs/laravel.log');
    $sampleLogContent = "[2026-08-13 00:00:00] local.ERROR: Simulated Runtime Exception in System Test {\"exception\":\"[object] (Exception)\"}\n";
    
    File::append($logPath, $sampleLogContent);

    $this->actingAs($this->userAdmin);

    Livewire::test(SystemErrorLog::class)
        ->assertStatus(200)
        ->assertSee('Simulated Runtime Exception in System Test')
        ->set('filterLevel', 'ERROR')
        ->assertSee('Simulated Runtime Exception in System Test');
});

test('end-to-end system test: crud operations trigger audit logging and show in admin panel', function () {
    $this->actingAs($this->userAdmin);

    // 1. Perform CRUD on CapaianGuru
    $capaian = CapaianGuru::create([
        'guru_id' => $this->guru->id,
        'judul' => 'Sertifikasi Asesor SD 2026',
        'kategori' => 'sertifikasi',
        'link_gdrive' => 'https://drive.google.com/file/d/e2etest/view',
        'deskripsi' => 'Pengajuan Sertifikasi E2E Test',
        'status_penilaian' => 'diajukan',
    ]);

    // 2. Verify AuditLog written in DB
    $auditEntry = DB::table('activity_log')
        ->where('subject_type', CapaianGuru::class)
        ->where('subject_id', $capaian->id)
        ->first();

    expect($auditEntry)->not->toBeNull();
    expect($auditEntry->ip_address)->not->toBeNull();

    // 3. Admin views AuditLog UI
    Livewire::test(AuditLog::class)
        ->assertStatus(200)
        ->set('search', 'Sertifikasi Asesor SD 2026')
        ->assertSee('Sertifikasi Asesor SD 2026');

    // 4. Admin views System Error Log UI
    Livewire::test(SystemErrorLog::class)
        ->assertStatus(200)
        ->assertSee('System Error Log Viewer');
});
