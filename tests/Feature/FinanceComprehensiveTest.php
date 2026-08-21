<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\JenisTagihan;
use App\Models\Tagihan;
use App\Models\KategoriPengeluaran;
use App\Models\Pengeluaran;
use App\Models\PemasukanKas;
use App\Models\DanaBos;
use App\Models\GajiGuru;
use App\Models\Peminjaman;
use Livewire\Livewire;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    $this->artisan('db:seed', ['--class' => 'PengaturanSeeder']);
    $this->artisan('db:seed', ['--class' => 'DemoDataSeeder']);
    $this->artisan('db:seed', ['--class' => 'JenisTagihanSeeder']);

    $this->activeTA = TahunAjaran::where('status_aktif', true)->first() ?? TahunAjaran::create([
        'nama' => '2025/2026',
        'semester' => 'Ganjil',
        'status_aktif' => true,
        'tanggal_mulai' => '2025-07-01',
        'tanggal_selesai' => '2025-12-31',
    ]);

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

    $this->kelas = Kelas::first() ?? Kelas::create([
        'nama_kelas' => '7A',
        'tingkat' => 7,
    ]);

    $this->siswa = Siswa::first();
    $this->guru = Guru::first();
});

test('finance user can access all finance pages successfully', function () {
    $this->actingAs($this->userFinance);

    $routes = [
        'finance.dashboard',
        'finance.overview-pembayaran',
        'finance.tagihan',
        'finance.tabungan',
        'finance.input-pembayaran',
        'finance.arus-masuk',
        'finance.arus-kas',
        'finance.arus-kas-masuk',
        'finance.arus-kas-keluar',
        'finance.pengajuan-dana',
        'finance.gaji-guru',
        'finance.peminjaman',
        'finance.dana-bos',
        'finance.laporan.tunggakan',
        'finance.laporan.pemasukan',
        'finance.laporan.pengeluaran',
    ];

    foreach ($routes as $route) {
        $response = $this->get(route($route));
        $response->assertStatus(200);
    }
});

test('it can manage kas masuk yayasan with date filters and bulk delete', function () {
    $this->actingAs($this->userFinance);

    // Create 2 records
    PemasukanKas::create([
        'kategori' => 'Infaq',
        'jumlah' => 150000,
        'tanggal' => date('Y-m-d'),
        'keterangan' => 'Infaq 1',
        'petugas_id' => $this->userFinance->id,
    ]);

    PemasukanKas::create([
        'kategori' => 'Donasi',
        'jumlah' => 350000,
        'tanggal' => date('Y-m-d'),
        'keterangan' => 'Donasi 1',
        'petugas_id' => $this->userFinance->id,
    ]);

    $items = PemasukanKas::all();

    Livewire::test(\App\Livewire\Finance\ArusKasMasuk::class)
        ->call('setPeriode', 'hari_ini')
        ->assertSet('filterPeriode', 'hari_ini')
        ->call('openCreateModal')
        ->assertSet('showCreateModal', true)
        ->set('kategori', 'Sedekah Subuh')
        ->set('jumlah', 500000)
        ->set('tanggal', date('Y-m-d'))
        ->set('keterangan', 'Sedekah Subuh Jamaah')
        ->call('saveIncome')
        ->assertSet('showCreateModal', false)
        ->assertHasNoErrors()
        // Test Select All & Bulk Delete
        ->set('selectAll', true)
        ->call('bulkDelete')
        ->assertHasNoErrors();

    expect(PemasukanKas::count())->toBe(0);
});

