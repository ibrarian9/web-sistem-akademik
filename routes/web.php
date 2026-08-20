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

// Public Document Verification Routes (QR Code Scan)
Route::get('/verifikasi/dokumen/{uuid}', [\App\Http\Controllers\DocumentVerificationController::class, 'verify'])->name('verifikasi.dokumen.public');
Route::get('/verifikasi-dokumen/{uuid}', [\App\Http\Controllers\DocumentVerificationController::class, 'verify'])->name('verifikasi.dokumen');


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
// Role-based Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/profil', \App\Livewire\Shared\ProfilSaya::class)->name('profil');
    
    // Shared Notifications & Documents Route (accessible by any authenticated user)
    Route::get('/notifikasi', \App\Livewire\Shared\NotificationsList::class)->name('shared.notifications');
    Route::get('/kalender-akademik', \App\Livewire\TataUsaha\ManajemenKalenderAkademik::class)->name('kalender-akademik.shared');
    Route::get('/tutorial-faq', \App\Livewire\Shared\TutorialDanFaq::class)->name('shared.tutorial-faq');
    
    // PDF Rapor Inline Browser Preview
    Route::get('/rapor/pdf-preview/{siswaId}', [\App\Http\Controllers\RaporPdfController::class, 'previewPdf'])->name('rapor.pdf.preview');
    Route::get('/rapor/pdf-tahfidz-preview/{siswaId}', [\App\Http\Controllers\RaporPdfController::class, 'previewTahfidzPdf'])->name('rapor.pdf-tahfidz.preview');


    
    // Multi-role Protected Documents (Authorization Handled in Controller)
    Route::get('/pembayaran/resi/{id}', [\App\Http\Controllers\FinanceReportController::class, 'cetakResi'])->name('pembayaran.resi');
    Route::get('/cetak-resi/{id}', [\App\Http\Controllers\FinanceReportController::class, 'cetakResi'])->name('cetak-resi');
    Route::get('/finance/cetak-resi/{id}', [\App\Http\Controllers\FinanceReportController::class, 'cetakResi'])->name('finance.cetak-resi');
    Route::get('/finance/pembayaran/resi/{id}', [\App\Http\Controllers\FinanceReportController::class, 'cetakResi'])->name('finance.pembayaran.resi');
    Route::get('/gaji-guru/slip/{id}', [\App\Http\Controllers\FinanceReportController::class, 'slipGaji'])->name('gaji-guru.slip');
    Route::get('/finance/gaji-guru/slip/{id}', [\App\Http\Controllers\FinanceReportController::class, 'slipGaji'])->name('finance.gaji-guru.slip');

    // Pengajuan Dana Route — Accessible strictly by Finance only
    Route::middleware(['role:finance'])
        ->get('/finance/pengajuan-dana', \App\Livewire\Finance\PengajuanDanaIndex::class)
        ->name('finance.pengajuan-dana');

    // Super Admin Group — Oversight, Keuangan, User Management, Audit
    Route::middleware(['role:super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
        Route::get('/dashboard', \App\Livewire\SuperAdmin\Dashboard::class)->name('dashboard');
        Route::get('/audit-log', \App\Livewire\SuperAdmin\TataKelola\AuditLog::class)->name('audit-log');
        Route::get('/error-log', \App\Livewire\SuperAdmin\TataKelola\SystemErrorLog::class)->name('error-log');
        Route::get('/user', \App\Livewire\SuperAdmin\TataKelola\ManajemenUser::class)->name('user');
        Route::get('/pengaturan', \App\Livewire\SuperAdmin\TataKelola\ManajemenPengaturan::class)->name('pengaturan');
             // Master Data Oversight
        Route::match(['get', 'post'], '/siswa', \App\Livewire\SuperAdmin\TataKelola\ManajemenSiswa::class)->name('siswa');
        Route::match(['get', 'post'], '/guru', \App\Livewire\SuperAdmin\TataKelola\ManajemenGuru::class)->name('guru');
        Route::match(['get', 'post'], '/capaian-guru', \App\Livewire\SuperAdmin\TataKelola\CapaianPengembanganGuru::class)->name('capaian-guru');
        Route::match(['get', 'post'], '/karyawan', \App\Livewire\TataUsaha\ManajemenKaryawan::class)->name('karyawan');
        Route::match(['get', 'post'], '/kelas', \App\Livewire\SuperAdmin\TataKelola\ManajemenKelas::class)->name('kelas');
        Route::match(['get', 'post'], '/plotting-kelas', \App\Livewire\TataUsaha\PlottingSiswaKelas::class)->name('plotting-kelas');
        Route::match(['get', 'post'], '/surat', \App\Livewire\TataUsaha\ManajemenSurat::class)->name('surat');
        Route::match(['get', 'post'], '/jadwal', \App\Livewire\SuperAdmin\TataKelola\ManajemenJadwal::class)->name('jadwal');
        Route::match(['get', 'post'], '/mapel', \App\Livewire\SuperAdmin\TataKelola\ManajemenMapel::class)->name('mapel');
        Route::match(['get', 'post'], '/komponen-nilai', \App\Livewire\SuperAdmin\TataKelola\ManajemenKomponenNilai::class)->name('komponen-nilai');
        Route::match(['get', 'post'], '/kalender-akademik', \App\Livewire\TataUsaha\ManajemenKalenderAkademik::class)->name('kalender-akademik');
        Route::match(['get', 'post'], '/kenaikan-kelas', \App\Livewire\TataUsaha\ProsesKenaikanKelas::class)->name('kenaikan-kelas');
        
        // Laporan
        Route::get('/laporan/absensi-siswa', \App\Livewire\Shared\Laporan\RekapAbsensiSiswa::class)->name('laporan.absensi-siswa');
        Route::get('/laporan/absensi-guru', \App\Livewire\Shared\Laporan\RekapAbsensiGuru::class)->name('laporan.absensi-guru');
        Route::get('/laporan/rekap-nilai', \App\Livewire\Shared\Laporan\RekapNilai::class)->name('laporan.rekap-nilai');
    });

    // Tata Usaha Group — Data Master, Jadwal, Akademik
    Route::middleware(['role:tata_usaha'])->prefix('tata-usaha')->name('tata-usaha.')->group(function () {
        Route::get('/dashboard', \App\Livewire\TataUsaha\Dashboard::class)->name('dashboard');
        Route::match(['get', 'post'], '/siswa', \App\Livewire\SuperAdmin\TataKelola\ManajemenSiswa::class)->name('siswa');
        Route::match(['get', 'post'], '/guru', \App\Livewire\SuperAdmin\TataKelola\ManajemenGuru::class)->name('guru');
        Route::match(['get', 'post'], '/user', \App\Livewire\SuperAdmin\TataKelola\ManajemenUser::class)->name('user');
        Route::match(['get', 'post'], '/absensi-karyawan', \App\Livewire\TataUsaha\InputAbsensiKaryawan::class)->name('absensi-karyawan');
        Route::match(['get', 'post'], '/karyawan', \App\Livewire\TataUsaha\ManajemenKaryawan::class)->name('karyawan');
        Route::match(['get', 'post'], '/piket', \App\Livewire\TataUsaha\ManajemenPiketGuru::class)->name('piket');
        Route::match(['get', 'post'], '/alumni', \App\Livewire\TataUsaha\DataAlumni::class)->name('alumni');
        Route::match(['get', 'post'], '/kelas', \App\Livewire\SuperAdmin\TataKelola\ManajemenKelas::class)->name('kelas');
        Route::match(['get', 'post'], '/plotting-kelas', \App\Livewire\TataUsaha\PlottingSiswaKelas::class)->name('plotting-kelas');
        Route::match(['get', 'post'], '/surat', \App\Livewire\TataUsaha\ManajemenSurat::class)->name('surat');
        Route::match(['get', 'post'], '/jadwal', \App\Livewire\SuperAdmin\TataKelola\ManajemenJadwal::class)->name('jadwal');
        Route::match(['get', 'post'], '/mapel', \App\Livewire\SuperAdmin\TataKelola\ManajemenMapel::class)->name('mapel');
        Route::get('/komponen-nilai', \App\Livewire\SuperAdmin\TataKelola\ManajemenKomponenNilai::class)->name('komponen-nilai');
        Route::get('/kalender-akademik', \App\Livewire\TataUsaha\ManajemenKalenderAkademik::class)->name('kalender-akademik');
        Route::get('/kenaikan-kelas', \App\Livewire\TataUsaha\ProsesKenaikanKelas::class)->name('kenaikan-kelas');
        
        // Laporan
        Route::get('/laporan/absensi-siswa', \App\Livewire\Shared\Laporan\RekapAbsensiSiswa::class)->name('laporan.absensi-siswa');
        Route::get('/laporan/absensi-guru', \App\Livewire\Shared\Laporan\RekapAbsensiGuru::class)->name('laporan.absensi-guru');
        Route::get('/laporan/rekap-nilai', \App\Livewire\Shared\Laporan\RekapNilai::class)->name('laporan.rekap-nilai');
    });

    // Pengawas Group (Renamed from Koordinator)
    Route::middleware(['role:pengawas,koordinator,super_admin'])->prefix('pengawas')->name('pengawas.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Pengawas\ManajemenKoreksiNilai::class)->name('dashboard');
        Route::get('/koreksi-nilai', \App\Livewire\Pengawas\ManajemenKoreksiNilai::class)->name('koreksi-nilai');
        Route::get('/kalender-akademik', \App\Livewire\TataUsaha\ManajemenKalenderAkademik::class)->name('kalender-akademik');
    });

    // Alias legacy Koordinator routes to Pengawas dashboard
    Route::middleware(['role:pengawas,koordinator,super_admin'])->prefix('koordinator')->name('koordinator.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Pengawas\ManajemenKoreksiNilai::class)->name('dashboard');
        Route::get('/koreksi-nilai', \App\Livewire\Pengawas\ManajemenKoreksiNilai::class)->name('koreksi-nilai');
        Route::get('/kalender-akademik', \App\Livewire\TataUsaha\ManajemenKalenderAkademik::class)->name('kalender-akademik');
    });

    // Kepala Sekolah Group
    Route::middleware(['role:kepala_sekolah,super_admin'])->prefix('kepala-sekolah')->name('kepala-sekolah.')->group(function () {
        Route::get('/dashboard', \App\Livewire\KepalaSekolah\Dashboard::class)->name('dashboard');
        Route::get('/audit-log', \App\Livewire\SuperAdmin\TataKelola\AuditLog::class)->name('audit-log');
        Route::get('/kalender-akademik', \App\Livewire\TataUsaha\ManajemenKalenderAkademik::class)->name('kalender-akademik');
        
        // Laporan Monitoring
        Route::get('/laporan/absensi-siswa', \App\Livewire\Shared\Laporan\RekapAbsensiSiswa::class)->name('laporan.absensi-siswa');
        Route::get('/laporan/absensi-guru', \App\Livewire\Shared\Laporan\RekapAbsensiGuru::class)->name('laporan.absensi-guru');
        Route::get('/laporan/rekap-nilai', \App\Livewire\Shared\Laporan\RekapNilai::class)->name('laporan.rekap-nilai');
    });

    // Guru Group
    Route::middleware(['role:guru'])->prefix('guru')->name('guru.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Guru\Dashboard::class)->name('dashboard');
        Route::get('/kurikulum-merdeka', \App\Livewire\Guru\ManajemenKurikulumMerdeka::class)->name('kurikulum-merdeka');
        Route::get('/input-sumatif', \App\Livewire\Guru\InputNilaiSumatif::class)->name('input-sumatif');
        Route::get('/input-tahfidz', \App\Livewire\Guru\InputNilaiTahfidz::class)->name('input-tahfidz');
        Route::get('/penilaian-p5', \App\Livewire\Guru\PenilaianP5::class)->name('penilaian-p5');

        Route::get('/input-nilai', \App\Livewire\Guru\InputNilaiSiswa::class)->name('input-nilai');

        Route::get('/bobot-nilai', \App\Livewire\Guru\PengaturanBobotNilai::class)->name('bobot-nilai');
        Route::get('/absensi-siswa', \App\Livewire\Guru\AbsensiSiswa::class)->name('absensi-siswa');
        Route::get('/absensi-diri', \App\Livewire\Guru\AbsensiDiri::class)->name('absensi-diri');
        Route::get('/jadwal-mengajar', \App\Livewire\Guru\JadwalMengajar::class)->name('jadwal-mengajar');
        Route::get('/piket', \App\Livewire\TataUsaha\ManajemenPiketGuru::class)->name('piket');
        Route::get('/kelola-rapor', \App\Livewire\Guru\KelolaRapor::class)->name('kelola-rapor');
        Route::get('/remedial', \App\Livewire\Guru\ManajemenRemedial::class)->name('remedial');
        Route::get('/pengembangan-diri', \App\Livewire\Guru\CapaianPengembanganDiri::class)->name('pengembangan-diri');
        Route::get('/kalender-akademik', \App\Livewire\TataUsaha\ManajemenKalenderAkademik::class)->name('kalender-akademik');
        
        // Laporan
        Route::get('/laporan/absensi-siswa', \App\Livewire\Shared\Laporan\RekapAbsensiSiswa::class)->name('laporan.absensi-siswa');
        Route::get('/laporan/rekap-nilai', \App\Livewire\Shared\Laporan\RekapNilai::class)->name('laporan.rekap-nilai');
    });

    // Murid Group
    Route::middleware(['role:murid'])->prefix('murid')->name('murid.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Murid\Dashboard::class)->name('dashboard');
        Route::get('/rapor', \App\Livewire\Murid\RaporNilai::class)->name('rapor');
        Route::get('/tahfidz', \App\Livewire\Murid\SetoranTahfidz::class)->name('tahfidz');
        Route::get('/remedial', \App\Livewire\Murid\JadwalRemedial::class)->name('remedial');
        Route::get('/kehadiran', \App\Livewire\Murid\KehadiranSaya::class)->name('kehadiran');

        Route::get('/ekskul', \App\Livewire\Murid\EkstrakurikulerSaya::class)->name('ekskul');
        Route::get('/jadwal', \App\Livewire\Murid\JadwalPelajaran::class)->name('jadwal');
        Route::get('/tagihan', \App\Livewire\Murid\TagihanSpp::class)->name('tagihan');
        Route::get('/tabungan', \App\Livewire\Murid\TabunganSaya::class)->name('tabungan');
        Route::get('/riwayat-aktivitas', \App\Livewire\Murid\RiwayatAktivitas::class)->name('riwayat-aktivitas');
        Route::get('/kalender-akademik', \App\Livewire\TataUsaha\ManajemenKalenderAkademik::class)->name('kalender-akademik');
    });

    // Finance Group — Accessible by Finance, Super Admin, & Kepala Sekolah (for Monitoring)
    Route::middleware(['role:finance,super_admin,kepala_sekolah'])->prefix('finance')->name('finance.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Finance\Dashboard::class)->name('dashboard');
        Route::get('/overview-pembayaran', \App\Livewire\Finance\OverviewPembayaran::class)->name('overview-pembayaran');
        Route::get('/tagihan', \App\Livewire\Finance\ManajemenTagihan::class)->name('tagihan');
        Route::get('/tabungan', \App\Livewire\Finance\TabunganSiswa::class)->name('tabungan');
        Route::get('/input-pembayaran', \App\Livewire\Finance\InputPembayaran::class)->name('input-pembayaran');
        Route::get('/arus-masuk', \App\Livewire\Finance\ArusMasuk::class)->name('arus-masuk');
        Route::get('/arus-kas-masuk', \App\Livewire\Finance\ArusKasMasuk::class)->name('arus-kas-masuk');
        Route::get('/arus-kas-keluar', \App\Livewire\Finance\ArusKasKeluar::class)->name('arus-kas-keluar');
        Route::get('/arus-kas', \App\Livewire\Finance\ArusKasKeluar::class)->name('arus-kas');
        Route::get('/dana-bos', \App\Livewire\Finance\DanaBos::class)->name('dana-bos');

        // Peminjaman & Gaji Guru
        Route::get('/peminjaman', \App\Livewire\Finance\ManajemenPeminjaman::class)->name('peminjaman');
        Route::get('/gaji-guru', \App\Livewire\Finance\ManajemenGajiGuru::class)->name('gaji-guru');

        // Laporan Keuangan
        Route::get('/laporan/tunggakan', \App\Livewire\Finance\Laporan\LaporanTunggakan::class)->name('laporan.tunggakan');
        Route::get('/laporan/pemasukan', \App\Livewire\Finance\Laporan\LaporanPemasukan::class)->name('laporan.pemasukan');
        Route::get('/laporan/pengeluaran', \App\Livewire\Finance\Laporan\LaporanPengeluaran::class)->name('laporan.pengeluaran');

        // Excel / CSV Exports
        Route::get('/export/tunggakan', [\App\Http\Controllers\FinanceExportController::class, 'exportTunggakan'])->name('export.tunggakan');
        Route::get('/export/pemasukan', [\App\Http\Controllers\FinanceExportController::class, 'exportPemasukan'])->name('export.pemasukan');
        Route::get('/export/pengeluaran', [\App\Http\Controllers\FinanceExportController::class, 'exportPengeluaran'])->name('export.pengeluaran');
    });

});
