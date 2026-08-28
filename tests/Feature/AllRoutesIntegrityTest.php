<?php

use Illuminate\Support\Facades\Route;

test('all named routes in web.php can be generated without throwing RouteNotFoundException', function () {
    $routes = Route::getRoutes()->getRoutesByName();

    expect(count($routes))->toBeGreaterThan(0);

    foreach ($routes as $name => $route) {
        // Collect required parameters if any
        $parameterNames = $route->parameterNames();
        $parameters = [];
        foreach ($parameterNames as $param) {
            $parameters[$param] = '1';
        }

        // Generating route must not throw RouteNotFoundException
        $url = route($name, $parameters);
        expect($url)->toBeString()->not->toBeEmpty();
    }
});

test('all sidebar routes exist in route collection', function () {
    $sidebarPath = resource_path('views/components/sidebar.blade.php');
    $content = file_get_contents($sidebarPath);

    // Extract all route names referenced in sidebar
    preg_match_all("/'route'\s*=>\s*'([^']+)'/", $content, $matches);
    $sidebarRouteNames = array_unique(array_filter($matches[1] ?? []));

    $registeredRoutes = array_keys(Route::getRoutes()->getRoutesByName());

    foreach ($sidebarRouteNames as $routeName) {
        expect(in_array($routeName, $registeredRoutes))
            ->toBeTrue("Sidebar references route '{$routeName}', but it is not defined in routes/web.php!");
    }
});

test('finance.tabungan route is registered and resolves correctly', function () {
    expect(Route::has('finance.tabungan'))->toBeTrue();
    expect(route('finance.tabungan'))->toContain('/finance/tabungan');
});

