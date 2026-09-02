<?php

use App\Models\DanaBos;
use App\Models\GajiGuru;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\Tabungan;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    $this->artisan('db:seed', ['--class' => 'PengaturanSeeder']);
    $this->artisan('db:seed', ['--class' => 'DemoDataSeeder']);

    $this->financeUser = User::whereHas('role', function ($q) {
        $q->where('nama', 'finance');
    })->first();

    $this->tahunAjaran = TahunAjaran::where('status_aktif', true)->first() ?? TahunAjaran::latest()->first();
    $this->kelas = Kelas::first();
});

test('finance user can export dana bos to pdf and excel', function () {
    DanaBos::create([
        'tahun_ajaran_id' => $this->tahunAjaran->id,
        'jenis' => 'masuk',
        'tanggal' => '2026-01-15',
        'nominal' => 25000000,
        'kategori' => 'BOS Reguler Tahap 1',
        'keterangan' => 'Penerimaan dana BOS termin pertama',
    ]);

    DanaBos::create([
        'tahun_ajaran_id' => $this->tahunAjaran->id,
        'jenis' => 'keluar',
        'tanggal' => '2026-01-20',
        'nominal' => 5000000,
        'kategori' => 'Buku Pelajaran',
        'keterangan' => 'Pembelian buku paket kurikulum',
    ]);

    // Test PDF export
    $responsePdf = $this->actingAs($this->financeUser)->get(route('finance.dana-bos.pdf'));
    $responsePdf->assertOk();
    $responsePdf->assertHeader('Content-Type', 'application/pdf');

    // Test Excel export
    $responseExcel = $this->actingAs($this->financeUser)->get(route('finance.dana-bos.excel'));
    $responseExcel->assertOk();
    $responseExcel->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
});

test('finance user can export rekap gaji guru to pdf and excel', function () {
    $guru = Guru::with('user')->first();
    if (!$guru) {
        $roleGuru = Role::where('nama', 'guru')->first();
        $guruUser = User::create([
            'nama' => 'Ustadz Abdullah',
            'username' => 'guru_abdullah',
            'email' => 'abdullah@test.com',
            'password' => bcrypt('password'),
            'role_id' => $roleGuru->id,
        ]);

        $guru = Guru::create([
            'user_id' => $guruUser->id,
            'nip' => '198501012010011001',
            'jabatan' => 'Guru PAI',
        ]);
    }

    GajiGuru::create([
        'guru_id' => $guru->id,
        'bulan' => 'Januari',
        'tahun' => 2026,
        'jabatan' => 'Guru PAI',
        'jam_kerja' => '07.00-14.00',
        'gaji_pokok' => 3000000,
        'gaji_berkala' => 200000,
        'honor_ekskul' => 150000,
        'insentif' => 250000,
        'insentif_bpjs' => 0,
        'insentif_maghrib_mengaji' => 0,
        'total_bruto' => 3600000,
        'potongan_sosial' => 10000,
        'potongan_peminjaman' => 0,
        'potongan_bpjstk' => 0,
        'potongan_lainnya' => 0,
        'total_potongan' => 10000,
        'total_diterima' => 3590000,
        'status' => 'dibayar',
        'tanggal_bayar' => '2026-01-25',
    ]);

    // Test Rekap PDF
    $responsePdf = $this->actingAs($this->financeUser)->get(route('finance.gaji-guru.rekap-pdf', [
        'bulan' => 'Januari',
        'tahun' => 2026,
    ]));
    $responsePdf->assertOk();
    $responsePdf->assertHeader('Content-Type', 'application/pdf');

    // Test Rekap Excel
    $responseExcel = $this->actingAs($this->financeUser)->get(route('finance.gaji-guru.rekap-excel', [
        'bulan' => 'Januari',
        'tahun' => 2026,
    ]));
    $responseExcel->assertOk();
    $responseExcel->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
});

test('finance user can export tabungan siswa rekap and individual buku mutasi to pdf and excel', function () {
    $siswa = Siswa::with('user')->first();
    if (!$siswa) {
        $roleMurid = Role::where('nama', 'murid')->first();
        $siswaUser = User::create([
            'nama' => 'Ahmad Santri',
            'username' => 'santri_ahmad',
            'email' => 'ahmad_santri@test.com',
            'password' => bcrypt('password'),
            'role_id' => $roleMurid->id,
        ]);

        $siswa = Siswa::create([
            'user_id' => $siswaUser->id,
            'nis' => '12345',
            'nisn' => '0012345678',
            'kelas_id' => $this->kelas->id,
        ]);
    }

    Tabungan::create([
        'siswa_id' => $siswa->id,
        'petugas_id' => $this->financeUser->id,
        'jenis' => 'setor',
        'nominal' => 100000,
        'saldo_akhir' => 100000,
        'tanggal' => '2026-01-10',
        'kode_transaksi' => 'TAB-001',
        'keterangan' => 'Setoran awal',
    ]);

    Tabungan::create([
        'siswa_id' => $siswa->id,
        'petugas_id' => $this->financeUser->id,
        'jenis' => 'setor',
        'nominal' => 50000,
        'saldo_akhir' => 150000,
        'tanggal' => '2026-01-15',
        'kode_transaksi' => 'TAB-002',
        'keterangan' => 'Setoran tabungan',
    ]);

    // 1. Test Global Tabungan Rekap PDF & Excel
    $responseGlobalPdf = $this->actingAs($this->financeUser)->get(route('finance.tabungan.pdf'));
    $responseGlobalPdf->assertOk();
    $responseGlobalPdf->assertHeader('Content-Type', 'application/pdf');

    $responseGlobalExcel = $this->actingAs($this->financeUser)->get(route('finance.tabungan.excel'));
    $responseGlobalExcel->assertOk();
    $responseGlobalExcel->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

    // 2. Test Individual Student Mutasi PDF & Excel
    $responseStudentPdf = $this->actingAs($this->financeUser)->get(route('finance.tabungan.pdf', ['siswa_id' => $siswa->id]));
    $responseStudentPdf->assertOk();
    $responseStudentPdf->assertHeader('Content-Type', 'application/pdf');

    $responseStudentExcel = $this->actingAs($this->financeUser)->get(route('finance.tabungan.excel', ['siswa_id' => $siswa->id]));
    $responseStudentExcel->assertOk();
    $responseStudentExcel->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
});

test('unauthorized users cannot access finance exports and get redirected', function () {
    $muridUser = User::whereHas('role', fn($q) => $q->where('nama', 'murid'))->first();

    $this->actingAs($muridUser)->get(route('finance.dana-bos.pdf'))->assertRedirect(route('murid.dashboard'));
    $this->actingAs($muridUser)->get(route('finance.dana-bos.excel'))->assertRedirect(route('murid.dashboard'));
    $this->actingAs($muridUser)->get(route('finance.gaji-guru.rekap-pdf'))->assertRedirect(route('murid.dashboard'));
    $this->actingAs($muridUser)->get(route('finance.gaji-guru.rekap-excel'))->assertRedirect(route('murid.dashboard'));
    $this->actingAs($muridUser)->get(route('finance.tabungan.pdf'))->assertRedirect(route('murid.dashboard'));
    $this->actingAs($muridUser)->get(route('finance.tabungan.excel'))->assertRedirect(route('murid.dashboard'));
});
