<?php

use App\Models\KategoriPengeluaran;
use App\Models\Kelas;
use App\Models\Pengeluaran;
use App\Models\Pembayaran;
use App\Models\Role;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Livewire\Finance\Laporan\LaporanPengeluaran;
use App\Livewire\Finance\Laporan\LaporanPemasukan;
use App\Livewire\Finance\Laporan\LaporanTunggakan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->roleFinance = Role::firstOrCreate(['nama' => 'finance'], ['deskripsi' => 'Finance']);
    $this->roleMurid = Role::firstOrCreate(['nama' => 'murid'], ['deskripsi' => 'Murid']);

    $this->financeUser = User::create([
        'nama' => 'Staff Keuangan',
        'username' => 'finance_staff',
        'email' => 'finance_staff@test.com',
        'password' => bcrypt('password123'),
        'role_id' => $this->roleFinance->id,
        'status' => 'aktif',
    ]);

    TahunAjaran::query()->update(['status_aktif' => false]);
    $this->tahunAjaran = TahunAjaran::create([
        'nama' => '2026/2027',
        'status_aktif' => true,
    ]);

    $this->semester = Semester::create([
        'tahun_ajaran_id' => $this->tahunAjaran->id,
        'semester' => 'ganjil',
        'tanggal_mulai' => date('Y-m-d'),
        'tanggal_selesai' => date('Y-m-d', strtotime('+6 months')),
        'status_aktif' => true,
    ]);

    $this->kelas = Kelas::create([
        'nama_kelas' => '7A',
        'tingkat' => 7,
        'tahun_ajaran_id' => $this->tahunAjaran->id,
        'semester_id' => $this->semester->id,
    ]);

    $this->kategoriPengeluaran = KategoriPengeluaran::create([
        'nama' => 'Operasional Kantor',
        'jenis' => 'operasional',
    ]);
});

test('laporan pengeluaran mendukung filter hari ini, bulan ini, custom, dan filter bulan', function () {
    $this->actingAs($this->financeUser);

    // Buat data pengeluaran dengan tanggal berbeda
    $pToday = Pengeluaran::create([
        'kategori_pengeluaran_id' => $this->kategoriPengeluaran->id,
        'jumlah' => 1500000.00,
        'tanggal' => date('Y-m-d'),
        'keterangan' => 'Pengeluaran Hari Ini',
        'petugas_id' => $this->financeUser->id,
    ]);

    $pPast = Pengeluaran::create([
        'kategori_pengeluaran_id' => $this->kategoriPengeluaran->id,
        'jumlah' => 250000.00,
        'tanggal' => '2025-01-15',
        'keterangan' => 'Pengeluaran Tahun Lalu',
        'petugas_id' => $this->financeUser->id,
    ]);

    // Test filter hari ini
    Livewire::test(LaporanPengeluaran::class)
        ->set('filterPeriode', 'hari_ini')
        ->assertSee('Pengeluaran Hari Ini')
        ->assertDontSee('Pengeluaran Tahun Lalu');

    // Test filter custom
    Livewire::test(LaporanPengeluaran::class)
        ->set('filterPeriode', 'custom')
        ->set('startDate', '2025-01-01')
        ->set('endDate', '2025-01-31')
        ->assertSee('Pengeluaran Tahun Lalu')
        ->assertDontSee('Pengeluaran Hari Ini');

    // Test filter bulan
    $currentMonthName = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][intval(date('m')) - 1];
    Livewire::test(LaporanPengeluaran::class)
        ->set('bulan', $currentMonthName)
        ->assertSee('Pengeluaran Hari Ini');

    // Test PDF route dengan filter query
    $res = $this->get(route('finance.laporan.pengeluaran.pdf', [
        'filter_periode' => 'hari_ini',
        'bulan' => $currentMonthName,
    ]));
    $res->assertStatus(200);
    expect($res->headers->get('content-type'))->toBe('application/pdf');

    // Test CSV route dengan filter query
    $csvRes = $this->get(route('finance.export.pengeluaran', [
        'filter_periode' => 'hari_ini',
        'bulan' => $currentMonthName,
    ]));
    $csvRes->assertStatus(200);
});

