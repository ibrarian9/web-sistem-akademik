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
use Livewire\Livewire;
use App\Livewire\Finance\ManajemenTagihan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KategoriTagihanCustomManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $financeUser;
    protected User $superAdmin2;
    protected Kelas $kelas;
    protected Siswa $siswa;
    protected TahunAjaran $tahunAjaran;

    protected function setUp(): void
    {
        parent::setUp();

        $roleFinance = Role::firstOrCreate(['nama' => 'finance'], ['deskripsi' => 'Staff Keuangan']);
        $roleSuperAdmin2 = Role::firstOrCreate(['nama' => 'super_admin_2'], ['deskripsi' => 'Super Admin 2']);

        $this->financeUser = User::factory()->create([
            'nama' => 'Staff Keuangan Bendahara',
            'username' => 'finance_user_' . uniqid(),
            'role_id' => $roleFinance->id,
            'status' => 'aktif',
        ]);

        $this->superAdmin2 = User::factory()->create([
            'nama' => 'Pengawas Read Only',
            'username' => 'sa2_user_' . uniqid(),
            'role_id' => $roleSuperAdmin2->id,
            'status' => 'aktif',
        ]);

        $this->tahunAjaran = TahunAjaran::firstOrCreate(
            ['nama' => '2026/2027'],
            ['status_aktif' => true, 'tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2027-06-30']
        );

        $semester = \App\Models\Semester::firstOrCreate(
            ['tahun_ajaran_id' => $this->tahunAjaran->id, 'semester' => 'ganjil'],
            ['tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2026-12-31', 'status_aktif' => true]
        );

        $this->kelas = Kelas::firstOrCreate(
            ['nama_kelas' => '8A'],
            ['tingkat' => 8, 'kapasitas' => 30, 'semester_id' => $semester->id]
        );

        $studentUser = User::factory()->create(['nama' => 'Santri Budi', 'role_id' => Role::firstOrCreate(['nama' => 'murid'])->id]);
        $this->siswa = Siswa::create([
            'user_id' => $studentUser->id,
            'nis' => '8801',
            'kelas_id' => $this->kelas->id,
            'tanggal_masuk' => '2026-07-01',
            'status' => 'aktif',
        ]);
    }

    public function test_finance_can_open_and_view_kategori_modal(): void
    {
        JenisTagihan::create([
            'nama' => 'SPP Standar',
            'kategori' => 'rutin',
            'default_nominal' => 300000,
            'is_blocking' => true,
        ]);

        Livewire::actingAs($this->financeUser)
            ->test(ManajemenTagihan::class)
            ->call('openKategoriModal')
            ->assertSet('showKategoriModal', true)
            ->assertSee('Kelola Kategori Tagihan Siswa')
            ->assertSee('SPP Standar');
    }

    public function test_finance_can_create_new_custom_category(): void
    {
        Livewire::actingAs($this->financeUser)
            ->test(ManajemenTagihan::class)
            ->call('openKategoriModal')
            ->set('kategori_nama', 'Uang Sekolah')
            ->set('kategori_tipe', 'rutin')
            ->set('kategori_nominal', 275000)
            ->set('kategori_is_blocking', true)
            ->call('saveKategori')
            ->assertDispatched('show-alert', function ($name, $params) {
                $payload = $params[0] ?? $params;
                return ($payload['type'] ?? '') === 'create' && str_contains($payload['title'] ?? '', 'Ditambahkan');
            });

        $this->assertDatabaseHas('jenis_tagihan', [
            'nama' => 'Uang Sekolah',
            'kategori' => 'rutin',
            'default_nominal' => 275000.00,
            'is_blocking' => 1,
        ]);
    }

    public function test_validation_fails_for_empty_category_name(): void
    {
        Livewire::actingAs($this->financeUser)
            ->test(ManajemenTagihan::class)
            ->call('openKategoriModal')
            ->set('kategori_nama', '')
            ->set('kategori_tipe', 'rutin')
            ->set('kategori_nominal', 200000)
            ->call('saveKategori')
            ->assertHasErrors(['kategori_nama']);
    }

    public function test_finance_can_edit_existing_category(): void
    {
        $kategori = JenisTagihan::create([
            'nama' => 'Uang Gedung Awal',
            'kategori' => 'one_time',
            'default_nominal' => 1000000,
            'is_blocking' => true,
        ]);

        Livewire::actingAs($this->financeUser)
            ->test(ManajemenTagihan::class)
            ->call('openKategoriModal', $kategori->id)
            ->assertSet('editingKategoriId', $kategori->id)
            ->assertSet('kategori_nama', 'Uang Gedung Awal')
            ->set('kategori_nama', 'Uang Pembangunan Gedung Baru')
            ->set('kategori_nominal', 1250000)
            ->call('saveKategori')
            ->assertDispatched('show-alert', function ($name, $params) {
                $payload = $params[0] ?? $params;
                return ($payload['type'] ?? '') === 'edit';
            });

        $this->assertDatabaseHas('jenis_tagihan', [
            'id' => $kategori->id,
            'nama' => 'Uang Pembangunan Gedung Baru',
            'default_nominal' => 1250000.00,
        ]);
    }

    public function test_category_cannot_be_deleted_if_used_by_existing_bills(): void
    {
        $kategori = JenisTagihan::create([
            'nama' => 'Biaya Praktikum Lab',
            'kategori' => 'tahunan',
            'default_nominal' => 150000,
            'is_blocking' => false,
        ]);

        Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'jenis_tagihan_id' => $kategori->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'nominal' => 150000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => now()->addDays(10)->toDateString(),
        ]);

        Livewire::actingAs($this->financeUser)
            ->test(ManajemenTagihan::class)
            ->call('deleteKategori', $kategori->id)
            ->assertDispatched('show-alert', function ($name, $params) {
                $payload = $params[0] ?? $params;
                return ($payload['type'] ?? '') === 'warning' && str_contains($payload['title'] ?? '', 'Tidak Dapat Dihapus');
            });

        $this->assertDatabaseHas('jenis_tagihan', ['id' => $kategori->id]);
    }

    public function test_category_can_be_deleted_if_not_used(): void
    {
        $kategori = JenisTagihan::create([
            'nama' => 'Biaya Ekskul Musik',
            'kategori' => 'rutin',
            'default_nominal' => 50000,
            'is_blocking' => false,
        ]);

        Livewire::actingAs($this->financeUser)
            ->test(ManajemenTagihan::class)
            ->call('deleteKategori', $kategori->id)
            ->assertDispatched('show-alert', function ($name, $params) {
                $payload = $params[0] ?? $params;
                return ($payload['type'] ?? '') === 'delete';
            });

        $this->assertDatabaseMissing('jenis_tagihan', ['id' => $kategori->id]);
    }

    public function test_super_admin_2_cannot_manage_categories(): void
    {
        Livewire::actingAs($this->superAdmin2)
            ->test(ManajemenTagihan::class)
            ->call('openKategoriModal')
            ->assertDispatched('show-alert', function ($name, $params) {
                $payload = $params[0] ?? $params;
                return ($payload['type'] ?? '') === 'danger' && str_contains($payload['title'] ?? '', 'Akses Ditolak');
            });
    }

    public function test_newly_created_category_can_be_billed_to_students(): void
    {
        $kategori = JenisTagihan::create([
            'nama' => 'Uang Komite Sekolah',
            'kategori' => 'rutin',
            'default_nominal' => 75000,
            'is_blocking' => true,
        ]);

        Livewire::actingAs($this->financeUser)
            ->test(ManajemenTagihan::class)
            ->call('openCreateModal', $this->siswa->id)
            ->set('single_siswa_id', $this->siswa->id)
            ->set('jenis_tagihan_id', $kategori->id)
            ->set('nominal', 75000)
            ->set('bulan', 'Juli')
            ->set('periodeTipe', 'single')
            ->call('createSingleTagihan');

        $this->assertDatabaseHas('tagihan', [
            'siswa_id' => $this->siswa->id,
            'jenis_tagihan_id' => $kategori->id,
            'nominal' => 75000.00,
            'status' => 'belum_bayar',
        ]);
    }

    public function test_finance_can_create_category_with_semester_frequency(): void
    {
        Livewire::actingAs($this->financeUser)
            ->test(ManajemenTagihan::class)
            ->call('openKategoriModal')
            ->set('kategori_nama', 'Uang Kegiatan Semester')
            ->set('kategori_tipe', 'semester')
            ->set('kategori_nominal', 450000)
            ->set('kategori_is_blocking', true)
            ->call('saveKategori')
            ->assertHasNoErrors()
            ->assertDispatched('show-alert', function ($name, $params) {
                $payload = $params[0] ?? $params;
                return ($payload['type'] ?? '') === 'create' && str_contains($payload['title'] ?? '', 'Ditambahkan');
            });

        $this->assertDatabaseHas('jenis_tagihan', [
            'nama' => 'Uang Kegiatan Semester',
            'kategori' => 'semester',
            'default_nominal' => 450000.00,
            'is_blocking' => 1,
        ]);
    }

    public function test_auto_cleanup_duplicate_unpaid_bills_when_category_switched_from_rutin_to_semester(): void
    {
        $kategori = JenisTagihan::create([
            'nama' => 'Biaya Ujian Semester 1',
            'kategori' => 'rutin',
            'default_nominal' => 200000,
            'is_blocking' => true,
        ]);

        // Tagihan 1: Bulan Juli - sudah lunas
        $tagihanJuli = Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'jenis_tagihan_id' => $kategori->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Juli',
            'nominal' => 200000,
            'total_dibayar' => 200000,
            'status' => 'lunas',
            'jatuh_tempo' => '2026-07-10',
        ]);

        \App\Models\Pembayaran::create([
            'tagihan_id' => $tagihanJuli->id,
            'petugas_id' => $this->financeUser->id,
            'nominal_dibayar' => 200000,
            'metode_bayar' => 'Transfer',
            'tanggal_bayar' => '2026-07-05',
        ]);

        // Tagihan 2: Bulan Agustus - duplikat yang terbit saat masih rutin, belum bayar
        $tagihanAgustus = Tagihan::create([
            'siswa_id' => $this->siswa->id,
            'jenis_tagihan_id' => $kategori->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'bulan' => 'Agustus',
            'nominal' => 200000,
            'total_dibayar' => 0,
            'status' => 'belum_bayar',
            'jatuh_tempo' => '2026-08-10',
        ]);

        $this->assertCount(2, Tagihan::where('jenis_tagihan_id', $kategori->id)->get());

        // Staf finance mengubah kategori dari 'rutin' ke 'semester'
        Livewire::actingAs($this->financeUser)
            ->test(ManajemenTagihan::class)
            ->call('editKategori', $kategori->id)
            ->set('kategori_tipe', 'semester')
            ->call('saveKategori')
            ->assertHasNoErrors()
            ->assertDispatched('show-alert', function ($name, $params) {
                $payload = $params[0] ?? $params;
                return ($payload['type'] ?? '') === 'edit'
                    && str_contains($payload['message'] ?? '', 'membersihkan 1 tagihan duplikat yang belum dibayar');
            });

        // Verifikasi di Database:
        // Tagihan Agustus (duplikat belum bayar) harus otomatis terhapus (soft deleted)
        $this->assertSoftDeleted('tagihan', [
            'id' => $tagihanAgustus->id,
        ]);

        // Tagihan Juli (yang sudah lunas dan ada pembayaran) harus TETAP ADA
        $this->assertDatabaseHas('tagihan', [
            'id' => $tagihanJuli->id,
            'status' => 'lunas',
            'total_dibayar' => 200000.00,
        ]);

        // Jumlah tagihan untuk santri ini sekarang tepat 1
        $this->assertCount(1, Tagihan::where('jenis_tagihan_id', $kategori->id)->where('siswa_id', $this->siswa->id)->get());
    }
}

