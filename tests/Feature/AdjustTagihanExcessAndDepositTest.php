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
use App\Livewire\Finance\DetailTagihanSiswa;
use App\Livewire\Finance\ManajemenTagihan;
use App\Actions\Finance\AdjustTagihanExcessAction;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    $this->artisan('db:seed', ['--class' => 'PengaturanSeeder']);
    $this->artisan('db:seed', ['--class' => 'DemoDataSeeder']);
    $this->artisan('db:seed', ['--class' => 'JenisTagihanSeeder']);

    $this->userSuperAdmin = User::whereHas('role', function ($q) {
        $q->where('nama', 'super_admin');
    })->first();

    $this->userFinance = User::whereHas('role', function ($q) {
        $q->where('nama', 'finance');
    })->first();

    $this->tahunAjaran = TahunAjaran::where('status_aktif', true)->first() ?? TahunAjaran::first();
    $this->kelas = Kelas::first();
});

test('editing tagihan nominal from 350k to 1350k absorbs 1000k excess and reduces student deposit', function () {
    $this->actingAs($this->userSuperAdmin);

    $userSiswa = User::create([
        'nama' => 'Rian Hidayat',
        'username' => 'rian_' . rand(100, 999),
        'email' => 'rian_' . rand(100, 999) . '@test.com',
        'password' => bcrypt('password'),
        'role_id' => Role::where('nama', 'murid')->first()->id ?? 4,
    ]);

    // Student starts with 1.000.000 deposit from the overpayment
    $siswa = Siswa::create([
        'user_id' => $userSiswa->id,
        'kelas_id' => $this->kelas->id,
        'nisn' => '5551234567',
        'nis' => '55512',
        'saldo_deposit' => 1000000,
        'tanggal_masuk' => now()->toDateString(),
    ]);

    $jt = JenisTagihan::where('nama', 'like', '%SPP%')->first() ?? JenisTagihan::first();

    // Bill originally 350.000, student paid 1.350.000, resulting in 1.000.000 excess
    $tagihan = Tagihan::create([
        'siswa_id' => $siswa->id,
        'jenis_tagihan_id' => $jt->id,
        'tahun_ajaran_id' => $this->tahunAjaran->id,
        'bulan' => 'Juli',
        'nominal' => 350000,
        'total_dibayar' => 1350000,
        'status' => 'lunas',
        'jatuh_tempo' => now()->addDays(5),
    ]);

    $pembayaran = Pembayaran::create([
        'no_resi' => 'KW-TEST-SYNC-01',
        'tagihan_id' => $tagihan->id,
        'tanggal_bayar' => now()->toDateString(),
        'nominal_dibayar' => 1350000,
        'kelebihan_bayar' => 1000000,
        'metode_bayar' => 'Transfer Bank',
        'is_void' => false,
        'petugas_id' => $this->userSuperAdmin->id,
    ]);

    // Admin updates tagihan nominal to 1.350.000 using DetailTagihanSiswa
    Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $siswa->id])
        ->call('openEditModal', $tagihan->id)
        ->set('edit_nominal', 1350000)
        ->call('updateTagihan')
        ->assertHasNoErrors();

    $tagihan->refresh();
    $pembayaran->refresh();
    $siswa->refresh();

    // 1. Tagihan nominal is now 1.350.000 and status remains 'lunas'
    expect(floatval($tagihan->nominal))->toBe(1350000.00);
    expect($tagihan->status)->toBe('lunas');

    // 2. Pembayaran kelebihan_bayar is updated from 1.000.000 to 0
    expect(floatval($pembayaran->kelebihan_bayar))->toBe(0.00);

    // 3. Siswa saldo_deposit is reduced by 1.000.000 (becomes 0)
    expect(floatval($siswa->saldo_deposit))->toBe(0.00);
});