test('it can manage kas keluar yayasan with date filters and bulk delete', function () {
    $this->actingAs($this->userFinance);

    $cat = KategoriPengeluaran::first() ?? KategoriPengeluaran::create(['nama' => 'Operasional']);

    Pengeluaran::create([
        'kategori_pengeluaran_id' => $cat->id,
        'jumlah' => 200000,
        'tanggal' => date('Y-m-d'),
        'keterangan' => 'Belanja ATK',
        'petugas_id' => $this->userFinance->id,
    ]);

    Livewire::test(\App\Livewire\Finance\ArusKasKeluar::class)
        ->call('setPeriode', 'minggu_ini')
        ->assertSet('filterPeriode', 'minggu_ini')
        ->call('openCreateModal')
        ->assertSet('showCreateModal', true)
        ->set('kategori_pengeluaran_id', $cat->id)
        ->set('jumlah', 175000)
        ->set('tanggal', date('Y-m-d'))
        ->set('keterangan', 'Beli Kertas')
        ->call('saveExpense')
        ->assertSet('showCreateModal', false)
        ->assertHasNoErrors()
        ->set('selectAll', true)
        ->call('bulkDelete')
        ->assertHasNoErrors();

    expect(Pengeluaran::count())->toBe(0);
});

test('it can manage dana bos with tabs date filters and bulk delete', function () {
    $this->actingAs($this->userFinance);

    Livewire::test(\App\Livewire\Finance\DanaBos::class)
        ->call('selectTab', 'masuk')
        ->assertSet('filterJenis', 'masuk')
        ->call('openCreateModal', 'masuk')
        ->assertSet('showCreateModal', true)
        ->set('kategori', 'Penerimaan BOS Tahap 1')
        ->set('nominal', 20000000)
        ->set('tanggal', date('Y-m-d'))
        ->set('keterangan', 'BOS Reguler')
        ->call('saveTransaction')
        ->assertSet('showCreateModal', false)
        ->assertHasNoErrors()
        ->set('selectAll', true)
        ->call('bulkDelete')
        ->assertHasNoErrors();

    expect(DanaBos::count())->toBe(0);
});

test('it can release per-student invoice with custom fee and perform bulk delete on unpaid invoices', function () {
    $this->actingAs($this->userFinance);

    $jenisSpp = JenisTagihan::first() ?? JenisTagihan::create([
        'nama' => 'SPP Siswa',
        'kategori' => 'rutin',
        'default_nominal' => 350000,
    ]);

    Livewire::test(\App\Livewire\Finance\ManajemenTagihan::class)
        ->call('setPeriode', 'bulan_ini')
        ->assertSet('filterPeriode', 'bulan_ini')
        ->call('openCreateModal')
        ->assertSet('showCreateModal', true)
        ->set('single_siswa_id', $this->siswa->id)
        ->set('jenis_tagihan_id', $jenisSpp->id)
        ->set('bulan', 'Agustus')
        ->set('nominal', 400000) // Custom SPP nominal
        ->set('jatuh_tempo', date('Y-m-d', strtotime('+30 days')))
        ->call('createSingleTagihan')
        ->assertSet('showCreateModal', false)
        ->assertHasNoErrors();

    $this->assertDatabaseHas('tagihan', [
        'siswa_id' => $this->siswa->id,
        'nominal' => 400000,
        'status' => 'belum_bayar',
    ]);

    // Test Open Detail Modal & Month Filter
    Livewire::test(\App\Livewire\Finance\ManajemenTagihan::class)
        ->set('filterBulan', 'Agustus')
        ->assertSet('filterBulan', 'Agustus')
        ->call('openDetail', $this->siswa->id)
        ->assertSet('showDetailModal', true)
        ->assertSet('selectedSiswaId', $this->siswa->id)
        ->call('closeDetailModal')
        ->assertSet('showDetailModal', false);

    // Test Bulk Delete
    Livewire::test(\App\Livewire\Finance\ManajemenTagihan::class)
        ->set('selectAll', true)
        ->call('bulkDelete')
        ->assertHasNoErrors();
});

