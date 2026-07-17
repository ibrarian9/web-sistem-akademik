<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Auth;

// Public & Auth checks
Route::get('/', function () {
    if (Auth::check()) {
        $role = Auth::user()->role->nama ?? '';
        return match ($role) {
            'super_admin' => redirect()->route('super-admin.dashboard'),
            'tata_usaha' => redirect()->route('tata-usaha.dashboard'),
            'guru' => redirect()->route('guru.dashboard'),
            'murid' => redirect()->route('murid.dashboard'),
            'finance' => redirect()->route('finance.dashboard'),
            default => redirect()->route('login'),
        };
    }
    return redirect()->route('login');
});

// Guest-only Login Route
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

// Logout Route
Route::post('/logout', function (\Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// Role-based Protected Routes
Route::middleware(['auth'])->group(function () {
    
    // Shared Notifications Route (accessible by any authenticated user)
    Route::get('/notifikasi', \App\Livewire\Shared\NotificationsList::class)->name('shared.notifications');
    
    // Super Admin Group — Oversight, Keuangan, User Management, Audit
    Route::middleware(['role:super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
        Route::get('/dashboard', \App\Livewire\SuperAdmin\Dashboard::class)->name('dashboard');
        Route::get('/audit-log', \App\Livewire\SuperAdmin\TataKelola\AuditLog::class)->name('audit-log');
        Route::get('/user', \App\Livewire\SuperAdmin\TataKelola\ManajemenUser::class)->name('user');
        Route::get('/pengaturan', \App\Livewire\SuperAdmin\TataKelola\ManajemenPengaturan::class)->name('pengaturan');
        
        // Master Data Oversight
        Route::get('/siswa', \App\Livewire\SuperAdmin\TataKelola\ManajemenSiswa::class)->name('siswa');
        Route::get('/guru', \App\Livewire\SuperAdmin\TataKelola\ManajemenGuru::class)->name('guru');
        Route::get('/kelas', \App\Livewire\SuperAdmin\TataKelola\ManajemenKelas::class)->name('kelas');
        Route::get('/jadwal', \App\Livewire\SuperAdmin\TataKelola\ManajemenJadwal::class)->name('jadwal');
        Route::get('/mapel', \App\Livewire\SuperAdmin\TataKelola\ManajemenMapel::class)->name('mapel');
        Route::get('/komponen-nilai', \App\Livewire\SuperAdmin\TataKelola\ManajemenKomponenNilai::class)->name('komponen-nilai');
        
        // Laporan
        Route::get('/laporan/absensi-siswa', \App\Livewire\Shared\Laporan\RekapAbsensiSiswa::class)->name('laporan.absensi-siswa');
        Route::get('/laporan/absensi-guru', \App\Livewire\Shared\Laporan\RekapAbsensiGuru::class)->name('laporan.absensi-guru');
        Route::get('/laporan/rekap-nilai', \App\Livewire\Shared\Laporan\RekapNilai::class)->name('laporan.rekap-nilai');
    });

    // Tata Usaha Group — Data Master, Jadwal, Akademik
    Route::middleware(['role:tata_usaha'])->prefix('tata-usaha')->name('tata-usaha.')->group(function () {
        Route::get('/dashboard', \App\Livewire\TataUsaha\Dashboard::class)->name('dashboard');
        Route::get('/siswa', \App\Livewire\SuperAdmin\TataKelola\ManajemenSiswa::class)->name('siswa');
        Route::get('/guru', \App\Livewire\SuperAdmin\TataKelola\ManajemenGuru::class)->name('guru');
        Route::get('/kelas', \App\Livewire\SuperAdmin\TataKelola\ManajemenKelas::class)->name('kelas');
        Route::get('/jadwal', \App\Livewire\SuperAdmin\TataKelola\ManajemenJadwal::class)->name('jadwal');
        Route::get('/mapel', \App\Livewire\SuperAdmin\TataKelola\ManajemenMapel::class)->name('mapel');
        Route::get('/komponen-nilai', \App\Livewire\SuperAdmin\TataKelola\ManajemenKomponenNilai::class)->name('komponen-nilai');
        
        // Laporan
        Route::get('/laporan/absensi-siswa', \App\Livewire\Shared\Laporan\RekapAbsensiSiswa::class)->name('laporan.absensi-siswa');
        Route::get('/laporan/absensi-guru', \App\Livewire\Shared\Laporan\RekapAbsensiGuru::class)->name('laporan.absensi-guru');
        Route::get('/laporan/rekap-nilai', \App\Livewire\Shared\Laporan\RekapNilai::class)->name('laporan.rekap-nilai');
    });

    // Guru Group
    Route::middleware(['role:guru'])->prefix('guru')->name('guru.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Guru\Dashboard::class)->name('dashboard');
        Route::get('/input-nilai', \App\Livewire\Guru\InputNilaiSiswa::class)->name('input-nilai');
        Route::get('/absensi-siswa', \App\Livewire\Guru\AbsensiSiswa::class)->name('absensi-siswa');
        Route::get('/absensi-diri', \App\Livewire\Guru\AbsensiDiri::class)->name('absensi-diri');
        Route::get('/jadwal-mengajar', \App\Livewire\Guru\JadwalMengajar::class)->name('jadwal-mengajar');
        Route::get('/kelola-rapor', \App\Livewire\Guru\KelolaRapor::class)->name('kelola-rapor');
        
        // Laporan
        Route::get('/laporan/absensi-siswa', \App\Livewire\Shared\Laporan\RekapAbsensiSiswa::class)->name('laporan.absensi-siswa');
        Route::get('/laporan/rekap-nilai', \App\Livewire\Shared\Laporan\RekapNilai::class)->name('laporan.rekap-nilai');
    });

    // Murid Group
    Route::middleware(['role:murid'])->prefix('murid')->name('murid.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Murid\Dashboard::class)->name('dashboard');
        Route::get('/rapor', \App\Livewire\Murid\RaporNilai::class)->name('rapor');
        Route::get('/kehadiran', \App\Livewire\Murid\KehadiranSaya::class)->name('kehadiran');
        Route::get('/jadwal', \App\Livewire\Murid\JadwalPelajaran::class)->name('jadwal');
        Route::get('/tagihan', \App\Livewire\Murid\TagihanSpp::class)->name('tagihan');
        Route::get('/riwayat-aktivitas', \App\Livewire\Murid\RiwayatAktivitas::class)->name('riwayat-aktivitas');
    });

    // Finance Group
    Route::middleware(['role:finance,super_admin'])->prefix('finance')->name('finance.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Finance\Dashboard::class)->name('dashboard');
        Route::get('/overview-pembayaran', \App\Livewire\Finance\OverviewPembayaran::class)->name('overview-pembayaran');
        Route::get('/tagihan', \App\Livewire\Finance\ManajemenTagihan::class)->name('tagihan');
        Route::get('/input-pembayaran', \App\Livewire\Finance\InputPembayaran::class)->name('input-pembayaran');
        Route::get('/arus-kas', \App\Livewire\Finance\ArusKas::class)->name('arus-kas');
        Route::get('/dana-bos', \App\Livewire\Finance\DanaBos::class)->name('dana-bos');

        // Stage 2: Peminjaman & Gaji Guru
        Route::get('/peminjaman', \App\Livewire\Finance\ManajemenPeminjaman::class)->name('peminjaman');
        Route::get('/gaji-guru', \App\Livewire\Finance\ManajemenGajiGuru::class)->name('gaji-guru');
        Route::get('/gaji-guru/slip/{id}', [\App\Http\Controllers\FinanceReportController::class, 'slipGaji'])->name('gaji-guru.slip');

        // Stage 2: Laporan Keuangan
        Route::get('/laporan/tunggakan', \App\Livewire\Finance\Laporan\LaporanTunggakan::class)->name('laporan.tunggakan');
        Route::get('/laporan/pemasukan', \App\Livewire\Finance\Laporan\LaporanPemasukan::class)->name('laporan.pemasukan');
        Route::get('/laporan/pengeluaran', \App\Livewire\Finance\Laporan\LaporanPengeluaran::class)->name('laporan.pengeluaran');
    });

});
