<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\JenisTagihan;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\KategoriPengeluaran;
use App\Models\Pengeluaran;
use App\Livewire\Finance\InputPembayaran;
use App\Livewire\Finance\DetailTagihanSiswa;
use App\Livewire\Finance\ArusKasKeluar;
use App\Livewire\Finance\ArusKas;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    $this->artisan('db:seed', ['--class' => 'PengaturanSeeder']);
    $this->artisan('db:seed', ['--class' => 'DemoDataSeeder']);
    $this->artisan('db:seed', ['--class' => 'JenisTagihanSeeder']);

    Storage::fake('public');

    $this->activeTA = TahunAjaran::where('status_aktif', true)->first() ?? TahunAjaran::create([
        'nama' => '2025/2026',
        'semester' => 'Ganjil',
        'status_aktif' => true,
        'tanggal_mulai' => '2025-07-01',
        'tanggal_selesai' => '2025-12-31',
    ]);

    $roleFinance = Role::where('nama', 'finance')->first();
    $this->userFinance = User::whereHas('role', fn($q) => $q->where('nama', 'finance'))->first() ?? User::create([
        'nama' => 'Staff Keuangan Upload',
        'username' => 'finance_test_upload',
        'email' => 'finance_upload@test.com',
        'password' => bcrypt('password'),
        'role_id' => $roleFinance->id,
    ]);

    $this->siswa = Siswa::first();
    if (!$this->siswa) {
        $roleMurid = Role::where('nama', 'murid')->first();
        $userSiswa = User::create([
            'nama' => 'Siswa Test Upload',
            'username' => 'siswa_test_upload',
            'email' => 'siswa_upload@test.com',
            'password' => bcrypt('password'),
            'role_id' => $roleMurid->id,
        ]);

        $this->kelas = Kelas::first() ?? Kelas::create([
            'nama_kelas' => '7A',
            'tingkat' => 7,
        ]);

        $this->siswa = Siswa::create([
            'user_id' => $userSiswa->id,
            'nis' => '999888',
            'nisn' => '9998887776',
            'kelas_id' => $this->kelas->id,
            'jenis_kelamin' => 'L',
            'status' => 'aktif',
        ]);
    }

    $this->jenisTagihan = JenisTagihan::where('nama', 'SPP')->first();

    $this->tagihan = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $this->jenisTagihan->id,
        'tahun_ajaran_id' => $this->activeTA->id,
        'bulan' => 'Juli',
        'nominal' => 250000,
        'sisa_tagihan' => 250000,
        'status' => 'belum_bayar',
        'jatuh_tempo' => now()->addMonth()->toDateString(),
    ]);

    $this->kategori = KategoriPengeluaran::firstOrCreate([
        'nama' => 'Operasional Kantor',
    ], [
        'jenis' => 'operasional',
    ]);
});

test('pembayaran: upload image under 2MB succeeds on InputPembayaran', function () {
    $this->actingAs($this->userFinance);

    $file = UploadedFile::fake()->image('bukti_transfer.png', 600, 400)->size(1024); // 1MB

    Livewire::test(InputPembayaran::class)
        ->set('siswa_id', $this->siswa->id)
        ->set('tagihan_id', $this->tagihan->id)
        ->set('nominal_dibayar', 250000)
        ->set('metode_bayar', 'Transfer Bank')
        ->set('bukti_foto', $file)
        ->call('savePayment')
        ->assertHasNoErrors();

    $pembayaran = Pembayaran::where('tagihan_id', $this->tagihan->id)->first();
    expect($pembayaran)->not->toBeNull();
    expect($pembayaran->bukti_bayar)->not->toBeNull();
    Storage::disk('public')->assertExists($pembayaran->bukti_bayar);
});

test('pembayaran: rejects file larger than 2MB or non-image on InputPembayaran', function () {
    $this->actingAs($this->userFinance);

    // Test > 2MB
    $oversized = UploadedFile::fake()->image('struk_besar.jpg')->size(2500); // 2.5MB
    Livewire::test(InputPembayaran::class)
        ->set('siswa_id', $this->siswa->id)
        ->set('tagihan_id', $this->tagihan->id)
        ->set('nominal_dibayar', 250000)
        ->set('bukti_foto', $oversized)
        ->call('savePayment')
        ->assertHasErrors(['bukti_foto']);

    // Test non-image PDF
    $pdf = UploadedFile::fake()->create('dokumen.pdf', 500, 'application/pdf');
    Livewire::test(InputPembayaran::class)
        ->set('siswa_id', $this->siswa->id)
        ->set('tagihan_id', $this->tagihan->id)
        ->set('nominal_dibayar', 250000)
        ->set('bukti_foto', $pdf)
        ->call('savePayment')
        ->assertHasErrors(['bukti_foto']);
});

