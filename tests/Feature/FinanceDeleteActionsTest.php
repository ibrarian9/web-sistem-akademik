<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\JenisTagihan;
use App\Models\Tagihan;
use App\Models\DanaBos;
use App\Models\PemasukanKas;
use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;
use App\Models\Guru;
use App\Models\GajiGuru;
use App\Models\ApprovalKeuangan;
use App\Models\Pembayaran;
use App\Models\Tabungan;
use App\Livewire\Finance\DanaBos as DanaBosComponent;
use App\Livewire\Finance\ArusKasMasuk;
use App\Livewire\Finance\ArusKasKeluar;
use App\Livewire\Finance\ManajemenTagihan;
use App\Livewire\Finance\ManajemenGajiGuru;
use App\Livewire\Finance\DetailTagihanSiswa;
use App\Livewire\Finance\ApprovalKeuanganIndex;
use App\Livewire\Finance\Dashboard;
use App\Livewire\Finance\TabunganSiswa as TabunganSiswaComponent;
use App\Livewire\Finance\Laporan\LaporanPengeluaran;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

    $roleSuperAdmin = Role::where('nama', 'super_admin')->first();
    $roleFinance = Role::where('nama', 'finance')->first();
    $roleSuperAdmin2 = Role::where('nama', 'super_admin_2')->first();
    $roleGuru = Role::where('nama', 'guru')->first();
    $roleMurid = Role::where('nama', 'murid')->first();

    $this->superAdmin = User::create([
        'nama' => 'Super Admin Test',
        'username' => 'superadmin_test',
        'email' => 'superadmin_test@yayasan.or.id',
        'password' => bcrypt('password123'),
        'role_id' => $roleSuperAdmin->id,
        'status' => 'aktif',
    ]);

    $this->financeUser = User::create([
        'nama' => 'Staff Keuangan Test',
        'username' => 'finance_test',
        'email' => 'finance_test@yayasan.or.id',
        'password' => bcrypt('password123'),
        'role_id' => $roleFinance->id,
        'status' => 'aktif',
    ]);

    $this->superAdmin2 = User::create([
        'nama' => 'Super Admin 2 Test',
        'username' => 'superadmin2_test',
        'email' => 'superadmin2_test@yayasan.or.id',
        'password' => bcrypt('password123'),
        'role_id' => $roleSuperAdmin2->id,
        'status' => 'aktif',
    ]);

    $this->ta = TahunAjaran::create([
        'nama' => '2026/2027',
        'status_aktif' => true,
    ]);

    $this->semester = Semester::create([
        'tahun_ajaran_id' => $this->ta->id,
        'semester' => 'ganjil',
        'tanggal_mulai' => date('Y-m-d'),
        'tanggal_selesai' => date('Y-m-d', strtotime('+6 months')),
        'status_aktif' => true,
    ]);

    $this->kelas = Kelas::create([
        'nama_kelas' => '7A',
        'tingkat' => 7,
        'semester_id' => $this->semester->id,
    ]);

    $this->userMurid = User::create([
        'nama' => 'Siswa Test A',
        'username' => 'siswa_test_a',
        'email' => 'siswa_test_a@yayasan.or.id',
        'password' => bcrypt('password123'),
        'role_id' => $roleMurid->id,
        'status' => 'aktif',
    ]);

    $this->siswa = Siswa::create([
        'user_id' => $this->userMurid->id,
        'nis' => '10001',
        'kelas_id' => $this->kelas->id,
        'tanggal_masuk' => '2026-07-01',
    ]);
});

/* =========================================================================
 * 1. DANA BOS: Single and Bulk Deletion Tests
 * ========================================================================= */

test('super admin can delete single dana bos transaction directly', function () {
    $tx = DanaBos::create([
        'tahun_ajaran_id' => $this->ta->id,
        'tanggal' => now(),
        'jenis' => 'masuk',
        'kategori' => 'BOS Reguler',
        'nominal' => 15000000,
        'keterangan' => 'Pencairan BOS Tahap 1',
    ]);

    $this->actingAs($this->superAdmin);

    Livewire::test(DanaBosComponent::class)
        ->call('deleteTransaction', $tx->id)
        ->assertSee('berhasil dihapus');

    expect(DanaBos::find($tx->id))->toBeNull();
});

test('finance staff deleting dana bos transaction routes to financial approval request', function () {
    $tx = DanaBos::create([
        'tahun_ajaran_id' => $this->ta->id,
        'tanggal' => now(),
        'jenis' => 'keluar',
        'kategori' => 'Operasional',
        'nominal' => 2500000,
        'keterangan' => 'Beli ATK BOS',
    ]);

    $this->actingAs($this->financeUser);

    Livewire::test(DanaBosComponent::class)
        ->call('deleteTransaction', $tx->id, 'Salah input nominal BOS')
        ->assertSee('diajukan');

    // Transaction is not deleted yet
    expect(DanaBos::find($tx->id))->not->toBeNull();

    // Approval request is recorded
    $approval = ApprovalKeuangan::where('fitur', 'dana_bos')
        ->where('model_id', $tx->id)
        ->where('tipe_aksi', 'hapus')
        ->first();

    expect($approval)->not->toBeNull();
    expect($approval->pemohon_id)->toBe($this->financeUser->id);
    expect($approval->alasan)->toContain('Salah input nominal BOS');
});

test('super admin can bulk delete selected dana bos transactions', function () {
    $tx1 = DanaBos::create(['tahun_ajaran_id' => $this->ta->id, 'tanggal' => now(), 'jenis' => 'masuk', 'kategori' => 'BOS', 'nominal' => 1000000, 'keterangan' => 'BOS 1']);
    $tx2 = DanaBos::create(['tahun_ajaran_id' => $this->ta->id, 'tanggal' => now(), 'jenis' => 'masuk', 'kategori' => 'BOS', 'nominal' => 2000000, 'keterangan' => 'BOS 2']);
    $tx3 = DanaBos::create(['tahun_ajaran_id' => $this->ta->id, 'tanggal' => now(), 'jenis' => 'masuk', 'kategori' => 'BOS', 'nominal' => 3000000, 'keterangan' => 'BOS 3']);

    $this->actingAs($this->superAdmin);

    Livewire::test(DanaBosComponent::class)
        ->set('selectedIds', [$tx1->id, $tx2->id])
        ->call('bulkDelete')
        ->assertSee('Berhasil menghapus 2 catatan transaksi');

    expect(DanaBos::find($tx1->id))->toBeNull();
    expect(DanaBos::find($tx2->id))->toBeNull();
    expect(DanaBos::find($tx3->id))->not->toBeNull();
});

test('finance staff is blocked from bulk deleting dana bos transactions', function () {
    $tx1 = DanaBos::create(['tahun_ajaran_id' => $this->ta->id, 'tanggal' => now(), 'jenis' => 'masuk', 'kategori' => 'BOS', 'nominal' => 1000000, 'keterangan' => 'BOS 1']);

    $this->actingAs($this->financeUser);

    Livewire::test(DanaBosComponent::class)
        ->set('selectedIds', [$tx1->id])
        ->call('bulkDelete')
        ->assertSee('Akses Ditolak');

    expect(DanaBos::find($tx1->id))->not->toBeNull();
});

