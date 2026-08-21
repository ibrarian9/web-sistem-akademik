<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\JenisTagihan;
use App\Models\Tagihan;
use App\Models\Tabungan;
use App\Models\AuditLog;
use App\Livewire\Finance\ManajemenTagihan;
use App\Livewire\Finance\DetailTagihanSiswa;
use App\Livewire\Finance\TabunganSiswa;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

    $roleFounder = Role::where('nama', 'super_admin')->first();
    $roleFinance = Role::where('nama', 'finance')->first();
    $roleMurid = Role::where('nama', 'murid')->first();

    $this->founderUser = User::create([
        'nama' => 'Marwansyah (Founder)',
        'username' => 'marwansyah_founder',
        'email' => 'founder@siakad.or.id',
        'password' => bcrypt('password123'),
        'role_id' => $roleFounder->id,
        'status' => 'aktif',
    ]);

    $this->financeUser = User::create([
        'nama' => 'Staff Keuangan',
        'username' => 'staff_finance',
        'email' => 'finance@siakad.or.id',
        'password' => bcrypt('password123'),
        'role_id' => $roleFinance->id,
        'status' => 'aktif',
    ]);

    $this->ta = TahunAjaran::create([
        'nama' => '2026/2027',
        'status_aktif' => true,
    ]);

    $this->semester = Semester::create([
        'tahun_ajaran_id' => $this->ta->id,
        'semester' => 'ganjil',
        'tanggal_mulai' => date('Y-m-d'),
        'tanggal_selesai' => date('Y-m-d', strtotime('+6 months')),
        'status_aktif' => true,
    ]);

    $this->kelas = Kelas::create([
        'nama_kelas' => '7A',
        'tingkat' => 7,
        'semester_id' => $this->semester->id,
    ]);

    $this->siswaUser = User::create([
        'nama' => 'Ahmad Santri',
        'username' => 'ahmad_santri',
        'password' => bcrypt('password123'),
        'role_id' => $roleMurid->id,
        'status' => 'aktif',
    ]);

    $this->siswa = Siswa::create([
        'user_id' => $this->siswaUser->id,
        'nis' => '9901',
        'kelas_id' => $this->kelas->id,
        'status' => 'aktif',
        'tanggal_masuk' => date('Y-m-d'),
    ]);

    $this->jenisTagihan = JenisTagihan::create([
        'nama' => 'SPP Bulanan',
        'default_nominal' => 250000.00,
        'tipe' => 'bulanan',
    ]);
});

test('finance can edit tagihan but cannot delete tagihan', function () {
    $tagihan = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $this->jenisTagihan->id,
        'tahun_ajaran_id' => $this->ta->id,
        'bulan' => 'Juli',
        'nominal' => 250000.00,
        'total_dibayar' => 0.00,
        'status' => 'belum_bayar',
        'jatuh_tempo' => date('Y-m-d', strtotime('+30 days')),
    ]);

    $this->actingAs($this->financeUser);

    // 1. Finance can edit tagihan
    Livewire::test(ManajemenTagihan::class)
        ->call('openEditModal', $tagihan->id)
        ->assertSet('edit_nominal', 250000.00)
        ->set('edit_nominal', 300000.00)
        ->set('edit_bulan', 'Agustus')
        ->call('saveEditTagihan')
        ->assertHasNoErrors()
        ->assertSee('Tagihan berhasil diperbarui');

    $tagihan->refresh();
    expect((float)$tagihan->nominal)->toBe(300000.00);
    expect($tagihan->bulan)->toBe('Agustus');

    // 2. Finance CANNOT delete tagihan
    Livewire::test(ManajemenTagihan::class)
        ->call('deleteTagihan', $tagihan->id)
        ->assertSee('Akses Ditolak');

    $this->assertDatabaseHas('tagihan', ['id' => $tagihan->id]);
});

test('founder can edit and delete tagihan with audit log recorded', function () {
    $tagihan = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $this->jenisTagihan->id,
        'tahun_ajaran_id' => $this->ta->id,
        'bulan' => 'September',
        'nominal' => 250000.00,
        'total_dibayar' => 0.00,
        'status' => 'belum_bayar',
        'jatuh_tempo' => date('Y-m-d', strtotime('+30 days')),
    ]);

    $this->actingAs($this->founderUser);

    // Founder can edit
    Livewire::test(ManajemenTagihan::class)
        ->call('openEditModal', $tagihan->id)
        ->set('edit_nominal', 275000.00)
        ->call('saveEditTagihan')
        ->assertHasNoErrors();

    $tagihan->refresh();
    expect((float)$tagihan->nominal)->toBe(275000.00);

    // Founder can delete
    Livewire::test(ManajemenTagihan::class)
        ->call('deleteTagihan', $tagihan->id)
        ->assertSee('Tagihan berhasil dihapus');

    $this->assertSoftDeleted('tagihan', ['id' => $tagihan->id]);

    // Check AuditLog recorded
    $logs = DB::table('activity_log')
        ->where('subject_type', Tagihan::class)
        ->where('subject_id', $tagihan->id)
        ->get();

    expect($logs->count())->toBeGreaterThanOrEqual(2);
});

