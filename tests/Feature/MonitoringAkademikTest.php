<?php

namespace Tests\Feature;

use App\Livewire\SuperAdmin\MonitoringAkademik;
use App\Models\AbsensiGuru;
use App\Models\AbsensiSiswa;
use App\Models\Guru;
use App\Models\GuruMapelKelas;
use App\Models\Kelas;
use App\Models\LingkupMateri;
use App\Models\MataPelajaran;
use App\Models\NilaiSas;
use App\Models\NilaiSumatifTp;
use App\Models\Rapor;
use App\Models\RaporDetail;
use App\Models\Role;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\TujuanPembelajaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MonitoringAkademikTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $guruUser;
    protected Guru $guru;
    protected User $muridUser;
    protected Siswa $siswa;
    protected Kelas $kelas;
    protected MataPelajaran $mapel;
    protected TahunAjaran $tahunAjaran;
    protected Semester $semester;
    protected LingkupMateri $bab1;
    protected TujuanPembelajaran $tp1;

    protected function setUp(): void
    {
        parent::setUp();

        $roleAdmin = Role::firstOrCreate(['nama' => 'super_admin'], ['deskripsi' => 'Super Admin']);
        $roleGuru = Role::firstOrCreate(['nama' => 'guru'], ['deskripsi' => 'Guru']);
        $roleMurid = Role::firstOrCreate(['nama' => 'murid'], ['deskripsi' => 'Murid']);

        $this->adminUser = User::factory()->create([
            'username' => 'admin_test_' . uniqid(),
            'role_id' => $roleAdmin->id,
            'nama' => 'Super Admin Monitoring',
            'status' => 'aktif',
        ]);

        $this->guruUser = User::factory()->create([
            'username' => 'guru_test_' . uniqid(),
            'role_id' => $roleGuru->id,
            'nama' => 'Ustadz Abdullah',
            'status' => 'aktif',
        ]);

        $this->guru = Guru::create([
            'user_id' => $this->guruUser->id,
            'nip' => '199001012022011001',
            'jenis_guru' => 'umum',
            'status_kepegawaian' => 'tetap_yayasan',
            'tanggal_masuk' => '2022-01-01',
            'status_aktif' => true,
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
            'nama_kelas' => '6A',
            'jenis_kelas' => 'umum',
            'tingkat' => 6,
            'guru_umum_id' => $this->guru->id,
            'semester_id' => $this->semester->id,
        ]);

        $this->muridUser = User::factory()->create([
            'username' => 'murid_test_' . uniqid(),
            'role_id' => $roleMurid->id,
            'nama' => 'Muhammad Al-Fatih',
            'status' => 'aktif',
        ]);

        $this->siswa = Siswa::create([
            'user_id' => $this->muridUser->id,
            'kelas_id' => $this->kelas->id,
            'nis' => '2001',
            'nisn' => '0000002001',
            'jenis_kelamin' => 'L',
            'tanggal_masuk' => '2026-07-01',
            'status' => 'aktif',
            'saldo_deposit' => 0,
        ]);

        $this->mapel = MataPelajaran::create([
            'nama_mapel' => 'Ilmu Pengetahuan Alam',
            'jenis' => 'umum',
        ]);

        GuruMapelKelas::create([
            'guru_id' => $this->guru->id,
            'mapel_id' => $this->mapel->id,
            'kelas_id' => $this->kelas->id,
            'semester_id' => $this->semester->id,
        ]);

        $this->bab1 = LingkupMateri::create([
            'mapel_id' => $this->mapel->id,
            'nama_lingkup_materi' => 'Sistem Tata Surya & Galaksi',
            'urutan' => 1,
        ]);

        $this->tp1 = TujuanPembelajaran::create([
            'lingkup_materi_id' => $this->bab1->id,
            'deskripsi_tp' => 'Mengidentifikasi karakteristik planet dalam tata surya',
            'urutan' => 1,
        ]);
    }

    public function test_admin_can_access_monitoring_akademik_page(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('super-admin.monitoring-akademik'));
        $response->assertStatus(200)
            ->assertSee('Monitoring Akademik & Pembelajaran');
    }

    public function test_admin_can_monitor_nilai_siswa_per_bab_and_sas(): void
    {
        // Add scores
        NilaiSumatifTp::create([
            'siswa_id' => $this->siswa->id,
            'tp_id' => $this->tp1->id,
            'semester_id' => $this->semester->id,
            'nilai' => 88,
        ]);

        NilaiSas::create([
            'siswa_id' => $this->siswa->id,
            'mapel_id' => $this->mapel->id,
            'semester_id' => $this->semester->id,
            'nilai' => 92,
            'nilai_sas' => 92,
        ]);

        $rapor = Rapor::create([
            'siswa_id' => $this->siswa->id,
            'semester_id' => $this->semester->id,
            'kelas_id' => $this->kelas->id,
            'tipe_rapor' => 'akademik',
            'status' => 'terbit',
        ]);

        RaporDetail::create([
            'rapor_id' => $rapor->id,
            'mapel_id' => $this->mapel->id,
            'nilai_akhir' => 90.00,
            'predikat' => 'A',
        ]);

        $this->actingAs($this->adminUser);

        Livewire::test(MonitoringAkademik::class)
            ->set('selectedKelasId', $this->kelas->id)
            ->set('selectedMapelId', $this->mapel->id)
            ->set('selectedSemesterId', $this->semester->id)
            ->assertSee('Muhammad Al-Fatih')
            ->assertSee('Sistem Tata Surya & Galaksi')
            ->assertSee('88')
            ->assertSee('92')
            ->assertSee('90')
            ->assertSee('Ustadz Abdullah');
    }

    public function test_admin_can_monitor_bab_and_tp_curriculum(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(MonitoringAkademik::class)
            ->call('setTab', 'kurikulum')
            ->set('kurikulumMapelId', $this->mapel->id)
            ->assertSee('Ilmu Pengetahuan Alam')
            ->assertSee('Sistem Tata Surya & Galaksi')
            ->assertSee('Mengidentifikasi karakteristik planet dalam tata surya')
            ->assertSee('Ustadz Abdullah');
    }

    public function test_admin_can_monitor_absensi_siswa_and_guru(): void
    {
        $today = Carbon::today()->toDateString();

        AbsensiSiswa::create([
            'siswa_id' => $this->siswa->id,
            'kelas_id' => $this->kelas->id,
            'guru_id' => $this->guru->id,
            'tanggal' => $today,
            'status' => 'hadir',
            'catatan' => 'Tepat waktu',
        ]);

        AbsensiGuru::create([
            'guru_id' => $this->guru->id,
            'tanggal' => $today,
            'waktu_datang' => '07:05:00',
            'status' => 'hadir',
            'catatan' => 'Piket pagi',
        ]);

        $this->actingAs($this->adminUser);

        // 1. Check Absensi Siswa
        $test = Livewire::test(MonitoringAkademik::class)
            ->call('setTab', 'absen')
            ->call('setAbsenSubTab', 'siswa')
            ->assertSee('Muhammad Al-Fatih')
            ->assertSee('HADIR')
            ->assertSee('Tepat waktu');

        // 2. Check Absensi Guru
        $test->call('setAbsenSubTab', 'guru')
            ->assertSee('Ustadz Abdullah')
            ->assertSee('07:05:00')
            ->assertSee('Piket pagi');
    }

    public function test_admin_sidebar_does_not_contain_setup_bab_tp_for_guru(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('super-admin.dashboard'));
        $response->assertStatus(200)
            ->assertSee('Monitoring Akademik')
            ->assertDontSee('Setup Bab & TP');
    }

    public function test_murid_cannot_access_monitoring_akademik(): void
    {
        $this->actingAs($this->muridUser);

        $response = $this->get(route('super-admin.monitoring-akademik'));
        $response->assertRedirect(route('murid.dashboard'));
    }
}
