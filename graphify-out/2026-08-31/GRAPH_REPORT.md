# Graph Report - web-sistem-akademik  (2026-08-31)

## Corpus Check
- 424 files · ~308,784 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 2136 nodes · 4127 edges · 298 communities (218 shown, 80 thin omitted)
- Extraction: 85% EXTRACTED · 15% INFERRED · 0% AMBIGUOUS · INFERRED: 630 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `1677ff85`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- MataPelajaran
- ManajemenJadwal
- Illuminate\Http\Request
- TestCase
- Tagihan
- 1. Super Admin
- DetailTagihanSiswa
- scripts
- ManajemenKalenderAkademik
- 5. Finance / Keuangan
- AbsensiGuru
- Kelas
- 4. Model Data (Entitas & Field)
- DetailGajiGuru
- ManajemenSurat
- DanaBos
- Livewire\Component
- ManajemenGajiGuru
- package.json
- LaporanPengeluaran
- PRODUCT REQUIREMENTS DOCUMENT (PRD)
- 5. Finance / Keuangan
- 1. Super Admin
- Perencanaan Sistem Informasi Akademik (Kurikulum Merdeka & Tahfizh) & Keuangan Yayasan
- Illuminate\Database\Eloquent\Model
- NilaiTahfidz
- AREA AUDIT & ASPEK EVALUASI
- TabunganSiswa
- Prompt: Review Logika Bisnis — Sistem Informasi Akademik (Kurikulum Merdeka & Tahfizh) & Keuangan Yayasan
- BobotNilaiGuru
- Semester
- Illuminate\Database\Eloquent\Factories\HasFactory
- PengajuanDana
- require
- CapaianGuru
- TahunAjaran
- Standar Desain UI Komponen (Buttons, Cards, Modals & Alerts) — SIAKAD
- NilaiP5
- Pengeluaran
- Notifikasi
- LaporanPemasukan
- ManajemenRemedial
- pengajuan-dana.blade.php
- manajemen-gaji-guru.blade.php
- manajemen-kurikulum-merdeka.blade.php
- require-dev
- manajemen-jadwal.blade.php
- ManajemenTagihan
- SlipGajiSaya
- composer.json
- OverviewPembayaran
- config
- .run
- Pengaturan
- AppServiceProvider
- manajemen-komponen-nilai.blade.php
- manajemen-surat.blade.php
- Rapor
- setup
- overview-pembayaran.blade.php
- psr-4
- manajemen-kalender-akademik.blade.php
- manajemen-mapel.blade.php
- ArusKasKeluar
- ManajemenUser
- data-alumni.blade.php
- UserFactory.php
- notifications-list.blade.php
- input-pembayaran.blade.php
- README.md
- Dashboard
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
- ArusKasMasuk
- dana-bos.blade.php
- manajemen-peminjaman.blade.php
- audit-log.blade.php
- rules/graphify.md
- workflows/graphify.md
- bulk-actions.blade.php
- InputNilaiSiswa
- Guru
- KomponenNilai
- slip-gaji-saya.blade.php
- detail-tagihan-siswa.blade.php
- KelolaRapor
- manajemen-remedial.blade.php
- Illuminate\Database\Seeder
- PengajuanKoreksiNilai
- TutorialDanFaq
- Nilai
- tabungan-siswa.blade.php
- {{ $closeAction }}
- ManajemenPeminjaman
- ProsesKenaikanKelas
- capaian-pengembangan-diri.blade.php
- capaian-pengembangan-guru.blade.php
- AuditLog
- tutorial-dan-faq.blade.php
- system-error-log.blade.php
- ESignatureService
- .handle
- User
- detail-gaji-guru.blade.php
- AutomatedSppGenerationTest
- ArusKas
- .mount
- .mount
- TagihanSpp
- WithDateFilter.php
- post-create-project-cmd
- RekapAbsensiSiswa
- Siswa
- bootstrap.blade.php
- DimensiP5
- simple-bootstrap.blade.php
- ManajemenKelas
- LaporanTunggakan
- extra
- ManajemenSiswa
- ManajemenKaryawan
- GuruMapelKelas
- livewire/simple-tailwind.blade.php
- livewire/tailwind.blade.php
- KalenderAkademikTest
- Dashboard