test('finance can edit tabungan but cannot delete tabungan', function () {
    $this->actingAs($this->financeUser);

    // Create 2 transactions
    Livewire::test(TabunganSiswa::class)
        ->call('openTransactionModal', $this->siswa->id, 'setor')
        ->set('nominal', 50000.00)
        ->set('tanggal', date('Y-m-d'))
        ->call('saveTransaction')
        ->assertHasNoErrors();

    Livewire::test(TabunganSiswa::class)
        ->call('openTransactionModal', $this->siswa->id, 'setor')
        ->set('nominal', 30000.00)
        ->set('tanggal', date('Y-m-d'))
        ->call('saveTransaction')
        ->assertHasNoErrors();

    $firstTx = Tabungan::where('siswa_id', $this->siswa->id)->orderBy('id', 'asc')->first();
    $secondTx = Tabungan::where('siswa_id', $this->siswa->id)->orderBy('id', 'desc')->first();

    expect((float)$secondTx->saldo_akhir)->toBe(80000.00);

    // Finance edits first transaction from 50000 to 70000
    Livewire::test(TabunganSiswa::class)
        ->call('openEditTransaction', $firstTx->id)
        ->set('edit_nominal', 70000.00)
        ->call('saveEditTransaction')
        ->assertHasNoErrors();

    $secondTx->refresh();
    // After recalculation, second balance should be 70000 + 30000 = 100000
    expect((float)$secondTx->saldo_akhir)->toBe(100000.00);

    // Finance tries to delete -> Blocked!
    Livewire::test(TabunganSiswa::class)
        ->call('deleteTransaction', $firstTx->id)
        ->assertSee('Akses Ditolak');

    $this->assertDatabaseHas('tabungans', ['id' => $firstTx->id]);
});

test('founder can edit and delete tabungan with balance recalculation and audit log', function () {
    $this->actingAs($this->founderUser);

    $tx1 = Tabungan::create([
        'siswa_id' => $this->siswa->id,
        'petugas_id' => $this->founderUser->id,
        'kode_transaksi' => 'TAB-2026-001',
        'jenis' => 'setor',
        'nominal' => 100000.00,
        'saldo_akhir' => 100000.00,
        'tanggal' => date('Y-m-d'),
        'keterangan' => 'Setoran 1',
    ]);

    $tx2 = Tabungan::create([
        'siswa_id' => $this->siswa->id,
        'petugas_id' => $this->founderUser->id,
        'kode_transaksi' => 'TAB-2026-002',
        'jenis' => 'tarik',
        'nominal' => 20000.00,
        'saldo_akhir' => 80000.00,
        'tanggal' => date('Y-m-d'),
        'keterangan' => 'Penarikan 1',
    ]);

    // Founder deletes tx1
    Livewire::test(TabunganSiswa::class)
        ->call('deleteTransaction', $tx1->id)
        ->assertSee('Catatan transaksi tabungan berhasil dihapus');

    $this->assertDatabaseMissing('tabungans', ['id' => $tx1->id]);

    // After recalculation, remaining tx2 (tarik 20000) becomes -20000
    $tx2->refresh();
    expect((float)$tx2->saldo_akhir)->toBe(-20000.00);

    // Audit log exists
    $logs = DB::table('activity_log')->where('subject_type', Tabungan::class)->get();
    expect($logs->count())->toBeGreaterThanOrEqual(1);
});

test('detail tagihan siswa page renders correctly with filters and metrics', function () {
    $this->actingAs($this->financeUser);

    $t1 = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'tahun_ajaran_id' => $this->ta->id,
        'jenis_tagihan_id' => $this->jenisTagihan->id,
        'bulan' => 'Juli',
        'nominal' => 350000.00,
        'total_dibayar' => 350000.00,
        'status' => 'lunas',
        'jatuh_tempo' => date('Y-m-d', strtotime('+30 days')),
    ]);

    $t2 = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'tahun_ajaran_id' => $this->ta->id,
        'jenis_tagihan_id' => $this->jenisTagihan->id,
        'bulan' => 'Agustus',
        'nominal' => 350000.00,
        'total_dibayar' => 0.00,
        'status' => 'belum_bayar',
        'jatuh_tempo' => date('Y-m-d', strtotime('+60 days')),
    ]);

    // Test component rendering
    Livewire::test(DetailTagihanSiswa::class, ['siswaId' => $this->siswa->id])
        ->assertOk()
        ->assertSee('Ahmad Santri')
        ->assertSee('Total Tagihan')
        ->assertSee('700.000')
        ->assertSee('Total Terbayar')
        ->assertSee('350.000')
        ->assertSee('Sisa Tunggakan')
        ->set('filterBulan', 'Agustus')
        ->assertSee('20/10/2026') // Due date of Agustus
        ->set('filterBulan', '')
        ->set('filterStatus', 'lunas')
        ->assertSee('350.000');

    // Test HTTP route access
    $response = $this->get(route('finance.tagihan.detail', $this->siswa->id));
    $response->assertOk();
    $response->assertSee('Ahmad Santri');
});

