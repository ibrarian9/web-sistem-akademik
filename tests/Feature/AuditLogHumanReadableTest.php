<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\JenisTagihan;
use App\Models\KategoriPengeluaran;
use App\Models\Pengeluaran;
use App\Services\AuditLogger;
use App\Services\AuditLogFormatter;
use App\Livewire\SuperAdmin\TataKelola\AuditLog;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

    $roleSuperAdmin = Role::where('nama', 'super_admin')->first();
    $roleFinance = Role::where('nama', 'finance')->first();
    $roleMurid = Role::where('nama', 'murid')->first();

    $this->superAdmin = User::create([
        'nama' => 'Admin Utama',
        'username' => 'admin_audit',
        'email' => 'admin_audit@siakad.or.id',
        'password' => bcrypt('password123'),
        'role_id' => $roleSuperAdmin->id,
        'status' => 'aktif',
    ]);

    $this->financeUser = User::create([
        'nama' => 'Siti Aminah, S.E.',
        'username' => 'siti_finance',
        'email' => 'siti@siakad.or.id',
        'password' => bcrypt('password123'),
        'role_id' => $roleFinance->id,
        'status' => 'aktif',
    ]);

    $this->ta = TahunAjaran::create([
        'nama' => '2025/2026',
        'status_aktif' => true,
    ]);

    $this->semester = Semester::create([
        'tahun_ajaran_id' => $this->ta->id,
        'semester' => 'ganjil',
        'tanggal_mulai' => '2025-07-01',
        'tanggal_selesai' => '2025-12-31',
        'status_aktif' => true,
    ]);

    $this->kelas = Kelas::create([
        'nama_kelas' => '7A',
        'tingkat' => 7,
        'semester_id' => $this->semester->id,
    ]);

    $uMurid = User::create([
        'nama' => 'Fulan Ahmad Santoso',
        'username' => 'fulan_santoso',
        'email' => 'fulan_santoso@siakad.or.id',
        'password' => bcrypt('password123'),
        'role_id' => $roleMurid->id,
        'status' => 'aktif',
    ]);

    $this->siswa = Siswa::create([
        'user_id' => $uMurid->id,
        'kelas_id' => $this->kelas->id,
        'nis' => '10045',
        'nisn' => '2000000045',
        'tanggal_masuk' => '2025-07-01',
        'status' => 'aktif',
    ]);

    $this->kategori = KategoriPengeluaran::create([
        'nama' => 'ATK & Fotokopi',
        'jenis' => 'operasional',
    ]);
});

test('AuditLogFormatter cleans raw JSON and numeric IDs from descriptions', function () {
    $rawDescWithJson = 'Membuat data Pengeluaran ({"id":10,"nama":"ATK & Fotokopi","jenis":"operasional","created_at":"2026-08-31T04:26:59.000000Z"})';
    $cleaned = AuditLogFormatter::cleanDescription($rawDescWithJson);

    expect($cleaned)->not->toContain('{"id":10');
    expect($cleaned)->toContain('ATK & Fotokopi');
    expect($cleaned)->toBe('Mencatat Pengeluaran Kas (ATK & Fotokopi)');

    $rawDescWithId = 'Gagal menghapus data guru ID 12: Data tidak ditemukan';
    $cleanedId = AuditLogFormatter::cleanDescription($rawDescWithId);
    expect($cleanedId)->not->toContain('ID 12');
    expect($cleanedId)->toBe('Gagal menghapus data guru: Data tidak ditemukan');
});

test('AuditLogFormatter converts raw User-Agent to clean human-readable device and browser', function () {
    $rawAgent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36';
    $parsed = AuditLogFormatter::parseUserAgent($rawAgent);

    expect($parsed['short'])->toBe('Chrome di Linux');
    expect($parsed['browser'])->toBe('Chrome');
    expect($parsed['platform'])->toBe('Linux');
    expect($parsed['device'])->toBe('Komputer Desktop');
});