## God Nodes (most connected - your core abstractions)
1. `Siswa` - 104 edges
2. `TahunAjaran` - 81 edges
3. `Kelas` - 68 edges
4. `Guru` - 64 edges
5. `Tagihan` - 55 edges
6. `User` - 53 edges
7. `JenisTagihan` - 46 edges
8. `ManajemenGajiGuru` - 41 edges
9. `Semester` - 40 edges
10. `GajiGuru` - 38 edges

## Surprising Connections (you probably didn't know these)
- `TagihanZeroNominalAndPaymentDeletionTest` --references--> `JenisTagihan`  [EXTRACTED]
  tests/Feature/TagihanZeroNominalAndPaymentDeletionTest.php → app/Models/JenisTagihan.php
- `createUserWithRole()` --calls--> `Role`  [INFERRED]
  tests/Feature/RbacAndNavigationTest.php → app/Models/Role.php
- `TagihanZeroNominalAndPaymentDeletionTest` --references--> `Siswa`  [EXTRACTED]
  tests/Feature/TagihanZeroNominalAndPaymentDeletionTest.php → app/Models/Siswa.php
- `TagihanZeroNominalAndPaymentDeletionTest` --references--> `TahunAjaran`  [EXTRACTED]
  tests/Feature/TagihanZeroNominalAndPaymentDeletionTest.php → app/Models/TahunAjaran.php
- `createUserWithRole()` --calls--> `User`  [INFERRED]
  tests/Feature/RbacAndNavigationTest.php → app/Models/User.php

## Import Cycles
- None detected.

## Communities (298 total, 80 thin omitted)

### Community 0 - "MataPelajaran"
Cohesion: 0.07
Nodes (12): InputNilaiSumatif, ManajemenKurikulumMerdeka, ManajemenMapel, LingkupMateri, MataPelajaran, NilaiSas, NilaiSumatifTp, TemplateDeskripsi (+4 more)

### Community 1 - "ManajemenJadwal"
Cohesion: 0.13
Nodes (3): JadwalMengajar, JadwalPelajaran, ManajemenJadwal

### Community 2 - "Illuminate\Http\Request"
Cohesion: 0.08
Nodes (10): FinanceExportController, FinanceReportController, RoleMiddleware, Login, ProfilSaya, AuditLogger, bootAuditable(), Closure (+2 more)

### Community 3 - "TestCase"
Cohesion: 0.22
Nodes (4): Illuminate\Foundation\Testing\TestCase, GuruDashboardTest, TahfidzParentFeedbackTest, TestCase

### Community 4 - "Tagihan"
Cohesion: 0.13
Nodes (3): Pembayaran, Tagihan, TagihanZeroNominalAndPaymentDeletionTest

### Community 5 - "1. Super Admin"
Cohesion: 0.06
Nodes (32): 1.10 Manajemen Pengaturan Sistem & TTD, 1.1 Dashboard Super Admin, 1.2 Manajemen User, 1.3 Manajemen Siswa, 1.4 Manajemen Guru, 1.5 Manajemen Kelas, 1.6 Manajemen Jadwal, 1.7 Manajemen Mata Pelajaran (+24 more)

### Community 8 - "scripts"
Cohesion: 0.14
Nodes (14): scripts, dev, post-autoload-dump, post-update-cmd, pre-package-uninstall, test, Composer\\Config::disableProcessTimeout, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump (+6 more)

### Community 10 - "5. Finance / Keuangan"
Cohesion: 0.06
Nodes (32): 4.1 Dashboard Murid, 4.2 Jadwal Pelajaran Saya, 4.3 Kehadiran Saya, 4.4 Rapor & Nilai, 4.5 Tagihan SPP & Keuangan, 4.6 Ekstrakurikuler Saya, 4.7 Riwayat Aktivitas, 4. Murid / Portal Siswa (+24 more)

