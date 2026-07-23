<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Role;
use App\Models\Tagihan;
use Livewire\Livewire;
use App\Livewire\TataUsaha\ProsesKenaikanKelas;

class KenaikanKelasExportTest extends TestCase
{
    public function test_can_process_bulk_class_promotion()
    {
        $roleTu = Role::firstOrCreate(['nama' => 'tata_usaha'], ['deskripsi' => 'Tata Usaha']);
        $userTu = User::firstOrCreate(
            ['username' => 'tu_test_promotion'],
            [
                'nama' => 'Staff TU Promo',
                'email' => 'tu_test_promotion@example.com',
                'password' => bcrypt('password'),
                'role_id' => $roleTu->id,
            ]
        );

        $roleMurid = Role::firstOrCreate(['nama' => 'murid'], ['deskripsi' => 'Murid']);
        $ta = \App\Models\TahunAjaran::firstOrCreate(['nama' => '2026/2027'], ['status_aktif' => true]);
        $semester = \App\Models\Semester::firstOrCreate(
            ['semester' => 'Ganjil', 'tahun_ajaran_id' => $ta->id],
            ['status_aktif' => true, 'tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2026-12-31']
        );

        $kelas7A = Kelas::firstOrCreate(['nama_kelas' => '7-A-Test'], ['tingkat' => 7, 'semester_id' => $semester->id]);
        $kelas8A = Kelas::firstOrCreate(['nama_kelas' => '8-A-Test'], ['tingkat' => 8, 'semester_id' => $semester->id]);

        $userSiswa = User::firstOrCreate(
            ['username' => 'siswa_test_promo'],
            [
                'nama' => 'Siswa Test Promo',
                'email' => 'siswa_test_promo@example.com',
                'password' => bcrypt('password'),
                'role_id' => $roleMurid->id,
            ]
        );

        $siswa = Siswa::firstOrCreate(
            ['user_id' => $userSiswa->id],
            [
                'nis' => '112233',
                'nisn' => '0011223344',
                'jenis_kelamin' => 'L',
                'kelas_id' => $kelas7A->id,
                'status' => 'aktif',
                'tanggal_masuk' => '2024-07-15',
            ]
        );

        Livewire::actingAs($userTu)
            ->test(ProsesKenaikanKelas::class)
            ->set('kelasAsalId', $kelas7A->id)
            ->set('aksiTujuan', 'naik_kelas')
            ->set('kelasTujuanId', $kelas8A->id)
            ->set('selectedSiswa', [(string)$siswa->id])
            ->call('prosesKenaikan')
            ->assertHasNoErrors();

        $siswa->refresh();
        $this->assertEquals($kelas8A->id, $siswa->kelas_id);
        $this->assertEquals('aktif', $siswa->status);
    }

    public function test_can_process_bulk_graduation()
    {
        $roleTu = Role::firstOrCreate(['nama' => 'tata_usaha'], ['deskripsi' => 'Tata Usaha']);
        $userTu = User::firstOrCreate(
            ['username' => 'tu_test_grad'],
            [
                'nama' => 'Staff TU Grad',
                'email' => 'tu_test_grad@example.com',
                'password' => bcrypt('password'),
                'role_id' => $roleTu->id,
            ]
        );

        $roleMurid = Role::firstOrCreate(['nama' => 'murid'], ['deskripsi' => 'Murid']);
        $ta = \App\Models\TahunAjaran::firstOrCreate(['nama' => '2026/2027'], ['status_aktif' => true]);
        $semester = \App\Models\Semester::firstOrCreate(
            ['semester' => 'Ganjil', 'tahun_ajaran_id' => $ta->id],
            ['status_aktif' => true, 'tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2026-12-31']
        );
        $kelas9A = Kelas::firstOrCreate(['nama_kelas' => '9-A-Test'], ['tingkat' => 9, 'semester_id' => $semester->id]);

        $userSiswa = User::firstOrCreate(
            ['username' => 'siswa_grad_test'],
            [
                'nama' => 'Siswa Grad Test',
                'email' => 'siswa_grad_test@example.com',
                'password' => bcrypt('password'),
                'role_id' => $roleMurid->id,
            ]
        );

        $siswa = Siswa::firstOrCreate(
            ['user_id' => $userSiswa->id],
            [
                'nis' => '998877',
                'nisn' => '0099887766',
                'jenis_kelamin' => 'P',
                'kelas_id' => $kelas9A->id,
                'status' => 'aktif',
                'tanggal_masuk' => '2024-07-15',
            ]
        );

        Livewire::actingAs($userTu)
            ->test(ProsesKenaikanKelas::class)
            ->set('kelasAsalId', $kelas9A->id)
            ->set('aksiTujuan', 'lulus_alumni')
            ->set('selectedSiswa', [(string)$siswa->id])
            ->call('prosesKenaikan')
            ->assertHasNoErrors();

        $siswa->refresh();
        $this->assertNull($siswa->kelas_id);
        $this->assertEquals('lulus', $siswa->status);
        $this->assertEquals(date('Y'), $siswa->tahun_lulus);
    }

