<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\SuperAdmin2ProductionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class SuperAdmin2ProductionSeederAndMurottalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['nama' => 'super_admin_2']);
        Role::firstOrCreate(['nama' => 'super_admin']);
        Role::firstOrCreate(['nama' => 'founder']);
        Role::firstOrCreate(['nama' => 'pengawas']);
    }

    public function test_super_admin_2_production_seeder_creates_rina_account(): void
    {
        $this->seed(SuperAdmin2ProductionSeeder::class);

        $user = User::where('email', 'Ina724152@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Rina', $user->nama);
        $this->assertEquals('rina', $user->username);
        $this->assertEquals('super_admin_2', $user->role->nama);
        $this->assertEquals('aktif', $user->status);
        $this->assertTrue(Hash::check('724152', $user->password));
        $this->assertTrue($user->isSuperAdmin2());
        $this->assertTrue($user->isReadOnlyAdmin());
        $this->assertTrue($user->canApproveFinancial());
    }

    public function test_founder_and_pengawas_roles_equivalent_to_super_admin_and_super_admin_2(): void
    {
        $roleFounder = Role::where('nama', 'founder')->first();
        $rolePengawas = Role::where('nama', 'pengawas')->first();

        $userFounder = User::factory()->create([
            'role_id' => $roleFounder->id,
            'status' => 'aktif',
        ]);

        $userPengawas = User::factory()->create([
            'role_id' => $rolePengawas->id,
            'status' => 'aktif',
        ]);

        // Founder is super admin
        $this->assertTrue($userFounder->isSuperAdmin());
        $this->assertTrue($userFounder->canApproveFinancial());

        // Pengawas is super admin 2 (viewer / oversight)
        $this->assertTrue($userPengawas->isSuperAdmin2());
        $this->assertTrue($userPengawas->isReadOnlyAdmin());
        $this->assertTrue($userPengawas->canApproveFinancial());
    }

    public function test_dashboard_renders_quran_audio_player(): void
    {
        $roleSuperAdmin2 = Role::where('nama', 'super_admin_2')->first();
        $user = User::factory()->create([
            'nama' => 'Rina',
            'role_id' => $roleSuperAdmin2->id,
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($user)->get(route('super-admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('murottal.mp3');
        $response->assertSee('Surah Al-Fatihah');
        $response->assertSee('Lantunan Al-Qur\'an', false);
    }
}