### Community 11 - "AbsensiGuru"
Cohesion: 0.11
Nodes (5): AbsensiDiri, RekapAbsensiGuru, InputAbsensiKaryawan, AbsensiGuru, Livewire\WithFileUploads

### Community 13 - "4. Model Data (Entitas & Field)"
Cohesion: 0.06
Nodes (30): 10. Rekomendasi Tahapan Pengembangan (Roadmap), 1. Ringkasan Sistem Sumber, 2. Peran Pengguna (Aktor) yang Disarankan, 3. Alur Kerja End-to-End, 4.10 `ekstrakurikuler` (dari sheet **EKSKUL**), 4.11 `kehadiran` & `catatan_wali_kelas`, 4.12 `leger` (dari sheet **LEGER**) — VIEW, bukan tabel fisik, 4.13 Output Cetak Rapor (dari sheet **SAMPUL RAPOR**, **ISI SEMESTER 1/2**, **RAPOR INKUL**) (+22 more)

### Community 17 - "Livewire\Component"
Cohesion: 0.10
Nodes (7): ArusMasuk, EkstrakurikulerSaya, RiwayatAktivitas, TabunganSaya, Illuminate\Database\Eloquent\SoftDeletes, Livewire\Component, Livewire\WithPagination

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
Cohesion: 0.08
Nodes (8): AbsensiSiswa, DanaBos, JadwalPelajaran, JadwalRemedial, ProyekP5, SubdimensiP5, TargetHafalanTahfidz, Illuminate\Database\Eloquent\Model

### Community 27 - "AREA AUDIT & ASPEK EVALUASI"
Cohesion: 0.14
Nodes (13): 1. Konsistensi Design System & Visual Aesthetics, 2. Responsivitas Lintas Layar (Cross-Device & Cross-Resolution), 3. Kejelasan Alur Pengguna (User Flow & Micro-Interactions), 4. Efisiensi Form Input Nilai (Ergonomi Kerja Guru), 5. Aksesibilitas (Accessibility / WCAG AA) & Feedback UI, 6. Desain Output Cetak PDF Rapor & Resi STT, AREA AUDIT & ASPEK EVALUASI, BATASAN REVIEW (+5 more)

### Community 29 - "Prompt: Review Logika Bisnis — Sistem Informasi Akademik (Kurikulum Merdeka & Tahfizh) & Keuangan Yayasan"
Cohesion: 0.15
Nodes (12): 1. Konsistensi Penilaian Kurikulum Merdeka & Auto-Narasi, 2. Isosiasi & Integrasi Model Tahfizh vs Rombel Umum, 3. Keamanan & Integritas QR Code Keabsahan Dokumen, 4. Trace End-to-End Alur Kritis, 5. Edge Cases & Penanganan Transisi State, AREA AUDIT & TUGAS REVIEW, BATASAN REVIEW, CHECKLIST TITIK RAWAN KHUSUS (Wajib Diverifikasi Statusnya) (+4 more)

### Community 34 - "require"
Cohesion: 0.15
Nodes (13): require, barryvdh/laravel-dompdf, blade-ui-kit/blade-icons, chillerlan/php-qrcode, laravel/framework, laravel/octane, laravel/tinker, livewire/livewire (+5 more)

### Community 35 - "CapaianGuru"
Cohesion: 0.10
Nodes (3): CapaianPengembanganDiri, CapaianPengembanganGuru, CapaianGuru

### Community 36 - "TahunAjaran"
Cohesion: 0.17
Nodes (3): JenisTagihan, TahunAjaran, Illuminate\Foundation\Testing\RefreshDatabase

### Community 37 - "Standar Desain UI Komponen (Buttons, Cards, Modals & Alerts) — SIAKAD"
Cohesion: 0.18
Nodes (10): 1. Standar Desain Kartu (Cards), 2. Standar Desain Tombol (Buttons), 3. Integrasi MicroModal.js untuk Alert & Konfirmasi Dialog, 4. Standar Warna Status (Status Badges), **A. Primary Content Card**, **A. Struktur HTML MicroModal (`resources/views/components/layouts/app.blade.php`)**, **B. Cara Penggunaan di JavaScript / Alpine.js**, **B. Hero / Header Banner Card** (+2 more)

