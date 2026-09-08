<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Guru;
use App\Models\GajiGuru;
use App\Models\Pengeluaran;
use App\Livewire\Finance\ManajemenGajiGuru;
use App\Livewire\Finance\DetailGajiGuru;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    $this->artisan('db:seed', ['--class' => 'PengaturanSeeder']);

    Storage::fake('public');

    $roleFinance = Role::where('nama', 'finance')->first();
    $this->userFinance = User::whereHas('role', fn($q) => $q->where('nama', 'finance'))->first() ?? User::create([
        'nama' => 'Staf Finance Test Gaji',
        'username' => 'finance_test_gaji',
        'email' => 'finance_gaji@test.com',
        'password' => bcrypt('password'),
        'role_id' => $roleFinance->id,
    ]);

    $roleGuru = Role::where('nama', 'guru')->first();
    $this->userGuru = User::create([
        'nama' => 'Ustadz Ahmad Payroll',
        'username' => 'ahmad_payroll_test',
        'email' => 'ahmad_payroll@test.com',
        'password' => bcrypt('password'),
        'role_id' => $roleGuru->id,
    ]);

    $this->guru = Guru::create([
        'user_id' => $this->userGuru->id,
        'nip' => '99887766',
        'status' => 'aktif',
        'jabatan' => 'Guru Tahfizh',
        'tanggal_masuk' => '2020-01-01',
    ]);
});

test('staf keuangan dapat membuka modal bayar gaji dan mengunggah foto struk/bukti tf', function () {
    $salary = GajiGuru::create([
        'guru_id' => $this->guru->id,
        'bulan' => 'Januari',
        'tahun' => 2026,
        'gaji_pokok' => 2500000,
        'total_bruto' => 2500000,
        'total_diterima' => 2500000,
        'status' => 'draft',
        'sumber_dana' => 'Yayasan',
        'tanggal_bayar' => now()->toDateString(),
    ]);

    $fakeImage = UploadedFile::fake()->image('bukti_transfer_gaji.jpg', 600, 400);

    Livewire::actingAs($this->userFinance)
        ->test(ManajemenGajiGuru::class)
        ->call('openPayModal', $salary->id)
        ->assertSet('showPayModal', true)
        ->assertSet('paySalaryId', $salary->id)
        ->set('payTanggalBayar', '2026-01-25')
        ->set('payCatatan', 'Transfer via BSI ke Rekening Guru')
        ->set('payBuktiFoto', $fakeImage)
        ->call('confirmPaySalary')
        ->assertSet('showPayModal', false)
        ->assertHasNoErrors();

    $salary->refresh();
    expect($salary->status)->toBe('dibayar');
    expect($salary->bukti_bayar)->not->toBeNull();
    expect($salary->pengeluaran_id)->not->toBeNull();

    Storage::disk('public')->assertExists($salary->bukti_bayar);

    $pengeluaran = Pengeluaran::find($salary->pengeluaran_id);
    expect($pengeluaran)->not->toBeNull();
    expect($pengeluaran->bukti)->toBe($salary->bukti_bayar);
    expect($pengeluaran->keterangan)->toContain('Transfer via BSI');
});

test('upload foto bukti saat bayar gaji bersifat opsional', function () {
    $salary = GajiGuru::create([
        'guru_id' => $this->guru->id,
        'bulan' => 'Februari',
        'tahun' => 2026,
        'gaji_pokok' => 2000000,
        'total_bruto' => 2000000,
        'total_diterima' => 2000000,
        'status' => 'draft',
        'sumber_dana' => 'Yayasan',
        'tanggal_bayar' => now()->toDateString(),
    ]);

    Livewire::actingAs($this->userFinance)
        ->test(ManajemenGajiGuru::class)
        ->call('openPayModal', $salary->id)
        ->set('payTanggalBayar', '2026-02-25')
        ->set('payBuktiFoto', null)
        ->call('confirmPaySalary')
        ->assertHasNoErrors();

    $salary->refresh();
    expect($salary->status)->toBe('dibayar');
    expect($salary->bukti_bayar)->toBeNull();
    expect($salary->pengeluaran_id)->not->toBeNull();
});