test('super admin 2 is blocked from deleting dana bos transactions', function () {
    $tx = DanaBos::create(['tahun_ajaran_id' => $this->ta->id, 'tanggal' => now(), 'jenis' => 'masuk', 'kategori' => 'BOS', 'nominal' => 1000000, 'keterangan' => 'BOS 1']);

    $this->actingAs($this->superAdmin2);

    Livewire::test(DanaBosComponent::class)
        ->call('deleteTransaction', $tx->id)
        ->assertSee('Akses Ditolak');

    expect(DanaBos::find($tx->id))->not->toBeNull();
});

/* =========================================================================
 * 2. ARUS KAS MASUK: Single and Bulk Deletion Tests
 * ========================================================================= */

test('super admin can delete single pemasukan kas yayasan directly', function () {
    $inflow = PemasukanKas::create([
        'tanggal' => now(),
        'kategori' => 'Infaq',
        'jumlah' => 500000,
        'keterangan' => 'Infaq Jumat Berkah',
        'petugas_id' => $this->superAdmin->id,
    ]);

    $this->actingAs($this->superAdmin);

    Livewire::test(ArusKasMasuk::class)
        ->call('deleteIncome', $inflow->id)
        ->assertSee('berhasil dihapus');

    expect(PemasukanKas::find($inflow->id))->toBeNull();
});

test('finance staff deleting pemasukan kas creates financial approval request', function () {
    $inflow = PemasukanKas::create([
        'tanggal' => now(),
        'kategori' => 'Donasi',
        'jumlah' => 1000000,
        'keterangan' => 'Donasi Pembangunan',
        'petugas_id' => $this->financeUser->id,
    ]);

    $this->actingAs($this->financeUser);

    Livewire::test(ArusKasMasuk::class)
        ->call('deleteIncome', $inflow->id, 'Salah catat rekening donasi')
        ->assertSee('diajukan');

    expect(PemasukanKas::find($inflow->id))->not->toBeNull();

    $approval = ApprovalKeuangan::where('fitur', 'arus_kas')
        ->where('model_id', $inflow->id)
        ->where('tipe_aksi', 'hapus')
        ->first();

    expect($approval)->not->toBeNull();
    expect($approval->pemohon_id)->toBe($this->financeUser->id);
});

test('super admin can bulk delete selected pemasukan kas records', function () {
    $in1 = PemasukanKas::create(['tanggal' => now(), 'kategori' => 'Infaq', 'jumlah' => 100000, 'keterangan' => 'Infaq 1', 'petugas_id' => $this->superAdmin->id]);
    $in2 = PemasukanKas::create(['tanggal' => now(), 'kategori' => 'Infaq', 'jumlah' => 200000, 'keterangan' => 'Infaq 2', 'petugas_id' => $this->superAdmin->id]);
    $in3 = PemasukanKas::create(['tanggal' => now(), 'kategori' => 'Infaq', 'jumlah' => 300000, 'keterangan' => 'Infaq 3', 'petugas_id' => $this->superAdmin->id]);

    $this->actingAs($this->superAdmin);

    Livewire::test(ArusKasMasuk::class)
        ->set('selectedIds', [$in1->id, $in2->id])
        ->call('bulkDelete')
        ->assertSee('Berhasil menghapus 2 catatan');

    expect(PemasukanKas::find($in1->id))->toBeNull();
    expect(PemasukanKas::find($in2->id))->toBeNull();
    expect(PemasukanKas::find($in3->id))->not->toBeNull();
});

test('finance staff is blocked from bulk deleting pemasukan kas', function () {
    $in1 = PemasukanKas::create(['tanggal' => now(), 'kategori' => 'Infaq', 'jumlah' => 100000, 'keterangan' => 'Infaq 1', 'petugas_id' => $this->financeUser->id]);

    $this->actingAs($this->financeUser);

    Livewire::test(ArusKasMasuk::class)
        ->set('selectedIds', [$in1->id])
        ->call('bulkDelete')
        ->assertSee('Akses Ditolak');

    expect(PemasukanKas::find($in1->id))->not->toBeNull();
});

/* =========================================================================
 * 3. ARUS KAS KELUAR: Single and Bulk Deletion Tests
 * ========================================================================= */

test('super admin can delete single pengeluaran operasional directly', function () {
    $kat = KategoriPengeluaran::firstOrCreate(['nama' => 'Konsumsi']);

    $out = Pengeluaran::create([
        'tanggal' => now(),
        'kategori_pengeluaran_id' => $kat->id,
        'jumlah' => 350000,
        'keterangan' => 'Snack Rapat Guru',
        'petugas_id' => $this->superAdmin->id,
    ]);

    $this->actingAs($this->superAdmin);

    Livewire::test(ArusKasKeluar::class)
        ->call('deleteExpense', $out->id)
        ->assertSee('berhasil dihapus');

    expect(Pengeluaran::find($out->id))->toBeNull();
});

test('finance staff deleting pengeluaran operasional creates financial approval request', function () {
    $kat = KategoriPengeluaran::firstOrCreate(['nama' => 'ATK']);

    $out = Pengeluaran::create([
        'tanggal' => now(),
        'kategori_pengeluaran_id' => $kat->id,
        'jumlah' => 150000,
        'keterangan' => 'Kertas HVS',
        'petugas_id' => $this->financeUser->id,
    ]);

    $this->actingAs($this->financeUser);

    Livewire::test(ArusKasKeluar::class)
        ->call('deleteExpense', $out->id, 'Kuitansi dibatalkan')
        ->assertSee('diajukan');

    expect(Pengeluaran::find($out->id))->not->toBeNull();

    $approval = ApprovalKeuangan::where('fitur', 'arus_kas')
        ->where('model_id', $out->id)
        ->where('tipe_aksi', 'hapus')
        ->first();

    expect($approval)->not->toBeNull();
    expect($approval->pemohon_id)->toBe($this->financeUser->id);
});

test('super admin can bulk delete selected pengeluaran operasional', function () {
    $kat = KategoriPengeluaran::firstOrCreate(['nama' => 'Operasional']);

    $out1 = Pengeluaran::create(['tanggal' => now(), 'kategori_pengeluaran_id' => $kat->id, 'jumlah' => 100000, 'keterangan' => 'A', 'petugas_id' => $this->superAdmin->id]);
    $out2 = Pengeluaran::create(['tanggal' => now(), 'kategori_pengeluaran_id' => $kat->id, 'jumlah' => 200000, 'keterangan' => 'B', 'petugas_id' => $this->superAdmin->id]);
    $out3 = Pengeluaran::create(['tanggal' => now(), 'kategori_pengeluaran_id' => $kat->id, 'jumlah' => 300000, 'keterangan' => 'C', 'petugas_id' => $this->superAdmin->id]);

    $this->actingAs($this->superAdmin);

    Livewire::test(ArusKasKeluar::class)
        ->set('selectedIds', [$out1->id, $out2->id])
        ->call('bulkDelete')
        ->assertSee('Berhasil menghapus 2 catatan');

    expect(Pengeluaran::find($out1->id))->toBeNull();
    expect(Pengeluaran::find($out2->id))->toBeNull();
    expect(Pengeluaran::find($out3->id))->not->toBeNull();
});

