<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\KalenderAkademik;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Models\Role;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Semester;
use Livewire\Livewire;
use App\Livewire\TataUsaha\ManajemenKalenderAkademik;
use App\Livewire\Shared\Laporan\RekapAbsensiSiswa;
use App\Livewire\Shared\Laporan\RekapAbsensiGuru;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KalenderAkademikTanggalMerahTest extends TestCase
{
    use RefreshDatabase;

    public function test_sundays_and_national_holidays_are_automatically_recognized_as_holidays()
    {
        // 2026-08-02 is a Sunday (Hari Ahad / Tanggal Merah Mingguan)
        $this->assertTrue(KalenderAkademik::isHolidayDate('2026-08-02'));

        // 2026-08-03 is a Monday (Regular working day, no holiday)
        $this->assertFalse(KalenderAkademik::isHolidayDate('2026-08-03'));

        // 2026-08-17 is HUT RI (Indonesian Independence Day, Monday - official national red date)
        $this->assertTrue(KalenderAkademik::isHolidayDate('2026-08-17'));
        $this->assertTrue(KalenderAkademik::isNationalHoliday('2026-08-17'));

        // Fixed national holidays
        $this->assertTrue(KalenderAkademik::isHolidayDate('2026-01-01')); // Tahun Baru
        $this->assertTrue(KalenderAkademik::isHolidayDate('2026-05-01')); // Hari Buruh
        $this->assertTrue(KalenderAkademik::isHolidayDate('2026-06-01')); // Hari Lahir Pancasila
        $this->assertTrue(KalenderAkademik::isHolidayDate('2026-12-25')); // Natal

        // Holiday info helper
        $sundayInfo = KalenderAkademik::getHolidayInfo('2026-08-02');
        $this->assertNotNull($sundayInfo);
        $this->assertTrue($sundayInfo['is_tanggal_merah']);
        $this->assertTrue($sundayInfo['liburkan_presensi']);

        $hutRiInfo = KalenderAkademik::getHolidayInfo('2026-08-17');
        $this->assertNotNull($hutRiInfo);
        $this->assertStringContainsString('Kemerdekaan', $hutRiInfo['nama']);
        $this->assertEquals('libur_nasional', $hutRiInfo['sumber']);

        $regularDayInfo = KalenderAkademik::getHolidayInfo('2026-08-03');
        $this->assertNull($regularDayInfo);
    }

    public function test_sync_tanggal_merah_nasional_populates_academic_calendar()
    {
        $role = Role::firstOrCreate(['nama' => 'tata_usaha'], ['deskripsi' => 'Tata Usaha']);
        $user = User::firstOrCreate(
            ['username' => 'tu_sync_test'],
            [
                'nama' => 'Staff TU Sync',
                'email' => 'tu_sync@example.com',
                'password' => bcrypt('password'),
                'role_id' => $role->id,
            ]
        );

        $ta = TahunAjaran::create([
            'nama' => '2026/2027',
            'status_aktif' => true,
        ]);

        $this->assertEquals(0, KalenderAkademik::where('tahun_ajaran_id', $ta->id)->count());

        Livewire::actingAs($user)
            ->test(ManajemenKalenderAkademik::class)
            ->call('syncTanggalMerahNasional', $ta->id)
            ->assertStatus(200)
            ->assertSee('Berhasil menyinkronkan');

        // Verify national holidays were inserted with liburkan_presensi = true
        $count = KalenderAkademik::where('tahun_ajaran_id', $ta->id)->count();
        $this->assertGreaterThan(10, $count);

        $hutRi = KalenderAkademik::where('tahun_ajaran_id', $ta->id)
            ->whereDate('tanggal_mulai', '2026-08-17')
            ->first();

        $this->assertNotNull($hutRi);
        $this->assertEquals('hari_libur', $hutRi->jenis);
        $this->assertTrue($hutRi->liburkan_presensi);
        $this->assertStringContainsString('Tanggal Merah', $hutRi->keterangan);
    }

    public function test_rekap_absensi_automatically_marks_sundays_and_national_holidays_as_libur()
    {
        $role = Role::firstOrCreate(['nama' => 'super_admin'], ['deskripsi' => 'Super Admin']);
        $admin = User::firstOrCreate(
            ['username' => 'admin_rekap_test'],
            [
                'nama' => 'Admin Rekap',
                'email' => 'admin_rekap@example.com',
                'password' => bcrypt('password'),
                'role_id' => $role->id,
            ]
        );

        $ta = TahunAjaran::create(['nama' => '2026/2027', 'status_aktif' => true]);
        $sem = Semester::create([
            'tahun_ajaran_id' => $ta->id,
            'semester' => 'Ganjil',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2026-12-31',
            'status_aktif' => true,
        ]);

        $roleGuru = Role::firstOrCreate(['nama' => 'guru'], ['deskripsi' => 'Guru']);
        $userGuru = User::create([
            'nama' => 'Ustadz Ahmad',
            'username' => 'ustadz_ahmad',
            'email' => 'ahmad@example.com',
            'password' => bcrypt('password'),
            'role_id' => $roleGuru->id,
            'status' => 'aktif',
        ]);

        $guru = Guru::create([
            'user_id' => $userGuru->id,
            'nip' => 'GURU-RED-01',
            'jenis_guru' => 'umum',
            'status_kepegawaian' => 'tetap',
            'pendidikan' => 'S1',
            'tanggal_masuk' => '2024-01-01',
            'status_aktif' => true,
        ]);

        $kelas = Kelas::create([
            'nama_kelas' => '7A',
            'jenis_kelas' => 'umum',
            'tingkat' => '7',
            'semester_id' => $sem->id,
        ]);

        $roleMurid = Role::firstOrCreate(['nama' => 'murid'], ['deskripsi' => 'Murid']);
        $userMurid = User::create([
            'nama' => 'Santri Fulan',
            'username' => 'santri_fulan',
            'email' => 'santri_fulan@example.com',
            'password' => bcrypt('password'),
            'role_id' => $roleMurid->id,
            'status' => 'aktif',
        ]);

        $siswa = Siswa::create([
            'user_id' => $userMurid->id,
            'nis' => '1001',
            'nisn' => '1001001',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '2012-01-01',
            'alamat' => 'Jakarta',
            'status' => 'aktif',
            'tanggal_masuk' => '2024-07-01',
            'kelas_id' => $kelas->id,
        ]);

        // Test Rekap Absensi Guru for August 2026
        $rekapGuru = new RekapAbsensiGuru();
        $rekapGuru->bulan = 8;
        $rekapGuru->tahun = 2026;

        $guruData = $rekapGuru->getMatrixData();
        $this->assertNotEmpty($guruData['matrix']);

        $guruDays = $guruData['matrix'][0]['days'];
        // Day 2 (2026-08-02 is Sunday) -> must be 'libur'
        $this->assertEquals('libur', $guruDays[2]);
        // Day 17 (2026-08-17 is HUT RI) -> must be 'libur'
        $this->assertEquals('libur', $guruDays[17]);
        // Guru should not be penalized with alpa on red dates
        $this->assertEquals(0, $guruData['matrix'][0]['tidak_hadir']);

        // Test Rekap Absensi Siswa for August 2026
        $rekapSiswa = new RekapAbsensiSiswa();
        $rekapSiswa->kelasId = $kelas->id;
        $rekapSiswa->bulan = 8;
        $rekapSiswa->tahun = 2026;

        $siswaData = $rekapSiswa->getMatrixData();
        $this->assertNotEmpty($siswaData['matrix']);

        $siswaDays = $siswaData['matrix'][0]['days'];
        // Day 2 (Sunday) -> 'libur'
        $this->assertEquals('libur', $siswaDays[2]);
        // Day 17 (HUT RI) -> 'libur'
        $this->assertEquals('libur', $siswaDays[17]);
        // Siswa should not be penalized with alpa on red dates
        $this->assertEquals(0, $siswaData['matrix'][0]['tidak_hadir']);
    }
}
