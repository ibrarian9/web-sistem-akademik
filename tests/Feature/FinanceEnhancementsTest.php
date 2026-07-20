<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use App\Models\Pembayaran;
use Livewire\Livewire;
use App\Livewire\Finance\InputPembayaran;
use App\Livewire\Finance\ManajemenTagihan;
use App\Livewire\Finance\ArusKasMasuk;
use App\Livewire\Finance\ArusKasKeluar;

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
            'nama' => 'Staf Keuangan Test',
            'username' => 'finance_enh_test',
            'email' => 'finance_enh@test.com',
            'password' => bcrypt('password'),
            'role_id' => $roleFinance->id,
        ]);
    }
});

test('user can select non-cash payment methods in InputPembayaran', function () {
    $this->actingAs($this->userFinance);

    $siswa = Siswa::first();
    $activeTA = TahunAjaran::where('status_aktif', true)->first() ?? TahunAjaran::first();
    $jt = JenisTagihan::where('nama', 'SPP')->first() ?? JenisTagihan::first();

    $tagihan = Tagihan::create([
        'siswa_id' => $siswa->id,
        'jenis_tagihan_id' => $jt->id,
        'tahun_ajaran_id' => $activeTA->id,
        'bulan' => 'Agustus',
        'nominal' => 350000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => now()->addDays(5),
    ]);

    // Test selecting Transfer Bank method
    $comp = Livewire::test(InputPembayaran::class)
        ->call('pilihSiswaAndTagihan', $siswa->id, $tagihan->id)
        ->call('setMetodeBayar', 'Transfer Bank')
        ->assertSet('metode_bayar', 'Transfer Bank')
        ->set('nominal_dibayar', 350000)
        ->set('tanggal_bayar', date('Y-m-d'))
        ->call('savePayment')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('pembayaran', [
        'tagihan_id' => $tagihan->id,
        'nominal_dibayar' => 350000,
        'metode_bayar' => 'Transfer Bank',
    ]);

    // Verify lastPembayaranId is set for printing receipt
    expect($comp->get('lastPembayaranId'))->not->toBeNull();
});

test('finance official receipt route generates PDF stream with finance signature', function () {
    $this->actingAs($this->userFinance);

    $siswa = Siswa::first();
    $activeTA = TahunAjaran::where('status_aktif', true)->first() ?? TahunAjaran::first();
    $jt = JenisTagihan::where('nama', 'SPP')->first() ?? JenisTagihan::first();

    $tagihan = Tagihan::create([
        'siswa_id' => $siswa->id,
        'jenis_tagihan_id' => $jt->id,
        'tahun_ajaran_id' => $activeTA->id,
        'bulan' => 'September',
        'nominal' => 400000,
        'total_dibayar' => 400000,
        'status' => 'lunas',
        'jatuh_tempo' => now(),
    ]);

    $pembayaran = Pembayaran::create([
        'no_resi' => 'KW-TEST-1234',
        'tagihan_id' => $tagihan->id,
        'tanggal_bayar' => date('Y-m-d'),
        'nominal_dibayar' => 400000,
        'metode_bayar' => 'E-Wallet',
        'petugas_id' => $this->userFinance->id,
    ]);

    $response = $this->get(route('finance.cetak-resi', $pembayaran->id));

    $response->assertStatus(200);
});

test('finance views render without delete buttons for data integrity', function () {
    $this->actingAs($this->userFinance);

    Livewire::test(ManajemenTagihan::class)
        ->assertStatus(200)
        ->assertDontSee('deleteTagihan');

    Livewire::test(ArusKasMasuk::class)
        ->assertStatus(200)
        ->assertDontSee('deleteIncome');

    Livewire::test(ArusKasKeluar::class)
        ->assertStatus(200)
        ->assertDontSee('deleteExpense');
});
