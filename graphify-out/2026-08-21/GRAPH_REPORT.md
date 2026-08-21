# Graph Report - web-sistem-akademik  (2026-08-21)

## Corpus Check
- 383 files · ~242,396 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1877 nodes · 3608 edges · 264 communities (198 shown, 66 thin omitted)
- Extraction: 86% EXTRACTED · 14% INFERRED · 0% AMBIGUOUS · INFERRED: 521 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `d97b47b5`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- MataPelajaran
- ManajemenJadwal
- Role
- ManajemenKelas
- Notifikasi
- 1. Super Admin
- TahunAjaran
- Illuminate\Http\Request
- scripts
- 5. Finance / Keuangan
- ManajemenKalenderAkademik
- Kelas
- 4. Model Data (Entitas & Field)
- Tagihan
- ManajemenSurat
- JadwalMengajar
- TahunAjaran.php
- ManajemenGajiGuru
- package.json
- DanaBos
- KelolaRapor
- 5. Finance / Keuangan
- 1. Super Admin
- Perencanaan Sistem Informasi Akademik (Kurikulum Merdeka & Tahfizh) & Keuangan Yayasan
- Illuminate\Database\Eloquent\Model
- NilaiTahfidz
- AREA AUDIT & ASPEK EVALUASI
- TabunganSiswa
- Prompt: Review Logika Bisnis — Sistem Informasi Akademik (Kurikulum Merdeka & Tahfizh) & Keuangan Yayasan
- PengaturanBobotNilai
- AbsensiGuru
- JenisTagihan
- .run
- require
- CapaianGuru
- PengajuanKoreksiNilai
- Standar Desain UI Komponen (Buttons, Cards, Modals & Alerts) — SIAKAD
- Illuminate\Database\Seeder
- OverviewPembayaran
- PengajuanDana
- ManajemenSiswa
- ManajemenRemedial
- pengajuan-dana.blade.php
- manajemen-gaji-guru.blade.php
- manajemen-kurikulum-merdeka.blade.php
- require-dev
- manajemen-jadwal.blade.php
- ManajemenTagihan
- Nilai
- composer.json
- ArusKasMasuk
- config
- ManajemenUser
- Pengaturan
- AppServiceProvider
- manajemen-komponen-nilai.blade.php
- manajemen-surat.blade.php
- GuruMapelKelas
- setup
- overview-pembayaran.blade.php
- psr-4
- manajemen-kalender-akademik.blade.php
- manajemen-mapel.blade.php
- Pengeluaran
- ManajemenKaryawan
- data-alumni.blade.php
- UserFactory.php
- notifications-list.blade.php
- input-pembayaran.blade.php
- README.md
- auth.php
- manajemen-koreksi-nilai.blade.php
- setoran-tahfidz.blade.php
- User
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
- audit-log.blade.php
- rules/graphify.md
- workflows/graphify.md
- bulk-actions.blade.php
- ArusKas
- Guru
- .run
- Semester
- Dashboard
- RiwayatAktivitas
- manajemen-remedial.blade.php
- post-create-project-cmd
- Pembayaran
- KehadiranSaya
- ProsesKenaikanKelas
- tabungan-siswa.blade.php
- {{ $closeAction }}
- Peminjaman
- WithDateFilter.php
- capaian-pengembangan-diri.blade.php
- capaian-pengembangan-guru.blade.php
- RoleMiddleware.php
- tutorial-dan-faq.blade.php
- system-error-log.blade.php
- Rapor
- JadwalRemedial
- .loadDashboardData
- autoload-dev
- TagihanSpp
- .getMatrixData
- AuditLogger.php
- Livewire\Component
- RekapAbsensiSiswa
- TutorialDanFaq
- AuditLog
- Siswa

## God Nodes (most connected - your core abstractions)
1. `Siswa` - 154 edges
2. `User` - 101 edges
3. `Kelas` - 64 edges
4. `Guru` - 53 edges
5. `Semester` - 40 edges
6. `Tagihan` - 38 edges
7. `ManajemenTagihan` - 37 edges
8. `TahunAjaran` - 36 edges
9. `Role` - 33 edges
10. `Pengeluaran` - 28 edges

