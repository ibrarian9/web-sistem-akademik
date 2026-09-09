# Graph Report - web-sistem-akademik  (2026-09-09)

## Corpus Check
- 492 files · ~385,866 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 2674 nodes · 5687 edges · 326 communities (249 shown, 77 thin omitted)
- Extraction: 86% EXTRACTED · 14% INFERRED · 0% AMBIGUOUS · INFERRED: 789 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `f4cebe26`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- CatatanPendampinganIndex
- ManajemenJadwal
- AuditLogger
- Pengeluaran.php
- User
- 1. Super Admin
- DetailTagihanSiswa
- GuruMapelKelas
- scripts
- ManajemenKalenderAkademik
- 5. Finance / Keuangan
- AbsensiGuru
- ApprovalKeuangan
- 4. Model Data (Entitas & Field)
- DetailGajiGuru
- ManajemenSurat
- Notifikasi
- Kelas
- GajiGuru
- package.json
- LaporanPengeluaran
- PRODUCT REQUIREMENTS DOCUMENT (PRD)
- 5. Finance / Keuangan
- 1. Super Admin
- Perencanaan Sistem Informasi Akademik (Kurikulum Merdeka & Tahfizh) & Keuangan Yayasan
- Illuminate\Database\Eloquent\Model
- ProsesKenaikanKelas
- AREA AUDIT & ASPEK EVALUASI
- LaporanTunggakan
- Prompt: Review Logika Bisnis — Sistem Informasi Akademik (Kurikulum Merdeka & Tahfizh) & Keuangan Yayasan
- Livewire\Component
- TabunganSiswa
- Pengeluaran
- PengajuanDanaIndex
- require
- CapaianGuru
- DanaBos
- Standar Desain UI Komponen (Buttons, Cards, Modals & Alerts) — SIAKAD
- NilaiP5
- Role
- DanaBos
- LaporanPemasukan
- manajemen-ekstrakurikuler.blade.php
- pengajuan-dana.blade.php
- manajemen-gaji-guru.blade.php
- manajemen-kurikulum-merdeka.blade.php
- require-dev
- manajemen-jadwal.blade.php
- ManajemenTagihan
- SlipGajiSaya
- composer.json
- ArusKas
- config
- Illuminate\Database\Seeder
- ManajemenRemedial
- MoneyCast.php
- manajemen-komponen-nilai.blade.php
- manajemen-surat.blade.php
- Semester
- setup
- kirimReminder({{ $item[
- psr-4
- manajemen-kalender-akademik.blade.php
- manajemen-mapel.blade.php
- TahunAjaran
- ManajemenSiswa.php
- data-alumni.blade.php
- FinancialApprovalService
- notifications-list.blade.php
- input-pembayaran.blade.php
- README.md
- NilaiTahfidz
- manajemen-koreksi-nilai.blade.php
- setoran-tahfidz.blade.php
- Kelas.php
- KomponenNilai
- SystemErrorLog
- arus-kas-masuk.blade.php
- manajemen-tagihan.blade.php
- shared/notification-dropdown.blade.php
- proses-kenaikan-kelas.blade.php
- kelola-rapor.blade.php
- penilaian-p5.blade.php
- rapor-nilai.blade.php
- deletePiket({{ $p->id }})
- input-nilai-tahfidz.blade.php
- arus-kas.blade.php
- arus-kas-keluar.blade.php
- laporan-pemasukan.blade.php
- laporan-pengeluaran.blade.php
- laporan-tunggakan.blade.php
- absensi-siswa.blade.php
- input-nilai-sumatif.blade.php
- rekap-absensi-guru.blade.php
- rekap-absensi-siswa.blade.php
- rekap-nilai.blade.php
- manajemen-guru.blade.php
- manajemen-kelas.blade.php
- manajemen-siswa.blade.php
- manajemen-user.blade.php
- input-absensi-karyawan.blade.php
- manajemen-karyawan.blade.php
- components.sidebar-icon
- dana-bos.blade.php
- manajemen-peminjaman.blade.php
- riwayat-aktivitas.blade.php
- audit-log.blade.php
- rules/graphify.md
- workflows/graphify.md
- bulk-actions.blade.php
- KepalaSekolahDanaBosMonitoringTest
- catatan-pendampingan-index.blade.php
- AcademicAndFinanceEnhancementsTest
- slip-gaji-saya.blade.php
- detail-tagihan-siswa.blade.php
- RoleMiddleware.php
- manajemen-remedial.blade.php
- JadwalPiketGuru
- InputPembayaran
- extra
- tabungan-siswa.blade.php
- {{ $closeAction }}
- Peminjaman
- capaian-pengembangan-diri.blade.php
- capaian-pengembangan-guru.blade.php
- AuditLog
- tutorial-dan-faq.blade.php
- system-error-log.blade.php
- Guru
- detail-gaji-guru.blade.php
- MonitoringAkademik.php
- AbsensiSiswa
- ArusKasCustomKategoriDanFilterCardTest
- Pengaturan
- approval-keuangan.blade.php
- OverviewPembayaran
- NilaiSas
- Siswa
- bootstrap.blade.php
- simple-bootstrap.blade.php
- ESignatureService
- Ekstrakurikuler
- Controller
- Rapor
- MataPelajaran
- livewire/simple-tailwind.blade.php
- livewire/tailwind.blade.php
- ManajemenUser
- PengajuanKoreksiNilai
- Tagihan
- ekstrakurikuler.blade.php
- Dashboard
- TagihanSpp
- DimensiP5
- SiswaKelas
- dev
- JadwalPelajaran
- monitoring-akademik.blade.php
- WithDateFilter.php
- AuditLogger.php
- AbsensiDiri
- RekapAbsensiGuru
- TutorialDanFaq
- NotificationService.php
- .save

## God Nodes (most connected - your core abstractions)
1. `Siswa` - 231 edges
2. `Role` - 140 edges
3. `User` - 113 edges
4. `Guru` - 95 edges
5. `Kelas` - 92 edges
6. `Semester` - 72 edges
7. `Tagihan` - 71 edges
8. `TahunAjaran` - 70 edges
9. `Pembayaran` - 64 edges
10. `TestCase` - 53 edges

## Surprising Connections (you probably didn't know these)
- `createUserWithRole()` --calls--> `User`  [INFERRED]
  tests/Feature/RbacAndNavigationTest.php → app/Models/User.php
- `KepalaSekolahDanaBosMonitoringTest` --references--> `DanaBos`  [EXTRACTED]
  tests/Feature/KepalaSekolahDanaBosMonitoringTest.php → app/Models/DanaBos.php
- `AcademicAndFinanceEnhancementsTest` --references--> `Guru`  [EXTRACTED]
  tests/Feature/AcademicAndFinanceEnhancementsTest.php → app/Models/Guru.php
- `EkstrakurikulerModuleTest` --references--> `Guru`  [EXTRACTED]
  tests/Feature/EkstrakurikulerModuleTest.php → app/Models/Guru.php
- `MonitoringAkademikTest` --references--> `Guru`  [EXTRACTED]
  tests/Feature/MonitoringAkademikTest.php → app/Models/Guru.php

## Import Cycles
- None detected.

## Communities (326 total, 77 thin omitted)

### Community 2 - "AuditLogger"
Cohesion: 0.21
Nodes (5): FinanceExportController, FinanceReportController, AuditLogger, bootAuditable(), Illuminate\Http\Request

### Community 4 - "User"
Cohesion: 0.09
Nodes (4): ManajemenKaryawan, User, Illuminate\Foundation\Auth\User, KenaikanKelasExportTest

### Community 5 - "1. Super Admin"
Cohesion: 0.06
Nodes (32): 1.10 Manajemen Pengaturan Sistem & TTD, 1.1 Dashboard Super Admin, 1.2 Manajemen User, 1.3 Manajemen Siswa, 1.4 Manajemen Guru, 1.5 Manajemen Kelas, 1.6 Manajemen Jadwal, 1.7 Manajemen Mata Pelajaran (+24 more)

### Community 8 - "scripts"
Cohesion: 0.13
Nodes (15): scripts, post-autoload-dump, post-create-project-cmd, post-update-cmd, pre-package-uninstall, test, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump, Illuminate\\Foundation\\ComposerScripts::prePackageUninstall (+7 more)

### Community 10 - "5. Finance / Keuangan"
Cohesion: 0.06
Nodes (32): 4.1 Dashboard Murid, 4.2 Jadwal Pelajaran Saya, 4.3 Kehadiran Saya, 4.4 Rapor & Nilai, 4.5 Tagihan SPP & Keuangan, 4.6 Ekstrakurikuler Saya, 4.7 Riwayat Aktivitas, 4. Murid / Portal Siswa (+24 more)

### Community 13 - "4. Model Data (Entitas & Field)"
Cohesion: 0.06
Nodes (30): 10. Rekomendasi Tahapan Pengembangan (Roadmap), 1. Ringkasan Sistem Sumber, 2. Peran Pengguna (Aktor) yang Disarankan, 3. Alur Kerja End-to-End, 4.10 `ekstrakurikuler` (dari sheet **EKSKUL**), 4.11 `kehadiran` & `catatan_wali_kelas`, 4.12 `leger` (dari sheet **LEGER**) — VIEW, bukan tabel fisik, 4.13 Output Cetak Rapor (dari sheet **SAMPUL RAPOR**, **ISI SEMESTER 1/2**, **RAPOR INKUL**) (+22 more)

### Community 17 - "Kelas"
Cohesion: 0.07
Nodes (4): Dashboard, ManajemenKelas, PlottingSiswaKelas, Kelas

### Community 19 - "package.json"
Cohesion: 0.09
Nodes (22): chart.js, concurrently, laravel-vite-plugin, micromodal, dependencies, chart.js, micromodal, devDependencies (+14 more)

### Community 21 - "PRODUCT REQUIREMENTS DOCUMENT (PRD)"
Cohesion: 0.05
Nodes (43): 10. KESIMPULAN, 1. DOKUMEN KONTROL & INFORMASI PROYEK, 2.1 Latar Belakang, 2.2 Visi & Nilai Utama Produk, 2. LATAR BELAKANG & VISI PRODUK, 3. PENGGUNA & PERAN SISTEM (USER PERSONAS & RBAC), 4. ARSITEKTUR INFORMASI & STRUKTUR MODUL, 5.1 Modul Tata Usaha & Kesiswaan (Master Data) (+35 more)

### Community 22 - "5. Finance / Keuangan"
Cohesion: 0.07
Nodes (29): 4.1 Lihat Jadwal Pelajaran, 4.2 Lihat Kehadiran Saya, 4.3 Lihat Rapor & Nilai, 4.4 Lihat Tagihan SPP & Keuangan, 4.5 Ekstrakurikuler Saya, 4.6 Riwayat Aktivitas Akun, 4. Murid / Portal Siswa, 5.10 Laporan Pemasukan (+21 more)

### Community 23 - "1. Super Admin"
Cohesion: 0.07
Nodes (28): 1.10 Pengaturan Sistem & TTD Elektronik, 1.1 Login & Redirect berdasarkan Role, 1.2 Manajemen User (CRUD), 1.3 Manajemen Siswa, 1.4 Manajemen Guru, 1.5 Manajemen Kelas, 1.6 Manajemen Jadwal Pelajaran, 1.7 Manajemen Mata Pelajaran (+20 more)

### Community 24 - "Perencanaan Sistem Informasi Akademik (Kurikulum Merdeka & Tahfizh) & Keuangan Yayasan"
Cohesion: 0.09
Nodes (21): 1.1 Peran Pengguna (Role Aktor), 1.2 Dual Architecture: Kurikulum Merdeka Umum vs Model Tahfizh, 1.3 Aturan Bisnis Kunci, 1. Ringkasan Kebutuhan & Aturan Bisnis, 2. Arsitektur Informasi (Sitemap per Role), 3.1 Flowchart Verifikasi Keabsahan Dokumen via QR Code, 3. Flowchart Proses Bisnis & Verifikasi QR Code, 4.1 Detail Struktur Tabel Database (+13 more)

### Community 25 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.09
Nodes (10): AbsensiSiswa, BobotNilaiGuru, Ekstrakurikuler, JadwalPelajaran, JadwalRemedial, ProyekP5, SubdimensiP5, TargetHafalanTahfidz (+2 more)

### Community 27 - "AREA AUDIT & ASPEK EVALUASI"
Cohesion: 0.14
Nodes (13): 1. Konsistensi Design System & Visual Aesthetics, 2. Responsivitas Lintas Layar (Cross-Device & Cross-Resolution), 3. Kejelasan Alur Pengguna (User Flow & Micro-Interactions), 4. Efisiensi Form Input Nilai (Ergonomi Kerja Guru), 5. Aksesibilitas (Accessibility / WCAG AA) & Feedback UI, 6. Desain Output Cetak PDF Rapor & Resi STT, AREA AUDIT & ASPEK EVALUASI, BATASAN REVIEW (+5 more)

### Community 29 - "Prompt: Review Logika Bisnis — Sistem Informasi Akademik (Kurikulum Merdeka & Tahfizh) & Keuangan Yayasan"
Cohesion: 0.15
Nodes (12): 1. Konsistensi Penilaian Kurikulum Merdeka & Auto-Narasi, 2. Isosiasi & Integrasi Model Tahfizh vs Rombel Umum, 3. Keamanan & Integritas QR Code Keabsahan Dokumen, 4. Trace End-to-End Alur Kritis, 5. Edge Cases & Penanganan Transisi State, AREA AUDIT & TUGAS REVIEW, BATASAN REVIEW, CHECKLIST TITIK RAWAN KHUSUS (Wajib Diverifikasi Statusnya) (+4 more)

### Community 30 - "Livewire\Component"
Cohesion: 0.12
Nodes (5): ArusMasuk, Dashboard, Dashboard, Livewire\Component, Livewire\WithPagination

### Community 31 - "TabunganSiswa"
Cohesion: 0.05
Nodes (10): format_rupiah(), unmask_rupiah(), Collection, ArusKasMasuk, TabunganSiswa, TabunganSaya, PemasukanKas, Tabungan (+2 more)

### Community 34 - "require"
Cohesion: 0.15
Nodes (13): require, barryvdh/laravel-dompdf, blade-ui-kit/blade-icons, chillerlan/php-qrcode, laravel/framework, laravel/octane, laravel/tinker, livewire/livewire (+5 more)

### Community 35 - "CapaianGuru"
Cohesion: 0.11
Nodes (3): CapaianPengembanganDiri, CapaianPengembanganGuru, CapaianGuru

### Community 37 - "Standar Desain UI Komponen (Buttons, Cards, Modals & Alerts) — SIAKAD"
Cohesion: 0.18
Nodes (10): 1. Standar Desain Kartu (Cards), 2. Standar Desain Tombol (Buttons), 3. Integrasi MicroModal.js untuk Alert & Konfirmasi Dialog, 4. Standar Warna Status (Status Badges), **A. Primary Content Card**, **A. Struktur HTML MicroModal (`resources/views/components/layouts/app.blade.php`)**, **B. Cara Penggunaan di JavaScript / Alpine.js**, **B. Hero / Header Banner Card** (+2 more)

### Community 39 - "Role"
Cohesion: 0.07
Nodes (15): Role, Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, Illuminate\Notifications\Notifiable, GuruDashboardTest, GuruRoleAccessTest, GuruStudentClassDisplayTest, KalenderAkademikTanggalMerahTest (+7 more)

### Community 42 - "manajemen-ekstrakurikuler.blade.php"
Cohesion: 0.22
Nodes (8): addSiswaToEkskul, closeRosterModal, openRosterModal({{ $item->id }}), removeSiswaFromEkskul({{ $r->id }}), resetForm, delete({{ $item->id }}), openCreate, openEdit({{ $item->id }})

### Community 43 - "pengajuan-dana.blade.php"
Cohesion: 0.22
Nodes (8): approveByKepalaYayasan({{ $item->id }}), approveByKoordinator({{ $item->id }}), openModal, realisasikanDana({{ $item->id }}), rejectPengajuan, closeModal, openRejectModal({{ $item->id }}), $set(

### Community 44 - "manajemen-gaji-guru.blade.php"
Cohesion: 0.08
Nodes (25): closeGenerateModal, closePayModal, deleteSalaryBuktiFoto({{ $selectedSalaryDetail->id }}), generateDrafts, openEditModal({{ $sal->id }}), openEditModal({{ $selectedSalaryDetail->id }}), openGenerateModal, openPayModal({{ $sal->id }}) (+17 more)

### Community 45 - "manajemen-kurikulum-merdeka.blade.php"
Cohesion: 0.25
Nodes (7): closeLmModal, closeTpModal, editLingkupMateri({{ $lm->id }}), editTp({{ $tp->id }}), openLmModal, openTpModal, openTpModal({{ $lm->id }})

### Community 46 - "require-dev"
Cohesion: 0.22
Nodes (9): require-dev, fakerphp/faker, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision, pestphp/pest (+1 more)

### Community 47 - "manajemen-jadwal.blade.php"
Cohesion: 0.18
Nodes (10): closeEkskulSchedule, closeForm, delete({{ $jadwal->id }}), delete({{ $sched->id }}), openCreateForDay(, openEdit({{ $jadwal->id }}), openEdit({{ $sched->id }}), openEditEkskulSchedule({{ $ek->id }}) (+2 more)

### Community 50 - "composer.json"
Cohesion: 0.14
Nodes (13): autoload-dev, psr-4, description, keywords, license, minimum-stability, name, prefer-stable (+5 more)

### Community 52 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 53 - "Illuminate\Database\Seeder"
Cohesion: 0.08
Nodes (16): CapaianGuruSeeder, DatabaseSeeder, DemoDataSeeder, FinanceSeeder, JenisTagihanSeeder, KategoriPengeluaranSeeder, KomponenNilaiSeeder, PengaturanSeeder (+8 more)

### Community 56 - "manajemen-komponen-nilai.blade.php"
Cohesion: 0.33
Nodes (5): delete({{ $komponen[, openEdit({{ $komponen[, closeModal, openCreate, $set(

### Community 57 - "manajemen-surat.blade.php"
Cohesion: 0.29
Nodes (6): deleteRiwayat({{ $r->id }}), downloadCurrentPdf, downloadPdfById({{ $r->id }}), loadRiwayatSurat({{ $r->id }}), resetPenandatanganToDefault, $set(

### Community 59 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 61 - "psr-4"
Cohesion: 0.29
Nodes (7): autoload, files, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\, app/Helpers/CurrencyHelper.php

### Community 62 - "manajemen-kalender-akademik.blade.php"
Cohesion: 0.17
Nodes (11): delete({{ $event->id }}), deleteTahunAjaran({{ $ta->id }}), openEditModal({{ $event->id }}), openEditSemester({{ $s->id }}), openTahunAjaranModal, closeModal, openCreateModal, $set( (+3 more)

### Community 63 - "manajemen-mapel.blade.php"
Cohesion: 0.40
Nodes (4): delete({{ $mapel->id }}), openEdit({{ $mapel->id }}), openCreate, $set(

### Community 64 - "TahunAjaran"
Cohesion: 0.08
Nodes (4): JenisTagihan, TahunAjaran, Command, BusinessLogicSecurityAndIntegrityTest

### Community 65 - "ManajemenSiswa.php"
Cohesion: 0.33
Nodes (4): EligibleShadowTeacher, MaxOneShadowTeacherPerClass, Closure, Illuminate\Contracts\Validation\ValidationRule

### Community 67 - "FinancialApprovalService"
Cohesion: 0.27
Nodes (3): FinancialApprovalService, ApprovalKeuanganBulkActionsTest, FinancialApprovalCancellationTest

### Community 68 - "notifications-list.blade.php"
Cohesion: 0.50
Nodes (3): markAsRead({{ $notif->id }}), markAllAsRead, $set(

### Community 69 - "input-pembayaran.blade.php"
Cohesion: 0.40
Nodes (4): pilihSiswaAndTagihan({{ $t->siswa_id }}, {{ $t->id }}), removeBuktiFoto, resetSelection, setMetodeBayar(

### Community 70 - "README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 71 - "NilaiTahfidz"
Cohesion: 0.16
Nodes (3): InputNilaiTahfidz, SetoranTahfidz, NilaiTahfidz

### Community 74 - "Kelas.php"
Cohesion: 0.18
Nodes (4): GenerateMonthlySpp, GenerateMonthlySppCommand, Pembayaran, Illuminate\Console\Command

### Community 75 - "KomponenNilai"
Cohesion: 0.09
Nodes (5): InputNilaiSiswa, PengaturanBobotNilai, ManajemenKomponenNilai, KomponenNilai, Nilai

### Community 76 - "SystemErrorLog"
Cohesion: 0.08
Nodes (5): RiwayatAktivitas, NotificationsList, ManajemenMapel, SystemErrorLog, Illuminate\Support\Collection

### Community 105 - "arus-kas-masuk.blade.php"
Cohesion: 0.20
Nodes (9): bulkDelete, closeCreateModal, deleteIncome({{ $item->raw_id }}), exportExcel, exportPdf, openCreateModal, resetFilters, selectStream( (+1 more)

### Community 106 - "manajemen-tagihan.blade.php"
Cohesion: 0.20
Nodes (9): addSiswaToBulk({{ $res->id }}), clearSelectedStudent, removeSiswaFromBulk({{ $res->id }}), removeSiswaFromBulk({{ $sel->id }}), closeCreateModal, closeEditModal, openCreateModal, selectStudent({{ $s->id }}) (+1 more)

### Community 108 - "proses-kenaikan-kelas.blade.php"
Cohesion: 0.50
Nodes (3): prosesKenaikan, $set(, toggleTinggalKelas({{ $siswa->id }})

### Community 115 - "input-nilai-tahfidz.blade.php"
Cohesion: 0.33
Nodes (5): openScoreModal({{ $s->id }}), selectTab(, $set(, setTanggalToday, setTanggalYesterday

### Community 116 - "arus-kas.blade.php"
Cohesion: 0.11
Nodes (17): closeEditExpenseModal, closeExpenseModal, closeIncomeModal, filterByCard(, openEditExpenseModal({{ $item->raw_id }}), openExpenseModal, openIncomeModal, deleteEditBukti (+9 more)

### Community 117 - "arus-kas-keluar.blade.php"
Cohesion: 0.14
Nodes (13): openEditModal({{ $item->raw_id }}), bulkDelete, closeCreateModal, closeEditModal, deleteEditBukti, deleteExpense({{ $item->raw_id }}), exportExcel, exportPdf (+5 more)

### Community 119 - "laporan-pengeluaran.blade.php"
Cohesion: 0.22
Nodes (8): closeManualReportModal, deletePengeluaran({{ $e->id }}), openManualReportModal, closeCreateModal, closePreviewPdf, openCreateModal, openPreviewPdf, $set(

### Community 121 - "absensi-siswa.blade.php"
Cohesion: 0.50
Nodes (3): setStatusAll(, setPresetDate(, setStatus({{ $index }}, 

### Community 130 - "input-absensi-karyawan.blade.php"
Cohesion: 0.50
Nodes (3): downloadTemplate, exportAttendance, setStatusAll(

### Community 160 - "dana-bos.blade.php"
Cohesion: 0.18
Nodes (10): closePreviewBukti, deleteTransaction({{ $t->id }}), openEditModal({{ $t->id }}), openPreviewBukti(, closeCreateModal, closeEditModal, deleteEditBukti, openCreateModal( (+2 more)

### Community 189 - "audit-log.blade.php"
Cohesion: 0.40
Nodes (4): openDetail({{ $log->id }}), closeDetail, $set(, setPeriodPreset(

### Community 205 - "catatan-pendampingan-index.blade.php"
Cohesion: 0.20
Nodes (9): cancelDelete, closeFormModal, confirmDelete({{ $item->id }}), deleteRecord({{ $deletingId }}), openCreateModal, openEditModal({{ $item->id }}), resetFilters, selectTab( (+1 more)

### Community 209 - "detail-tagihan-siswa.blade.php"
Cohesion: 0.15
Nodes (12): closeBuktiModal, deleteBuktiFoto, deletePembayaran({{ $rp->id }}), deleteTagihan({{ $item->id }}), openBuktiModal({{ $rp->id }}), resetBayarFilters, closeCreateModal, closeEditModal (+4 more)

### Community 211 - "manajemen-remedial.blade.php"
Cohesion: 0.33
Nodes (5): delete({{ $item->id }}), openCreate, openEdit({{ $item->id }}), $set(, updateStatus({{ $item->id }}, 

### Community 215 - "extra"
Cohesion: 0.67
Nodes (3): extra, laravel, dont-discover

### Community 218 - "tabungan-siswa.blade.php"
Cohesion: 0.22
Nodes (8): closeEditTransactionModal, closeModals, deleteTransaction({{ $htx->id }}), deleteTransaction({{ $tx->id }}), openEditTransaction({{ $htx->id }}), openEditTransaction({{ $tx->id }}), openHistoryModal({{ $siswa->id }}), openTransactionModal({{ $siswa->id }}, 

### Community 224 - "capaian-pengembangan-diri.blade.php"
Cohesion: 0.29
Nodes (6): closeDetailModal, closeModal, delete({{ $item->id }}), openCreate, openDetailModal({{ $item->id }}), openEdit({{ $item->id }})

### Community 225 - "capaian-pengembangan-guru.blade.php"
Cohesion: 0.29
Nodes (6): openEvaluateFromDetail, openEvaluateModal({{ $item->id }}), closeDetailModal, closeModal, delete({{ $item->id }}), openDetailModal({{ $item->id }})

### Community 230 - "system-error-log.blade.php"
Cohesion: 0.50
Nodes (3): clearLog, closeErrorDetail, openErrorDetail({{ $log[

### Community 235 - "Guru"
Cohesion: 0.08
Nodes (3): ManajemenGuru, Guru, SuperAdmin2MonitoringGuruTest

### Community 252 - "detail-gaji-guru.blade.php"
Cohesion: 0.20
Nodes (9): deleteSalaryBuktiFoto({{ $sd->id }}), openPreview({{ $sd->id }}), closePreview, deleteSalary({{ $sal->id }}), deleteSelected, openDetailModal({{ $sal->id }}), openPreview({{ $sal->id }}), $set( (+1 more)

### Community 256 - "AbsensiSiswa"
Cohesion: 0.10
Nodes (4): AbsensiSiswa, Dashboard, KehadiranSaya, RekapAbsensiSiswa

### Community 259 - "Pengaturan"
Cohesion: 0.14
Nodes (3): ProfilSaya, ManajemenPengaturan, Pengaturan

### Community 260 - "approval-keuangan.blade.php"
Cohesion: 0.09
Nodes (22): approve, bulkApprove, bulkCancel, cancelApproval, closeApproveModal, closeBulkApproveModal, closeBulkCancelModal, closeCancelModal (+14 more)

### Community 267 - "NilaiSas"
Cohesion: 0.12
Nodes (5): InputNilaiSumatif, RaporNilai, NilaiSas, NilaiSumatifTp, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 268 - "Siswa"
Cohesion: 0.06
Nodes (5): ManajemenSiswa, DataAlumni, Siswa, Student, AutoNarasiService

### Community 274 - "bootstrap.blade.php"
Cohesion: 0.50
Nodes (3): gotoPage({{ $page }}, , nextPage(, previousPage(

### Community 276 - "simple-bootstrap.blade.php"
Cohesion: 0.50
Nodes (3): nextPage(, previousPage(, setPage(

### Community 278 - "Ekstrakurikuler"
Cohesion: 0.09
Nodes (6): Ekstrakurikuler, EkstrakurikulerSaya, ManajemenEkstrakurikuler, KegiatanEkstrakurikuler, PresensiEkstrakurikuler, SiswaEkstrakurikuler

### Community 279 - "Controller"
Cohesion: 0.20
Nodes (4): Controller, DocumentVerificationController, PendampinganReportController, VerifikasiDokumenController

### Community 280 - "Rapor"
Cohesion: 0.12
Nodes (5): RaporPdfController, KelolaRapor, Rapor, RaporDetail, RaporTahfidzDetail

### Community 281 - "MataPelajaran"
Cohesion: 0.09
Nodes (7): ManajemenKurikulumMerdeka, MonitoringAkademik, LingkupMateri, MataPelajaran, TujuanPembelajaran, MonitoringAkademikTest, PenilaianTpDanSasTest

### Community 283 - "livewire/simple-tailwind.blade.php"
Cohesion: 0.50
Nodes (3): nextPage(, previousPage(, setPage(

### Community 284 - "livewire/tailwind.blade.php"
Cohesion: 0.50
Nodes (3): gotoPage({{ $page }}, , nextPage(, previousPage(

### Community 285 - "ManajemenUser"
Cohesion: 0.26
Nodes (3): ManajemenUser, UserFactory, Illuminate\Database\Eloquent\Factories\Factory

### Community 287 - "PengajuanKoreksiNilai"
Cohesion: 0.22
Nodes (3): ManajemenKoreksiNilai, ManajemenKoreksiNilai, PengajuanKoreksiNilai

### Community 299 - "Tagihan"
Cohesion: 0.11
Nodes (4): Tagihan, AutomatedSppGenerationTest, SuperAdmin2AndFinancialApprovalTest, TagihanZeroNominalAndPaymentDeletionTest

### Community 300 - "ekstrakurikuler.blade.php"
Cohesion: 0.25
Nodes (7): deleteKegiatan({{ $currentKegiatan->id }}), openCreateKegiatan, $set(, savePresensiDanNilaiSesi, saveScore({{ $m->id }}), selectEkskul({{ $ekskul->id }}), setSemuaHadir

### Community 308 - "dev"
Cohesion: 0.67
Nodes (3): dev, Composer\\Config::disableProcessTimeout, npx concurrently -c \"#93c5fd,#c4b5fd,#fb7185,#fdba74\" \"php artisan serve\" \"php artisan queue:listen --tries=1 --timeout=0\" \"php artisan pail --timeout=0\" \"npm run dev\" --names=server,queue,logs,vite --kill-others

### Community 309 - "JadwalPelajaran"
Cohesion: 0.15
Nodes (3): Dashboard, JadwalMengajar, JadwalPelajaran

### Community 313 - "monitoring-akademik.blade.php"
Cohesion: 0.50
Nodes (3): inspectNilai({{ $p[, setTab(, setAbsenSubTab(

### Community 320 - "AuditLogger.php"
Cohesion: 0.18
Nodes (3): Login, AppServiceProvider, Illuminate\Support\ServiceProvider

## Knowledge Gaps
- **596 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+591 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **77 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Siswa` connect `Siswa` to `AbsensiSiswa`, `CatatanPendampinganIndex`, `AuditLogger`, `Pengeluaran.php`, `User`, `DetailTagihanSiswa`, `GuruMapelKelas`, `OverviewPembayaran`, `NilaiSas`, `ManajemenSurat`, `Kelas`, `Tabungan.php`, `Controller`, `Rapor`, `MataPelajaran`, `Illuminate\Database\Eloquent\Model`, `ProsesKenaikanKelas`, `Livewire\Component`, `TabunganSiswa`, `NilaiP5`, `Role`, `Tagihan`, `Pengaturan.php`, `ManajemenTagihan`, `Semester.php`, `ManajemenRemedial`, `Semester`, `TahunAjaran`, `ManajemenSiswa.php`, `FinancialApprovalService`, `NilaiTahfidz`, `Kelas.php`, `KomponenNilai`, `InputPembayaran`, `Guru`, `MonitoringAkademik.php`?**
  _High betweenness centrality (0.062) - this node is a cross-community bridge._
- **Why does `Guru` connect `Guru` to `CatatanPendampinganIndex`, `Pengeluaran.php`, `User`, `GuruMapelKelas`, `AbsensiGuru`, `Siswa`, `DetailGajiGuru`, `ManajemenSurat`, `Kelas`, `GajiGuru`, `Illuminate\Database\Eloquent\Model`, `MataPelajaran`, `Livewire\Component`, `CapaianGuru`, `Role`, `ManajemenRemedial`, `Semester`, `TahunAjaran`, `ManajemenSiswa.php`, `RekapAbsensiGuru`, `AcademicAndFinanceEnhancementsTest`, `JadwalPiketGuru`, `Peminjaman`?**
  _High betweenness centrality (0.029) - this node is a cross-community bridge._
- **Why does `User` connect `User` to `AuditLogger`, `Pengeluaran.php`, `ArusKasCustomKategoriDanFilterCardTest`, `GuruMapelKelas`, `AbsensiGuru`, `Siswa`, `Notifikasi`, `ESignatureService`, `Illuminate\Database\Eloquent\Model`, `MataPelajaran`, `ManajemenUser`, `Livewire\Component`, `DanaBos`, `Role`, `Tagihan`, `Pengaturan.php`, `Illuminate\Database\Seeder`, `Semester`, `TahunAjaran`, `FinancialApprovalService`, `NotificationService.php`, `Kelas.php`, `KepalaSekolahDanaBosMonitoringTest`, `AcademicAndFinanceEnhancementsTest`, `Guru`?**
  _High betweenness centrality (0.020) - this node is a cross-community bridge._
- **Are the 43 inferred relationships involving `User` (e.g. with `.cetakResi()` and `.save()`) actually correct?**
  _`User` has 43 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _596 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `CatatanPendampinganIndex` be split into smaller, more focused modules?**
  _Cohesion score 0.07816091954022988 - nodes in this community are weakly interconnected._
- **Should `User` be split into smaller, more focused modules?**
  _Cohesion score 0.08735632183908046 - nodes in this community are weakly interconnected._