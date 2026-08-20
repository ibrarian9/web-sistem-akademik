@php
    $role = auth()->user()->role->nama ?? '';
    $userGuru = auth()->user()->guru;
    $jenisGuru = strtolower($userGuru->jenis_guru ?? 'umum');
    if ($jenisGuru === 'tahfidz') {
        $jenisGuru = 'tahfizh';
    }

    $guruMenuItems = match ($jenisGuru) {
        'tahfizh' => [
            ['title' => 'Dashboard Guru', 'route' => 'guru.dashboard', 'icon' => 'home'],
            ['title' => 'Panduan & FAQ', 'route' => 'shared.tutorial-faq', 'icon' => 'help-circle'],
            
            ['title' => 'KBM & Presensi', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Absensi Siswa', 'route' => 'guru.absensi-siswa', 'icon' => 'check-square'],
            ['title' => 'Jadwal Mengajar', 'route' => 'guru.jadwal-mengajar', 'icon' => 'calendar'],
            ['title' => 'Jadwal Piket', 'route' => 'guru.piket', 'icon' => 'shield-check'],

            ['title' => 'Setoran & Tahfizh', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Setoran Tahfizh', 'route' => 'guru.input-tahfidz', 'icon' => 'award'],

            ['title' => 'Rapor & Bimbingan', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Cetak & Kelola Rapor', 'route' => 'guru.kelola-rapor', 'icon' => 'book-open'],
            ['title' => 'Pengembangan Diri', 'route' => 'guru.pengembangan-diri', 'icon' => 'user-check'],

            ['title' => 'Presensi Saya & Info', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Presensi Guru Mandiri', 'route' => 'guru.absensi-diri', 'icon' => 'clock'],
            ['title' => 'Kalender Akademik', 'route' => 'guru.kalender-akademik', 'icon' => 'calendar'],

            ['title' => 'Laporan', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Rekap Absensi Siswa', 'route' => 'guru.laporan.absensi-siswa', 'icon' => 'file-text'],
            ['title' => 'Rekap Nilai Kelas', 'route' => 'guru.laporan.rekap-nilai', 'icon' => 'bar-chart-2'],

            ['title' => 'Lainnya', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Notifikasi', 'route' => 'shared.notifications', 'icon' => 'bell'],
        ],
        'umum' => [
            ['title' => 'Dashboard Guru', 'route' => 'guru.dashboard', 'icon' => 'home'],
            ['title' => 'Panduan & FAQ', 'route' => 'shared.tutorial-faq', 'icon' => 'help-circle'],

            ['title' => 'KBM & Presensi', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Absensi Siswa', 'route' => 'guru.absensi-siswa', 'icon' => 'check-square'],
            ['title' => 'Jadwal Mengajar', 'route' => 'guru.jadwal-mengajar', 'icon' => 'calendar'],
            ['title' => 'Jadwal Piket', 'route' => 'guru.piket', 'icon' => 'shield-check'],

            ['title' => 'Penilaian & Kurikulum', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Setup Bab & TP', 'route' => 'guru.kurikulum-merdeka', 'icon' => 'layers'],
            ['title' => 'Nilai Sumatif TP & SAS', 'route' => 'guru.input-sumatif', 'icon' => 'edit-3'],
            ['title' => 'Penilaian Kokurikuler P5', 'route' => 'guru.penilaian-p5', 'icon' => 'star'],
            ['title' => 'Bobot & Formula Nilai', 'route' => 'guru.bobot-nilai', 'icon' => 'sliders'],
            ['title' => 'Jadwal Remedial', 'route' => 'guru.remedial', 'icon' => 'refresh-cw'],
            ['title' => 'Nilai KTSP (Arsip)', 'route' => 'guru.input-nilai', 'icon' => 'clipboard'],

            ['title' => 'Rapor & Wali Kelas', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Cetak & Kelola Rapor', 'route' => 'guru.kelola-rapor', 'icon' => 'book-open'],
            ['title' => 'Pengembangan Diri', 'route' => 'guru.pengembangan-diri', 'icon' => 'user-check'],

            ['title' => 'Presensi Saya & Info', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Presensi Guru Mandiri', 'route' => 'guru.absensi-diri', 'icon' => 'clock'],
            ['title' => 'Kalender Akademik', 'route' => 'guru.kalender-akademik', 'icon' => 'calendar'],

            ['title' => 'Laporan & Rekap', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Rekap Absensi Siswa', 'route' => 'guru.laporan.absensi-siswa', 'icon' => 'file-text'],
            ['title' => 'Rekap Nilai Kelas', 'route' => 'guru.laporan.rekap-nilai', 'icon' => 'bar-chart-2'],

            ['title' => 'Lainnya', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Notifikasi', 'route' => 'shared.notifications', 'icon' => 'bell'],
        ],
        default => [
            ['title' => 'Dashboard Guru', 'route' => 'guru.dashboard', 'icon' => 'home'],
            ['title' => 'Panduan & FAQ', 'route' => 'shared.tutorial-faq', 'icon' => 'help-circle'],

            ['title' => 'KBM & Presensi', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Absensi Siswa', 'route' => 'guru.absensi-siswa', 'icon' => 'check-square'],
            ['title' => 'Jadwal Mengajar', 'route' => 'guru.jadwal-mengajar', 'icon' => 'calendar'],
            ['title' => 'Jadwal Piket', 'route' => 'guru.piket', 'icon' => 'shield-check'],

            ['title' => 'Penilaian & Tahfizh', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Setup Bab & TP', 'route' => 'guru.kurikulum-merdeka', 'icon' => 'layers'],
            ['title' => 'Nilai Sumatif TP & SAS', 'route' => 'guru.input-sumatif', 'icon' => 'edit-3'],
            ['title' => 'Setoran Tahfizh', 'route' => 'guru.input-tahfidz', 'icon' => 'award'],
            ['title' => 'Penilaian Kokurikuler P5', 'route' => 'guru.penilaian-p5', 'icon' => 'star'],
            ['title' => 'Bobot & Formula Nilai', 'route' => 'guru.bobot-nilai', 'icon' => 'sliders'],
            ['title' => 'Jadwal Remedial', 'route' => 'guru.remedial', 'icon' => 'refresh-cw'],
            ['title' => 'Nilai KTSP (Arsip)', 'route' => 'guru.input-nilai', 'icon' => 'clipboard'],

            ['title' => 'Rapor & Wali Kelas', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Cetak & Kelola Rapor', 'route' => 'guru.kelola-rapor', 'icon' => 'book-open'],
            ['title' => 'Pengembangan Diri', 'route' => 'guru.pengembangan-diri', 'icon' => 'user-check'],

            ['title' => 'Presensi Saya & Info', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Presensi Guru Mandiri', 'route' => 'guru.absensi-diri', 'icon' => 'clock'],
            ['title' => 'Kalender Akademik', 'route' => 'guru.kalender-akademik', 'icon' => 'calendar'],

            ['title' => 'Laporan & Rekap', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Rekap Absensi Siswa', 'route' => 'guru.laporan.absensi-siswa', 'icon' => 'file-text'],
            ['title' => 'Rekap Nilai Kelas', 'route' => 'guru.laporan.rekap-nilai', 'icon' => 'bar-chart-2'],

            ['title' => 'Lainnya', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Notifikasi', 'route' => 'shared.notifications', 'icon' => 'bell'],
        ],
    };
    
    // Define navigation items based on role
    $menuItems = match ($role) {
        'super_admin' => [
            ['title' => 'Dashboard', 'route' => 'super-admin.dashboard', 'icon' => 'home'],
            ['title' => 'Panduan & FAQ', 'route' => 'shared.tutorial-faq', 'icon' => 'help-circle'],
            ['title' => 'Keuangan', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Overview Pembayaran', 'route' => 'finance.overview-pembayaran', 'icon' => 'eye'],
            ['title' => 'Kelola Tagihan', 'route' => 'finance.tagihan', 'icon' => 'file-text'],
            ['title' => 'Tabungan Siswa', 'route' => 'finance.tabungan', 'icon' => 'wallet'],
            ['title' => 'Input Pembayaran', 'route' => 'finance.input-pembayaran', 'icon' => 'plus-circle'],
            ['title' => 'Kas Masuk Yayasan', 'route' => 'finance.arus-kas-masuk', 'icon' => 'heart-handshake'],
            ['title' => 'Kas Keluar Yayasan', 'route' => 'finance.arus-kas-keluar', 'icon' => 'trending-down'],
            ['title' => 'Gaji Guru', 'route' => 'finance.gaji-guru', 'icon' => 'wallet'],
            ['title' => 'Dana BOS (Masuk & Keluar)', 'route' => 'finance.dana-bos', 'icon' => 'box'],
            ['title' => 'Kurikulum & Rapor', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Setup Bab & TP', 'route' => 'guru.kurikulum-merdeka', 'icon' => 'layers'],
            ['title' => 'Nilai Sumatif', 'route' => 'guru.input-sumatif', 'icon' => 'edit-3'],
            ['title' => 'Setoran Tahfizh', 'route' => 'guru.input-tahfidz', 'icon' => 'award'],
            ['title' => 'Penilaian P5', 'route' => 'guru.penilaian-p5', 'icon' => 'star'],
            ['title' => 'Rapor Murid', 'route' => 'guru.kelola-rapor', 'icon' => 'book-open'],
            ['title' => 'Data Master & SDM', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Data Siswa', 'route' => 'super-admin.siswa', 'icon' => 'users'],
            ['title' => 'Data Guru', 'route' => 'super-admin.guru', 'icon' => 'user-check'],
            ['title' => 'Capaian & Evaluasi Guru', 'route' => 'super-admin.capaian-guru', 'icon' => 'award'],
            ['title' => 'Direktori Karyawan', 'route' => 'super-admin.karyawan', 'icon' => 'users'],
            ['title' => 'Kelas & Mapel', 'route' => 'super-admin.kelas', 'icon' => 'layers'],
            ['title' => 'Plotting Siswa Kelas', 'route' => 'super-admin.plotting-kelas', 'icon' => 'users'],
            ['title' => 'Layanan Persuratan', 'route' => 'super-admin.surat', 'icon' => 'file-text'],
            ['title' => 'Jadwal Pelajaran', 'route' => 'super-admin.jadwal', 'icon' => 'calendar'],
            ['title' => 'Kalender Akademik', 'route' => 'super-admin.kalender-akademik', 'icon' => 'calendar'],
            ['title' => 'Kenaikan Kelas', 'route' => 'super-admin.kenaikan-kelas', 'icon' => 'user-check'],
            ['title' => 'Laporan', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Laporan Absensi Siswa', 'route' => 'super-admin.laporan.absensi-siswa', 'icon' => 'file-text'],
            ['title' => 'Laporan Absensi Guru', 'route' => 'super-admin.laporan.absensi-guru', 'icon' => 'clipboard'],
            ['title' => 'Laporan Rekap Nilai', 'route' => 'super-admin.laporan.rekap-nilai', 'icon' => 'award'],
            ['title' => 'Manajemen System', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Manajemen User', 'route' => 'super-admin.user', 'icon' => 'users'],
            ['title' => 'Audit Log', 'route' => 'super-admin.audit-log', 'icon' => 'activity'],
            ['title' => 'Log Error Sistem', 'route' => 'super-admin.error-log', 'icon' => 'alert-triangle'],
            ['title' => 'Pengaturan Sistem', 'route' => 'super-admin.pengaturan', 'icon' => 'settings'],
            ['title' => 'Lainnya', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Notifikasi', 'route' => 'shared.notifications', 'icon' => 'bell'],
        ],
        'tata_usaha' => [
            ['title' => 'Dashboard', 'route' => 'tata-usaha.dashboard', 'icon' => 'home'],
            ['title' => 'Panduan & FAQ', 'route' => 'shared.tutorial-faq', 'icon' => 'help-circle'],
            ['title' => 'Manajemen SDM & Presensi', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Input Absensi Karyawan', 'route' => 'tata-usaha.absensi-karyawan', 'icon' => 'clipboard'],
            ['title' => 'Direktori Karyawan', 'route' => 'tata-usaha.karyawan', 'icon' => 'users'],
            ['title' => 'Jadwal Piket Guru', 'route' => 'tata-usaha.piket', 'icon' => 'clock'],
            ['title' => 'Manajemen Akun Staff', 'route' => 'tata-usaha.user', 'icon' => 'users'],
            ['title' => 'Data Master', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Data Siswa', 'route' => 'tata-usaha.siswa', 'icon' => 'users'],
            ['title' => 'Data Alumni', 'route' => 'tata-usaha.alumni', 'icon' => 'award'],
            ['title' => 'Data Guru', 'route' => 'tata-usaha.guru', 'icon' => 'user-check'],
            ['title' => 'Kelas & Mapel', 'route' => 'tata-usaha.kelas', 'icon' => 'layers'],
            ['title' => 'Plotting Siswa Kelas', 'route' => 'tata-usaha.plotting-kelas', 'icon' => 'users'],
            ['title' => 'Layanan Persuratan', 'route' => 'tata-usaha.surat', 'icon' => 'file-text'],
            ['title' => 'Jadwal & Akademik', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Jadwal Pelajaran', 'route' => 'tata-usaha.jadwal', 'icon' => 'calendar'],
            ['title' => 'Kalender & Libur', 'route' => 'tata-usaha.kalender-akademik', 'icon' => 'calendar'],
            ['title' => 'Kenaikan Kelas', 'route' => 'tata-usaha.kenaikan-kelas', 'icon' => 'user-check'],
            ['title' => 'Komponen Nilai', 'route' => 'tata-usaha.komponen-nilai', 'icon' => 'sliders'],
            ['title' => 'Laporan', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Laporan Absensi Siswa', 'route' => 'tata-usaha.laporan.absensi-siswa', 'icon' => 'file-text'],
            ['title' => 'Rekap Absensi Guru', 'route' => 'tata-usaha.laporan.absensi-guru', 'icon' => 'clipboard'],
            ['title' => 'Laporan Rekap Nilai', 'route' => 'tata-usaha.laporan.rekap-nilai', 'icon' => 'award'],
            ['title' => 'Lainnya', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Notifikasi', 'route' => 'shared.notifications', 'icon' => 'bell'],
        ],
        'pengawas' => [
            ['title' => 'Dashboard', 'route' => 'pengawas.dashboard', 'icon' => 'home'],
            ['title' => 'Panduan & FAQ', 'route' => 'shared.tutorial-faq', 'icon' => 'help-circle'],
            ['title' => 'Akademik', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Persetujuan Nilai', 'route' => 'pengawas.koreksi-nilai', 'icon' => 'user-check'],
            ['title' => 'Kalender Akademik', 'route' => 'pengawas.kalender-akademik', 'icon' => 'calendar'],
            ['title' => 'Notifikasi', 'route' => 'shared.notifications', 'icon' => 'bell'],
        ],
        'kepala_sekolah' => [
            ['title' => 'Dashboard', 'route' => 'kepala-sekolah.dashboard', 'icon' => 'home'],
            ['title' => 'Panduan & FAQ', 'route' => 'shared.tutorial-faq', 'icon' => 'help-circle'],
            ['title' => 'Monitoring & Executive', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Overview Keuangan', 'route' => 'finance.overview-pembayaran', 'icon' => 'eye'],
            ['title' => 'Kas Masuk Yayasan', 'route' => 'finance.arus-kas-masuk', 'icon' => 'heart-handshake'],
            ['title' => 'Kas Keluar Yayasan', 'route' => 'finance.arus-kas-keluar', 'icon' => 'trending-down'],
            ['title' => 'Dana BOS (Masuk & Keluar)', 'route' => 'finance.dana-bos', 'icon' => 'box'],
            ['title' => 'Laporan Tunggakan', 'route' => 'finance.laporan.tunggakan', 'icon' => 'file-text'],
            ['title' => 'Laporan Pemasukan', 'route' => 'finance.laporan.pemasukan', 'icon' => 'activity'],
            ['title' => 'Laporan Pengeluaran', 'route' => 'finance.laporan.pengeluaran', 'icon' => 'trending-down'],
            ['title' => 'Laporan & Audit', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Laporan Absensi Siswa', 'route' => 'kepala-sekolah.laporan.absensi-siswa', 'icon' => 'file-text'],
            ['title' => 'Rekap Absensi Guru', 'route' => 'kepala-sekolah.laporan.absensi-guru', 'icon' => 'clipboard'],
            ['title' => 'Laporan Rekap Nilai', 'route' => 'kepala-sekolah.laporan.rekap-nilai', 'icon' => 'award'],
            ['title' => 'Audit Log Sistem', 'route' => 'kepala-sekolah.audit-log', 'icon' => 'activity'],
            ['title' => 'Kalender Akademik', 'route' => 'kepala-sekolah.kalender-akademik', 'icon' => 'calendar'],
            ['title' => 'Notifikasi', 'route' => 'shared.notifications', 'icon' => 'bell'],
        ],
        'guru' => $guruMenuItems,

        'murid' => [
            ['title' => 'Dashboard', 'route' => 'murid.dashboard', 'icon' => 'home'],
            ['title' => 'Panduan & FAQ', 'route' => 'shared.tutorial-faq', 'icon' => 'help-circle'],
            ['title' => 'Akademik & Tahfizh', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Nilai Akademik (TP & STS)', 'route' => 'murid.rapor', 'icon' => 'award'],
            ['title' => 'Evaluasi Tahfizh', 'route' => 'murid.tahfidz', 'icon' => 'book-open'],
            ['title' => 'Jadwal Remedial', 'route' => 'murid.remedial', 'icon' => 'clock'],
            ['title' => 'Kehadiran Saya', 'route' => 'murid.kehadiran', 'icon' => 'clipboard'],

            ['title' => 'Ekstrakurikuler', 'route' => 'murid.ekskul', 'icon' => 'star'],
            ['title' => 'Jadwal Pelajaran', 'route' => 'murid.jadwal', 'icon' => 'calendar'],
            ['title' => 'Kalender Akademik', 'route' => 'murid.kalender-akademik', 'icon' => 'calendar'],
            ['title' => 'Keuangan', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Tagihan SPP', 'route' => 'murid.tagihan', 'icon' => 'credit-card'],
            ['title' => 'Tabungan Saya', 'route' => 'murid.tabungan', 'icon' => 'wallet'],
            ['title' => 'Lainnya', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Riwayat Aktivitas', 'route' => 'murid.riwayat-aktivitas', 'icon' => 'activity'],
            ['title' => 'Notifikasi', 'route' => 'shared.notifications', 'icon' => 'bell'],
        ],
        'finance' => [
            ['title' => 'Dashboard', 'route' => 'finance.dashboard', 'icon' => 'home'],
            ['title' => 'Panduan & FAQ', 'route' => 'shared.tutorial-faq', 'icon' => 'help-circle'],
            ['title' => 'Pemasukan Kas Yayasan', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Overview Pembayaran', 'route' => 'finance.overview-pembayaran', 'icon' => 'eye'],
            ['title' => 'Buku Kas Masuk', 'route' => 'finance.arus-masuk', 'icon' => 'book-open'],
            ['title' => 'Kas Masuk Yayasan', 'route' => 'finance.arus-kas-masuk', 'icon' => 'heart-handshake'],
            ['title' => 'Manajemen Tagihan', 'route' => 'finance.tagihan', 'icon' => 'file-text'],
            ['title' => 'Tabungan Siswa', 'route' => 'finance.tabungan', 'icon' => 'wallet'],
            ['title' => 'Input Pembayaran', 'route' => 'finance.input-pembayaran', 'icon' => 'plus-circle'],
            ['title' => 'Pengeluaran & Anggaran Yayasan', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Kas Keluar Yayasan', 'route' => 'finance.arus-kas-keluar', 'icon' => 'trending-down'],
            ['title' => 'Pengajuan Dana', 'route' => 'finance.pengajuan-dana', 'icon' => 'banknote'],
            ['title' => 'Gaji Guru', 'route' => 'finance.gaji-guru', 'icon' => 'wallet'],
            ['title' => 'Kasbon Guru', 'route' => 'finance.peminjaman', 'icon' => 'link'],
            ['title' => 'Dana BOS (Bantuan Pemerintah)', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Dana BOS (Masuk & Keluar)', 'route' => 'finance.dana-bos', 'icon' => 'box'],
            ['title' => 'Laporan Keuangan', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Laporan Tunggakan', 'route' => 'finance.laporan.tunggakan', 'icon' => 'file-text'],
            ['title' => 'Laporan Pemasukan', 'route' => 'finance.laporan.pemasukan', 'icon' => 'activity'],
            ['title' => 'Laporan Pengeluaran', 'route' => 'finance.laporan.pengeluaran', 'icon' => 'trending-down'],
            ['title' => 'Lainnya', 'route' => null, 'icon' => null, 'section' => true],
            ['title' => 'Kalender Akademik', 'route' => 'kalender-akademik.shared', 'icon' => 'calendar'],
            ['title' => 'Notifikasi', 'route' => 'shared.notifications', 'icon' => 'bell'],
        ],
        default => [],
    };

    $roleLabel = match ($role) {
        'super_admin' => 'Kepala Yayasan',
        'tata_usaha' => 'Tata Usaha',
        'guru' => 'Guru',
        'murid' => 'Murid / Wali',
        'finance' => 'Bendahara',
        'pengawas' => 'Pengawas Sekolah',
        default => ucwords(str_replace('_', ' ', $role)),
    };
@endphp

<aside wire:persist="sidebar"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" 
       class="fixed inset-y-0 left-0 z-30 flex flex-col w-64 bg-white border-r border-stone-200 shadow-xl lg:shadow-sm transition-transform duration-300 ease-in-out lg:translate-x-0 -translate-x-full">
    <!-- Header/Brand -->
    <div class="flex items-center justify-between px-6 h-16 border-b border-stone-200">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-9 h-9 rounded-xl bg-green-50 border border-green-200">
                <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 019.918 5.842 50.45 50.45 0 00-2.658.814m-15.482 0a50.53 50.53 0 0115.482 0m-15.482 0v3.06c0 5.625 3.338 10.71 8.232 12.839m0-22.742V20.9" />
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-bold text-stone-800 tracking-wide">SIAKAD</h2>
                <p class="text-xs text-stone-500 font-medium">{{ $roleLabel }}</p>
            </div>
        </div>

        <!-- Mobile Close Button -->
        <button @click="sidebarOpen = false" type="button" class="p-1.5 rounded-xl text-stone-400 hover:text-stone-700 hover:bg-stone-100 lg:hidden" aria-label="Close sidebar">
            <x-lucide-x class="w-5 h-5" />
        </button>
    </div>

    <!-- Navigation Links -->
    <nav id="sidebar-nav"
         x-data="{
             init() {
                 this.restoreScroll();
                 document.addEventListener('livewire:navigated', () => {
                     this.restoreScroll();
                 });
                 this.$el.addEventListener('scroll', () => {
                     sessionStorage.setItem('sidebar_scroll_pos', this.$el.scrollTop);
                 });
             },
             restoreScroll() {
                 const saved = sessionStorage.getItem('sidebar_scroll_pos');
                 if (saved !== null) {
                     this.$el.scrollTop = parseInt(saved, 10);
                 }
                 this.$nextTick(() => {
                     const activeEl = this.$el.querySelector('.sidebar-active-link');
                     if (activeEl) {
                         const navRect = this.$el.getBoundingClientRect();
                         const elRect = activeEl.getBoundingClientRect();
                         if (elRect.top < navRect.top || elRect.bottom > navRect.bottom) {
                             activeEl.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                         }
                     }
                 });
             }
         }"
         class="flex-1 px-4 py-5 space-y-1 overflow-y-auto custom-scrollbar">
        @foreach ($menuItems as $item)
            @if (!empty($item['section']))
                <div class="pt-4 pb-1.5 px-3">
                    <span class="text-[11px] font-bold text-stone-400 uppercase tracking-wider">{{ $item['title'] }}</span>
                </div>
            @else
                @php
                    $isActive = !empty($item['route']) && request()->routeIs($item['route']);
                @endphp
                <a href="{{ !empty($item['route']) ? route($item['route']) : '#' }}" wire:navigate
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group
                          {{ $isActive 
                              ? 'bg-green-50 text-green-700 border-l-[3px] border-green-600 shadow-sm font-bold sidebar-active-link' 
                              : 'text-stone-600 hover:bg-stone-100 hover:text-stone-800' }}">
                    
                    @switch($item['icon'])
                        @case('home') <x-lucide-home class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('users') <x-lucide-users class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('book-open') <x-lucide-book-open class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('calendar') <x-lucide-calendar class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('graduation-cap') <x-lucide-graduation-cap class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('credit-card') <x-lucide-credit-card class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('file-text') <x-lucide-file-text class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('trending-down') <x-lucide-trending-down class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('plus-circle') <x-lucide-plus-circle class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('eye') <x-lucide-eye class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('settings') <x-lucide-settings class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('activity') <x-lucide-activity class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('alert-triangle') <x-lucide-alert-triangle class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('user-check') <x-lucide-user-check class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('bell') <x-lucide-bell class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('wallet') <x-lucide-wallet class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('box') <x-lucide-box class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('bar-chart-2') <x-lucide-bar-chart-2 class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('link') <x-lucide-link class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('banknote') <x-lucide-banknote class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('arrow-down-left') <x-lucide-arrow-down-left class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('heart-handshake') <x-lucide-heart-handshake class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('layers') <x-lucide-layers class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('clock') <x-lucide-clock class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('award') <x-lucide-award class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('star') <x-lucide-star class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('sliders') <x-lucide-sliders class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('edit-3') <x-lucide-edit-3 class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('clipboard') <x-lucide-clipboard class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('shield-check') <x-lucide-shield-check class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('check-square') <x-lucide-check-square class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('refresh-cw') <x-lucide-refresh-cw class="w-[18px] h-[18px] shrink-0" /> @break
                        @case('help-circle') <x-lucide-help-circle class="w-[18px] h-[18px] shrink-0" /> @break
                    @endswitch

                    <span>{{ $item['title'] }}</span>
                </a>
            @endif
        @endforeach
    </nav>

    <!-- User Profile Footer -->
    <div class="p-4 border-t border-stone-200 bg-stone-50/60">
        <div class="flex items-center gap-3 mb-3 p-1.5 -mx-1.5 rounded-xl">
            <div class="w-9 h-9 rounded-full bg-green-50 border border-green-200 flex items-center justify-center font-bold text-green-700 text-sm select-none">
                {{ strtoupper(substr(auth()->user()->nama ?? 'U', 0, 2)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-stone-800 truncate">{{ auth()->user()->nama ?? 'User' }}</p>
                <p class="text-xs text-stone-500 truncate capitalize">{{ $roleLabel }}</p>
            </div>
        </div>
        
        <!-- Logout Form -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" 
                class="w-full flex items-center justify-center gap-1.5 py-2 px-2 rounded-xl border border-stone-200 hover:border-red-200 text-xs font-semibold text-stone-600 hover:text-red-600 hover:bg-red-50 transition duration-150">
                <x-lucide-log-out class="w-3.5 h-3.5" />
                <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>
