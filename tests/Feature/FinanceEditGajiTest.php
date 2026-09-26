<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Guru;
use App\Models\GajiGuru;
use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;
use App\Models\Peminjaman;
use App\Models\ApprovalKeuangan;
use App\Services\FinancialApprovalService;
use Livewire\Livewire;
use App\Livewire\Finance\ManajemenGajiGuru;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FinanceEditGajiTest extends TestCase
{
    use RefreshDatabase;

    protected User $financeUser;
    protected User $superAdmin;
    protected User $superAdmin2;
    protected Guru $guru;

    protected function setUp(): void
    {
        parent::setUp();

        $roleAdmin = Role::firstOrCreate(['nama' => 'super_admin'], ['deskripsi' => 'Super Administrator']);
        $roleAdmin2 = Role::firstOrCreate(['nama' => 'super_admin_2'], ['deskripsi' => 'Super Administrator 2']);
        $roleGuru = Role::firstOrCreate(['nama' => 'guru'], ['deskripsi' => 'Guru']);
        $roleFinance = Role::firstOrCreate(['nama' => 'finance'], ['deskripsi' => 'Staff Keuangan']);

        $this->superAdmin = User::factory()->create([
            'username' => 'admin_' . uniqid(),
            'nama' => 'Super Admin Test',
            'role_id' => $roleAdmin->id,
            'status' => 'aktif',
        ]);

        $this->superAdmin2 = User::factory()->create([
            'username' => 'admin2_' . uniqid(),
            'nama' => 'Super Admin 2 Test',
            'role_id' => $roleAdmin2->id,
            'status' => 'aktif',
        ]);

        $guruUser = User::factory()->create([
            'username' => 'guru_' . uniqid(),
            'nama' => 'Ustadz Abdullah',
            'role_id' => $roleGuru->id,
            'status' => 'aktif',
        ]);

        $this->guru = Guru::create([
            'user_id' => $guruUser->id,
            'nip' => '19850101',
            'jenis_kelamin' => 'L',
            'status_aktif' => true,
            'tanggal_masuk' => now(),
        ]);

        $this->financeUser = User::factory()->create([
            'username' => 'finance_' . uniqid(),
            'nama' => 'Staff Keuangan Test',
            'role_id' => $roleFinance->id,
            'status' => 'aktif',
        ]);
    }

    /** 1. Draft salary edited by finance saves directly */
    public function test_finance_edits_draft_salary_directly_saves(): void
    {
        $gaji = GajiGuru::create([
            'guru_id' => $this->guru->id,
            'bulan' => 'Januari',
            'tahun' => 2026,
            'gaji_pokok' => 2000000,
            'total_bruto' => 2000000,
            'total_diterima' => 2000000,
            'status' => 'draft',
            'tanggal_bayar' => '2026-01-25',
        ]);

        Livewire::actingAs($this->financeUser)
            ->test(ManajemenGajiGuru::class)
            ->call('openEditModal', $gaji->id)
            ->set('editGajiPokok', 3000000)
            ->call('saveEdit')
            ->assertHasNoErrors()
            ->assertSet('showEditModal', false);

        $this->assertDatabaseHas('gaji_guru', [
            'id' => $gaji->id,
            'gaji_pokok' => 3000000,
            'total_diterima' => 2990000, // 3.000.000 - 10.000 default potongan sosial
        ]);

        // No approval request created for draft
        $this->assertEquals(0, ApprovalKeuangan::count());
    }

    /** 2. Paid salary edited by finance routes to approval without failing validation */
    public function test_finance_edits_paid_salary_creates_approval_request(): void
    {
        $kategori = KategoriPengeluaran::firstOrCreate(['nama' => 'Gaji']);
        $pengeluaran = Pengeluaran::create([
            'kategori_pengeluaran_id' => $kategori->id,
            'jumlah' => 2500000,
            'tanggal' => '2026-01-25',
            'keterangan' => 'Pembayaran Gaji Ustadz Abdullah (Januari 2026)',
            'petugas_id' => $this->financeUser->id,
        ]);

        $gaji = GajiGuru::create([
            'guru_id' => $this->guru->id,
            'bulan' => 'Januari',
            'tahun' => 2026,
            'gaji_pokok' => 2500000,
            'total_bruto' => 2500000,
            'total_diterima' => 2500000,
            'status' => 'dibayar',
            'tanggal_bayar' => '2026-01-25',
            'pengeluaran_id' => $pengeluaran->id,
        ]);

        // Finance user edits salary with pre-filled or customized reason and sets BPJS incentive
        Livewire::actingAs($this->financeUser)
            ->test(ManajemenGajiGuru::class)
            ->call('openEditModal', $gaji->id)
            ->assertSet('editStatus', 'dibayar')
            ->set('editGajiPokok', 3500000)
            ->set('editInsentifBpjs', 150000)
            ->set('edit_alasan', 'Penyesuaian kenaikan tunjangan dan gaji pokok')
            ->call('saveEdit')
            ->assertHasNoErrors()
            ->assertSet('showEditModal', false);

        // An approval request was created
        $approval = ApprovalKeuangan::where('model_id', $gaji->id)
            ->where('tipe_aksi', 'edit')
            ->where('fitur', 'gaji_guru')
            ->first();

        $this->assertNotNull($approval);
        $this->assertEquals('menunggu', $approval->status);
        $this->assertEquals($this->financeUser->id, $approval->pemohon_id);
        $this->assertEquals(3500000, $approval->data_baru['gaji_pokok']);

        // Salary in DB remains old value pending approval
        $gaji->refresh();
        $this->assertEquals(2500000, floatval($gaji->gaji_pokok));

        // 3. Super Admin approves the request -> salary in DB updates and syncs pengeluaran
        FinancialApprovalService::approve($approval, $this->superAdmin, 'Disetujui Super Admin');

        $approval->refresh();
        $this->assertEquals('disetujui', $approval->status);

        $gaji->refresh();
        $this->assertEquals(3500000, floatval($gaji->gaji_pokok));
        $this->assertEquals(150000, floatval($gaji->insentif_bpjs));
        $this->assertEquals(3640000, floatval($gaji->total_diterima)); // 3.500.000 + 150.000 - 10.000
        $pengeluaran->refresh();
        $this->assertEquals(3640000, floatval($pengeluaran->jumlah));
    }

    /** 4. Paid salary edited by super admin saves directly without approval */
    public function test_super_admin_edits_paid_salary_directly_saves(): void
    {
        $kategori = KategoriPengeluaran::firstOrCreate(['nama' => 'Gaji']);
        $pengeluaran = Pengeluaran::create([
            'kategori_pengeluaran_id' => $kategori->id,
            'jumlah' => 2000000,
            'tanggal' => '2026-01-25',
            'keterangan' => 'Pembayaran Gaji Ustadz Abdullah (Januari 2026)',
            'petugas_id' => $this->superAdmin->id,
        ]);

        $gaji = GajiGuru::create([
            'guru_id' => $this->guru->id,
            'bulan' => 'Januari',
            'tahun' => 2026,
            'gaji_pokok' => 2000000,
            'total_bruto' => 2000000,
            'total_diterima' => 2000000,
            'status' => 'dibayar',
            'tanggal_bayar' => '2026-01-25',
            'pengeluaran_id' => $pengeluaran->id,
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(ManajemenGajiGuru::class)
            ->call('openEditModal', $gaji->id)
            ->set('editGajiPokok', 2800000)
            ->call('saveEdit')
            ->assertHasNoErrors()
            ->assertSet('showEditModal', false);

        $gaji->refresh();
        $this->assertEquals(2800000, floatval($gaji->gaji_pokok));

        $pengeluaran->refresh();
        $this->assertEquals(2790000, floatval($pengeluaran->jumlah));
    }

    /** 5. Paid salary edited by finance can be approved by Super Admin 2 */
    public function test_super_admin_2_can_approve_paid_salary_edit(): void
    {
        $kategori = KategoriPengeluaran::firstOrCreate(['nama' => 'Gaji']);
        $pengeluaran = Pengeluaran::create([
            'kategori_pengeluaran_id' => $kategori->id,
            'jumlah' => 2000000,
            'tanggal' => '2026-01-25',
            'keterangan' => 'Pembayaran Gaji Ustadz Abdullah (Januari 2026)',
            'petugas_id' => $this->financeUser->id,
        ]);

        $gaji = GajiGuru::create([
            'guru_id' => $this->guru->id,
            'bulan' => 'Januari',
            'tahun' => 2026,
            'gaji_pokok' => 2000000,
            'total_bruto' => 2000000,
            'total_diterima' => 2000000,
            'status' => 'dibayar',
            'tanggal_bayar' => '2026-01-25',
            'pengeluaran_id' => $pengeluaran->id,
        ]);

        Livewire::actingAs($this->financeUser)
            ->test(ManajemenGajiGuru::class)
            ->call('openEditModal', $gaji->id)
            ->set('editGajiPokok', 3100000)
            ->call('saveEdit')
            ->assertHasNoErrors()
            ->assertSet('showEditModal', false);

        $approval = ApprovalKeuangan::where('model_id', $gaji->id)->first();
        $this->assertNotNull($approval);

        // Super Admin 2 approves
        FinancialApprovalService::approve($approval, $this->superAdmin2, 'Disetujui Super Admin 2');

        $gaji->refresh();
        $this->assertEquals(3100000, floatval($gaji->gaji_pokok));
        $this->assertEquals(3090000, floatval($gaji->total_diterima));

        $pengeluaran->refresh();
        $this->assertEquals(3090000, floatval($pengeluaran->jumlah));
    }

    /** 6. Manual creation of duplicate salary period shows validation error without DB crash */
    public function test_duplicate_salary_period_creation_does_not_crash_database(): void
    {
        GajiGuru::create([
            'guru_id' => $this->guru->id,
            'bulan' => 'September',
            'tahun' => 2026,
            'gaji_pokok' => 2000000,
            'total_bruto' => 2000000,
            'total_diterima' => 2000000,
            'status' => 'draft',
            'tanggal_bayar' => '2026-09-25',
        ]);

        Livewire::actingAs($this->financeUser)
            ->test(ManajemenGajiGuru::class)
            ->call('openCreateModal')
            ->set('createGuruId', $this->guru->id)
            ->set('createBulan', 'September')
            ->set('createTahun', 2026)
            ->set('createGajiPokok', 2500000)
            ->call('saveCreate')
            ->assertHasErrors(['createGuruId']);
    }

    /** 7. Bulk salary draft generator gracefully handles pre-existing and duplicate entries without 1062 exception */
    public function test_bulk_generator_gracefully_handles_duplicate_entries(): void
    {
        // Pre-create September 2026 for this guru
        GajiGuru::create([
            'guru_id' => $this->guru->id,
            'bulan' => 'September',
            'tahun' => 2026,
            'gaji_pokok' => 2000000,
            'total_bruto' => 2000000,
            'total_diterima' => 2000000,
            'status' => 'draft',
            'tanggal_bayar' => '2026-09-25',
        ]);

        $action = app(\App\Actions\Finance\GenerateBulkGajiAction::class);

        // Intentionally execute with items that include the already existing record
        $items = [
            [
                'guru_id' => $this->guru->id,
                'gaji_pokok' => 2000000,
                'gaji_berkala' => 120000,
                'insentif' => 500000,
                'insentif_bpjs' => 17928,
            ],
            // And duplicate identical row in the batch
            [
                'guru_id' => $this->guru->id,
                'gaji_pokok' => 2000000,
                'gaji_berkala' => 120000,
                'insentif' => 500000,
                'insentif_bpjs' => 17928,
            ],
        ];

        // Must execute cleanly without throwing 1062 UniqueConstraintViolationException
        $count = $action->execute($items, 'September', 2026);
        $this->assertEquals(0, $count);
    }
}