test('finance staff is blocked from bulk deleting pengeluaran operasional', function () {
    $kat = KategoriPengeluaran::firstOrCreate(['nama' => 'Operasional']);
    $out = Pengeluaran::create(['tanggal' => now(), 'kategori_pengeluaran_id' => $kat->id, 'jumlah' => 100000, 'keterangan' => 'X', 'petugas_id' => $this->financeUser->id]);

    $this->actingAs($this->financeUser);

    Livewire::test(ArusKasKeluar::class)
        ->set('selectedIds', [$out->id])
        ->call('bulkDelete')
        ->assertSee('Akses Ditolak');

    expect(Pengeluaran::find($out->id))->not->toBeNull();
});

/* =========================================================================
 * 4. MANAJEMEN TAGIHAN: Single and Bulk Deletion Tests
 * ========================================================================= */

test('super admin can delete unpaid tagihan directly', function () {
    $jenis = JenisTagihan::firstOrCreate(['nama' => 'SPP'], ['default_nominal' => 350000]);

    $tagihan = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'Juli 2026',
        'nominal' => 350000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    $this->actingAs($this->superAdmin);

    Livewire::test(ManajemenTagihan::class)
        ->call('deleteTagihan', $tagihan->id)
        ->assertDispatched('show-alert');

    expect(Tagihan::find($tagihan->id))->toBeNull();
});

test('tagihan with existing payment cannot be deleted', function () {
    $jenis = JenisTagihan::firstOrCreate(['nama' => 'SPP'], ['default_nominal' => 350000]);

    $tagihan = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'Agustus 2026',
        'nominal' => 350000,
        'total_dibayar' => 200000,
        'status' => 'sebagian',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    $this->actingAs($this->superAdmin);

    Livewire::test(ManajemenTagihan::class)
        ->call('deleteTagihan', $tagihan->id)
        ->assertDispatched('show-alert');

    expect(Tagihan::find($tagihan->id))->not->toBeNull();
});

test('finance staff deleting tagihan routes to approval request', function () {
    $jenis = JenisTagihan::firstOrCreate(['nama' => 'SPP'], ['default_nominal' => 350000]);

    $tagihan = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'September 2026',
        'nominal' => 350000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    $this->actingAs($this->financeUser);

    Livewire::test(ManajemenTagihan::class)
        ->call('deleteTagihan', $tagihan->id, 'Siswa pindah sekolah')
        ->assertDispatched('show-alert');

    expect(Tagihan::find($tagihan->id))->not->toBeNull();

    $approval = ApprovalKeuangan::where('fitur', 'tagihan')
        ->where('model_id', $tagihan->id)
        ->where('tipe_aksi', 'hapus')
        ->first();

    expect($approval)->not->toBeNull();
    expect($approval->pemohon_id)->toBe($this->financeUser->id);
});

test('super admin can bulk delete unpaid tagihans for selected students and skips paid ones', function () {
    $jenis = JenisTagihan::firstOrCreate(['nama' => 'SPP'], ['default_nominal' => 350000]);

    // Unpaid tagihan
    $t1 = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'Oktober 2026',
        'nominal' => 300000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    // Paid tagihan (must be skipped)
    $t2 = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'November 2026',
        'nominal' => 300000,
        'total_dibayar' => 300000,
        'status' => 'lunas',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    $this->actingAs($this->superAdmin);

    Livewire::test(ManajemenTagihan::class)
        ->set('selectedIds', [$this->siswa->id])
        ->call('bulkDelete')
        ->assertDispatched('show-alert');

    expect(Tagihan::find($t1->id))->toBeNull();
    expect(Tagihan::find($t2->id))->not->toBeNull();
});

test('finance staff can bulk submit approval requests for unpaid tagihan of selected students and skips paid ones', function () {
    $jenis = JenisTagihan::firstOrCreate(['nama' => 'SPP'], ['default_nominal' => 350000]);

    // Unpaid tagihan 1
    $t1 = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'November 2026',
        'nominal' => 300000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    // Unpaid tagihan 2
    $t2 = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'Desember 2026',
        'nominal' => 300000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    // Paid tagihan (must be skipped)
    $tPaid = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'Januari 2027',
        'nominal' => 300000,
        'total_dibayar' => 300000,
        'status' => 'lunas',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    $this->actingAs($this->financeUser);

    Livewire::test(ManajemenTagihan::class)
        ->set('selectedIds', [$this->siswa->id])
        ->call('bulkDelete')
        ->assertDispatched('show-alert');

    // Unpaid bills are NOT deleted yet (they are pending approval)
    expect(Tagihan::find($t1->id))->not->toBeNull();
    expect(Tagihan::find($t2->id))->not->toBeNull();
    expect(Tagihan::find($tPaid->id))->not->toBeNull();

    // Approvals created for unpaid bills
    $approval1 = ApprovalKeuangan::where('fitur', 'tagihan')
        ->where('model_id', $t1->id)
        ->where('tipe_aksi', 'hapus')
        ->where('status', 'menunggu')
        ->first();
    expect($approval1)->not->toBeNull();
    expect($approval1->pemohon_id)->toBe($this->financeUser->id);

    $approval2 = ApprovalKeuangan::where('fitur', 'tagihan')
        ->where('model_id', $t2->id)
        ->where('tipe_aksi', 'hapus')
        ->where('status', 'menunggu')
        ->first();
    expect($approval2)->not->toBeNull();
    expect($approval2->pemohon_id)->toBe($this->financeUser->id);

    // Paid bill must not have an approval request
    $approvalPaid = ApprovalKeuangan::where('fitur', 'tagihan')
        ->where('model_id', $tPaid->id)
        ->first();
    expect($approvalPaid)->toBeNull();
});

test('super admin 2 is blocked from bulk deleting or submitting tagihan in ManajemenTagihan', function () {
    $jenis = JenisTagihan::firstOrCreate(['nama' => 'SPP'], ['default_nominal' => 350000]);
    $t = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'Februari 2027',
        'nominal' => 300000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    $this->actingAs($this->superAdmin2);

    Livewire::test(ManajemenTagihan::class)
        ->set('selectedIds', [$this->siswa->id])
        ->call('bulkDelete')
        ->assertDispatched('show-alert');

    expect(Tagihan::find($t->id))->not->toBeNull();
    $approval = ApprovalKeuangan::where('fitur', 'tagihan')->where('model_id', $t->id)->first();
    expect($approval)->toBeNull();
});

test('finance staff sees Ajukan Hapus Terpilih in bulk action bar when students are selected', function () {
    $this->actingAs($this->financeUser);

    Livewire::test(ManajemenTagihan::class)
        ->set('selectedIds', [$this->siswa->id])
        ->assertSee('Ajukan Hapus Terpilih')
        ->assertSee('Terpilih');
});

