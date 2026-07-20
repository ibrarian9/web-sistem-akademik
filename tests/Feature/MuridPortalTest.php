<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Tagihan;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use App\Models\Semester;
use Livewire\Livewire;
use App\Livewire\Murid\Dashboard;
use App\Livewire\Murid\RaporNilai;
use App\Livewire\Murid\KehadiranSaya;
use App\Livewire\Murid\JadwalPelajaran;
use App\Livewire\Murid\TagihanSpp;

beforeEach(function () {
    // Seed basic setups
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    $this->artisan('db:seed', ['--class' => 'KomponenNilaiSeeder']);
    $this->artisan('db:seed', ['--class' => 'PengaturanSeeder']);
    $this->artisan('db:seed', ['--class' => 'DemoDataSeeder']);
    $this->artisan('db:seed', ['--class' => 'JenisTagihanSeeder']);

    // Find a student user
    $this->userMurid = User::whereHas('role', function ($q) {
        $q->where('nama', 'murid');
    })->first();

    $this->siswa = $this->userMurid->siswa;
});

test('murid can render dashboard', function () {
    $this->actingAs($this->userMurid);

    Livewire::test(Dashboard::class)
        ->assertStatus(200)
        ->assertSee('Dashboard Murid');
});

test('murid can view attendance logs', function () {
    $this->actingAs($this->userMurid);

    Livewire::test(KehadiranSaya::class)
        ->assertStatus(200)
        ->assertSee('Kehadiran Saya');
});

test('murid can view class schedule', function () {
    $this->actingAs($this->userMurid);

    Livewire::test(JadwalPelajaran::class)
        ->assertStatus(200)
        ->assertSee('Jadwal Pelajaran Kelas');
});

test('murid can view invoice list', function () {
    $this->actingAs($this->userMurid);

    Livewire::test(TagihanSpp::class)
        ->assertStatus(200)
        ->assertSee('Daftar Tagihan Murid');
});

test('murid without outstanding bills can see rapor', function () {
    $this->actingAs($this->userMurid);

    // Delete or mark all invoices of this student as lunas
    Tagihan::where('siswa_id', $this->siswa->id)->update([
        'status' => 'lunas'
    ]);

    Livewire::test(RaporNilai::class)
        ->assertStatus(200)
        ->assertDontSee('Akses Rapor Terkunci');
});

test('murid with outstanding bills is blocked from viewing rapor', function () {
    $this->actingAs($this->userMurid);

    // Ensure there is at least one unpaid invoice
    $activeTA = TahunAjaran::where('status_aktif', true)->first();
    $jt = JenisTagihan::where('is_blocking', true)->first() ?? JenisTagihan::first();
    $jt->update(['is_blocking' => true]);
    
    Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jt->id,
        'tahun_ajaran_id' => $activeTA->id,
        'bulan' => 'Januari',
        'nominal' => 200000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => now()->subDays(1),
    ]);

    Livewire::test(RaporNilai::class)
        ->assertStatus(200)
        ->assertSee('Akses Rapor Terkunci');
});

test('murid with non-blocking outstanding bills (like infaq) can still view rapor', function () {
    $this->actingAs($this->userMurid);

    // Mark all existing tagihans as lunas
    Tagihan::where('siswa_id', $this->siswa->id)->update([
        'status' => 'lunas'
    ]);

    // Create an unpaid optional/non-blocking tagihan (Infaq)
    $activeTA = TahunAjaran::where('status_aktif', true)->first();
    $jtInfaq = JenisTagihan::where('nama', 'Infaq')->first();
    if (!$jtInfaq) {
        $jtInfaq = JenisTagihan::create([
            'nama' => 'Infaq',
            'kategori' => 'rutin',
            'default_nominal' => 50000.00,
            'is_blocking' => false,
        ]);
    }

    Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jtInfaq->id,
        'tahun_ajaran_id' => $activeTA->id,
        'bulan' => 'Januari',
        'nominal' => 50000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => now()->addDays(5),
    ]);

    Livewire::test(RaporNilai::class)
        ->assertStatus(200)
        ->assertDontSee('Akses Rapor Terkunci');
});
