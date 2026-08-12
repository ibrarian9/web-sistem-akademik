<?php

use App\Models\Role;
use App\Models\Siswa;
use App\Models\Tabungan;
use App\Models\User;
use App\Livewire\Finance\TabunganSiswa;
use App\Livewire\Murid\TabunganSaya;
use Livewire\Livewire;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    $this->artisan('db:seed', ['--class' => 'KomponenNilaiSeeder']);
    $this->artisan('db:seed', ['--class' => 'PengaturanSeeder']);
    $this->artisan('db:seed', ['--class' => 'DemoDataSeeder']);

    // Fetch finance user (Siti Aminah / finance)
    $this->userFinance = User::where('username', 'finance')->first();

    // Fetch murid user (siswa1 / Ahmad Fauzi)
    $this->siswa = Siswa::first();
    $this->userMurid = $this->siswa->user;
});

test('finance can deposit to student savings and balance increases', function () {
    $this->actingAs($this->userFinance);

    Livewire::test(TabunganSiswa::class)
        ->call('openTransactionModal', $this->siswa->id, 'setor')
        ->set('nominal', 100000)
        ->set('tanggal', date('Y-m-d'))
        ->set('keterangan', 'Setoran Awal Tabungan')
        ->call('saveTransaction')
        ->assertHasNoErrors();

    $latest = Tabungan::where('siswa_id', $this->siswa->id)->latest()->first();
    expect($latest)->not->toBeNull();
    expect($latest->jenis)->toEqual('setor');
    expect((float) $latest->nominal)->toEqual(100000.0);
    expect((float) $latest->saldo_akhir)->toEqual(100000.0);
});

test('finance can withdraw from student savings when balance is sufficient', function () {
    $this->actingAs($this->userFinance);

    // Initial deposit
    Tabungan::create([
        'siswa_id' => $this->siswa->id,
        'petugas_id' => $this->userFinance->id,
        'kode_transaksi' => 'TAB-INIT-01',
        'jenis' => 'setor',
        'nominal' => 200000,
        'saldo_akhir' => 200000,
        'tanggal' => date('Y-m-d'),
        'keterangan' => 'Deposit Awal',
    ]);

    Livewire::test(TabunganSiswa::class)
        ->call('openTransactionModal', $this->siswa->id, 'tarik')
        ->set('nominal', 50000)
        ->set('tanggal', date('Y-m-d'))
        ->set('keterangan', 'Penarikan Uang Saku')
        ->call('saveTransaction')
        ->assertHasNoErrors();

    $latest = Tabungan::where('siswa_id', $this->siswa->id)->orderBy('id', 'desc')->first();
    expect($latest->jenis)->toEqual('tarik');
    expect((float) $latest->nominal)->toEqual(50000.0);
    expect((float) $latest->saldo_akhir)->toEqual(150000.0);
});

test('withdrawal fails if amount exceeds student savings balance', function () {
    $this->actingAs($this->userFinance);

    // Initial deposit of 30,000
    Tabungan::create([
        'siswa_id' => $this->siswa->id,
        'petugas_id' => $this->userFinance->id,
        'kode_transaksi' => 'TAB-INIT-02',
        'jenis' => 'setor',
        'nominal' => 30000,
        'saldo_akhir' => 30000,
        'tanggal' => date('Y-m-d'),
        'keterangan' => 'Deposit Awal',
    ]);

    Livewire::test(TabunganSiswa::class)
        ->call('openTransactionModal', $this->siswa->id, 'tarik')
        ->set('nominal', 50000)
        ->set('tanggal', date('Y-m-d'))
        ->set('keterangan', 'Penarikan Melebihi Saldo')
        ->call('saveTransaction')
        ->assertHasErrors(['nominal']);

    // Ensure no withdrawal transaction created
    $countTarik = Tabungan::where('siswa_id', $this->siswa->id)->where('jenis', 'tarik')->count();
    expect($countTarik)->toEqual(0);
});

test('student can view their savings balance and transaction history', function () {
    Tabungan::create([
        'siswa_id' => $this->siswa->id,
        'petugas_id' => $this->userFinance->id,
        'kode_transaksi' => 'TAB-MUT-01',
        'jenis' => 'setor',
        'nominal' => 75000,
        'saldo_akhir' => 75000,
        'tanggal' => date('Y-m-d'),
        'keterangan' => 'Setor Tabungan Mingguan',
    ]);

    $this->actingAs($this->userMurid);

    Livewire::test(TabunganSaya::class)
        ->assertStatus(200)
        ->assertSee('Tabungan Saya')
        ->assertSee('Rp 75.000')
        ->assertSee('Setor Tabungan Mingguan');
});
