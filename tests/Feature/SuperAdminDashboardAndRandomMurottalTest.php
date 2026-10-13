<?php

use App\Models\User;
use App\Models\Role;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Livewire\SuperAdmin\Dashboard as SuperAdminDashboard;
use Livewire\Livewire;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    $this->artisan('db:seed', ['--class' => 'PengaturanSeeder']);

    $this->roleSuperAdmin = Role::where('nama', 'super_admin')->first();

    $this->superAdminUser = User::create([
        'nama' => 'Super Admin Test',
        'username' => 'super_admin_test_' . uniqid(),
        'email' => 'superadmin_test_' . uniqid() . '@sekolah.id',
        'password' => bcrypt('password123'),
        'role_id' => $this->roleSuperAdmin->id,
        'status' => 'aktif',
    ]);

    $this->ta = TahunAjaran::firstOrCreate(
        ['status_aktif' => true],
        ['nama' => '2025/2026']
    );

    $this->semester = Semester::firstOrCreate(
        ['status_aktif' => true],
        [
            'tahun_ajaran_id' => $this->ta->id,
            'semester' => 'ganjil',
            'tanggal_mulai' => now()->startOfYear(),
            'tanggal_selesai' => now()->endOfYear(),
        ]
    );
});

test('super admin dashboard renders informative metrics and context cleanly', function () {
    $this->actingAs($this->superAdminUser);

    // Seed activity log entry
    DB::table('activity_log')->insert([
        'log_name' => 'default',
        'description' => 'Memperbarui data Pengaturan Sekolah',
        'event' => 'updated',
        'subject_type' => 'App\Models\Pengaturan',
        'causer_type' => 'App\Models\User',
        'causer_id' => $this->superAdminUser->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this->get(route('super-admin.dashboard'));
    $response->assertStatus(200);
    $response->assertSee('Selamat Datang, Super Admin Test');
    $response->assertSee('PANEL SUPER ADMIN');
    $response->assertSee('TA ' . $this->ta->nama);
    $response->assertSee('Aktivitas Sistem Terbaru');
    $response->assertSee('Status Server');
    $response->assertSee('Mutiara Ayat', false);
    $response->assertSee('Acak Ayat Baru');

    Livewire::test(SuperAdminDashboard::class)
        ->assertStatus(200)
        ->assertSee('Total Siswa Aktif')
        ->assertSee('Total Guru & Staf')
        ->assertSee('Total Kelas')
        ->assertSee('Tunggakan SPP')
        ->assertSee('Aktivitas Sistem Terbaru')
        ->assertSee('MariaDB Online');
});

test('quran audio player provides randomized surah and ayah functionality', function () {
    $this->actingAs($this->superAdminUser);

    $response = $this->get(route('super-admin.dashboard'));
    $response->assertStatus(200);

    // Verify audio component elements
    $response->assertSee('curatedAyahs');
    $response->assertSee('pickRandomAyah');
    $response->assertSee('nextRandomAyah');
    $response->assertSee('Lantunan Al-Qur\'an', false);
    $response->assertSee('Misyari Rasyid Al-Afasy');
    $response->assertSee('murottal.mp3');
});
