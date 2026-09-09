<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Guru;
use App\Models\Peminjaman;
use Livewire\Livewire;
use App\Livewire\Finance\ManajemenPeminjaman;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class KasbonGuruFilterWaktuTest extends TestCase
{
    use RefreshDatabase;

    protected User $userFinance;
    protected Guru $guru1;
    protected Guru $guru2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        $this->artisan('db:seed', ['--class' => 'PengaturanSeeder']);

        $roleFinance = Role::where('nama', 'finance')->first();
        $roleGuru = Role::firstOrCreate(['nama' => 'guru'], ['deskripsi' => 'Guru']);

        $this->userFinance = User::create([
            'nama' => 'Staff Finance Kasbon',
            'username' => 'finance_kasbon_' . uniqid(),
            'email' => 'finance_kasbon_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role_id' => $roleFinance->id,
            'status' => 'aktif',
        ]);

        $guruUser1 = User::create([
            'nama' => 'Ustadz Ahmad Fauzi',
            'username' => 'guru_fauzi_' . uniqid(),
            'email' => 'fauzi_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role_id' => $roleGuru->id,
            'status' => 'aktif',
        ]);
        $this->guru1 = Guru::create([
            'user_id' => $guruUser1->id,
            'nip' => '11223344',
            'tanggal_masuk' => '2020-01-01',
            'status_aktif' => true,
        ]);

        $guruUser2 = User::create([
            'nama' => 'Ustadzah Siti Aisyah',
            'username' => 'guru_siti_' . uniqid(),
            'email' => 'siti_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role_id' => $roleGuru->id,
            'status' => 'aktif',
        ]);
        $this->guru2 = Guru::create([
            'user_id' => $guruUser2->id,
            'nip' => '55667788',
            'tanggal_masuk' => '2021-01-01',
            'status_aktif' => true,
        ]);

        // 1. Kasbon Hari Ini (Ahmad Fauzi)
        Peminjaman::create([
            'guru_id' => $this->guru1->id,
            'tanggal_pinjam' => Carbon::today()->format('Y-m-d'),
            'nominal' => 1000000,
            'tenor_bulan' => 5,
            'cicilan_per_bulan' => 200000,
            'sisa_pinjaman' => 1000000,
            'status' => 'berjalan',
        ]);

        // 2. Kasbon Kemarin (Siti Aisyah)
        Peminjaman::create([
            'guru_id' => $this->guru2->id,
            'tanggal_pinjam' => Carbon::yesterday()->format('Y-m-d'),
            'nominal' => 2000000,
            'tenor_bulan' => 10,
            'cicilan_per_bulan' => 200000,
            'sisa_pinjaman' => 0,
            'status' => 'lunas',
        ]);

        // 3. Kasbon Bulan Lalu
        Peminjaman::create([
            'guru_id' => $this->guru1->id,
            'tanggal_pinjam' => Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d'),
            'nominal' => 500000,
            'tenor_bulan' => 2,
            'cicilan_per_bulan' => 250000,
            'sisa_pinjaman' => 0,
            'status' => 'lunas',
        ]);
    }

    public function test_filter_waktu_hari_ini_pada_kasbon_guru(): void
    {
        Livewire::actingAs($this->userFinance)
            ->test(ManajemenPeminjaman::class)
            ->set('filterPeriode', 'hari_ini')
            ->assertSee('Ustadz Ahmad Fauzi')
            ->assertSee('Rp 1.000.000')
            ->assertDontSee('Ustadzah Siti Aisyah')
            ->assertDontSee('Rp 2.000.000');
    }

    public function test_filter_waktu_kemarin_pada_kasbon_guru(): void
    {
        Livewire::actingAs($this->userFinance)
            ->test(ManajemenPeminjaman::class)
            ->set('filterPeriode', 'kemarin')
            ->assertSee('Ustadzah Siti Aisyah')
            ->assertSee('Rp 2.000.000')
            ->assertDontSee('Rp 1.000.000');
    }

    public function test_filter_waktu_bulan_lalu_pada_kasbon_guru(): void
    {
        Livewire::actingAs($this->userFinance)
            ->test(ManajemenPeminjaman::class)
            ->set('filterPeriode', 'bulan_lalu')
            ->assertSee('Rp 500.000')
            ->assertDontSee('Rp 1.000.000')
            ->assertDontSee('Rp 2.000.000');
    }

    public function test_filter_waktu_tahun_ini_pada_kasbon_guru(): void
    {
        Livewire::actingAs($this->userFinance)
            ->test(ManajemenPeminjaman::class)
            ->set('filterPeriode', 'tahun_ini')
            ->assertSee('Ustadz Ahmad Fauzi')
            ->assertSee('Ustadzah Siti Aisyah')
            ->assertSee('Rp 1.000.000')
            ->assertSee('Rp 2.000.000');
    }

    public function test_filter_waktu_custom_range_pada_kasbon_guru(): void
    {
        Livewire::actingAs($this->userFinance)
            ->test(ManajemenPeminjaman::class)
            ->set('filterPeriode', 'custom')
            ->set('startDate', Carbon::yesterday()->format('Y-m-d'))
            ->set('endDate', Carbon::yesterday()->format('Y-m-d'))
            ->assertSee('Ustadzah Siti Aisyah')
            ->assertDontSee('Rp 1.000.000')
            ->assertDontSee('Rp 500.000');
    }

    public function test_filter_kombinasi_waktu_dan_status_pinjaman(): void
    {
        Livewire::actingAs($this->userFinance)
            ->test(ManajemenPeminjaman::class)
            ->set('filterPeriode', 'tahun_ini')
            ->set('filterStatus', 'lunas')
            ->assertSee('Ustadzah Siti Aisyah')
            ->assertSee('Rp 2.000.000')
            ->assertSee('Rp 500.000')
            ->assertDontSee('Rp 1.000.000'); // berjalan is excluded
    }

    public function test_reset_filter_pada_kasbon_guru(): void
    {
        Livewire::actingAs($this->userFinance)
            ->test(ManajemenPeminjaman::class)
            ->set('search', 'Ahmad')
            ->set('filterStatus', 'berjalan')
            ->set('filterPeriode', 'hari_ini')
            ->assertSet('filterPeriode', 'hari_ini')
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSet('filterStatus', '')
            ->assertSet('filterPeriode', 'semua')
            ->assertSet('startDate', null)
            ->assertSet('endDate', null);
    }
}
