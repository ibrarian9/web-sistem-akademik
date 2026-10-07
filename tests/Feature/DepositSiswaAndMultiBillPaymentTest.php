<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Tagihan;
use App\Models\JenisTagihan;
use App\Models\Pembayaran;
use App\Models\TahunAjaran;
use Livewire\Livewire;
use App\Livewire\Finance\DepositSiswa;
use App\Livewire\Finance\InputPembayaran;

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
            'username' => 'finance_test_' . rand(100, 999),
            'email' => 'finance_deposit_' . rand(100, 999) . '@test.com',
            'password' => bcrypt('password'),
            'role_id' => $roleFinance->id,
        ]);
    }

    $this->userSuperAdmin = User::whereHas('role', function ($q) {
        $q->where('nama', 'super_admin');
    })->first();

    $this->tahunAjaran = TahunAjaran::where('status_aktif', true)->first() ?? TahunAjaran::first();
    $this->kelas = Kelas::first();
});

test('finance user can access deposit siswa page and view stats', function () {
    $this->actingAs($this->userFinance);

    // Create student with dormant deposit
    $userSiswa = User::create([
        'nama' => 'Ahmad Santoso',
        'username' => 'ahmad_' . rand(100, 999),
        'email' => 'ahmad_' . rand(100, 999) . '@test.com',
        'password' => bcrypt('password'),
        'role_id' => Role::where('nama', 'murid')->first()->id ?? 4,
    ]);

    $siswa = Siswa::create([
        'user_id' => $userSiswa->id,
        'kelas_id' => $this->kelas->id,
        'nisn' => '1234567890',
        'nis' => '12345',
        'saldo_deposit' => 75000,
        'tanggal_masuk' => now()->toDateString(),
    ]);

    $response = $this->get(route('finance.deposit-siswa'));
    $response->assertStatus(200);

    Livewire::test(DepositSiswa::class)
        ->assertStatus(200)
        ->assertSee('Monitoring Saldo Deposit Siswa')
        ->assertSee('Ahmad Santoso')
        ->assertSee('75.000');
});

test('finance user can view detail audit modal of student deposit', function () {
    $this->actingAs($this->userFinance);

    $userSiswa = User::create([
        'nama' => 'Budi Pratama',
        'username' => 'budi_' . rand(100, 999),
        'email' => 'budi_' . rand(100, 999) . '@test.com',
        'password' => bcrypt('password'),
        'role_id' => Role::where('nama', 'murid')->first()->id ?? 4,
    ]);

    $siswa = Siswa::create([
        'user_id' => $userSiswa->id,
        'kelas_id' => $this->kelas->id,
        'nisn' => '2234567890',
        'nis' => '22345',
        'saldo_deposit' => 50000,
        'tanggal_masuk' => now()->toDateString(),
    ]);

    $jt = JenisTagihan::first() ?? JenisTagihan::create(['nama' => 'SPP', 'tipe' => 'bulanan', 'default_nominal' => 200000]);

    $tagihan = Tagihan::create([
        'siswa_id' => $siswa->id,
        'jenis_tagihan_id' => $jt->id,
        'tahun_ajaran_id' => $this->tahunAjaran->id,
        'bulan' => 'Januari',
        'nominal' => 200000,
        'total_dibayar' => 200000,
        'status' => 'lunas',
        'jatuh_tempo' => now()->addDays(5),
    ]);

    Pembayaran::create([
        'no_resi' => 'KW-TEST-DEP-01',
        'tagihan_id' => $tagihan->id,
        'tanggal_bayar' => now()->toDateString(),
        'nominal_dibayar' => 250000,
        'kelebihan_bayar' => 50000,
        'metode_bayar' => 'Tunai',
        'is_void' => false,
        'petugas_id' => $this->userFinance->id,
    ]);

    Livewire::test(DepositSiswa::class)
        ->call('openDetail', $siswa->id)
        ->assertSet('showDetailModal', true)
        ->assertSee('KW-TEST-DEP-01')
        ->assertSee('+ Rp 50.000')
        ->call('closeDetail')
        ->assertSet('showDetailModal', false);
});

