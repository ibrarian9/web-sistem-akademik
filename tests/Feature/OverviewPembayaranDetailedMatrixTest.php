<?php

namespace Tests\Feature;

use App\Livewire\Finance\OverviewPembayaran;
use App\Models\Kelas;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OverviewPembayaranDetailedMatrixTest extends TestCase
{
    use RefreshDatabase;

    protected User $financeUser;
    protected TahunAjaran $tahunAjaran;
    protected JenisTagihan $jenisSpp;
    protected JenisTagihan $jenisGedung;
    protected Kelas $kelas7A;
    protected Kelas $kelas7B;
    protected Siswa $siswa1;
    protected Siswa $siswa2;

    protected function setUp(): void
    {
        parent::setUp();

        $roleFinance = Role::create(['nama' => 'finance', 'deskripsi' => 'Finance Staff']);
        $roleSiswa = Role::create(['nama' => 'siswa', 'deskripsi' => 'Siswa']);

        $this->financeUser = User::create([
            'nama' => 'Staff Keuangan',
            'username' => 'finance_test',
            'email' => 'finance@test.sch.id',
            'password' => bcrypt('password'),
            'role_id' => $roleFinance->id,
        ]);

        $this->tahunAjaran = TahunAjaran::create([
            'nama' => '2026/2027',
            'status_aktif' => true,
        ]);

        $semester = \App\Models\Semester::create([
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'semester' => 'ganjil',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+6 months')),
            'status_aktif' => true,
        ]);

        $this->jenisSpp = JenisTagihan::create([
            'nama' => 'SPP Bulanan',
            'kategori' => 'rutin',
            'default_nominal' => 350000,
            'is_blocking' => false,
        ]);

        $this->jenisGedung = JenisTagihan::create([
            'nama' => 'Uang Gedung',
            'kategori' => 'tahunan',
            'default_nominal' => 1500000,
            'is_blocking' => false,
        ]);

        $this->kelas7A = Kelas::create([
            'nama_kelas' => '7A',
            'tingkat' => 7,
            'semester_id' => $semester->id,
        ]);

        $this->kelas7B = Kelas::create([
            'nama_kelas' => '7B',
            'tingkat' => 7,
            'semester_id' => $semester->id,
        ]);

        $userSiswa1 = User::create([
            'nama' => 'Ahmad Santri',
            'username' => 'ahmad123',
            'email' => 'ahmad@test.sch.id',
            'password' => bcrypt('password'),
            'role_id' => $roleSiswa->id,
        ]);

        $this->siswa1 = Siswa::create([
            'user_id' => $userSiswa1->id,
            'nis' => '1001',
            'kelas_id' => $this->kelas7A->id,
            'status' => 'aktif',
            'tanggal_masuk' => '2026-07-01',
        ]);

        $userSiswa2 = User::create([
            'nama' => 'Budi Santoso',
            'username' => 'budi123',
            'email' => 'budi@test.sch.id',
            'password' => bcrypt('password'),
            'role_id' => $roleSiswa->id,
        ]);

        $this->siswa2 = Siswa::create([
            'user_id' => $userSiswa2->id,
            'nis' => '1002',
            'kelas_id' => $this->kelas7B->id,
            'status' => 'aktif',
            'tanggal_masuk' => '2026-07-01',
        ]);
    }

    public function test_default_view_renders_correctly_with_student_summary(): void
    {
        $this->actingAs($this->financeUser);

        // Siswa 1 pays Juli SPP in full
        Tagihan::create([
            'siswa_id' => $this->siswa1->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Juli',
            'nominal' => 350000,
            'total_dibayar' => 350000,
            'status' => 'lunas',
            'jatuh_tempo' => Carbon::now()->subMonth()->toDateString(),
        ]);

        // Siswa 2 unpaid for Juli SPP
        Tagihan::create([
            'siswa_id' => $this->siswa2->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Juli',
            'nominal' => 350000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => Carbon::now()->subMonth()->toDateString(),
        ]);

        Livewire::test(OverviewPembayaran::class)
            ->assertStatus(200)
            ->assertSet('activeViewTab', 'spp_matrix')
            ->assertSee('Buka Kasir Pembayaran')
            ->assertSee('Matriks Tagihan (SPP 6 Bulan dan Kategori Tunggakan)')
            ->assertSee('Matriks Bulanan per Kategori')
            ->assertSee('Daftar Ringkas Siswa')
            ->call('setViewTab', 'default')
            ->assertSet('activeViewTab', 'default')
            ->assertSee('Ahmad Santri')
            ->assertSee('Budi Santoso')
            ->assertSee('Nihil (Lunas)')
            ->assertSee('Ada Tunggakan');
    }

    public function test_can_switch_to_tabel_lengkap_and_see_monthly_grouped_matrix(): void
    {
        $this->actingAs($this->financeUser);

        // Siswa 1 pays Juli SPP
        Tagihan::create([
            'siswa_id' => $this->siswa1->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Juli',
            'nominal' => 350000,
            'total_dibayar' => 350000,
            'status' => 'lunas',
            'jatuh_tempo' => '2026-07-10',
        ]);

        // Siswa 2 unpaid for Juli SPP
        Tagihan::create([
            'siswa_id' => $this->siswa2->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Juli',
            'nominal' => 350000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => '2026-07-10',
        ]);

        Livewire::test(OverviewPembayaran::class)
            ->call('setViewTab', 'lengkap')
            ->call('selectBulan', 'Juli')
            ->assertSet('activeViewTab', 'lengkap')
            ->assertSet('selectedBulan', 'Juli')
            ->assertSee('Pilih Bulan Tahun Ajaran')
            ->assertSee('SPP Bulanan')
            ->assertSee('Uang Gedung')
            ->assertSee('Ahmad Santri')
            ->assertSee('Budi Santoso');
    }

    public function test_filter_by_month_and_payment_status(): void
    {
        $this->actingAs($this->financeUser);

        // Siswa 1: Lunas Agustus
        Tagihan::create([
            'siswa_id' => $this->siswa1->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Agustus',
            'nominal' => 350000,
            'total_dibayar' => 350000,
            'status' => 'lunas',
            'jatuh_tempo' => '2026-08-10',
        ]);

        // Siswa 2: Belum Bayar Agustus
        Tagihan::create([
            'siswa_id' => $this->siswa2->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Agustus',
            'nominal' => 350000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => '2026-08-10',
        ]);

        // 1. Select Agustus with all statuses
        $component = Livewire::test(OverviewPembayaran::class)
            ->set('activeViewTab', 'lengkap')
            ->call('selectBulan', 'Agustus')
            ->assertStatus(200)
            ->assertSee('Periode Dipantau')
            ->assertSee('Bulan Agustus')
            ->assertSee('Ahmad Santri')
            ->assertSee('Budi Santoso');

        // 2. Filter only unpaid students in Agustus
        $component->call('filterByBulanStatus', 'belum_bayar')
            ->assertSee('Budi Santoso')
            ->assertDontSee('Ahmad Santri');

        // 3. Filter only paid students in Agustus
        $component->call('filterByBulanStatus', 'lunas')
            ->assertSee('Ahmad Santri')
            ->assertDontSee('Budi Santoso');
    }

    public function test_open_and_close_per_murid_annual_matrix_modal(): void
    {
        $this->actingAs($this->financeUser);

        // Create bills across multiple months for Siswa 1
        Tagihan::create([
            'siswa_id' => $this->siswa1->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Juli',
            'nominal' => 350000,
            'total_dibayar' => 350000,
            'status' => 'lunas',
            'jatuh_tempo' => '2026-07-10',
        ]);

        Tagihan::create([
            'siswa_id' => $this->siswa1->id,
            'jenis_tagihan_id' => $this->jenisGedung->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Juli',
            'nominal' => 1500000,
            'total_dibayar' => 1500000,
            'status' => 'lunas',
            'jatuh_tempo' => '2026-07-10',
        ]);

        $component = Livewire::test(OverviewPembayaran::class)
            ->set('activeViewTab', 'lengkap')
            ->call('openSiswaMatrix', $this->siswa1->id)
            ->assertSet('showSiswaMatrixModal', true)
            ->assertSet('selectedSiswaMatrixId', $this->siswa1->id)
            ->assertSee('Matriks Tagihan Tahunan Siswa')
            ->assertSee('Ahmad Santri')
            ->assertSee('SPP Bulanan')
            ->assertSee('Uang Gedung')
            ->assertSee('1.850.000');

        // Close modal
        $component->call('closeSiswaMatrix')
            ->assertSet('showSiswaMatrixModal', false)
            ->assertSet('selectedSiswaMatrixId', null)
            ->assertDontSee('Matriks Tagihan Tahunan Siswa');
    }

    public function test_filter_by_rombel_kelas_works_on_detailed_matrix(): void
    {
        $this->actingAs($this->financeUser);

        Tagihan::create([
            'siswa_id' => $this->siswa1->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'September',
            'nominal' => 350000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => '2026-09-10',
        ]);

        Tagihan::create([
            'siswa_id' => $this->siswa2->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'September',
            'nominal' => 350000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => '2026-09-10',
        ]);

        // Filter for Kelas 7A only on September
        Livewire::test(OverviewPembayaran::class)
            ->set('activeViewTab', 'lengkap')
            ->call('selectBulan', 'September')
            ->set('filterKelas', $this->kelas7A->id)
            ->assertSee('Ahmad Santri')
            ->assertDontSee('Budi Santoso');
    }

    public function test_partial_payment_status_renders_in_matrix(): void
    {
        $this->actingAs($this->financeUser);

        // Siswa 1 pays 200,000 out of 350,000 (partial)
        Tagihan::create([
            'siswa_id' => $this->siswa1->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Oktober',
            'nominal' => 350000,
            'total_dibayar' => 200000,
            'status' => 'sebagian',
            'jatuh_tempo' => '2026-10-10',
        ]);

        Livewire::test(OverviewPembayaran::class)
            ->set('activeViewTab', 'lengkap')
            ->call('selectBulan', 'Oktober')
            ->assertStatus(200)
            ->assertSee('Cicil Rp 150.000');
    }

    public function test_finance_can_send_reminder_for_overdue_student(): void
    {
        $this->actingAs($this->financeUser);

        // Siswa 2 has overdue bill from previous month
        Tagihan::create([
            'siswa_id' => $this->siswa2->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Agustus',
            'nominal' => 350000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => Carbon::now()->subMonths(1)->startOfMonth()->addDays(5)->toDateString(),
        ]);

        Livewire::test(OverviewPembayaran::class)
            ->call('kirimReminder', $this->siswa2->id)
            ->assertSee('Reminder tunggakan berhasil dikirim');

        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $this->siswa2->user_id,
            'jenis' => 'tunggakan',
        ]);
    }

    public function test_spp_matrix_tab_renders_6_months_and_can_toggle_periods(): void
    {
        $this->actingAs($this->financeUser);

        // Siswa 1: Lunas Juli SPP
        $tagihanJuli1 = Tagihan::create([
            'siswa_id' => $this->siswa1->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Juli',
            'nominal' => 350000,
            'total_dibayar' => 350000,
            'status' => 'lunas',
            'jatuh_tempo' => '2026-07-10',
        ]);

        // Siswa 1: Belum Bayar Agustus SPP
        $tagihanAgustus1 = Tagihan::create([
            'siswa_id' => $this->siswa1->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Agustus',
            'nominal' => 350000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => '2026-08-10',
        ]);

        // Siswa 1: Lunas Uang Gedung (Non-SPP)
        $tagihanGedung1 = Tagihan::create([
            'siswa_id' => $this->siswa1->id,
            'jenis_tagihan_id' => $this->jenisGedung->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Tahunan',
            'nominal' => 1500000,
            'total_dibayar' => 1500000,
            'status' => 'lunas',
            'jatuh_tempo' => '2026-07-10',
        ]);

        $test = Livewire::test(OverviewPembayaran::class)
            ->assertSet('activeViewTab', 'spp_matrix')
            ->assertSee('SPP Rutin (Terbatas 6 Bulan)')
            ->assertSee('Kategori Tunggakan Lainnya (Non-SPP)')
            ->assertSee('Uang Gedung');

        $sppMonths = $test->viewData('sppMatrixMonths');
        $this->assertCount(6, $sppMonths);

        $nonSppList = $test->viewData('nonSppJenisList');
        $this->assertTrue($nonSppList->contains('id', $this->jenisGedung->id));

        // Switch to Semester Ganjil (6 months: Juli s/d Desember)
        $test->call('setSppPeriode', 'ganjil')
            ->assertSet('sppPeriode', 'ganjil')
            ->assertSee('SPP Juli')
            ->assertSee('SPP Agustus')
            ->assertSee('SPP September')
            ->assertSee('SPP Oktober')
            ->assertSee('SPP November')
            ->assertSee('SPP Desember')
            ->assertSee('Uang Gedung');

        $ganjilMonths = $test->viewData('sppMatrixMonths');
        $this->assertCount(6, $ganjilMonths);
        $this->assertEquals(['Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'], $ganjilMonths);

        $siswasSppMatrix = $test->viewData('siswasSppMatrix');
        $this->assertNotNull($siswasSppMatrix);

        $firstItem = collect($siswasSppMatrix->items())->firstWhere('id', $this->siswa1->id);
        $this->assertNotNull($firstItem);
        $this->assertEquals('lunas', $firstItem['spp_months']['Juli']['status']);
        $this->assertEquals('belum_bayar', $firstItem['spp_months']['Agustus']['status']);
        $this->assertEquals('tidak_ada', $firstItem['spp_months']['September']['status']);
        $this->assertEquals('lunas', $firstItem['non_spp_by_jenis'][$this->jenisGedung->id]['status']);

        // Direct payment cashier link should be rendered
        $expectedPayUrl = route('finance.input-pembayaran', [
            'siswa_id' => $this->siswa1->id,
            'tagihan_id' => $tagihanAgustus1->id,
        ]);
        $test->assertSeeHtml(e($expectedPayUrl));

        // Switch to Semester Genap (6 months: Januari s/d Juni)
        $test->call('setSppPeriode', 'genap')
            ->assertSet('sppPeriode', 'genap')
            ->assertSee('SPP Januari')
            ->assertSee('SPP Juni')
            ->assertDontSee('SPP Juli')
            ->assertSee('Uang Gedung');

        $genapMonths = $test->viewData('sppMatrixMonths');
        $this->assertCount(6, $genapMonths);
        $this->assertEquals(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'], $genapMonths);
    }

    public function test_filter_spp_matrix_by_payment_status(): void
    {
        $this->actingAs($this->financeUser);

        // Siswa 1: Lunas
        Tagihan::create([
            'siswa_id' => $this->siswa1->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Juli',
            'nominal' => 350000,
            'total_dibayar' => 350000,
            'status' => 'lunas',
            'jatuh_tempo' => '2026-07-10',
        ]);

        // Siswa 2: Menunggak
        Tagihan::create([
            'siswa_id' => $this->siswa2->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Juli',
            'nominal' => 350000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => '2026-07-10',
        ]);

        Livewire::test(OverviewPembayaran::class)
            ->assertSet('activeViewTab', 'spp_matrix')
            ->call('filterByStatusSpp', 'menunggak')
            ->assertSet('filterStatusSpp', 'menunggak')
            ->assertSee('Budi Santoso')
            ->call('filterByStatusSpp', 'lunas')
            ->assertSet('filterStatusSpp', 'lunas')
            ->assertSee('Ahmad Santri')
            ->call('filterByStatusSpp', 'lunas') // Toggle off
            ->assertSet('filterStatusSpp', '');
    }
}