## Surprising Connections (you probably didn't know these)
- `createUserWithRole()` --calls--> `Role`  [INFERRED]
  tests/Feature/RbacAndNavigationTest.php → app/Models/Role.php
- `createUserWithRole()` --calls--> `User`  [EXTRACTED]
  tests/Feature/RbacAndNavigationTest.php → app/Models/User.php
- `DocumentVerificationController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/DocumentVerificationController.php → app/Http/Controllers/Controller.php
- `RaporPdfController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/RaporPdfController.php → app/Http/Controllers/Controller.php
- `ManajemenTagihan` --references--> `Siswa`  [EXTRACTED]
  app/Livewire/Finance/ManajemenTagihan.php → app/Models/Siswa.php

## Import Cycles
- None detected.

## Communities (264 total, 66 thin omitted)

### Community 0 - "MataPelajaran"
Cohesion: 0.06
Nodes (13): InputNilaiSumatif, ManajemenKurikulumMerdeka, ManajemenMapel, LingkupMateri, MataPelajaran, NilaiSas, NilaiSumatifTp, TemplateDeskripsi (+5 more)

### Community 2 - "Role"
Cohesion: 0.22
Nodes (4): Role, GuruRoleAccessTest, GuruStudentClassDisplayTest, KenaikanKelasExportTest

### Community 4 - "Notifikasi"
Cohesion: 0.21
Nodes (3): NotificationDropdown, NotificationsList, Notifikasi

### Community 5 - "1. Super Admin"
Cohesion: 0.06
Nodes (32): 1.10 Manajemen Pengaturan Sistem & TTD, 1.1 Dashboard Super Admin, 1.2 Manajemen User, 1.3 Manajemen Siswa, 1.4 Manajemen Guru, 1.5 Manajemen Kelas, 1.6 Manajemen Jadwal, 1.7 Manajemen Mata Pelajaran (+24 more)

### Community 6 - "TahunAjaran"
Cohesion: 0.13
Nodes (3): LaporanTunggakan, TahunAjaran, KalenderAkademikTest

### Community 7 - "Illuminate\Http\Request"
Cohesion: 0.21
Nodes (5): Controller, FinanceExportController, FinanceReportController, VerifikasiDokumenController, Illuminate\Http\Request

### Community 8 - "scripts"
Cohesion: 0.14
Nodes (14): scripts, dev, post-autoload-dump, post-update-cmd, pre-package-uninstall, test, Composer\\Config::disableProcessTimeout, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump (+6 more)

### Community 10 - "5. Finance / Keuangan"
Cohesion: 0.06
Nodes (32): 4.1 Dashboard Murid, 4.2 Jadwal Pelajaran Saya, 4.3 Kehadiran Saya, 4.4 Rapor & Nilai, 4.5 Tagihan SPP & Keuangan, 4.6 Ekstrakurikuler Saya, 4.7 Riwayat Aktivitas, 4. Murid / Portal Siswa (+24 more)

### Community 12 - "Kelas"
Cohesion: 0.08
Nodes (4): Dashboard, Dashboard, PlottingSiswaKelas, Kelas

### Community 13 - "4. Model Data (Entitas & Field)"
Cohesion: 0.06
Nodes (30): 10. Rekomendasi Tahapan Pengembangan (Roadmap), 1. Ringkasan Sistem Sumber, 2. Peran Pengguna (Aktor) yang Disarankan, 3. Alur Kerja End-to-End, 4.10 `ekstrakurikuler` (dari sheet **EKSKUL**), 4.11 `kehadiran` & `catatan_wali_kelas`, 4.12 `leger` (dari sheet **LEGER**) — VIEW, bukan tabel fisik, 4.13 Output Cetak Rapor (dari sheet **SAMPUL RAPOR**, **ISI SEMESTER 1/2**, **RAPOR INKUL**) (+22 more)

### Community 14 - "Tagihan"
Cohesion: 0.13
Nodes (3): InputPembayaran, Tagihan, AutomatedSppGenerationTest

### Community 17 - "TahunAjaran.php"
Cohesion: 0.17
Nodes (4): GenerateMonthlySpp, GenerateMonthlySppCommand, Illuminate\Console\Command, Illuminate\Database\Eloquent\SoftDeletes

### Community 18 - "ManajemenGajiGuru"
Cohesion: 0.15
Nodes (3): ManajemenGajiGuru, GajiGuru, NotificationService

