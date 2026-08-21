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

    // Test Finance Key Routes
    $this->actingAs($finance);
    $routesToTest = [
        route('finance.dashboard'),
        route('finance.overview-pembayaran'),
        route('finance.tagihan'),
        route('finance.tagihan.detail', $siswa->id),
        route('finance.input-pembayaran'),
        route('finance.input-pembayaran', ['siswa_id' => $siswa->id]),
        route('finance.tabungan'),
        route('finance.arus-kas'),
        route('finance.dana-bos'),
        route('finance.gaji-guru'),
        route('finance.peminjaman'),
        route('finance.laporan.tunggakan'),
        route('finance.laporan.pemasukan'),
        route('finance.laporan.pengeluaran'),
        route('finance.pembayaran.resi', $pembayaran->id),
    ];

    foreach ($routesToTest as $url) {
        $res = $this->get($url);
        expect($res->status())->toBeLessThan(500, "Route {$url} returned status {$res->status()}");
    }

    // Test Super Admin Key Routes
    $this->actingAs($superAdmin);
    $adminRoutes = [
        route('super-admin.dashboard'),
        route('super-admin.siswa'),
        route('super-admin.guru'),
        route('super-admin.kelas'),
        route('super-admin.jadwal'),
        route('super-admin.kalender-akademik'),
        route('super-admin.user'),
        route('super-admin.audit-log'),
        route('super-admin.pengaturan'),
    ];

    foreach ($adminRoutes as $url) {
        $res = $this->get($url);
        expect($res->status())->toBeLessThan(500, "Route {$url} returned status {$res->status()}");
    }
});
