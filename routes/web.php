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
    
    // Super Admin Group
    Route::middleware(['role:super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
        Route::get('/dashboard', \App\Livewire\SuperAdmin\Dashboard::class)->name('dashboard');
        Route::get('/siswa', \App\Livewire\SuperAdmin\TataKelola\ManajemenSiswa::class)->name('siswa');
        Route::get('/guru', \App\Livewire\SuperAdmin\TataKelola\ManajemenGuru::class)->name('guru');
        Route::get('/kelas', \App\Livewire\SuperAdmin\TataKelola\ManajemenKelas::class)->name('kelas');
        Route::get('/jadwal', \App\Livewire\SuperAdmin\TataKelola\ManajemenJadwal::class)->name('jadwal');
        Route::get('/mapel', \App\Livewire\SuperAdmin\TataKelola\ManajemenMapel::class)->name('mapel');
        Route::get('/komponen-nilai', \App\Livewire\SuperAdmin\TataKelola\ManajemenKomponenNilai::class)->name('komponen-nilai');
        Route::get('/audit-log', \App\Livewire\SuperAdmin\TataKelola\AuditLog::class)->name('audit-log');
        Route::get('/user', \App\Livewire\SuperAdmin\TataKelola\ManajemenUser::class)->name('user');
        Route::get('/pengaturan', \App\Livewire\SuperAdmin\TataKelola\ManajemenPengaturan::class)->name('pengaturan');
    });

    // Guru Group
    Route::middleware(['role:guru'])->prefix('guru')->name('guru.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Guru\Dashboard::class)->name('dashboard');
        Route::get('/input-nilai', \App\Livewire\Guru\InputNilaiSiswa::class)->name('input-nilai');
        Route::get('/absensi-siswa', \App\Livewire\Guru\AbsensiSiswa::class)->name('absensi-siswa');
        Route::get('/absensi-diri', \App\Livewire\Guru\AbsensiDiri::class)->name('absensi-diri');
        Route::get('/jadwal-mengajar', \App\Livewire\Guru\JadwalMengajar::class)->name('jadwal-mengajar');
    });

    // Murid Group
    Route::middleware(['role:murid'])->prefix('murid')->name('murid.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Murid\Dashboard::class)->name('dashboard');
        Route::get('/rapor', \App\Livewire\Murid\RaporNilai::class)->name('rapor');
        Route::get('/kehadiran', \App\Livewire\Murid\KehadiranSaya::class)->name('kehadiran');
        Route::get('/jadwal', \App\Livewire\Murid\JadwalPelajaran::class)->name('jadwal');
        Route::get('/tagihan', \App\Livewire\Murid\TagihanSpp::class)->name('tagihan');
    });

    // Finance Group
    Route::middleware(['role:finance'])->prefix('finance')->name('finance.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Finance\Dashboard::class)->name('dashboard');
        Route::get('/tagihan', \App\Livewire\Finance\ManajemenTagihan::class)->name('tagihan');
        Route::get('/input-pembayaran', \App\Livewire\Finance\InputPembayaran::class)->name('input-pembayaran');
        Route::get('/arus-kas', \App\Livewire\Finance\ArusKas::class)->name('arus-kas');
        Route::get('/dana-bos', \App\Livewire\Finance\DanaBos::class)->name('dana-bos');
    });

});