### Community 19 - "package.json"
Cohesion: 0.10
Nodes (20): concurrently, laravel-vite-plugin, micromodal, dependencies, micromodal, devDependencies, concurrently, laravel-vite-plugin (+12 more)

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
Nodes (9): EkstrakurikulerSaya, AbsensiSiswa, DanaBos, JadwalPelajaran, ProyekP5, SubdimensiP5, TargetHafalanTahfidz, Illuminate\Database\Eloquent\Factories\HasFactory (+1 more)

### Community 27 - "AREA AUDIT & ASPEK EVALUASI"
Cohesion: 0.14
Nodes (13): 1. Konsistensi Design System & Visual Aesthetics, 2. Responsivitas Lintas Layar (Cross-Device & Cross-Resolution), 3. Kejelasan Alur Pengguna (User Flow & Micro-Interactions), 4. Efisiensi Form Input Nilai (Ergonomi Kerja Guru), 5. Aksesibilitas (Accessibility / WCAG AA) & Feedback UI, 6. Desain Output Cetak PDF Rapor & Resi STT, AREA AUDIT & ASPEK EVALUASI, BATASAN REVIEW (+5 more)

### Community 29 - "Prompt: Review Logika Bisnis — Sistem Informasi Akademik (Kurikulum Merdeka & Tahfizh) & Keuangan Yayasan"
Cohesion: 0.15
Nodes (12): 1. Konsistensi Penilaian Kurikulum Merdeka & Auto-Narasi, 2. Isosiasi & Integrasi Model Tahfizh vs Rombel Umum, 3. Keamanan & Integritas QR Code Keabsahan Dokumen, 4. Trace End-to-End Alur Kritis, 5. Edge Cases & Penanganan Transisi State, AREA AUDIT & TUGAS REVIEW, BATASAN REVIEW, CHECKLIST TITIK RAWAN KHUSUS (Wajib Diverifikasi Statusnya) (+4 more)

### Community 31 - "AbsensiGuru"
Cohesion: 0.11
Nodes (5): AbsensiDiri, RekapAbsensiGuru, InputAbsensiKaryawan, AbsensiGuru, Livewire\WithFileUploads

### Community 32 - "JenisTagihan"
Cohesion: 0.14
Nodes (3): LaporanPemasukan, JenisTagihan, Command

### Community 33 - ".run"
Cohesion: 0.14
Nodes (3): LaporanPengeluaran, KategoriPengeluaran, FinanceSeeder

### Community 34 - "require"
Cohesion: 0.15
Nodes (13): require, barryvdh/laravel-dompdf, blade-ui-kit/blade-icons, chillerlan/php-qrcode, laravel/framework, laravel/octane, laravel/tinker, livewire/livewire (+5 more)

### Community 35 - "CapaianGuru"
Cohesion: 0.11
Nodes (4): CapaianPengembanganDiri, CapaianPengembanganGuru, CapaianGuru, CapaianGuruSeeder

### Community 36 - "PengajuanKoreksiNilai"
Cohesion: 0.24
Nodes (3): ManajemenKoreksiNilai, ManajemenKoreksiNilai, PengajuanKoreksiNilai

### Community 37 - "Standar Desain UI Komponen (Buttons, Cards, Modals & Alerts) — SIAKAD"
Cohesion: 0.18
Nodes (10): 1. Standar Desain Kartu (Cards), 2. Standar Desain Tombol (Buttons), 3. Integrasi MicroModal.js untuk Alert & Konfirmasi Dialog, 4. Standar Warna Status (Status Badges), **A. Primary Content Card**, **A. Struktur HTML MicroModal (`resources/views/components/layouts/app.blade.php`)**, **B. Cara Penggunaan di JavaScript / Alpine.js**, **B. Hero / Header Banner Card** (+2 more)

### Community 38 - "Illuminate\Database\Seeder"
Cohesion: 0.10
Nodes (9): DatabaseSeeder, JenisTagihanSeeder, KategoriPengeluaranSeeder, KomponenNilaiSeeder, PengaturanSeeder, ProductionAccountsSeeder, RoleSeeder, UserSeeder (+1 more)

### Community 41 - "ManajemenSiswa"
Cohesion: 0.11
Nodes (4): PenilaianP5, ManajemenSiswa, DimensiP5, NilaiP5