### Community 39 - "Pengeluaran"
Cohesion: 0.15
Nodes (4): Dashboard, Peminjaman, Pengeluaran, NotificationService

### Community 40 - "Notifikasi"
Cohesion: 0.19
Nodes (3): NotificationDropdown, NotificationsList, Notifikasi

### Community 43 - "pengajuan-dana.blade.php"
Cohesion: 0.22
Nodes (8): approveByKepalaYayasan({{ $item->id }}), approveByKoordinator({{ $item->id }}), openModal, openRejectModal({{ $item->id }}), realisasikanDana({{ $item->id }}), rejectPengajuan, closeModal, $set(

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
Cohesion: 0.25
Nodes (7): delete({{ $jadwal->id }}), delete({{ $sched->id }}), openCreateForDay(, openEdit({{ $jadwal->id }}), openEdit({{ $sched->id }}), openCreate, $set(

### Community 50 - "composer.json"
Cohesion: 0.14
Nodes (13): autoload-dev, psr-4, description, keywords, license, minimum-stability, name, prefer-stable (+5 more)

### Community 52 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 53 - ".run"
Cohesion: 0.16
Nodes (3): Ekstrakurikuler, SiswaEkstrakurikuler, SiswaKelas

### Community 54 - "Pengaturan"
Cohesion: 0.12
Nodes (5): VerifikasiDokumenController, ManajemenPengaturan, ManajemenPiketGuru, JadwalPiketGuru, Pengaturan

### Community 56 - "manajemen-komponen-nilai.blade.php"
Cohesion: 0.33
Nodes (5): delete({{ $komponen[, openEdit({{ $komponen[, closeModal, openCreate, $set(

### Community 57 - "manajemen-surat.blade.php"
Cohesion: 0.33
Nodes (5): deleteRiwayat({{ $r->id }}), downloadCurrentPdf, downloadPdfById({{ $r->id }}), loadRiwayatSurat({{ $r->id }}), $set(

### Community 58 - "Rapor"
Cohesion: 0.20
Nodes (4): Controller, RaporPdfController, Rapor, RaporTahfidzDetail

### Community 59 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 60 - "overview-pembayaran.blade.php"
Cohesion: 0.40
Nodes (4): closeDetails, kirimReminder({{ $item[, viewDetails({{ $item[, voidPayment({{ $p->id }})

### Community 61 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 62 - "manajemen-kalender-akademik.blade.php"
Cohesion: 0.18
Nodes (10): delete({{ $event->id }}), deleteTahunAjaran({{ $ta->id }}), openEditModal({{ $event->id }}), openEditSemester({{ $s->id }}), openTahunAjaranModal, closeModal, openCreateModal, $set( (+2 more)

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

### Community 105 - "arus-kas-masuk.blade.php"
Cohesion: 0.29
Nodes (6): bulkDelete, closeCreateModal, deleteIncome({{ $item->raw_id }}), exportPdf, openCreateModal, selectStream(

### Community 106 - "manajemen-tagihan.blade.php"
Cohesion: 0.20
Nodes (9): addSiswaToBulk({{ $res->id }}), clearSelectedStudent, removeSiswaFromBulk({{ $res->id }}), removeSiswaFromBulk({{ $sel->id }}), closeCreateModal, closeEditModal, openCreateModal, selectStudent({{ $s->id }}) (+1 more)

### Community 108 - "proses-kenaikan-kelas.blade.php"
Cohesion: 0.50
Nodes (3): prosesKenaikan, $set(, toggleTinggalKelas({{ $siswa->id }})

### Community 115 - "input-nilai-tahfidz.blade.php"
Cohesion: 0.40
Nodes (4): openScoreModal({{ $s->id }}), selectTab(, setTanggalToday, setTanggalYesterday

### Community 116 - "arus-kas.blade.php"
Cohesion: 0.20
Nodes (9): closeExpenseModal, closeIncomeModal, openExpenseModal, openIncomeModal, deleteExpense({{ $item->raw_id }}), deleteIncome({{ $item->raw_id }}), exportPdf, selectStream( (+1 more)

### Community 117 - "arus-kas-keluar.blade.php"
Cohesion: 0.29
Nodes (6): bulkDelete, closeCreateModal, deleteExpense({{ $item->raw_id }}), exportPdf, openCreateModal, selectStream(

### Community 121 - "absensi-siswa.blade.php"
Cohesion: 0.50
Nodes (3): setStatusAll(, setPresetDate(, setStatus({{ $index }}, 

### Community 160 - "dana-bos.blade.php"
Cohesion: 0.33
Nodes (5): deleteTransaction({{ $t->id }}), closeCreateModal, openCreateModal(, selectTab(, $set(

### Community 189 - "audit-log.blade.php"
Cohesion: 0.40
Nodes (4): openDetail({{ $log->id }}), closeDetail, $set(, setPeriodPreset(

### Community 209 - "detail-tagihan-siswa.blade.php"
Cohesion: 0.22
Nodes (8): deletePembayaran({{ $rp->id }}), deleteTagihan({{ $item->id }}), openEditModal({{ $item->id }}), resetBayarFilters, resetFilters, closeCreateModal, closeEditModal, openCreateModal

### Community 211 - "manajemen-remedial.blade.php"
Cohesion: 0.33
Nodes (5): delete({{ $item->id }}), openCreate, openEdit({{ $item->id }}), $set(, updateStatus({{ $item->id }}, 

### Community 212 - "Illuminate\Database\Seeder"
Cohesion: 0.10
Nodes (15): CapaianGuruSeeder, DatabaseSeeder, DemoDataSeeder, FinanceSeeder, JenisTagihanSeeder, KategoriPengeluaranSeeder, KomponenNilaiSeeder, PengaturanSeeder (+7 more)

### Community 213 - "PengajuanKoreksiNilai"
Cohesion: 0.22
Nodes (3): ManajemenKoreksiNilai, ManajemenKoreksiNilai, PengajuanKoreksiNilai

### Community 215 - "Nilai"
Cohesion: 0.14
Nodes (3): Dashboard, RaporNilai, Nilai

### Community 218 - "tabungan-siswa.blade.php"
Cohesion: 0.29
Nodes (6): closeEditTransactionModal, closeModals, deleteTransaction({{ $tx->id }}), openEditTransaction({{ $tx->id }}), openHistoryModal({{ $siswa->id }}), openTransactionModal({{ $siswa->id }}, 

### Community 224 - "capaian-pengembangan-diri.blade.php"
Cohesion: 0.29
Nodes (6): closeDetailModal, closeModal, delete({{ $item->id }}), openCreate, openDetailModal({{ $item->id }}), openEdit({{ $item->id }})

### Community 225 - "capaian-pengembangan-guru.blade.php"
Cohesion: 0.29
Nodes (6): openEvaluateFromDetail, openEvaluateModal({{ $item->id }}), closeDetailModal, closeModal, delete({{ $item->id }}), openDetailModal({{ $item->id }})

### Community 230 - "system-error-log.blade.php"
Cohesion: 0.50
Nodes (3): clearLog, closeErrorDetail, openErrorDetail({{ $log[

### Community 231 - "ESignatureService"
Cohesion: 0.21
Nodes (3): DocumentVerificationController, ESignatureService, ESignatureTest

### Community 234 - ".handle"
Cohesion: 0.40
Nodes (4): GenerateMonthlySpp, GenerateMonthlySppCommand, Command, Illuminate\Console\Command

### Community 235 - "User"
Cohesion: 0.13
Nodes (8): Role, User, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, GuruRoleAccessTest, GuruStudentClassDisplayTest, KenaikanKelasExportTest, createUserWithRole()

### Community 252 - "detail-gaji-guru.blade.php"
Cohesion: 0.25
Nodes (7): openPreview({{ $sd->id }}), closePreview, deleteSalary({{ $sal->id }}), deleteSelected, openDetailModal({{ $sal->id }}), openPreview({{ $sal->id }}), $set(

### Community 264 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 274 - "bootstrap.blade.php"
Cohesion: 0.50
Nodes (3): gotoPage({{ $page }}, , nextPage(, previousPage(

### Community 276 - "simple-bootstrap.blade.php"
Cohesion: 0.50
Nodes (3): nextPage(, previousPage(, setPage(

### Community 279 - "extra"
Cohesion: 0.67
Nodes (3): extra, laravel, dont-discover

### Community 282 - "GuruMapelKelas"
Cohesion: 0.18
Nodes (3): RekapNilai, GuruMapelKelas, JadwalService

### Community 283 - "livewire/simple-tailwind.blade.php"
Cohesion: 0.50
Nodes (3): nextPage(, previousPage(, setPage(

### Community 284 - "livewire/tailwind.blade.php"
Cohesion: 0.50
Nodes (3): gotoPage({{ $page }}, , nextPage(, previousPage(

## Knowledge Gaps
- **497 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+492 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **80 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Siswa` connect `Siswa` to `MataPelajaran`, `.mount`, `TestCase`, `Tagihan`, `DetailTagihanSiswa`, `RekapAbsensiSiswa`, `Kelas`, `ManajemenSurat`, `Livewire\Component`, `DimensiP5`, `ManajemenSiswa`, `Illuminate\Database\Eloquent\Model`, `NilaiTahfidz`, `GuruMapelKelas`, `TabunganSiswa`, `Semester`, `Dashboard`, `Illuminate\Database\Eloquent\Factories\HasFactory`, `TahunAjaran`, `NilaiP5`, `Pengeluaran`, `Notifikasi`, `ManajemenRemedial`, `ManajemenTagihan`, `OverviewPembayaran`, `.run`, `Rapor`, `InputPembayaran`, `AbsensiSiswa`, `InputNilaiSiswa`, `KelolaRapor`, `ProsesKenaikanKelas`, `.handle`, `User`, `AutomatedSppGenerationTest`?**
  _High betweenness centrality (0.036) - this node is a cross-community bridge._
- **Why does `Guru` connect `Guru` to `.mount`, `TestCase`, `Tagihan`, `User.php`, `AbsensiGuru`, `DetailGajiGuru`, `ManajemenSurat`, `Livewire\Component`, `ManajemenGajiGuru`, `ManajemenKelas`, `Illuminate\Database\Eloquent\Model`, `ManajemenKaryawan`, `Dashboard`, `Illuminate\Database\Eloquent\Factories\HasFactory`, `Semester`, `CapaianGuru`, `Pengeluaran`, `ManajemenRemedial`, `.run`, `Pengaturan`, `ManajemenPeminjaman`, `User`?**
  _High betweenness centrality (0.030) - this node is a cross-community bridge._
- **Why does `Kelas` connect `Kelas` to `MataPelajaran`, `.mount`, `TestCase`, `Tagihan`, `RekapAbsensiSiswa`, `Livewire\Component`, `DimensiP5`, `ManajemenKelas`, `ManajemenSiswa`, `Illuminate\Database\Eloquent\Model`, `GuruMapelKelas`, `NilaiTahfidz`, `TabunganSiswa`, `Dashboard`, `Semester`, `Illuminate\Database\Eloquent\Factories\HasFactory`, `TahunAjaran`, `NilaiP5`, `Pengeluaran`, `ManajemenRemedial`, `OverviewPembayaran`, `.run`, `InputPembayaran`, `KelolaRapor`, `ProsesKenaikanKelas`, `User`?**
  _High betweenness centrality (0.024) - this node is a cross-community bridge._
- **Are the 69 inferred relationships involving `Siswa` (e.g. with `.handle()` and `.handle()`) actually correct?**
  _`Siswa` has 69 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _497 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `MataPelajaran` be split into smaller, more focused modules?**
  _Cohesion score 0.06604324956165984 - nodes in this community are weakly interconnected._
- **Should `ManajemenJadwal` be split into smaller, more focused modules?**
  _Cohesion score 0.13333333333333333 - nodes in this community are weakly interconnected._