test('AuditLogFormatter transforms raw attribute changes into human-readable data without raw IDs or raw JSON', function () {
    $rawChanges = [
        'kategori_pengeluaran_id' => $this->kategori->id,
        'jumlah' => 250000,
        'tanggal' => '2026-09-10 00:00:00',
        'keterangan' => 'Beli kertas HVS 5 rim',
        'petugas_id' => $this->financeUser->id,
        'siswa_id' => $this->siswa->id,
        'id' => 999,
        'created_at' => '2026-09-10 14:00:00',
        'updated_at' => '2026-09-10 14:00:00',
    ];

    $formatted = AuditLogFormatter::formatAttributeChanges($rawChanges);

    // Pastikan internal fields id, created_at, updated_at terfilter (tidak muncul)
    $keys = array_column($formatted, 'key');
    expect($keys)->not->toContain('id');
    expect($keys)->not->toContain('created_at');
    expect($keys)->not->toContain('updated_at');

    // Pastikan kategori_pengeluaran_id di-resolve menjadi nama kategori (bukan ID angka)
    $kategoriItem = collect($formatted)->firstWhere('key', 'kategori_pengeluaran_id');
    expect($kategoriItem['label'])->toBe('Kategori Pos Pengeluaran');
    expect($kategoriItem['value'])->toBe('ATK & Fotokopi');

    // Pastikan jumlah diformat ke Rupiah
    $jumlahItem = collect($formatted)->firstWhere('key', 'jumlah');
    expect($jumlahItem['label'])->toBe('Jumlah Uang');
    expect($jumlahItem['value'])->toBe('Rp 250.000');

    // Pastikan tanggal diformat ke Bahasa Indonesia
    $tanggalItem = collect($formatted)->firstWhere('key', 'tanggal');
    expect($tanggalItem['label'])->toBe('Tanggal Transaksi');
    expect($tanggalItem['value'])->toBe('10 September 2026');

    // Pastikan petugas_id di-resolve ke nama petugas asli
    $petugasItem = collect($formatted)->firstWhere('key', 'petugas_id');
    expect($petugasItem['label'])->toBe('Petugas Pelaksana');
    expect($petugasItem['value'])->toContain('Siti Aminah, S.E.');

    // Pastikan siswa_id di-resolve ke nama siswa asli beserta NIS
    $siswaItem = collect($formatted)->firstWhere('key', 'siswa_id');
    expect($siswaItem['label'])->toBe('Nama Siswa Penerima');
    expect($siswaItem['value'])->toContain('Fulan Ahmad Santoso');
    expect($siswaItem['value'])->toContain('10045');
});

test('AuditLog component displays cleaned descriptions and human-readable modal detail without raw JSON', function () {
    $this->actingAs($this->superAdmin);

    // Catat log pengeluaran
    $rawJsonDesc = 'Membuat data Pengeluaran ({"id":' . $this->kategori->id . ',"nama":"ATK & Fotokopi","jenis":"operasional"})';
    AuditLogger::log('created', $rawJsonDesc, null, [
        'changes' => [
            'kategori_pengeluaran_id' => $this->kategori->id,
            'jumlah' => 150000,
            'petugas_id' => $this->financeUser->id,
            'keterangan' => 'Pembelian Spidol Whiteboard',
            'id' => 777,
            'created_at' => '2026-09-10 10:00:00',
        ],
        'user_agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36',
    ]);

    $logEntry = DB::table('activity_log')->latest('id')->first();

    $livewire = Livewire::test(AuditLog::class)
        ->assertStatus(200)
        // Tabel utama harus menampilkan deskripsi bersih dan perangkat manusiawi
        ->assertSee('Mencatat Pengeluaran Kas (ATK & Fotokopi)')
        ->assertDontSee('{"id":')
        ->assertSee('Chrome di Linux')
        // Buka modal detail
        ->call('openDetail', $logEntry->id)
        ->assertSet('showDetailModal', true)
        // Di modal detail: tidak boleh ada JSON mentah dan tidak boleh ada ID mentah
        ->assertSee('Rincian Data yang Tercatat')
        ->assertSee('Kategori Pos Pengeluaran')
        ->assertSee('ATK & Fotokopi')
        ->assertSee('Rp 150.000')
        ->assertSee('Siti Aminah, S.E.')
        ->assertSee('Pembelian Spidol Whiteboard')
        ->assertDontSee('Raw JSON Tree')
        ->assertDontSee('{"kategori_pengeluaran_id":');
});
