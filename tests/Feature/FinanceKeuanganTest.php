<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Tagihan;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use App\Models\Pembayaran;
use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;
use App\Models\DanaBos;
use App\Models\Notifikasi;
use Livewire\Livewire;
use App\Livewire\Finance\Dashboard;
use App\Livewire\Finance\ManajemenTagihan;
use App\Livewire\Finance\InputPembayaran;
use App\Livewire\Finance\ArusKas;
use App\Livewire\Finance\DanaBos as BosLivewire;
use App\Livewire\Shared\NotificationDropdown;

beforeEach(function () {
    // Seed setups
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    $this->artisan('db:seed', ['--class' => 'KomponenNilaiSeeder']);
    $this->artisan('db:seed', ['--class' => 'PengaturanSeeder']);
    $this->artisan('db:seed', ['--class' => 'DemoDataSeeder']);
    $this->artisan('db:seed', ['--class' => 'JenisTagihanSeeder']);
    $this->artisan('db:seed', ['--class' => 'KategoriPengeluaranSeeder']);

    // Find finance user
    $this->userFinance = User::whereHas('role', function ($q) {
        $q->where('nama', 'finance');
    })->first();

    // Find student user
    $this->userMurid = User::whereHas('role', function ($q) {
        $q->where('nama', 'murid');
    })->first();

    $this->siswa = $this->userMurid->siswa;
});

test('finance can render dashboard', function () {
    $this->actingAs($this->userFinance);

    Livewire::test(Dashboard::class)
        ->assertStatus(200)
        ->assertSee('Dashboard Keuangan');
});

test('finance can create single tagihan for a student', function () {
    $this->actingAs($this->userFinance);

    $jt = JenisTagihan::first();
    $siswa = Siswa::first();

    Livewire::test(ManajemenTagihan::class)
        ->set('single_siswa_id', $siswa->id)
        ->set('jenis_tagihan_id', $jt->id)
        ->set('bulan', 'Agustus')
        ->set('nominal', 400000)
        ->set('jatuh_tempo', now()->addDays(15)->toDateString())
        ->call('createSingleTagihan')
        ->assertHasNoErrors();

    // Check tagihan record count
    $inserted = Tagihan::where([
        'siswa_id' => $siswa->id,
        'jenis_tagihan_id' => $jt->id,
        'bulan' => 'Agustus',
        'nominal' => 400000
    ])->count();

    expect($inserted)->toBeGreaterThan(0);
});

test('finance can record payment for a student and generate notification', function () {
    $this->actingAs($this->userFinance);

    $jt = JenisTagihan::first();
    $activeTA = TahunAjaran::where('status_aktif', true)->first();

    // Create an unpaid tagihan
    $tagihan = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $jt->id,
        'tahun_ajaran_id' => $activeTA->id,
        'bulan' => 'Juli',
        'nominal' => 500000,
        'total_dibayar' => 0,
        'status' => 'belum_bayar',
        'jatuh_tempo' => now()->addDays(10),
    ]);

    Livewire::test(InputPembayaran::class)
        ->set('siswa_id', $this->siswa->id)
        ->set('tagihan_id', $tagihan->id)
        ->set('nominal_dibayar', 300000)
        ->set('metode_bayar', 'Transfer Bank')
        ->call('savePayment')
        ->assertHasNoErrors();

    // Verify tagihan was updated
    $tagihan->refresh();
    expect($tagihan->total_dibayar)->toEqual(300000);
    expect($tagihan->status)->toEqual('sebagian');

    // Verify payment was recorded
    expect(Pembayaran::where('tagihan_id', $tagihan->id)->exists())->toBeTrue();

    // Verify notification was created for student
    $notification = Notifikasi::where('siswa_id', $this->siswa->id)->first();
    expect($notification)->not->toBeNull();
    expect($notification->judul)->toEqual('Pembayaran Berhasil');
});

test('finance can create an expenditure', function () {
    $this->actingAs($this->userFinance);

    $kategori = KategoriPengeluaran::first();

    Livewire::test(ArusKas::class)
        ->set('kategori_pengeluaran_id', $kategori->id)
        ->set('jumlah', 150000)
        ->set('tanggal', now()->toDateString())
        ->set('keterangan', 'Membeli ATK kantor sekretariat')
        ->call('saveExpense')
        ->assertHasNoErrors();

    expect(Pengeluaran::where('jumlah', 150000)->exists())->toBeTrue();
});

test('finance can create a bos fund transaction', function () {
    $this->actingAs($this->userFinance);

    Livewire::test(BosLivewire::class)
        ->set('jenis', 'masuk')
        ->set('nominal', 5000000)
        ->set('kategori', 'Belanja Buku Kurikulum Merdeka')
        ->set('tanggal', now()->toDateString())
        ->set('keterangan', 'BOS Reguler Tahap 1')
        ->call('saveTransaction')
        ->assertHasNoErrors();

    expect(DanaBos::where('nominal', 5000000)->exists())->toBeTrue();
});

test('student can view and mark notifications as read', function () {
    $this->actingAs($this->userMurid);

    // Create a dummy notification
    $notification = Notifikasi::create([
        'user_id' => $this->userMurid->id,
        'siswa_id' => $this->siswa->id,
        'judul' => 'Info Libur Semester',
        'isi_pesan' => 'Libur sekolah dimulai tanggal 20 Desember.',
        'jenis' => 'pengumuman',
        'channel' => 'in_app',
        'status_kirim' => 'terkirim',
        'dikirim_pada' => now(),
    ]);

    Livewire::test(NotificationDropdown::class)
        ->assertSee('Info Libur Semester')
        ->call('markAsRead', $notification->id);

    $notification->refresh();
    expect($notification->dibaca_pada)->not->toBeNull();
});
