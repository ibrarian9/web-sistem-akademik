<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Role;
use App\Models\User;
use App\Livewire\Guru\ManajemenKurikulumMerdeka;
use App\Livewire\Guru\InputNilaiSumatif;
use App\Livewire\Guru\PenilaianP5;
use App\Livewire\Guru\InputNilaiTahfidz;
use App\Livewire\Guru\KelolaRapor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GuruRoleAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Guru Tahfizh is blocked from General Kurikulum Merdeka Livewire components.
     */
    public function test_guru_tahfidz_blocked_from_kurikulum_merdeka_components(): void
    {
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        $guruRole = Role::where('nama', 'guru')->first();

        $userTahfizh = User::factory()->create([
            'username' => 'ustadztahfizh_' . rand(10000, 99999),
            'role_id' => $guruRole->id,
        ]);

        Guru::create([
            'user_id' => $userTahfizh->id,
            'nip' => (string)rand(1000000000, 9999999999),
            'jenis_guru' => 'tahfidz',
            'tanggal_masuk' => date('Y-m-d'),
        ]);

        $this->actingAs($userTahfizh);

        // Accessing Kurikulum Merdeka redirects to input-tahfidz
        Livewire::test(ManajemenKurikulumMerdeka::class)
            ->assertRedirect(route('guru.input-tahfidz'));

        // Accessing Input Sumatif redirects to input-tahfidz
        Livewire::test(InputNilaiSumatif::class)
            ->assertRedirect(route('guru.input-tahfidz'));

        // Accessing Penilaian P5 redirects to input-tahfidz
        Livewire::test(PenilaianP5::class)
            ->assertRedirect(route('guru.input-tahfidz'));

        // Accessing Input Tahfizh -> OK
        Livewire::test(InputNilaiTahfidz::class)
            ->assertStatus(200);

        // Accessing Kelola Rapor -> OK with Rapor Tahfizh mode
        Livewire::test(KelolaRapor::class)
            ->assertStatus(200)
            ->assertSet('tipeRapor', 'tahfizh');
    }

    /**
     * Test Guru Umum is blocked from Input Tahfizh Livewire component.
     */
    public function test_guru_umum_blocked_from_input_tahfidz_component(): void
    {
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        $guruRole = Role::where('nama', 'guru')->first();

        $userUmum = User::factory()->create([
            'username' => 'guruteladan_' . rand(10000, 99999),
            'role_id' => $guruRole->id,
        ]);

        Guru::create([
            'user_id' => $userUmum->id,
            'nip' => (string)rand(1000000000, 9999999999),
            'jenis_guru' => 'umum',
            'tanggal_masuk' => date('Y-m-d'),
        ]);

        $this->actingAs($userUmum);

        // Accessing Input Tahfizh redirects to kurikulum-merdeka
        Livewire::test(InputNilaiTahfidz::class)
            ->assertRedirect(route('guru.kurikulum-merdeka'));
    }
}
