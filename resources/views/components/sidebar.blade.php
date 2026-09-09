@php
    $role = auth()->user()->role->nama ?? '';
    $userGuru = auth()->user()->guru;
    $jenisGuru = strtolower($userGuru->jenis_guru ?? 'umum');
    if ($jenisGuru === 'tahfidz') {
        $jenisGuru = 'tahfizh';
    }

    $pendingApprovalsCount = 0;
    if (in_array($role, ['super_admin', 'super_admin_2', 'finance'])) {
        $pendingApprovalsCount = \Illuminate\Support\Facades\Schema::hasTable('approval_keuangan')
            ? \App\Models\ApprovalKeuangan::where('status', 'menunggu')->count()
            : 0;
    }

    $guruMenuItems = match ($jenisGuru) {
        'pendamping' => [
            ['type' => 'link', 'title' => 'Dashboard Guru', 'route' => 'guru.dashboard', 'icon' => 'home'],
            [
                'type' => 'group',
                'id' => 'pendampingan_khusus',
                'title' => 'Pendampingan (Inklusi)',
                'icon' => 'users',
                'items' => [
                    ['title' => 'Catatan Pendampingan', 'route' => 'guru.pendampingan', 'icon' => 'edit-3'],
                ]
            ],
            [
                'type' => 'group',
                'id' => 'presensi_jadwal',
                'title' => 'Presensi & Jadwal Piket',
                'icon' => 'calendar-check',
                'items' => [
                    ['title' => 'Lihat Absensi Siswa', 'route' => 'guru.absensi-siswa', 'icon' => 'check-square'],
                    ['title' => 'Jadwal Piket', 'route' => 'guru.piket', 'icon' => 'shield-check'],
                ]
            ],
            [
                'type' => 'group',
                'id' => 'info_rekap',
                'title' => 'Info Guru & Rekap',
                'icon' => 'user-check',
                'items' => [
                    ['title' => 'Presensi Guru Mandiri', 'route' => 'guru.absensi-diri', 'icon' => 'clock'],
                    ['title' => 'Slip Gaji Saya', 'route' => 'guru.slip-gaji', 'icon' => 'banknote'],
                    ['title' => 'Kalender Akademik', 'route' => 'guru.kalender-akademik', 'icon' => 'calendar'],
                ]
            ],
        ],
        'tahfizh' => [
            ['type' => 'link', 'title' => 'Dashboard Guru', 'route' => 'guru.dashboard', 'icon' => 'home'],
            [
                'type' => 'group',
                'id' => 'kbm_presensi',
                'title' => 'KBM & Presensi Siswa',
                'icon' => 'check-square',
                'items' => [
                    ['title' => 'Absensi Siswa', 'route' => 'guru.absensi-siswa', 'icon' => 'check-square'],
                    ['title' => 'Jadwal Mengajar', 'route' => 'guru.jadwal-mengajar', 'icon' => 'calendar'],
                    ['title' => 'Jadwal Piket', 'route' => 'guru.piket', 'icon' => 'shield-check'],
                ]
            ],
            [
                'type' => 'group',
                'id' => 'tahfizh_hafalan',
                'title' => 'Setoran & Tahfizh',
                'icon' => 'award',
                'items' => [
                    ['title' => 'Setoran Tahfizh', 'route' => 'guru.input-tahfidz', 'icon' => 'award'],
                ]
            ],
            [
                'type' => 'group',
                'id' => 'rapor_wali',
                'title' => 'Rapor & Wali Kelas',
                'icon' => 'book-open',
                'items' => [
                    ['title' => 'Cetak & Kelola Rapor', 'route' => 'guru.kelola-rapor', 'icon' => 'book-open'],
                    ['title' => 'Ekstrakurikuler', 'route' => 'guru.ekskul', 'icon' => 'star'],
                    ['title' => 'Pengembangan Diri', 'route' => 'guru.pengembangan-diri', 'icon' => 'user-check'],
                ]
            ],
            [
                'type' => 'group',
                'id' => 'info_rekap',
                'title' => 'Info Guru & Rekap',
                'icon' => 'user-check',
                'items' => [
                    ['title' => 'Presensi Guru Mandiri', 'route' => 'guru.absensi-diri', 'icon' => 'clock'],
                    ['title' => 'Slip Gaji Saya', 'route' => 'guru.slip-gaji', 'icon' => 'banknote'],
                    ['title' => 'Kalender Akademik', 'route' => 'guru.kalender-akademik', 'icon' => 'calendar'],
                    ['title' => 'Rekap Absensi Siswa', 'route' => 'guru.laporan.absensi-siswa', 'icon' => 'file-text'],
                    ['title' => 'Rekap Nilai Kelas', 'route' => 'guru.laporan.rekap-nilai', 'icon' => 'bar-chart-2'],
                ]
            ],
        ],
        'umum' => [
            ['type' => 'link', 'title' => 'Dashboard Guru', 'route' => 'guru.dashboard', 'icon' => 'home'],
            [
                'type' => 'group',
                'id' => 'kbm_presensi',
                'title' => 'KBM & Presensi Siswa',
                'icon' => 'check-square',
                'items' => [
                    ['title' => 'Absensi Siswa', 'route' => 'guru.absensi-siswa', 'icon' => 'check-square'],
                    ['title' => 'Jadwal Mengajar', 'route' => 'guru.jadwal-mengajar', 'icon' => 'calendar'],
                    ['title' => 'Jadwal Piket', 'route' => 'guru.piket', 'icon' => 'shield-check'],
                ]
            ],
            [
                'type' => 'group',
                'id' => 'penilaian_kurikulum',
                'title' => 'Penilaian & Kurikulum',
                'icon' => 'award',
                'items' => [
                    ['title' => 'Setup Bab & TP', 'route' => 'guru.kurikulum-merdeka', 'icon' => 'layers'],
                    ['title' => 'Nilai Sumatif TP & SAS', 'route' => 'guru.input-sumatif', 'icon' => 'edit-3'],
                    ['title' => 'Penilaian Kokurikuler P5', 'route' => 'guru.penilaian-p5', 'icon' => 'star'],
                    ['title' => 'Jadwal Remedial', 'route' => 'guru.remedial', 'icon' => 'refresh-cw'],
                ]
            ],
            [
                'type' => 'group',
                'id' => 'rapor_wali',
                'title' => 'Rapor & Wali Kelas',
                'icon' => 'book-open',
                'items' => [
                    ['title' => 'Cetak & Kelola Rapor', 'route' => 'guru.kelola-rapor', 'icon' => 'book-open'],
                    ['title' => 'Ekstrakurikuler', 'route' => 'guru.ekskul', 'icon' => 'star'],
                    ['title' => 'Pengembangan Diri', 'route' => 'guru.pengembangan-diri', 'icon' => 'user-check'],
                ]
            ],
            [
                'type' => 'group',
                'id' => 'info_rekap',
                'title' => 'Info Guru & Rekap',
                'icon' => 'user-check',
                'items' => [
                    ['title' => 'Presensi Guru Mandiri', 'route' => 'guru.absensi-diri', 'icon' => 'clock'],
                    ['title' => 'Slip Gaji Saya', 'route' => 'guru.slip-gaji', 'icon' => 'banknote'],
                    ['title' => 'Kalender Akademik', 'route' => 'guru.kalender-akademik', 'icon' => 'calendar'],
                    ['title' => 'Rekap Absensi Siswa', 'route' => 'guru.laporan.absensi-siswa', 'icon' => 'file-text'],
                    ['title' => 'Rekap Nilai Kelas', 'route' => 'guru.laporan.rekap-nilai', 'icon' => 'bar-chart-2'],
                ]
            ],
        ],
        default => [
            ['type' => 'link', 'title' => 'Dashboard Guru', 'route' => 'guru.dashboard', 'icon' => 'home'],
            [
                'type' => 'group',
                'id' => 'kbm_presensi',
                'title' => 'KBM & Presensi Siswa',
                'icon' => 'check-square',
                'items' => [
                    ['title' => 'Absensi Siswa', 'route' => 'guru.absensi-siswa', 'icon' => 'check-square'],
                    ['title' => 'Jadwal Mengajar', 'route' => 'guru.jadwal-mengajar', 'icon' => 'calendar'],
                    ['title' => 'Jadwal Piket', 'route' => 'guru.piket', 'icon' => 'shield-check'],
                ]
            ],
            [
                'type' => 'group',
                'id' => 'penilaian_tahfizh',
                'title' => 'Penilaian & Tahfizh',
                'icon' => 'award',
                'items' => [
                    ['title' => 'Setup Bab & TP', 'route' => 'guru.kurikulum-merdeka', 'icon' => 'layers'],
                    ['title' => 'Nilai Sumatif TP & SAS', 'route' => 'guru.input-sumatif', 'icon' => 'edit-3'],
                    ['title' => 'Setoran Tahfizh', 'route' => 'guru.input-tahfidz', 'icon' => 'award'],
                    ['title' => 'Penilaian Kokurikuler P5', 'route' => 'guru.penilaian-p5', 'icon' => 'star'],
                    ['title' => 'Jadwal Remedial', 'route' => 'guru.remedial', 'icon' => 'refresh-cw'],
                ]
            ],
            [
                'type' => 'group',
                'id' => 'rapor_wali',
                'title' => 'Rapor & Wali Kelas',
                'icon' => 'book-open',
                'items' => [
                    ['title' => 'Cetak & Kelola Rapor', 'route' => 'guru.kelola-rapor', 'icon' => 'book-open'],
                    ['title' => 'Ekstrakurikuler', 'route' => 'guru.ekskul', 'icon' => 'star'],
                    ['title' => 'Pengembangan Diri', 'route' => 'guru.pengembangan-diri', 'icon' => 'user-check'],
                ]
            ],
            [
                'type' => 'group',
                'id' => 'info_rekap',
                'title' => 'Info Guru & Rekap',
                'icon' => 'user-check',
                'items' => [
                    ['title' => 'Presensi Guru Mandiri', 'route' => 'guru.absensi-diri', 'icon' => 'clock'],
                    ['title' => 'Slip Gaji Saya', 'route' => 'guru.slip-gaji', 'icon' => 'banknote'],
                    ['title' => 'Kalender Akademik', 'route' => 'guru.kalender-akademik', 'icon' => 'calendar'],
                    ['title' => 'Rekap Absensi Siswa', 'route' => 'guru.laporan.absensi-siswa', 'icon' => 'file-text'],
                    ['title' => 'Rekap Nilai Kelas', 'route' => 'guru.laporan.rekap-nilai', 'icon' => 'bar-chart-2'],
                ]
            ],
        ],
    };

    // Modern Simplified Navigation Menu Items
    $menuItems = match ($role) {
        'super_admin', 'super_admin_2' => [
            ['type' => 'link', 'title' => 'Dashboard Utama', 'route' => 'super-admin.dashboard', 'icon' => 'home'],
            [
                'type' => 'group',
                'id' => 'sa_keuangan',
                'title' => 'Keuangan & Persetujuan',
                'icon' => 'credit-card',
                'badge' => $pendingApprovalsCount,
                'items' => [
                    ['title' => 'Persetujuan Keuangan', 'route' => 'super-admin.approval-keuangan', 'icon' => 'shield-check', 'badge' => $pendingApprovalsCount],
                    ['title' => 'Overview Pembayaran', 'route' => 'finance.overview-pembayaran', 'icon' => 'eye'],
                    ['title' => 'Arus Kas (Cash Flow)', 'route' => 'finance.arus-kas', 'icon' => 'layers'],
                    ['title' => 'Kelola Tagihan SPP', 'route' => 'finance.tagihan', 'icon' => 'file-text'],
                    ['title' => 'Tabungan Siswa', 'route' => 'finance.tabungan', 'icon' => 'wallet'],
                    ['title' => 'Gaji Guru & Staf', 'route' => 'finance.gaji-guru', 'icon' => 'banknote'],
                    ['title' => 'Dana BOS (Masuk & Keluar)', 'route' => 'finance.dana-bos', 'icon' => 'box'],
                ]
            ],
            [
                'type' => 'group',
                'id' => 'sa_akademik',
                'title' => 'Supervisi & Akademik',
                'icon' => 'award',
                'items' => [
                    ['title' => 'Monitoring Akademik', 'route' => 'super-admin.monitoring-akademik', 'icon' => 'activity'],
                    ['title' => 'Catatan Pendampingan', 'route' => 'super-admin.pendampingan', 'icon' => 'user-check'],
                    ['title' => 'Setoran Tahfizh', 'route' => 'guru.input-tahfidz', 'icon' => 'award'],
                    ['title' => 'Penilaian P5', 'route' => 'guru.penilaian-p5', 'icon' => 'star'],
                    ['title' => 'Rapor Murid', 'route' => 'guru.kelola-rapor', 'icon' => 'book-open'],
                ]
            ],
            [
                'type' => 'group',
                'id' => 'sa_sdm',
                'title' => 'Data Master & SDM',
                'icon' => 'users',
                'items' => [
                    ['title' => 'Data Siswa', 'route' => 'super-admin.siswa', 'icon' => 'users'],
                    ['title' => 'Data Guru', 'route' => 'super-admin.guru', 'icon' => 'user-check'],
                    ['title' => 'Capaian & Evaluasi Guru', 'route' => 'super-admin.capaian-guru', 'icon' => 'award'],
                    ['title' => 'Direktori Karyawan', 'route' => 'super-admin.karyawan', 'icon' => 'users'],
                    ['title' => 'Kelas & Mapel', 'route' => 'super-admin.kelas', 'icon' => 'layers'],
                    ['title' => 'Plotting Siswa Kelas', 'route' => 'super-admin.plotting-kelas', 'icon' => 'users'],
                    ['title' => 'Kelola Ekstrakurikuler', 'route' => 'tata-usaha.ekstrakurikuler', 'icon' => 'star'],
                    ['title' => 'Jadwal Pelajaran', 'route' => 'super-admin.jadwal', 'icon' => 'calendar'],
                    ['title' => 'Kalender Akademik', 'route' => 'super-admin.kalender-akademik', 'icon' => 'calendar'],
                    ['title' => 'Kenaikan Kelas', 'route' => 'super-admin.kenaikan-kelas', 'icon' => 'user-check'],
                    ['title' => 'Layanan Persuratan', 'route' => 'super-admin.surat', 'icon' => 'file-text'],
                ]
            ],
            [
                'type' => 'group',
                'id' => 'sa_laporan',
                'title' => 'Pusat Laporan',
                'icon' => 'bar-chart-2',
                'items' => [
                    ['title' => 'Laporan Absensi Siswa', 'route' => 'super-admin.laporan.absensi-siswa', 'icon' => 'file-text'],
                    ['title' => 'Laporan Absensi Guru', 'route' => 'super-admin.laporan.absensi-guru', 'icon' => 'clipboard'],
                    ['title' => 'Laporan Rekap Nilai', 'route' => 'super-admin.laporan.rekap-nilai', 'icon' => 'award'],
                ]
            ],
            [
                'type' => 'group',
                'id' => 'sa_sistem',
                'title' => 'Manajemen Sistem',
                'icon' => 'settings',
                'items' => [
                    ['title' => 'Manajemen User', 'route' => 'super-admin.user', 'icon' => 'users'],
                    ['title' => 'Audit Log', 'route' => 'super-admin.audit-log', 'icon' => 'activity'],
                    ['title' => 'Log Error Sistem', 'route' => 'super-admin.error-log', 'icon' => 'alert-triangle'],
                    ['title' => 'Pengaturan Sistem', 'route' => 'super-admin.pengaturan', 'icon' => 'settings'],
                ]
            ],
        ],
        'tata_usaha' => [
            ['type' => 'link', 'title' => 'Dashboard TU', 'route' => 'tata-usaha.dashboard', 'icon' => 'home'],
            [
                'type' => 'group',
                'id' => 'tu_kesiswaan',
                'title' => 'Kesiswaan & Alumni',
                'icon' => 'users',
                'items' => [
                    ['title' => 'Data Siswa', 'route' => 'tata-usaha.siswa', 'icon' => 'users'],
                    ['title' => 'Plotting Siswa Kelas', 'route' => 'tata-usaha.plotting-kelas', 'icon' => 'layers'],
                    ['title' => 'Kenaikan Kelas', 'route' => 'tata-usaha.kenaikan-kelas', 'icon' => 'user-check'],
                    ['title' => 'Data Alumni', 'route' => 'tata-usaha.alumni', 'icon' => 'award'],
                ]
            ],
            [
                'type' => 'group',
                'id' => 'tu_sdm',
                'title' => 'Manajemen SDM & Presensi',
                'icon' => 'user-check',
                'items' => [
                    ['title' => 'Data Guru', 'route' => 'tata-usaha.guru', 'icon' => 'user-check'],
                    ['title' => 'Direktori Karyawan', 'route' => 'tata-usaha.karyawan', 'icon' => 'users'],
                    ['title' => 'Input Absensi Karyawan', 'route' => 'tata-usaha.absensi-karyawan', 'icon' => 'clipboard'],
                    ['title' => 'Jadwal Piket Guru', 'route' => 'tata-usaha.piket', 'icon' => 'clock'],
                    ['title' => 'Manajemen Akun Staff', 'route' => 'tata-usaha.user', 'icon' => 'users'],
                ]
            ],
            [
                'type' => 'group',
                'id' => 'tu_akademik',
                'title' => 'Akademik & Persuratan',
                'icon' => 'calendar',
                'items' => [
                    ['title' => 'Kelas & Mapel', 'route' => 'tata-usaha.kelas', 'icon' => 'layers'],
                    ['title' => 'Jadwal Pelajaran', 'route' => 'tata-usaha.jadwal', 'icon' => 'calendar'],
                    ['title' => 'Kalender & Libur', 'route' => 'tata-usaha.kalender-akademik', 'icon' => 'calendar'],
                    ['title' => 'Kelola Ekstrakurikuler', 'route' => 'tata-usaha.ekstrakurikuler', 'icon' => 'star'],
                    ['title' => 'Layanan Persuratan', 'route' => 'tata-usaha.surat', 'icon' => 'file-text'],
                    ['title' => 'Pengaturan Sekolah', 'route' => 'tata-usaha.pengaturan', 'icon' => 'settings'],
                ]
            ],
            [
                'type' => 'group',
                'id' => 'tu_laporan',
                'title' => 'Pusat Laporan',
                'icon' => 'bar-chart-2',
                'items' => [
                    ['title' => 'Laporan Absensi Siswa', 'route' => 'tata-usaha.laporan.absensi-siswa', 'icon' => 'file-text'],
                    ['title' => 'Rekap Absensi Guru', 'route' => 'tata-usaha.laporan.absensi-guru', 'icon' => 'clipboard'],
                    ['title' => 'Laporan Rekap Nilai', 'route' => 'tata-usaha.laporan.rekap-nilai', 'icon' => 'award'],
                ]
            ],
        ],
        'finance' => [
            ['type' => 'link', 'title' => 'Dashboard Keuangan', 'route' => 'finance.dashboard', 'icon' => 'home'],
            [
                'type' => 'group',
                'id' => 'fin_transaksi',
                'title' => 'Kasir & Tagihan',
                'icon' => 'credit-card',
                'items' => [
                    ['title' => 'Kasir Pembayaran', 'route' => 'finance.input-pembayaran', 'icon' => 'credit-card'],
                    ['title' => 'Manajemen Tagihan', 'route' => 'finance.tagihan', 'icon' => 'file-text'],
                    ['title' => 'Tabungan Siswa', 'route' => 'finance.tabungan', 'icon' => 'wallet'],
                    ['title' => 'Overview Pembayaran', 'route' => 'finance.overview-pembayaran', 'icon' => 'eye'],
                ]
            ],
            [
                'type' => 'group',
                'id' => 'fin_arus_kas',
                'title' => 'Arus Kas & Pengeluaran',
                'icon' => 'layers',
                'badge' => $pendingApprovalsCount,
                'items' => [
                    ['title' => 'Persetujuan Keuangan', 'route' => 'finance.approval-keuangan', 'icon' => 'shield-check', 'badge' => $pendingApprovalsCount],
                    ['title' => 'Arus Kas (Cash Flow)', 'route' => 'finance.arus-kas', 'icon' => 'layers'],
                    ['title' => 'Pengajuan Dana', 'route' => 'finance.pengajuan-dana', 'icon' => 'banknote'],
                    ['title' => 'Gaji Guru & Karyawan', 'route' => 'finance.gaji-guru', 'icon' => 'wallet'],
                    ['title' => 'Kasbon Guru', 'route' => 'finance.peminjaman', 'icon' => 'link'],
                    ['title' => 'Dana BOS (Masuk & Keluar)', 'route' => 'finance.dana-bos', 'icon' => 'box'],
                ]
            ],
            [
                'type' => 'group',
                'id' => 'fin_laporan',
                'title' => 'Laporan Keuangan',
                'icon' => 'bar-chart-2',
                'items' => [
                    ['title' => 'Laporan Tunggakan', 'route' => 'finance.laporan.tunggakan', 'icon' => 'file-text'],
                    ['title' => 'Laporan Pemasukan', 'route' => 'finance.laporan.pemasukan', 'icon' => 'activity'],
                    ['title' => 'Laporan Pengeluaran', 'route' => 'finance.laporan.pengeluaran', 'icon' => 'trending-down'],
                ]
            ],
            ['type' => 'link', 'title' => 'Pengaturan Sekolah', 'route' => 'finance.pengaturan', 'icon' => 'settings'],
        ],
        'guru' => $guruMenuItems,
        'murid' => [
            ['type' => 'link', 'title' => 'Dashboard Santri', 'route' => 'murid.dashboard', 'icon' => 'home'],
            [
                'type' => 'group',
                'id' => 'murid_akademik',
                'title' => 'Akademik & Belajar',
                'icon' => 'book-open',
                'items' => [
                    ['title' => 'Nilai Akademik (Rapor)', 'route' => 'murid.rapor', 'icon' => 'award'],
                    ['title' => 'Evaluasi Tahfizh', 'route' => 'murid.tahfidz', 'icon' => 'book-open'],
                    ['title' => 'Jadwal Remedial', 'route' => 'murid.remedial', 'icon' => 'clock'],
                    ['title' => 'Kehadiran Saya', 'route' => 'murid.kehadiran', 'icon' => 'check-square'],
                    ['title' => 'Jadwal Pelajaran', 'route' => 'murid.jadwal', 'icon' => 'calendar'],
                    ['title' => 'Ekstrakurikuler', 'route' => 'murid.ekskul', 'icon' => 'star'],
                ]
            ],
            [
                'type' => 'group',
                'id' => 'murid_keuangan',
                'title' => 'Keuangan & SPP',
                'icon' => 'credit-card',
                'items' => [
                    ['title' => 'Tagihan SPP', 'route' => 'murid.tagihan', 'icon' => 'credit-card'],
                    ['title' => 'Tabungan Saya', 'route' => 'murid.tabungan', 'icon' => 'wallet'],
                ]
            ],
            ['type' => 'link', 'title' => 'Kalender Akademik', 'route' => 'murid.kalender-akademik', 'icon' => 'calendar'],
            ['type' => 'link', 'title' => 'Riwayat Aktivitas', 'route' => 'murid.riwayat-aktivitas', 'icon' => 'activity'],
        ],
        'pengawas' => [
            ['type' => 'link', 'title' => 'Dashboard Pengawas', 'route' => 'pengawas.dashboard', 'icon' => 'home'],
            [
                'type' => 'group',
                'id' => 'pengawas_supervisi',
                'title' => 'Supervisi & Penilaian Guru',
                'icon' => 'award',
                'items' => [
                    ['title' => 'Penilaian & Capaian Guru', 'route' => 'pengawas.capaian-guru', 'icon' => 'award'],
                    ['title' => 'Catatan Pendampingan', 'route' => 'pengawas.pendampingan', 'icon' => 'user-check'],
                    ['title' => 'Persetujuan Nilai Rapor', 'route' => 'pengawas.koreksi-nilai', 'icon' => 'shield-check'],
                ]
            ],
            [
                'type' => 'group',
                'id' => 'pengawas_laporan',
                'title' => 'Monitoring & Laporan',
                'icon' => 'bar-chart-2',
                'items' => [
                    ['title' => 'Laporan Absensi Siswa', 'route' => 'pengawas.laporan.absensi-siswa', 'icon' => 'file-text'],
                    ['title' => 'Rekap Absensi Guru', 'route' => 'pengawas.laporan.absensi-guru', 'icon' => 'clipboard'],
                    ['title' => 'Laporan Rekap Nilai', 'route' => 'pengawas.laporan.rekap-nilai', 'icon' => 'award'],
                    ['title' => 'Laporan Tunggakan Siswa', 'route' => 'pengawas.laporan.tunggakan', 'icon' => 'alert-circle'],
                ]
            ],
            ['type' => 'link', 'title' => 'Kalender Akademik', 'route' => 'pengawas.kalender-akademik', 'icon' => 'calendar'],
        ],
        'kepala_sekolah' => [
            ['type' => 'link', 'title' => 'Dashboard Kepsek', 'route' => 'kepala-sekolah.dashboard', 'icon' => 'home'],
            [
                'type' => 'group',
                'id' => 'kepsek_supervisi',
                'title' => 'Supervisi Guru',
                'icon' => 'award',
                'items' => [
                    ['title' => 'Penilaian & Capaian Guru', 'route' => 'kepala-sekolah.capaian-guru', 'icon' => 'award'],
                    ['title' => 'Catatan Pendampingan', 'route' => 'kepala-sekolah.pendampingan', 'icon' => 'user-check'],
                ]
            ],
            [
                'type' => 'group',
                'id' => 'kepsek_laporan',
                'title' => 'Monitoring & Laporan',
                'icon' => 'bar-chart-2',
                'items' => [
                    ['title' => 'Laporan Absensi Siswa', 'route' => 'kepala-sekolah.laporan.absensi-siswa', 'icon' => 'file-text'],
                    ['title' => 'Rekap Absensi Guru', 'route' => 'kepala-sekolah.laporan.absensi-guru', 'icon' => 'clipboard'],
                    ['title' => 'Laporan Rekap Nilai', 'route' => 'kepala-sekolah.laporan.rekap-nilai', 'icon' => 'award'],
                    ['title' => 'Laporan Tunggakan Siswa', 'route' => 'kepala-sekolah.laporan.tunggakan', 'icon' => 'alert-circle'],
                ]
            ],
            ['type' => 'link', 'title' => 'Dana BOS (Pemantauan)', 'route' => 'kepala-sekolah.dana-bos', 'icon' => 'box'],
            ['type' => 'link', 'title' => 'Kalender Akademik', 'route' => 'kepala-sekolah.kalender-akademik', 'icon' => 'calendar'],
        ],
        default => [],
    };

    $roleLabel = match ($role) {
        'super_admin' => 'Kepala Yayasan',
        'super_admin_2' => 'Super Admin 2 (Auditor)',
        'tata_usaha' => 'Tata Usaha',
        'guru' => 'Guru',
        'murid' => 'Santri / Wali',
        'finance' => 'Bendahara',
        'pengawas' => 'Pengawas Sekolah',
        'kepala_sekolah' => 'Kepala Sekolah',
        default => ucwords(str_replace('_', ' ', $role)),
    };
