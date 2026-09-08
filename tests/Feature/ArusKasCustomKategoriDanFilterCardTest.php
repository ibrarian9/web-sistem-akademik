<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\PemasukanKas;
use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;
use Livewire\Livewire;
use App\Livewire\Finance\ArusKas;
use App\Livewire\Finance\ArusKasMasuk;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArusKasCustomKategoriDanFilterCardTest extends TestCase
{
    use RefreshDatabase;

    protected User $financeUser;
    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $roleFinance = Role::firstOrCreate(['nama' => 'finance'], ['deskripsi' => 'Staff Keuangan']);
        $roleAdmin = Role::firstOrCreate(['nama' => 'super_admin'], ['deskripsi' => 'Super Administrator']);

        $this->financeUser = User::factory()->create([
            'username' => 'finance_test_' . uniqid(),
            'nama' => 'Staff Finance Test',
            'role_id' => $roleFinance->id,
            'status' => 'aktif',
        ]);

        $this->superAdmin = User::factory()->create([
            'username' => 'admin_test_' . uniqid(),
            'nama' => 'Super Admin Test',
            'role_id' => $roleAdmin->id,
            'status' => 'aktif',
        ]);

        KategoriPengeluaran::firstOrCreate(
            ['nama' => 'Operasional Kantor'],
            ['jenis' => 'operasional']
        );
    }

    public function test_catat_kas_masuk_dapat_menambahkan_kategori_kustom_baru_di_arus_kas(): void
    {
        Livewire::actingAs($this->financeUser)
            ->test(ArusKas::class)
            ->call('openIncomeModal')
            ->assertSet('showIncomeModal', true)
            ->assertSet('is_kategori_masuk_kustom', false)
            ->set('is_kategori_masuk_kustom', true)
            ->set('kategori_masuk_kustom', 'Wakaf Produktif Masjid')
            ->set('jumlah_masuk', 7500000)
            ->set('tanggal_masuk', '2026-09-08')
            ->set('keterangan_masuk', 'Penerimaan dana wakaf tunai dari hamba Allah')
            ->call('saveIncome')
            ->assertHasNoErrors()
            ->assertSet('showIncomeModal', false);

        $this->assertDatabaseHas('pemasukan_kas', [
            'kategori' => 'Wakaf Produktif Masjid',
            'jumlah' => 7500000,
            'keterangan' => 'Penerimaan dana wakaf tunai dari hamba Allah',
            'petugas_id' => $this->financeUser->id,
        ]);

        // Verifikasi bahwa kategori baru kini tersimpan dan tersedia pada opsi kategori
        Livewire::actingAs($this->financeUser)
            ->test(ArusKas::class)
            ->call('openIncomeModal')
            ->assertViewHas('kategoriMasukOptions', function ($options) {
                return in_array('Wakaf Produktif Masjid', $options);
            });
    }

    public function test_catat_kas_masuk_dapat_menambahkan_kategori_kustom_di_arus_kas_masuk(): void
    {
        Livewire::actingAs($this->financeUser)
            ->test(ArusKasMasuk::class)
            ->call('openCreateModal')
            ->assertSet('showCreateModal', true)
            ->set('is_kategori_kustom', true)
            ->set('kategori_kustom', 'Sumbangan Alumni Akbar')
            ->set('jumlah', 10000000)
            ->set('tanggal', '2026-09-08')
            ->set('keterangan', 'Donasi reuni akbar alumni 2026')
            ->call('saveIncome')
            ->assertHasNoErrors()
            ->assertSet('showCreateModal', false);

        $this->assertDatabaseHas('pemasukan_kas', [
            'kategori' => 'Sumbangan Alumni Akbar',
            'jumlah' => 10000000,
            'keterangan' => 'Donasi reuni akbar alumni 2026',
        ]);
    }

    public function test_card_total_kas_masuk_dan_total_kas_keluar_dapat_diklik_dan_memfilter_tabel(): void
    {
        Livewire::actingAs($this->financeUser)
            ->test(ArusKas::class)
            ->assertSet('tab', 'semua')
            // Klik kartu Total Kas Masuk -> filter tab 'masuk'
            ->call('filterByCard', 'masuk')
            ->assertSet('tab', 'masuk')
            ->assertSet('stream', 'semua')
            // Klik lagi kartu Total Kas Masuk -> toggle kembali ke 'semua'
            ->call('filterByCard', 'masuk')
            ->assertSet('tab', 'semua')
            // Klik kartu Total Kas Keluar -> filter tab 'keluar'
            ->call('filterByCard', 'keluar')
            ->assertSet('tab', 'keluar')
            ->assertSet('stream', 'semua')
            // Klik kartu Total Kas Keluar lagi -> toggle kembali ke 'semua'
            ->call('filterByCard', 'keluar')
            ->assertSet('tab', 'semua')
            // Klik kartu Surplus / Saldo Bersih -> reset ke 'semua'
            ->call('selectTab', 'masuk')
            ->assertSet('tab', 'masuk')
            ->call('selectTab', 'semua')
            ->assertSet('tab', 'semua');
    }

    public function test_card_stream_pada_arus_kas_masuk_dapat_diklik_dan_memfilter_stream(): void
    {
        Livewire::actingAs($this->financeUser)
            ->test(ArusKasMasuk::class)
            ->assertSet('stream', 'semua')
            ->call('selectStream', 'pembayaran_spp')
            ->assertSet('stream', 'pembayaran_spp')
            ->call('selectStream', 'kas_yayasan')
            ->assertSet('stream', 'kas_yayasan')
            ->call('selectStream', 'tabungan')
            ->assertSet('stream', 'tabungan')
            ->call('selectStream', 'semua')
            ->assertSet('stream', 'semua');
    }
}