test('pembayaran: can edit and attach or delete photo proof in DetailTagihanSiswa', function () {
    $this->actingAs($this->userFinance);

    $pembayaran = Pembayaran::create([
        'tagihan_id' => $this->tagihan->id,
        'nominal_dibayar' => 250000,
        'tanggal_bayar' => now(),
        'metode_bayar' => 'Transfer Bank',
        'no_resi' => 'INV-TEST-001',
        'petugas_id' => $this->userFinance->id,
        'bukti_bayar' => null,
    ]);

    // Attach photo via modal
    $file = UploadedFile::fake()->image('bukti_tf_baru.jpg')->size(800);
    Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
        ->call('openBuktiModal', $pembayaran->id)
        ->assertSet('showBuktiModal', true)
        ->set('edit_bukti_foto', $file)
        ->call('saveBuktiFoto')
        ->assertHasNoErrors()
        ->call('closeBuktiModal')
        ->assertSet('showBuktiModal', false);

    $pembayaran->refresh();
    expect($pembayaran->bukti_bayar)->not->toBeNull();
    Storage::disk('public')->assertExists($pembayaran->bukti_bayar);

    $uploadedPath = $pembayaran->bukti_bayar;

    // Delete photo via modal
    Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
        ->call('openBuktiModal', $pembayaran->id)
        ->call('deleteBuktiFoto')
        ->assertHasNoErrors();

    $pembayaran->refresh();
    expect($pembayaran->bukti_bayar)->toBeNull();
    Storage::disk('public')->assertMissing($uploadedPath);
});

test('pembayaran: can upload same transfer proof simultaneously for multiple transactions via openBulkBuktiModal', function () {
    $this->actingAs($this->userFinance);

    $p1 = Pembayaran::create([
        'tagihan_id' => $this->tagihan->id,
        'nominal_dibayar' => 100000,
        'tanggal_bayar' => now(),
        'metode_bayar' => 'Transfer Bank',
        'no_resi' => 'INV-MULTI-001',
        'petugas_id' => $this->userFinance->id,
        'bukti_bayar' => null,
    ]);

    $p2 = Pembayaran::create([
        'tagihan_id' => $this->tagihan->id,
        'nominal_dibayar' => 100000,
        'tanggal_bayar' => now(),
        'metode_bayar' => 'Transfer Bank',
        'no_resi' => 'INV-MULTI-002',
        'petugas_id' => $this->userFinance->id,
        'bukti_bayar' => null,
    ]);

    $p3 = Pembayaran::create([
        'tagihan_id' => $this->tagihan->id,
        'nominal_dibayar' => 50000,
        'tanggal_bayar' => now(),
        'metode_bayar' => 'Transfer Bank',
        'no_resi' => 'INV-MULTI-003',
        'petugas_id' => $this->userFinance->id,
        'bukti_bayar' => null,
    ]);

    $file = UploadedFile::fake()->image('bukti_tf_kombinasi.jpg')->size(950);

    Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
        ->set('selectedPembayaranIds', [(string)$p1->id, (string)$p2->id, (string)$p3->id])
        ->call('openBulkBuktiModal')
        ->assertSet('showBuktiModal', true)
        ->assertCount('targetPembayaranIds', 3)
        ->set('edit_bukti_foto', $file)
        ->call('saveBuktiFoto')
        ->assertHasNoErrors()
        ->call('closeBuktiModal')
        ->assertSet('showBuktiModal', false);

    $p1->refresh();
    $p2->refresh();
    $p3->refresh();

    expect($p1->bukti_bayar)->not->toBeNull();
    expect($p2->bukti_bayar)->not->toBeNull();
    expect($p3->bukti_bayar)->not->toBeNull();

    // Pastikan bukti tf sama persis untuk ketiga transaksi
    expect($p1->bukti_bayar)->toBe($p2->bukti_bayar);
    expect($p2->bukti_bayar)->toBe($p3->bukti_bayar);
    Storage::disk('public')->assertExists($p1->bukti_bayar);
});

