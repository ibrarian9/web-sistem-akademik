<?php

namespace Tests\Feature;

use App\Livewire\Finance\DetailTagihanSiswa;
use App\Livewire\Finance\ManajemenTagihan;
use App\Models\Kelas;
use App\Models\Role;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManajemenTagihanMatrixTest extends TestCase
{
    use RefreshDatabase;

    protected User $financeUser;
    protected TahunAjaran $tahunAjaran;
    protected Semester $semester;
    protected Kelas $kelas;
    protected Siswa $siswa1;
    protected Siswa $siswa2;
    protected JenisTagihan $jenisSpp;
    protected JenisTagihan $jenisGedung;
    protected JenisTagihan $jenisSeragam;

    protected function setUp(): void
    {
        parent::setUp();

        $roleFinance = Role::create(['nama' => 'finance', 'deskripsi' => 'Finance Staff']);
        $roleMurid = Role::create(['nama' => 'murid', 'deskripsi' => 'Siswa/Murid']);

        $this->financeUser = User::create([
            'nama' => 'Staff Finance SIAKAD',
            'username' => 'staff_finance',
            'email' => 'finance@siakad.sch.id',
            'password' => bcrypt('password123'),
            'role_id' => $roleFinance->id,
            'status' => 'aktif',
        ]);

        $this->tahunAjaran = TahunAjaran::create([
            'nama' => '2026/2027',
            'status_aktif' => true,
        ]);

        $this->semester = Semester::create([
            'tahun_ajaran_id' => $this->tahunAjaran->id,
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

        $userSiswa1 = User::create([
            'nama' => 'Fulan bin Fulan',
            'username' => 'fulan_7a',
            'password' => bcrypt('password123'),
            'role_id' => $roleMurid->id,
            'status' => 'aktif',
        ]);

        $this->siswa1 = Siswa::create([
            'user_id' => $userSiswa1->id,
            'nis' => '1001',
            'kelas_id' => $this->kelas->id,
            'status' => 'aktif',
            'tanggal_masuk' => date('Y-m-d'),
        ]);

        $userSiswa2 = User::create([
            'nama' => 'Aisyah Putri',
            'username' => 'aisyah_7a',
            'password' => bcrypt('password123'),
            'role_id' => $roleMurid->id,
            'status' => 'aktif',
        ]);

        $this->siswa2 = Siswa::create([
            'user_id' => $userSiswa2->id,
            'nis' => '1002',
            'kelas_id' => $this->kelas->id,
            'status' => 'aktif',
            'tanggal_masuk' => date('Y-m-d'),
        ]);

        $this->jenisSpp = JenisTagihan::create([
            'nama' => 'SPP Bulanan',
            'kategori' => 'rutin',
            'default_nominal' => 300000.00,
        ]);

        $this->jenisGedung = JenisTagihan::create([
            'nama' => 'Uang Gedung & Sarana',
            'kategori' => 'tahunan',
            'default_nominal' => 1500000.00,
        ]);

        $this->jenisSeragam = JenisTagihan::create([
            'nama' => 'Paket Seragam Sekolah',
            'kategori' => 'one_time',
            'default_nominal' => 750000.00,
        ]);

        // Setup bills for Siswa 1:
        // Juli: SPP (Lunas)
        Tagihan::create([
            'siswa_id' => $this->siswa1->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Juli',
            'nominal' => 300000.00,
            'total_dibayar' => 300000.00,
            'status' => 'lunas',
            'jatuh_tempo' => date('Y-07-10'),
        ]);

        // Juli: Uang Gedung (Dicicil)
        Tagihan::create([
            'siswa_id' => $this->siswa1->id,
            'jenis_tagihan_id' => $this->jenisGedung->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Juli',
            'nominal' => 1500000.00,
            'total_dibayar' => 500000.00,
            'status' => 'sebagian',
            'jatuh_tempo' => date('Y-07-10'),
        ]);

        // Agustus: SPP (Belum Bayar)
        Tagihan::create([
            'siswa_id' => $this->siswa1->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Agustus',
            'nominal' => 300000.00,
            'total_dibayar' => 0.00,
            'status' => 'belum_bayar',
            'jatuh_tempo' => date('Y-08-10'),
        ]);

        // Tahunan: Seragam (Belum Bayar)
        Tagihan::create([
            'siswa_id' => $this->siswa1->id,
            'jenis_tagihan_id' => $this->jenisSeragam->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Tahunan',
            'nominal' => 750000.00,
            'total_dibayar' => 0.00,
            'status' => 'belum_bayar',
            'jatuh_tempo' => date('Y-09-10'),
        ]);
    }

    public function test_manajemen_tagihan_renders_default_tab_and_can_switch_to_matrix_tab(): void
    {
        $this->actingAs($this->financeUser);

        Livewire::test(ManajemenTagihan::class)
            ->assertSet('activeViewTab', 'default')
            ->assertSee('Tabel Default (Daftar Siswa)')
            ->assertSee('Tabel Matriks Tagihan')
            ->call('setViewTab', 'matriks')
            ->assertSet('activeViewTab', 'matriks')
            ->assertSee('Matriks Tagihan dan Pembayaran Siswa')
            ->assertSee('SPP Bulanan')
            ->assertSee('Uang Gedung & Sarana')
            ->assertSee('Paket Seragam Sekolah')
            ->assertSee('Juli')
            ->assertSee('Agustus')
            ->assertSee('Tahunan')
            ->assertSee('Fulan bin Fulan');
    }

    public function test_manajemen_tagihan_matrix_computes_accurate_amounts_and_statuses(): void
    {
        $this->actingAs($this->financeUser);

        $test = Livewire::test(ManajemenTagihan::class)
            ->set('activeViewTab', 'matriks')
            ->set('matrixSiswaId', $this->siswa1->id);

        $matrixData = $test->viewData('matrixSiswaData');

        $this->assertNotNull($matrixData);
        $this->assertEquals($this->siswa1->id, $matrixData['siswa']->id);

        // Juli: 300k SPP + 1.5M Gedung = 1.8M nominal, 800k dibayar, 1.0M tunggakan
        $juliRow = $matrixData['months_rows']['Juli'];
        $this->assertEquals(1800000.0, $juliRow['total_nominal']);
        $this->assertEquals(800000.0, $juliRow['total_dibayar']);
        $this->assertEquals(1000000.0, $juliRow['sisa_tunggakan']);
        $this->assertEquals('Ada Tunggakan', $juliRow['status']);

        // Check cells for Juli
        $sppCell = $juliRow['bills'][$this->jenisSpp->id];
        $this->assertTrue($sppCell['has_tagihan']);
        $this->assertEquals('lunas', $sppCell['status']);
        $this->assertEquals(300000.0, $sppCell['nominal']);

        $gedungCell = $juliRow['bills'][$this->jenisGedung->id];
        $this->assertTrue($gedungCell['has_tagihan']);
        $this->assertEquals('sebagian', $gedungCell['status']);
        $this->assertEquals(1000000.0, $gedungCell['sisa']);

        // Agustus: 300k SPP, 0 dibayar
        $agustusRow = $matrixData['months_rows']['Agustus'];
        $this->assertEquals(300000.0, $agustusRow['total_nominal']);
        $this->assertEquals(0.0, $agustusRow['total_dibayar']);
        $this->assertEquals(300000.0, $agustusRow['sisa_tunggakan']);

        // Tahunan: 750k Seragam
        $tahunanRow = $matrixData['months_rows']['Tahunan'];
        $this->assertEquals(750000.0, $tahunanRow['total_nominal']);

        // Grand Totals: 1.8M + 300k + 750k = 2.85M
        $this->assertEquals(2850000.0, $matrixData['grand_nominal']);
        $this->assertEquals(800000.0, $matrixData['grand_dibayar']);
        $this->assertEquals(2050000.0, $matrixData['grand_tunggakan']);

        // Footer per jenis
        $this->assertEquals(600000.0, $matrixData['footer_per_jenis'][$this->jenisSpp->id]['nominal']);
        $this->assertEquals(300000.0, $matrixData['footer_per_jenis'][$this->jenisSpp->id]['dibayar']);
        $this->assertEquals(300000.0, $matrixData['footer_per_jenis'][$this->jenisSpp->id]['sisa']);

        $this->assertEquals(1500000.0, $matrixData['footer_per_jenis'][$this->jenisGedung->id]['nominal']);
        $this->assertEquals(1000000.0, $matrixData['footer_per_jenis'][$this->jenisGedung->id]['sisa']);
    }

    public function test_manajemen_tagihan_matrix_global_recap_mode(): void
    {
        $this->actingAs($this->financeUser);

        // Also add a bill for Siswa 2 in Juli SPP
        Tagihan::create([
            'siswa_id' => $this->siswa2->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Juli',
            'nominal' => 300000.00,
            'total_dibayar' => 300000.00,
            'status' => 'lunas',
            'jatuh_tempo' => date('Y-07-10'),
        ]);

        $test = Livewire::test(ManajemenTagihan::class)
            ->set('activeViewTab', 'matriks')
            ->call('setMatrixMode', 'rekap')
            ->assertSet('matrixMode', 'rekap')
            ->assertSee('Total Tagihan Seluruh Siswa')
            ->assertSee('Total Realisasi Terkumpul');

        $matrixData = $test->viewData('matrixSiswaData');
        $this->assertNull($matrixData['siswa']);

        // Juli: Siswa 1 (300k SPP + 1.5M Gedung) + Siswa 2 (300k SPP) = 2.1M
        $juliRow = $matrixData['months_rows']['Juli'];
        $this->assertEquals(2100000.0, $juliRow['total_nominal']);
        $this->assertEquals(1100000.0, $juliRow['total_dibayar']);
        $this->assertEquals(1000000.0, $juliRow['sisa_tunggakan']);
    }

    public function test_detail_tagihan_siswa_renders_multi_tagihan_matrix_table(): void
    {
        $this->actingAs($this->financeUser);

        $test = Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa1->id])
            ->assertSet('matrixViewStyle', 'table')
            ->assertSee('Matriks Tagihan dan Status SPP Siswa')
            ->assertSee('Tabel Matriks Lengkap')
            ->assertSee('Kartu Ringkas SPP')
            ->assertSee('SPP Bulanan')
            ->assertSee('Uang Gedung & Sarana')
            ->assertSee('Paket Seragam Sekolah')
            ->assertSee('Juli')
            ->assertSee('Agustus')
            ->assertSee('Tahunan');

        $detailMatrixData = $test->viewData('detailMatrixData');
        $this->assertNotNull($detailMatrixData);
        $this->assertEquals(2850000.0, $detailMatrixData['grand_nominal']);
        $this->assertEquals(800000.0, $detailMatrixData['grand_dibayar']);
        $this->assertEquals(2050000.0, $detailMatrixData['grand_tunggakan']);

        // Switch to cards and back
        $test->call('setMatrixViewStyle', 'cards')
            ->assertSet('matrixViewStyle', 'cards')
            ->call('setMatrixViewStyle', 'table')
            ->assertSet('matrixViewStyle', 'table');
    }

    public function test_quick_pay_directly_from_matrix_table(): void
    {
        $this->actingAs($this->financeUser);

        // Find Agustus SPP which is belum_bayar 300k
        $tagihanAgustus = Tagihan::where('siswa_id', $this->siswa1->id)
            ->where('jenis_tagihan_id', $this->jenisSpp->id)
            ->where('bulan', 'Agustus')
            ->first();

        $this->assertNotNull($tagihanAgustus);

        Livewire::test(ManajemenTagihan::class)
            ->set('activeViewTab', 'matriks')
            ->set('matrixSiswaId', $this->siswa1->id)
            ->call('openQuickPay', $tagihanAgustus->id)
            ->assertSet('showQuickPayModal', true)
            ->assertSet('quickPayNominal', 300000.0)
            ->assertSet('quickPayMetode', 'Tunai')
            ->call('saveQuickPay')
            ->assertSet('showQuickPayModal', false)
            ->assertHasNoErrors();

        $tagihanAgustus->refresh();
        $this->assertEquals('lunas', $tagihanAgustus->status);
        $this->assertEquals(300000.0, (float) $tagihanAgustus->total_dibayar);

        $this->assertDatabaseHas('pembayaran', [
            'tagihan_id' => $tagihanAgustus->id,
            'nominal_dibayar' => 300000.0,
            'metode_bayar' => 'Tunai',
        ]);
    }

    public function test_quick_create_tagihan_directly_from_empty_matrix_cell(): void
    {
        $this->actingAs($this->financeUser);

        Livewire::test(ManajemenTagihan::class)
            ->set('activeViewTab', 'matriks')
            ->set('matrixSiswaId', $this->siswa1->id)
            ->call('quickCreateTagihanForMonth', $this->jenisSpp->id, 'September', $this->siswa1->id)
            ->assertSet('showCreateModal', true)
            ->assertSet('single_siswa_id', $this->siswa1->id)
            ->assertSet('selectedStudentName', 'Fulan bin Fulan')
            ->assertSet('jenis_tagihan_id', $this->jenisSpp->id)
            ->assertSet('bulan', 'September')
            ->assertSet('nominal', 300000.0)
            ->assertSet('releaseMode', 'single');
    }

    public function test_matrix_table_renders_direct_cashier_payment_links(): void
    {
        $this->actingAs($this->financeUser);

        $tagihanAgustus = Tagihan::where('siswa_id', $this->siswa1->id)
            ->where('jenis_tagihan_id', $this->jenisSpp->id)
            ->where('bulan', 'Agustus')
            ->first();

        $expectedPaymentUrl = route('finance.input-pembayaran', [
            'siswa_id' => $this->siswa1->id,
            'tagihan_id' => $tagihanAgustus->id,
        ]);

        Livewire::test(ManajemenTagihan::class)
            ->set('activeViewTab', 'matriks')
            ->set('matrixMode', 'siswa')
            ->set('matrixSiswaId', $this->siswa1->id)
            ->assertSeeHtml(e($expectedPaymentUrl))
            ->assertSee('Bayar Tagihan');
    }
}
