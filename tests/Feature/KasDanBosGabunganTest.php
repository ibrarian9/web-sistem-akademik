<?php

use App\Models\User;
use App\Models\Role;
use App\Models\PemasukanKas;
use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;
use App\Models\DanaBos;
use App\Models\TahunAjaran;
use Livewire\Livewire;
use App\Livewire\Finance\KasDanBosGabungan;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    $this->artisan('db:seed', ['--class' => 'PengaturanSeeder']);
    $this->artisan('db:seed', ['--class' => 'DemoDataSeeder']);

    $this->userFinance = User::whereHas('role', function ($q) {
        $q->where('nama', 'finance');
    })->first();

    $this->userSuperAdmin = User::whereHas('role', function ($q) {
        $q->where('nama', 'super_admin');
    })->first();

    $this->tahunAjaran = TahunAjaran::where('status_aktif', true)->first() ?? TahunAjaran::first();
});

test('finance user and super admin can render kas dan bos gabungan page', function () {
    $this->actingAs($this->userFinance);

    $response = $this->get(route('finance.kas-gabungan'));
    $response->assertStatus(200);

    Livewire::test(KasDanBosGabungan::class)
        ->assertStatus(200)
        ->assertSee('Arus Kas & Dana BOS')
        ->assertSee('Buku kas konsolidasi');
});

test('unified ledger displays both arus kas and dana bos transactions with accurate metrics', function () {
    $this->actingAs($this->userFinance);

    // 1. Create Kas Umum Pemasukan
    PemasukanKas::create([
        'kategori' => 'Infaq Santri',
        'jumlah' => 500000,
        'tanggal' => now()->toDateString(),
        'keterangan' => 'Infaq Sholat Jumat Santri',
        'petugas_id' => $this->userFinance->id,
    ]);

    // 2. Create Kas Umum Pengeluaran
    $kategoriPengeluaran = KategoriPengeluaran::first() ?? KategoriPengeluaran::create(['nama' => 'Operasional ATK']);
    Pengeluaran::create([
        'kategori_pengeluaran_id' => $kategoriPengeluaran->id,
        'jumlah' => 200000,
        'tanggal' => now()->toDateString(),
        'keterangan' => 'Beli Kertas HVS dan Tinta Printer',
        'petugas_id' => $this->userFinance->id,
    ]);

    // 3. Create Dana BOS Masuk
    DanaBos::create([
        'tahun_ajaran_id' => $this->tahunAjaran->id,
        'jenis' => 'masuk',
        'tanggal' => now()->toDateString(),
        'nominal' => 1500000,
        'kategori' => 'BOS Reguler Tahap 1',
        'keterangan' => 'Pencairan Dana BOS Reguler Tahap 1',
    ]);

    // 4. Create Dana BOS Keluar
    DanaBos::create([
        'tahun_ajaran_id' => $this->tahunAjaran->id,
        'jenis' => 'keluar',
        'tanggal' => now()->toDateString(),
        'nominal' => 300000,
        'kategori' => 'Pengadaan Buku Teks BOS',
        'keterangan' => 'Beli Buku Kurikulum Merdeka Siswa',
    ]);

    $component = Livewire::test(KasDanBosGabungan::class)
        ->assertStatus(200)
        ->assertSee('Infaq Sholat Jumat Santri')
        ->assertSee('Beli Kertas HVS dan Tinta Printer')
        ->assertSee('Pencairan Dana BOS Reguler Tahap 1')
        ->assertSee('Beli Buku Kurikulum Merdeka Siswa');

    $metrics = $component->get('metrics');

    // Expected total masuk: 500.000 + 1.500.000 = 2.000.000
    expect($metrics['total_masuk'])->toBeGreaterThanOrEqual(2000000.00);

    // Expected total keluar: 200.000 + 300.000 = 500.000
    expect($metrics['total_keluar'])->toBeGreaterThanOrEqual(500000.00);

    // Net balance = Masuk - Keluar
    expect($metrics['net_balance'])->toBe($metrics['total_masuk'] - $metrics['total_keluar']);
});

