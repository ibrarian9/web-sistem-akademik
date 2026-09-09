<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\PemasukanKas;
use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Peminjaman;
use Livewire\Livewire;
use App\Livewire\Finance\ArusKas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class ArusKasFilterInvestigationTest extends TestCase
{
    use RefreshDatabase;

    protected User $userFinance;
    protected KategoriPengeluaran $kategoriATK;
    protected KategoriPengeluaran $kategoriListrik;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        $this->artisan('db:seed', ['--class' => 'PengaturanSeeder']);

        $roleFinance = Role::where('nama', 'finance')->first();
        $this->userFinance = User::create([
            'nama' => 'Staff Keuangan Test',
            'username' => 'finance_filter_test',
            'email' => 'finance_filter_test@test.com',
            'password' => bcrypt('password'),
            'role_id' => $roleFinance->id,
            'status' => 'aktif',
        ]);

        $this->kategoriATK = KategoriPengeluaran::firstOrCreate(['nama' => 'Operasional ATK']);
        $this->kategoriListrik = KategoriPengeluaran::firstOrCreate(['nama' => 'Listrik & Air']);

        // 1. Infaq Hari Ini
        PemasukanKas::create([
            'kategori' => 'Infaq',
            'jumlah' => 100000,
            'tanggal' => Carbon::today()->format('Y-m-d'),
            'keterangan' => 'Infaq Hari Ini',
            'petugas_id' => $this->userFinance->id,
        ]);

        // 2. Donasi Kemarin
        PemasukanKas::create([
            'kategori' => 'Donasi',
            'jumlah' => 200000,
            'tanggal' => Carbon::yesterday()->format('Y-m-d'),
            'keterangan' => 'Donasi Kemarin',
            'petugas_id' => $this->userFinance->id,
        ]);

        // 3. Pengeluaran ATK Hari Ini
        Pengeluaran::create([
            'kategori_pengeluaran_id' => $this->kategoriATK->id,
            'jumlah' => 50000,
            'tanggal' => Carbon::today()->format('Y-m-d'),
            'keterangan' => 'Beli ATK Hari Ini',
            'petugas_id' => $this->userFinance->id,
        ]);

        // 4. Pengeluaran Listrik Kemarin
        Pengeluaran::create([
            'kategori_pengeluaran_id' => $this->kategoriListrik->id,
            'jumlah' => 75000,
            'tanggal' => Carbon::yesterday()->format('Y-m-d'),
            'keterangan' => 'Bayar Listrik Kemarin',
            'petugas_id' => $this->userFinance->id,
        ]);
    }

    public function test_filter_periode_hari_ini_only_shows_today_transactions(): void
    {
        Livewire::actingAs($this->userFinance)
            ->test(ArusKas::class)
            ->set('filterPeriode', 'hari_ini')
            ->assertSee('Infaq Hari Ini')
            ->assertSee('Beli ATK Hari Ini')
            ->assertDontSee('Donasi Kemarin')
            ->assertDontSee('Bayar Listrik Kemarin');
    }

    public function test_filter_periode_kemarin_only_shows_yesterday_transactions(): void
    {
        Livewire::actingAs($this->userFinance)
            ->test(ArusKas::class)
            ->set('filterPeriode', 'kemarin')
            ->assertSee('Donasi Kemarin')
            ->assertSee('Bayar Listrik Kemarin')
            ->assertDontSee('Infaq Hari Ini')
            ->assertDontSee('Beli ATK Hari Ini');
    }

    public function test_filter_kategori_masuk_should_not_show_unrelated_spp_or_other_streams(): void
    {
        // Create SPP payment
        $ta = TahunAjaran::create(['nama' => '2026/2027', 'is_active' => true]);
        $semester = \App\Models\Semester::create([
            'tahun_ajaran_id' => $ta->id,
            'semester' => '1',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2026-12-31',
            'status_aktif' => true,
        ]);
        $kelas = Kelas::create(['nama_kelas' => '7A', 'tingkat' => '7', 'semester_id' => $semester->id]);
        $siswaRole = Role::firstOrCreate(['nama' => 'siswa'], ['deskripsi' => 'Siswa']);
        $siswaUser = User::create([
            'nama' => 'Ahmad Santoso',
            'username' => 'ahmad_santoso_' . uniqid(),
            'email' => 'ahmad_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role_id' => $siswaRole->id,
            'status' => 'aktif',
        ]);
        $siswa = Siswa::create([
            'user_id' => $siswaUser->id,
            'nis' => '12345',
            'nisn' => '1234567890',
            'kelas_id' => $kelas->id,
            'tanggal_masuk' => '2026-07-01',
            'status' => 'aktif',
        ]);
        $jenisTagihan = JenisTagihan::create([
            'nama' => 'SPP Bulanan',
            'kategori' => 'rutin',
            'default_nominal' => 250000,
        ]);
        $tagihan = Tagihan::create([
            'siswa_id' => $siswa->id,
            'jenis_tagihan_id' => $jenisTagihan->id,
            'tahun_ajaran_id' => $ta->id,
            'bulan' => 'September',
            'nominal' => 250000,
            'jatuh_tempo' => date('Y-m-d'),
        ]);
        Pembayaran::create([
            'no_resi' => 'RESI-' . uniqid(),
            'tagihan_id' => $tagihan->id,
            'tanggal_bayar' => Carbon::today()->format('Y-m-d'),
            'nominal_dibayar' => 250000,
            'metode_bayar' => 'Transfer',
            'petugas_id' => $this->userFinance->id,
        ]);

        // When filtering by filterKategoriMasuk = 'Infaq', SPP Bulanan (Ahmad Santoso) should NOT be shown in the table!
        Livewire::actingAs($this->userFinance)
            ->test(ArusKas::class)
            ->set('tab', 'masuk')
            ->set('filterKategoriMasuk', 'Infaq')
            ->assertSee('Infaq Hari Ini')
            ->assertDontSee('Ahmad Santoso');
    }

    public function test_filter_kategori_keluar_should_not_show_gaji_or_kasbon_when_specific_expense_category_selected(): void
    {
        $guruRole = Role::where('nama', 'guru')->first() ?? Role::create(['nama' => 'guru', 'deskripsi' => 'Guru']);
        $guruUser = User::create([
            'nama' => 'Ustadz Abdullah',
            'username' => 'guru_abdullah_' . uniqid(),
            'email' => 'abdullah_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role_id' => $guruRole->id,
            'status' => 'aktif',
        ]);
        $guru = Guru::create([
            'user_id' => $guruUser->id,
            'nip' => '99887766',
            'tanggal_masuk' => '2020-01-01',
            'status_aktif' => true,
        ]);

        Peminjaman::create([
            'guru_id' => $guru->id,
            'tanggal_pinjam' => Carbon::today()->format('Y-m-d'),
            'nominal' => 1000000,
            'tenor_bulan' => 5,
            'cicilan_per_bulan' => 200000,
            'sisa_pinjaman' => 1000000,
            'status' => 'berjalan',
        ]);

        // When filtering by filterKategoriKeluar = Operasional ATK, Kasbon Guru should NOT be shown!
        Livewire::actingAs($this->userFinance)
            ->test(ArusKas::class)
            ->set('tab', 'keluar')
            ->set('filterKategoriKeluar', $this->kategoriATK->id)
            ->assertSee('Beli ATK Hari Ini')
            ->assertDontSee('Pencairan kasbon: Ustadz Abdullah');
    }

    public function test_filter_metode_pembayaran(): void
    {
        Livewire::actingAs($this->userFinance)
            ->test(ArusKas::class)
            ->set('filterMetode', 'tunai')
            ->assertSee('Infaq Hari Ini')
            ->assertSee('Beli ATK Hari Ini')
            ->set('filterMetode', 'transfer')
            ->assertSee('Infaq Hari Ini');
    }

    public function test_filter_nominal_min_and_max(): void
    {
        Livewire::actingAs($this->userFinance)
            ->test(ArusKas::class)
            ->set('nominalMin', 150000)
            ->assertSee('Donasi Kemarin')
            ->assertDontSee('Infaq Hari Ini')
            ->assertDontSee('Beli ATK Hari Ini')
            ->assertDontSee('Bayar Listrik Kemarin')
            ->set('nominalMin', null)
            ->set('nominalMax', 60000)
            ->assertSee('Beli ATK Hari Ini')
            ->assertDontSee('Donasi Kemarin');
    }

    public function test_filter_periode_bulan_lalu_and_tahun_ini(): void
    {
        // Add transaction for last month
        PemasukanKas::create([
            'kategori' => 'Infaq',
            'jumlah' => 300000,
            'tanggal' => Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d'),
            'keterangan' => 'Infaq Bulan Lalu Spesifik',
            'petugas_id' => $this->userFinance->id,
        ]);

        Livewire::actingAs($this->userFinance)
            ->test(ArusKas::class)
            ->set('filterPeriode', 'bulan_lalu')
            ->assertSee('Infaq Bulan Lalu Spesifik')
            ->assertDontSee('Infaq Hari Ini')
            ->set('filterPeriode', 'tahun_ini')
            ->assertSee('Infaq Bulan Lalu Spesifik')
            ->assertSee('Infaq Hari Ini');
    }

    public function test_pagination_resets_to_page_1_when_filter_changes(): void
    {
        // Create 20 items so we have 2 pages
        for ($i = 1; $i <= 20; $i++) {
            PemasukanKas::create([
                'kategori' => 'Infaq',
                'jumlah' => 10000 * $i,
                'tanggal' => Carbon::today()->format('Y-m-d'),
                'keterangan' => "Infaq Massal #{$i}",
                'petugas_id' => $this->userFinance->id,
            ]);
        }

        $test = Livewire::actingAs($this->userFinance)
            ->test(ArusKas::class)
            ->call('gotoPage', 2);

        // Change filter to 'kemarin' (which only has 1 item)
        $test->set('filterPeriode', 'kemarin')
            ->assertSee('Donasi Kemarin');
    }
}
