<?php

namespace Tests\Feature;

use App\Livewire\Finance\DetailTagihanSiswa;
use App\Livewire\Finance\ManajemenTagihan;
use App\Livewire\Finance\OverviewPembayaran;
use App\Models\Kelas;
use App\Models\Role;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TagihanOneTimePaymentMatrixTest extends TestCase
{
    use RefreshDatabase;

    protected User $financeUser;
    protected TahunAjaran $tahunAjaran;
    protected Semester $semester;
    protected Kelas $kelas;
    protected Siswa $siswa;
    protected JenisTagihan $jenisSpp;
    protected JenisTagihan $jenisPembangunan;
    protected JenisTagihan $jenisSeragam;

    protected function setUp(): void
    {
        parent::setUp();

        $roleFinance = Role::create(['nama' => 'finance', 'deskripsi' => 'Finance Staff']);
        $roleMurid = Role::create(['nama' => 'murid', 'deskripsi' => 'Siswa']);

        $this->financeUser = User::create([
            'nama' => 'Staff Keuangan',
            'username' => 'finance_staff',
            'email' => 'keuangan@siakad.sch.id',
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
            'nama_kelas' => '7B',
            'tingkat' => 7,
            'semester_id' => $this->semester->id,
        ]);

        $userMurid = User::create([
            'nama' => 'Ahmad Santri',
            'username' => 'ahmad_santri',
            'password' => bcrypt('password123'),
            'role_id' => $roleMurid->id,
            'status' => 'aktif',
        ]);

        $this->siswa = Siswa::create([
            'user_id' => $userMurid->id,
            'nis' => '2001',
            'kelas_id' => $this->kelas->id,
            'status' => 'aktif',
            'tanggal_masuk' => date('Y-m-d'),
        ]);

        $this->jenisSpp = JenisTagihan::create([
            'nama' => 'SPP Bulanan',
            'kategori' => 'rutin',
            'default_nominal' => 350000.00,
        ]);

        $this->jenisPembangunan = JenisTagihan::create([
            'nama' => 'Uang Pembangunan',
            'kategori' => 'one_time',
            'default_nominal' => 1500000.00,
        ]);

        $this->jenisSeragam = JenisTagihan::create([
            'nama' => 'Uang Seragam',
            'kategori' => 'one_time',
            'default_nominal' => 500000.00,
        ]);
    }

    public function test_detail_tagihan_siswa_counts_one_time_payment_paid_in_august_as_lunas_in_september(): void
    {
        $this->actingAs($this->financeUser);

        // Uang Pembangunan billed and paid LUNAS in Agustus
        $tagihanPembangunan = Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'jenis_tagihan_id' => $this->jenisPembangunan->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Agustus',
            'nominal' => 1500000.00,
            'total_dibayar' => 1500000.00,
            'status' => 'lunas',
            'jatuh_tempo' => date('Y-08-10'),
        ]);

        // SPP billed for September (Belum Bayar)
        Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'September',
            'nominal' => 350000.00,
            'total_dibayar' => 0.00,
            'status' => 'belum_bayar',
            'jatuh_tempo' => date('Y-09-10'),
        ]);

        $component = Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id]);

        $matrixData = $component->viewData('detailMatrixData');
        $this->assertNotNull($matrixData);

        // Row Agustus: Uang Pembangunan is original primary bill
        $agustusBills = $matrixData['months_rows']['Agustus']['bills'];
        $pembangunanAgustus = $agustusBills[$this->jenisPembangunan->id];
        $this->assertTrue($pembangunanAgustus['has_tagihan']);
        $this->assertEquals('lunas', $pembangunanAgustus['status']);
        $this->assertEquals(1500000.0, $pembangunanAgustus['nominal']);
        $this->assertFalse($pembangunanAgustus['is_one_time_fulfilled']);

        // Row September: Uang Pembangunan MUST be counted as LUNAS
        $septemberBills = $matrixData['months_rows']['September']['bills'];
        $pembangunanSeptember = $septemberBills[$this->jenisPembangunan->id];
        $this->assertTrue($pembangunanSeptember['has_tagihan']);
        $this->assertEquals('lunas', $pembangunanSeptember['status']);
        $this->assertTrue($pembangunanSeptember['is_one_time_fulfilled']);
        $this->assertEquals('Agustus', $pembangunanSeptember['original_bulan']);
        $this->assertEquals(1500000.0, $pembangunanSeptember['original_nominal']);
        // Nominal in September must be 0 to prevent double billing in September
        $this->assertEquals(0.0, $pembangunanSeptember['nominal']);
        $this->assertEquals(0.0, $pembangunanSeptember['sisa']);

        // Check September row total: only SPP (350k)
        $septemberRow = $matrixData['months_rows']['September'];
        $this->assertEquals(350000.0, $septemberRow['total_nominal']);
        $this->assertEquals(350000.0, $septemberRow['sisa_tunggakan']);

        // Check grand total nominal: exactly 1.5M + 350k = 1.85M (NOT duplicated to 3.35M)
        $this->assertEquals(1850000.0, $matrixData['grand_nominal']);
        $this->assertEquals(1500000.0, $matrixData['grand_dibayar']);
        $this->assertEquals(350000.0, $matrixData['grand_tunggakan']);

        // HTML rendering: verify "1x Bayar • Agustus" badge is shown
        $component->assertSee('1x Bayar • Agustus');
        $component->assertDontSeeHtml("quickCreateTagihanForMonth({$this->jenisPembangunan->id}, 'September')");
    }

    public function test_manajemen_tagihan_matrix_also_recognizes_one_time_fulfilled_in_september(): void
    {
        $this->actingAs($this->financeUser);

        // Uang Seragam billed and paid in Agustus
        Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'jenis_tagihan_id' => $this->jenisSeragam->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Agustus',
            'nominal' => 500000.00,
            'total_dibayar' => 500000.00,
            'status' => 'lunas',
            'jatuh_tempo' => date('Y-08-10'),
        ]);

        $component = Livewire::test(ManajemenTagihan::class)
            ->set('activeViewTab', 'matriks')
            ->set('matrixMode', 'siswa')
            ->set('matrixSiswaId', $this->siswa->id);

        $matrixData = $component->viewData('matrixSiswaData');
        $this->assertNotNull($matrixData);

        // In September row, Seragam should be Lunas
        $seragamSeptember = $matrixData['months_rows']['September']['bills'][$this->jenisSeragam->id];
        $this->assertTrue($seragamSeptember['has_tagihan']);
        $this->assertEquals('lunas', $seragamSeptember['status']);
        $this->assertTrue($seragamSeptember['is_one_time_fulfilled']);
        $this->assertEquals('Agustus', $seragamSeptember['original_bulan']);

        // Ensure badge text rendered in HTML
        $component->assertSee('1x Bayar • Agustus');
    }

    public function test_overview_pembayaran_september_view_recognizes_one_time_paid_in_august_as_lunas(): void
    {
        $this->actingAs($this->financeUser);

        // Uang Pembangunan paid in Agustus
        Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'jenis_tagihan_id' => $this->jenisPembangunan->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Agustus',
            'nominal' => 1500000.00,
            'total_dibayar' => 1500000.00,
            'status' => 'lunas',
            'jatuh_tempo' => date('Y-08-10'),
        ]);

        $component = Livewire::test(OverviewPembayaran::class)
            ->set('activeViewTab', 'lengkap')
            ->set('selectedBulan', 'September');

        $siswasLengkap = $component->viewData('siswasLengkap');
        $this->assertNotEmpty($siswasLengkap);

        $firstSiswa = collect($siswasLengkap->items())->firstWhere('id', $this->siswa->id);
        $this->assertNotNull($firstSiswa);

        $pembangunanBill = $firstSiswa['bills_by_jenis'][$this->jenisPembangunan->id];
        $this->assertTrue($pembangunanBill['has_tagihan']);
        $this->assertEquals('lunas', $pembangunanBill['status']);
        $this->assertTrue($pembangunanBill['is_one_time_fulfilled']);
        $this->assertEquals('Agustus', $pembangunanBill['original_bulan']);

        // Verify HTML output displays badge
        $component->assertSee('1x • Agustus');
    }
}
