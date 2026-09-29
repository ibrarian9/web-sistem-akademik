<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Guru;
use App\Models\GajiGuru;
use App\Models\Peminjaman;
use App\Services\Finance\SalaryBulkGeneratorService;
use App\Actions\Finance\GenerateBulkGajiAction;
use App\Livewire\Finance\ManajemenGajiGuru;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class FinanceBulkGajiPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $financeUser;
    protected Role $financeRole;
    protected Role $guruRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->financeRole = Role::firstOrCreate(['nama' => 'finance'], ['deskripsi' => 'Finance']);
        $this->guruRole = Role::firstOrCreate(['nama' => 'guru'], ['deskripsi' => 'Guru']);

        $this->financeUser = User::factory()->create([
            'role_id' => $this->financeRole->id,
            'status' => 'aktif',
        ]);
    }

    public function test_build_preview_items_executes_minimal_queries_for_nine_teachers()
    {
        // Create 9 active teachers
        $gurus = [];
        for ($i = 1; $i <= 9; $i++) {
            $user = User::factory()->create([
                'nama' => "Guru Test $i",
                'role_id' => $this->guruRole->id,
                'status' => 'aktif',
            ]);

            $guru = Guru::create([
                'user_id' => $user->id,
                'nip' => "NIP-$i",
                'jenis_guru' => 'umum',
                'status_kepegawaian' => $i % 2 === 0 ? 'tetap_yayasan' : 'kontrak',
                'status_aktif' => true,
                'tanggal_masuk' => '2025-01-01',
            ]);

            $gurus[] = $guru;
        }

        // Add active loan for 2 teachers
        Peminjaman::create([
            'guru_id' => $gurus[0]->id,
            'tanggal_pinjam' => now()->subMonths(2)->toDateString(),
            'nominal' => 1000000,
            'tenor_bulan' => 5,
            'cicilan_per_bulan' => 200000,
            'sisa_pinjaman' => 600000,
            'status' => 'berjalan',
        ]);

        Peminjaman::create([
            'guru_id' => $gurus[1]->id,
            'tanggal_pinjam' => now()->subMonth()->toDateString(),
            'nominal' => 500000,
            'tenor_bulan' => 5,
            'cicilan_per_bulan' => 100000,
            'sisa_pinjaman' => 100000,
            'status' => 'berjalan',
        ]);

        DB::flushQueryLog();
        DB::enableQueryLog();

        $service = app(SalaryBulkGeneratorService::class);
        $preview = $service->buildPreviewItems('September', 2026);

        $queryCount = count(DB::getQueryLog());

        // For 9 teachers, query count must be <= 4 (active gurus + with user + check existing gaji + check loans)
        // Previously this took 1 + 2*9 = 19+ queries!
        $this->assertLessThanOrEqual(5, $queryCount, "Query count {$queryCount} should be bounded and not scale N+1 with teachers.");
        $this->assertCount(9, $preview);

        // Verify loan deduction is properly populated
        $this->assertEquals(200000, $preview[$gurus[0]->id]['potongan_peminjaman']);
        $this->assertEquals(100000, $preview[$gurus[1]->id]['potongan_peminjaman']);
        $this->assertEquals(0, $preview[$gurus[2]->id]['potongan_peminjaman']);
    }

    public function test_bulk_generate_action_handles_nine_teachers_efficiently()
    {
        $gurus = [];
        for ($i = 1; $i <= 9; $i++) {
            $user = User::factory()->create([
                'nama' => "Guru Batch $i",
                'role_id' => $this->guruRole->id,
                'status' => 'aktif',
            ]);

            $gurus[] = Guru::create([
                'user_id' => $user->id,
                'nip' => "BATCH-$i",
                'jenis_guru' => 'umum',
                'status_kepegawaian' => 'tetap_yayasan',
                'status_aktif' => true,
                'tanggal_masuk' => '2025-01-01',
            ]);
        }

        $service = app(SalaryBulkGeneratorService::class);
        $preview = $service->buildPreviewItems('September', 2026);

        $action = app(GenerateBulkGajiAction::class);
        $created = $action->execute($preview, 'September', 2026);

        $this->assertEquals(9, $created);
        $this->assertEquals(9, GajiGuru::where('bulan', 'September')->where('tahun', 2026)->count());

        // Second run should skip all existing without duplicate exception
        $createdSecond = $action->execute($preview, 'September', 2026);
        $this->assertEquals(0, $createdSecond);
    }

    public function test_livewire_manajemen_gaji_guru_bulk_modal_and_recalculation()
    {
        for ($i = 1; $i <= 5; $i++) {
            $user = User::factory()->create([
                'nama' => "Pegawai $i",
                'role_id' => $this->guruRole->id,
                'status' => 'aktif',
            ]);

            Guru::create([
                'user_id' => $user->id,
                'nip' => "PEG-$i",
                'jenis_guru' => 'umum',
                'status_kepegawaian' => 'tetap_yayasan',
                'status_aktif' => true,
                'tanggal_masuk' => '2025-01-01',
            ]);
        }

        $this->actingAs($this->financeUser);

        $firstGuru = Guru::first();

        Livewire::test(ManajemenGajiGuru::class)
            ->call('openGenerateModal')
            ->assertSet('showGenerateModal', true)
            ->assertCount('generateItems', 5)
            // Simulate editing nominal on blur
            ->set("generateItems.{$firstGuru->id}.gaji_pokok", 2500000)
            ->assertSet("generateItems.{$firstGuru->id}.total_bruto", 3137928) // 2.5m + 120k + 500k + 17928
            ->call('generateDrafts')
            ->assertSet('showGenerateModal', false)
            ->assertHasNoErrors();

        $currentBulan = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ][intval(date('n')) - 1] ?? 'Januari';

        $this->assertEquals(5, GajiGuru::where('bulan', $currentBulan)->where('tahun', intval(date('Y')))->count());
        $saved = GajiGuru::where('guru_id', $firstGuru->id)->where('bulan', $currentBulan)->first();
        $this->assertEquals(2500000, floatval($saved->gaji_pokok));
    }

    public function test_bulk_generate_prefills_values_from_previous_month_salary()
    {
        $user = User::factory()->create([
            'nama' => "Guru Senior",
            'role_id' => $this->guruRole->id,
            'status' => 'aktif',
        ]);

        $guru = Guru::create([
            'user_id' => $user->id,
            'nip' => "SENIOR-01",
            'jenis_guru' => 'umum',
            'status_kepegawaian' => 'tetap_yayasan',
            'status_aktif' => true,
            'tanggal_masuk' => '2024-01-01',
        ]);

        // Prior salary in Agustus 2026
        GajiGuru::create([
            'guru_id' => $guru->id,
            'bulan' => 'Agustus',
            'tahun' => 2026,
            'gaji_pokok' => 3200000,
            'gaji_berkala' => 250000,
            'insentif' => 650000,
            'honor_ekskul' => 150000,
            'insentif_bpjs' => 25000,
            'insentif_maghrib_mengaji' => 100000,
            'potongan_sosial' => 15000,
            'potongan_peminjaman' => 0,
            'potongan_bpjstk' => 20000,
            'potongan_lainnya' => 5000,
            'total_bruto' => 4375000,
            'total_diterima' => 4335000,
            'tanggal_bayar' => '2026-08-25',
            'status' => 'dibayar',
            'sumber_dana' => 'Yayasan',
            'jam_kerja' => '07.00-14.00 (Fleksibel)',
            'jabatan' => 'Guru Senior',
        ]);

        $service = app(SalaryBulkGeneratorService::class);
        $preview = $service->buildPreviewItems('September', 2026);

        $this->assertArrayHasKey($guru->id, $preview);
        $item = $preview[$guru->id];

        $this->assertTrue($item['has_previous_salary']);
        $this->assertEquals(3200000, $item['gaji_pokok']);
        $this->assertEquals(250000, $item['gaji_berkala']);
        $this->assertEquals(650000, $item['insentif']);
        $this->assertEquals(150000, $item['honor_ekskul']);
        $this->assertEquals(25000, $item['insentif_bpjs']);
        $this->assertEquals(100000, $item['insentif_maghrib_mengaji']);
        $this->assertEquals(15000, $item['potongan_sosial']);
        $this->assertEquals(20000, $item['potongan_bpjstk']);
        $this->assertEquals(5000, $item['potongan_lainnya']);
        $this->assertEquals('Guru Senior', $item['jabatan']);
    }

    public function test_single_create_modal_prefills_values_from_previous_month_salary()
    {
        $user = User::factory()->create([
            'nama' => "Guru Manual",
            'role_id' => $this->guruRole->id,
            'status' => 'aktif',
        ]);

        $guru = Guru::create([
            'user_id' => $user->id,
            'nip' => "MANUAL-01",
            'jenis_guru' => 'umum',
            'status_kepegawaian' => 'tetap_yayasan',
            'status_aktif' => true,
            'tanggal_masuk' => '2024-01-01',
        ]);

        // Prior salary in Agustus 2026
        GajiGuru::create([
            'guru_id' => $guru->id,
            'bulan' => 'Agustus',
            'tahun' => 2026,
            'gaji_pokok' => 2800000,
            'gaji_berkala' => 180000,
            'insentif' => 550000,
            'honor_ekskul' => 120000,
            'insentif_bpjs' => 20000,
            'insentif_maghrib_mengaji' => 80000,
            'potongan_sosial' => 12000,
            'potongan_peminjaman' => 0,
            'potongan_bpjstk' => 18000,
            'potongan_lainnya' => 2000,
            'total_bruto' => 3750000,
            'total_diterima' => 3718000,
            'tanggal_bayar' => '2026-08-25',
            'status' => 'dibayar',
            'sumber_dana' => 'Yayasan',
            'jam_kerja' => '07.00-14.00 (Fleksibel)',
            'jabatan' => 'Koordinator Tahfidz',
        ]);

        $this->actingAs($this->financeUser);

        Livewire::test(ManajemenGajiGuru::class)
            ->set('createGuruId', $guru->id)
            ->set('createBulan', 'September')
            ->set('createTahun', 2026)
            ->assertSet('createHasPreviousSalary', true)
            ->assertSet('createGajiPokok', 2800000)
            ->assertSet('createGajiBerkala', 180000)
            ->assertSet('createInsentif', 550000)
            ->assertSet('createHonorEkskul', 120000)
            ->assertSet('createInsentifMaghrib', 80000)
            ->assertSet('createPotonganSosial', 12000)
            ->assertSet('createJabatan', 'Koordinator Tahfidz');
    }
}
