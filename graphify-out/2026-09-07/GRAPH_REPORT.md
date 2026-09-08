# Graph Report - web-sistem-akademik  (2026-09-07)

## Corpus Check
- 449 files · ~343,997 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 2340 nodes · 4701 edges · 304 communities (235 shown, 69 thin omitted)
- Extraction: 85% EXTRACTED · 15% INFERRED · 0% AMBIGUOUS · INFERRED: 693 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `c57cc364`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Notifikasi
- ManajemenJadwal
- Illuminate\Http\Request
- Pembayaran
- 1. Super Admin
- DetailTagihanSiswa
- Illuminate\Database\Seeder
- scripts
- ManajemenKalenderAkademik
- 5. Finance / Keuangan
- InputAbsensiKaryawan
- ApprovalKeuangan
- 4. Model Data (Entitas & Field)
- DetailGajiGuru
- ManajemenSurat
- MataPelajaran
- ManajemenGajiGuru
- package.json
- LaporanPengeluaran
- PRODUCT REQUIREMENTS DOCUMENT (PRD)
- 5. Finance / Keuangan
- 1. Super Admin
- Perencanaan Sistem Informasi Akademik (Kurikulum Merdeka & Tahfizh) & Keuangan Yayasan
- Auditable.php
- Livewire\Component
- AREA AUDIT & ASPEK EVALUASI
- ManajemenRemedial
- Prompt: Review Logika Bisnis — Sistem Informasi Akademik (Kurikulum Merdeka & Tahfizh) & Keuangan Yayasan
- BobotNilaiGuru
- TabunganSiswa
- ArusKasKeluar
- PengajuanDana
- require
- CapaianGuru
- KepalaSekolahDanaBosMonitoringTest
- Standar Desain UI Komponen (Buttons, Cards, Modals & Alerts) — SIAKAD
- NilaiP5
- Pengeluaran
- JadwalPelajaran
- LaporanPemasukan
- LaporanTunggakan
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
- RekapAbsensiSiswa
- ProsesKenaikanKelas
- AppServiceProvider
- manajemen-komponen-nilai.blade.php
- manajemen-surat.blade.php
- RiwayatAktivitas
- setup
- kirimReminder({{ $item[
- psr-4
- manajemen-kalender-akademik.blade.php
- manajemen-mapel.blade.php
- Nilai
- NotificationDropdown
- data-alumni.blade.php
- UserFactory.php
- notifications-list.blade.php
- input-pembayaran.blade.php
- README.md
- NilaiTahfidz
- manajemen-koreksi-nilai.blade.php
- setoran-tahfidz.blade.php
- InputPembayaran
- AbsensiSiswa
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
- dana-bos.blade.php
- manajemen-peminjaman.blade.php
- riwayat-aktivitas.blade.php
- audit-log.blade.php
- rules/graphify.md
- workflows/graphify.md
- bulk-actions.blade.php
- NotificationsList
- Guru
- KomponenNilai
- slip-gaji-saya.blade.php
- detail-tagihan-siswa.blade.php
- PengajuanKoreksiNilai
- manajemen-remedial.blade.php
- JadwalPiketGuru
- TutorialDanFaq
- RoleMiddleware.php
- tabungan-siswa.blade.php
- {{ $closeAction }}
- ManajemenPeminjaman
- capaian-pengembangan-diri.blade.php
- capaian-pengembangan-guru.blade.php
- AuditLog
- tutorial-dan-faq.blade.php
- system-error-log.blade.php
- User
- detail-gaji-guru.blade.php
- DimensiP5
- extra
- TagihanSpp
- Pengaturan
- approval-keuangan.blade.php
- post-autoload-dump
- Siswa
- bootstrap.blade.php
- GajiGuru
- simple-bootstrap.blade.php
- ESignatureService
- SiswaEkstrakurikuler
- Controller
- .run
- livewire/simple-tailwind.blade.php
- livewire/tailwind.blade.php
- ManajemenUser
- GuruMapelKelas
- ekstrakurikuler.blade.php
- Livewire\WithPagination
- Tagihan
- Kelas
- TahunAjaran

## God Nodes (most connected - your core abstractions)
1. `Siswa` - 117 edges
2. `Tagihan` - 106 edges
3. `User` - 81 edges
4. `Kelas` - 78 edges
5. `Guru` - 73 edges
6. `TahunAjaran` - 58 edges
7. `Semester` - 52 edges
8. `Role` - 45 edges
9. `GajiGuru` - 44 edges
10. `MataPelajaran` - 44 edges

## Surprising Connections (you probably didn't know these)
- `KepalaSekolahDanaBosMonitoringTest` --references--> `DanaBos`  [EXTRACTED]
  tests/Feature/KepalaSekolahDanaBosMonitoringTest.php → app/Models/DanaBos.php
- `AcademicAndFinanceEnhancementsTest` --references--> `Guru`  [EXTRACTED]
  tests/Feature/AcademicAndFinanceEnhancementsTest.php → app/Models/Guru.php
- `PenilaianTpDanSasTest` --references--> `Guru`  [EXTRACTED]
  tests/Feature/PenilaianTpDanSasTest.php → app/Models/Guru.php
- `TagihanZeroNominalAndPaymentDeletionTest` --references--> `JenisTagihan`  [EXTRACTED]
  tests/Feature/TagihanZeroNominalAndPaymentDeletionTest.php → app/Models/JenisTagihan.php
- `PenilaianTpDanSasTest` --references--> `Kelas`  [EXTRACTED]
  tests/Feature/PenilaianTpDanSasTest.php → app/Models/Kelas.php

## Import Cycles
- None detected.

## Communities (304 total, 69 thin omitted)

### Community 0 - "Notifikasi"
Cohesion: 0.13
Nodes (4): OverviewPembayaran, Notifikasi, AuditLogger, bootAuditable()

### Community 2 - "Illuminate\Http\Request"
Cohesion: 0.20
Nodes (3): FinanceExportController, FinanceReportController, Illuminate\Http\Request

### Community 4 - "Pembayaran"
Cohesion: 0.14
Nodes (3): Dashboard, Pembayaran, TagihanZeroNominalAndPaymentDeletionTest

### Community 5 - "1. Super Admin"
Cohesion: 0.06
Nodes (32): 1.10 Manajemen Pengaturan Sistem & TTD, 1.1 Dashboard Super Admin, 1.2 Manajemen User, 1.3 Manajemen Siswa, 1.4 Manajemen Guru, 1.5 Manajemen Kelas, 1.6 Manajemen Jadwal, 1.7 Manajemen Mata Pelajaran (+24 more)

### Community 7 - "Illuminate\Database\Seeder"
Cohesion: 0.09
Nodes (15): CapaianGuruSeeder, DatabaseSeeder, DemoDataSeeder, FinanceSeeder, JenisTagihanSeeder, KategoriPengeluaranSeeder, KomponenNilaiSeeder, PengaturanSeeder (+7 more)

### Community 8 - "scripts"
Cohesion: 0.13
Nodes (15): scripts, dev, post-create-project-cmd, post-update-cmd, pre-package-uninstall, test, Composer\\Config::disableProcessTimeout, Illuminate\\Foundation\\ComposerScripts::prePackageUninstall (+7 more)

### Community 10 - "5. Finance / Keuangan"
Cohesion: 0.06
Nodes (32): 4.1 Dashboard Murid, 4.2 Jadwal Pelajaran Saya, 4.3 Kehadiran Saya, 4.4 Rapor & Nilai, 4.5 Tagihan SPP & Keuangan, 4.6 Ekstrakurikuler Saya, 4.7 Riwayat Aktivitas, 4. Murid / Portal Siswa (+24 more)

### Community 11 - "InputAbsensiKaryawan"
Cohesion: 0.10
Nodes (5): AbsensiDiri, RekapAbsensiGuru, InputAbsensiKaryawan, AbsensiGuru, Livewire\WithFileUploads

### Community 12 - "ApprovalKeuangan"
Cohesion: 0.07
Nodes (4): ApprovalKeuanganIndex, ApprovalKeuangan, FinancialApprovalService, FinancialApprovalCancellationTest

### Community 13 - "4. Model Data (Entitas & Field)"
Cohesion: 0.06
Nodes (30): 10. Rekomendasi Tahapan Pengembangan (Roadmap), 1. Ringkasan Sistem Sumber, 2. Peran Pengguna (Aktor) yang Disarankan, 3. Alur Kerja End-to-End, 4.10 `ekstrakurikuler` (dari sheet **EKSKUL**), 4.11 `kehadiran` & `catatan_wali_kelas`, 4.12 `leger` (dari sheet **LEGER**) — VIEW, bukan tabel fisik, 4.13 Output Cetak Rapor (dari sheet **SAMPUL RAPOR**, **ISI SEMESTER 1/2**, **RAPOR INKUL**) (+22 more)

### Community 17 - "MataPelajaran"
Cohesion: 0.05
Nodes (13): InputNilaiSumatif, ManajemenKurikulumMerdeka, RaporNilai, ManajemenMapel, LingkupMateri, MataPelajaran, NilaiSas, NilaiSumatifTp (+5 more)

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

### Community 25 - "Auditable.php"
Cohesion: 0.10
Nodes (9): AbsensiSiswa, Ekstrakurikuler, JadwalPelajaran, JadwalRemedial, ProyekP5, SubdimensiP5, TargetHafalanTahfidz, Illuminate\Database\Eloquent\Factories\HasFactory (+1 more)

### Community 26 - "Livewire\Component"
Cohesion: 0.09
Nodes (6): Login, Dashboard, EkstrakurikulerSaya, TabunganSaya, Dashboard, Livewire\Component

### Community 27 - "AREA AUDIT & ASPEK EVALUASI"
Cohesion: 0.14
Nodes (13): 1. Konsistensi Design System & Visual Aesthetics, 2. Responsivitas Lintas Layar (Cross-Device & Cross-Resolution), 3. Kejelasan Alur Pengguna (User Flow & Micro-Interactions), 4. Efisiensi Form Input Nilai (Ergonomi Kerja Guru), 5. Aksesibilitas (Accessibility / WCAG AA) & Feedback UI, 6. Desain Output Cetak PDF Rapor & Resi STT, AREA AUDIT & ASPEK EVALUASI, BATASAN REVIEW (+5 more)

### Community 29 - "Prompt: Review Logika Bisnis — Sistem Informasi Akademik (Kurikulum Merdeka & Tahfizh) & Keuangan Yayasan"
Cohesion: 0.15
Nodes (12): 1. Konsistensi Penilaian Kurikulum Merdeka & Auto-Narasi, 2. Isosiasi & Integrasi Model Tahfizh vs Rombel Umum, 3. Keamanan & Integritas QR Code Keabsahan Dokumen, 4. Trace End-to-End Alur Kritis, 5. Edge Cases & Penanganan Transisi State, AREA AUDIT & TUGAS REVIEW, BATASAN REVIEW, CHECKLIST TITIK RAWAN KHUSUS (Wajib Diverifikasi Statusnya) (+4 more)

### Community 31 - "TabunganSiswa"
Cohesion: 0.07
Nodes (4): ArusKasMasuk, TabunganSiswa, PemasukanKas, Tabungan

### Community 34 - "require"
Cohesion: 0.15
Nodes (13): require, barryvdh/laravel-dompdf, blade-ui-kit/blade-icons, chillerlan/php-qrcode, laravel/framework, laravel/octane, laravel/tinker, livewire/livewire (+5 more)

### Community 35 - "CapaianGuru"
Cohesion: 0.11
Nodes (3): CapaianPengembanganDiri, CapaianPengembanganGuru, CapaianGuru

### Community 37 - "Standar Desain UI Komponen (Buttons, Cards, Modals & Alerts) — SIAKAD"
Cohesion: 0.18
Nodes (10): 1. Standar Desain Kartu (Cards), 2. Standar Desain Tombol (Buttons), 3. Integrasi MicroModal.js untuk Alert & Konfirmasi Dialog, 4. Standar Warna Status (Status Badges), **A. Primary Content Card**, **A. Struktur HTML MicroModal (`resources/views/components/layouts/app.blade.php`)**, **B. Cara Penggunaan di JavaScript / Alpine.js**, **B. Hero / Header Banner Card** (+2 more)

### Community 39 - "Pengeluaran"
Cohesion: 0.17
Nodes (3): Peminjaman, Pengeluaran, NotificationService

### Community 40 - "JadwalPelajaran"
Cohesion: 0.13
Nodes (4): Dashboard, JadwalMengajar, Dashboard, JadwalPelajaran

### Community 43 - "pengajuan-dana.blade.php"
Cohesion: 0.22
Nodes (8): approveByKepalaYayasan({{ $item->id }}), approveByKoordinator({{ $item->id }}), openModal, realisasikanDana({{ $item->id }}), rejectPengajuan, closeModal, openRejectModal({{ $item->id }}), $set(

### Community 44 - "manajemen-gaji-guru.blade.php"
Cohesion: 0.09
Nodes (22): closeGenerateModal, generateDrafts, openEditModal({{ $sal->id }}), openEditModal({{ $selectedSalaryDetail->id }}), openGenerateModal, openPreview({{ $selectedSalaryDetail->id }}), paySalary({{ $sal->id }}), paySalary({{ $selectedSalaryDetail->id }}) (+14 more)

### Community 45 - "manajemen-kurikulum-merdeka.blade.php"
Cohesion: 0.25
Nodes (7): closeLmModal, closeTpModal, editLingkupMateri({{ $lm->id }}), editTp({{ $tp->id }}), openLmModal, openTpModal, openTpModal({{ $lm->id }})

### Community 46 - "require-dev"
Cohesion: 0.22
Nodes (9): require-dev, fakerphp/faker, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision, pestphp/pest (+1 more)

### Community 47 - "manajemen-jadwal.blade.php"
Cohesion: 0.22
Nodes (8): closeForm, delete({{ $jadwal->id }}), delete({{ $sched->id }}), openCreateForDay(, openEdit({{ $jadwal->id }}), openEdit({{ $sched->id }}), openCreate, $set(

### Community 48 - "ManajemenTagihan"
Cohesion: 0.06
Nodes (4): DanaBos, ManajemenTagihan, setPeriode(), updatedFilterPeriode()

### Community 50 - "composer.json"
Cohesion: 0.14
Nodes (13): autoload-dev, psr-4, description, keywords, license, minimum-stability, name, prefer-stable (+5 more)

### Community 52 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 56 - "manajemen-komponen-nilai.blade.php"
Cohesion: 0.33
Nodes (5): delete({{ $komponen[, openEdit({{ $komponen[, closeModal, openCreate, $set(

### Community 57 - "manajemen-surat.blade.php"
Cohesion: 0.33
Nodes (5): deleteRiwayat({{ $r->id }}), downloadCurrentPdf, downloadPdfById({{ $r->id }}), loadRiwayatSurat({{ $r->id }}), $set(

### Community 59 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 61 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 62 - "manajemen-kalender-akademik.blade.php"
Cohesion: 0.17
Nodes (11): delete({{ $event->id }}), deleteTahunAjaran({{ $ta->id }}), openEditModal({{ $event->id }}), openEditSemester({{ $s->id }}), openTahunAjaranModal, closeModal, openCreateModal, $set( (+3 more)

### Community 63 - "manajemen-mapel.blade.php"
Cohesion: 0.40
Nodes (4): delete({{ $mapel->id }}), openEdit({{ $mapel->id }}), openCreate, $set(

### Community 68 - "notifications-list.blade.php"
Cohesion: 0.50
Nodes (3): markAsRead({{ $notif->id }}), markAllAsRead, $set(

### Community 69 - "input-pembayaran.blade.php"
Cohesion: 0.50
Nodes (3): pilihSiswaAndTagihan({{ $t->siswa_id }}, {{ $t->id }}), resetSelection, setMetodeBayar(

### Community 70 - "README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 71 - "NilaiTahfidz"
Cohesion: 0.11
Nodes (4): InputNilaiTahfidz, SetoranTahfidz, NilaiTahfidz, RaporTahfidzDetail

### Community 105 - "arus-kas-masuk.blade.php"
Cohesion: 0.25
Nodes (7): bulkDelete, closeCreateModal, deleteIncome({{ $item->raw_id }}), exportExcel, exportPdf, openCreateModal, selectStream(

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
Cohesion: 0.17
Nodes (11): closeExpenseModal, closeIncomeModal, openExpenseModal, openIncomeModal, deleteExpense({{ $item->raw_id }}), deleteIncome({{ $item->raw_id }}), exportExcel, exportPdf (+3 more)

### Community 117 - "arus-kas-keluar.blade.php"
Cohesion: 0.22
Nodes (8): bulkDelete, closeCreateModal, deleteExpense({{ $item->raw_id }}), exportExcel, exportPdf, openCreateModal, selectStream(, $toggle(

### Community 119 - "laporan-pengeluaran.blade.php"
Cohesion: 0.25
Nodes (7): closeManualReportModal, deletePengeluaran({{ $e->id }}), openManualReportModal, closeCreateModal, closePreviewPdf, openCreateModal, openPreviewPdf

### Community 121 - "absensi-siswa.blade.php"
Cohesion: 0.50
Nodes (3): setStatusAll(, setPresetDate(, setStatus({{ $index }}, 

### Community 130 - "input-absensi-karyawan.blade.php"
Cohesion: 0.50
Nodes (3): downloadTemplate, exportAttendance, setStatusAll(

### Community 160 - "dana-bos.blade.php"
Cohesion: 0.33
Nodes (5): deleteTransaction({{ $t->id }}), closeCreateModal, openCreateModal(, selectTab(, $set(

### Community 189 - "audit-log.blade.php"
Cohesion: 0.40
Nodes (4): openDetail({{ $log->id }}), closeDetail, $set(, setPeriodPreset(

### Community 205 - "Guru"
Cohesion: 0.08
Nodes (3): ManajemenGuru, ManajemenKelas, Guru

### Community 209 - "detail-tagihan-siswa.blade.php"
Cohesion: 0.22
Nodes (8): deletePembayaran({{ $rp->id }}), deleteTagihan({{ $item->id }}), openEditModal({{ $item->id }}), resetBayarFilters, resetFilters, closeCreateModal, closeEditModal, openCreateModal

### Community 210 - "PengajuanKoreksiNilai"
Cohesion: 0.22
Nodes (3): ManajemenKoreksiNilai, ManajemenKoreksiNilai, PengajuanKoreksiNilai

### Community 211 - "manajemen-remedial.blade.php"
Cohesion: 0.33
Nodes (5): delete({{ $item->id }}), openCreate, openEdit({{ $item->id }}), $set(, updateStatus({{ $item->id }}, 

### Community 215 - "RoleMiddleware.php"
Cohesion: 0.60
Nodes (3): RoleMiddleware, Closure, Symfony\Component\HttpFoundation\Response

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

### Community 235 - "User"
Cohesion: 0.08
Nodes (9): ManajemenKaryawan, Role, User, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, KalenderAkademikTanggalMerahTest, KalenderAkademikTest, KenaikanKelasExportTest (+1 more)

### Community 252 - "detail-gaji-guru.blade.php"
Cohesion: 0.25
Nodes (7): openPreview({{ $sd->id }}), closePreview, deleteSalary({{ $sal->id }}), deleteSelected, openDetailModal({{ $sal->id }}), openPreview({{ $sal->id }}), $set(

### Community 256 - "extra"
Cohesion: 0.67
Nodes (3): extra, laravel, dont-discover

### Community 259 - "Pengaturan"
Cohesion: 0.14
Nodes (3): ProfilSaya, ManajemenPengaturan, Pengaturan

### Community 260 - "approval-keuangan.blade.php"
Cohesion: 0.12
Nodes (15): approve, cancelApproval, closeApproveModal, closeCancelModal, closeRejectModal, openApproveModal({{ $item->id }}), openApproveModal({{ $selectedApproval->id }}), openCancelModal({{ $item->id }}) (+7 more)

### Community 264 - "post-autoload-dump"
Cohesion: 0.67
Nodes (3): post-autoload-dump, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump, @php artisan package:discover --ansi

### Community 268 - "Siswa"
Cohesion: 0.08
Nodes (3): ManajemenSiswa, DataAlumni, Siswa

### Community 274 - "bootstrap.blade.php"
Cohesion: 0.50
Nodes (3): gotoPage({{ $page }}, , nextPage(, previousPage(

### Community 276 - "simple-bootstrap.blade.php"
Cohesion: 0.50
Nodes (3): nextPage(, previousPage(, setPage(

### Community 279 - "Controller"
Cohesion: 0.29
Nodes (3): Controller, DocumentVerificationController, VerifikasiDokumenController

### Community 280 - ".run"
Cohesion: 0.11
Nodes (5): RaporPdfController, KelolaRapor, Rapor, RaporDetail, PenilaianTpDanSasTest

### Community 283 - "livewire/simple-tailwind.blade.php"
Cohesion: 0.50
Nodes (3): nextPage(, previousPage(, setPage(

### Community 284 - "livewire/tailwind.blade.php"
Cohesion: 0.50
Nodes (3): gotoPage({{ $page }}, , nextPage(, previousPage(

### Community 307 - "Livewire\WithPagination"
Cohesion: 0.15
Nodes (4): ArusMasuk, DanaBos, Illuminate\Database\Eloquent\SoftDeletes, Livewire\WithPagination

### Community 308 - "Tagihan"
Cohesion: 0.10
Nodes (12): GenerateMonthlySpp, GenerateMonthlySppCommand, Tagihan, Illuminate\Console\Command, Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, AutomatedSppGenerationTest, GuruDashboardTest (+4 more)

### Community 309 - "Kelas"
Cohesion: 0.10
Nodes (3): Dashboard, PlottingSiswaKelas, Kelas

### Community 310 - "TahunAjaran"
Cohesion: 0.08
Nodes (5): JenisTagihan, Semester, TahunAjaran, Command, SuperAdmin2AndFinancialApprovalTest

## Knowledge Gaps
- **530 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+525 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **69 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Siswa` connect `Siswa` to `Notifikasi`, `Illuminate\Http\Request`, `Pembayaran`, `DetailTagihanSiswa`, `ApprovalKeuangan`, `ManajemenSurat`, `Siswa.php`, `MataPelajaran`, `.run`, `Auditable.php`, `ManajemenRemedial`, `GuruMapelKelas`, `TabunganSiswa`, `NilaiP5`, `ManajemenTagihan`, `Livewire\WithPagination`, `Tagihan`, `RekapAbsensiSiswa`, `TahunAjaran`, `Kelas`, `ProsesKenaikanKelas`, `Nilai`, `NilaiTahfidz`, `InputPembayaran`, `AbsensiSiswa`, `Guru`, `User`?**
  _High betweenness centrality (0.041) - this node is a cross-community bridge._
- **Why does `Guru` connect `Guru` to `User.php`, `InputAbsensiKaryawan`, `Siswa`, `DetailGajiGuru`, `ManajemenSurat`, `MataPelajaran`, `ManajemenGajiGuru`, `GajiGuru`, `.run`, `Auditable.php`, `ManajemenRemedial`, `CapaianGuru`, `Livewire\WithPagination`, `Kelas`, `TahunAjaran`, `ProsesKenaikanKelas`, `NilaiTahfidz`, `JadwalPiketGuru`, `ManajemenPeminjaman`, `User`?**
  _High betweenness centrality (0.027) - this node is a cross-community bridge._
- **Why does `TahunAjaran` connect `TahunAjaran` to `Notifikasi`, `Illuminate\Http\Request`, `KepalaSekolahDanaBosMonitoringTest`, `Pembayaran`, `DetailTagihanSiswa`, `NilaiTahfidz`, `ManajemenKalenderAkademik`, `LaporanTunggakan`, `User`, `ManajemenTagihan`, `MataPelajaran`, `Livewire\WithPagination`, `Tagihan`, `GajiGuru`, `ProsesKenaikanKelas`, `.run`, `Auditable.php`?**
  _High betweenness centrality (0.024) - this node is a cross-community bridge._
- **Are the 72 inferred relationships involving `Siswa` (e.g. with `.handle()` and `.handle()`) actually correct?**
  _`Siswa` has 72 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _530 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Notifikasi` be split into smaller, more focused modules?**
  _Cohesion score 0.1286549707602339 - nodes in this community are weakly interconnected._
- **Should `User.php` be split into smaller, more focused modules?**
  _Cohesion score 0.11818181818181818 - nodes in this community are weakly interconnected._