test('pembayaran: can select multiple transactions inside modal when opened from single row', function () {
    $this->actingAs($this->userFinance);

    $p1 = Pembayaran::create([
        'tagihan_id' => $this->tagihan->id,
        'nominal_dibayar' => 150000,
        'tanggal_bayar' => now(),
        'metode_bayar' => 'Transfer Bank',
        'no_resi' => 'INV-ROW-001',
        'petugas_id' => $this->userFinance->id,
        'bukti_bayar' => null,
    ]);

    $p2 = Pembayaran::create([
        'tagihan_id' => $this->tagihan->id,
        'nominal_dibayar' => 100000,
        'tanggal_bayar' => now(),
        'metode_bayar' => 'Transfer Bank',
        'no_resi' => 'INV-ROW-002',
        'petugas_id' => $this->userFinance->id,
        'bukti_bayar' => null,
    ]);

    $file = UploadedFile::fake()->image('bukti_tf_tunggal_ke_banyak.png')->size(600);

    Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
        ->call('openBuktiModal', $p1->id)
        ->assertSet('showBuktiModal', true)
        ->set('targetPembayaranIds', [$p1->id, $p2->id])
        ->set('edit_bukti_foto', $file)
        ->call('saveBuktiFoto')
        ->assertHasNoErrors();

    $p1->refresh();
    $p2->refresh();

    expect($p1->bukti_bayar)->not->toBeNull();
    expect($p2->bukti_bayar)->not->toBeNull();
    expect($p1->bukti_bayar)->toBe($p2->bukti_bayar);
    Storage::disk('public')->assertExists($p1->bukti_bayar);
});

test('pengeluaran: upload image under 2MB succeeds on ArusKasKeluar and can be edited', function () {
    $this->actingAs($this->userFinance);

    $file = UploadedFile::fake()->image('nota_atk.jpg')->size(1200);

    // 1. Create expense with proof
    Livewire::test(ArusKasKeluar::class)
        ->set('kategori_pengeluaran_id', $this->kategori->id)
        ->set('jumlah', 150000)
        ->set('tanggal', now()->toDateString())
        ->set('keterangan', 'Beli Kertas HVS & Tinta')
        ->set('bukti_foto', $file)
        ->call('saveExpense')
        ->assertHasNoErrors();

    $pengeluaran = Pengeluaran::where('keterangan', 'Beli Kertas HVS & Tinta')->first();
    expect($pengeluaran)->not->toBeNull();
    expect($pengeluaran->bukti)->not->toBeNull();
    Storage::disk('public')->assertExists($pengeluaran->bukti);

    $firstPath = $pengeluaran->bukti;

    // 2. Edit expense and replace proof
    $newFile = UploadedFile::fake()->image('nota_revisi.png')->size(900);
    Livewire::test(ArusKasKeluar::class)
        ->call('openEditModal', $pengeluaran->id)
        ->assertSet('showEditModal', true)
        ->set('edit_jumlah', 160000)
        ->set('edit_keterangan', 'Beli Kertas HVS & Tinta (Revisi)')
        ->set('edit_bukti_foto', $newFile)
        ->call('updateExpense')
        ->assertHasNoErrors()
        ->assertSet('showEditModal', false);

    $pengeluaran->refresh();
    expect((float)$pengeluaran->jumlah)->toEqual(160000.0);
    expect($pengeluaran->keterangan)->toEqual('Beli Kertas HVS & Tinta (Revisi)');
    expect($pengeluaran->bukti)->not->toEqual($firstPath);
    Storage::disk('public')->assertExists($pengeluaran->bukti);
    Storage::disk('public')->assertMissing($firstPath);

    // 3. Delete proof via edit modal
    Livewire::test(ArusKasKeluar::class)
        ->call('openEditModal', $pengeluaran->id)
        ->call('deleteEditBukti')
        ->assertHasNoErrors();

    $pengeluaran->refresh();
    expect($pengeluaran->bukti)->toBeNull();
});

test('pengeluaran: rejects photo over 2MB on ArusKasKeluar', function () {
    $this->actingAs($this->userFinance);

    $oversized = UploadedFile::fake()->image('bon_besar.jpg')->size(2500);

    Livewire::test(ArusKasKeluar::class)
        ->set('kategori_pengeluaran_id', $this->kategori->id)
        ->set('jumlah', 50000)
        ->set('tanggal', now()->toDateString())
        ->set('bukti_foto', $oversized)
        ->call('saveExpense')
        ->assertHasErrors(['bukti_foto']);
});

