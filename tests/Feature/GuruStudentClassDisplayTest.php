<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\User;
use App\Livewire\Guru\AbsensiSiswa;
use App\Livewire\Guru\InputNilaiSiswa;
use App\Livewire\Guru\KelolaRapor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GuruStudentClassDisplayTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Guru Tahfizh can see their Tahfizh class and student names in AbsensiSiswa.
     */
    public function test_guru_tahfidz_sees_tahfidz_class_and_students_in_absensi(): void
    {
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        $guruRole = Role::where('nama', 'guru')->first();
        $muridRole = Role::where('nama', 'murid')->first();

        // 0. Create active Semester
        $tahun = \App\Models\TahunAjaran::create(['nama' => '2025/2026', 'status_aktif' => true]);
        $semester = \App\Models\Semester::create([
            'tahun_ajaran_id' => $tahun->id,
            'semester' => 'ganjil',
            'tanggal_mulai' => date('Y-07-01'),
            'tanggal_selesai' => date('Y-12-31'),
            'status_aktif' => true,
        ]);

        // 1. Create Guru Tahfizh
        $userGuru = User::factory()->create([
            'nama' => 'Ustadz Abdullah',
            'username' => 'ustadbullah',
            'role_id' => $guruRole->id,
        ]);
        $guru = Guru::create([
            'user_id' => $userGuru->id,
            'nip' => '1998237198273',
            'jenis_guru' => 'tahfidz',
            'tanggal_masuk' => date('Y-m-d'),
        ]);

        // 2. Create Kelas Tahfizh assigned to this Guru Tahfizh
        $kelasTahfidz = Kelas::create([
            'nama_kelas' => 'Halaqah Al-Fatihah',
            'jenis_kelas' => 'tahfidz',
            'tingkat' => 1,
            'semester_id' => $semester->id,
            'guru_tahfidz_id' => $guru->id,
        ]);

        // 3. Create Student assigned to this Tahfizh Class
        $userSiswa = User::factory()->create([
            'nama' => 'Muhammad Zaky',
            'username' => 'zaky123',
            'role_id' => $muridRole->id,
        ]);
        $siswa = Siswa::create([
            'user_id' => $userSiswa->id,
            'nis' => '10001',
            'nisn' => '009128312',
            'jenis_kelamin' => 'L',
            'kelas_tahfidz_id' => $kelasTahfidz->id,
            'tanggal_masuk' => date('Y-m-d'),
            'status' => 'aktif',
        ]);

        $this->actingAs($userGuru);

        // 4. Test AbsensiSiswa Livewire component
        Livewire::test(AbsensiSiswa::class)
            ->assertStatus(200)
            ->set('kelas_id', $kelasTahfidz->id)
            ->assertSee('Halaqah Al-Fatihah')
            ->assertSee('Muhammad Zaky');
    }

    /**
     * Test Guru Umum sees their General class and student names in AbsensiSiswa & InputNilaiSiswa.
     */
    public function test_guru_umum_sees_general_class_and_students(): void
    {
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        $guruRole = Role::where('nama', 'guru')->first();
        $muridRole = Role::where('nama', 'murid')->first();

        // 0. Create active Semester
        $tahun2 = \App\Models\TahunAjaran::create(['nama' => '2026/2027', 'status_aktif' => true]);
        $semester2 = \App\Models\Semester::create([
            'tahun_ajaran_id' => $tahun2->id,
            'semester' => 'ganjil',
            'tanggal_mulai' => date('Y-07-01'),
            'tanggal_selesai' => date('Y-12-31'),
            'status_aktif' => true,
        ]);

        // 1. Create Guru Umum
        $userGuru = User::factory()->create([
            'nama' => 'Ibu Rahmawati',
            'username' => 'iburahma',
            'role_id' => $guruRole->id,
        ]);
        $guru = Guru::create([
            'user_id' => $userGuru->id,
            'nip' => '1990237198274',
            'jenis_guru' => 'umum',
            'tanggal_masuk' => date('Y-m-d'),
        ]);

        // 2. Create Kelas Umum
        $kelasUmum = Kelas::create([
            'nama_kelas' => 'Kelas 3A',
            'jenis_kelas' => 'umum',
            'tingkat' => 3,
            'semester_id' => $semester2->id,
            'guru_umum_id' => $guru->id,
        ]);

        // 3. Create Student assigned to Kelas 3A
        $userSiswa = User::factory()->create([
            'nama' => 'Aisha Maryam',
            'username' => 'aisha3a',
            'role_id' => $muridRole->id,
        ]);
        $siswa = Siswa::create([
            'user_id' => $userSiswa->id,
            'nis' => '10002',
            'nisn' => '009128313',
            'jenis_kelamin' => 'P',
            'kelas_id' => $kelasUmum->id,
            'tanggal_masuk' => date('Y-m-d'),
            'status' => 'aktif',
        ]);

        $this->actingAs($userGuru);

        // Test AbsensiSiswa
        Livewire::test(AbsensiSiswa::class)
            ->assertStatus(200)
            ->set('kelas_id', $kelasUmum->id)
            ->assertSee('Kelas 3A')
            ->assertSee('Aisha Maryam');

        // Test KelolaRapor
        Livewire::test(KelolaRapor::class)
            ->assertStatus(200)
            ->set('kelasId', $kelasUmum->id)
            ->assertSee('Aisha Maryam');
    }
}
