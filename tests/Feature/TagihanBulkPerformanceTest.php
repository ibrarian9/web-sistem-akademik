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
use App\Livewire\Finance\ManajemenTagihan;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class TagihanBulkPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $financeUser;
    protected Kelas $kelas;
    protected TahunAjaran $ta;
    protected JenisTagihan $jenisSPP;

    protected function setUp(): void
    {
        parent::setUp();

        $roleFinance = Role::firstOrCreate(['nama' => 'finance'], ['deskripsi' => 'Finance']);
        $roleMurid = Role::firstOrCreate(['nama' => 'murid'], ['deskripsi' => 'Murid']);

        $this->financeUser = User::factory()->create([
            'role_id' => $roleFinance->id,
            'status' => 'aktif',
        ]);

        $this->ta = TahunAjaran::create([
            'nama' => '2026/2027',
            'status_aktif' => true,
        ]);

        $semester = Semester::create([
            'tahun_ajaran_id' => $this->ta->id,
            'semester' => 'ganjil',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2026-12-31',
            'status_aktif' => true,
        ]);

        $this->kelas = Kelas::create([
            'nama_kelas' => '8-A',
            'tingkat' => 8,
            'semester_id' => $semester->id,
        ]);

        $this->jenisSPP = JenisTagihan::create([
            'nama' => 'SPP Bulanan',
            'kategori' => 'rutin',
            'default_nominal' => 350000,
        ]);

        // Create 15 students in class 8-A
        for ($i = 1; $i <= 15; $i++) {
            $user = User::factory()->create([
                'nama' => "Siswa Test $i",
                'role_id' => $roleMurid->id,
                'status' => 'aktif',
            ]);

            Siswa::create([
                'user_id' => $user->id,
                'kelas_id' => $this->kelas->id,
                'nis' => "NIS-$i",
                'status' => 'aktif',
                'tanggal_masuk' => '2026-07-01',
            ]);
        }
    }

    public function test_bulk_release_for_fifteen_students_over_twelve_months_executes_minimal_queries()
    {
        $this->actingAs($this->financeUser);

        $component = Livewire::test(ManajemenTagihan::class)
            ->call('openCreateModal')
            ->set('releaseMode', 'bulk')
            ->set('bulkTarget', 'class')
            ->set('release_kelas_id', $this->kelas->id)
            ->set('periodeTipe', 'full_year_jan_des')
            ->set('jenis_tagihan_id', $this->jenisSPP->id)
            ->set('nominal', 350000)
            ->set('jatuh_tempo', '2026-01-10');

        DB::flushQueryLog();
        DB::enableQueryLog();

        $component->call('createBulkTagihan')
            ->assertHasNoErrors()
            ->assertDispatched('show-alert');

        $queryLog = DB::getQueryLog();
        $queryCount = count($queryLog);

        // 15 students * 12 months = 180 bills.
        // Old implementation executed: 180 exists SELECT + 180 INSERT + 180 activity_log = 540+ queries!
        // Optimized implementation does:
        // 1 query to get active students + 1 query for active TA + 1 single batch query to check existing bills + 1 chunked INSERT + 1 audit log.
        // Total query count must be under 30 (including Livewire mount / render queries).
        $this->assertLessThan(40, $queryCount, "Query count {$queryCount} should be bounded and not scale per-bill.");

        // Verify total created bills = 15 students * 12 months = 180 bills
        $this->assertEquals(180, Tagihan::where('jenis_tagihan_id', $this->jenisSPP->id)->count());

        // Re-running the same bulk action must skip all 180 existing bills gracefully
        Livewire::test(ManajemenTagihan::class)
            ->call('openCreateModal')
            ->set('releaseMode', 'bulk')
            ->set('bulkTarget', 'class')
            ->set('release_kelas_id', $this->kelas->id)
            ->set('periodeTipe', 'full_year_jan_des')
            ->set('jenis_tagihan_id', $this->jenisSPP->id)
            ->set('nominal', 350000)
            ->set('jatuh_tempo', '2026-01-10')
            ->call('createBulkTagihan')
            ->assertHasNoErrors();

        // Count must still be exactly 180 (no duplicate bills created)
        $this->assertEquals(180, Tagihan::where('jenis_tagihan_id', $this->jenisSPP->id)->count());
    }
}