test('pengeluaran: upload and edit proof on ArusKas main page', function () {
    $this->actingAs($this->userFinance);

    $file = UploadedFile::fake()->image('nota_konsumsi.webp')->size(750);

    Livewire::test(ArusKas::class)
        ->set('kategori_pengeluaran_id', $this->kategori->id)
        ->set('jumlah_keluar', 75000)
        ->set('tanggal_keluar', now()->toDateString())
        ->set('keterangan_keluar', 'Konsumsi Rapat Guru')
        ->set('bukti_keluar', $file)
        ->call('saveExpense')
        ->assertHasNoErrors();

    $pengeluaran = Pengeluaran::where('keterangan', 'Konsumsi Rapat Guru')->first();

    expect($pengeluaran)->not->toBeNull();
    expect($pengeluaran->bukti)->not->toBeNull();
    Storage::disk('public')->assertExists($pengeluaran->bukti);

    // Edit proof on ArusKas
    $newFile = UploadedFile::fake()->image('struk_final.jpg')->size(600);
    Livewire::test(ArusKas::class)
        ->call('openEditExpenseModal', $pengeluaran->id)
        ->set('edit_bukti_keluar', $newFile)
        ->call('updateExpense')
        ->assertHasNoErrors();

    $pengeluaran->refresh();
    Storage::disk('public')->assertExists($pengeluaran->bukti);
});

test('pengeluaran: upload photo proof on LaporanPengeluaran', function () {
    $this->actingAs($this->userFinance);

    $file = UploadedFile::fake()->image('struk_manual.jpg')->size(650);

    Livewire::test(\App\Livewire\Finance\Laporan\LaporanPengeluaran::class)
        ->call('openCreateModal')
        ->set('createTanggal', now()->toDateString())
        ->set('createKategoriId', $this->kategori->id)
        ->set('createJumlah', 85000)
        ->set('createKeterangan', 'Pembelian Spidol & Penghapus')
        ->set('createBukti', $file)
        ->call('savePengeluaran')
        ->assertHasNoErrors();

    $p = Pengeluaran::where('keterangan', 'Pembelian Spidol & Penghapus')->first();
    expect($p)->not->toBeNull();
    expect($p->bukti)->not->toBeNull();
    Storage::disk('public')->assertExists($p->bukti);
});

test('pembayaran: upload image is purely OPTIONAL and works without errors when omitted', function () {
    $this->actingAs($this->userFinance);

    Livewire::test(InputPembayaran::class)
        ->set('siswa_id', $this->siswa->id)
        ->set('tagihan_id', $this->tagihan->id)
        ->set('nominal_dibayar', 250000)
        ->set('metode_bayar', 'Tunai')
        ->set('bukti_foto', null) // No photo uploaded
        ->call('savePayment')
        ->assertHasNoErrors();

    $pembayaran = Pembayaran::where('tagihan_id', $this->tagihan->id)->latest()->first();
    expect($pembayaran)->not->toBeNull();
    expect($pembayaran->bukti_bayar)->toBeNull();
});

test('pengeluaran: upload image is purely OPTIONAL on ArusKasKeluar and ArusKas when omitted', function () {
    $this->actingAs($this->userFinance);

    // 1. ArusKasKeluar without proof
    Livewire::test(ArusKasKeluar::class)
        ->set('kategori_pengeluaran_id', $this->kategori->id)
        ->set('jumlah', 50000)
        ->set('tanggal', now()->toDateString())
        ->set('keterangan', 'Pengeluaran Tanpa Foto')
        ->set('bukti_foto', null)
        ->call('saveExpense')
        ->assertHasNoErrors();

    $p1 = Pengeluaran::where('keterangan', 'Pengeluaran Tanpa Foto')->first();
    expect($p1)->not->toBeNull();
    expect($p1->bukti)->toBeNull();

    // 2. ArusKas without proof
    Livewire::test(ArusKas::class)
        ->set('kategori_pengeluaran_id', $this->kategori->id)
        ->set('jumlah_keluar', 45000)
        ->set('tanggal_keluar', now()->toDateString())
        ->set('keterangan_keluar', 'Pengeluaran Kas Tanpa Foto')
        ->set('bukti_keluar', null)
        ->call('saveExpense')
        ->assertHasNoErrors();

    $p2 = Pengeluaran::where('keterangan', 'Pengeluaran Kas Tanpa Foto')->first();
    expect($p2)->not->toBeNull();
    expect($p2->bukti)->toBeNull();
});