test('super admin sees Hapus Terpilih in bulk action bar when students are selected', function () {
    $this->actingAs($this->superAdmin);

    Livewire::test(ManajemenTagihan::class)
        ->set('selectedIds', [$this->siswa->id])
        ->assertSee('Hapus Terpilih')
        ->assertDontSee('Ajukan Hapus Terpilih');
});

/* =========================================================================
 * 5. MANAJEMEN GAJI GURU: Single and Bulk Deletion Tests
 * ========================================================================= */

test('finance staff can delete draft salary record directly', function () {
    $roleGuru = Role::where('nama', 'guru')->first();
    $userGuru = User::create([
        'nama' => 'Guru Test B',
        'username' => 'guru_test_b',
        'email' => 'guru_b@yayasan.or.id',
        'password' => bcrypt('password123'),
        'role_id' => $roleGuru->id,
        'status' => 'aktif',
    ]);
    $guru = Guru::create(['user_id' => $userGuru->id, 'nip' => '99901', 'tanggal_masuk' => '2020-01-01']);

    $gaji = GajiGuru::create([
        'guru_id' => $guru->id,
        'bulan' => 'Januari',
        'tahun' => 2026,
        'gaji_pokok' => 2500000,
        'insentif_bpjs' => 0,
        'insentif_maghrib_mengaji' => 0,
        'potongan_peminjaman' => 0,
        'potongan_lainnya' => 100000,
        'total_diterima' => 2400000,
        'tanggal_bayar' => now(),
        'status' => 'draft',
    ]);

    $this->actingAs($this->financeUser);

    Livewire::test(ManajemenGajiGuru::class)
        ->call('deleteDraft', $gaji->id);

    expect(GajiGuru::find($gaji->id))->toBeNull();
});

test('finance staff deleting paid salary creates approval request', function () {
    $roleGuru = Role::where('nama', 'guru')->first();
    $userGuru = User::create([
        'nama' => 'Guru Test C',
        'username' => 'guru_test_c',
        'email' => 'guru_c@yayasan.or.id',
        'password' => bcrypt('password123'),
        'role_id' => $roleGuru->id,
        'status' => 'aktif',
    ]);
    $guru = Guru::create(['user_id' => $userGuru->id, 'nip' => '99902', 'tanggal_masuk' => '2020-01-01']);

    $gaji = GajiGuru::create([
        'guru_id' => $guru->id,
        'bulan' => 'Februari',
        'tahun' => 2026,
        'gaji_pokok' => 2500000,
        'insentif_bpjs' => 0,
        'insentif_maghrib_mengaji' => 0,
        'potongan_peminjaman' => 0,
        'potongan_lainnya' => 100000,
        'total_diterima' => 2400000,
        'tanggal_bayar' => now(),
        'status' => 'dibayar',
    ]);

    $this->actingAs($this->financeUser);

    Livewire::test(ManajemenGajiGuru::class)
        ->call('deleteSalary', $gaji->id, 'Koreksi jam mengajar');

    expect(GajiGuru::find($gaji->id))->not->toBeNull();

    $approval = ApprovalKeuangan::where('fitur', 'gaji_guru')
        ->where('model_id', $gaji->id)
        ->where('tipe_aksi', 'hapus')
        ->first();

    expect($approval)->not->toBeNull();
    expect($approval->pemohon_id)->toBe($this->financeUser->id);
});

test('super admin can bulk delete salaries directly', function () {
    $roleGuru = Role::where('nama', 'guru')->first();
    $userGuru = User::create([
        'nama' => 'Guru Test D',
        'username' => 'guru_test_d',
        'email' => 'guru_d@yayasan.or.id',
        'password' => bcrypt('password123'),
        'role_id' => $roleGuru->id,
        'status' => 'aktif',
    ]);
    $guru = Guru::create(['user_id' => $userGuru->id, 'nip' => '99903', 'tanggal_masuk' => '2020-01-01']);

    $g1 = GajiGuru::create([
        'guru_id' => $guru->id,
        'bulan' => 'Maret',
        'tahun' => 2026,
        'gaji_pokok' => 2000000,
        'insentif_bpjs' => 0,
        'insentif_maghrib_mengaji' => 0,
        'potongan_peminjaman' => 0,
        'potongan_lainnya' => 0,
        'total_diterima' => 2000000,
        'tanggal_bayar' => now(),
        'status' => 'draft',
    ]);
    $g2 = GajiGuru::create([
        'guru_id' => $guru->id,
        'bulan' => 'April',
        'tahun' => 2026,
        'gaji_pokok' => 2000000,
        'insentif_bpjs' => 0,
        'insentif_maghrib_mengaji' => 0,
        'potongan_peminjaman' => 0,
        'potongan_lainnya' => 0,
        'total_diterima' => 2000000,
        'tanggal_bayar' => now(),
        'status' => 'draft',
    ]);

    $this->actingAs($this->superAdmin);

    Livewire::test(ManajemenGajiGuru::class)
        ->set('selectedGajiIds', [$g1->id, $g2->id])
        ->call('deleteSelected');

    expect(GajiGuru::find($g1->id))->toBeNull();
    expect(GajiGuru::find($g2->id))->toBeNull();
});

/* =========================================================================
 * 6. DETAIL TAGIHAN SISWA: Single Deletion Tests (Tagihan & Pembayaran)
 * ========================================================================= */

test('super admin can delete unpaid tagihan directly in DetailTagihanSiswa', function () {
    $jenis = JenisTagihan::firstOrCreate(['nama' => 'Uang Gedung'], ['default_nominal' => 1000000]);
    $tagihan = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'Juli 2026',
        'nominal' => 1000000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    $this->actingAs($this->superAdmin);

    Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
        ->call('deleteTagihan', $tagihan->id)
        ->assertSee('Data tagihan berhasil dihapus.');

    expect(Tagihan::find($tagihan->id))->toBeNull();
});

test('finance staff deleting tagihan in DetailTagihanSiswa routes to financial approval request', function () {
    $jenis = JenisTagihan::firstOrCreate(['nama' => 'Uang Seragam'], ['default_nominal' => 500000]);
    $tagihan = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'Agustus 2026',
        'nominal' => 500000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    $this->actingAs($this->financeUser);

    Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
        ->call('deleteTagihan', $tagihan->id, 'Dispensasi siswa berprestasi')
        ->assertSee('diajukan');

    expect(Tagihan::find($tagihan->id))->not->toBeNull();

    $approval = ApprovalKeuangan::where('fitur', 'tagihan')
        ->where('model_id', $tagihan->id)
        ->where('tipe_aksi', 'hapus')
        ->first();

    expect($approval)->not->toBeNull();
    expect($approval->pemohon_id)->toBe($this->financeUser->id);
});

