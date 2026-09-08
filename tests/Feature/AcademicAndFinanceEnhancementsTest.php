<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\NilaiSas;
use App\Models\KomponenNilai;
use App\Models\GajiGuru;
use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;
use App\Livewire\Guru\InputNilaiSumatif;
use App\Livewire\SuperAdmin\TataKelola\ManajemenKomponenNilai;
use App\Livewire\SuperAdmin\TataKelola\ManajemenSiswa;
use App\Livewire\SuperAdmin\TataKelola\ManajemenJadwal;
use App\Livewire\Guru\Ekstrakurikuler;
use App\Livewire\Finance\ManajemenGajiGuru;
use App\Livewire\Finance\ArusKas;
use App\Livewire\Finance\ArusKasKeluar;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AcademicAndFinanceEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $guruUser;
    protected Guru $guru;
    protected User $financeUser;
    protected TahunAjaran $tahunAjaran;
    protected Semester $semester;

    protected function setUp(): void
    {
        parent::setUp();

        $roleAdmin = Role::firstOrCreate(['nama' => 'super_admin'], ['deskripsi' => 'Super Administrator']);
        $roleGuru = Role::firstOrCreate(['nama' => 'guru'], ['deskripsi' => 'Guru']);
        $roleFinance = Role::firstOrCreate(['nama' => 'finance'], ['deskripsi' => 'Staff Keuangan']);
        $roleMurid = Role::firstOrCreate(['nama' => 'murid'], ['deskripsi' => 'Murid']);

        $this->superAdmin = User::factory()->create([
            'username' => 'admin_' . uniqid(),
            'nama' => 'Super Admin Test',
            'role_id' => $roleAdmin->id,
            'status' => 'aktif',
        ]);

        $this->guruUser = User::factory()->create([
            'username' => 'guru_' . uniqid(),
            'nama' => 'Ustadz Abdullah',
            'role_id' => $roleGuru->id,
            'status' => 'aktif',
        ]);

        $this->guru = Guru::create([
            'user_id' => $this->guruUser->id,
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

        $this->tahunAjaran = TahunAjaran::firstOrCreate(
            ['nama' => '2026/2027'],
            ['status_aktif' => true]
        );

        $this->semester = Semester::firstOrCreate(
            ['tahun_ajaran_id' => $this->tahunAjaran->id, 'semester' => 'ganjil'],
            [
                'status_aktif' => true,
                'tanggal_mulai' => '2026-07-01',
                'tanggal_selesai' => '2026-12-31',
            ]
        );
    }

    /** Test 1: Penilaian Sumatif Matrix with UH, UTS, and SAS */
    public function test_input_nilai_sumatif_stores_uh_uts_and_sas(): void
    {
        $kelas = Kelas::create([
            'nama_kelas' => '7A',
            'tingkat' => 7,
            'guru_umum_id' => $this->guru->id,
            'semester_id' => $this->semester->id,
        ]);

        $mapel = MataPelajaran::create([
            'nama_mapel' => 'Matematika',
            'kkm' => 75,
        ]);

        $muridUser = User::factory()->create([
            'username' => 'murid_' . uniqid(),
            'nama' => 'Ahmad Fulan',
            'role_id' => Role::where('nama', 'murid')->first()->id,
        ]);
        $siswa = Siswa::create([
            'user_id' => $muridUser->id,
            'nis' => '1001',
            'kelas_id' => $kelas->id,
            'status' => 'aktif',
            'tanggal_masuk' => now(),
        ]);

        $this->actingAs($this->guruUser);

        Livewire::test(InputNilaiSumatif::class)
            ->set('kelas_id', $kelas->id)
            ->set('mapel_id', $mapel->id)
            ->set('semester_id', $this->semester->id)
            ->set("nilaiSasMatrix.{$siswa->id}", 90)
            ->call('saveMatrix');

        $this->assertDatabaseHas('nilai_sas', [
            'siswa_id' => $siswa->id,
            'mapel_id' => $mapel->id,
            'semester_id' => $this->semester->id,
            'nilai_sas' => 90,
        ]);
    }

    /** Test 2: Manajemen Komponen Nilai with Weight Percentage */
    public function test_manajemen_komponen_nilai_saves_bobot_percentage(): void
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(ManajemenKomponenNilai::class)
            ->set('nama', 'Ujian Tengah Semester')
            ->set('bobot', 25)
            ->set('kategori', 'pengetahuan')
            ->set('berlaku_untuk', 'umum')
            ->call('saveForm')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('komponen_nilai', [
            'nama' => 'Ujian Tengah Semester',
            'bobot' => 25,
            'kategori' => 'pengetahuan',
        ]);
    }

    /** Test 3: Ekstrakurikuler Menu for Guru */
    public function test_guru_can_access_ekstrakurikuler(): void
    {
        $this->actingAs($this->guruUser);

        // Verify direct page access
        $response = $this->get(route('guru.ekskul'));
        $response->assertStatus(200);

        Livewire::test(Ekstrakurikuler::class)
            ->assertSee('Ekstrakurikuler');

        // Verify sidebar menu displays Ekstrakurikuler for Guru Umum
        $dashboardResponse = $this->get(route('guru.dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee(route('guru.ekskul'));

        // Verify sidebar menu displays Ekstrakurikuler for Guru Tahfizh
        $this->guru->update(['jenis_guru' => 'tahfidz']);
        $tahfidzResponse = $this->get(route('guru.dashboard'));
        $tahfidzResponse->assertStatus(200);
        $tahfidzResponse->assertSee(route('guru.ekskul'));

        // Verify Super Admin sidebar has Kelola Ekstrakurikuler
        $this->actingAs($this->superAdmin);
        $adminResponse = $this->get(route('super-admin.dashboard'));
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee(route('tata-usaha.ekstrakurikuler'));
    }

    /** Test 4: Timetable Form closeForm method exists and handles reset */
    public function test_manajemen_jadwal_close_form(): void
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(ManajemenJadwal::class)
            ->set('isFormOpen', true)
            ->call('closeForm')
            ->assertSet('isFormOpen', false);
    }

    /** Test 5: Manajemen Siswa with Shadow Teacher (Guru Pendamping Khusus) */
    public function test_manajemen_siswa_handles_shadow_teacher(): void
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(ManajemenSiswa::class)
            ->set('nama', 'Budi Santoso')
            ->set('username', 'budi_santoso')
            ->set('email', 'budi@sekolah.sch.id')
            ->set('password', 'password123')
            ->set('nis', '12345')
            ->set('jenis_kelamin', 'L')
            ->set('tanggal_masuk', '2026-07-01')
            ->set('shadow_teacher_id', $this->guru->id)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('siswa', [
            'nis' => '12345',
            'shadow_teacher_id' => $this->guru->id,
        ]);
    }

    /** Test 6: Manajemen Gaji Guru - Edit Bulan/Tahun and Syncs Pengeluaran */
    public function test_gaji_guru_edit_period_and_syncs_pengeluaran(): void
    {
        $this->actingAs($this->superAdmin);

        $kategori = KategoriPengeluaran::firstOrCreate(['nama' => 'Gaji']);

        $pengeluaran = Pengeluaran::create([
            'kategori_pengeluaran_id' => $kategori->id,
            'jumlah' => 2500000,
            'tanggal' => '2026-01-25',
            'keterangan' => 'Pembayaran Gaji ' . $this->guruUser->nama . ' (Januari 2026)',
            'petugas_id' => $this->superAdmin->id,
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

        Livewire::test(ManajemenGajiGuru::class)
            ->call('openEditModal', $gaji->id)
            ->assertSet('editBulan', 'Januari')
            ->assertSet('editTahun', 2026)
            ->set('editBulan', 'Februari')
            ->set('editTahun', 2026)
            ->call('saveEdit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('gaji_guru', [
            'id' => $gaji->id,
            'bulan' => 'Februari',
            'tahun' => 2026,
        ]);

        $this->assertDatabaseHas('pengeluaran', [
            'id' => $pengeluaran->id,
            'keterangan' => 'Pembayaran Gaji ' . $this->guruUser->nama . ' (Februari 2026)',
        ]);
    }

    /** Test 7: Batch Delete for Finance role submits approval for paid salaries */
    public function test_finance_batch_delete_handles_draft_and_submits_approval(): void
    {
        $this->actingAs($this->financeUser);

        $draftGaji = GajiGuru::create([
            'guru_id' => $this->guru->id,
            'bulan' => 'Maret',
            'tahun' => 2026,
            'gaji_pokok' => 2000000,
            'total_bruto' => 2000000,
            'total_diterima' => 2000000,
            'status' => 'draft',
            'tanggal_bayar' => '2026-03-25',
        ]);

        $paidGaji = GajiGuru::create([
            'guru_id' => $this->guru->id,
            'bulan' => 'April',
            'tahun' => 2026,
            'gaji_pokok' => 2000000,
            'total_bruto' => 2000000,
            'total_diterima' => 2000000,
            'status' => 'dibayar',
            'tanggal_bayar' => '2026-04-25',
        ]);

        Livewire::test(ManajemenGajiGuru::class)
            ->set('selectedGajiIds', [$draftGaji->id, $paidGaji->id])
            ->call('deleteSelected');

        // Draft should be soft deleted
        $this->assertSoftDeleted('gaji_guru', ['id' => $draftGaji->id]);

        // Paid salary still exists pending approval (not soft deleted)
        $this->assertDatabaseHas('gaji_guru', [
            'id' => $paidGaji->id,
            'deleted_at' => null,
        ]);

        // Approval request was created
        $this->assertDatabaseHas('approval_keuangan', [
            'model_id' => $paidGaji->id,
            'tipe_aksi' => 'hapus',
            'status' => 'menunggu',
        ]);
    }

    /** Test 8: Arus Kas and Arus Kas Keluar custom category creation */
    public function test_arus_kas_creates_custom_category_on_the_fly(): void
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(ArusKas::class)
            ->call('openExpenseModal')
            ->set('is_kategori_kustom', true)
            ->set('kategori_keluar_kustom', 'Perbaikan Sound System Mesjid')
            ->set('jumlah_keluar', 500000)
            ->set('keterangan_keluar', 'Beli kabel mic dan splitter baru')
            ->call('saveExpense')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('kategori_pengeluaran', [
            'nama' => 'Perbaikan Sound System Mesjid',
        ]);

        $this->assertDatabaseHas('pengeluaran', [
            'jumlah' => 500000,
            'keterangan' => 'Beli kabel mic dan splitter baru',
        ]);
    }

    /** Test 9: Detail Gaji Guru batch delete for finance staff */
    public function test_detail_gaji_guru_batch_delete_works_for_finance(): void
    {
        $this->actingAs($this->financeUser);

        $draftGaji = GajiGuru::create([
            'guru_id' => $this->guru->id,
            'bulan' => 'Mei',
            'tahun' => 2026,
            'gaji_pokok' => 2500000,
            'total_bruto' => 2500000,
            'total_diterima' => 2500000,
            'status' => 'draft',
            'tanggal_bayar' => '2026-05-25',
        ]);

        $paidGaji = GajiGuru::create([
            'guru_id' => $this->guru->id,
            'bulan' => 'Juni',
            'tahun' => 2026,
            'gaji_pokok' => 2500000,
            'total_bruto' => 2500000,
            'total_diterima' => 2500000,
            'status' => 'dibayar',
            'tanggal_bayar' => '2026-06-25',
        ]);

        Livewire::test(\App\Livewire\Finance\DetailGajiGuru::class, ['guruId' => $this->guru->id])
            ->set('selectedGajiIds', [$draftGaji->id, $paidGaji->id])
            ->call('deleteSelected')
            ->assertDispatched('notify');

        // Draft deleted immediately
        $this->assertSoftDeleted('gaji_guru', ['id' => $draftGaji->id]);

        // Paid salary still exists pending approval
        $this->assertDatabaseHas('gaji_guru', [
            'id' => $paidGaji->id,
            'deleted_at' => null,
        ]);

        // Approval request was created
        $this->assertDatabaseHas('approval_keuangan', [
            'model_id' => $paidGaji->id,
            'tipe_aksi' => 'hapus',
            'status' => 'menunggu',
        ]);
    }
}