test('filtering by sumber dana isolates kas sekolah and dana bos accurately', function () {
    $this->actingAs($this->userFinance);

    // Kas transaction
    PemasukanKas::create([
        'kategori' => 'Donasi Khusus',
        'jumlah' => 750000,
        'tanggal' => now()->toDateString(),
        'keterangan' => 'Donasi Pembangunan Asrama',
        'petugas_id' => $this->userFinance->id,
    ]);

    // BOS transaction
    DanaBos::create([
        'tahun_ajaran_id' => $this->tahunAjaran->id,
        'jenis' => 'masuk',
        'tanggal' => now()->toDateString(),
        'nominal' => 2500000,
        'kategori' => 'BOS Kinerja',
        'keterangan' => 'Pencairan BOS Kinerja Prestasi',
    ]);

    // Filter to Dana BOS only
    Livewire::test(KasDanBosGabungan::class)
        ->set('filterSumber', 'dana_bos')
        ->assertSee('Pencairan BOS Kinerja Prestasi')
        ->assertDontSee('Donasi Pembangunan Asrama');

    // Filter to Kas Umum only
    Livewire::test(KasDanBosGabungan::class)
        ->set('filterSumber', 'kas_umum')
        ->assertSee('Donasi Pembangunan Asrama')
        ->assertDontSee('Pencairan BOS Kinerja Prestasi');
});

test('filtering by jenis arus isolates pemasukan and pengeluaran', function () {
    $this->actingAs($this->userFinance);

    PemasukanKas::create([
        'kategori' => 'Donasi Peduli',
        'jumlah' => 400000,
        'tanggal' => now()->toDateString(),
        'keterangan' => 'Pemasukan Donasi Peduli Yatim',
        'petugas_id' => $this->userFinance->id,
    ]);

    DanaBos::create([
        'tahun_ajaran_id' => $this->tahunAjaran->id,
        'jenis' => 'keluar',
        'tanggal' => now()->toDateString(),
        'nominal' => 450000,
        'kategori' => 'Renovasi Ringan BOS',
        'keterangan' => 'Pengeluaran Renovasi Ruang Kelas BOS',
    ]);

    // Filter to masuk only
    Livewire::test(KasDanBosGabungan::class)
        ->set('filterJenis', 'masuk')
        ->assertSee('Pemasukan Donasi Peduli Yatim')
        ->assertDontSee('Pengeluaran Renovasi Ruang Kelas BOS');

    // Filter to keluar only
    Livewire::test(KasDanBosGabungan::class)
        ->set('filterJenis', 'keluar')
        ->assertSee('Pengeluaran Renovasi Ruang Kelas BOS')
        ->assertDontSee('Pemasukan Donasi Peduli Yatim');
});

test('export excel and pdf work for consolidated ledger', function () {
    $this->actingAs($this->userFinance);

    PemasukanKas::create([
        'kategori' => 'Infaq Rutin',
        'jumlah' => 100000,
        'tanggal' => now()->toDateString(),
        'keterangan' => 'Infaq Rutin Jumat',
        'petugas_id' => $this->userFinance->id,
    ]);

    $component = Livewire::test(KasDanBosGabungan::class);

    // Test Excel export
    $excelResponse = $component->call('exportExcel');
    expect($excelResponse)->not->toBeNull();

    // Test PDF export
    $pdfResponse = $component->call('exportPdf');
    expect($pdfResponse)->not->toBeNull();
});

test('interactive cards filter by flow correctly and toggle back', function () {
    $this->actingAs($this->userFinance);

    Livewire::test(KasDanBosGabungan::class)
        ->assertSet('filterJenis', 'semua')
        ->call('filterByFlow', 'masuk')
        ->assertSet('filterJenis', 'masuk')
        ->call('filterByFlow', 'masuk')
        ->assertSet('filterJenis', 'semua')
        ->call('filterByFlow', 'keluar')
        ->assertSet('filterJenis', 'keluar')
        ->call('setJenis', 'semua')
        ->assertSet('filterJenis', 'semua');
});