test('it can process cashier payment with full calculation', function () {
    $this->actingAs($this->userFinance);

    $jenis = JenisTagihan::first();
    $tagihan = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->activeTA->id,
        'bulan' => 'Agustus',
        'nominal' => 300000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => date('Y-m-d'),
    ]);

    Livewire::test(\App\Livewire\Finance\InputPembayaran::class)
        ->call('pilihSiswaAndTagihan', $this->siswa->id, $tagihan->id)
        ->set('nominal_dibayar', 300000)
        ->set('tanggal_bayar', date('Y-m-d'))
        ->call('setMetodeBayar', 'Tunai')
        ->call('savePayment')
        ->assertHasNoErrors();

    $tagihan->refresh();
    expect($tagihan->status)->toBe('lunas');
    expect(floatval($tagihan->total_dibayar))->toBe(300000.0);
});

test('it can manage teacher loan and salary draft generation', function () {
    $this->actingAs($this->userFinance);

    Livewire::test(\App\Livewire\Finance\ManajemenPeminjaman::class)
        ->call('openCreateModal')
        ->assertSet('showCreateModal', true)
        ->set('guru_id', $this->guru->id)
        ->set('nominal', 1200000)
        ->set('tenor_bulan', 6)
        ->set('tanggal_pinjam', date('Y-m-d'))
        ->call('savePeminjaman')
        ->assertSet('showCreateModal', false)
        ->assertHasNoErrors();

    $this->assertDatabaseHas('peminjaman', [
        'guru_id' => $this->guru->id,
        'nominal' => 1200000,
        'cicilan_per_bulan' => 200000,
    ]);

    Livewire::test(\App\Livewire\Finance\ManajemenGajiGuru::class)
        ->call('openGenerateModal')
        ->assertSet('showGenerateModal', true)
        ->set('generateBulan', 'Agustus')
        ->set('generateTahun', 2026)
        ->call('generateDrafts')
        ->assertSet('showGenerateModal', false)
        ->assertHasNoErrors();

    $gaji = GajiGuru::first();
    expect($gaji)->not->toBeNull();
    expect(floatval($gaji->potongan_peminjaman))->toBe(200000.0);

    Livewire::test(\App\Livewire\Finance\ManajemenGajiGuru::class)
        ->call('paySalary', $gaji->id);

    $gaji->refresh();
    expect($gaji->status)->toBe('dibayar');
});

test('it renders receipt interactive web preview and pdf download', function () {
    $this->actingAs($this->userFinance);

    $jenisSPP = JenisTagihan::where('nama', 'SPP')->first();
    $tagihan = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenisSPP->id,
        'tahun_ajaran_id' => $this->activeTA->id,
        'bulan' => 'September',
        'nominal' => 350000,
        'status' => 'lunas',
        'total_dibayar' => 350000,
        'jatuh_tempo' => date('Y-m-d'),
    ]);

    $pembayaran = \App\Models\Pembayaran::create([
        'tagihan_id' => $tagihan->id,
        'no_resi' => 'REC-TEST-001',
        'nominal_dibayar' => 350000,
        'tanggal_bayar' => date('Y-m-d'),
        'metode_bayar' => 'tunai',
        'petugas_id' => $this->userFinance->id,
    ]);

    // 1. Default route renders interactive web preview (HTTP 200)
    $response = $this->get(route('finance.pembayaran.resi', $pembayaran->id));
    $response->assertStatus(200);
    $response->assertSee('Pratinjau Kuitansi Pembayaran');
    $response->assertSee('REC-TEST-001');
    $response->assertSee('Cetak Kuitansi');

    // 2. Download query streams download file
    $responseDownload = $this->get(route('finance.pembayaran.resi', ['id' => $pembayaran->id, 'download' => '1']));
    $responseDownload->assertStatus(200);
    $responseDownload->assertHeader('content-type', 'application/pdf');

    // 3. Format=pdf streams inline PDF
    $responsePdf = $this->get(route('finance.pembayaran.resi', ['id' => $pembayaran->id, 'format' => 'pdf']));
    $responsePdf->assertStatus(200);
    $responsePdf->assertHeader('content-type', 'application/pdf');
});

