<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\Pembayaran;
use Livewire\Livewire;
use App\Livewire\Finance\OverviewPembayaran;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OverviewPembayaranMatrixExportTest extends TestCase
{
    use RefreshDatabase;

    protected User $financeUser;
    protected User $superAdmin;
    protected Kelas $kelas;
    protected Siswa $siswa1;
    protected Siswa $siswa2;
    protected TahunAjaran $tahunAjaran;
    protected JenisTagihan $jenisSpp;
    protected JenisTagihan $jenisUjian;

    protected function setUp(): void
    {
        parent::setUp();

        $roleFinance = Role::firstOrCreate(['nama' => 'finance'], ['deskripsi' => 'Staf Keuangan']);
        $roleSuperAdmin = Role::firstOrCreate(['nama' => 'super_admin'], ['deskripsi' => 'Super Admin']);

        $this->financeUser = User::factory()->create([
            'nama' => 'Staf Bendahara',
            'username' => 'finance_export_' . uniqid(),
            'role_id' => $roleFinance->id,
            'status' => 'aktif',
        ]);

        $this->superAdmin = User::factory()->create([
            'nama' => 'Super Admin Utama',
            'username' => 'sa_export_' . uniqid(),
            'role_id' => $roleSuperAdmin->id,
            'status' => 'aktif',
        ]);

        $this->tahunAjaran = TahunAjaran::firstOrCreate(
            ['nama' => '2026/2027'],
            ['status_aktif' => true, 'tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2027-06-30']
        );

        $semester = Semester::firstOrCreate(
            ['tahun_ajaran_id' => $this->tahunAjaran->id, 'semester' => 'ganjil'],
            ['tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2026-12-31', 'status_aktif' => true]
        );

        $this->kelas = Kelas::firstOrCreate(
            ['nama_kelas' => '7A'],
            ['tingkat' => 7, 'kapasitas' => 30, 'semester_id' => $semester->id]
        );

        $u1 = User::factory()->create(['nama' => 'Santri Ali', 'role_id' => Role::firstOrCreate(['nama' => 'murid'])->id]);
        $this->siswa1 = Siswa::create([
            'user_id' => $u1->id,
            'nis' => '7001',
            'kelas_id' => $this->kelas->id,
            'tanggal_masuk' => '2026-07-01',
            'status' => 'aktif',
        ]);

        $u2 = User::factory()->create(['nama' => 'Santri Budi', 'role_id' => Role::firstOrCreate(['nama' => 'murid'])->id]);
        $this->siswa2 = Siswa::create([
            'user_id' => $u2->id,
            'nis' => '7002',
            'kelas_id' => $this->kelas->id,
            'tanggal_masuk' => '2026-07-01',
            'status' => 'aktif',
        ]);

        $this->jenisSpp = JenisTagihan::create([
            'nama' => 'SPP Bulanan',
            'kategori' => 'rutin',
            'default_nominal' => 250000,
            'is_blocking' => true,
        ]);

        $this->jenisUjian = JenisTagihan::create([
            'nama' => 'Biaya Ujian Semester 1',
            'kategori' => 'semester',
            'default_nominal' => 200000,
            'is_blocking' => true,
        ]);

        // Siswa 1: Lunas SPP Juli dan Lunas Biaya Ujian
        $t1 = Tagihan::create([
            'siswa_id' => $this->siswa1->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Juli',
            'nominal' => 250000,
            'total_dibayar' => 250000,
            'status' => 'lunas',
            'jatuh_tempo' => '2026-07-10',
        ]);
        Pembayaran::create([
            'tagihan_id' => $t1->id,
            'petugas_id' => $this->financeUser->id,
            'nominal_dibayar' => 250000,
            'metode_bayar' => 'Transfer',
            'tanggal_bayar' => '2026-07-05',
        ]);

        $t2 = Tagihan::create([
            'siswa_id' => $this->siswa1->id,
            'jenis_tagihan_id' => $this->jenisUjian->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Juli',
            'nominal' => 200000,
            'total_dibayar' => 200000,
            'status' => 'lunas',
            'jatuh_tempo' => '2026-07-10',
        ]);
        Pembayaran::create([
            'tagihan_id' => $t2->id,
            'petugas_id' => $this->financeUser->id,
            'nominal_dibayar' => 200000,
            'metode_bayar' => 'Tunai',
            'tanggal_bayar' => '2026-07-06',
        ]);

        // Siswa 2: Menunggak SPP Juli
        Tagihan::create([
            'siswa_id' => $this->siswa2->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Juli',
            'nominal' => 250000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => '2026-07-10',
        ]);
    }

    public function test_finance_can_export_matrix_to_excel_csv(): void
    {
        $test = Livewire::actingAs($this->financeUser)
            ->test(OverviewPembayaran::class)
            ->call('exportMatrixExcel');

        $response = $test->instance()->exportMatrixExcel();

        $this->assertNotNull($response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('matriks_tagihan_pembayaran_', $response->headers->get('Content-Disposition'));

        // Capture output stream
        ob_start();
        $response->sendContent();
        $csvContent = ob_get_clean();

        // Verify CSV contains headers and student records
        $this->assertStringContainsString('Nama Santri', $csvContent);
        $this->assertStringContainsString('SPP Juli', $csvContent);
        $this->assertStringContainsString('Biaya Ujian Semester 1', $csvContent);
        $this->assertStringContainsString('Santri Ali', $csvContent);
        $this->assertStringContainsString('Santri Budi', $csvContent);
        $this->assertStringContainsString('Lunas', $csvContent);
        $this->assertStringContainsString('Belum Bayar', $csvContent);
    }

    public function test_finance_can_export_matrix_to_pdf(): void
    {
        $test = Livewire::actingAs($this->financeUser)
            ->test(OverviewPembayaran::class);

        $response = $test->instance()->exportMatrixPdf();

        $this->assertNotNull($response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('matriks_tagihan_pembayaran_', $response->headers->get('Content-Disposition'));

        ob_start();
        $response->sendContent();
        $pdfOutput = ob_get_clean();

        // PDF starts with %PDF magic bytes
        $this->assertStringStartsWith('%PDF', $pdfOutput);
    }

    public function test_export_respects_status_filter(): void
    {
        // Filter: Hanya yang menunggak
        $test = Livewire::actingAs($this->financeUser)
            ->test(OverviewPembayaran::class)
            ->set('filterStatusSpp', 'menunggak');

        $response = $test->instance()->exportMatrixExcel();

        $this->assertNotNull($response);
        ob_start();
        $response->sendContent();
        $csvContent = ob_get_clean();

        // Santri Budi (menunggak) ada, Santri Ali (lunas) tidak ada
        $this->assertStringContainsString('Santri Budi', $csvContent);
        $this->assertStringNotContainsString('Santri Ali', $csvContent);
    }

    public function test_matrix_view_displays_export_buttons(): void
    {
        Livewire::actingAs($this->financeUser)
            ->test(OverviewPembayaran::class)
            ->assertSee('Ekspor Excel')
            ->assertSee('Cetak PDF');
    }
}
