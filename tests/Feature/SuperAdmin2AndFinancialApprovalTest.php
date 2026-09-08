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
use App\Models\Pembayaran;
use App\Models\ApprovalKeuangan;
use App\Services\FinancialApprovalService;
use Livewire\Livewire;
use App\Livewire\Finance\ApprovalKeuanganIndex;
use App\Livewire\Finance\ManajemenTagihan;
use App\Livewire\Finance\DetailTagihanSiswa;
use App\Livewire\SuperAdmin\TataKelola\ManajemenUser;
use App\Livewire\SuperAdmin\TataKelola\ManajemenKelas;
use App\Livewire\SuperAdmin\TataKelola\ManajemenKomponenNilai;
use App\Livewire\SuperAdmin\TataKelola\ManajemenPengaturan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SuperAdmin2AndFinancialApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $superAdmin2;
    protected User $financeUser;
    protected Siswa $siswa;
    protected JenisTagihan $jenisSpp;
    protected TahunAjaran $tahunAjaran;
    protected Semester $semester;
    protected Kelas $kelas;

    protected function setUp(): void
    {
        parent::setUp();

        $roleAdmin = Role::firstOrCreate(['nama' => 'super_admin'], ['deskripsi' => 'Super Administrator']);
        $roleAdmin2 = Role::firstOrCreate(['nama' => 'super_admin_2'], ['deskripsi' => 'Super Administrator 2 (Read Only & Approver)']);
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
            'nama' => 'Dra. Hj. Nurul Hidayati',
            'role_id' => $roleAdmin2->id,
            'status' => 'aktif',
        ]);

        $this->financeUser = User::factory()->create([
            'username' => 'finance_user_' . uniqid(),
            'nama' => 'Staff Keuangan Utama',
            'role_id' => $roleFinance->id,
            'status' => 'aktif',
        ]);

        $this->tahunAjaran = TahunAjaran::create([
            'nama' => '2026/2027',
            'status_aktif' => true,
        ]);

        $this->semester = Semester::create([
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'semester' => 'Ganjil',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2026-12-31',
            'status_aktif' => true,
        ]);

        $this->kelas = Kelas::create([
            'nama_kelas' => '7A',
            'jenis_kelas' => 'umum',
            'tingkat' => 7,
            'semester_id' => $this->semester->id,
        ]);

        $userMurid = User::factory()->create([
            'username' => 'siswa_' . uniqid(),
            'role_id' => $roleMurid->id,
            'nama' => 'Ahmad Santoso',
            'status' => 'aktif',
        ]);

        $this->siswa = Siswa::create([
            'user_id' => $userMurid->id,
            'nis' => '1001',
            'nisn' => '0000001001',
            'jenis_kelamin' => 'L',
            'tanggal_masuk' => '2026-07-01',
            'kelas_id' => $this->kelas->id,
            'status' => 'aktif',
            'saldo_deposit' => 0.00,
        ]);

        $this->jenisSpp = JenisTagihan::create([
            'nama' => 'SPP Bulanan',
            'kategori' => 'rutin',
            'default_nominal' => 350000,
            'is_blocking' => true,
        ]);
    }

    public function test_super_admin_2_can_login_and_access_dashboard_and_modules(): void
    {
        $this->actingAs($this->superAdmin2);

        // Can access super-admin dashboard
        $response = $this->get(route('super-admin.dashboard'));
        $response->assertStatus(200);

        // Can access approval keuangan page
        $approvalResponse = $this->get(route('super-admin.approval-keuangan'));
        $approvalResponse->assertStatus(200);

        // Can access finance modules for viewing
        $tagihanResponse = $this->get(route('finance.tagihan'));
        $tagihanResponse->assertStatus(200);

        // Model helper checks
        $this->assertTrue($this->superAdmin2->isSuperAdmin2());
        $this->assertTrue($this->superAdmin2->isReadOnlyAdmin());
        $this->assertTrue($this->superAdmin2->canApproveFinancial());
        $this->assertFalse($this->superAdmin2->isSuperAdmin());
    }

    public function test_super_admin_2_is_blocked_from_mutating_master_data(): void
    {
        $this->actingAs($this->superAdmin2);

        // 1. Cannot create user in ManajemenUser
        Livewire::test(ManajemenUser::class)
            ->set('nama', 'User Palsu')
            ->set('username', 'palsu_' . uniqid())
            ->set('role_id', $this->financeUser->role_id)
            ->set('password', 'secret123')
            ->call('save')
            ->assertSee('Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');

        // 2. Cannot save class in ManajemenKelas
        Livewire::test(ManajemenKelas::class)
            ->set('nama_kelas', 'Kelas Palsu')
            ->set('tingkat', 8)
            ->call('save')
            ->assertSee('Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');

        // 3. Cannot save component in ManajemenKomponenNilai
        Livewire::test(ManajemenKomponenNilai::class)
            ->set('nama', 'Komponen Palsu')
            ->call('saveForm')
            ->assertSee('Akses ditolak: Akun Super Admin 2 hanya memiliki hak akses lihat.');

        // 4. Cannot save settings in ManajemenPengaturan
        Livewire::test(ManajemenPengaturan::class)
            ->call('save')
            ->assertSee('Akses ditolak: Akun Super Admin 2 hanya memiliki hak akses lihat.');
    }

    public function test_finance_edit_tagihan_creates_approval_request_without_immediate_change(): void
    {
        $this->actingAs($this->financeUser);

        $tagihan = Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'bulan' => 'Agustus',
            'nominal' => 350000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => '2026-08-10',
        ]);

        // Finance attempts to edit tagihan nominal to 400.000
        Livewire::test(ManajemenTagihan::class)
            ->call('openEditModal', $tagihan->id)
            ->set('edit_nominal', 400000)
            ->set('edit_alasan', 'Perubahan biaya sesuai instruksi yayasan')
            ->call('saveEditTagihan')
            ->assertHasNoErrors()
            ->assertSee('Permohonan edit tagihan telah diajukan ke Super Admin / Super Admin 2 untuk disetujui.');

        // Verify original tagihan nominal is UNCHANGED
        $tagihan->refresh();
        $this->assertEquals(350000, floatval($tagihan->nominal));

        // Verify ApprovalKeuangan record created
        $approval = ApprovalKeuangan::where('fitur', 'tagihan')
            ->where('model_id', $tagihan->id)
            ->where('tipe_aksi', 'edit')
            ->first();

        $this->assertNotNull($approval);
        $this->assertEquals('menunggu', $approval->status);
        $this->assertEquals($this->financeUser->id, $approval->pemohon_id);
        $this->assertEquals(350000, floatval($approval->data_lama['nominal']));
        $this->assertEquals(400000, floatval($approval->data_baru['nominal']));
    }

    public function test_finance_delete_tagihan_creates_approval_request(): void
    {
        $this->actingAs($this->financeUser);

        $tagihan = Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'bulan' => 'September',
            'nominal' => 350000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => '2026-09-10',
        ]);

        Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
            ->call('deleteTagihan', $tagihan->id, 'Tagihan ganda tidak sengaja dibuat')
            ->assertHasNoErrors()
            ->assertSee('Permohonan penghapusan tagihan telah diajukan ke Super Admin / Super Admin 2 untuk disetujui.');

        // Original tagihan is NOT deleted yet
        $this->assertDatabaseHas('tagihan', ['id' => $tagihan->id, 'deleted_at' => null]);

        $approval = ApprovalKeuangan::where('fitur', 'tagihan')
            ->where('model_id', $tagihan->id)
            ->where('tipe_aksi', 'hapus')
            ->first();

        $this->assertNotNull($approval);
        $this->assertEquals('menunggu', $approval->status);
    }

    public function test_finance_delete_payment_creates_approval_request(): void
    {
        $this->actingAs($this->financeUser);

        $tagihan = Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'bulan' => 'Oktober',
            'nominal' => 350000,
            'total_dibayar' => 350000,
            'status' => 'lunas',
            'jatuh_tempo' => '2026-10-10',
        ]);

        $pembayaran = Pembayaran::create([
            'no_resi' => 'KW-TEST-APPROVAL-01',
            'tagihan_id' => $tagihan->id,
            'tanggal_bayar' => '2026-10-05',
            'nominal_dibayar' => 350000,
            'kelebihan_bayar' => 0,
            'metode_bayar' => 'Tunai',
            'petugas_id' => $this->financeUser->id,
        ]);

        Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
            ->call('deletePembayaran', $pembayaran->id, 'Salah input resi wali santri')
            ->assertHasNoErrors()
            ->assertSee('Permohonan pembatalan pembayaran telah diajukan ke Super Admin / Super Admin 2 untuk disetujui.');

        // Payment NOT deleted yet
        $this->assertDatabaseHas('pembayaran', ['id' => $pembayaran->id, 'deleted_at' => null]);

        $approval = ApprovalKeuangan::where('fitur', 'pembayaran')
            ->where('model_id', $pembayaran->id)
            ->first();

        $this->assertNotNull($approval);
        $this->assertEquals('menunggu', $approval->status);
    }

    public function test_super_admin_can_approve_tagihan_edit_request(): void
    {
        $tagihan = Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'bulan' => 'November',
            'nominal' => 350000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => '2026-11-10',
        ]);

        $approval = FinancialApprovalService::createRequest(
            $this->financeUser,
            'edit',
            'tagihan',
            $tagihan,
            ['nominal' => 250000, 'keterangan' => 'Diskon beasiswa disetujui'],
            'Keringanan SPP',
            'Edit Tagihan SPP November'
        );

        $this->actingAs($this->superAdmin);

        // Super admin approves via Livewire ApprovalKeuanganIndex
        Livewire::test(ApprovalKeuanganIndex::class)
            ->call('openApproveModal', $approval->id)
            ->call('approve')
            ->assertHasNoErrors();

        $approval->refresh();
        $this->assertEquals('disetujui', $approval->status);
        $this->assertEquals($this->superAdmin->id, $approval->disetujui_oleh);

        // Verify tagihan nominal is updated to 250.000
        $tagihan->refresh();
        $this->assertEquals(250000, floatval($tagihan->nominal));
    }

    public function test_super_admin_2_can_approve_payment_deletion_and_it_executes_atomically(): void
    {
        $this->siswa->update(['saldo_deposit' => 10000]);

        $tagihan = Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'bulan' => 'Desember',
            'nominal' => 300000,
            'total_dibayar' => 300000,
            'status' => 'lunas',
            'jatuh_tempo' => '2026-12-10',
        ]);

        $pembayaran = Pembayaran::create([
            'no_resi' => 'KW-DEPOSIT-APP2',
            'tagihan_id' => $tagihan->id,
            'tanggal_bayar' => '2026-12-05',
            'nominal_dibayar' => 300000,
            'kelebihan_bayar' => 0,
            'metode_bayar' => 'Deposit',
            'petugas_id' => $this->financeUser->id,
        ]);

        $approval = FinancialApprovalService::createRequest(
            $this->financeUser,
            'hapus',
            'pembayaran',
            $pembayaran,
            null,
            'Pembatalan transaksi deposit',
            'Hapus Pembayaran KW-DEPOSIT-APP2'
        );

        $this->actingAs($this->superAdmin2);

        // Super Admin 2 approves the request!
        Livewire::test(ApprovalKeuanganIndex::class)
            ->call('openApproveModal', $approval->id)
            ->call('approve')
            ->assertHasNoErrors();

        $approval->refresh();
        $this->assertEquals('disetujui', $approval->status);
        $this->assertEquals($this->superAdmin2->id, $approval->disetujui_oleh);

        // Pembayaran is soft deleted
        $this->assertSoftDeleted('pembayaran', ['id' => $pembayaran->id]);

        // Tagihan status reset to belum_bayar
        $tagihan->refresh();
        $this->assertEquals(0, floatval($tagihan->total_dibayar));
        $this->assertEquals('belum_bayar', $tagihan->status);

        // Deposit was refunded (10.000 + 300.000 = 310.000)
        $this->siswa->refresh();
        $this->assertEquals(310000, floatval($this->siswa->saldo_deposit));
    }

    public function test_super_admin_2_can_reject_request_with_reason(): void
    {
        $tagihan = Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'bulan' => 'Januari',
            'nominal' => 350000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => '2027-01-10',
        ]);

        $approval = FinancialApprovalService::createRequest(
            $this->financeUser,
            'hapus',
            'tagihan',
            $tagihan,
            null,
            'Minta dihapus',
            'Hapus Tagihan Siswa'
        );

        $this->actingAs($this->superAdmin2);

        // Reject request
        Livewire::test(ApprovalKeuanganIndex::class)
            ->call('openRejectModal', $approval->id)
            ->set('rejectReason', 'Bukti dokumen pendukung belum lengkap.')
            ->call('reject')
            ->assertHasNoErrors();

        $approval->refresh();
        $this->assertEquals('ditolak', $approval->status);
        $this->assertEquals($this->superAdmin2->id, $approval->disetujui_oleh);
        $this->assertEquals('Bukti dokumen pendukung belum lengkap.', $approval->catatan_approval);

        // Tagihan remains untouched
        $this->assertDatabaseHas('tagihan', ['id' => $tagihan->id, 'deleted_at' => null]);
    }

    public function test_finance_user_cannot_approve_or_reject_requests(): void
    {
        $tagihan = Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'bulan' => 'Februari',
            'nominal' => 350000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => '2027-02-10',
        ]);

        $approval = FinancialApprovalService::createRequest(
            $this->financeUser,
            'hapus',
            'tagihan',
            $tagihan,
            null,
            'Permintaan sepihak',
            'Hapus Tagihan Siswa'
        );

        $this->actingAs($this->financeUser);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unauthorized action');

        Livewire::test(ApprovalKeuanganIndex::class)
            ->call('openApproveModal', $approval->id);
    }
}
