<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\GuruMapelKelas;
use App\Models\LingkupMateri;
use App\Models\TujuanPembelajaran;
use App\Models\NilaiSumatifTp;
use App\Models\TahunAjaran;
use App\Models\Semester;
use Livewire\Livewire;
use App\Livewire\SuperAdmin\MonitoringAkademik;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SuperAdmin2MonitoringGuruTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin2;
    protected User $regularGuruUser;
    protected Guru $guru;
    protected Kelas $kelas;
    protected MataPelajaran $mapel;
    protected Semester $semester;

    protected function setUp(): void
    {
        parent::setUp();

        $roleAdmin2 = Role::firstOrCreate(['nama' => 'super_admin_2'], ['deskripsi' => 'Super Administrator 2']);
        $roleGuru = Role::firstOrCreate(['nama' => 'guru'], ['deskripsi' => 'Guru Pengajar']);

        $tahun = TahunAjaran::create(['nama' => '2026/2027', 'status_aktif' => true]);
        $this->semester = Semester::create([
            'tahun_ajaran_id' => $tahun->id,
            'semester' => '1',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2026-12-31',
            'status_aktif' => true,
        ]);

        $this->superAdmin2 = User::factory()->create([
            'username' => 'admin2_' . uniqid(),
            'role_id' => $roleAdmin2->id,
            'status' => 'aktif',
        ]);

        $this->regularGuruUser = User::factory()->create([
            'nama' => 'Ust. Budi Santoso, S.Pd',
            'username' => 'guru_' . uniqid(),
            'role_id' => $roleGuru->id,
            'status' => 'aktif',
        ]);

        $this->guru = Guru::create([
            'user_id' => $this->regularGuruUser->id,
            'nip' => '198501012020',
            'tanggal_masuk' => '2020-01-01',
            'status_aktif' => true,
        ]);

        $this->kelas = Kelas::create(['nama_kelas' => '6A', 'tingkat' => 6, 'semester_id' => $this->semester->id]);
        $this->mapel = MataPelajaran::create([
            'nama_mapel' => 'Bahasa Indonesia',
            'jenis' => 'umum',
            'kkm' => 75,
        ]);

        GuruMapelKelas::create([
            'guru_id' => $this->guru->id,
            'kelas_id' => $this->kelas->id,
            'mapel_id' => $this->mapel->id,
            'semester_id' => $this->semester->id,
        ]);
    }

    public function test_super_admin_2_can_access_monitoring_guru_progress(): void
    {
        // 1. Create Bab & TP
        $bab = LingkupMateri::create([
            'mapel_id' => $this->mapel->id,
            'nama_lingkup_materi' => 'Bab 1: Menulis Teks Deskripsi',
            'kategori' => 'sumatif',
            'urutan' => 1,
        ]);

        $tp = TujuanPembelajaran::create([
            'lingkup_materi_id' => $bab->id,
            'deskripsi_tp' => 'Peserta didik mampu memahami teks deskripsi',
            'urutan' => 1,
        ]);

        // 2. Create Student and input grade
        $roleMurid = Role::firstOrCreate(['nama' => 'murid'], ['deskripsi' => 'Murid']);
        $muridUser = User::factory()->create(['role_id' => $roleMurid->id, 'status' => 'aktif']);
        $siswa = Siswa::create([
            'user_id' => $muridUser->id,
            'kelas_id' => $this->kelas->id,
            'nis' => '1001',
            'nisn' => '00112233',
            'tanggal_masuk' => '2022-07-15',
            'status' => 'aktif',
        ]);

        NilaiSumatifTp::create([
            'siswa_id' => $siswa->id,
            'tp_id' => $tp->id,
            'semester_id' => $this->semester->id,
            'nilai' => 88.5,
        ]);

        // 3. Test Livewire component as super_admin_2
        Livewire::actingAs($this->superAdmin2)
            ->test(MonitoringAkademik::class)
            ->assertSet('activeTab', 'progres_guru')
            ->assertSee('Progres Supervisi Guru di Setiap Kelas')
            ->assertSee('Ust. Budi Santoso, S.Pd')
            ->assertSee('Bahasa Indonesia')
            ->assertSee('1 Bab')
            ->assertSee('1 TP')
            ->assertSee('100%')
            ->assertSee('Lengkap');
    }
}
