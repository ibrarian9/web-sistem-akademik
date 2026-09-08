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

class ApprovalKeuanganBulkActionsTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $financeUser;
    protected User $otherUser;
    protected Siswa $siswa;
    protected Tagihan $tagihan1;
    protected Tagihan $tagihan2;
    protected Tagihan $tagihan3;

    protected function setUp(): void
    {
        parent::setUp();

        $roleAdmin = Role::firstOrCreate(['nama' => 'super_admin'], ['deskripsi' => 'Super Administrator']);
        $roleFinance = Role::firstOrCreate(['nama' => 'finance'], ['deskripsi' => 'Staff Keuangan']);
        $roleMurid = Role::firstOrCreate(['nama' => 'murid'], ['deskripsi' => 'Murid']);

        $this->superAdmin = User::factory()->create([
            'username' => 'super_admin_' . uniqid(),
            'nama' => 'Super Administrator Test',
            'role_id' => $roleAdmin->id,
            'status' => 'aktif',
        ]);

        $this->financeUser = User::factory()->create([
            'username' => 'finance_bulk_' . uniqid(),
            'nama' => 'Staff Finance Bulk',
            'role_id' => $roleFinance->id,
            'status' => 'aktif',
        ]);

        $this->otherUser = User::factory()->create([
            'username' => 'murid_bulk_' . uniqid(),
            'nama' => 'Siswa Bulk Test',
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
            'nama_kelas' => 'X-A',
            'jenis_kelas' => 'umum',
            'tingkat' => 10,
            'semester_id' => $semester->id,
        ]);

        $this->siswa = Siswa::create([
            'user_id' => $this->otherUser->id,
            'kelas_id' => $kelas->id,
            'nis' => '110022',
            'nisn' => '9900112233',
            'jenis_kelamin' => 'L',
            'tanggal_masuk' => '2026-07-01',
            'status' => 'aktif',
            'saldo_deposit' => 0.00,
        ]);

        $jenisTagihan = JenisTagihan::create([
            'nama' => 'Iuran Sarana',
            'kategori' => 'rutin',
            'default_nominal' => 200000,
            'is_blocking' => true,
        ]);

        $this->tagihan1 = Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'jenis_tagihan_id' => $jenisTagihan->id,
            'bulan' => 'September',
            'nominal' => 200000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => '2026-09-10',
        ]);

        $this->tagihan2 = Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'jenis_tagihan_id' => $jenisTagihan->id,
            'bulan' => 'Oktober',
            'nominal' => 200000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => '2026-10-10',
        ]);

        $this->tagihan3 = Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'jenis_tagihan_id' => $jenisTagihan->id,
            'bulan' => 'November',
            'nominal' => 200000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => '2026-11-10',
        ]);
    }

    public function test_select_all_only_selects_actionable_pending_items(): void
    {
        $app1 = FinancialApprovalService::createRequest(
            $this->financeUser,
            'edit',
            'tagihan',
            $this->tagihan1,
            ['nominal' => 250000],
            'Alasan 1',
            'Edit Tagihan 1'
        );

        $app2 = FinancialApprovalService::createRequest(
            $this->financeUser,
            'edit',
            'tagihan',
            $this->tagihan2,
            ['nominal' => 260000],
            'Alasan 2',
            'Edit Tagihan 2'
        );

        $app3 = FinancialApprovalService::createRequest(
            $this->financeUser,
            'edit',
            'tagihan',
            $this->tagihan3,
            ['nominal' => 270000],
            'Alasan 3',
            'Edit Tagihan 3'
        );

        // Langsung setujui app3 di awal agar statusnya bukan menunggu
        FinancialApprovalService::approve($app3, $this->superAdmin, 'Sudah oke');

        $this->actingAs($this->superAdmin);

        Livewire::test(ApprovalKeuanganIndex::class)
            ->set('selectAll', true)
            ->assertCount('selectedIds', 2)
            ->assertSet('selectedIds', [$app1->id, $app2->id]);
    }

    public function test_super_admin_can_bulk_approve_pending_requests(): void
    {
        $app1 = FinancialApprovalService::createRequest(
            $this->financeUser,
            'edit',
            'tagihan',
            $this->tagihan1,
            ['nominal' => 250000],
            'Alasan penyesuaian 1',
            'Edit Tagihan 1'
        );

        $app2 = FinancialApprovalService::createRequest(
            $this->financeUser,
            'edit',
            'tagihan',
            $this->tagihan2,
            ['nominal' => 260000],
            'Alasan penyesuaian 2',
            'Edit Tagihan 2'
        );

        $this->actingAs($this->superAdmin);

        Livewire::test(ApprovalKeuanganIndex::class)
            ->set('selectedIds', [$app1->id, $app2->id])
            ->call('openBulkApproveModal')
            ->assertSet('showBulkApproveModal', true)
            ->set('bulkApprovalNote', 'Persetujuan massal terverifikasi')
            ->call('bulkApprove')
            ->assertSet('showBulkApproveModal', false)
            ->assertCount('selectedIds', 0)
            ->assertDispatched('show-alert');

        $app1->refresh();
        $app2->refresh();
        $this->assertEquals('disetujui', $app1->status);
        $this->assertEquals('disetujui', $app2->status);
        $this->assertEquals($this->superAdmin->id, $app1->disetujui_oleh);
        $this->assertEquals($this->superAdmin->id, $app2->disetujui_oleh);

        // Verifikasi target nominal tagihan telah terupdate
        $this->tagihan1->refresh();
        $this->tagihan2->refresh();
        $this->assertEquals(250000, floatval($this->tagihan1->nominal));
        $this->assertEquals(260000, floatval($this->tagihan2->nominal));
    }

    public function test_finance_can_bulk_cancel_pending_requests(): void
    {
        $app1 = FinancialApprovalService::createRequest(
            $this->financeUser,
            'edit',
            'tagihan',
            $this->tagihan1,
            ['nominal' => 300000],
            'Koreksi',
            'Pengajuan 1'
        );

        $app2 = FinancialApprovalService::createRequest(
            $this->financeUser,
            'edit',
            'tagihan',
            $this->tagihan2,
            ['nominal' => 350000],
            'Koreksi',
            'Pengajuan 2'
        );

        $this->actingAs($this->financeUser);

        Livewire::test(ApprovalKeuanganIndex::class)
            ->set('selectedIds', [$app1->id, $app2->id])
            ->call('openBulkCancelModal')
            ->assertSet('showBulkCancelModal', true)
            ->set('bulkCancelReason', 'Dibatalkan serentak karena revisi berkas')
            ->call('bulkCancel')
            ->assertSet('showBulkCancelModal', false)
            ->assertCount('selectedIds', 0)
            ->assertDispatched('show-alert');

        $app1->refresh();
        $app2->refresh();
        $this->assertEquals('dibatalkan', $app1->status);
        $this->assertEquals('dibatalkan', $app2->status);
        $this->assertEquals('Dibatalkan serentak karena revisi berkas', $app1->catatan_approval);
        $this->assertEquals('Dibatalkan serentak karena revisi berkas', $app2->catatan_approval);

        // Tagihan asli tidak berubah
        $this->tagihan1->refresh();
        $this->tagihan2->refresh();
        $this->assertEquals(200000, floatval($this->tagihan1->nominal));
        $this->assertEquals(200000, floatval($this->tagihan2->nominal));
    }

    public function test_unauthorized_user_cannot_bulk_approve(): void
    {
        $app1 = FinancialApprovalService::createRequest(
            $this->financeUser,
            'edit',
            'tagihan',
            $this->tagihan1,
            ['nominal' => 300000],
            'Koreksi',
            'Pengajuan 1'
        );

        // Login as non-admin (finance user cannot approve)
        $this->actingAs($this->financeUser);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unauthorized action');

        Livewire::test(ApprovalKeuanganIndex::class)
            ->set('selectedIds', [$app1->id])
            ->call('bulkApprove');

        $app1->refresh();
        $this->assertEquals('menunggu', $app1->status);
    }
}
