<?php

use App\Models\CapaianGuru;
use App\Models\Guru;
use App\Models\JenisTagihan;
use App\Models\Siswa;
use App\Models\Tabungan;
use App\Models\Tagihan;
use App\Models\User;
use App\Livewire\Finance\TabunganSiswa;
use App\Livewire\Guru\CapaianPengembanganDiri;
use App\Livewire\Guru\InputNilaiTahfidz;
use App\Livewire\Guru\ManajemenKurikulumMerdeka;
use App\Livewire\Guru\ManajemenRemedial;
use App\Livewire\Murid\RaporNilai;
use App\Livewire\SuperAdmin\TataKelola\CapaianPengembanganGuru;
use App\Livewire\SuperAdmin\TataKelola\SystemErrorLog;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    $this->artisan('db:seed', ['--class' => 'KomponenNilaiSeeder']);
    $this->artisan('db:seed', ['--class' => 'PengaturanSeeder']);
    $this->artisan('db:seed', ['--class' => 'DemoDataSeeder']);

    $this->userAdmin = User::where('username', 'admin')->first();
    $this->userFinance = User::where('username', 'finance')->first();
    
    // Guru Umum
    $this->userGuruUmum = User::where('username', 'budi')->first();
    
    // Guru Tahfidz
    $this->userGuruTahfidz = User::whereHas('guru', fn($q) => $q->where('jenis_guru', 'tahfidz'))->first();
    
    if (!$this->userGuruTahfidz) {
        $guruTahfidzModel = Guru::create([
            'user_id' => User::factory()->create(['role_id' => 3])->id,
            'nip' => '199901012026',
            'jenis_guru' => 'tahfidz',
            'status_aktif' => true,
        ]);
        $this->userGuruTahfidz = $guruTahfidzModel->user;
    }

    $this->siswa = Siswa::first();
    $this->userMurid = $this->siswa->user;
});

test('1. savings withdrawal extreme negative zero and over balance rejected', function () {
    $this->actingAs($this->userFinance);

    // Initial Deposit 100,000
    Tabungan::create([
        'siswa_id' => $this->siswa->id,
        'petugas_id' => $this->userFinance->id,
        'kode_transaksi' => 'TAB-EX-001',
        'jenis' => 'setor',
        'nominal' => 100000,
        'saldo_akhir' => 100000,
        'tanggal' => date('Y-m-d'),
        'keterangan' => 'Deposit Awal',
    ]);

    // Attempt negative withdrawal
    Livewire::test(TabunganSiswa::class)
        ->call('openTransactionModal', $this->siswa->id, 'tarik')
        ->set('nominal', -50000)
        ->set('tanggal', date('Y-m-d'))
        ->call('saveTransaction')
        ->assertHasErrors(['nominal']);

    // Attempt zero withdrawal
    Livewire::test(TabunganSiswa::class)
        ->call('openTransactionModal', $this->siswa->id, 'tarik')
        ->set('nominal', 0)
        ->set('tanggal', date('Y-m-d'))
        ->call('saveTransaction')
        ->assertHasErrors(['nominal']);

    // Attempt withdrawal over balance by 1 rupiah
    Livewire::test(TabunganSiswa::class)
        ->call('openTransactionModal', $this->siswa->id, 'tarik')
        ->set('nominal', 100001)
        ->set('tanggal', date('Y-m-d'))
        ->call('saveTransaction')
        ->assertHasErrors(['nominal']);

    // Balance remains untouched
    $latest = Tabungan::where('siswa_id', $this->siswa->id)->latest()->first();
    expect((float) $latest->saldo_akhir)->toEqual(100000.0);
});

test('2. role abuse and escalation blocked with 403', function () {
    // Ensure Guru Tahfidz has jenis_guru = tahfidz
    $this->userGuruTahfidz->guru->update(['jenis_guru' => 'tahfidz']);
    $this->actingAs($this->userGuruTahfidz);
    Livewire::test(ManajemenRemedial::class)->assertStatus(403);

    // Ensure Guru Umum has jenis_guru = umum
    $this->userGuruUmum->guru->update(['jenis_guru' => 'umum']);
    $this->actingAs($this->userGuruUmum);
    Livewire::test(InputNilaiTahfidz::class)->assertStatus(403);

    // Murid accessing Finance Tabungan -> 403
    $this->actingAs($this->userMurid);
    Livewire::test(TabunganSiswa::class)->assertStatus(403);

    // Murid accessing System Error Log -> 403
    Livewire::test(SystemErrorLog::class)->assertStatus(403);
});

