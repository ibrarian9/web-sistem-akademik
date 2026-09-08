<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Ekstrakurikuler;
use App\Models\SiswaEkstrakurikuler;
use App\Models\KegiatanEkstrakurikuler;
use App\Models\PresensiEkstrakurikuler;
use App\Models\TahunAjaran;
use App\Models\Semester;
use Livewire\Livewire;
use App\Livewire\TataUsaha\ManajemenEkstrakurikuler;
use App\Livewire\SuperAdmin\TataKelola\ManajemenJadwal;
use App\Livewire\Guru\Ekstrakurikuler as GuruEkstrakurikuler;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EkstrakurikulerModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $tuUser;
    protected User $adminUser;
    protected User $admin2User;
    protected User $pembinaUser;
    protected Guru $guru;
    protected Siswa $siswa;
    protected Semester $semester;

    protected function setUp(): void
    {
        parent::setUp();

        $roleTU = Role::firstOrCreate(['nama' => 'tata_usaha'], ['deskripsi' => 'Tata Usaha']);
        $roleAdmin = Role::firstOrCreate(['nama' => 'super_admin'], ['deskripsi' => 'Super Administrator']);
        $roleAdmin2 = Role::firstOrCreate(['nama' => 'super_admin_2'], ['deskripsi' => 'Super Administrator 2']);
        $roleGuru = Role::firstOrCreate(['nama' => 'guru'], ['deskripsi' => 'Guru Pengajar']);
        $roleMurid = Role::firstOrCreate(['nama' => 'murid'], ['deskripsi' => 'Murid']);

        $tahun = TahunAjaran::create(['nama' => '2026/2027', 'status_aktif' => true]);
        $this->semester = Semester::create([
            'tahun_ajaran_id' => $tahun->id,
            'semester' => '1',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2026-12-31',
            'status_aktif' => true,
        ]);

        $this->tuUser = User::factory()->create(['role_id' => $roleTU->id, 'status' => 'aktif']);
        $this->adminUser = User::factory()->create(['role_id' => $roleAdmin->id, 'status' => 'aktif']);
        $this->admin2User = User::factory()->create(['role_id' => $roleAdmin2->id, 'status' => 'aktif']);

        $this->pembinaUser = User::factory()->create([
            'nama' => 'Ust. Kakang Prabu',
            'role_id' => $roleGuru->id,
            'status' => 'aktif'
        ]);

        $this->guru = Guru::create([
            'user_id' => $this->pembinaUser->id,
            'nip' => '19870101001',
            'tanggal_masuk' => '2020-01-01',
            'status_aktif' => true,
        ]);

        $kelas = Kelas::create(['nama_kelas' => '6B', 'tingkat' => 6, 'semester_id' => $this->semester->id]);
        $muridUser = User::factory()->create(['nama' => 'Ahmad Faiz', 'role_id' => $roleMurid->id, 'status' => 'aktif']);
        $this->siswa = Siswa::create([
            'user_id' => $muridUser->id,
            'kelas_id' => $kelas->id,
            'nis' => '2001',
            'tanggal_masuk' => '2022-07-15',
            'status' => 'aktif',
        ]);
    }

    public function test_tata_usaha_can_assign_pembina_and_register_student(): void
    {
        // 1. TU creates ekskul and assigns pembina
        Livewire::actingAs($this->tuUser)
            ->test(ManajemenEkstrakurikuler::class)
            ->set('nama', 'Pramuka Penggalang')
            ->set('pembina_guru_id', $this->guru->id)
            ->set('kuota', 40)
            ->set('status_aktif', true)
            ->call('save')
            ->assertHasNoErrors();

        $ekskul = Ekstrakurikuler::where('nama', 'Pramuka Penggalang')->first();
        $this->assertNotNull($ekskul);
        $this->assertEquals($this->guru->id, $ekskul->pembina_guru_id);

        // 2. TU registers student into ekskul
        Livewire::actingAs($this->tuUser)
            ->test(ManajemenEkstrakurikuler::class)
            ->call('openRosterModal', $ekskul->id)
            ->set('selectedSiswaIdToAdd', $this->siswa->id)
            ->call('addSiswaToEkskul');

        $this->assertDatabaseHas('siswa_ekstrakurikuler', [
            'ekstrakurikuler_id' => $ekskul->id,
            'siswa_id' => $this->siswa->id,
            'semester_id' => $this->semester->id,
        ]);
    }

    public function test_admin_can_manage_ekstrakurikuler_schedule_and_admin2_is_read_only(): void
    {
        $ekskul = Ekstrakurikuler::create([
            'nama' => 'Klub Robotik',
            'pembina_guru_id' => $this->guru->id,
            'kuota' => 20,
            'status_aktif' => true,
        ]);

        // 1. Super Admin manages schedule
        Livewire::actingAs($this->adminUser)
            ->test(ManajemenJadwal::class)
            ->set('jadwalType', 'ekstrakurikuler')
            ->call('openEditEkskulSchedule', $ekskul->id)
            ->set('ekskulHari', 'sabtu')
            ->set('ekskulJamMulai', '08:00')
            ->set('ekskulJamSelesai', '10:00')
            ->set('ekskulTempat', 'Lab Robotik')
            ->call('saveEkskulSchedule')
            ->assertHasNoErrors();

        $ekskul->refresh();
        $this->assertEquals('sabtu', $ekskul->hari);
        $this->assertEquals('08:00', $ekskul->jam_mulai);
        $this->assertEquals('Lab Robotik', $ekskul->tempat);

        // 2. Super Admin 2 is blocked from saving schedule
        Livewire::actingAs($this->admin2User)
            ->test(ManajemenJadwal::class)
            ->call('openEditEkskulSchedule', $ekskul->id)
            ->assertSee('Akses ditolak');
    }

    public function test_pembina_can_record_attendance_and_session_score(): void
    {
        $ekskul = Ekstrakurikuler::create([
            'nama' => 'Futsal Prestasi',
            'pembina_guru_id' => $this->guru->id,
            'kuota' => 25,
            'status_aktif' => true,
        ]);

        SiswaEkstrakurikuler::create([
            'ekstrakurikuler_id' => $ekskul->id,
            'siswa_id' => $this->siswa->id,
            'semester_id' => $this->semester->id,
            'predikat' => 'B',
        ]);

        // 1. Pembina creates session, inputs attendance & session score
        Livewire::actingAs($this->pembinaUser)
            ->test(GuruEkstrakurikuler::class)
            ->set('selectedEkskulId', $ekskul->id)
            ->set('kegiatanTanggal', '2026-09-12')
            ->set('kegiatanNama', 'Pertemuan 1 - Dribbling & Passing')
            ->set('kegiatanKeterangan', 'Latihan fisik dan teknik operan cepat')
            ->call('saveKegiatan')
            ->assertHasNoErrors();

        $kegiatan = KegiatanEkstrakurikuler::where('ekstrakurikuler_id', $ekskul->id)->first();
        $this->assertNotNull($kegiatan);

        // 2. Save attendance and periodic grade
        Livewire::actingAs($this->pembinaUser)
            ->test(GuruEkstrakurikuler::class)
            ->set('selectedEkskulId', $ekskul->id)
            ->set('selectedKegiatanId', $kegiatan->id)
            ->set('presensiStatus.' . $this->siswa->id, 'Hadir')
            ->set('nilaiBerkala.' . $this->siswa->id, 92.5)
            ->set('catatanBerkala.' . $this->siswa->id, 'Passing sangat akurat')
            ->call('savePresensiDanNilaiSesi');

        $this->assertDatabaseHas('presensi_ekstrakurikuler', [
            'kegiatan_ekstrakurikuler_id' => $kegiatan->id,
            'siswa_id' => $this->siswa->id,
            'status_kehadiran' => 'Hadir',
            'nilai' => 92.5,
            'catatan' => 'Passing sangat akurat',
        ]);
    }
}
