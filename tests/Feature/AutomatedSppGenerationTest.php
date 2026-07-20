<?php

namespace Tests\Feature;

use App\Models\JenisTagihan;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Livewire\Finance\ManajemenTagihan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AutomatedSppGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic database structures
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\JenisTagihanSeeder::class);

        $ta = TahunAjaran::create([
            'nama' => '2025/2026',
            'status_aktif' => true,
        ]);

        $roleSiswa = \App\Models\Role::where('nama', 'siswa')->first() ?? \App\Models\Role::create(['nama' => 'siswa', 'deskripsi' => 'Siswa']);
        $roleFinance = \App\Models\Role::where('nama', 'finance')->first() ?? \App\Models\Role::create(['nama' => 'finance', 'deskripsi' => 'Finance']);

        $user1 = User::create([
            'role_id' => $roleSiswa->id,
            'nama' => 'Siswa Test Satu',
            'username' => 'siswa1',
            'email' => 'siswa1@test.com',
            'password' => bcrypt('password'),
        ]);

        Siswa::create([
            'user_id' => $user1->id,
            'nis' => '1001',
            'status' => 'aktif',
            'tanggal_masuk' => now()->toDateString(),
        ]);

        $user2 = User::create([
            'role_id' => $roleSiswa->id,
            'nama' => 'Siswa Test Dua',
            'username' => 'siswa2',
            'email' => 'siswa2@test.com',
            'password' => bcrypt('password'),
        ]);

        Siswa::create([
            'user_id' => $user2->id,
            'nis' => '1002',
            'status' => 'aktif',
            'tanggal_masuk' => now()->toDateString(),
        ]);
    }

    public function test_artisan_command_generates_spp_for_active_students(): void
    {
        $this->artisan('tagihan:generate-spp Juli --nominal=350000 --due-day=10')
            ->assertExitCode(0);

        $this->assertDatabaseHas('tagihan', [
            'bulan' => 'Juli',
            'nominal' => 350000,
        ]);

        $this->assertEquals(2, Tagihan::where('bulan', 'Juli')->count());
    }

    public function test_artisan_command_prevents_duplicate_generation(): void
    {
        // First run
        $this->artisan('tagihan:generate-spp Juli --nominal=350000 --due-day=10')
            ->assertExitCode(0);

        // Second run
        $this->artisan('tagihan:generate-spp Juli --nominal=350000 --due-day=10')
            ->assertExitCode(0);

        // Count should remain 2
        $this->assertEquals(2, Tagihan::where('bulan', 'Juli')->count());
    }

    public function test_livewire_action_generates_spp_bulanan(): void
    {
        $roleFinance = \App\Models\Role::where('nama', 'finance')->first();
        $financeUser = User::create([
            'role_id' => $roleFinance->id,
            'nama' => 'Bendahara Finance',
            'username' => 'finance_test',
            'email' => 'finance@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($financeUser);

        Livewire::test(ManajemenTagihan::class)
            ->set('autoBulan', 'Agustus')
            ->set('autoNominal', 400000)
            ->set('autoJatuhTempo', '2026-08-10')
            ->call('generateAutoSppBulanan');

        $this->assertDatabaseHas('tagihan', [
            'bulan' => 'Agustus',
            'nominal' => 400000,
        ]);

        $this->assertEquals(2, Tagihan::where('bulan', 'Agustus')->count());
    }
}
