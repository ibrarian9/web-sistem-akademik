<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\DanaBos;
use App\Models\TahunAjaran;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KepalaSekolahDanaBosMonitoringTest extends TestCase
{
    use RefreshDatabase;

    protected User $kepalaSekolah;
    protected User $superAdmin;
    protected User $financeUser;
    protected TahunAjaran $tahunAjaran;
    protected DanaBos $transaksi1;
    protected DanaBos $transaksi2;

    protected function setUp(): void
    {
        parent::setUp();

        $roleKepsek = Role::firstOrCreate(['nama' => 'kepala_sekolah'], ['deskripsi' => 'Kepala Sekolah']);
        $roleAdmin = Role::firstOrCreate(['nama' => 'super_admin'], ['deskripsi' => 'Super Administrator']);
        $roleFinance = Role::firstOrCreate(['nama' => 'finance'], ['deskripsi' => 'Staff Keuangan']);

        $this->kepalaSekolah = User::factory()->create([
            'username' => 'kepsek_' . uniqid(),
            'nama' => 'Drs. H. Ahmad Fauzi, M.Pd.',
            'role_id' => $roleKepsek->id,
            'status' => 'aktif',
        ]);

        $this->superAdmin = User::factory()->create([
            'username' => 'admin_' . uniqid(),
            'nama' => 'Super Admin Utama',
            'role_id' => $roleAdmin->id,
            'status' => 'aktif',
        ]);

        $this->financeUser = User::factory()->create([
            'username' => 'finance_' . uniqid(),
            'nama' => 'Staff Keuangan',
            'role_id' => $roleFinance->id,
            'status' => 'aktif',
        ]);

        $this->tahunAjaran = TahunAjaran::firstOrCreate(
            ['nama' => '2026/2027'],
            ['status_aktif' => true]
        );

        $this->transaksi1 = DanaBos::create([
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'jenis' => 'masuk',
            'tanggal' => date('Y-m-d'),
            'nominal' => 25000000,
            'kategori' => 'Pencairan BOS Reguler Tahap 1',
            'keterangan' => 'Pencairan dana BOS tahap 1 tahun anggaran 2026',
        ]);

        $this->transaksi2 = DanaBos::create([
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'jenis' => 'keluar',
            'tanggal' => date('Y-m-d'),
            'nominal' => 5000000,
            'kategori' => 'Pengadaan Modul Pembelajaran',
            'keterangan' => 'Belanja buku dan modul kurikulum merdeka',
        ]);
    }

    public function test_helper_is_kepala_sekolah_returns_true(): void
    {
        $this->assertTrue($this->kepalaSekolah->isKepalaSekolah());
        $this->assertFalse($this->superAdmin->isKepalaSekolah());
        $this->assertFalse($this->financeUser->isKepalaSekolah());
    }

    public function test_kepala_sekolah_can_access_dana_bos_monitoring_page(): void
    {
        $this->actingAs($this->kepalaSekolah);

        $response = $this->get(route('kepala-sekolah.dana-bos'));
        $response->assertStatus(200);
        $response->assertSee('MONITORING KEPALA SEKOLAH');
        $response->assertSee('Pencairan BOS Reguler Tahap 1');
        $response->assertSee('Pengadaan Modul Pembelajaran');
        $response->assertSee('Rp 25.000.000');
        $response->assertSee('Rp 5.000.000');
        $response->assertSee('Lihat Saja');
        $response->assertDontSee('Catat Penerimaan BOS');
        $response->assertDontSee('Catat Belanja BOS');
    }

    public function test_kepala_sekolah_is_blocked_from_opening_create_modal(): void
    {
        $this->actingAs($this->kepalaSekolah);

        Livewire::test(\App\Livewire\Finance\DanaBos::class)
            ->call('openCreateModal', 'masuk')
            ->assertSet('showCreateModal', false)
            ->assertSee('Akses Ditolak: Anda hanya memiliki hak akses pemantauan (Lihat Saja).');
    }

    public function test_kepala_sekolah_is_blocked_from_saving_new_transaction(): void
    {
        $this->actingAs($this->kepalaSekolah);

        $initialCount = DanaBos::count();

        Livewire::test(\App\Livewire\Finance\DanaBos::class)
            ->set('jenis', 'masuk')
            ->set('nominal', 10000000)
            ->set('kategori', 'Dana Siluman')
            ->set('keterangan', 'Tidak boleh tersimpan')
            ->set('tanggal', date('Y-m-d'))
            ->call('saveTransaction')
            ->assertSee('Akses Ditolak: Anda hanya memiliki hak akses pemantauan (Lihat Saja).');

        $this->assertEquals($initialCount, DanaBos::count());
    }

    public function test_kepala_sekolah_is_blocked_from_deleting_transaction(): void
    {
        $this->actingAs($this->kepalaSekolah);

        Livewire::test(\App\Livewire\Finance\DanaBos::class)
            ->call('deleteTransaction', $this->transaksi1->id)
            ->assertSee('Akses Ditolak: Anda hanya memiliki hak akses pemantauan (Lihat Saja).');

        $this->assertDatabaseHas('dana_bos', ['id' => $this->transaksi1->id]);
    }

    public function test_kepala_sekolah_is_blocked_from_bulk_delete(): void
    {
        $this->actingAs($this->kepalaSekolah);

        Livewire::test(\App\Livewire\Finance\DanaBos::class)
            ->set('selectedIds', [(string) $this->transaksi1->id, (string) $this->transaksi2->id])
            ->call('bulkDelete')
            ->assertSee('Akses Ditolak: Anda hanya memiliki hak akses pemantauan (Lihat Saja).');

        $this->assertDatabaseHas('dana_bos', ['id' => $this->transaksi1->id]);
        $this->assertDatabaseHas('dana_bos', ['id' => $this->transaksi2->id]);
    }

    public function test_kepala_sekolah_can_export_dana_bos_pdf_and_excel(): void
    {
        $this->actingAs($this->kepalaSekolah);

        $pdfResponse = $this->get(route('kepala-sekolah.dana-bos.pdf'));
        $pdfResponse->assertOk();
        $pdfResponse->assertHeader('Content-Type', 'application/pdf');

        $excelResponse = $this->get(route('kepala-sekolah.dana-bos.excel'));
        $excelResponse->assertOk();
        $excelResponse->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_sidebar_displays_dana_bos_menu_for_kepala_sekolah(): void
    {
        $this->actingAs($this->kepalaSekolah);

        $response = $this->get(route('kepala-sekolah.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Dana BOS (Pemantauan)');
        $response->assertSee(route('kepala-sekolah.dana-bos'));
    }
}
