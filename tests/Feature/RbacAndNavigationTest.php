<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Tagihan;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use App\Models\Pembayaran;
use App\Models\GajiGuru;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    $this->artisan('db:seed', ['--class' => 'PengaturanSeeder']);
    $this->artisan('db:seed', ['--class' => 'DemoDataSeeder']);
    $this->artisan('db:seed', ['--class' => 'JenisTagihanSeeder']);
});

function createUserWithRole(string $roleName)
{
    $role = Role::firstOrCreate(['nama' => $roleName]);
    return User::create([
        'nama' => 'Test User ' . ucfirst($roleName),
        'username' => 'test_' . $roleName . '_' . rand(100, 999),
        'email' => 'test_' . $roleName . '_' . rand(100, 999) . '@test.com',
        'password' => bcrypt('password'),
        'role_id' => $role->id,
        'status' => 'aktif',
    ]);
}

test('pengajuan dana route is accessible strictly by finance role', function () {
    $financeUser = createUserWithRole('finance');
    $response = $this->actingAs($financeUser)->get(route('finance.pengajuan-dana'));
    $response->assertStatus(200);
});

test('pengajuan dana route is blocked for non finance roles', function () {
    $nonFinanceRoles = ['guru', 'koordinator', 'kepala_sekolah', 'tata_usaha', 'super_admin', 'murid'];

    foreach ($nonFinanceRoles as $roleName) {
        $user = createUserWithRole($roleName);
        $response = $this->actingAs($user)->get(route('finance.pengajuan-dana'));
        $response->assertStatus(302);
    }
});

test('kalender akademik route is accessible by all authenticated roles', function () {
    $allRoles = ['guru', 'murid', 'tata_usaha', 'finance', 'koordinator', 'kepala_sekolah', 'super_admin'];

    foreach ($allRoles as $roleName) {
        $user = createUserWithRole($roleName);
        $response = $this->actingAs($user)->get(route('kalender-akademik.shared'));
        $response->assertStatus(200);
    }
});

test('kepala sekolah can access monitoring & audit log routes', function () {
    $kepsek = createUserWithRole('kepala_sekolah');

    $routesToTest = [
        'kepala-sekolah.dashboard',
        'kepala-sekolah.audit-log',
        'finance.overview-pembayaran',
        'finance.laporan.tunggakan',
        'finance.laporan.pemasukan',
        'finance.laporan.pengeluaran',
        'finance.dana-bos',
    ];

    foreach ($routesToTest as $routeName) {
        $response = $this->actingAs($kepsek)->get(route($routeName));
        $response->assertStatus(200);
    }
});

test('murid can download own receipt but cannot download others receipt', function () {
    $roleMurid = Role::where('nama', 'murid')->first();
    $roleTU = Role::where('nama', 'tata_usaha')->first();

    $userMurid1 = createUserWithRole('murid');
    $userMurid2 = createUserWithRole('murid');

    $kelas = Kelas::first();
    $siswa1 = Siswa::create(['user_id' => $userMurid1->id, 'kelas_id' => $kelas->id, 'nisn' => '1111111111', 'nis' => '11111', 'tanggal_masuk' => date('Y-m-d')]);
    $siswa2 = Siswa::create(['user_id' => $userMurid2->id, 'kelas_id' => $kelas->id, 'nisn' => '2222222222', 'nis' => '22222', 'tanggal_masuk' => date('Y-m-d')]);

    $userMurid1->refresh();
    $userMurid2->refresh();

    $ta = TahunAjaran::first();
    $jt = JenisTagihan::first();

    $tagihan1 = Tagihan::create([
        'siswa_id' => $siswa1->id,
        'jenis_tagihan_id' => $jt->id,
        'tahun_ajaran_id' => $ta->id,
        'bulan' => 'Juli',
        'nominal' => 200000,
        'total_dibayar' => 200000,
        'status' => 'lunas',
        'jatuh_tempo' => now(),
    ]);

    $pembayaran1 = Pembayaran::create([
        'no_resi' => 'RESI-TEST-001',
        'tagihan_id' => $tagihan1->id,
        'tanggal_bayar' => date('Y-m-d'),
        'nominal_dibayar' => 200000,
        'metode_bayar' => 'Tunai',
        'petugas_id' => $userMurid1->id,
    ]);

    // Murid 1 can download own receipt
    $resOwner = $this->actingAs($userMurid1)->get(route('pembayaran.resi', $pembayaran1->id));
    $resOwner->assertStatus(200);

    // Murid 2 blocked from downloading Murid 1 receipt
    $resOther = $this->actingAs($userMurid2)->get(route('pembayaran.resi', $pembayaran1->id));
    $resOther->assertStatus(403);
});

test('guru can download own salary slip but cannot download others slip', function () {
    $userGuru1 = createUserWithRole('guru');
    $userGuru2 = createUserWithRole('guru');

    $guru1 = Guru::create(['user_id' => $userGuru1->id, 'nip' => 'G101', 'jenis_kelamin' => 'L', 'tanggal_masuk' => date('Y-m-d')]);
    $guru2 = Guru::create(['user_id' => $userGuru2->id, 'nip' => 'G102', 'jenis_kelamin' => 'P', 'tanggal_masuk' => date('Y-m-d')]);

    $userGuru1->refresh();
    $userGuru2->refresh();

    $gaji1 = GajiGuru::create([
        'guru_id' => $guru1->id,
        'bulan' => 'Juli',
        'tahun' => date('Y'),
        'gaji_pokok' => 3000000,
        'insentif_bpjs' => 0,
        'insentif_maghrib_mengaji' => 0,
        'potongan_peminjaman' => 0,
        'potongan_lainnya' => 0,
        'total_diterima' => 3000000,
        'tanggal_bayar' => date('Y-m-d'),
        'status' => 'dibayar',
    ]);

    // Guru 1 can download own salary slip
    $resOwner = $this->actingAs($userGuru1)->get(route('gaji-guru.slip', $gaji1->id));
    $resOwner->assertStatus(200);

    // Guru 2 blocked from downloading Guru 1 salary slip
    $resOther = $this->actingAs($userGuru2)->get(route('gaji-guru.slip', $gaji1->id));
    $resOther->assertStatus(403);
});

test('sidebar renders correctly without missing routes for all roles', function () {
    $roles = ['super_admin', 'tata_usaha', 'guru', 'murid', 'finance', 'kepala_sekolah', 'pengawas', 'koordinator'];

    foreach ($roles as $roleName) {
        $user = createUserWithRole($roleName);
        $view = $this->actingAs($user)->view('components.sidebar');
        $view->assertSee('SIAKAD');
    }
});