test('3. strange characters xss and invalid drive url handling', function () {
    $this->actingAs($this->userGuruUmum);

    $xssJudul = '<script>alert("XSS")</script> 𝕱3 𝕾𝕴𝕬𝕶𝕬𝕯 🔥💩';

    // Submit with XSS string
    Livewire::test(CapaianPengembanganDiri::class)
        ->call('openCreate')
        ->set('judul', $xssJudul)
        ->set('kategori', 'pelatihan')
        ->set('link_gdrive', 'https://drive.google.com/file/d/test1234/view')
        ->set('deskripsi', 'Uji Coba String Ekstrem')
        ->call('save')
        ->assertHasNoErrors();

    $record = CapaianGuru::where('judul', $xssJudul)->first();
    expect($record)->not->toBeNull();

    // Submit with invalid drive URL (javascript protocol)
    Livewire::test(CapaianPengembanganDiri::class)
        ->call('openCreate')
        ->set('judul', 'Uji Coba Script Protocol')
        ->set('kategori', 'pelatihan')
        ->set('link_gdrive', 'javascript:alert("hacked")')
        ->call('save')
        ->assertHasErrors(['link_gdrive']);
});

test('4. super admin evaluation out of bounds score and stale id handled', function () {
    $this->actingAs($this->userAdmin);

    $capaian = CapaianGuru::create([
        'guru_id' => $this->userGuruUmum->guru->id,
        'judul' => 'Capaian Pembelajaran Ekstrem',
        'kategori' => 'sertifikasi',
        'status_penilaian' => 'diajukan',
    ]);

    // Out of bounds score -50
    Livewire::test(CapaianPengembanganGuru::class)
        ->call('openEvaluateModal', $capaian->id)
        ->set('skor_nilai', -50)
        ->set('tanggal_penilaian', date('Y-m-d'))
        ->call('saveEvaluation')
        ->assertHasErrors(['skor_nilai']);

    // Out of bounds score 150
    Livewire::test(CapaianPengembanganGuru::class)
        ->call('openEvaluateModal', $capaian->id)
        ->set('skor_nilai', 150)
        ->set('tanggal_penilaian', date('Y-m-d'))
        ->call('saveEvaluation')
        ->assertHasErrors(['skor_nilai']);

    // Non existent Stale ID
    Livewire::test(CapaianPengembanganGuru::class)
        ->call('openEvaluateModal', 999999)
        ->assertStatus(200);
});

test('5. corrupted and malformed laravel log file handling', function () {
    $logPath = storage_path('logs/laravel.log');
    $corruptedText = "MALFORMED_BINARY_\x00\x01\x02_NON_STANDARD_TEXT_WITHOUT_TIMESTAMP_OR_LOG_LEVEL\nSECOND_CORRUPTED_LINE";
    
    File::put($logPath, $corruptedText);

    $this->actingAs($this->userAdmin);

    Livewire::test(SystemErrorLog::class)
        ->assertStatus(200)
        ->assertSee('MALFORMED_BINARY');
});

test('6. student with overdue bills access locked in portal', function () {
    $jenisSpp = JenisTagihan::first() ?? JenisTagihan::create(['nama' => 'SPP Bulanan', 'tipe' => 'spp', 'default_nominal' => 300000]);
    $tahun = \App\Models\TahunAjaran::first();
    $semester = \App\Models\Semester::first();

    // Create overdue bill (past due date)
    Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenisSpp->id,
        'tahun_ajaran_id' => $tahun->id ?? null,
        'semester_id' => $semester->id ?? null,
        'kode_tagihan' => 'TAG-OVERDUE-001',
        'nominal' => 300000,
        'sisa_tagihan' => 300000,
        'status' => 'belum_bayar',
        'tanggal_tagihan' => now()->subMonths(2)->format('Y-m-d'),
        'jatuh_tempo' => now()->subMonth()->format('Y-m-d'),
    ]);

    $this->actingAs($this->userMurid);

    Livewire::test(RaporNilai::class)
        ->assertStatus(200)
        ->assertSee('Akses Rapor Terkunci');
});