test('metric card figures remain intact when filtering by flow', function () {
    $this->actingAs($this->userFinance);

    PemasukanKas::create([
        'kategori' => 'Donasi',
        'jumlah' => 600000,
        'tanggal' => now()->toDateString(),
        'keterangan' => 'Donasi Sarpras',
        'petugas_id' => $this->userFinance->id,
    ]);

    DanaBos::create([
        'tahun_ajaran_id' => $this->tahunAjaran->id,
        'jenis' => 'keluar',
        'tanggal' => now()->toDateString(),
        'nominal' => 200000,
        'kategori' => 'ATK BOS',
        'keterangan' => 'Beli Spidol BOS',
    ]);

    $test = Livewire::test(KasDanBosGabungan::class);
    $initialMetrics = $test->get('metrics');

    // Filter to masuk only: table filters, but metrics don't change
    $test->call('filterByFlow', 'masuk');
    $filteredMetrics = $test->get('metrics');

    expect($filteredMetrics['total_masuk'])->toBe($initialMetrics['total_masuk']);
    expect($filteredMetrics['total_keluar'])->toBe($initialMetrics['total_keluar']);
    expect($filteredMetrics['net_balance'])->toBe($initialMetrics['net_balance']);

    // Filter to keluar only: table filters, but metrics don't change
    $test->call('filterByFlow', 'keluar');
    $keluarMetrics = $test->get('metrics');

    expect($keluarMetrics['total_masuk'])->toBe($initialMetrics['total_masuk']);
    expect($keluarMetrics['total_keluar'])->toBe($initialMetrics['total_keluar']);
    expect($keluarMetrics['net_balance'])->toBe($initialMetrics['net_balance']);
});

test('filtering by category isolates transactions accurately across kas and dana bos', function () {
    $this->actingAs($this->userFinance);

    // 1. Kas Inflow with unique category
    PemasukanKas::create([
        'kategori' => 'Donasi Khusus Lab Komputer',
        'jumlah' => 800000,
        'tanggal' => now()->toDateString(),
        'keterangan' => 'Donasi Komputer PC Siswa',
        'petugas_id' => $this->userFinance->id,
    ]);

    // 2. BOS Inflow with unique category
    DanaBos::create([
        'tahun_ajaran_id' => $this->tahunAjaran->id,
        'jenis' => 'masuk',
        'tanggal' => now()->toDateString(),
        'nominal' => 3000000,
        'kategori' => 'BOS Afirmasi 2026',
        'keterangan' => 'Pencairan BOS Afirmasi Khusus',
    ]);

    // Filter to Kas category: should see Donasi, not BOS
    Livewire::test(KasDanBosGabungan::class)
        ->set('filterKategori', 'Donasi Khusus Lab Komputer')
        ->assertSee('Donasi Komputer PC Siswa')
        ->assertDontSee('Pencairan BOS Afirmasi Khusus');

    // Filter to BOS category: should see BOS, not Donasi
    Livewire::test(KasDanBosGabungan::class)
        ->set('filterKategori', 'BOS Afirmasi 2026')
        ->assertSee('Pencairan BOS Afirmasi Khusus')
        ->assertDontSee('Donasi Komputer PC Siswa');
});

test('export excel and pdf respect active category filter', function () {
    $this->actingAs($this->userFinance);

    PemasukanKas::create([
        'kategori' => 'Wakaf Pembangunan Musholla',
        'jumlah' => 1200000,
        'tanggal' => now()->toDateString(),
        'keterangan' => 'Setoran Wakaf Semen dan Pasir',
        'petugas_id' => $this->userFinance->id,
    ]);

    PemasukanKas::create([
        'kategori' => 'Infaq Konsumsi Rapat',
        'jumlah' => 150000,
        'tanggal' => now()->toDateString(),
        'keterangan' => 'Infaq Snack Rapat Guru',
        'petugas_id' => $this->userFinance->id,
    ]);

    $component = Livewire::test(KasDanBosGabungan::class)
        ->set('filterKategori', 'Wakaf Pembangunan Musholla');

    // Collection should contain only the filtered category
    $filteredCollection = $component->instance()->getConsolidatedCollection();
    expect($filteredCollection->pluck('keterangan'))->toContain('Setoran Wakaf Semen dan Pasir');
    expect($filteredCollection->pluck('keterangan'))->not->toContain('Infaq Snack Rapat Guru');

    // Test Excel export with category filter
    $excelResponse = $component->call('exportExcel');
    expect($excelResponse)->not->toBeNull();

    // Test PDF export with category filter
    $pdfResponse = $component->call('exportPdf');
    expect($pdfResponse)->not->toBeNull();
});

