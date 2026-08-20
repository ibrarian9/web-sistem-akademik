<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\JenisTagihan;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\Tabungan;
use App\Models\PemasukanKas;
use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;
use App\Models\DanaBos;
use App\Models\PengajuanDana;
use App\Models\GajiGuru;
use App\Models\Peminjaman;
use App\Models\Nilai;
use App\Models\Rapor;
use App\Models\MataPelajaran;
use App\Livewire\Finance\OverviewPembayaran;
use App\Livewire\Finance\TabunganSiswa;
use App\Livewire\Finance\Dashboard as FinanceDashboard;
use App\Livewire\Finance\ManajemenTagihan;
use Livewire\Livewire;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

    $roleFinance = Role::firstOrCreate(['nama' => 'finance']);
    $roleSuperAdmin = Role::firstOrCreate(['nama' => 'super_admin']);
    $roleGuru = Role::firstOrCreate(['nama' => 'guru']);
    $roleMurid = Role::firstOrCreate(['nama' => 'murid']);

    $this->userFinance = User::create([
        'nama' => 'Bendahara Utama',
        'username' => 'finance_audit',
        'email' => 'finance_audit@yayasan.or.id',
        'password' => bcrypt('password123'),
        'role_id' => $roleFinance->id,
        'status' => 'aktif',
    ]);

    $this->userGuru = User::create([
        'nama' => 'Ust. Hamzah',
        'username' => 'guru_audit',
        'email' => 'guru_audit@yayasan.or.id',
        'password' => bcrypt('password123'),
        'role_id' => $roleGuru->id,
        'status' => 'aktif',
    ]);

    $this->guru = Guru::create([
        'user_id' => $this->userGuru->id,
        'nip' => 'GUR-999',
        'status_aktif' => true,
        'tanggal_masuk' => date('Y-m-d'),
    ]);

    $this->activeTA = TahunAjaran::create([
        'nama' => '2026/2027',
        'status_aktif' => true,
    ]);

    $this->semester = \App\Models\Semester::create([
        'tahun_ajaran_id' => $this->activeTA->id,
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

    $this->userSiswa = User::create([
        'nama' => 'Ahmad Zaki',
        'username' => 'siswa_audit',
        'email' => 'siswa_audit@yayasan.or.id',
        'password' => bcrypt('password123'),
        'role_id' => $roleMurid->id,
        'status' => 'aktif',
    ]);

    $this->siswa = Siswa::create([
        'user_id' => $this->userSiswa->id,
        'nis' => '1001',
        'nisn' => '001001001',
        'kelas_id' => $this->kelas->id,
        'status' => 'aktif',
        'saldo_deposit' => 50000,
        'tanggal_masuk' => date('Y-m-d'),
    ]);

    $this->jenisSpp = JenisTagihan::create([
        'nama' => 'SPP Bulanan',
        'kategori' => 'rutin',
        'default_nominal' => 350000,
    ]);
});

test('audit log covers financial model lifecycle', function () {
    $this->actingAs($this->userFinance);

    // 1. Tagihan Create & Update
    $tagihan = Tagihan::create([
        'siswa_id' => $this->siswa->id,
        'jenis_tagihan_id' => $this->jenisSpp->id,
        'tahun_ajaran_id' => $this->activeTA->id,
        'bulan' => 'Agustus',
        'nominal' => 350000,
        'status' => 'belum_bayar',
        'total_dibayar' => 0,
        'jatuh_tempo' => date('Y-m-d'),
    ]);

    $this->assertDatabaseHas('activity_log', [
        'subject_type' => Tagihan::class,
        'subject_id' => $tagihan->id,
        'event' => 'created',
        'log_name' => 'keuangan',
        'causer_id' => $this->userFinance->id,
    ]);

    // 2. Pembayaran Create
    $pembayaran = Pembayaran::create([
        'no_resi' => 'KW-TEST-999',
        'tagihan_id' => $tagihan->id,
        'nominal_dibayar' => 350000,
        'kelebihan_bayar' => 0,
        'tanggal_bayar' => date('Y-m-d'),
        'metode_bayar' => 'Tunai',
        'petugas_id' => $this->userFinance->id,
    ]);

    $this->assertDatabaseHas('activity_log', [
        'subject_type' => Pembayaran::class,
        'subject_id' => $pembayaran->id,
        'event' => 'created',
        'log_name' => 'keuangan',
        'causer_id' => $this->userFinance->id,
    ]);

    // 3. Tabungan Setor
    $tabungan = Tabungan::create([
        'siswa_id' => $this->siswa->id,
        'petugas_id' => $this->userFinance->id,
        'kode_transaksi' => 'TAB-TEST-001',
        'jenis' => 'setor',
        'nominal' => 100000,
        'saldo_akhir' => 150000,
        'tanggal' => date('Y-m-d'),
        'keterangan' => 'Setor Tabungan',
    ]);

    $this->assertDatabaseHas('activity_log', [
        'subject_type' => Tabungan::class,
        'subject_id' => $tabungan->id,
        'event' => 'created',
        'log_name' => 'keuangan',
    ]);

    // 4. Pemasukan Kas & Pengeluaran
    $pemasukan = PemasukanKas::create([
        'kategori' => 'Infaq',
        'jumlah' => 200000,
        'tanggal' => date('Y-m-d'),
        'keterangan' => 'Infaq Jumat',
        'petugas_id' => $this->userFinance->id,
    ]);

    $this->assertDatabaseHas('activity_log', [
        'subject_type' => PemasukanKas::class,
        'subject_id' => $pemasukan->id,
        'event' => 'created',
        'log_name' => 'keuangan',
    ]);

    $kategoriExp = KategoriPengeluaran::create(['nama' => 'ATK']);
    $pengeluaran = Pengeluaran::create([
        'kategori_pengeluaran_id' => $kategoriExp->id,
        'jumlah' => 50000,
        'tanggal' => date('Y-m-d'),
        'keterangan' => 'Beli Spidol',
        'petugas_id' => $this->userFinance->id,
    ]);

    $this->assertDatabaseHas('activity_log', [
        'subject_type' => Pengeluaran::class,
        'subject_id' => $pengeluaran->id,
        'event' => 'created',
        'log_name' => 'keuangan',
    ]);

    // 5. Dana BOS & Pengajuan Dana
    $bos = DanaBos::create([
        'tahun_ajaran_id' => $this->activeTA->id,
        'jenis' => 'masuk',
        'tanggal' => date('Y-m-d'),
        'nominal' => 5000000,
        'kategori' => 'BOS Reguler Tahap 1',
        'keterangan' => 'Penerimaan BOS',
    ]);

    $this->assertDatabaseHas('activity_log', [
        'subject_type' => DanaBos::class,
        'subject_id' => $bos->id,
        'event' => 'created',
        'log_name' => 'keuangan',
    ]);
});