test('super admin 2 is blocked from deleting tagihan in DetailTagihanSiswa', function () {
    $jenis = JenisTagihan::firstOrCreate(['nama' => 'Uang Buku'], ['default_nominal' => 200000]);
    $tagihan = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'September 2026',
        'nominal' => 200000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    $this->actingAs($this->superAdmin2);

    Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
        ->call('deleteTagihan', $tagihan->id)
        ->assertSee('Akses Ditolak');

    expect(Tagihan::find($tagihan->id))->not->toBeNull();
});

test('super admin can delete pembayaran and rollback student deposit in DetailTagihanSiswa', function () {
    $jenis = JenisTagihan::firstOrCreate(['nama' => 'SPP'], ['default_nominal' => 350000]);
    $tagihan = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'Oktober 2026',
        'nominal' => 350000,
        'total_dibayar' => 350000,
        'status' => 'lunas',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    $this->siswa->update(['saldo_deposit' => 0]);

    $pembayaran = Pembayaran::create([
        'tagihan_id' => $tagihan->id,
        'nominal_dibayar' => 350000,
        'kelebihan_bayar' => 0,
        'metode_bayar' => 'Deposit',
        'petugas_id' => $this->superAdmin->id,
        'tanggal_bayar' => now(),
        'no_resi' => 'RES-TEST-001',
    ]);

    $this->actingAs($this->superAdmin);

    Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
        ->call('deletePembayaran', $pembayaran->id)
        ->assertSee('berhasil');

    expect(Pembayaran::find($pembayaran->id))->toBeNull();
    // Since payment was made via Deposit, student deposit must be refunded
    $this->siswa->refresh();
    expect((float)$this->siswa->saldo_deposit)->toBe(350000.0);
    $tagihan->refresh();
    expect($tagihan->status)->toBe('belum_bayar');
    expect((float)$tagihan->total_dibayar)->toBe(0.0);
});

test('finance staff deleting pembayaran in DetailTagihanSiswa routes to financial approval request', function () {
    $jenis = JenisTagihan::firstOrCreate(['nama' => 'SPP'], ['default_nominal' => 350000]);
    $tagihan = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'November 2026',
        'nominal' => 350000,
        'total_dibayar' => 350000,
        'status' => 'lunas',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    $pembayaran = Pembayaran::create([
        'tagihan_id' => $tagihan->id,
        'nominal_dibayar' => 350000,
        'kelebihan_bayar' => 0,
        'metode_bayar' => 'Tunai',
        'petugas_id' => $this->financeUser->id,
        'tanggal_bayar' => now(),
        'no_resi' => 'RES-TEST-002',
    ]);

    $this->actingAs($this->financeUser);

    Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
        ->call('deletePembayaran', $pembayaran->id, 'Salah cetak kuitansi')
        ->assertSee('diajukan');

    expect(Pembayaran::find($pembayaran->id))->not->toBeNull();

    $approval = ApprovalKeuangan::where('fitur', 'pembayaran')
        ->where('model_id', $pembayaran->id)
        ->where('tipe_aksi', 'hapus')
        ->first();

    expect($approval)->not->toBeNull();
    expect($approval->pemohon_id)->toBe($this->financeUser->id);
});

test('super admin 2 is blocked from deleting pembayaran in DetailTagihanSiswa', function () {
    $jenis = JenisTagihan::firstOrCreate(['nama' => 'SPP'], ['default_nominal' => 350000]);
    $tagihan = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'Desember 2026',
        'nominal' => 350000,
        'total_dibayar' => 350000,
        'status' => 'lunas',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    $pembayaran = Pembayaran::create([
        'tagihan_id' => $tagihan->id,
        'nominal_dibayar' => 350000,
        'kelebihan_bayar' => 0,
        'metode_bayar' => 'Tunai',
        'petugas_id' => $this->financeUser->id,
        'tanggal_bayar' => now(),
        'no_resi' => 'RES-TEST-003',
    ]);

    $this->actingAs($this->superAdmin2);

    Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
        ->call('deletePembayaran', $pembayaran->id)
        ->assertSee('Akses Ditolak');

    expect(Pembayaran::find($pembayaran->id))->not->toBeNull();
});

/* =========================================================================
 * 7. TABUNGAN SISWA: Deletion Tests
 * ========================================================================= */

test('super admin can delete tabungan transaction directly and recalculates balance', function () {
    $this->siswa->update(['saldo_tabungan' => 100000]);

    $tx = Tabungan::create([
        'kode_transaksi' => 'TBG-TEST-001',
        'siswa_id' => $this->siswa->id,
        'petugas_id' => $this->superAdmin->id,
        'jenis' => 'setor',
        'nominal' => 100000,
        'saldo_akhir' => 100000,
        'tanggal' => now(),
        'keterangan' => 'Setoran awal',
    ]);

    $this->actingAs($this->superAdmin);

    Livewire::test(TabunganSiswaComponent::class)
        ->call('deleteTransaction', $tx->id)
        ->assertDispatched('show-alert');

    expect(Tabungan::find($tx->id))->toBeNull();
    $this->siswa->refresh();
    expect((float)$this->siswa->saldo_tabungan)->toBe(0.0);
});

test('finance staff deleting tabungan transaction routes to financial approval request', function () {
    $this->siswa->update(['saldo_tabungan' => 50000]);

    $tx = Tabungan::create([
        'kode_transaksi' => 'TBG-TEST-002',
        'siswa_id' => $this->siswa->id,
        'petugas_id' => $this->financeUser->id,
        'jenis' => 'setor',
        'nominal' => 50000,
        'saldo_akhir' => 50000,
        'tanggal' => now(),
        'keterangan' => 'Setoran kedua',
    ]);

    $this->actingAs($this->financeUser);

    Livewire::test(TabunganSiswaComponent::class)
        ->call('deleteTransaction', $tx->id, 'Koreksi setoran tabungan')
        ->assertDispatched('show-alert');

    expect(Tabungan::find($tx->id))->not->toBeNull();

    $approval = ApprovalKeuangan::where('fitur', 'tabungan_siswa')
        ->where('model_id', $tx->id)
        ->where('tipe_aksi', 'hapus')
        ->first();

    expect($approval)->not->toBeNull();
    expect($approval->pemohon_id)->toBe($this->financeUser->id);
});

test('super admin 2 is blocked from deleting tabungan transaction', function () {
    $tx = Tabungan::create([
        'kode_transaksi' => 'TBG-TEST-003',
        'siswa_id' => $this->siswa->id,
        'petugas_id' => $this->financeUser->id,
        'jenis' => 'setor',
        'nominal' => 50000,
        'saldo_akhir' => 50000,
        'tanggal' => now(),
        'keterangan' => 'Setoran ketiga',
    ]);

    $this->actingAs($this->superAdmin2);

    Livewire::test(TabunganSiswaComponent::class)
        ->call('deleteTransaction', $tx->id)
        ->assertSee('Akses Ditolak');

    expect(Tabungan::find($tx->id))->not->toBeNull();
});

/* =========================================================================
 * 8. LAPORAN PENGELUARAN: Deletion Tests
 * ========================================================================= */

