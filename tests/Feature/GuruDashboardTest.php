<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Role;
use App\Models\Semester;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Livewire\Guru\Dashboard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GuruDashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Guru Dashboard displays stats and schedules for assigned classes.
     */
    public function test_guru_dashboard_populates_stats_and_schedules(): void
    {
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        $guruRole = Role::where('nama', 'guru')->first();

        // Create active semester
        $tahun = TahunAjaran::create(['nama' => '2025/2026', 'status_aktif' => true]);
        $semester = Semester::create([
            'tahun_ajaran_id' => $tahun->id,
            'semester' => 'ganjil',
            'tanggal_mulai' => date('Y-07-01'),
            'tanggal_selesai' => date('Y-12-31'),
            'status_aktif' => true,
        ]);

        // Create Guru Tahfizh
        $userGuru = User::factory()->create([
            'nama' => 'Ustadz Nurul',
            'username' => 'ustadznurul',
            'role_id' => $guruRole->id,
        ]);
        $guru = Guru::create([
            'user_id' => $userGuru->id,
            'nip' => '199283719283',
            'jenis_guru' => 'tahfidz',
            'tanggal_masuk' => date('Y-m-d'),
        ]);

        // Create Kelas Tahfizh assigned to this Guru
        $kelasTahfidz = Kelas::create([
            'nama_kelas' => 'Halaqah An-Nur',
            'jenis_kelas' => 'tahfidz',
            'tingkat' => 1,
            'semester_id' => $semester->id,
            'guru_tahfidz_id' => $guru->id,
        ]);

        $this->actingAs($userGuru);

        Livewire::test(Dashboard::class)
            ->assertStatus(200)
            ->assertSet('totalKelas', 1)
            ->assertSee('Selamat Datang, Ustadz Nurul')
            ->assertSee('Halaqah An-Nur');
    }
}