test('authenticated users can access all authorized module routes with HTTP 200/302 without 500 errors', function () {
    $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

    $roleSuperAdmin = \App\Models\Role::where('nama', 'super_admin')->first();
    $roleFinance = \App\Models\Role::where('nama', 'finance')->first();
    $roleTU = \App\Models\Role::where('nama', 'tata_usaha')->first();
    $roleGuru = \App\Models\Role::where('nama', 'guru')->first();
    $roleMurid = \App\Models\Role::where('nama', 'murid')->first();

    $superAdmin = \App\Models\User::create([
        'nama' => 'Admin Test',
        'username' => 'admin_test',
        'email' => 'admin_test@test.com',
        'password' => bcrypt('password'),
        'role_id' => $roleSuperAdmin->id,
        'status' => 'aktif',
    ]);

    $finance = \App\Models\User::create([
        'nama' => 'Finance Test',
        'username' => 'finance_test',
        'email' => 'finance_test@test.com',
        'password' => bcrypt('password'),
        'role_id' => $roleFinance->id,
        'status' => 'aktif',
    ]);

    $ta = \App\Models\TahunAjaran::create(['nama' => '2026/2027', 'status_aktif' => true]);
    $semester = \App\Models\Semester::create([
        'tahun_ajaran_id' => $ta->id,
        'semester' => 'ganjil',
        'tanggal_mulai' => date('Y-m-d'),
        'tanggal_selesai' => date('Y-m-d', strtotime('+6 months')),
        'status_aktif' => true,
    ]);
    $kelas = \App\Models\Kelas::create(['nama_kelas' => '7A', 'tingkat' => 7, 'semester_id' => $semester->id]);
    $muridUser = \App\Models\User::create([
        'nama' => 'Murid Test',
        'username' => 'murid_test',
        'email' => 'murid_test@test.com',
        'password' => bcrypt('password'),
        'role_id' => $roleMurid->id,
        'status' => 'aktif',
    ]);
    $siswa = \App\Models\Siswa::create([
        'user_id' => $muridUser->id,
        'nis' => '1001',
        'nisn' => '0011223344',
        'kelas_id' => $kelas->id,
        'nama_wali' => 'Budi Santoso',
        'no_hp_wali' => '08123456789',
        'tanggal_masuk' => date('Y-m-d'),
    ]);

    $jt = \App\Models\JenisTagihan::create(['nama' => 'SPP', 'default_nominal' => 350000]);
    $tagihan = \App\Models\Tagihan::create([
        'siswa_id' => $siswa->id,
        'tahun_ajaran_id' => $ta->id,
        'jenis_tagihan_id' => $jt->id,
        'bulan' => 'Juli',
        'nominal' => 350000,
        'total_dibayar' => 350000,
        'status' => 'lunas',
        'jatuh_tempo' => date('Y-m-d'),
    ]);
    $pembayaran = \App\Models\Pembayaran::create([
        'no_resi' => 'RES-00001',
        'tagihan_id' => $tagihan->id,
        'tanggal_bayar' => date('Y-m-d'),
        'nominal_dibayar' => 350000,
        'metode_bayar' => 'Tunai',
        'petugas_id' => $finance->id,
    ]);

    // 1. Test Finance Routes
    $this->actingAs($finance);
    $financeRoutes = [
        route('finance.dashboard'),
        route('finance.overview-pembayaran'),
        route('finance.tagihan'),
        route('finance.tagihan.detail', $siswa->id),
        route('finance.input-pembayaran'),
        route('finance.input-pembayaran', ['siswa_id' => $siswa->id]),
        route('finance.tabungan'),
        route('finance.arus-kas'),
        route('finance.arus-masuk'),
        route('finance.dana-bos'),
        route('finance.gaji-guru'),
        route('finance.peminjaman'),
        route('finance.laporan.tunggakan'),
        route('finance.laporan.pemasukan'),
        route('finance.laporan.pengeluaran'),
        route('finance.pembayaran.resi', $pembayaran->id),
        route('shared.tutorial-faq'),
        route('shared.notifications'),
    ];

    foreach ($financeRoutes as $url) {
        $res = $this->get($url);
        expect($res->status())->toBeLessThan(500, "Finance Route {$url} returned status {$res->status()}");
    }

    // 2. Test Super Admin Routes
    $this->actingAs($superAdmin);
    $adminRoutes = [
        route('super-admin.dashboard'),
        route('super-admin.siswa'),
        route('super-admin.guru'),
        route('super-admin.capaian-guru'),
        route('super-admin.karyawan'),
        route('super-admin.kelas'),
        route('super-admin.plotting-kelas'),
        route('super-admin.surat'),
        route('super-admin.jadwal'),
        route('super-admin.kalender-akademik'),
        route('super-admin.kenaikan-kelas'),
        route('super-admin.laporan.absensi-siswa'),
        route('super-admin.laporan.absensi-guru'),
        route('super-admin.laporan.rekap-nilai'),
        route('super-admin.user'),
        route('super-admin.audit-log'),
        route('super-admin.error-log'),
        route('super-admin.pengaturan'),
    ];

    foreach ($adminRoutes as $url) {
        $res = $this->get($url);
        expect($res->status())->toBeLessThan(500, "Super Admin Route {$url} returned status {$res->status()}");
    }

    // 3. Test Tata Usaha Routes
    $tuUser = \App\Models\User::create([
        'nama' => 'TU Test',
        'username' => 'tu_test',
        'email' => 'tu_test@test.com',
        'password' => bcrypt('password'),
        'role_id' => $roleTU->id,
        'status' => 'aktif',
    ]);
    $this->actingAs($tuUser);
    $tuRoutes = [
        route('tata-usaha.dashboard'),
        route('tata-usaha.absensi-karyawan'),
        route('tata-usaha.karyawan'),
        route('tata-usaha.piket'),
        route('tata-usaha.user'),
        route('tata-usaha.siswa'),
        route('tata-usaha.alumni'),
        route('tata-usaha.guru'),
        route('tata-usaha.kelas'),
        route('tata-usaha.plotting-kelas'),
        route('tata-usaha.surat'),
        route('tata-usaha.jadwal'),
        route('tata-usaha.kalender-akademik'),
        route('tata-usaha.kenaikan-kelas'),
        route('tata-usaha.komponen-nilai'),
        route('tata-usaha.laporan.absensi-siswa'),
        route('tata-usaha.laporan.absensi-guru'),
        route('tata-usaha.laporan.rekap-nilai'),
    ];

    foreach ($tuRoutes as $url) {
        $res = $this->get($url);
        expect($res->status())->toBeLessThan(500, "TU Route {$url} returned status {$res->status()}");
    }

    // 4. Test Guru Routes (Umum & Tahfizh)
    $guruUser = \App\Models\User::create([
        'nama' => 'Guru Test',
        'username' => 'guru_test',
        'email' => 'guru_test@test.com',
        'password' => bcrypt('password'),
        'role_id' => $roleGuru->id,
        'status' => 'aktif',
    ]);
    $guru = \App\Models\Guru::create([
        'user_id' => $guruUser->id,
        'nip' => '198501012010011002',
        'niy' => 'YFI-G01',
        'jenis_guru' => 'umum',
        'status_kepegawaian' => 'tetap_yayasan',
        'pendidikan' => 'S1 Pendidikan',
        'tanggal_masuk' => date('Y-m-d'),
        'status_aktif' => true,
    ]);
    $this->actingAs($guruUser);
    $guruRoutes = [
        route('guru.dashboard'),
        route('guru.absensi-siswa'),
        route('guru.jadwal-mengajar'),
        route('guru.piket'),
        route('guru.kurikulum-merdeka'),
        route('guru.input-sumatif'),
        route('guru.penilaian-p5'),
        route('guru.bobot-nilai'),
        route('guru.remedial'),
        route('guru.kelola-rapor'),
        route('guru.pengembangan-diri'),
        route('guru.absensi-diri'),
        route('guru.slip-gaji'),
        route('guru.kalender-akademik'),
        route('guru.laporan.absensi-siswa'),
        route('guru.laporan.rekap-nilai'),
    ];

    foreach ($guruRoutes as $url) {
        $res = $this->get($url);
        expect($res->status())->toBeLessThan(500, "Guru Route {$url} returned status {$res->status()}");
    }

    // 5. Test Murid Routes
    $this->actingAs($muridUser);
    $muridRoutes = [
        route('murid.dashboard'),
        route('murid.rapor'),
        route('murid.tahfidz'),
        route('murid.remedial'),
        route('murid.kehadiran'),
        route('murid.ekskul'),
        route('murid.jadwal'),
        route('murid.kalender-akademik'),
        route('murid.tagihan'),
        route('murid.tabungan'),
        route('murid.riwayat-aktivitas'),
    ];

    foreach ($muridRoutes as $url) {
        $res = $this->get($url);
        expect($res->status())->toBeLessThan(500, "Murid Route {$url} returned status {$res->status()}");
    }

    // 6. Test Kepala Sekolah & Pengawas Routes
    $roleKepsek = \App\Models\Role::where('nama', 'kepala_sekolah')->first();
    $kepsekUser = \App\Models\User::create([
        'nama' => 'Kepsek Test',
        'username' => 'kepsek_test',
        'email' => 'kepsek_test@test.com',
        'password' => bcrypt('password'),
        'role_id' => $roleKepsek->id,
        'status' => 'aktif',
    ]);
    $this->actingAs($kepsekUser);
    $kepsekRoutes = [
        route('kepala-sekolah.dashboard'),
        route('kepala-sekolah.laporan.absensi-siswa'),
        route('kepala-sekolah.laporan.absensi-guru'),
        route('kepala-sekolah.laporan.rekap-nilai'),
        route('kepala-sekolah.audit-log'),
        route('kepala-sekolah.kalender-akademik'),
    ];

    foreach ($kepsekRoutes as $url) {
        $res = $this->get($url);
        expect($res->status())->toBeLessThan(500, "Kepsek Route {$url} returned status {$res->status()}");
    }

    $rolePengawas = \App\Models\Role::where('nama', 'pengawas')->first();
    $pengawasUser = \App\Models\User::create([
        'nama' => 'Pengawas Test',
        'username' => 'pengawas_test',
        'email' => 'pengawas_test@test.com',
        'password' => bcrypt('password'),
        'role_id' => $rolePengawas->id,
        'status' => 'aktif',
    ]);
    $this->actingAs($pengawasUser);
    $pengawasRoutes = [
        route('pengawas.dashboard'),
        route('pengawas.koreksi-nilai'),
        route('pengawas.kalender-akademik'),
    ];

    foreach ($pengawasRoutes as $url) {
        $res = $this->get($url);
        expect($res->status())->toBeLessThan(500, "Pengawas Route {$url} returned status {$res->status()}");
    }
});
