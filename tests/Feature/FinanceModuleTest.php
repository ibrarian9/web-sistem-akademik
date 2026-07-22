<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\JenisTagihan;
use App\Models\PemasukanKas;
use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;
use App\Models\PengajuanDana;
use App\Models\TahunAjaran;
use Livewire\Livewire;
use App\Livewire\Finance\Dashboard;
use App\Livewire\Finance\OverviewPembayaran;
use App\Livewire\Finance\ArusKasMasuk;
use App\Livewire\Finance\ArusKasKeluar;
use App\Livewire\Finance\InputPembayaran;
use App\Livewire\Finance\ManajemenTagihan;
use App\Livewire\Finance\PengajuanDanaIndex;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    $this->artisan('db:seed', ['--class' => 'PengaturanSeeder']);
    $this->artisan('db:seed', ['--class' => 'DemoDataSeeder']);
    $this->artisan('db:seed', ['--class' => 'JenisTagihanSeeder']);

    $this->userFinance = User::whereHas('role', function ($q) {
        $q->where('nama', 'finance');
    })->first();

    if (!$this->userFinance) {
        $roleFinance = Role::where('nama', 'finance')->first();
        $this->userFinance = User::create([
            'nama' => 'Staff Keuangan',
            'username' => 'finance_test',
            'email' => 'finance@test.com',
            'password' => bcrypt('password'),
            'role_id' => $roleFinance->id,
        ]);
    }

    $this->userKoordinator = User::whereHas('role', function ($q) {
        $q->where('nama', 'koordinator');
    })->first();

    if (!$this->userKoordinator) {
        $roleKoor = Role::where('nama', 'koordinator')->first();
        $this->userKoordinator = User::create([
            'nama' => 'Koordinator Test',
            'username' => 'koordinator_test',
            'email' => 'koordinator@test.com',
            'password' => bcrypt('password'),
            'role_id' => $roleKoor->id,
        ]);
    }

    $this->userSuperAdmin = User::whereHas('role', function ($q) {
        $q->where('nama', 'super_admin');
    })->first();

    if (!$this->userSuperAdmin) {
        $roleSA = Role::where('nama', 'super_admin')->first();
        $this->userSuperAdmin = User::create([
            'nama' => 'Kepala Yayasan Test',
            'username' => 'superadmin_test',
            'email' => 'superadmin@test.com',
            'password' => bcrypt('password'),
            'role_id' => $roleSA->id,
        ]);
    }
});

test('finance user can render finance dashboard', function () {
    $this->actingAs($this->userFinance);

    Livewire::test(Dashboard::class)
        ->assertStatus(200)
        ->assertSee('Dashboard Keuangan');
});

test('finance user can render overview pembayaran', function () {
    $this->actingAs($this->userFinance);

    Livewire::test(OverviewPembayaran::class)
        ->assertStatus(200)
        ->assertSee('Overview');
});

test('finance user can record cash inflow in ArusKasMasuk', function () {
    $this->actingAs($this->userFinance);

    Livewire::test(ArusKasMasuk::class)
        ->set('kategori', 'Infaq')
        ->set('jumlah', 150000)
        ->set('tanggal', date('Y-m-d'))
        ->set('keterangan', 'Infaq Subuh Jamaah')
        ->call('saveIncome')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('pemasukan_kas', [
        'kategori' => 'Infaq',
        'jumlah' => 150000,
        'keterangan' => 'Infaq Subuh Jamaah',
    ]);
});

test('finance user can record operational expense in ArusKasKeluar', function () {
    $this->actingAs($this->userFinance);

    $kategori = KategoriPengeluaran::first();
    if (!$kategori) {
        $kategori = KategoriPengeluaran::create(['nama' => 'Operasional Sekolah', 'deskripsi' => 'Pengeluaran harian']);
    }

    Livewire::test(ArusKasKeluar::class)
        ->set('kategori_pengeluaran_id', $kategori->id)
        ->set('jumlah', 75000)
        ->set('tanggal', date('Y-m-d'))
        ->set('keterangan', 'Beli ATK dan Spidol')
        ->call('saveExpense')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('pengeluaran', [
        'kategori_pengeluaran_id' => $kategori->id,
        'jumlah' => 75000,
        'keterangan' => 'Beli ATK dan Spidol',
    ]);
});

test('manajemen tagihan excludes voluntary donations from options', function () {
    $this->actingAs($this->userFinance);

    $component = Livewire::test(ManajemenTagihan::class);
    $jenisTagihans = $component->get('jenisTagihans');

    foreach ($jenisTagihans as $jt) {
        expect($jt['nama'])->not->toContain('Infaq')
            ->and($jt['nama'])->not->toContain('Sedekah');
    }
});