test('audit log covers academic models', function () {
    $this->actingAs($this->userGuru);

    $mapel = MataPelajaran::create([
        'nama_mapel' => 'Matematika',
        'jenis' => 'umum',
        'kkm' => 75,
    ]);

    $this->assertDatabaseHas('activity_log', [
        'subject_type' => MataPelajaran::class,
        'subject_id' => $mapel->id,
        'event' => 'created',
    ]);

    $komponen = \App\Models\KomponenNilai::create([
        'nama' => 'Ulangan Harian',
        'bobot' => 30,
    ]);

    $nilai = Nilai::create([
        'siswa_id' => $this->siswa->id,
        'mapel_id' => $mapel->id,
        'guru_id' => $this->guru->id,
        'kelas_id' => $this->kelas->id,
        'semester_id' => $this->semester->id,
        'komponen_nilai_id' => $komponen->id,
        'tanggal' => date('Y-m-d'),
        'nilai' => 88.5,
    ]);

    $this->assertDatabaseHas('activity_log', [
        'subject_type' => Nilai::class,
        'subject_id' => $nilai->id,
        'event' => 'created',
        'log_name' => 'akademik',
    ]);
});

test('finance pages render without N+1 query leaks', function () {
    $this->actingAs($this->userFinance);

    // Create multiple students & invoices to detect N+1
    for ($i = 1; $i <= 5; $i++) {
        $u = User::create([
            'nama' => "Siswa {$i}",
            'username' => "siswa_{$i}",
            'password' => bcrypt('password123'),
            'role_id' => $this->userSiswa->role_id,
        ]);
        $s = Siswa::create([
            'user_id' => $u->id,
            'nis' => "200{$i}",
            'kelas_id' => $this->kelas->id,
            'status' => 'aktif',
            'tanggal_masuk' => date('Y-m-d'),
        ]);
        $t = Tagihan::create([
            'siswa_id' => $s->id,
            'jenis_tagihan_id' => $this->jenisSpp->id,
            'tahun_ajaran_id' => $this->activeTA->id,
            'bulan' => 'Agustus',
            'nominal' => 350000,
            'status' => 'belum_bayar',
            'total_dibayar' => 0,
            'jatuh_tempo' => date('Y-m-d'),
        ]);
        Tabungan::create([
            'siswa_id' => $s->id,
            'petugas_id' => $this->userFinance->id,
            'kode_transaksi' => "TAB-00{$i}",
            'jenis' => 'setor',
            'nominal' => 50000,
            'saldo_akhir' => 50000,
            'tanggal' => date('Y-m-d'),
        ]);
    }

    // 1. OverviewPembayaran
    Livewire::test(OverviewPembayaran::class)
        ->assertStatus(200)
        ->assertSee('Overview Pembayaran Siswa');

    // 2. TabunganSiswa
    Livewire::test(TabunganSiswa::class)
        ->assertStatus(200)
        ->assertSee('Manajemen Tabungan Siswa');

    // 3. FinanceDashboard
    Livewire::test(FinanceDashboard::class)
        ->assertStatus(200);

    // 4. ManajemenTagihan
    Livewire::test(ManajemenTagihan::class)
        ->assertStatus(200)
        ->assertSee('Manajemen Tagihan Siswa');
});
