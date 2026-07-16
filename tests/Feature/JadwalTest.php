<?php

use App\Models\User;
use App\Models\Role;
use App\Models\GuruMapelKelas;
use App\Models\JadwalPelajaran;
use Livewire\Livewire;
use App\Livewire\SuperAdmin\TataKelola\ManajemenJadwal;

beforeEach(function () {
    // Seed everything
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    $this->artisan('db:seed', ['--class' => 'DemoDataSeeder']);
    
    // Authenticate as Super Admin
    $admin = User::whereHas('role', function ($q) {
        $q->where('nama', 'super_admin');
    })->first();
    
    $this->actingAs($admin);
});

it('renders schedule management page successfully', function () {
    $response = $this->get(route('super-admin.jadwal'));
    $response->assertStatus(200);
    $response->assertSee('Jadwal Pelajaran');
});

it('prevents overlapping schedule for the same class', function () {
    // Let's get an existing assignment
    $asg1 = GuruMapelKelas::first(); // Class 7A, Mapel MTK, Guru Budi (for example)
    
    // Create an initial schedule
    $sched = JadwalPelajaran::create([
        'guru_mapel_kelas_id' => $asg1->id,
        'hari' => 'senin',
        'jam_mulai' => '08:00',
        'jam_selesai' => '09:30',
    ]);

    // Try to schedule another mapel for the same class at overlapping time (08:30 - 10:00)
    // Find another assignment for the same class
    $asg2 = GuruMapelKelas::where('kelas_id', $asg1->kelas_id)
        ->where('id', '!=', $asg1->id)
        ->first();

    if ($asg2) {
        Livewire::test(ManajemenJadwal::class)
            ->set('guru_mapel_kelas_id', $asg2->id)
            ->set('hari', 'senin')
            ->set('jam_mulai', '08:30')
            ->set('jam_selesai', '10:00')
            ->call('save')
            ->assertHasErrors(['guru_mapel_kelas_id']);
            
        // Assert that conflicting schedule was NOT written to database
        $this->assertDatabaseMissing('jadwal_pelajaran', [
            'guru_mapel_kelas_id' => $asg2->id,
            'hari' => 'senin',
            'jam_mulai' => '08:30',
            'jam_selesai' => '10:00',
        ]);
    }
});

it('prevents overlapping schedule for the same teacher', function () {
    // Get an assignment
    $asg1 = GuruMapelKelas::first(); // Guru Budi, Class 7A, Mapel MTK
    
    // Create initial schedule
    JadwalPelajaran::create([
        'guru_mapel_kelas_id' => $asg1->id,
        'hari' => 'selasa',
        'jam_mulai' => '09:00',
        'jam_selesai' => '10:30',
    ]);

    // Find another assignment for the SAME teacher but a different class (if exists in demo data)
    $asg2 = GuruMapelKelas::where('guru_id', $asg1->guru_id)
        ->where('id', '!=', $asg1->id)
        ->first();

    if ($asg2) {
        Livewire::test(ManajemenJadwal::class)
            ->set('guru_mapel_kelas_id', $asg2->id)
            ->set('hari', 'selasa')
            ->set('jam_mulai', '09:30')
            ->set('jam_selesai', '11:00')
            ->call('save')
            ->assertHasErrors(['guru_mapel_kelas_id']);
    }
});
