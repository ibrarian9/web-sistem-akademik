<?php

namespace Tests\Feature;

use App\Livewire\SuperAdmin\TataKelola\ManajemenSiswa;
use App\Models\Guru;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TataUsahaShadowTeacherTest extends TestCase
{
    use RefreshDatabase;

    protected User $userTu;
    protected Guru $guruUmum;
    protected Guru $guruTahfidz;

    protected function setUp(): void
    {
        parent::setUp();

        $roleTu = Role::firstOrCreate(['nama' => 'tata_usaha'], ['deskripsi' => 'Tata Usaha']);
        $roleGuru = Role::firstOrCreate(['nama' => 'guru'], ['deskripsi' => 'Guru']);
        Role::firstOrCreate(['nama' => 'murid'], ['deskripsi' => 'Murid']);

        $this->userTu = User::factory()->create([
            'role_id' => $roleTu->id,
            'status' => 'aktif',
        ]);

        $userGuruUmum = User::factory()->create([
            'nama' => 'Ustadzah Sarah',
            'role_id' => $roleGuru->id,
            'status' => 'aktif',
        ]);
        $this->guruUmum = Guru::create([
            'user_id' => $userGuruUmum->id,
            'nip' => '198801012020012001',
            'jenis_guru' => 'umum',
            'status_kepegawaian' => 'tetap',
            'tanggal_masuk' => now()->toDateString(),
            'status_aktif' => true,
        ]);

        $userGuruTahfidz = User::factory()->create([
            'nama' => 'Ustadz Zaid',
            'role_id' => $roleGuru->id,
            'status' => 'aktif',
        ]);
        $this->guruTahfidz = Guru::create([
            'user_id' => $userGuruTahfidz->id,
            'nip' => '199002022020011002',
            'jenis_guru' => 'tahfidz',
            'status_kepegawaian' => 'tetap',
            'tanggal_masuk' => now()->toDateString(),
            'status_aktif' => true,
        ]);
    }

    public function test_tata_usaha_can_assign_shadow_teacher_from_guru_umum_or_tahfidz(): void
    {
        $this->actingAs($this->userTu);

        // 1. Create student with Guru Tahfidz as Shadow Teacher
        Livewire::test(ManajemenSiswa::class)
            ->call('openCreate')
            ->set('nama', 'Budi ABK')
            ->set('username', 'budi.abk')
            ->set('nis', '88881111')
            ->set('jenis_kelamin', 'L')
            ->set('password', 'password123')
            ->set('shadow_teacher_id', $this->guruTahfidz->id)
            ->call('save')
            ->assertHasNoErrors();

        $siswa = Siswa::where('nis', '88881111')->first();
        $this->assertNotNull($siswa);
        $this->assertEquals($this->guruTahfidz->id, $siswa->shadow_teacher_id);

        // 2. Verify search by shadow teacher name works
        Livewire::test(ManajemenSiswa::class)
            ->set('search', 'Zaid')
            ->assertSee('BUDI ABK')
            ->assertSee('GPK: Ustadz Zaid (Tahfizh)')
            ->call('openDetail', $siswa->id)
            ->assertSee('Shadow Teacher / GPK')
            ->assertSee('(Guru Tahfizh)');

        // 3. Tata usaha re-assigns shadow teacher to Guru Umum
        Livewire::test(ManajemenSiswa::class)
            ->call('openEdit', $siswa->id)
            ->set('shadow_teacher_id', $this->guruUmum->id)
            ->call('save')
            ->assertHasNoErrors();

        $siswa->refresh();
        $this->assertEquals($this->guruUmum->id, $siswa->shadow_teacher_id);

        Livewire::test(ManajemenSiswa::class)
            ->set('search', 'Sarah')
            ->assertSee('BUDI ABK')
            ->assertSee('GPK: Ustadzah Sarah (Umum)');
    }
}