@endphp

<aside wire:persist="sidebar"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" 
       class="fixed inset-y-0 left-0 z-30 flex flex-col w-64 bg-white border-r border-stone-200 shadow-xl lg:shadow-sm transition-transform duration-300 ease-in-out lg:translate-x-0 -translate-x-full select-none">
    
    <!-- Header / Brand -->
    <div class="flex items-center justify-between px-5 h-16 border-b border-stone-200 bg-white">
        <div class="flex items-center gap-3 min-w-0">
            <div class="flex items-center justify-center w-9 h-9 rounded-xl bg-emerald-50 border border-emerald-200 shrink-0">
                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <div class="truncate">
                <h2 class="text-sm font-extrabold text-stone-900 tracking-tight">SIAKAD DIGITAL</h2>
                <p class="text-[11px] text-emerald-700 font-bold truncate capitalize">{{ $roleLabel }}</p>
            </div>
        </div>

        <!-- Mobile Close Button -->
        <button @click="sidebarOpen = false" type="button" class="p-1.5 rounded-xl text-stone-400 hover:text-stone-700 hover:bg-stone-100 lg:hidden" aria-label="Close sidebar">
            <x-lucide-x class="w-5 h-5" />
        </button>
    </div>

    <!-- Navigation Accordion -->
    <nav id="sidebar-nav"
         x-data="{
             openGroups: {},
             init() {
                 this.restoreScroll();
                 document.addEventListener('livewire:navigated', () => {
                     this.restoreScroll();
                 });
                 this.$el.addEventListener('scroll', () => {
                     sessionStorage.setItem('sidebar_scroll_pos', this.$el.scrollTop);
                 });
             },
             toggleGroup(id) {
                 this.openGroups[id] = !this.openGroups[id];
             },
             isGroupOpen(id, defaultOpen) {
                 if (this.openGroups[id] === undefined) {
                     this.openGroups[id] = defaultOpen;
                 }
                 return this.openGroups[id];
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
         class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto custom-scrollbar">

        @foreach ($menuItems as $item)
            @if (($item['type'] ?? 'link') === 'link')
                @php
                    $hasRoute = !empty($item['route']) && \Illuminate\Support\Facades\Route::has($item['route']);
                    $isActive = $hasRoute && (request()->routeIs($item['route']) || request()->routeIs($item['route'] . '.*'));
                    $itemUrl = $hasRoute ? route($item['route']) : (!empty($item['url']) ? $item['url'] : '#');
                @endphp
                <a href="{{ $itemUrl }}" wire:navigate
                   class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-200 group
                          {{ $isActive 
                              ? 'bg-emerald-50 text-emerald-800 border-l-[3px] border-emerald-600 shadow-xs font-bold sidebar-active-link' 
                              : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900' }}">
                    
                    @include('components.sidebar-icon', ['icon' => $item['icon']])

                    <span class="flex-1 truncate">{{ $item['title'] }}</span>

                    @if (!empty($item['badge']) && $item['badge'] > 0)
                        <span class="ml-auto px-2 py-0.5 text-[10px] font-bold text-white bg-amber-500 rounded-full animate-pulse shadow-2xs">
                            {{ $item['badge'] }}
                        </span>
                    @endif
                </a>
            @elseif (($item['type'] ?? '') === 'group')
                @php
                    $isGroupActive = false;
                    foreach ($item['items'] as $subItem) {
                        if (!empty($subItem['route']) && \Illuminate\Support\Facades\Route::has($subItem['route']) && (request()->routeIs($subItem['route']) || request()->routeIs($subItem['route'] . '.*'))) {
                            $isGroupActive = true;
                            break;
                        }
                    }
                    $groupId = $item['id'] ?? Str::slug($item['title']);
                @endphp
                
                <div class="space-y-0.5">
                    <!-- Group Header Button -->
                    <button type="button" 
                            @click="toggleGroup('{{ $groupId }}')"
                            class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-150 group text-left
                                   {{ $isGroupActive 
                                       ? 'bg-emerald-50/60 text-emerald-900 border-l-[3px] border-emerald-600 font-bold' 
                                       : 'text-stone-600 hover:text-stone-900 hover:bg-stone-100' }}">
                        
                        <div class="flex items-center gap-2.5 min-w-0">
                            @include('components.sidebar-icon', ['icon' => $item['icon']])
                            <span class="truncate">{{ $item['title'] }}</span>
                        </div>

                        <div class="flex items-center gap-1.5 shrink-0 ml-2">
                            @if (!empty($item['badge']) && $item['badge'] > 0)
                                <span class="px-1.5 py-0.2 text-[10px] font-bold text-white bg-amber-500 rounded-full animate-pulse shadow-2xs">
                                    {{ $item['badge'] }}
                                </span>
                            @endif

                            <x-lucide-chevron-down class="w-3.5 h-3.5 text-stone-400 transition-transform duration-200 group-hover:text-stone-600"
                                                   ::class="isGroupOpen('{{ $groupId }}', {{ $isGroupActive ? 'true' : 'false' }}) ? 'rotate-180 text-emerald-600' : ''" />
                        </div>
                    </button>

                    <!-- Submenu Collapsible Links -->
                    <div x-show="isGroupOpen('{{ $groupId }}', {{ $isGroupActive ? 'true' : 'false' }})"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="pl-4 pr-1 py-1 space-y-0.5 border-l-2 border-emerald-100 ml-4 my-0.5"
                         style="display: none;">
                        
                        @foreach ($item['items'] as $subItem)
                            @php
                                $subHasRoute = !empty($subItem['route']) && \Illuminate\Support\Facades\Route::has($subItem['route']);
                                $subIsActive = $subHasRoute && (request()->routeIs($subItem['route']) || request()->routeIs($subItem['route'] . '.*'));
                                $subUrl = $subHasRoute ? route($subItem['route']) : (!empty($subItem['url']) ? $subItem['url'] : '#');
                            @endphp
                            <a href="{{ $subUrl }}" wire:navigate
                               class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition duration-150 group
                                      {{ $subIsActive 
                                          ? 'bg-emerald-600 text-white font-bold shadow-2xs sidebar-active-link' 
                                          : 'text-stone-600 hover:text-stone-900 hover:bg-stone-100' }}">
                                
                                <span class="truncate">{{ $subItem['title'] }}</span>

                                @if (!empty($subItem['badge']) && $subItem['badge'] > 0)
                                    <span class="ml-1 px-1.5 py-0.2 text-[9px] font-bold {{ $subIsActive ? 'bg-white text-emerald-800' : 'bg-amber-500 text-white' }} rounded-full">
                                        {{ $subItem['badge'] }}
                                    </span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </nav>

    <!-- Sidebar Footer (Panduan + Profil User) -->
    <div class="p-3 border-t border-stone-200 bg-stone-50/70 shrink-0 space-y-2">
        <!-- Panduan & FAQ Compact Utility Link -->
        <a href="{{ route('shared.tutorial-faq') }}" wire:navigate 
           class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-stone-500 hover:text-emerald-700 hover:bg-emerald-50/80 transition">
            <x-lucide-help-circle class="w-3.5 h-3.5 text-stone-400 shrink-0" />
            <span class="truncate">Panduan & FAQ</span>
        </a>

        <!-- User Profile Card -->
        <div class="flex items-center gap-2.5 p-2 bg-white rounded-xl border border-stone-200/80 shadow-2xs">
            <div class="w-8 h-8 rounded-full bg-emerald-100 border border-emerald-200 flex items-center justify-center font-extrabold text-emerald-800 text-xs shrink-0 select-none">
                {{ strtoupper(substr(auth()->user()->nama ?? 'U', 0, 2)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-bold text-stone-800 truncate">{{ auth()->user()->nama ?? 'User' }}</p>
                <p class="text-[10px] text-stone-500 font-medium truncate capitalize">{{ $roleLabel }}</p>
            </div>
            
            <!-- Quick Logout Form -->
            <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                @csrf
                <button type="submit" title="Keluar dari sistem" 
                    class="p-1.5 text-stone-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition">
                    <x-lucide-log-out class="w-3.5 h-3.5" />
                </button>
            </form>
        </div>
    </div>
</aside>
