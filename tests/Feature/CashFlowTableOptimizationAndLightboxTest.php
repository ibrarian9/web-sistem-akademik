<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;
use App\Models\PemasukanKas;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Livewire\Finance\ArusKas;
use Livewire\Livewire;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    $this->artisan('db:seed', ['--class' => 'PengaturanSeeder']);

    $this->roleFinance = Role::where('nama', 'finance')->first();

    $this->userFinance = User::create([
        'nama' => 'Staff Keuangan Cash Flow',
        'username' => 'finance_cf_' . uniqid(),
        'email' => 'finance_cf_' . uniqid() . '@test.com',
        'password' => bcrypt('password'),
        'role_id' => $this->roleFinance->id,
    ]);

    $this->kategori = KategoriPengeluaran::firstOrCreate(
        ['nama' => 'Operasional ATK & Kantor'],
        ['deskripsi' => 'Pengadaan ATK']
    );

    // Create Inflow record
    $this->pemasukan = PemasukanKas::create([
        'kategori' => 'Infaq Donatur',
        'jumlah' => 150000,
        'tanggal' => date('Y-m-d'),
        'keterangan' => 'Infaq Pembangunan Masjid',
        'petugas_id' => $this->userFinance->id,
    ]);

    // Create Outflow record with proof image
    $this->pengeluaranWithBukti = Pengeluaran::create([
        'kategori_pengeluaran_id' => $this->kategori->id,
        'jumlah' => 75000,
        'tanggal' => date('Y-m-d'),
        'keterangan' => 'Beli Spidol dan Kertas HVS',
        'bukti' => 'bukti-pengeluaran/nota_atk_dummy.jpg',
        'petugas_id' => $this->userFinance->id,
    ]);

    // Create Outflow record without proof image
    $this->pengeluaranNoBukti = Pengeluaran::create([
        'kategori_pengeluaran_id' => $this->kategori->id,
        'jumlah' => 25000,
        'tanggal' => date('Y-m-d'),
        'keterangan' => 'Konsumsi Ringan Rapat',
        'bukti' => null,
        'petugas_id' => $this->userFinance->id,
    ]);
});

test('cash flow table displays both Kas Masuk and Kas Keluar headers when tab is semua', function () {
    $this->actingAs($this->userFinance);

    Livewire::test(ArusKas::class)
        ->assertStatus(200)
        ->assertSet('tab', 'semua')
        ->assertSee('Kas Masuk (Rp)')
        ->assertSee('Kas Keluar (Rp)')
        ->assertSee('Infaq Pembangunan Masjid')
        ->assertSee('Beli Spidol dan Kertas HVS');
});

test('cash flow table hides Kas Keluar column when tab is masuk', function () {
    $this->actingAs($this->userFinance);

    Livewire::test(ArusKas::class)
        ->assertStatus(200)
        ->call('selectTab', 'masuk')
        ->assertSet('tab', 'masuk')
        ->assertSee('Nominal Masuk (Rp)')
        ->assertDontSee('Nominal Keluar (Rp)')
        ->assertDontSee('Kas Keluar (Rp)')
        ->assertSee('Infaq Pembangunan Masjid')
        ->assertDontSee('Beli Spidol dan Kertas HVS');
});

test('cash flow table hides Kas Masuk column when tab is keluar', function () {
    $this->actingAs($this->userFinance);

    Livewire::test(ArusKas::class)
        ->assertStatus(200)
        ->call('selectTab', 'keluar')
        ->assertSet('tab', 'keluar')
        ->assertSee('Nominal Keluar (Rp)')
        ->assertDontSee('Nominal Masuk (Rp)')
        ->assertDontSee('Kas Masuk (Rp)')
        ->assertSee('Beli Spidol dan Kertas HVS')
        ->assertDontSee('Infaq Pembangunan Masjid');
});

test('cash flow table renders thumbnail and Alpine.js lightbox triggers for transactions with proof', function () {
    $this->actingAs($this->userFinance);

    Livewire::test(ArusKas::class)
        ->assertStatus(200)
        // Verify Alpine state wrapper
        ->assertSee('previewOpen: false', false)
        // Verify thumbnail click attributes
        ->assertSee('nota_atk_dummy.jpg')
        ->assertSee('previewOpen = true', false)
        ->assertSee('previewSrc =', false)
        // Verify Alpine Lightbox modal markup
        ->assertSee('x-show="previewOpen"', false)
        ->assertSee('Tekan <kbd', false);
});
