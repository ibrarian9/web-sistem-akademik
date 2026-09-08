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
use App\Models\Semester;
use App\Models\ApprovalKeuangan;
use App\Services\FinancialApprovalService;
use Livewire\Livewire;
use App\Livewire\Finance\ApprovalKeuanganIndex;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FinancialApprovalCancellationTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $superAdmin2;
    protected User $financeUser;
    protected User $otherUser;
    protected Siswa $siswa;
    protected Tagihan $tagihan;

    protected function setUp(): void
    {
        parent::setUp();

        $roleAdmin = Role::firstOrCreate(['nama' => 'super_admin'], ['deskripsi' => 'Super Administrator']);
        $roleAdmin2 = Role::firstOrCreate(['nama' => 'super_admin_2'], ['deskripsi' => 'Super Administrator 2']);
        $roleFinance = Role::firstOrCreate(['nama' => 'finance'], ['deskripsi' => 'Staff Keuangan']);
        $roleMurid = Role::firstOrCreate(['nama' => 'murid'], ['deskripsi' => 'Murid']);

        $this->superAdmin = User::factory()->create([
            'username' => 'admin_super_' . uniqid(),
            'nama' => 'Super Administrator 1',
            'role_id' => $roleAdmin->id,
            'status' => 'aktif',
        ]);

        $this->superAdmin2 = User::factory()->create([
            'username' => 'admin_two_' . uniqid(),
            'nama' => 'Super Admin 2',
            'role_id' => $roleAdmin2->id,
            'status' => 'aktif',
        ]);

        $this->financeUser = User::factory()->create([
            'username' => 'finance_user_' . uniqid(),
            'nama' => 'Staff Keuangan Siti',
            'role_id' => $roleFinance->id,
            'status' => 'aktif',
        ]);

        $this->otherUser = User::factory()->create([
            'username' => 'murid_' . uniqid(),
            'nama' => 'Siswa Test',
            'role_id' => $roleMurid->id,
            'status' => 'aktif',
        ]);

        $tahunAjaran = TahunAjaran::create([
            'nama' => '2026/2027',
            'status_aktif' => true,
        ]);

        $semester = Semester::create([
            'tahun_ajaran_id' => $tahunAjaran->id,
            'semester' => 'Ganjil',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2026-12-31',
            'status_aktif' => true,
        ]);

        $kelas = Kelas::create([
            'nama_kelas' => 'X-MIPA-1',
            'jenis_kelas' => 'umum',
            'tingkat' => 10,
            'semester_id' => $semester->id,
        ]);

        $this->siswa = Siswa::create([
            'user_id' => $this->otherUser->id,
            'kelas_id' => $kelas->id,
            'nis' => '998877',
            'nisn' => '1122334455',
            'jenis_kelamin' => 'L',
            'tanggal_masuk' => '2026-07-01',
            'status' => 'aktif',
            'saldo_deposit' => 0.00,
        ]);

        $jenisSpp = JenisTagihan::create([
            'nama' => 'SPP Bulanan',
            'kategori' => 'rutin',
            'default_nominal' => 350000,
            'is_blocking' => true,
        ]);

        $this->tagihan = Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'jenis_tagihan_id' => $jenisSpp->id,
            'bulan' => 'September',
            'nominal' => 350000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => '2026-09-10',
        ]);
    }

    public function test_finance_can_cancel_pending_approval_request_via_service(): void
    {
        $approval = FinancialApprovalService::createRequest(
            $this->financeUser,
            'edit',
            'tagihan',
            $this->tagihan,
            ['nominal' => 450000],
            'Koreksi biaya kegiatan',
            'Edit Tagihan Budi Santoso'
        );

        $this->assertEquals('menunggu', $approval->status);

        // Cancel the request
        $cancelled = FinancialApprovalService::cancel($approval, $this->financeUser, 'Salah input nominal');

        $this->assertTrue($cancelled);
        $approval->refresh();
        $this->assertEquals('dibatalkan', $approval->status);
        $this->assertEquals('Salah input nominal', $approval->catatan_approval);
        $this->assertTrue($approval->isDibatalkan());
        $this->assertFalse($approval->isMenunggu());

        // Target tagihan remains unchanged
        $this->tagihan->refresh();
        $this->assertEquals(350000, floatval($this->tagihan->nominal));
    }

    public function test_cannot_cancel_already_approved_request(): void
    {
        $approval = FinancialApprovalService::createRequest(
            $this->financeUser,
            'edit',
            'tagihan',
            $this->tagihan,
            ['nominal' => 400000],
            'Koreksi',
            'Edit Tagihan Budi'
        );

        FinancialApprovalService::approve($approval, $this->superAdmin, 'Disetujui');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Permohonan persetujuan tidak dapat dibatalkan');

        FinancialApprovalService::cancel($approval, $this->financeUser, 'Batalkan setelah disetujui');
    }

    public function test_unauthorized_user_cannot_cancel_approval_request(): void
    {
        $approval = FinancialApprovalService::createRequest(
            $this->financeUser,
            'edit',
            'tagihan',
            $this->tagihan,
            ['nominal' => 400000],
            'Koreksi',
            'Edit Tagihan Budi'
        );

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Akses Ditolak');

        FinancialApprovalService::cancel($approval, $this->otherUser, 'Mencoba membatalkan tanpa hak akses');
    }

    public function test_finance_can_cancel_approval_via_livewire_component(): void
    {
        $approval = FinancialApprovalService::createRequest(
            $this->financeUser,
            'hapus',
            'tagihan',
            $this->tagihan,
            null,
            'Tagihan dibuat duplikat',
            'Hapus Tagihan Duplikat Budi'
        );

        $this->actingAs($this->financeUser);

        Livewire::test(ApprovalKeuanganIndex::class)
            ->assertSee('Hapus Tagihan Duplikat Budi')
            ->assertSee('Batalkan')
            ->call('openCancelModal', $approval->id)
            ->assertSet('showCancelModal', true)
            ->set('cancelReason', 'Ditemukan bahwa tagihan tersebut masih valid')
            ->call('cancelApproval')
            ->assertSet('showCancelModal', false)
            ->assertHasNoErrors()
            ->assertDispatched('show-alert');

        $approval->refresh();
        $this->assertEquals('dibatalkan', $approval->status);
        $this->assertEquals('Ditemukan bahwa tagihan tersebut masih valid', $approval->catatan_approval);
    }

    public function test_metrics_show_dibatalkan_count(): void
    {
        $approval1 = FinancialApprovalService::createRequest(
            $this->financeUser,
            'edit',
            'tagihan',
            $this->tagihan,
            ['nominal' => 400000],
            'Test 1',
            'Pengajuan 1'
        );

        $approval2 = FinancialApprovalService::createRequest(
            $this->financeUser,
            'hapus',
            'tagihan',
            $this->tagihan,
            null,
            'Test 2',
            'Pengajuan 2'
        );

        // Cancel approval1
        FinancialApprovalService::cancel($approval1, $this->financeUser, 'Dibatalkan');

        $this->actingAs($this->financeUser);

        Livewire::test(ApprovalKeuanganIndex::class)
            ->assertViewHas('counts', function ($counts) {
                return isset($counts['dibatalkan']) && $counts['dibatalkan'] === 1
                    && isset($counts['menunggu']) && $counts['menunggu'] === 1;
            })
            ->set('filterStatus', 'dibatalkan')
            ->assertSee('Pengajuan 1')
            ->assertDontSee('Pengajuan 2');
    }
}
