<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Tagihan;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use App\Models\KategoriPengeluaran;
use App\Models\PemasukanKas;
use App\Models\Pengeluaran;
use App\Models\Tabungan;
use App\Models\DanaBos;
use App\Models\PengajuanDana;
use App\Casts\MoneyCast;
use App\Traits\WithCurrencySanitizer;
use Livewire\Livewire;
use App\Livewire\Finance\ArusKasMasuk;
use App\Livewire\Finance\ArusKasKeluar;
use App\Livewire\Finance\TabunganSiswa;
use App\Livewire\Finance\DetailTagihanSiswa;
use App\Livewire\Finance\DanaBos as DanaBosLivewire;
use App\Livewire\Finance\PengajuanDanaIndex;
use Illuminate\Database\Eloquent\Model;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    $this->artisan('db:seed', ['--class' => 'PengaturanSeeder']);
    $this->artisan('db:seed', ['--class' => 'DemoDataSeeder']);
    $this->artisan('db:seed', ['--class' => 'JenisTagihanSeeder']);

    $this->userFinance = User::whereHas('role', function ($q) {
        $q->where('nama', 'finance');
    })->first();

    if (!$this->userFinance) {
        $roleFinance = Role::where('nama', 'finance')->first();
        $this->userFinance = User::create([
            'nama' => 'Staff Keuangan',
            'username' => 'finance_currency_test',
            'email' => 'finance_currency@test.com',
            'password' => bcrypt('password'),
            'role_id' => $roleFinance->id,
        ]);
    }
});

/*
|--------------------------------------------------------------------------
| A. CurrencyHelper Tests (format_rupiah & unmask_rupiah)
|--------------------------------------------------------------------------
*/

test('format_rupiah formats integers and floats correctly', function () {
    expect(format_rupiah(1000000))->toBe('1.000.000');
    expect(format_rupiah(1000000, true))->toBe('Rp 1.000.000');
    expect(format_rupiah(50000))->toBe('50.000');
    expect(format_rupiah(0))->toBe('0');
    expect(format_rupiah(0, true))->toBe('Rp 0');
    expect(format_rupiah(null))->toBe('0');
    expect(format_rupiah(null, true))->toBe('Rp 0');
    expect(format_rupiah(''))->toBe('0');
    expect(format_rupiah('', true))->toBe('Rp 0');
});

test('format_rupiah handles negative values correctly', function () {
    expect(format_rupiah(-50000))->toBe('-50.000');
    expect(format_rupiah(-50000, true))->toBe('-Rp 50.000');
});

test('format_rupiah handles formatted strings and database decimals', function () {
    expect(format_rupiah('1.500.000'))->toBe('1.500.000');
    expect(format_rupiah('1.500.000', true))->toBe('Rp 1.500.000');
    expect(format_rupiah('150000.00'))->toBe('150.000');
    expect(format_rupiah('150000.00', true))->toBe('Rp 150.000');
});

test('unmask_rupiah parses formatted currency strings into numeric floats', function () {
    expect(unmask_rupiah('1.000.000'))->toBe(1000000.0);
    expect(unmask_rupiah('Rp 1.000.000'))->toBe(1000000.0);
    expect(unmask_rupiah('Rp. 1.000.000'))->toBe(1000000.0);
    expect(unmask_rupiah('1.500.000,50'))->toBe(1500000.5);
    expect(unmask_rupiah('150000.00'))->toBe(150000.0);
    expect(unmask_rupiah('-50.000'))->toBe(-50000.0);
    expect(unmask_rupiah('-Rp 50.000'))->toBe(-50000.0);
    expect(unmask_rupiah(75000))->toBe(75000.0);
    expect(unmask_rupiah(75000.25))->toBe(75000.25);
    expect(unmask_rupiah(null))->toBe(0.0);
    expect(unmask_rupiah(''))->toBe(0.0);
    expect(unmask_rupiah('0'))->toBe(0.0);
});

/*
|--------------------------------------------------------------------------
| B. WithCurrencySanitizer Trait Tests
|--------------------------------------------------------------------------
*/

test('WithCurrencySanitizer trait sanitizes single and multiple properties', function () {
    $component = new class {
        use WithCurrencySanitizer;

        public $gaji_pokok = 'Rp 3.500.000';
        public $tunjangan = '500.000';
        public $bonus = '0';
        public $potongan = null;

        public function sanitize()
        {
            $this->sanitizeCurrency('gaji_pokok');
            $this->sanitizeCurrencies(['tunjangan', 'bonus', 'potongan']);
        }
    };

    $component->sanitize();

    expect($component->gaji_pokok)->toBe(3500000.0);
    expect($component->tunjangan)->toBe(500000.0);
    expect($component->bonus)->toBe(0.0);
    expect($component->potongan)->toBe(0.0);
});

/*
|--------------------------------------------------------------------------
| C. MoneyCast Tests
|--------------------------------------------------------------------------
*/

test('MoneyCast handles getting and setting currency attributes safely', function () {
    $cast = new MoneyCast();
    $dummyModel = new class extends Model {};

    // Setting values (unmasking)
    expect($cast->set($dummyModel, 'nominal', 'Rp 2.500.000', []))->toBe(2500000.0);
    expect($cast->set($dummyModel, 'nominal', '1.000.000', []))->toBe(1000000.0);
    expect($cast->set($dummyModel, 'nominal', 500000, []))->toBe(500000.0);
    expect($cast->set($dummyModel, 'nominal', null, []))->toBeNull();
    expect($cast->set($dummyModel, 'nominal', '', []))->toBeNull();

    // Getting values (pure float)
    expect($cast->get($dummyModel, 'nominal', '2500000.00', []))->toBe(2500000.0);
    expect($cast->get($dummyModel, 'nominal', 1000000, []))->toBe(1000000.0);
    expect($cast->get($dummyModel, 'nominal', null, []))->toBeNull();
});