    public function test_can_download_excel_tunggakan_report()
    {
        $roleFinance = Role::firstOrCreate(['nama' => 'finance'], ['deskripsi' => 'Finance']);
        $userFinance = User::firstOrCreate(
            ['username' => 'finance_test_export'],
            [
                'nama' => 'Bendahara Export Test',
                'email' => 'finance_test_export@example.com',
                'password' => bcrypt('password'),
                'role_id' => $roleFinance->id,
                'status' => 'aktif',
            ]
        );

        $response = $this->actingAs($userFinance)
            ->get(route('finance.export.tunggakan'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_tata_usaha_can_retain_student_tinggal_kelas()
    {
        $roleTu = Role::firstOrCreate(['nama' => 'tata_usaha'], ['deskripsi' => 'Tata Usaha']);
        $userTu = User::firstOrCreate(
            ['username' => 'tu_test_retain'],
            [
                'nama' => 'Staff TU Retain',
                'email' => 'tu_test_retain@example.com',
                'password' => bcrypt('password'),
                'role_id' => $roleTu->id,
            ]
        );

        $roleMurid = Role::firstOrCreate(['nama' => 'murid'], ['deskripsi' => 'Murid']);
        $ta = \App\Models\TahunAjaran::firstOrCreate(['nama' => '2026/2027'], ['status_aktif' => true]);
        $semester = \App\Models\Semester::firstOrCreate(
            ['semester' => 'Ganjil', 'tahun_ajaran_id' => $ta->id],
            ['status_aktif' => true, 'tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2026-12-31']
        );

        $kelas7A = Kelas::firstOrCreate(['nama_kelas' => '7-A-Retain'], ['tingkat' => 7, 'semester_id' => $semester->id]);
        $kelas8A = Kelas::firstOrCreate(['nama_kelas' => '8-A-Retain'], ['tingkat' => 8, 'semester_id' => $semester->id]);

        $userSiswaPromoted = User::create(['username' => 's_promo_' . rand(100,999), 'nama' => 'Siswa Promoted', 'email' => 's_promo_' . rand(100,999) . '@test.com', 'password' => bcrypt('password'), 'role_id' => $roleMurid->id]);
        $userSiswaRetained = User::create(['username' => 's_retain_' . rand(100,999), 'nama' => 'Siswa Retained', 'email' => 's_retain_' . rand(100,999) . '@test.com', 'password' => bcrypt('password'), 'role_id' => $roleMurid->id]);

        $nis1 = '77' . rand(1000, 9999);
        $nis2 = '78' . rand(1000, 9999);
        $siswaPromoted = Siswa::create(['user_id' => $userSiswaPromoted->id, 'nis' => $nis1, 'nisn' => 'N' . $nis1, 'jenis_kelamin' => 'L', 'kelas_id' => $kelas7A->id, 'status' => 'aktif', 'tanggal_masuk' => '2024-07-15']);
        $siswaRetained = Siswa::create(['user_id' => $userSiswaRetained->id, 'nis' => $nis2, 'nisn' => 'N' . $nis2, 'jenis_kelamin' => 'P', 'kelas_id' => $kelas7A->id, 'status' => 'aktif', 'tanggal_masuk' => '2024-07-15']);

        Livewire::actingAs($userTu)
            ->test(ProsesKenaikanKelas::class)
            ->set('kelasAsalId', $kelas7A->id)
            ->set('aksiTujuan', 'naik_kelas')
            ->set('kelasTujuanId', $kelas8A->id)
            ->set('selectedSiswa', [(string)$siswaPromoted->id, (string)$siswaRetained->id])
            ->call('toggleTinggalKelas', $siswaRetained->id)
            ->call('prosesKenaikan')
            ->assertHasNoErrors();

        $siswaPromoted->refresh();
        $siswaRetained->refresh();

        // Promoted student advances to 8A
        $this->assertEquals($kelas8A->id, $siswaPromoted->kelas_id);

        // Retained student stays in 7A
        $this->assertEquals($kelas7A->id, $siswaRetained->kelas_id);
        $this->assertEquals('aktif', $siswaRetained->status);

        // History record has status 'tinggal_kelas'
        $this->assertDatabaseHas('siswa_kelas', [
            'siswa_id' => $siswaRetained->id,
            'kelas_id' => $kelas7A->id,
            'status' => 'tinggal_kelas',
        ]);
    }
}