test('finance cashier can pay multiple bills at once in InputPembayaran', function () {
    $this->actingAs($this->userFinance);

    $userSiswa = User::create([
        'nama' => 'Citra Lestari',
        'username' => 'citra_' . rand(100, 999),
        'email' => 'citra_' . rand(100, 999) . '@test.com',
        'password' => bcrypt('password'),
        'role_id' => Role::where('nama', 'murid')->first()->id ?? 4,
    ]);

    $siswa = Siswa::create([
        'user_id' => $userSiswa->id,
        'kelas_id' => $this->kelas->id,
        'nisn' => '3234567890',
        'nis' => '32345',
        'saldo_deposit' => 0,
        'tanggal_masuk' => now()->toDateString(),
    ]);

    $jtSpp = JenisTagihan::where('nama', 'like', '%SPP%')->first() ?? JenisTagihan::create(['nama' => 'SPP Bulanan', 'tipe' => 'bulanan', 'default_nominal' => 200000]);
    $jtUjian = JenisTagihan::where('nama', 'like', '%Ujian%')->first() ?? JenisTagihan::create(['nama' => 'Ujian Semester', 'tipe' => 'satu_kali', 'default_nominal' => 150000]);

    $tagihanSpp = Tagihan::create([
        'siswa_id' => $siswa->id,
        'jenis_tagihan_id' => $jtSpp->id,
        'tahun_ajaran_id' => $this->tahunAjaran->id,
        'bulan' => 'Januari',
        'nominal' => 200000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => now()->addDays(5),
    ]);

    $tagihanUjian = Tagihan::create([
        'siswa_id' => $siswa->id,
        'jenis_tagihan_id' => $jtUjian->id,
        'tahun_ajaran_id' => $this->tahunAjaran->id,
        'bulan' => null,
        'nominal' => 150000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    $test = Livewire::test(InputPembayaran::class)
        ->call('pilihSiswaAndTagihan', $siswa->id, $tagihanSpp->id)
        ->assertSet('selected_tagihan_ids', [$tagihanSpp->id]);

    // Select second bill as well
    $test->call('toggleTagihan', $tagihanUjian->id)
        ->assertSet('selected_tagihan_ids', [$tagihanSpp->id, $tagihanUjian->id]);

    // Check recalculated total (200.000 + 150.000 = 350.000)
    expect($test->get('nominal_dibayar'))->toBe(350000.00);

    // Pay full 350.000 for both bills simultaneously
    $test->set('metode_bayar', 'Tunai')
        ->set('tanggal_bayar', now()->toDateString())
        ->call('savePayment')
        ->assertHasNoErrors();

    // Both bills must now be 'lunas'
    $tagihanSpp->refresh();
    $tagihanUjian->refresh();
    expect($tagihanSpp->status)->toBe('lunas');
    expect($tagihanSpp->total_dibayar)->toBe('200000.00');
    expect($tagihanUjian->status)->toBe('lunas');
    expect($tagihanUjian->total_dibayar)->toBe('150000.00');

    // Two distinct payment records must be created
    $payments = Pembayaran::whereIn('tagihan_id', [$tagihanSpp->id, $tagihanUjian->id])->get();
    expect($payments->count())->toBe(2);

    // Each payment has a unique receipt number
    $resiList = $payments->pluck('no_resi')->toArray();
    expect(count(array_unique($resiList)))->toBe(2);
});

test('multi-bill payment with overpayment creates deposit balance', function () {
    $this->actingAs($this->userFinance);

    $userSiswa = User::create([
        'nama' => 'Doni Darmawan',
        'username' => 'doni_' . rand(100, 999),
        'email' => 'doni_' . rand(100, 999) . '@test.com',
        'password' => bcrypt('password'),
        'role_id' => Role::where('nama', 'murid')->first()->id ?? 4,
    ]);

    $siswa = Siswa::create([
        'user_id' => $userSiswa->id,
        'kelas_id' => $this->kelas->id,
        'nisn' => '4234567890',
        'nis' => '42345',
        'saldo_deposit' => 0,
        'tanggal_masuk' => now()->toDateString(),
    ]);

    $jtSpp = JenisTagihan::where('nama', 'like', '%SPP%')->first() ?? JenisTagihan::create(['nama' => 'SPP Bulanan', 'tipe' => 'bulanan', 'default_nominal' => 200000]);
    $jtUjian = JenisTagihan::where('nama', 'like', '%Ujian%')->first() ?? JenisTagihan::create(['nama' => 'Ujian Semester', 'tipe' => 'satu_kali', 'default_nominal' => 100000]);

    $tagihanSpp = Tagihan::create([
        'siswa_id' => $siswa->id,
        'jenis_tagihan_id' => $jtSpp->id,
        'tahun_ajaran_id' => $this->tahunAjaran->id,
        'bulan' => 'Januari',
        'nominal' => 200000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => now()->addDays(5),
    ]);

    $tagihanUjian = Tagihan::create([
        'siswa_id' => $siswa->id,
        'jenis_tagihan_id' => $jtUjian->id,
        'tahun_ajaran_id' => $this->tahunAjaran->id,
        'bulan' => null,
        'nominal' => 100000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    // Total bills = 300.000, but student pays 350.000 (overpayment 50.000)
    Livewire::test(InputPembayaran::class)
        ->call('pilihSiswaAndTagihan', $siswa->id, $tagihanSpp->id)
        ->call('toggleTagihan', $tagihanUjian->id)
        ->set('nominal_dibayar', 350000)
        ->set('metode_bayar', 'Transfer Bank')
        ->set('tanggal_bayar', now()->toDateString())
        ->call('savePayment')
        ->assertHasNoErrors();

    $siswa->refresh();
    expect(floatval($siswa->saldo_deposit))->toBe(50000.00);

    $tagihanSpp->refresh();
    $tagihanUjian->refresh();
    expect($tagihanSpp->status)->toBe('lunas');
    expect($tagihanUjian->status)->toBe('lunas');
});
