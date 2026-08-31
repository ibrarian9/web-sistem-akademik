<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Tagihan;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use App\Models\Pembayaran;
use Livewire\Livewire;
use App\Livewire\Finance\ManajemenTagihan;
use App\Livewire\Finance\DetailTagihanSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagihanZeroNominalAndPaymentDeletionTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $financeUser;
    protected Siswa $siswa;
    protected JenisTagihan $jenisSpp;
    protected TahunAjaran $tahunAjaran;

    protected function setUp(): void
    {
        parent::setUp();

        $roleAdmin = Role::firstOrCreate(['nama' => 'super_admin'], ['deskripsi' => 'Super Admin']);
        $roleFinance = Role::firstOrCreate(['nama' => 'finance'], ['deskripsi' => 'Staff Keuangan']);
        $roleMurid = Role::firstOrCreate(['nama' => 'murid'], ['deskripsi' => 'Murid']);

        $this->adminUser = User::factory()->create([
            'username' => 'admin_test_' . uniqid(),
            'role_id' => $roleAdmin->id,
            'status' => 'aktif',
        ]);
        $this->financeUser = User::factory()->create([
            'username' => 'finance_test_' . uniqid(),
            'role_id' => $roleFinance->id,
            'status' => 'aktif',
        ]);

        $this->tahunAjaran = TahunAjaran::create([
            'nama' => '2026/2027',
            'status_aktif' => true,
        ]);

        $semester = \App\Models\Semester::create([
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'semester' => 'ganjil',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2026-12-31',
            'status_aktif' => true,
        ]);

        $kelas = Kelas::create([
            'nama_kelas' => '7A',
            'jenis_kelas' => 'umum',
            'tingkat' => 7,
            'semester_id' => $semester->id,
        ]);

        $userMurid = User::factory()->create([
            'username' => 'murid_test_' . uniqid(),
            'role_id' => $roleMurid->id,
            'nama' => 'Siswa Beasiswa',
            'status' => 'aktif',
        ]);

        $this->siswa = Siswa::create([
            'user_id' => $userMurid->id,
            'nis' => '10001',
            'nisn' => '0000000001',
            'jenis_kelamin' => 'L',
            'tanggal_masuk' => '2026-07-01',
            'kelas_id' => $kelas->id,
            'status' => 'aktif',
            'saldo_deposit' => 0.00,
        ]);

        $this->jenisSpp = JenisTagihan::create([
            'nama' => 'SPP',
            'kategori' => 'rutin',
            'default_nominal' => 350000,
            'is_blocking' => true,
        ]);
    }

    public function test_tagihan_with_zero_nominal_is_automatically_lunas_on_single_create(): void
    {
        $this->actingAs($this->financeUser);

        Livewire::test(ManajemenTagihan::class)
            ->set('releaseMode', 'single')
            ->set('single_siswa_id', $this->siswa->id)
            ->set('jenis_tagihan_id', $this->jenisSpp->id)
            ->set('bulan', 'Juli')
            ->set('nominal', 0.00)
            ->set('jatuh_tempo', '2026-07-10')
            ->call('createSingleTagihan')
            ->assertHasNoErrors();

        $tagihan = Tagihan::where('siswa_id', $this->siswa->id)->where('bulan', 'Juli')->first();
        $this->assertNotNull($tagihan);
        $this->assertEquals(0, floatval($tagihan->nominal));
        $this->assertEquals('lunas', $tagihan->status);
    }

    public function test_tagihan_with_zero_nominal_is_automatically_lunas_on_bulk_create(): void
    {
        $this->actingAs($this->financeUser);

        Livewire::test(ManajemenTagihan::class)
            ->set('releaseMode', 'bulk')
            ->set('bulkTarget', 'all')
            ->set('jenis_tagihan_id', $this->jenisSpp->id)
            ->set('bulan', 'Agustus')
            ->set('nominal', 0.00)
            ->set('jatuh_tempo', '2026-08-10')
            ->call('createBulkTagihan')
            ->assertHasNoErrors();

        $tagihan = Tagihan::where('siswa_id', $this->siswa->id)->where('bulan', 'Agustus')->first();
        $this->assertNotNull($tagihan);
        $this->assertEquals(0, floatval($tagihan->nominal));
        $this->assertEquals('lunas', $tagihan->status);
    }

    public function test_detail_tagihan_siswa_creates_zero_nominal_tagihan_as_lunas(): void
    {
        $this->actingAs($this->financeUser);

        Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
            ->set('jenis_tagihan_id', $this->jenisSpp->id)
            ->set('bulan', 'September')
            ->set('nominal', 0.00)
            ->set('jatuh_tempo', '2026-09-10')
            ->call('createTagihan')
            ->assertHasNoErrors();

        $tagihan = Tagihan::where('siswa_id', $this->siswa->id)->where('bulan', 'September')->first();
        $this->assertNotNull($tagihan);
        $this->assertEquals(0, floatval($tagihan->nominal));
        $this->assertEquals('lunas', $tagihan->status);
    }

    public function test_finance_can_delete_payment_and_tagihan_is_recalculated(): void
    {
        $this->actingAs($this->financeUser);

        // Create a normal tagihan of 300.000
        $tagihan = Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'bulan' => 'Oktober',
            'nominal' => 300000,
            'total_dibayar' => 300000,
            'status' => 'lunas',
            'jatuh_tempo' => '2026-10-10',
        ]);

        // Create payment
        $pembayaran = Pembayaran::create([
            'no_resi' => 'KW-20261010-0001',
            'tagihan_id' => $tagihan->id,
            'tanggal_bayar' => '2026-10-05',
            'nominal_dibayar' => 300000,
            'kelebihan_bayar' => 0,
            'metode_bayar' => 'Tunai',
            'petugas_id' => $this->financeUser->id,
        ]);

        $this->assertDatabaseHas('pembayaran', ['id' => $pembayaran->id]);

        // Finance deletes the payment
        Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
            ->call('deletePembayaran', $pembayaran->id)
            ->assertHasNoErrors();

        // Payment record should be deleted
        $this->assertSoftDeleted('pembayaran', ['id' => $pembayaran->id]);

        // Tagihan status and total_dibayar should be updated back to belum_bayar
        $tagihan->refresh();
        $this->assertEquals(0, floatval($tagihan->total_dibayar));
        $this->assertEquals('belum_bayar', $tagihan->status);
    }

    public function test_deleting_deposit_payment_refunds_student_deposit(): void
    {
        $this->actingAs($this->financeUser);

        // Student initial deposit
        $this->siswa->update(['saldo_deposit' => 50000]);

        $tagihan = Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'bulan' => 'November',
            'nominal' => 200000,
            'total_dibayar' => 200000,
            'status' => 'lunas',
            'jatuh_tempo' => '2026-11-10',
        ]);

        $pembayaran = Pembayaran::create([
            'no_resi' => 'KW-20261110-0002',
            'tagihan_id' => $tagihan->id,
            'tanggal_bayar' => '2026-11-05',
            'nominal_dibayar' => 200000,
            'kelebihan_bayar' => 0,
            'metode_bayar' => 'Deposit',
            'petugas_id' => $this->financeUser->id,
        ]);

        // Finance deletes payment
        Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
            ->call('deletePembayaran', $pembayaran->id)
            ->assertHasNoErrors();

        // Saldo deposit should be refunded (50.000 + 200.000 = 250.000)
        $this->siswa->refresh();
        $this->assertEquals(250000, floatval($this->siswa->saldo_deposit));

        // Tagihan should be belum_bayar
        $tagihan->refresh();
        $this->assertEquals(0, floatval($tagihan->total_dibayar));
        $this->assertEquals('belum_bayar', $tagihan->status);
    }

    public function test_finance_can_delete_unpaid_tagihan_on_detail_page_and_it_is_logged_in_audit(): void
    {
        $this->actingAs($this->financeUser);

        $tagihan = Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'bulan' => 'Desember',
            'nominal' => 350000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => '2026-12-10',
        ]);

        $tagihanId = $tagihan->id;

        // Finance deletes the unpaid tagihan
        Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
            ->call('deleteTagihan', $tagihanId)
            ->assertHasNoErrors()
            ->assertSee('Data tagihan berhasil dihapus.');

        // Verify soft-deleted
        $this->assertSoftDeleted('tagihan', ['id' => $tagihanId]);

        // Verify Audit Log entry exists
        $auditLog = \Illuminate\Support\Facades\DB::table('activity_log')
            ->where('subject_type', Tagihan::class)
            ->where('subject_id', $tagihanId)
            ->where('event', 'deleted')
            ->first();

        $this->assertNotNull($auditLog, 'Audit log for deleted tagihan must be recorded.');
        $this->assertEquals($this->financeUser->id, $auditLog->causer_id);
        $this->assertEquals('keuangan', $auditLog->log_name);
    }

    public function test_finance_can_delete_tagihan_with_existing_payment_and_automatically_rolls_back_payments(): void
    {
        $this->actingAs($this->financeUser);

        $tagihan = Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'bulan' => 'Januari',
            'nominal' => 350000,
            'total_dibayar' => 150000,
            'status' => 'sebagian',
            'jatuh_tempo' => '2027-01-10',
        ]);

        $pembayaran = Pembayaran::create([
            'no_resi' => 'KW-JAN-001',
            'tagihan_id' => $tagihan->id,
            'tanggal_bayar' => '2027-01-05',
            'nominal_dibayar' => 150000,
            'kelebihan_bayar' => 0,
            'metode_bayar' => 'Tunai',
            'petugas_id' => $this->financeUser->id,
        ]);

        Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
            ->call('deleteTagihan', $tagihan->id)
            ->assertHasNoErrors()
            ->assertSee('Data tagihan berhasil dihapus.');

        $this->assertSoftDeleted('tagihan', ['id' => $tagihan->id]);
        $this->assertSoftDeleted('pembayaran', ['id' => $pembayaran->id]);
    }

    public function test_payment_history_filters_and_search_in_detail_tagihan_siswa(): void
    {
        $this->actingAs($this->financeUser);

        $tagihan1 = Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'bulan' => 'Februari',
            'nominal' => 300000,
            'total_dibayar' => 300000,
            'status' => 'lunas',
            'jatuh_tempo' => '2027-02-10',
        ]);

        $tagihan2 = Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'bulan' => 'Maret',
            'nominal' => 300000,
            'total_dibayar' => 300000,
            'status' => 'lunas',
            'jatuh_tempo' => '2027-03-10',
        ]);

        $p1 = Pembayaran::create([
            'no_resi' => 'KW-FEB-001',
            'tagihan_id' => $tagihan1->id,
            'tanggal_bayar' => '2027-02-05',
            'nominal_dibayar' => 300000,
            'kelebihan_bayar' => 0,
            'metode_bayar' => 'Tunai',
            'petugas_id' => $this->financeUser->id,
        ]);

        $p2 = Pembayaran::create([
            'no_resi' => 'KW-MAR-002',
            'tagihan_id' => $tagihan2->id,
            'tanggal_bayar' => '2027-03-05',
            'nominal_dibayar' => 300000,
            'kelebihan_bayar' => 0,
            'metode_bayar' => 'Transfer Bank',
            'petugas_id' => $this->financeUser->id,
        ]);

        // 1. Filter by search resi
        Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
            ->assertSee('KW-FEB-001')
            ->assertSee('KW-MAR-002')
            ->set('searchBayar', 'KW-FEB')
            ->assertSee('KW-FEB-001')
            ->assertDontSee('KW-MAR-002');

        // 2. Filter by month
        Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
            ->set('filterBayarBulan', 'Maret')
            ->assertSee('KW-MAR-002')
            ->assertDontSee('KW-FEB-001');

        // 3. Filter by payment method
        Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
            ->set('filterBayarMetode', 'Transfer Bank')
            ->assertSee('KW-MAR-002')
            ->assertDontSee('KW-FEB-001')
            ->call('resetBayarFilters')
            ->assertSet('filterBayarMetode', '')
            ->assertSee('KW-FEB-001')
            ->assertSee('KW-MAR-002');
    }
}
