<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Guru;
use Livewire\Livewire;
use App\Livewire\SuperAdmin\TataKelola\AuditLog;
use App\Livewire\SuperAdmin\TataKelola\ManajemenGuru;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleFilterAuditAndGuruTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $guruUser;
    protected User $tuUser;
    protected Role $roleAdmin;
    protected Role $roleGuru;
    protected Role $roleTU;

    protected function setUp(): void
    {
        parent::setUp();

        $this->roleAdmin = Role::firstOrCreate(['nama' => 'super_admin'], ['deskripsi' => 'Super Administrator']);
        $this->roleGuru = Role::firstOrCreate(['nama' => 'guru'], ['deskripsi' => 'Guru']);
        $this->roleTU = Role::firstOrCreate(['nama' => 'tata_usaha'], ['deskripsi' => 'Tata Usaha']);

        $this->superAdmin = User::factory()->create([
            'nama' => 'Admin Utama',
            'username' => 'admin_super_' . uniqid(),
            'role_id' => $this->roleAdmin->id,
            'status' => 'aktif',
        ]);

        $this->guruUser = User::factory()->create([
            'nama' => 'Ust. Zaid Guru',
            'username' => 'guru_zaid_' . uniqid(),
            'role_id' => $this->roleGuru->id,
            'status' => 'aktif',
        ]);

        $this->tuUser = User::factory()->create([
            'nama' => 'Ibu Siti TU',
            'username' => 'tu_siti_' . uniqid(),
            'role_id' => $this->roleTU->id,
            'status' => 'aktif',
        ]);
    }

    public function test_audit_log_filters_by_role_correctly(): void
    {
        // 1. Insert activity logs for different roles
        DB::table('activity_log')->insert([
            'log_name' => 'default',
            'description' => 'Admin melakukan backup database',
            'event' => 'created',
            'causer_id' => $this->superAdmin->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('activity_log')->insert([
            'log_name' => 'default',
            'description' => 'Guru menginput nilai harian',
            'event' => 'created',
            'causer_id' => $this->guruUser->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Filter by 'super_admin'
        Livewire::actingAs($this->superAdmin)
            ->test(AuditLog::class)
            ->set('filterRole', 'super_admin')
            ->assertSee('Admin melakukan backup database')
            ->assertDontSee('Guru menginput nilai harian');

        // 3. Filter by 'guru'
        Livewire::actingAs($this->superAdmin)
            ->test(AuditLog::class)
            ->set('filterRole', 'guru')
            ->assertSee('Guru menginput nilai harian')
            ->assertDontSee('Admin melakukan backup database');
    }

    public function test_manajemen_guru_filters_by_role_correctly(): void
    {
        // 1. Create a guru with role 'guru'
        Guru::create([
            'user_id' => $this->guruUser->id,
            'nip' => '19900101',
            'grade_guru' => 'A',
            'tanggal_masuk' => '2020-01-01',
            'status_aktif' => true,
        ]);

        // 2. Create a second guru who also has role 'tata_usaha'
        Guru::create([
            'user_id' => $this->tuUser->id,
            'nip' => '19920202',
            'grade_guru' => 'B',
            'tanggal_masuk' => '2020-01-01',
            'status_aktif' => true,
        ]);

        // 3. Test filter role = 'guru'
        Livewire::actingAs($this->superAdmin)
            ->test(ManajemenGuru::class)
            ->set('filterRole', 'guru')
            ->assertSee('UST. ZAID GURU')
            ->assertDontSee('IBU SITI TU');

        // 4. Test filter role = 'tata_usaha'
        Livewire::actingAs($this->superAdmin)
            ->test(ManajemenGuru::class)
            ->set('filterRole', 'tata_usaha')
            ->assertSee('IBU SITI TU')
            ->assertDontSee('UST. ZAID GURU');
    }
}
