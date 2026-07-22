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
}