test('validasi foto bukti pembayaran menolak file selain gambar atau ukuran lebih dari 2mb', function () {
    $salary = GajiGuru::create([
        'guru_id' => $this->guru->id,
        'bulan' => 'Maret',
        'tahun' => 2026,
        'gaji_pokok' => 2000000,
        'total_bruto' => 2000000,
        'total_diterima' => 2000000,
        'status' => 'draft',
        'sumber_dana' => 'Yayasan',
        'tanggal_bayar' => now()->toDateString(),
    ]);

    $fakeFile = UploadedFile::fake()->image('foto_terlalu_besar.jpg')->size(2500);

    Livewire::actingAs($this->userFinance)
        ->test(ManajemenGajiGuru::class)
        ->call('openPayModal', $salary->id)
        ->set('payBuktiFoto', $fakeFile)
        ->call('confirmPaySalary')
        ->assertHasErrors(['payBuktiFoto']);

    $salary->refresh();
    expect($salary->status)->toBe('draft');
});

test('dapat memperbarui dan menghapus foto bukti pembayaran pada detail modal', function () {
    $kategori = \App\Models\KategoriPengeluaran::firstOrCreate(
        ['nama' => 'Gaji Guru'],
        ['jenis' => 'operasional']
    );

    $fakeInitial = UploadedFile::fake()->image('bukti_awal.png', 400, 400);
    $initialPath = $fakeInitial->store('bukti-gaji', 'public');

    $pengeluaran = Pengeluaran::create([
        'kategori_pengeluaran_id' => $kategori->id,
        'jumlah' => 3000000,
        'tanggal' => now()->toDateString(),
        'keterangan' => 'Gaji Guru Test',
        'petugas_id' => $this->userFinance->id,
        'bukti' => $initialPath,
    ]);

    $salary = GajiGuru::create([
        'guru_id' => $this->guru->id,
        'pengeluaran_id' => $pengeluaran->id,
        'bulan' => 'April',
        'tahun' => 2026,
        'gaji_pokok' => 3000000,
        'total_bruto' => 3000000,
        'total_diterima' => 3000000,
        'status' => 'dibayar',
        'bukti_bayar' => $initialPath,
        'tanggal_bayar' => now()->toDateString(),
    ]);

    Storage::disk('public')->assertExists($initialPath);

    // Update foto
    $fakeNew = UploadedFile::fake()->image('bukti_revisi.jpg', 600, 600);
    Livewire::actingAs($this->userFinance)
        ->test(ManajemenGajiGuru::class)
        ->call('openDetailModal', $salary->id)
        ->set('detailBuktiFoto', $fakeNew)
        ->call('updateSalaryBuktiFoto', $salary->id)
        ->assertHasNoErrors();

    $salary->refresh();
    $pengeluaran->refresh();
    expect($salary->bukti_bayar)->not->toBe($initialPath);
    expect($pengeluaran->bukti)->toBe($salary->bukti_bayar);
    Storage::disk('public')->assertExists($salary->bukti_bayar);
    Storage::disk('public')->assertMissing($initialPath);

    // Hapus foto
    Livewire::actingAs($this->userFinance)
        ->test(ManajemenGajiGuru::class)
        ->call('deleteSalaryBuktiFoto', $salary->id)
        ->assertHasNoErrors();

    $salary->refresh();
    $pengeluaran->refresh();
    expect($salary->bukti_bayar)->toBeNull();
    expect($pengeluaran->bukti)->toBeNull();
});

test('detail gaji guru perorangan juga mendukung pembaruan foto bukti', function () {
    $fakeImg = UploadedFile::fake()->image('bukti_guru_detail.jpg', 400, 400);

    $salary = GajiGuru::create([
        'guru_id' => $this->guru->id,
        'bulan' => 'Mei',
        'tahun' => 2026,
        'gaji_pokok' => 2800000,
        'total_bruto' => 2800000,
        'total_diterima' => 2800000,
        'status' => 'dibayar',
        'tanggal_bayar' => now()->toDateString(),
    ]);

    Livewire::actingAs($this->userFinance)
        ->test(DetailGajiGuru::class, ['guruId' => $this->guru->id])
        ->call('openDetailModal', $salary->id)
        ->set('detailBuktiFoto', $fakeImg)
        ->call('updateSalaryBuktiFoto', $salary->id)
        ->assertHasNoErrors();

    $salary->refresh();
    expect($salary->bukti_bayar)->not->toBeNull();
    Storage::disk('public')->assertExists($salary->bukti_bayar);
});