test('decreasing tagihan nominal adds excess difference to student deposit', function () {
    $this->actingAs($this->userSuperAdmin);

    $userSiswa = User::create([
        'nama' => 'Sarah Amalia',
        'username' => 'sarah_' . rand(100, 999),
        'email' => 'sarah_' . rand(100, 999) . '@test.com',
        'password' => bcrypt('password'),
        'role_id' => Role::where('nama', 'murid')->first()->id ?? 4,
    ]);

    $siswa = Siswa::create([
        'user_id' => $userSiswa->id,
        'kelas_id' => $this->kelas->id,
        'nisn' => '6661234567',
        'nis' => '66612',
        'saldo_deposit' => 0,
        'tanggal_masuk' => now()->toDateString(),
    ]);

    $jt = JenisTagihan::where('nama', 'like', '%SPP%')->first() ?? JenisTagihan::first();

    // Bill originally 1.000.000, paid 1.000.000 (excess = 0)
    $tagihan = Tagihan::create([
        'siswa_id' => $siswa->id,
        'jenis_tagihan_id' => $jt->id,
        'tahun_ajaran_id' => $this->tahunAjaran->id,
        'bulan' => 'Agustus',
        'nominal' => 1000000,
        'total_dibayar' => 1000000,
        'status' => 'lunas',
        'jatuh_tempo' => now()->addDays(5),
    ]);

    $pembayaran = Pembayaran::create([
        'no_resi' => 'KW-TEST-SYNC-02',
        'tagihan_id' => $tagihan->id,
        'tanggal_bayar' => now()->toDateString(),
        'nominal_dibayar' => 1000000,
        'kelebihan_bayar' => 0,
        'metode_bayar' => 'Tunai',
        'is_void' => false,
        'petugas_id' => $this->userSuperAdmin->id,
    ]);

    // Admin updates nominal to 800.000 directly via Action
    $tagihan->update(['nominal' => 800000]);
    $action = app(AdjustTagihanExcessAction::class);
    $result = $action->execute($tagihan, 1000000);

    expect($result['adjusted'])->toBeTrue();
    expect($result['delta_excess'])->toBe(200000.00);

    $tagihan->refresh();
    $pembayaran->refresh();
    $siswa->refresh();

    // Excess on payment becomes 200.000
    expect(floatval($pembayaran->kelebihan_bayar))->toBe(200000.00);

    // Student deposit increased by 200.000
    expect(floatval($siswa->saldo_deposit))->toBe(200000.00);
});

test('sync tagihan excess command identifies and reconciles inconsistent deposit balances', function () {
    $userSiswa = User::create([
        'nama' => 'Dimas Anggara',
        'username' => 'dimas_' . rand(100, 999),
        'email' => 'dimas_' . rand(100, 999) . '@test.com',
        'password' => bcrypt('password'),
        'role_id' => Role::where('nama', 'murid')->first()->id ?? 4,
    ]);

    // Inconsistent state: Tagihan already changed to 1.350.000, but payment still has 1.000.000 excess and student has 1.000.000 deposit
    $siswa = Siswa::create([
        'user_id' => $userSiswa->id,
        'kelas_id' => $this->kelas->id,
        'nisn' => '7771234567',
        'nis' => '77712',
        'saldo_deposit' => 1000000,
        'tanggal_masuk' => now()->toDateString(),
    ]);

    $jt = JenisTagihan::where('nama', 'like', '%SPP%')->first() ?? JenisTagihan::first();

    $tagihan = Tagihan::create([
        'siswa_id' => $siswa->id,
        'jenis_tagihan_id' => $jt->id,
        'tahun_ajaran_id' => $this->tahunAjaran->id,
        'bulan' => 'September',
        'nominal' => 1350000, // already edited
        'total_dibayar' => 1350000,
        'status' => 'lunas',
        'jatuh_tempo' => now()->addDays(5),
    ]);

    $pembayaran = Pembayaran::create([
        'no_resi' => 'KW-TEST-SYNC-03',
        'tagihan_id' => $tagihan->id,
        'tanggal_bayar' => now()->toDateString(),
        'nominal_dibayar' => 1350000,
        'kelebihan_bayar' => 1000000, // still old 1.000.000
        'metode_bayar' => 'Transfer Bank',
        'is_void' => false,
        'petugas_id' => $this->userSuperAdmin->id,
    ]);

    // Run the command to synchronize
    $this->artisan('finance:sync-tagihan-excess', ['--siswa_id' => $siswa->id])
        ->expectsOutputToContain('Berhasil menyinkronkan 1 tagihan dan saldo deposit siswa')
        ->assertExitCode(0);

    $pembayaran->refresh();
    $siswa->refresh();

    // After running command, excess is 0 and student deposit is 0
    expect(floatval($pembayaran->kelebihan_bayar))->toBe(0.00);
    expect(floatval($siswa->saldo_deposit))->toBe(0.00);
});