test('super admin can delete pengeluaran directly in LaporanPengeluaran', function () {
    $kat = KategoriPengeluaran::firstOrCreate(['nama' => 'Pemeliharaan Gedung']);
    $p = Pengeluaran::create([
        'tanggal' => now(),
        'kategori_pengeluaran_id' => $kat->id,
        'jumlah' => 750000,
        'keterangan' => 'Cat Tembok Kelas',
        'petugas_id' => $this->superAdmin->id,
    ]);

    $this->actingAs($this->superAdmin);

    Livewire::test(LaporanPengeluaran::class)
        ->call('deletePengeluaran', $p->id)
        ->assertSee('berhasil dihapus');

    expect(Pengeluaran::find($p->id))->toBeNull();
});

test('finance staff deleting pengeluaran in LaporanPengeluaran routes to approval request', function () {
    $kat = KategoriPengeluaran::firstOrCreate(['nama' => 'Logistik']);
    $p = Pengeluaran::create([
        'tanggal' => now(),
        'kategori_pengeluaran_id' => $kat->id,
        'jumlah' => 250000,
        'keterangan' => 'Sapu dan Pel',
        'petugas_id' => $this->financeUser->id,
    ]);

    $this->actingAs($this->financeUser);

    Livewire::test(LaporanPengeluaran::class)
        ->call('deletePengeluaran', $p->id, 'Koreksi logistik')
        ->assertSee('diajukan');

    expect(Pengeluaran::find($p->id))->not->toBeNull();

    $approval = ApprovalKeuangan::where('fitur', 'arus_kas')
        ->where('model_id', $p->id)
        ->where('tipe_aksi', 'hapus')
        ->first();

    expect($approval)->not->toBeNull();
    expect($approval->pemohon_id)->toBe($this->financeUser->id);
});

test('super admin 2 is blocked from deleting pengeluaran in LaporanPengeluaran', function () {
    $kat = KategoriPengeluaran::firstOrCreate(['nama' => 'Konsumsi']);
    $p = Pengeluaran::create([
        'tanggal' => now(),
        'kategori_pengeluaran_id' => $kat->id,
        'jumlah' => 100000,
        'keterangan' => 'Kopi Rapat',
        'petugas_id' => $this->superAdmin->id,
    ]);

    $this->actingAs($this->superAdmin2);

    Livewire::test(LaporanPengeluaran::class)
        ->call('deletePengeluaran', $p->id)
        ->assertSee('Akses Ditolak');

    expect(Pengeluaran::find($p->id))->not->toBeNull();
});

/* =========================================================================
 * 8. VISUAL STATUS & ROW HIGHLIGHT TESTS: Pengajuan Hapus pada Tagihan
 * ========================================================================= */

test('student row in ManajemenTagihan displays Pengajuan Hapus status and amber row highlight when bill is submitted for deletion', function () {
    $jenis = JenisTagihan::firstOrCreate(['nama' => 'SPP'], ['default_nominal' => 350000]);
    $tagihan = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'Maret 2027',
        'nominal' => 350000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    ApprovalKeuangan::create([
        'pemohon_id' => $this->financeUser->id,
        'tipe_aksi' => 'hapus',
        'fitur' => 'tagihan',
        'model_type' => Tagihan::class,
        'model_id' => $tagihan->id,
        'judul' => 'Hapus Tagihan: ' . ($this->siswa->user->nama ?? 'Siswa'),
        'alasan' => 'Pengajuan dispensasi siswa',
        'status' => 'menunggu',
    ]);

    $this->actingAs($this->financeUser);

    Livewire::test(ManajemenTagihan::class)
        ->assertSee('Pengajuan Hapus')
        ->assertSee('border-l-amber-500');
});

test('invoice row in DetailTagihanSiswa displays Pengajuan Hapus status, amber highlight, and Menunggu Persetujuan action', function () {
    $jenis = JenisTagihan::firstOrCreate(['nama' => 'Iuran Ekstrakurikuler'], ['default_nominal' => 150000]);
    $tagihan = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'April 2027',
        'nominal' => 150000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    ApprovalKeuangan::create([
        'pemohon_id' => $this->financeUser->id,
        'tipe_aksi' => 'hapus',
        'fitur' => 'tagihan',
        'model_type' => Tagihan::class,
        'model_id' => $tagihan->id,
        'judul' => 'Hapus Tagihan: ' . ($this->siswa->user->nama ?? 'Siswa'),
        'alasan' => 'Penyesuaian kegiatan ekskul',
        'status' => 'menunggu',
    ]);

    $this->actingAs($this->financeUser);

    Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
        ->assertSee('Pengajuan Hapus')
        ->assertSee('border-l-amber-500')
        ->assertSee('Menunggu Persetujuan');
});

test('quick detail modal in ManajemenTagihan displays Pengajuan Hapus status and amber highlight for pending bill', function () {
    $jenis = JenisTagihan::firstOrCreate(['nama' => 'Biaya Praktikum'], ['default_nominal' => 200000]);
    $tagihan = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'Mei 2027',
        'nominal' => 200000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    ApprovalKeuangan::create([
        'pemohon_id' => $this->financeUser->id,
        'tipe_aksi' => 'hapus',
        'fitur' => 'tagihan',
        'model_type' => Tagihan::class,
        'model_id' => $tagihan->id,
        'judul' => 'Hapus Tagihan: ' . ($this->siswa->user->nama ?? 'Siswa'),
        'alasan' => 'Dispensasi biaya praktikum',
        'status' => 'menunggu',
    ]);

    $this->actingAs($this->financeUser);

    Livewire::test(ManajemenTagihan::class)
        ->call('openQuickDetail', $this->siswa->id)
        ->assertSee('Pengajuan Hapus')
        ->assertSee('border-l-amber-500');
});

test('super admin can bulk delete multiple pembayaran in DetailTagihanSiswa and rolls back deposits and recalculates tagihan', function () {
    $jenis = JenisTagihan::firstOrCreate(['nama' => 'SPP'], ['default_nominal' => 350000]);
    $tagihan1 = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'Januari 2027',
        'nominal' => 350000,
        'total_dibayar' => 350000,
        'status' => 'lunas',
        'jatuh_tempo' => now()->addDays(10),
    ]);
    $tagihan2 = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'Februari 2027',
        'nominal' => 350000,
        'total_dibayar' => 350000,
        'status' => 'lunas',
        'jatuh_tempo' => now()->addDays(20),
    ]);

    $this->siswa->update(['saldo_deposit' => 0]);

    $pembayaran1 = Pembayaran::create([
        'tagihan_id' => $tagihan1->id,
        'nominal_dibayar' => 350000,
        'kelebihan_bayar' => 0,
        'metode_bayar' => 'Deposit',
        'petugas_id' => $this->superAdmin->id,
        'tanggal_bayar' => now(),
        'no_resi' => 'RES-BULK-001',
    ]);

    $pembayaran2 = Pembayaran::create([
        'tagihan_id' => $tagihan2->id,
        'nominal_dibayar' => 350000,
        'kelebihan_bayar' => 0,
        'metode_bayar' => 'Tunai',
        'petugas_id' => $this->superAdmin->id,
        'tanggal_bayar' => now(),
        'no_resi' => 'RES-BULK-002',
    ]);

    $this->actingAs($this->superAdmin);

    Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
        ->set('selectedPembayaranIds', [$pembayaran1->id, $pembayaran2->id])
        ->call('bulkDeletePembayaran')
        ->assertSee('Berhasil membatalkan dan menghapus');

    expect(Pembayaran::find($pembayaran1->id))->toBeNull();
    expect(Pembayaran::find($pembayaran2->id))->toBeNull();

    $this->siswa->refresh();
    expect((float)$this->siswa->saldo_deposit)->toBe(350000.0);

    $tagihan1->refresh();
    expect($tagihan1->status)->toBe('belum_bayar');
    expect((float)$tagihan1->total_dibayar)->toBe(0.0);

    $tagihan2->refresh();
    expect($tagihan2->status)->toBe('belum_bayar');
    expect((float)$tagihan2->total_dibayar)->toBe(0.0);
});

