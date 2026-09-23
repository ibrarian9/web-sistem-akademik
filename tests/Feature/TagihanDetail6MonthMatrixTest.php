<?php

namespace Tests\Feature;

use App\Livewire\Finance\DetailTagihanSiswa;
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

class TagihanDetail6MonthMatrixTest extends TestCase
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
            'nama' => 'Santri Al-Falah',
            'username' => 'santri_alfalah',
            'password' => bcrypt('password123'),
            'role_id' => $roleMurid->id,
            'status' => 'aktif',
        ]);

        $this->siswa = Siswa::create([
            'user_id' => $userMurid->id,
            'nis' => '5001',
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
            'default_nominal' => 1200000.00,
        ]);

        $this->jenisSeragam = JenisTagihan::create([
            'nama' => 'Uang Seragam',
            'kategori' => 'one_time',
            'default_nominal' => 600000.00,
        ]);
    }

    public function test_detail_tagihan_renders_6_month_spp_columns_and_non_spp_on_sumbu_x(): void
    {
        $this->actingAs($this->financeUser);

        // SPP Juli: Lunas
        Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Juli',
            'nominal' => 350000.00,
            'total_dibayar' => 350000.00,
            'status' => 'lunas',
            'jatuh_tempo' => date('Y-07-10'),
        ]);

        // SPP Agustus: Belum Bayar
        Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Agustus',
            'nominal' => 350000.00,
            'total_dibayar' => 0.00,
            'status' => 'belum_bayar',
            'jatuh_tempo' => date('Y-08-10'),
        ]);

        // Uang Pembangunan: Lunas in Agustus
        Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'jenis_tagihan_id' => $this->jenisPembangunan->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Agustus',
            'nominal' => 1200000.00,
            'total_dibayar' => 1200000.00,
            'status' => 'lunas',
            'jatuh_tempo' => date('Y-08-10'),
        ]);

        // Uang Seragam: Dicicil
        Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'jenis_tagihan_id' => $this->jenisSeragam->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Tahunan',
            'nominal' => 600000.00,
            'total_dibayar' => 200000.00,
            'status' => 'sebagian',
            'jatuh_tempo' => date('Y-08-10'),
        ]);

        $test = Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id]);

        // 1. Verify Sumbu X headers for Ganjil (Default 6 Months)
        $test->assertSee('SPP Rutin (Terbatas 6 Bulan)')
            ->assertSee('SPP Juli')
            ->assertSee('SPP Agustus')
            ->assertSee('SPP September')
            ->assertSee('SPP Oktober')
            ->assertSee('SPP November')
            ->assertSee('SPP Desember')
            ->assertSee('Kategori Tagihan Lainnya (Non-SPP)')
            ->assertSee('Uang Pembangunan')
            ->assertSee('Uang Seragam');

        // 2. Verify Metric Rows on Sumbu Y
        $test->assertSee('Status dan Aksi Kasir')
            ->assertSee('Kewajiban Tagihan (Nominal)')
            ->assertSee('Total Telah Dibayar')
            ->assertSee('Sisa Tunggakan (Piutang)')
            ->assertSee('Histori Pembayaran Terakhir');

        // 3. Verify Totals
        $test->assertSee('Total SPP')
            ->assertSee('Total Non-SPP')
            ->assertSee('Grand Total');

        // 4. Verify Period Switcher to Genap
        $test->call('setSppPeriode', 'genap')
            ->assertSet('sppPeriode', 'genap')
            ->assertSee('SPP Januari')
            ->assertSee('SPP Februari')
            ->assertSee('SPP Maret')
            ->assertSee('SPP April')
            ->assertSee('SPP Mei')
            ->assertSee('SPP Juni');

        // 5. Verify Period Switcher to 6 Bln Berjalan
        $test->call('setSppPeriode', 'terakhir')
            ->assertSet('sppPeriode', 'terakhir');
    }
}