test('finance user can process payment in InputPembayaran', function () {
    $this->actingAs($this->userFinance);

    $siswa = Siswa::first();
    $activeTA = TahunAjaran::where('status_aktif', true)->first() ?? TahunAjaran::first();
    $jt = JenisTagihan::where('nama', 'SPP')->first() ?? JenisTagihan::first();

    $tagihan = Tagihan::create([
        'siswa_id' => $siswa->id,
        'jenis_tagihan_id' => $jt->id,
        'tahun_ajaran_id' => $activeTA->id,
        'bulan' => 'Juli',
        'nominal' => 300000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    Livewire::test(InputPembayaran::class)
        ->call('pilihSiswaAndTagihan', $siswa->id, $tagihan->id)
        ->set('nominal_dibayar', 300000)
        ->set('metode_bayar', 'Tunai')
        ->set('tanggal_bayar', date('Y-m-d'))
        ->call('savePayment')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('tagihan', [
        'id' => $tagihan->id,
        'status' => 'lunas',
        'total_dibayar' => 300000,
    ]);
});

test('pengajuan dana tier approval workflow works', function () {
    $this->actingAs($this->userFinance);

    // 1. Submit proposal <= 1 Juta (Needs only Koordinator approval)
    Livewire::test(PengajuanDanaIndex::class)
        ->set('judul', 'Beli Buku Modul')
        ->set('kategori', 'Pembelian Buku / Literasi')
        ->set('jumlah', 500000)
        ->set('keterangan', 'Beli buku 10 eksemplar')
        ->call('createPengajuan')
        ->assertHasNoErrors();

    $proposal1 = PengajuanDana::where('judul', 'Beli Buku Modul')->first();
    expect($proposal1)->not->toBeNull();
    expect($proposal1->status)->toBe('menunggu_koordinator');

    // 2. Koordinator approves proposal <= 1 Juta -> status becomes disetujui directly
    $this->actingAs($this->userKoordinator);
    Livewire::test(PengajuanDanaIndex::class)
        ->call('approveByKoordinator', $proposal1->id);

    $proposal1->refresh();
    expect($proposal1->status)->toBe('disetujui');

    // 3. Realisasi / Cairkan Dana by Finance
    $this->actingAs($this->userFinance);
    Livewire::test(PengajuanDanaIndex::class)
        ->call('realisasikanDana', $proposal1->id);

    $proposal1->refresh();
    expect($proposal1->status)->toBe('direalisasi');

    // 4. Submit proposal > 1 Juta (Needs Koordinator then Kepala Yayasan)
    Livewire::test(PengajuanDanaIndex::class)
        ->set('judul', 'Beli Laptop Operasional')
        ->set('kategori', 'Renovasi & Pemeliharaan Fasilitas')
        ->set('jumlah', 2500000)
        ->set('keterangan', 'Pembelian unit laptop kantor')
        ->call('createPengajuan')
        ->assertHasNoErrors();

    $proposal2 = PengajuanDana::where('judul', 'Beli Laptop Operasional')->first();
    
    // Stage 1 approval by Koordinator -> status becomes menunggu_kepala_yayasan
    $this->actingAs($this->userKoordinator);
    Livewire::test(PengajuanDanaIndex::class)
        ->call('approveByKoordinator', $proposal2->id);

    $proposal2->refresh();
    expect($proposal2->status)->toBe('menunggu_kepala_yayasan');

    // Stage 2 approval by Super Admin (Kepala Yayasan) -> status becomes disetujui
    $this->actingAs($this->userSuperAdmin);
    Livewire::test(PengajuanDanaIndex::class)
        ->call('approveByKepalaYayasan', $proposal2->id);

    $proposal2->refresh();
    expect($proposal2->status)->toBe('disetujui');
});

test('finance user can delete single unpaid tagihan', function () {
    $this->actingAs($this->userFinance);

    $siswa = Siswa::first();
    $activeTA = TahunAjaran::where('status_aktif', true)->first() ?? TahunAjaran::first();
    $jt = JenisTagihan::first();

    $tagihan = Tagihan::create([
        'siswa_id' => $siswa->id,
        'jenis_tagihan_id' => $jt->id,
        'tahun_ajaran_id' => $activeTA->id,
        'bulan' => 'Agustus',
        'nominal' => 250000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    Livewire::test(ManajemenTagihan::class)
        ->call('deleteTagihan', $tagihan->id)
        ->assertHasNoErrors();

    $this->assertSoftDeleted('tagihan', [
        'id' => $tagihan->id,
    ]);
});