test('arus_kas: bulk upload proof for multiple transactions simultaneously', function () {
    $this->actingAs($this->userFinance);

    $p1 = Pengeluaran::create([
        'kategori_pengeluaran_id' => $this->kategori->id,
        'jumlah' => 120000,
        'tanggal' => now()->toDateString(),
        'keterangan' => 'Belanja ATK 1',
        'petugas_id' => $this->userFinance->id,
    ]);

    $p2 = Pengeluaran::create([
        'kategori_pengeluaran_id' => $this->kategori->id,
        'jumlah' => 85000,
        'tanggal' => now()->toDateString(),
        'keterangan' => 'Belanja ATK 2',
        'petugas_id' => $this->userFinance->id,
    ]);

    $infaq = \App\Models\PemasukanKas::create([
        'kategori' => 'Infaq',
        'jumlah' => 500000,
        'tanggal' => now()->toDateString(),
        'keterangan' => 'Infaq Donatur Masjid',
        'petugas_id' => $this->userFinance->id,
    ]);

    $file = UploadedFile::fake()->image('bukti_gabungan_kas.jpg')->size(450);

    Livewire::test(ArusKas::class)
        ->set('selectedTransactionIds', ['op_' . $p1->id, 'op_' . $p2->id, 'infaq_' . $infaq->id])
        ->call('openBulkUploadBuktiModal')
        ->assertSet('showUploadBuktiModal', true)
        ->assertSet('isBulkUpload', true)
        ->set('uploadBuktiFile', $file)
        ->call('saveUploadedBukti')
        ->assertHasNoErrors()
        ->assertSet('showUploadBuktiModal', false)
        ->assertSet('selectedTransactionIds', []);

    $p1->refresh();
    $p2->refresh();
    $infaq->refresh();

    expect($p1->bukti)->not->toBeNull();
    expect($p2->bukti)->not->toBeNull();
    expect($infaq->bukti)->not->toBeNull();
    expect($p1->bukti)->toBe($p2->bukti);
    expect($p1->bukti)->toBe($infaq->bukti);

    Storage::disk('public')->assertExists($p1->bukti);
});

test('arus_kas: finance bulk delete submits approval request', function () {
    $this->actingAs($this->userFinance);

    $p1 = Pengeluaran::create([
        'kategori_pengeluaran_id' => $this->kategori->id,
        'jumlah' => 250000,
        'tanggal' => now()->toDateString(),
        'keterangan' => 'Perbaikan Pintu',
        'petugas_id' => $this->userFinance->id,
    ]);

    $infaq = \App\Models\PemasukanKas::create([
        'kategori' => 'Infaq',
        'jumlah' => 300000,
        'tanggal' => now()->toDateString(),
        'keterangan' => 'Infaq Hamba Allah',
        'petugas_id' => $this->userFinance->id,
    ]);

    Livewire::test(ArusKas::class)
        ->set('selectedTransactionIds', ['op_' . $p1->id, 'infaq_' . $infaq->id])
        ->call('bulkDeleteTransactions', 'Alasan salah input massal')
        ->assertHasNoErrors()
        ->assertSet('selectedTransactionIds', []);

    expect(\App\Models\ApprovalKeuangan::where('model_type', Pengeluaran::class)->where('model_id', $p1->id)->where('status', 'menunggu')->exists())->toBeTrue();
    expect(\App\Models\ApprovalKeuangan::where('model_type', \App\Models\PemasukanKas::class)->where('model_id', $infaq->id)->where('status', 'menunggu')->exists())->toBeTrue();
});

test('arus_kas: super admin bulk delete directly deletes transactions', function () {
    $roleSuperAdmin = Role::where('nama', 'super_admin')->first();
    $userSuperAdmin = User::whereHas('role', fn($q) => $q->where('nama', 'super_admin'))->first() ?? User::create([
        'nama' => 'Super Admin Test',
        'username' => 'super_admin_test_delete',
        'email' => 'superadmin_test@test.com',
        'password' => bcrypt('password'),
        'role_id' => $roleSuperAdmin->id,
    ]);

    $this->actingAs($userSuperAdmin);

    $p1 = Pengeluaran::create([
        'kategori_pengeluaran_id' => $this->kategori->id,
        'jumlah' => 150000,
        'tanggal' => now()->toDateString(),
        'keterangan' => 'Beli Bensin',
        'petugas_id' => $userSuperAdmin->id,
    ]);

    $infaq = \App\Models\PemasukanKas::create([
        'kategori' => 'Infaq',
        'jumlah' => 200000,
        'tanggal' => now()->toDateString(),
        'keterangan' => 'Infaq Kotak Amal',
        'petugas_id' => $userSuperAdmin->id,
    ]);

    Livewire::test(ArusKas::class)
        ->set('selectedTransactionIds', ['op_' . $p1->id, 'infaq_' . $infaq->id])
        ->call('bulkDeleteTransactions')
        ->assertHasNoErrors()
        ->assertSet('selectedTransactionIds', []);

    expect(Pengeluaran::find($p1->id))->toBeNull();
    expect(\App\Models\PemasukanKas::find($infaq->id))->toBeNull();
});