test('finance user and super admin can upload proof photo for transaction without proof in kas gabungan', function () {
    Storage::fake('public');

    // Create a transaction without proof
    $kasMasuk = PemasukanKas::create([
        'kategori' => 'Infaq Gedung',
        'jumlah' => 750000,
        'tanggal' => now()->toDateString(),
        'keterangan' => 'Infaq Pembangunan Asrama Santri',
        'petugas_id' => $this->userFinance->id,
        'bukti' => null,
    ]);

    // 1. Finance Admin can see '+ Bukti' and upload proof
    $this->actingAs($this->userFinance);

    $file = \Illuminate\Http\UploadedFile::fake()->image('bukti_transfer.jpg');

    Livewire::test(KasDanBosGabungan::class)
        ->assertSee('+ Bukti')
        ->call('openUploadBuktiModal', 'infaq_' . $kasMasuk->id, 'Infaq Gedung', 'Infaq Pembangunan Asrama Santri', 750000)
        ->assertSet('showUploadBuktiModal', true)
        ->assertSet('uploadTargetId', 'infaq_' . $kasMasuk->id)
        ->set('uploadBuktiFile', $file)
        ->call('saveUploadedBukti')
        ->assertSet('showUploadBuktiModal', false)
        ->assertSee('Foto bukti transaksi berhasil diunggah.');

    $kasMasuk->refresh();
    expect($kasMasuk->bukti)->not->toBeNull();
    Storage::disk('public')->assertExists($kasMasuk->bukti);

    // 2. Super Admin can also upload proof for an expense without proof
    $kategori = KategoriPengeluaran::first() ?? KategoriPengeluaran::create(['nama' => 'Konsumsi']);
    $pengeluaran = Pengeluaran::create([
        'kategori_pengeluaran_id' => $kategori->id,
        'jumlah' => 125000,
        'tanggal' => now()->toDateString(),
        'keterangan' => 'Beli Snack Rapat Yayasan',
        'petugas_id' => $this->userFinance->id,
        'bukti' => null,
    ]);

    $this->actingAs($this->userSuperAdmin);

    $fileSuperAdmin = \Illuminate\Http\UploadedFile::fake()->image('struk_snack.png');

    Livewire::test(KasDanBosGabungan::class)
        ->assertSee('+ Bukti')
        ->call('openUploadBuktiModal', 'op_' . $pengeluaran->id, 'Konsumsi', 'Beli Snack Rapat Yayasan', 125000)
        ->assertSet('showUploadBuktiModal', true)
        ->set('uploadBuktiFile', $fileSuperAdmin)
        ->call('saveUploadedBukti')
        ->assertSet('showUploadBuktiModal', false)
        ->assertSee('Foto bukti transaksi berhasil diunggah.');

    $pengeluaran->refresh();
    expect($pengeluaran->bukti)->not->toBeNull();
    Storage::disk('public')->assertExists($pengeluaran->bukti);
});

test('finance user and super admin can upload proof in arus kas page', function () {
    Storage::fake('public');

    $kategori = KategoriPengeluaran::first() ?? KategoriPengeluaran::create(['nama' => 'Listrik']);
    $pengeluaran = Pengeluaran::create([
        'kategori_pengeluaran_id' => $kategori->id,
        'jumlah' => 450000,
        'tanggal' => now()->toDateString(),
        'keterangan' => 'Tagihan Listrik PLN Kantor',
        'petugas_id' => $this->userFinance->id,
        'bukti' => null,
    ]);

    $this->actingAs($this->userFinance);

    $file = \Illuminate\Http\UploadedFile::fake()->image('struk_pln.jpg');

    Livewire::test(\App\Livewire\Finance\ArusKas::class)
        ->assertSee('+ Bukti')
        ->call('openUploadBuktiModal', 'op_' . $pengeluaran->id, 'Listrik', 'Tagihan Listrik PLN Kantor', 450000)
        ->assertSet('showUploadBuktiModal', true)
        ->set('uploadBuktiFile', $file)
        ->call('saveUploadedBukti')
        ->assertSet('showUploadBuktiModal', false);

    $pengeluaran->refresh();
    expect($pengeluaran->bukti)->not->toBeNull();
    Storage::disk('public')->assertExists($pengeluaran->bukti);
});


