<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\KalenderAkademik;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Models\Role;
use Livewire\Livewire;
use App\Livewire\TataUsaha\ManajemenKalenderAkademik;

class KalenderAkademikTest extends TestCase
{
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
}