test('finance staff submitting bulk delete pembayaran in DetailTagihanSiswa creates approval requests', function () {
    $jenis = JenisTagihan::firstOrCreate(['nama' => 'SPP'], ['default_nominal' => 350000]);
    $tagihan = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'Maret 2027',
        'nominal' => 350000,
        'total_dibayar' => 350000,
        'status' => 'lunas',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    $pembayaran1 = Pembayaran::create([
        'tagihan_id' => $tagihan->id,
        'nominal_dibayar' => 150000,
        'kelebihan_bayar' => 0,
        'metode_bayar' => 'Tunai',
        'petugas_id' => $this->financeUser->id,
        'tanggal_bayar' => now(),
        'no_resi' => 'RES-FIN-001',
    ]);

    $pembayaran2 = Pembayaran::create([
        'tagihan_id' => $tagihan->id,
        'nominal_dibayar' => 200000,
        'kelebihan_bayar' => 0,
        'metode_bayar' => 'Tunai',
        'petugas_id' => $this->financeUser->id,
        'tanggal_bayar' => now(),
        'no_resi' => 'RES-FIN-002',
    ]);

    $this->actingAs($this->financeUser);

    Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
        ->set('selectedPembayaranIds', [$pembayaran1->id, $pembayaran2->id])
        ->call('bulkDeletePembayaran', 'Koreksi ganda pembayaran')
        ->assertSee('telah diajukan ke Super Admin');

    expect(Pembayaran::find($pembayaran1->id))->not->toBeNull();
    expect(Pembayaran::find($pembayaran2->id))->not->toBeNull();

    $approvals = ApprovalKeuangan::where('fitur', 'pembayaran')
        ->whereIn('model_id', [$pembayaran1->id, $pembayaran2->id])
        ->where('tipe_aksi', 'hapus')
        ->get();

    expect($approvals)->toHaveCount(2);
    expect($approvals->pluck('status')->unique()->toArray())->toBe(['menunggu']);
});

test('super admin 2 is blocked from bulk deleting pembayaran in DetailTagihanSiswa', function () {
    $jenis = JenisTagihan::firstOrCreate(['nama' => 'SPP'], ['default_nominal' => 350000]);
    $tagihan = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'April 2027',
        'nominal' => 350000,
        'total_dibayar' => 350000,
        'status' => 'lunas',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    $pembayaran = Pembayaran::create([
        'tagihan_id' => $tagihan->id,
        'nominal_dibayar' => 350000,
        'kelebihan_bayar' => 0,
        'metode_bayar' => 'Tunai',
        'petugas_id' => $this->financeUser->id,
        'tanggal_bayar' => now(),
        'no_resi' => 'RES-SA2-001',
    ]);

    $this->actingAs($this->superAdmin2);

    Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
        ->set('selectedPembayaranIds', [$pembayaran->id])
        ->call('bulkDeletePembayaran')
        ->assertSee('Akses Ditolak');

    expect(Pembayaran::find($pembayaran->id))->not->toBeNull();
});

test('receipt button is removed from invoice table in DetailTagihanSiswa and header displays Aksi', function () {
    $jenis = JenisTagihan::firstOrCreate(['nama' => 'SPP'], ['default_nominal' => 350000]);
    $tagihan = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'Mei 2027',
        'nominal' => 350000,
        'total_dibayar' => 350000,
        'status' => 'lunas',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    $this->actingAs($this->superAdmin);

    Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
        ->assertSee('Aksi')
        ->assertDontSee('Aksi & Resi')
        ->assertDontSeeHtml('title="Cetak Kwitansi"');
});

test('approver column is hidden in ApprovalKeuanganIndex when filterStatus is menunggu and visible when disetujui', function () {
    $this->actingAs($this->superAdmin);

    Livewire::test(ApprovalKeuanganIndex::class)
        ->assertSet('filterStatus', 'menunggu')
        ->assertDontSeeHtml('<th class="py-3 px-4 text-center">Approver</th>')
        ->set('filterStatus', 'disetujui')
        ->assertSeeHtml('<th class="py-3 px-4 text-center">Approver</th>');
});

test('receipt button is removed from payment history table in DetailTagihanSiswa', function () {
    $jenis = JenisTagihan::firstOrCreate(['nama' => 'SPP'], ['default_nominal' => 350000]);
    $tagihan = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'Juni 2027',
        'nominal' => 350000,
        'total_dibayar' => 350000,
        'status' => 'lunas',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    $pembayaran = Pembayaran::create([
        'tagihan_id' => $tagihan->id,
        'nominal_dibayar' => 350000,
        'kelebihan_bayar' => 0,
        'metode_bayar' => 'Tunai',
        'petugas_id' => $this->superAdmin->id,
        'tanggal_bayar' => now(),
        'no_resi' => 'RES-TEST-NO-PRINT',
    ]);

    $this->actingAs($this->superAdmin);

    Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
        ->assertSee('RES-TEST-NO-PRINT')
        ->assertDontSeeHtml('title="Cetak Kuitansi Resi"');
});

test('deleting pembayaran updates cashflow and dashboard without discrepancies', function () {
    $jenis = JenisTagihan::firstOrCreate(['nama' => 'SPP'], ['default_nominal' => 350000]);
    $tagihan = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'Juli 2027',
        'nominal' => 350000,
        'total_dibayar' => 350000,
        'status' => 'lunas',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    $pembayaran = Pembayaran::create([
        'tagihan_id' => $tagihan->id,
        'nominal_dibayar' => 350000,
        'kelebihan_bayar' => 0,
        'metode_bayar' => 'Tunai',
        'petugas_id' => $this->superAdmin->id,
        'tanggal_bayar' => now(),
        'no_resi' => 'RES-SYNC-001',
    ]);

    // Initial metrics
    $initialMetrics = \App\Services\Finance\CashFlowService::calculateMetrics(['filter_periode' => 'bulan_ini']);
    expect($initialMetrics['totalTagihanSpp'])->toBeGreaterThanOrEqual(350000.0);

    $this->actingAs($this->superAdmin);

    // Delete payment via DetailTagihanSiswa
    Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
        ->call('deletePembayaran', $pembayaran->id);

    // Verify cashflow service after deletion
    $afterMetrics = \App\Services\Finance\CashFlowService::calculateMetrics(['filter_periode' => 'bulan_ini']);
    expect($afterMetrics['totalTagihanSpp'])->toBe($initialMetrics['totalTagihanSpp'] - 350000.0);

    // Verify dashboard reflects exact same income as CashFlowService
    $dashboard = Livewire::test(Dashboard::class);
    expect($dashboard->get('incomeThisMonth'))->toBe((float)$afterMetrics['totalInflow']);
    expect($dashboard->get('expenseThisMonth'))->toBe((float)$afterMetrics['totalOutflow']);
});