test('laporan pemasukan mendukung filter hari ini, bulan ini, custom, dan filter bulan', function () {
    $this->actingAs($this->financeUser);

    $u = User::create([
        'nama' => 'Santri Pemasukan Test',
        'username' => 'santri_pemasukan',
        'email' => 'santripemasukan@test.com',
        'password' => bcrypt('password123'),
        'role_id' => $this->roleMurid->id,
        'status' => 'aktif',
    ]);
    $siswa = Siswa::create([
        'user_id' => $u->id,
        'nis' => '9001',
        'nisn' => '0009001',
        'kelas_id' => $this->kelas->id,
        'nama_wali' => 'Wali Santri Pemasukan',
        'no_hp_wali' => '08123456780',
        'status' => 'aktif',
        'tanggal_masuk' => date('Y-m-d'),
    ]);

    $jenisTagihan = \App\Models\JenisTagihan::create([
        'nama' => 'Infaq Pembangunan',
        'kategori' => 'rutin',
        'default_nominal' => 500000.00,
        'is_blocking' => true,
    ]);

    $currentMonthName = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][intval(date('m')) - 1];

    $tagihan = Tagihan::create([
        'siswa_id' => $siswa->id,
        'tahun_ajaran_id' => $this->tahunAjaran->id,
        'jenis_tagihan_id' => $jenisTagihan->id,
        'nama_tagihan' => 'Infaq Pembangunan 2026',
        'bulan' => $currentMonthName,
        'nominal' => 500000.00,
        'total_dibayar' => 500000.00,
        'status' => 'lunas',
        'jatuh_tempo' => date('Y-m-d'),
    ]);

    $pembayaran = Pembayaran::create([
        'tagihan_id' => $tagihan->id,
        'tanggal_bayar' => date('Y-m-d'),
        'nominal_dibayar' => 500000.00,
        'metode_bayar' => 'Tunai',
        'petugas_id' => $this->financeUser->id,
    ]);

    // Test filter hari ini
    Livewire::test(LaporanPemasukan::class)
        ->set('filterPeriode', 'hari_ini')
        ->assertSee('Santri Pemasukan Test')
        ->assertSee('Infaq Pembangunan');

    // Test filter bulan
    Livewire::test(LaporanPemasukan::class)
        ->set('bulan', $currentMonthName)
        ->assertSee('Santri Pemasukan Test');

    // Test PDF route
    $res = $this->get(route('finance.laporan.pemasukan.pdf', [
        'filter_periode' => 'hari_ini',
        'bulan' => $currentMonthName,
    ]));
    $res->assertStatus(200);
    expect($res->headers->get('content-type'))->toBe('application/pdf');

    // Test CSV route
    $csvRes = $this->get(route('finance.export.pemasukan', [
        'filter_periode' => 'hari_ini',
        'bulan' => $currentMonthName,
    ]));
    $csvRes->assertStatus(200);
});

test('laporan tunggakan mendukung preview pdf modal dan disable tombol saat kosong', function () {
    $this->actingAs($this->financeUser);

    // 1. Kondisi Kosong (0 tunggakan)
    Livewire::test(LaporanTunggakan::class)
        ->set('tahun_ajaran_id', $this->tahunAjaran->id)
        ->set('search', 'NonExistentStudentNameXYZ')
        ->call('openPreviewPdf')
        ->assertSet('showPreviewModal', false)
        ->assertSee('disabled')
        ->assertSee('Tidak ada data tunggakan');

    // 2. Buat Tagihan Belum Lunas
    $u = User::create([
        'nama' => 'Santri Tunggakan Modal Test',
        'username' => 'santri_tunggakan_modal',
        'email' => 'tunggakanmodal@test.com',
        'password' => bcrypt('password123'),
        'role_id' => $this->roleMurid->id,
        'status' => 'aktif',
    ]);
    $siswa = Siswa::create([
        'user_id' => $u->id,
        'nis' => '9002',
        'nisn' => '0009002',
        'kelas_id' => $this->kelas->id,
        'nama_wali' => 'Wali Tunggakan',
        'no_hp_wali' => '08123456781',
        'status' => 'aktif',
        'tanggal_masuk' => date('Y-m-d'),
    ]);

    $jt = \App\Models\JenisTagihan::create([
        'nama' => 'SPP Modal',
        'kategori' => 'rutin',
        'default_nominal' => 300000.00,
        'is_blocking' => true,
    ]);

    $tagihan = Tagihan::create([
        'siswa_id' => $siswa->id,
        'tahun_ajaran_id' => $this->tahunAjaran->id,
        'jenis_tagihan_id' => $jt->id,
        'nama_tagihan' => 'SPP Agustus 2026',
        'bulan' => 'Agustus',
        'nominal' => 300000.00,
        'total_dibayar' => 0.00,
        'status' => 'belum_bayar',
        'jatuh_tempo' => date('Y-m-d'),
    ]);

    // 3. Buka Preview Modal Berhasil
    Livewire::test(LaporanTunggakan::class)
        ->set('tahun_ajaran_id', $this->tahunAjaran->id)
        ->call('openPreviewPdf')
        ->assertSet('showPreviewModal', true)
        ->assertSee('Pratinjau Laporan Tunggakan Siswa PDF')
        ->call('closePreviewPdf')
        ->assertSet('showPreviewModal', false);

    // 4. Endpoint PDF Route
    $res = $this->get(route('finance.laporan.tunggakan.pdf', [
        'tahun_ajaran_id' => $this->tahunAjaran->id,
    ]));
    $res->assertStatus(200);
    expect($res->headers->get('content-type'))->toBe('application/pdf');
});