/*
|--------------------------------------------------------------------------
| D. Blade Component Rendering Tests
|--------------------------------------------------------------------------
*/

test('input-currency component renders with proper alpine modelable directive', function () {
    $view = $this->blade(
        '<x-input-currency name="nominal" wire:model="nominal" label="Nominal Tagihan" placeholder="Contoh: 1.000.000" />'
    );

    $view->assertSee('x-modelable="rawValue"', false);
    $view->assertSee('Nominal Tagihan');
    $view->assertSee('Rp');
    $view->assertSee('formatNumber');
});

test('input-currency component hides prefix when prefix is false', function () {
    $view = $this->blade(
        '<x-input-currency name="nominal" wire:model="nominal" :prefix="false" />'
    );

    $view->assertSee('x-modelable="rawValue"', false);
    $view->assertDontSee('text-stone-400 font-extrabold select-none text-xs">Rp</span>', false);
});

/*
|--------------------------------------------------------------------------
| E. Livewire Integration Tests (ArusKas, DetailTagihan, DanaBos, Tabungan)
|--------------------------------------------------------------------------
*/

test('ArusKasMasuk saves clean integer to database when formatted currency is submitted', function () {
    $this->actingAs($this->userFinance);

    Livewire::test(ArusKasMasuk::class)
        ->set('kategori', 'Infaq / Sedekah')
        ->set('jumlah', '1.750.000') // Formatted string from input
        ->set('tanggal', date('Y-m-d'))
        ->set('keterangan', 'Pemasukan infaq via input-currency')
        ->call('saveIncome')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('pemasukan_kas', [
        'kategori' => 'Infaq / Sedekah',
        'jumlah' => 1750000.00,
        'keterangan' => 'Pemasukan infaq via input-currency',
    ]);
});

test('ArusKasKeluar saves clean integer to database when formatted currency is submitted', function () {
    $this->actingAs($this->userFinance);

    $kategori = KategoriPengeluaran::firstOrCreate(['nama' => 'Operasional Kantor']);

    Livewire::test(ArusKasKeluar::class)
        ->set('kategori_pengeluaran_id', $kategori->id)
        ->set('jumlah', '850.000') // Formatted string from input
        ->set('tanggal', date('Y-m-d'))
        ->set('keterangan', 'Beli ATK kantor via input-currency')
        ->call('saveExpense')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('pengeluaran', [
        'kategori_pengeluaran_id' => $kategori->id,
        'jumlah' => 850000.00,
        'keterangan' => 'Beli ATK kantor via input-currency',
    ]);
});

test('TabunganSiswa saves clean integer to database when formatted currency is submitted', function () {
    $this->actingAs($this->userFinance);

    $siswa = Siswa::first();

    Livewire::test(TabunganSiswa::class)
        ->set('siswa_id', $siswa->id)
        ->set('jenis', 'setor')
        ->set('nominal', '150.000') // Formatted string from input
        ->set('tanggal', date('Y-m-d'))
        ->set('keterangan', 'Setoran tabungan via input-currency')
        ->call('saveTransaction')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('tabungans', [
        'siswa_id' => $siswa->id,
        'jenis' => 'setor',
        'nominal' => 150000.00,
        'keterangan' => 'Setoran tabungan via input-currency',
    ]);
});

test('DanaBos saves clean integer to database when formatted currency is submitted', function () {
    $this->actingAs($this->userFinance);

    $ta = TahunAjaran::where('status_aktif', true)->first() ?? TahunAjaran::first();

    Livewire::test(DanaBosLivewire::class)
        ->set('jenis', 'masuk')
        ->set('nominal', '5.000.000') // Formatted string from input
        ->set('tanggal', date('Y-m-d'))
        ->set('kategori', 'Penyaluran Dana BOS Reguler')
        ->set('keterangan', 'Penerimaan dana BOS via input-currency')
        ->call('saveTransaction')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('dana_bos', [
        'jenis' => 'masuk',
        'nominal' => 5000000.00,
        'keterangan' => 'Penerimaan dana BOS via input-currency',
    ]);
});

test('DetailTagihanSiswa saves clean integer to database when formatted currency is submitted', function () {
    $this->actingAs($this->userFinance);

    $siswa = Siswa::first();
    $jt = JenisTagihan::first();

    Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $siswa->id])
        ->set('jenis_tagihan_id', $jt->id)
        ->set('bulan', 'September')
        ->set('nominal', '450.000') // Formatted string from input
        ->set('jatuh_tempo', date('Y-m-d', strtotime('+30 days')))
        ->call('createTagihan')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('tagihan', [
        'siswa_id' => $siswa->id,
        'bulan' => 'September',
        'nominal' => 450000.00,
    ]);
});

test('PengajuanDanaIndex saves clean integer to database when formatted currency is submitted', function () {
    $this->actingAs($this->userFinance);

    Livewire::test(PengajuanDanaIndex::class)
        ->set('judul', 'Pengadaan Media Pembelajaran')
        ->set('kategori', 'Pengembangan Kurikulum')
        ->set('jumlah', '2.500.000') // Formatted string from input
        ->set('keterangan', 'Pengadaan media pembelajaran via input-currency')
        ->call('createPengajuan')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('pengajuan_dana', [
        'judul' => 'Pengadaan Media Pembelajaran',
        'nominal' => 2500000.00,
    ]);
});
