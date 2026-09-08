<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\DanaBos;
use App\Models\TahunAjaran;
use Livewire\Livewire;
use App\Livewire\Finance\DanaBos as DanaBosComponent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DanaBosBuktiTransaksiTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $financeUser;
    protected User $kepalaSekolah;
    protected TahunAjaran $tahunAjaran;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $roleAdmin = Role::firstOrCreate(['nama' => 'super_admin'], ['deskripsi' => 'Super Administrator']);
        $roleFinance = Role::firstOrCreate(['nama' => 'finance'], ['deskripsi' => 'Staff Keuangan']);
        $roleKepsek = Role::firstOrCreate(['nama' => 'kepala_sekolah'], ['deskripsi' => 'Kepala Sekolah']);

        $this->superAdmin = User::factory()->create([
            'username' => 'admin_bos_' . uniqid(),
            'nama' => 'Super Admin BOS',
            'role_id' => $roleAdmin->id,
            'status' => 'aktif',
        ]);

        $this->financeUser = User::factory()->create([
            'username' => 'finance_bos_' . uniqid(),
            'nama' => 'Staff Keuangan BOS',
            'role_id' => $roleFinance->id,
            'status' => 'aktif',
        ]);

        $this->kepalaSekolah = User::factory()->create([
            'username' => 'kepsek_bos_' . uniqid(),
            'nama' => 'Kepala Sekolah Monitoring',
            'role_id' => $roleKepsek->id,
            'status' => 'aktif',
        ]);

        $this->tahunAjaran = TahunAjaran::firstOrCreate(
            ['nama' => '2026/2027'],
            ['status_aktif' => true]
        );
    }

    public function test_catat_penerimaan_dana_bos_dapat_mengunggah_foto_bukti_transfer(): void
    {
        $fakeFile = UploadedFile::fake()->image('bukti_transfer_bos_tahap1.jpg', 600, 400);

        Livewire::actingAs($this->financeUser)
            ->test(DanaBosComponent::class)
            ->call('openCreateModal', 'masuk')
            ->set('kategori', 'BOS Reguler Tahap 1')
            ->set('nominal', 25000000)
            ->set('tanggal', '2026-09-01')
            ->set('keterangan', 'Pencairan dana BOS Reguler dari Kas Daerah')
            ->set('bukti_foto', $fakeFile)
            ->call('saveTransaction')
            ->assertHasNoErrors()
            ->assertSet('showCreateModal', false);

        $tx = DanaBos::where('kategori', 'BOS Reguler Tahap 1')->first();
        $this->assertNotNull($tx);
        $this->assertEquals('masuk', $tx->jenis);
        $this->assertEquals(25000000, floatval($tx->nominal));
        $this->assertNotNull($tx->bukti);
        Storage::disk('public')->assertExists($tx->bukti);
    }

    public function test_catat_belanja_dana_bos_dapat_mengunggah_foto_nota_struk(): void
    {
        $fakeFile = UploadedFile::fake()->image('nota_belanja_buku.png', 500, 500);

        Livewire::actingAs($this->financeUser)
            ->test(DanaBosComponent::class)
            ->call('openCreateModal', 'keluar')
            ->set('kategori', 'Belanja Buku Teks Pelajaran')
            ->set('nominal', 4500000)
            ->set('tanggal', '2026-09-02')
            ->set('keterangan', 'Pembelian buku teks pelajaran kurikulum merdeka via Siplah')
            ->set('bukti_foto', $fakeFile)
            ->call('saveTransaction')
            ->assertHasNoErrors()
            ->assertSet('showCreateModal', false);

        $tx = DanaBos::where('kategori', 'Belanja Buku Teks Pelajaran')->first();
        $this->assertNotNull($tx);
        $this->assertEquals('keluar', $tx->jenis);
        $this->assertEquals(4500000, floatval($tx->nominal));
        $this->assertNotNull($tx->bukti);
        Storage::disk('public')->assertExists($tx->bukti);
    }

    public function test_catat_transaksi_tanpa_foto_tetap_berhasil_opsional(): void
    {
        Livewire::actingAs($this->financeUser)
            ->test(DanaBosComponent::class)
            ->call('openCreateModal', 'masuk')
            ->set('kategori', 'BOS Kinerja')
            ->set('nominal', 15000000)
            ->set('tanggal', '2026-09-03')
            ->set('keterangan', 'Penerimaan BOS Kinerja tanpa lampiran nota fisik')
            ->call('saveTransaction')
            ->assertHasNoErrors();

        $tx = DanaBos::where('kategori', 'BOS Kinerja')->first();
        $this->assertNotNull($tx);
        $this->assertNull($tx->bukti);
    }

    public function test_validasi_menolak_file_foto_lebih_dari_2mb(): void
    {
        $fakeBigFile = UploadedFile::fake()->image('foto_sangat_besar.jpg')->size(2500);

        Livewire::actingAs($this->financeUser)
            ->test(DanaBosComponent::class)
            ->call('openCreateModal', 'keluar')
            ->set('kategori', 'Belanja Sarana')
            ->set('nominal', 1000000)
            ->set('tanggal', '2026-09-04')
            ->set('keterangan', 'Uji validasi ukuran foto')
            ->set('bukti_foto', $fakeBigFile)
            ->call('saveTransaction')
            ->assertHasErrors(['bukti_foto']);

        $this->assertDatabaseMissing('dana_bos', [
            'kategori' => 'Belanja Sarana',
        ]);
    }

    public function test_dapat_memperbarui_transaksi_dan_mengganti_foto_bukti(): void
    {
        $initialPhoto = UploadedFile::fake()->image('nota_awal.jpg', 300, 300);
        $initialPath = $initialPhoto->store('bukti_dana_bos', 'public');

        $tx = DanaBos::create([
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'jenis' => 'keluar',
            'tanggal' => '2026-09-05',
            'nominal' => 2000000,
            'kategori' => 'Alat Tulis Kantor',
            'keterangan' => 'Pembelian ATK',
            'bukti' => $initialPath,
        ]);

        Storage::disk('public')->assertExists($initialPath);

        $newPhoto = UploadedFile::fake()->image('nota_revisi.jpg', 400, 400);

        Livewire::actingAs($this->financeUser)
            ->test(DanaBosComponent::class)
            ->call('openEditModal', $tx->id)
            ->assertSet('showEditModal', true)
            ->assertSet('edit_existing_bukti', $initialPath)
            ->set('edit_nominal', 2200000)
            ->set('edit_bukti_foto', $newPhoto)
            ->call('updateTransaction')
            ->assertHasNoErrors()
            ->assertSet('showEditModal', false);

        $tx->refresh();
        $this->assertEquals(2200000, floatval($tx->nominal));
        $this->assertNotEquals($initialPath, $tx->bukti);
        Storage::disk('public')->assertExists($tx->bukti);
        Storage::disk('public')->assertMissing($initialPath);
    }

    public function test_dapat_menghapus_foto_bukti_pada_transaksi_yang_sudah_ada(): void
    {
        $photo = UploadedFile::fake()->image('nota_hapus.jpg', 300, 300);
        $path = $photo->store('bukti_dana_bos', 'public');

        $tx = DanaBos::create([
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'jenis' => 'keluar',
            'tanggal' => '2026-09-06',
            'nominal' => 1500000,
            'kategori' => 'Kebersihan',
            'keterangan' => 'Belanja alat kebersihan',
            'bukti' => $path,
        ]);

        Storage::disk('public')->assertExists($path);

        Livewire::actingAs($this->financeUser)
            ->test(DanaBosComponent::class)
            ->call('openEditModal', $tx->id)
            ->call('deleteEditBukti')
            ->assertHasNoErrors()
            ->assertSet('edit_existing_bukti', null);

        $tx->refresh();
        $this->assertNull($tx->bukti);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_kepala_sekolah_dapat_melihat_pratinjau_bukti_tetapi_tidak_dapat_mengubah(): void
    {
        $photo = UploadedFile::fake()->image('nota_kepsek.jpg', 400, 400);
        $path = $photo->store('bukti_dana_bos', 'public');

        $tx = DanaBos::create([
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'jenis' => 'masuk',
            'tanggal' => '2026-09-07',
            'nominal' => 30000000,
            'kategori' => 'BOS Reguler',
            'keterangan' => 'Pencairan',
            'bukti' => $path,
        ]);

        // Kepala sekolah dapat membuka preview lightbox
        Livewire::actingAs($this->kepalaSekolah)
            ->test(DanaBosComponent::class)
            ->call('openPreviewBukti', $tx->bukti, 'BOS Reguler (MASUK)')
            ->assertSet('showPreviewBuktiModal', true)
            ->assertSet('previewBuktiUrl', asset('storage/' . $path))
            ->call('closePreviewBukti')
            ->assertSet('showPreviewBuktiModal', false);

        // Kepala sekolah diblokir saat mencoba membuka create atau edit modal
        Livewire::actingAs($this->kepalaSekolah)
            ->test(DanaBosComponent::class)
            ->call('openCreateModal', 'masuk')
            ->assertSet('showCreateModal', false)
            ->call('openEditModal', $tx->id)
            ->assertSet('showEditModal', false);
    }
}
