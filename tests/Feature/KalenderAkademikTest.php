<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\KalenderAkademik;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Models\Role;
use Livewire\Livewire;
use App\Livewire\TataUsaha\ManajemenKalenderAkademik;

use Illuminate\Foundation\Testing\RefreshDatabase;

class KalenderAkademikTest extends TestCase
{
    use RefreshDatabase;
    public function test_can_create_and_check_holiday_event()
    {
        $ta = TahunAjaran::firstOrCreate(
            ['nama' => '2026/2027'],
            ['status_aktif' => true]
        );

        $event = KalenderAkademik::create([
            'tahun_ajaran_id' => $ta->id,
            'nama_kegiatan' => 'Libur Hari Raya Idul Fitri',
            'jenis' => 'hari_libur',
            'tanggal_mulai' => '2026-04-10',
            'tanggal_selesai' => '2026-04-15',
            'liburkan_presensi' => true,
            'keterangan' => 'Libur resmi nasional',
        ]);

        $this->assertTrue(KalenderAkademik::isHolidayDate('2026-04-12'));
        $this->assertFalse(KalenderAkademik::isHolidayDate('2026-04-20'));
    }

    public function test_livewire_kalender_akademik_component_can_render_and_create()
    {
        $role = Role::firstOrCreate(['nama' => 'tata_usaha'], ['deskripsi' => 'Tata Usaha']);
        $user = User::firstOrCreate(
            ['username' => 'tu_test_kalender'],
            [
                'nama' => 'Staff TU Test',
                'email' => 'tu_test_kalender@example.com',
                'password' => bcrypt('password'),
                'role_id' => $role->id,
            ]
        );

        $ta = TahunAjaran::firstOrCreate(
            ['nama' => '2026/2027'],
            ['status_aktif' => true]
        );

        Livewire::actingAs($user)
            ->test(ManajemenKalenderAkademik::class)
            ->assertStatus(200)
            ->set('tahun_ajaran_id', $ta->id)
            ->set('nama_kegiatan', 'Libur Semester Ganjil')
            ->set('jenis', 'libur_semester')
            ->set('tanggal_mulai', '2026-12-20')
            ->set('tanggal_selesai', '2026-12-31')
            ->set('liburkan_presensi', true)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('kalender_akademik', [
            'nama_kegiatan' => 'Libur Semester Ganjil',
            'jenis' => 'libur_semester',
        ]);
    }

    public function test_can_create_set_active_and_delete_tahun_ajaran()
    {
        $role = Role::firstOrCreate(['nama' => 'tata_usaha'], ['deskripsi' => 'Tata Usaha']);
        $user = User::firstOrCreate(
            ['username' => 'tu_test_ta_crud'],
            [
                'nama' => 'Staff TU Test TA',
                'email' => 'tu_test_ta_crud@example.com',
                'password' => bcrypt('password'),
                'role_id' => $role->id,
            ]
        );

        // 1. Create new Tahun Ajaran
        Livewire::actingAs($user)
            ->test(ManajemenKalenderAkademik::class)
            ->set('newTahunAjaranNama', '2035/2036')
            ->set('tglMulaiGanjil', '2035-07-01')
            ->set('tglSelesaiGanjil', '2035-12-31')
            ->set('tglMulaiGenap', '2036-01-01')
            ->set('tglSelesaiGenap', '2036-06-30')
            ->call('createTahunAjaran')
            ->assertHasNoErrors();

        $taNew = TahunAjaran::where('nama', '2035/2036')->first();
        $this->assertNotNull($taNew);
        $this->assertEquals(2, $taNew->semesters()->count());

        // 2. Set Active
        Livewire::actingAs($user)
            ->test(ManajemenKalenderAkademik::class)
            ->call('setTahunAjaranAktif', $taNew->id)
            ->assertHasNoErrors();

        $this->assertTrue((bool)$taNew->fresh()->status_aktif);

        // 3. Delete unused Tahun Ajaran (must set another active first)
        Livewire::actingAs($user)
            ->test(ManajemenKalenderAkademik::class)
            ->set('newTahunAjaranNama', '2036/2037')
            ->set('tglMulaiGanjil', '2036-07-01')
            ->set('tglSelesaiGanjil', '2036-12-31')
            ->set('tglMulaiGenap', '2037-01-01')
            ->set('tglSelesaiGenap', '2037-06-30')
            ->call('createTahunAjaran')
            ->assertHasNoErrors();

        $taOther = TahunAjaran::where('nama', '2036/2037')->first();

        Livewire::actingAs($user)
            ->test(ManajemenKalenderAkademik::class)
            ->call('setTahunAjaranAktif', $taOther->id)
            ->call('deleteTahunAjaran', $taNew->id)
            ->assertHasNoErrors();

        // 4. Custom Edit Semester Dates
        $sem = $taOther->semesters()->first();
        Livewire::actingAs($user)
            ->test(ManajemenKalenderAkademik::class)
            ->call('openEditSemester', $sem->id)
            ->set('editSemesterMulai', '2030-08-01')
            ->set('editSemesterSelesai', '2031-01-15')
            ->call('saveSemesterDates')
            ->assertHasNoErrors();

        $this->assertEquals('2030-08-01', $sem->fresh()->tanggal_mulai->format('Y-m-d'));
        $this->assertEquals('2031-01-15', $sem->fresh()->tanggal_selesai->format('Y-m-d'));
    }
}
