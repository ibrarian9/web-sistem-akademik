<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\JenisTagihan;
use App\Models\Tagihan;
use App\Livewire\Finance\InputPembayaran;
use App\Livewire\Finance\OverviewPembayaran;
use Livewire\Livewire;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FinanceFiltersSimpelTest extends TestCase
{
    use RefreshDatabase;

    protected User $financeUser;
    protected Siswa $siswaA;
    protected Siswa $siswaB;
    protected Kelas $kelas7A;
    protected Kelas $kelas7B;
    protected TahunAjaran $tahunAjaran;
    protected JenisTagihan $jenisSpp;
    protected JenisTagihan $jenisGedung;

    protected function setUp(): void
    {
        parent::setUp();

        $roleFinance = Role::firstOrCreate(['nama' => 'finance']);
        $this->financeUser = User::factory()->create([
            'role_id' => $roleFinance->id,
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

        $this->kelas7A = Kelas::firstOrCreate(
            ['nama_kelas' => '7A'],
            ['tingkat' => 7, 'kapasitas' => 30, 'semester_id' => $semester->id]
        );

        $this->kelas7B = Kelas::firstOrCreate(
            ['nama_kelas' => '7B'],
            ['tingkat' => 7, 'kapasitas' => 30, 'semester_id' => $semester->id]
        );

        $roleMurid = Role::firstOrCreate(['nama' => 'murid']);
        
        $userA = User::factory()->create(['nama' => 'Ahmad Santri', 'role_id' => $roleMurid->id]);
        $this->siswaA = Siswa::create([
            'user_id' => $userA->id,
            'nis' => '1001',
            'kelas_id' => $this->kelas7A->id,
            'tanggal_masuk' => '2026-07-01',
            'status' => 'aktif',
        ]);

        $userB = User::factory()->create(['nama' => 'Bambang Santri', 'role_id' => $roleMurid->id]);
        $this->siswaB = Siswa::create([
            'user_id' => $userB->id,
            'nis' => '1002',
            'kelas_id' => $this->kelas7B->id,
            'tanggal_masuk' => '2026-07-01',
            'status' => 'aktif',
        ]);

        $this->jenisSpp = JenisTagihan::firstOrCreate(
            ['nama' => 'SPP'],
            ['kategori' => 'rutin', 'default_nominal' => 350000, 'is_blocking' => true]
        );

        $this->jenisGedung = JenisTagihan::firstOrCreate(
            ['nama' => 'Uang Gedung'],
            ['kategori' => 'one_time', 'default_nominal' => 1500000, 'is_blocking' => false]
        );
    }

    public function test_input_pembayaran_filters_by_jenis_tagihan(): void
    {
        // Siswa A has SPP bill
        Tagihan::create([
            'siswa_id' => $this->siswaA->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Juli',
            'nominal' => 350000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => Carbon::now()->startOfMonth()->toDateString(),
        ]);

        // Siswa B has Uang Gedung bill
        Tagihan::create([
            'siswa_id' => $this->siswaB->id,
            'jenis_tagihan_id' => $this->jenisGedung->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => null,
            'nominal' => 1500000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => Carbon::now()->startOfMonth()->toDateString(),
        ]);

        Livewire::actingAs($this->financeUser)
            ->test(InputPembayaran::class)
            ->set('filterJenisTagihan', $this->jenisSpp->id)
            ->assertSee('Ahmad Santri')
            ->assertDontSee('Bambang Santri')
            ->set('filterJenisTagihan', $this->jenisGedung->id)
            ->assertSee('Bambang Santri')
            ->assertDontSee('Ahmad Santri');
    }

    public function test_input_pembayaran_filters_by_periode(): void
    {
        // Siswa A has Juli bill
        Tagihan::create([
            'siswa_id' => $this->siswaA->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Juli',
            'nominal' => 350000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => Carbon::now()->startOfMonth()->toDateString(),
        ]);

        // Siswa B has Agustus bill
        Tagihan::create([
            'siswa_id' => $this->siswaB->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Agustus',
            'nominal' => 350000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => Carbon::now()->startOfMonth()->toDateString(),
        ]);

        Livewire::actingAs($this->financeUser)
            ->test(InputPembayaran::class)
            ->set('filterPeriode', 'Juli')
            ->assertSee('Ahmad Santri')
            ->assertDontSee('Bambang Santri')
            ->set('filterPeriode', 'Agustus')
            ->assertSee('Bambang Santri')
            ->assertDontSee('Ahmad Santri');
    }

    public function test_input_pembayaran_filters_by_waktu(): void
    {
        // Past bill (Overdue)
        $billOverdue = Tagihan::create([
            'siswa_id' => $this->siswaA->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Juli',
            'nominal' => 350000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => Carbon::now()->subMonths(2)->startOfMonth()->toDateString(),
        ]);

        // Future bill (Mendatang)
        $billFuture = Tagihan::create([
            'siswa_id' => $this->siswaB->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Desember',
            'nominal' => 350000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => Carbon::now()->addMonths(2)->startOfMonth()->toDateString(),
        ]);

        // By default: only current/due arrears shown (Ahmad seen, Bambang not seen)
        Livewire::actingAs($this->financeUser)
            ->test(InputPembayaran::class)
            ->assertSee('Ahmad Santri')
            ->assertDontSee('Bambang Santri')
            // Filter Lewat Jatuh Tempo
            ->set('filterWaktu', 'lewat')
            ->assertSee('Ahmad Santri')
            ->assertDontSee('Bambang Santri')
            // Filter Mendatang
            ->set('filterWaktu', 'mendatang')
            ->assertSee('Bambang Santri')
            ->assertDontSee('Ahmad Santri')
            // Filter Semua
            ->set('filterWaktu', 'semua')
            ->assertSee('Ahmad Santri')
            ->assertSee('Bambang Santri');
    }

    public function test_input_pembayaran_reset_filters(): void
    {
        Livewire::actingAs($this->financeUser)
            ->test(InputPembayaran::class)
            ->set('search', 'Ahmad')
            ->set('filterKelas', $this->kelas7A->id)
            ->set('filterJenisTagihan', $this->jenisSpp->id)
            ->set('filterPeriode', 'Juli')
            ->set('filterWaktu', 'lewat')
            ->assertSet('activeFilterCount', 5)
            ->call('resetTableFilters')
            ->assertSet('search', '')
            ->assertSet('filterKelas', null)
            ->assertSet('filterJenisTagihan', null)
            ->assertSet('filterPeriode', '')
            ->assertSet('filterWaktu', '')
            ->assertSet('activeFilterCount', 0);
    }

    public function test_overview_pembayaran_reset_filters_and_status_sync(): void
    {
        Livewire::actingAs($this->financeUser)
            ->test(OverviewPembayaran::class)
            ->set('search', 'Ahmad')
            ->set('filterKelas', $this->kelas7A->id)
            ->call('filterByStatus', 'tunggakan')
            ->assertSet('filterStatus', 'tunggakan')
            ->assertSet('filterStatusSpp', 'menunggak')
            ->assertSet('activeFilterCount', 3)
            ->call('resetOverviewFilters')
            ->assertSet('search', '')
            ->assertSet('filterKelas', null)
            ->assertSet('filterStatus', '')
            ->assertSet('filterStatusSpp', '')
            ->assertSet('activeFilterCount', 0);
    }

    public function test_input_pembayaran_can_export_to_excel_csv(): void
    {
        // Siswa A has SPP bill
        Tagihan::create([
            'siswa_id' => $this->siswaA->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Juli',
            'nominal' => 350000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => '2026-07-10',
        ]);

        $test = Livewire::actingAs($this->financeUser)
            ->test(InputPembayaran::class)
            ->set('search', 'Ahmad');

        $response = $test->instance()->exportExcel();

        $this->assertNotNull($response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('daftar_tagihan_kasir_', $response->headers->get('Content-Disposition'));

        ob_start();
        $response->sendContent();
        $csvContent = ob_get_clean();

        // Check header columns and data content
        $this->assertStringContainsString('Nama Siswa', $csvContent);
        $this->assertStringContainsString('Jenis Tagihan', $csvContent);
        $this->assertStringContainsString('Ahmad Santri', $csvContent);
        $this->assertStringContainsString('350000', $csvContent);
        $this->assertStringContainsString('Belum Bayar', $csvContent);
    }

    public function test_input_pembayaran_can_export_to_pdf(): void
    {
        // Siswa A has SPP bill
        Tagihan::create([
            'siswa_id' => $this->siswaA->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Juli',
            'nominal' => 350000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => '2026-07-10',
        ]);

        $test = Livewire::actingAs($this->financeUser)
            ->test(InputPembayaran::class)
            ->set('filterKelas', $this->kelas7A->id);

        $response = $test->instance()->exportPdf();

        $this->assertNotNull($response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('daftar_tagihan_kasir_', $response->headers->get('Content-Disposition'));

        ob_start();
        $response->sendContent();
        $pdfOutput = ob_get_clean();

        $this->assertStringStartsWith('%PDF', $pdfOutput);
    }

    public function test_input_pembayaran_export_empty_handles_gracefully(): void
    {
        Livewire::actingAs($this->financeUser)
            ->test(InputPembayaran::class)
            ->set('search', 'NamaTidakPernahAda123')
            ->call('exportExcel')
            ->assertDispatched('show-alert');

        Livewire::actingAs($this->financeUser)
            ->test(InputPembayaran::class)
            ->set('search', 'NamaTidakPernahAda123')
            ->call('exportPdf')
            ->assertDispatched('show-alert');
    }

    public function test_input_pembayaran_view_displays_export_buttons(): void
    {
        Livewire::actingAs($this->financeUser)
            ->test(InputPembayaran::class)
            ->assertSee('Ekspor Excel')
            ->assertSee('Cetak PDF')
            ->assertSee('Excel')
            ->assertSee('PDF');
    }

    public function test_input_pembayaran_has_no_n_plus_one_queries(): void
    {
        $roleMurid = Role::firstOrCreate(['nama' => 'murid']);

        // Batch 1: Create 3 students with unpaid bills
        for ($i = 1; $i <= 3; $i++) {
            $user = User::factory()->create(['nama' => "Santri Batch 1 No $i", 'role_id' => $roleMurid->id]);
            $siswa = Siswa::create([
                'user_id' => $user->id,
                'nis' => "300$i",
                'kelas_id' => $this->kelas7A->id,
                'tanggal_masuk' => '2026-07-01',
                'status' => 'aktif',
            ]);
            Tagihan::create([
                'siswa_id' => $siswa->id,
                'jenis_tagihan_id' => $this->jenisSpp->id,
                'tahun_ajaran_id' => $this->tahunAjaran->id,
                'bulan' => 'Juli',
                'nominal' => 350000,
                'total_dibayar' => 0,
                'status' => 'belum_bayar',
                'jatuh_tempo' => '2026-07-10',
            ]);
        }

        // Warm-up to prime static/role permissions and app cache
        Livewire::actingAs($this->financeUser)->test(InputPembayaran::class);

        \Illuminate\Support\Facades\DB::flushQueryLog();
        \Illuminate\Support\Facades\DB::enableQueryLog();

        Livewire::actingAs($this->financeUser)->test(InputPembayaran::class);

        $queriesBatch1 = count(\Illuminate\Support\Facades\DB::getQueryLog());
        \Illuminate\Support\Facades\DB::disableQueryLog();

        // Batch 2: Add 7 MORE students with unpaid bills (total 10 students)
        for ($i = 4; $i <= 10; $i++) {
            $user = User::factory()->create(['nama' => "Santri Batch 2 No $i", 'role_id' => $roleMurid->id]);
            $siswa = Siswa::create([
                'user_id' => $user->id,
                'nis' => "300$i",
                'kelas_id' => $this->kelas7A->id,
                'tanggal_masuk' => '2026-07-01',
                'status' => 'aktif',
            ]);
            Tagihan::create([
                'siswa_id' => $siswa->id,
                'jenis_tagihan_id' => $this->jenisSpp->id,
                'tahun_ajaran_id' => $this->tahunAjaran->id,
                'bulan' => 'Juli',
                'nominal' => 350000,
                'total_dibayar' => 0,
                'status' => 'belum_bayar',
                'jatuh_tempo' => '2026-07-10',
            ]);
        }

        \Illuminate\Support\Facades\DB::flushQueryLog();
        \Illuminate\Support\Facades\DB::enableQueryLog();

        Livewire::actingAs($this->financeUser)->test(InputPembayaran::class);

        $queriesBatch2 = count(\Illuminate\Support\Facades\DB::getQueryLog());
        \Illuminate\Support\Facades\DB::disableQueryLog();

        // The query count must be constant, proving O(1) query complexity without N+1
        $this->assertEquals($queriesBatch1, $queriesBatch2, "Query count increased from $queriesBatch1 to $queriesBatch2, indicating an N+1 query issue.");
    }
}