### Community 43 - "pengajuan-dana.blade.php"
Cohesion: 0.22
Nodes (8): approveByKepalaYayasan({{ $item->id }}), approveByKoordinator({{ $item->id }}), openModal, openRejectModal({{ $item->id }}), realisasikanDana({{ $item->id }}), rejectPengajuan, closeModal, $set(

### Community 44 - "manajemen-gaji-guru.blade.php"
Cohesion: 0.22
Nodes (8): closeGenerateModal, deleteDraft({{ $sal->id }}), generateDrafts, openEditModal({{ $sal->id }}), openGenerateModal, paySalary({{ $sal->id }}), closeEditModal, saveEdit

### Community 45 - "manajemen-kurikulum-merdeka.blade.php"
Cohesion: 0.22
Nodes (8): closeLmModal, closeTpModal, editLingkupMateri({{ $lm->id }}), editTp({{ $tp->id }}), openLmModal, openTpModal, openTpModal({{ $lm->id }}), saveTemplate

### Community 46 - "require-dev"
Cohesion: 0.22
Nodes (9): require-dev, fakerphp/faker, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision, pestphp/pest (+1 more)

### Community 47 - "manajemen-jadwal.blade.php"
Cohesion: 0.25
Nodes (7): delete({{ $jadwal->id }}), delete({{ $sched->id }}), openCreateForDay(, openEdit({{ $jadwal->id }}), openEdit({{ $sched->id }}), openCreate, $set(

### Community 49 - "Nilai"
Cohesion: 0.09
Nodes (5): InputNilaiSiswa, RaporNilai, ManajemenKomponenNilai, KomponenNilai, Nilai

### Community 50 - "composer.json"
Cohesion: 0.14
Nodes (13): description, extra, laravel, keywords, dont-discover, license, minimum-stability, name (+5 more)

### Community 52 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 54 - "Pengaturan"
Cohesion: 0.11
Nodes (6): ManajemenPengaturan, ManajemenPiketGuru, JadwalPiketGuru, Pengaturan, ESignatureService, ESignatureTest

### Community 56 - "manajemen-komponen-nilai.blade.php"
Cohesion: 0.33
Nodes (5): delete({{ $komponen[, openEdit({{ $komponen[, closeModal, openCreate, $set(

### Community 57 - "manajemen-surat.blade.php"
Cohesion: 0.33
Nodes (5): deleteRiwayat({{ $r->id }}), downloadCurrentPdf, downloadPdfById({{ $r->id }}), loadRiwayatSurat({{ $r->id }}), $set(

### Community 58 - "GuruMapelKelas"
Cohesion: 0.17
Nodes (3): Dashboard, GuruMapelKelas, JadwalService

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

### Community 74 - "User"
Cohesion: 0.09
Nodes (9): User, Illuminate\Foundation\Auth\User, Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, Illuminate\Notifications\Notifiable, GuruDashboardTest, createUserWithRole(), TahfidzParentFeedbackTest (+1 more)

### Community 105 - "arus-kas-masuk.blade.php"
Cohesion: 0.29
Nodes (6): bulkDelete, closeCreateModal, deleteIncome({{ $item->raw_id }}), exportPdf, openCreateModal, selectStream(

### Community 106 - "manajemen-tagihan.blade.php"
Cohesion: 0.12
Nodes (15): addSiswaToBulk({{ $bs->id }}), clearBulkSelected, clearSelectedStudent, closeDetailModal, deleteTagihan({{ $item->id }}), openCreateModal({{ $selectedSiswa->id }}), openDetail({{ $siswa->id }}), openEditModal({{ $item->id }}) (+7 more)

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

### Community 205 - "Guru"
Cohesion: 0.10
Nodes (3): Dashboard, ManajemenGuru, Guru

### Community 206 - ".run"
Cohesion: 0.13
Nodes (6): Ekstrakurikuler, RaporDetail, SiswaEkstrakurikuler, SiswaKelas, ProductionDataSeeder, RaporOrangTuaSeeder

### Community 208 - "Semester"
Cohesion: 0.15
Nodes (3): SetoranTahfidz, Semester, TahfidzMutabaahSeeder

### Community 211 - "manajemen-remedial.blade.php"
Cohesion: 0.33
Nodes (5): delete({{ $item->id }}), openCreate, openEdit({{ $item->id }}), $set(, updateStatus({{ $item->id }}, 

### Community 212 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 218 - "tabungan-siswa.blade.php"
Cohesion: 0.29
Nodes (6): closeEditTransactionModal, closeModals, deleteTransaction({{ $tx->id }}), openEditTransaction({{ $tx->id }}), openHistoryModal({{ $siswa->id }}), openTransactionModal({{ $siswa->id }}, 

### Community 224 - "capaian-pengembangan-diri.blade.php"
Cohesion: 0.40
Nodes (4): closeModal, delete({{ $item->id }}), openCreate, openEdit({{ $item->id }})

### Community 225 - "capaian-pengembangan-guru.blade.php"
Cohesion: 0.50
Nodes (3): openEvaluateModal({{ $item->id }}), closeModal, delete({{ $item->id }})

### Community 226 - "RoleMiddleware.php"
Cohesion: 0.60
Nodes (3): RoleMiddleware, Closure, Symfony\Component\HttpFoundation\Response

### Community 230 - "system-error-log.blade.php"
Cohesion: 0.50
Nodes (3): clearLog, closeErrorDetail, openErrorDetail({{ $log[

### Community 231 - "Rapor"
Cohesion: 0.24
Nodes (3): RaporPdfController, Rapor, RaporTahfidzDetail

### Community 252 - "autoload-dev"
Cohesion: 0.67
Nodes (3): autoload-dev, psr-4, Tests\\

### Community 259 - "Livewire\Component"
Cohesion: 0.13
Nodes (3): ArusMasuk, Livewire\Component, Livewire\WithPagination

### Community 268 - "Siswa"
Cohesion: 0.09
Nodes (3): TabunganSaya, DataAlumni, Siswa

## Knowledge Gaps
- **421 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+416 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **66 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Siswa` connect `Siswa` to `MataPelajaran`, `.getMatrixData`, `Role`, `Livewire\Component`, `RekapAbsensiSiswa`, `Kelas.php`, `Kelas`, `Tagihan`, `ManajemenSurat`, `TahunAjaran.php`, `KelolaRapor`, `Illuminate\Database\Eloquent\Model`, `NilaiTahfidz`, `TabunganSiswa`, `JenisTagihan`, `.run`, `Illuminate\Database\Seeder`, `OverviewPembayaran`, `ManajemenSiswa`, `ManajemenRemedial`, `ManajemenTagihan`, `Nilai`, `User`, `AbsensiSiswa`, `Guru`, `.run`, `Semester`, `ProsesKenaikanKelas`, `Rapor`?**
  _High betweenness centrality (0.045) - this node is a cross-community bridge._
- **Why does `ManajemenTagihan` connect `ManajemenTagihan` to `JenisTagihan`, `Livewire\Component`, `Siswa`, `Tagihan`, `TahunAjaran.php`, `WithDateFilter.php`?**
  _High betweenness centrality (0.030) - this node is a cross-community bridge._
- **Why does `User` connect `User` to `MataPelajaran`, `AuditLogger.php`, `Livewire\Component`, `Role`, `TahunAjaran`, `Illuminate\Http\Request`, `Kelas.php`, `Siswa`, `Tagihan`, `TahunAjaran.php`, `Illuminate\Database\Eloquent\Model`, `NilaiTahfidz`, `AbsensiGuru`, `.run`, `CapaianGuru`, `Illuminate\Database\Seeder`, `ManajemenSiswa`, `ManajemenUser`, `Pengaturan`, `ManajemenKaryawan`, `UserFactory.php`, `auth.php`, `Guru`, `.run`, `Semester`?**
  _High betweenness centrality (0.020) - this node is a cross-community bridge._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _421 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `MataPelajaran` be split into smaller, more focused modules?**
  _Cohesion score 0.06010230179028133 - nodes in this community are weakly interconnected._
- **Should `1. Super Admin` be split into smaller, more focused modules?**
  _Cohesion score 0.06060606060606061 - nodes in this community are weakly interconnected._
- **Should `TahunAjaran` be split into smaller, more focused modules?**
  _Cohesion score 0.1286549707602339 - nodes in this community are weakly interconnected._