test('kategori tagihan can be created with sekali untuk 1 semester option', function () {
    $this->actingAs($this->superAdmin);

    Livewire::test(ManajemenTagihan::class)
        ->set('kategori_nama', 'Biaya Ujian Semester Gasal')
        ->set('kategori_tipe', 'semester')
        ->set('kategori_nominal', 150000)
        ->set('kategori_is_blocking', true)
        ->call('saveKategori');

    $jt = JenisTagihan::where('nama', 'Biaya Ujian Semester Gasal')->first();
    expect($jt)->not->toBeNull();
    expect($jt->kategori)->toBe('semester');
    expect((float)$jt->default_nominal)->toBe(150000.0);

    // Check modal kelola kategori displays label when modal is opened
    Livewire::test(ManajemenTagihan::class)
        ->call('openKategoriModal')
        ->assertSee('Pembayaran Sekali untuk 1 Semester')
        ->assertSee('1 Semester (Sekali Bayar)');
});

test('super admin can bulk delete unpaid tagihans in DetailTagihanSiswa and skips paid ones', function () {
    $jenis = JenisTagihan::firstOrCreate(['nama' => 'SPP'], ['default_nominal' => 350000]);

    $tagihan1 = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'Januari 2028',
        'nominal' => 350000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    $tagihan2 = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'Februari 2028',
        'nominal' => 350000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    $tagihan3Paid = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'Maret 2028',
        'nominal' => 350000,
        'total_dibayar' => 200000,
        'status' => 'sebagian',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    $this->actingAs($this->superAdmin);

    Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
        ->set('selectedTagihanIds', [$tagihan1->id, $tagihan2->id, $tagihan3Paid->id])
        ->call('bulkDeleteTagihan')
        ->assertSee('Berhasil menghapus 2 data tagihan')
        ->assertSee('1 tagihan dilewati karena sudah ada pembayaran');

    expect(Tagihan::find($tagihan1->id))->toBeNull();
    expect(Tagihan::find($tagihan2->id))->toBeNull();
    expect(Tagihan::find($tagihan3Paid->id))->not->toBeNull();
});

test('finance staff submitting bulk delete tagihan in DetailTagihanSiswa creates approval requests and skips paid ones', function () {
    $jenis = JenisTagihan::firstOrCreate(['nama' => 'SPP'], ['default_nominal' => 350000]);

    $tagihan1 = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'April 2028',
        'nominal' => 350000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    $tagihan2 = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'Mei 2028',
        'nominal' => 350000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    $tagihan3Paid = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'Juni 2028',
        'nominal' => 350000,
        'total_dibayar' => 350000,
        'status' => 'lunas',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    $this->actingAs($this->financeUser);

    Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
        ->set('selectedTagihanIds', [$tagihan1->id, $tagihan2->id, $tagihan3Paid->id])
        ->call('bulkDeleteTagihan', 'Koreksi tagihan dobel tahun ajaran')
        ->assertSee('Permohonan penghapusan 2 tagihan berhasil diajukan ke Super Admin')
        ->assertSee('1 tagihan dilewati karena sudah ada pembayaran');

    // Invoices should not be deleted directly
    expect(Tagihan::find($tagihan1->id))->not->toBeNull();
    expect(Tagihan::find($tagihan2->id))->not->toBeNull();
    expect(Tagihan::find($tagihan3Paid->id))->not->toBeNull();

    // Approval requests created
    $approvals = ApprovalKeuangan::where('model_type', Tagihan::class)
        ->whereIn('model_id', [$tagihan1->id, $tagihan2->id])
        ->where('tipe_aksi', 'hapus')
        ->get();

    expect($approvals)->toHaveCount(2);
    expect($approvals->pluck('status')->unique()->toArray())->toBe(['menunggu']);

    // Paid invoice did NOT get an approval request
    $paidApproval = ApprovalKeuangan::where('model_type', Tagihan::class)
        ->where('model_id', $tagihan3Paid->id)
        ->exists();
    expect($paidApproval)->toBeFalse();
});

test('super admin 2 is blocked from bulk deleting tagihan in DetailTagihanSiswa', function () {
    $jenis = JenisTagihan::firstOrCreate(['nama' => 'SPP'], ['default_nominal' => 350000]);

    $tagihan = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenis->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'Juli 2028',
        'nominal' => 350000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    $this->actingAs($this->superAdmin2);

    Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
        ->set('selectedTagihanIds', [$tagihan->id])
        ->call('bulkDeleteTagihan')
        ->assertSee('Akses Ditolak');

    expect(Tagihan::find($tagihan->id))->not->toBeNull();
});

test('bulk selecting tagihans works with active filters in DetailTagihanSiswa', function () {
    $jenisSpp = JenisTagihan::firstOrCreate(['nama' => 'SPP Bulanan'], ['default_nominal' => 300000]);
    $jenisBuku = JenisTagihan::firstOrCreate(['nama' => 'Paket Buku Pelajaran'], ['default_nominal' => 150000]);

    $tagihanSppJuli = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenisSpp->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'Juli',
        'nominal' => 300000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    $tagihanSppAgustus = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenisSpp->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'Agustus',
        'nominal' => 300000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    $tagihanBuku = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jenisBuku->id,
        'tahun_ajaran_id' => $this->ta->id,
        'semester_id' => $this->semester->id,
        'bulan' => 'Juli',
        'nominal' => 150000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    $this->actingAs($this->superAdmin);

    // Test 1: Category tab 'spp' filter selects only SPP invoices
    $component = Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
        ->set('activeCategoryTab', 'spp')
        ->set('selectAllTagihan', true);

    $selected = $component->get('selectedTagihanIds');
    expect($selected)->toContain((string)$tagihanSppJuli->id);
    expect($selected)->toContain((string)$tagihanSppAgustus->id);
    expect($selected)->not->toContain((string)$tagihanBuku->id);

    // Test 2: Filter by month 'Agustus' selects only Agustus invoice
    $component = Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
        ->set('filterBulan', 'Agustus')
        ->set('selectAllTagihan', true);

    $selectedMonth = $component->get('selectedTagihanIds');
    expect($selectedMonth)->toContain((string)$tagihanSppAgustus->id);
    expect($selectedMonth)->not->toContain((string)$tagihanSppJuli->id);
    expect($selectedMonth)->not->toContain((string)$tagihanBuku->id);
});





