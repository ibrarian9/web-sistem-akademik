<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Tagihan;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use App\Models\Pembayaran;
use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;
use App\Models\Peminjaman;
use App\Models\GajiGuru;
use App\Models\Notifikasi;
use Livewire\Livewire;
use App\Livewire\Finance\ManajemenPeminjaman;
use App\Livewire\Finance\ManajemenGajiGuru;
use App\Livewire\Finance\Laporan\LaporanTunggakan;
use App\Livewire\Finance\Laporan\LaporanPemasukan;
use App\Livewire\Finance\Laporan\LaporanPengeluaran;
use App\Livewire\Murid\RaporNilai;

beforeEach(function () {
    // Seed setups
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    $this->artisan('db:seed', ['--class' => 'KomponenNilaiSeeder']);
    $this->artisan('db:seed', ['--class' => 'PengaturanSeeder']);
    $this->artisan('db:seed', ['--class' => 'DemoDataSeeder']);
    $this->artisan('db:seed', ['--class' => 'JenisTagihanSeeder']);
    $this->artisan('db:seed', ['--class' => 'KategoriPengeluaranSeeder']);

    // Find users
    $this->userFinance = User::whereHas('role', function ($q) {
        $q->where('nama', 'finance');
    })->first();

    $this->userGuru = User::whereHas('role', function ($q) {
        $q->where('nama', 'guru');
    })->first();

    $this->userMurid = User::whereHas('role', function ($q) {
        $q->where('nama', 'murid');
    })->first();

    $this->guru = $this->userGuru->guru;
    $this->siswa = $this->userMurid->siswa;
});

test('finance can view and create peminjaman (loan)', function () {
    $this->actingAs($this->userFinance);

    Livewire::test(ManajemenPeminjaman::class)
        ->assertStatus(200)
        ->set('guru_id', $this->guru->id)
        ->set('nominal', 1200000)
        ->set('tenor_bulan', 12)
        ->call('savePeminjaman')
        ->assertHasNoErrors();

    $loan = Peminjaman::where('guru_id', $this->guru->id)->first();
    expect($loan)->not->toBeNull();
    expect($loan->nominal)->toEqual(1200000);
    expect($loan->tenor_bulan)->toEqual(12);
    expect($loan->cicilan_per_bulan)->toEqual(100000); // 1,200,000 / 12
    expect($loan->status)->toEqual('berjalan');
});

test('finance can generate, edit, and pay salary drafts', function () {
    $this->actingAs($this->userFinance);

    // Create loan for the teacher to check automatic deduction
    $loan = Peminjaman::create([
        'guru_id' => $this->guru->id,
        'nominal' => 1200000,
        'tenor_bulan' => 12,
        'cicilan_per_bulan' => 100000,
        'sisa_pinjaman' => 1200000,
        'status' => 'berjalan',
        'tanggal_pinjam' => now()->toDateString(),
    ]);

    // Ensure Guru status is active to generate draft
    $this->guru->update(['status_aktif' => true]);

    // 1. Generate Draft
    Livewire::test(ManajemenGajiGuru::class)
        ->assertStatus(200)
        ->set('generateBulan', 'Januari')
        ->set('generateTahun', 2026)
        ->call('generateDrafts')
        ->assertHasNoErrors();

    $gaji = GajiGuru::where('guru_id', $this->guru->id)
        ->where('bulan', 'Januari')
        ->where('tahun', 2026)
        ->first();

    expect($gaji)->not->toBeNull();
    expect($gaji->gaji_pokok)->toEqual(2500000);
    expect($gaji->potongan_peminjaman)->toEqual(100000); // cicilan per bulan
    expect($gaji->status)->toEqual('draft');

    // 2. Edit Draft
    Livewire::test(ManajemenGajiGuru::class)
        ->call('openEditModal', $gaji->id)
        ->set('editGajiPokok', 2700000)
        ->call('saveEdit')
        ->assertHasNoErrors();

    $gaji->refresh();
    expect($gaji->gaji_pokok)->toEqual(2700000);
    expect($gaji->total_diterima)->toEqual(2950000); // (2700000 + 150000 + 200000) - 100000 - 0

    // 3. Payout Salary
    Livewire::test(ManajemenGajiGuru::class)
        ->call('paySalary', $gaji->id)
        ->assertHasNoErrors();

    $gaji->refresh();
    expect($gaji->status)->toEqual('dibayar');
    expect($gaji->pengeluaran_id)->not->toBeNull();

    // Verify expenditure transaction
    $exp = Pengeluaran::find($gaji->pengeluaran_id);
    expect($exp)->not->toBeNull();
    expect($exp->jumlah)->toEqual(2950000);

    // Verify loan deduction is applied
    $loan->refresh();
    expect($loan->sisa_pinjaman)->toEqual(1100000);

    // Verify in-app notification sent to the teacher
    $notif = Notifikasi::where('user_id', $this->guru->user_id)
        ->where('judul', 'Gaji Telah Dibayarkan')
        ->first();
    expect($notif)->not->toBeNull();
});

test('finance can view and export reports', function () {
    $this->actingAs($this->userFinance);

    // 1. Laporan Tunggakan
    Livewire::test(LaporanTunggakan::class)
        ->assertStatus(200)
        ->call('exportCsv')
        ->assertFileDownloaded()
        ->call('exportPdf')
        ->assertFileDownloaded();

    // 2. Laporan Pemasukan
    Livewire::test(LaporanPemasukan::class)
        ->assertStatus(200)
        ->call('exportCsv')
        ->assertFileDownloaded()
        ->call('exportPdf')
        ->assertFileDownloaded();

    // 3. Laporan Pengeluaran
    Livewire::test(LaporanPengeluaran::class)
        ->assertStatus(200)
        ->call('exportCsv')
        ->assertFileDownloaded()
        ->call('exportPdf')
        ->assertFileDownloaded();
});

test('student can download official report card pdf', function () {
    $this->actingAs($this->userMurid);

    // Ensure student has no outstanding SPP bills
    Tagihan::where('siswa_id', $this->siswa->id)->delete();

    // Setup an official report
    $activeTA = TahunAjaran::where('status_aktif', true)->first();
    $activeSemester = DB::table('semester')
        ->where('tahun_ajaran_id', $activeTA->id)
        ->where('status_aktif', true)
        ->first();

    $rapor = App\Models\Rapor::create([
        'siswa_id' => $this->siswa->id,
        'kelas_id' => $this->siswa->kelas_id,
        'semester_id' => $activeSemester->id,
        'catatan_wali_kelas' => 'Teruskan belajarmu!',
        'tanggal_terbit' => now()->toDateString(),
    ]);

    Livewire::test(RaporNilai::class)
        ->assertStatus(200)
        ->assertSee('Laporan Hasil Belajar Semester')
        ->call('downloadPdf')
        ->assertFileDownloaded();
});
