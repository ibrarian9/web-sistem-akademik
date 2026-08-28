# Graph Report - web-sistem-akademik  (2026-08-28)

## Corpus Check
- 407 files · ~278,594 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 2087 nodes · 4015 edges · 273 communities (204 shown, 69 thin omitted)
- Extraction: 85% EXTRACTED · 15% INFERRED · 0% AMBIGUOUS · INFERRED: 620 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `2ec691a2`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- MataPelajaran
- ManajemenJadwal
- Illuminate\Http\Request
- Semester
- LaporanTunggakan
- 1. Super Admin
- DetailTagihanSiswa
- Pengaturan
- scripts
- ManajemenKalenderAkademik
- 5. Finance / Keuangan
- ManajemenUser
- Kelas
- 4. Model Data (Entitas & Field)
- DetailGajiGuru
- ManajemenSurat
- DanaBos
- Rapor
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
- PengaturanBobotNilai
- JadwalPelajaran
- Pembayaran
- PengajuanDana
- require
- CapaianGuru
- Livewire\Component
- Standar Desain UI Komponen (Buttons, Cards, Modals & Alerts) — SIAKAD
- PenilaianP5.php
- Notifikasi
- Controller
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
- ProfilSaya
- RoleMiddleware.php
- AppServiceProvider
- manajemen-komponen-nilai.blade.php
- manajemen-surat.blade.php
- .run
- setup
- overview-pembayaran.blade.php
- psr-4
- manajemen-kalender-akademik.blade.php
- manajemen-mapel.blade.php
- ArusKasKeluar
- Pengeluaran
- data-alumni.blade.php
- UserFactory.php
- notifications-list.blade.php
- input-pembayaran.blade.php
- README.md
- LaporanPemasukan
- manajemen-koreksi-nilai.blade.php
- setoran-tahfidz.blade.php
- Tagihan
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
- TahunAjaran
- manajemen-remedial.blade.php
- Illuminate\Database\Seeder
- ManajemenKelas
- TutorialDanFaq
- RaporNilai
- tabungan-siswa.blade.php
- {{ $closeAction }}
- ManajemenPeminjaman
- ProsesKenaikanKelas
- capaian-pengembangan-diri.blade.php
- capaian-pengembangan-guru.blade.php
- AuditLog
- tutorial-dan-faq.blade.php
- system-error-log.blade.php
- KelolaRapor
- InputNilaiSumatif.php
- Role
- detail-gaji-guru.blade.php
- ESignatureService
- ArusKas
- GuruMapelKelas
- TagihanSpp
- WithDateFilter.php
- RekapAbsensiSiswa
- Siswa
- post-create-project-cmd
- keywords

## God Nodes (most connected - your core abstractions)
1. `User` - 111 edges
2. `Siswa` - 99 edges
3. `Kelas` - 67 edges
4. `Guru` - 64 edges
5. `Tagihan` - 46 edges
6. `ManajemenGajiGuru` - 41 edges
7. `Semester` - 40 edges
8. `GajiGuru` - 38 edges
9. `TahunAjaran` - 38 edges
10. `ManajemenTagihan` - 37 edges

## Surprising Connections (you probably didn't know these)
- `createUserWithRole()` --calls--> `Role`  [INFERRED]
  tests/Feature/RbacAndNavigationTest.php → app/Models/Role.php
- `createUserWithRole()` --calls--> `User`  [EXTRACTED]
  tests/Feature/RbacAndNavigationTest.php → app/Models/User.php
- `DocumentVerificationController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/DocumentVerificationController.php → app/Http/Controllers/Controller.php
- `FinanceExportController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/FinanceExportController.php → app/Http/Controllers/Controller.php
- `FinanceReportController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/FinanceReportController.php → app/Http/Controllers/Controller.php

## Import Cycles
- None detected.

## Communities (273 total, 69 thin omitted)

### Community 0 - "MataPelajaran"
Cohesion: 0.10
Nodes (5): ManajemenKurikulumMerdeka, ManajemenMapel, LingkupMateri, MataPelajaran, TujuanPembelajaran

### Community 2 - "Illuminate\Http\Request"
Cohesion: 0.23
Nodes (3): FinanceExportController, FinanceReportController, Illuminate\Http\Request

### Community 3 - "Semester"
Cohesion: 0.12
Nodes (4): Dashboard, Semester, DemoDataSeeder, TahfidzMutabaahSeeder

### Community 5 - "1. Super Admin"
Cohesion: 0.06
Nodes (32): 1.10 Manajemen Pengaturan Sistem & TTD, 1.1 Dashboard Super Admin, 1.2 Manajemen User, 1.3 Manajemen Siswa, 1.4 Manajemen Guru, 1.5 Manajemen Kelas, 1.6 Manajemen Jadwal, 1.7 Manajemen Mata Pelajaran (+24 more)

### Community 7 - "Pengaturan"
Cohesion: 0.06
Nodes (9): AbsensiDiri, RekapAbsensiGuru, ManajemenPengaturan, InputAbsensiKaryawan, ManajemenPiketGuru, AbsensiGuru, JadwalPiketGuru, Pengaturan (+1 more)

### Community 8 - "scripts"
Cohesion: 0.14
Nodes (14): scripts, dev, post-autoload-dump, post-update-cmd, pre-package-uninstall, test, Composer\\Config::disableProcessTimeout, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump (+6 more)

### Community 10 - "5. Finance / Keuangan"
Cohesion: 0.06
Nodes (32): 4.1 Dashboard Murid, 4.2 Jadwal Pelajaran Saya, 4.3 Kehadiran Saya, 4.4 Rapor & Nilai, 4.5 Tagihan SPP & Keuangan, 4.6 Ekstrakurikuler Saya, 4.7 Riwayat Aktivitas, 4. Murid / Portal Siswa (+24 more)

### Community 12 - "Kelas"
Cohesion: 0.10
Nodes (3): Dashboard, PlottingSiswaKelas, Kelas

### Community 13 - "4. Model Data (Entitas & Field)"
Cohesion: 0.06
Nodes (30): 10. Rekomendasi Tahapan Pengembangan (Roadmap), 1. Ringkasan Sistem Sumber, 2. Peran Pengguna (Aktor) yang Disarankan, 3. Alur Kerja End-to-End, 4.10 `ekstrakurikuler` (dari sheet **EKSKUL**), 4.11 `kehadiran` & `catatan_wali_kelas`, 4.12 `leger` (dari sheet **LEGER**) — VIEW, bukan tabel fisik, 4.13 Output Cetak Rapor (dari sheet **SAMPUL RAPOR**, **ISI SEMESTER 1/2**, **RAPOR INKUL**) (+22 more)

### Community 17 - "Rapor"
Cohesion: 0.22
Nodes (3): RaporPdfController, Rapor, RaporTahfidzDetail

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
Cohesion: 0.10
Nodes (8): AbsensiSiswa, DanaBos, JadwalPelajaran, JadwalRemedial, SubdimensiP5, TargetHafalanTahfidz, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Database\Eloquent\Model

### Community 26 - "NilaiTahfidz"
Cohesion: 0.15
Nodes (3): InputNilaiTahfidz, SetoranTahfidz, NilaiTahfidz

### Community 27 - "AREA AUDIT & ASPEK EVALUASI"
Cohesion: 0.14
Nodes (13): 1. Konsistensi Design System & Visual Aesthetics, 2. Responsivitas Lintas Layar (Cross-Device & Cross-Resolution), 3. Kejelasan Alur Pengguna (User Flow & Micro-Interactions), 4. Efisiensi Form Input Nilai (Ergonomi Kerja Guru), 5. Aksesibilitas (Accessibility / WCAG AA) & Feedback UI, 6. Desain Output Cetak PDF Rapor & Resi STT, AREA AUDIT & ASPEK EVALUASI, BATASAN REVIEW (+5 more)

### Community 29 - "Prompt: Review Logika Bisnis — Sistem Informasi Akademik (Kurikulum Merdeka & Tahfizh) & Keuangan Yayasan"
Cohesion: 0.15
Nodes (12): 1. Konsistensi Penilaian Kurikulum Merdeka & Auto-Narasi, 2. Isosiasi & Integrasi Model Tahfizh vs Rombel Umum, 3. Keamanan & Integritas QR Code Keabsahan Dokumen, 4. Trace End-to-End Alur Kritis, 5. Edge Cases & Penanganan Transisi State, AREA AUDIT & TUGAS REVIEW, BATASAN REVIEW, CHECKLIST TITIK RAWAN KHUSUS (Wajib Diverifikasi Statusnya) (+4 more)

### Community 31 - "JadwalPelajaran"
Cohesion: 0.15
Nodes (3): Dashboard, JadwalMengajar, JadwalPelajaran

### Community 34 - "require"
Cohesion: 0.15
Nodes (13): require, barryvdh/laravel-dompdf, blade-ui-kit/blade-icons, chillerlan/php-qrcode, laravel/framework, laravel/octane, laravel/tinker, livewire/livewire (+5 more)

### Community 35 - "CapaianGuru"
Cohesion: 0.11
Nodes (3): CapaianPengembanganDiri, CapaianPengembanganGuru, CapaianGuru

### Community 36 - "Livewire\Component"
Cohesion: 0.06
Nodes (19): GenerateMonthlySpp, GenerateMonthlySppCommand, ArusMasuk, RiwayatAktivitas, TabunganSaya, User, Illuminate\Console\Command, Illuminate\Database\Eloquent\SoftDeletes (+11 more)

### Community 37 - "Standar Desain UI Komponen (Buttons, Cards, Modals & Alerts) — SIAKAD"
Cohesion: 0.18
Nodes (10): 1. Standar Desain Kartu (Cards), 2. Standar Desain Tombol (Buttons), 3. Integrasi MicroModal.js untuk Alert & Konfirmasi Dialog, 4. Standar Warna Status (Status Badges), **A. Primary Content Card**, **A. Struktur HTML MicroModal (`resources/views/components/layouts/app.blade.php`)**, **B. Cara Penggunaan di JavaScript / Alpine.js**, **B. Hero / Header Banner Card** (+2 more)

### Community 38 - "PenilaianP5.php"
Cohesion: 0.15
Nodes (4): PenilaianP5, DimensiP5, NilaiP5, ProyekP5

### Community 40 - "Notifikasi"
Cohesion: 0.07
Nodes (9): Login, ManajemenKoreksiNilai, ManajemenKoreksiNilai, NotificationDropdown, NotificationsList, Notifikasi, PengajuanKoreksiNilai, AuditLogger (+1 more)

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
Nodes (13): autoload-dev, psr-4, description, extra, laravel, dont-discover, license, minimum-stability (+5 more)

### Community 52 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 54 - "RoleMiddleware.php"
Cohesion: 0.60
Nodes (3): RoleMiddleware, Closure, Symfony\Component\HttpFoundation\Response

### Community 56 - "manajemen-komponen-nilai.blade.php"
Cohesion: 0.33
Nodes (5): delete({{ $komponen[, openEdit({{ $komponen[, closeModal, openCreate, $set(

### Community 57 - "manajemen-surat.blade.php"
Cohesion: 0.33
Nodes (5): deleteRiwayat({{ $r->id }}), downloadCurrentPdf, downloadPdfById({{ $r->id }}), loadRiwayatSurat({{ $r->id }}), $set(

### Community 58 - ".run"
Cohesion: 0.08
Nodes (8): EkstrakurikulerSaya, BobotNilaiGuru, Ekstrakurikuler, Nilai, RaporDetail, SiswaEkstrakurikuler, SiswaKelas, RaporOrangTuaSeeder

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

### Community 65 - "Pengeluaran"
Cohesion: 0.17
Nodes (4): Peminjaman, Pengeluaran, NotificationService, FinanceSeeder

### Community 68 - "notifications-list.blade.php"
Cohesion: 0.50
Nodes (3): markAsRead({{ $notif->id }}), markAllAsRead, $set(

### Community 69 - "input-pembayaran.blade.php"
Cohesion: 0.50
Nodes (3): pilihSiswaAndTagihan({{ $t->siswa_id }}, {{ $t->id }}), resetSelection, setMetodeBayar(

### Community 70 - "README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 74 - "Tagihan"
Cohesion: 0.12
Nodes (3): InputPembayaran, Tagihan, AutomatedSppGenerationTest

### Community 75 - "AbsensiSiswa"
Cohesion: 0.15
Nodes (3): AbsensiSiswa, Dashboard, KehadiranSaya

### Community 105 - "arus-kas-masuk.blade.php"
Cohesion: 0.29
Nodes (6): bulkDelete, closeCreateModal, deleteIncome({{ $item->raw_id }}), exportPdf, openCreateModal, selectStream(

### Community 106 - "manajemen-tagihan.blade.php"
Cohesion: 0.18
Nodes (10): addSiswaToBulk({{ $bs->id }}), clearBulkSelected, clearSelectedStudent, removeSiswaFromBulk({{ $bs->id }}), removeSiswaFromBulk({{ $sel->id }}), closeCreateModal, closeEditModal, openCreateModal (+2 more)

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
Cohesion: 0.29
Nodes (6): deleteTagihan({{ $item->id }}), openEditModal({{ $item->id }}), resetFilters, closeCreateModal, closeEditModal, openCreateModal

### Community 210 - "TahunAjaran"
Cohesion: 0.12
Nodes (4): JenisTagihan, TahunAjaran, Command, KalenderAkademikTest

### Community 211 - "manajemen-remedial.blade.php"
Cohesion: 0.33
Nodes (5): delete({{ $item->id }}), openCreate, openEdit({{ $item->id }}), $set(, updateStatus({{ $item->id }}, 

### Community 212 - "Illuminate\Database\Seeder"
Cohesion: 0.11
Nodes (10): CapaianGuruSeeder, DatabaseSeeder, JenisTagihanSeeder, KategoriPengeluaranSeeder, KomponenNilaiSeeder, PengaturanSeeder, ProductionAccountsSeeder, ProductionDataSeeder (+2 more)

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

### Community 234 - "InputNilaiSumatif.php"
Cohesion: 0.13
Nodes (7): InputNilaiSumatif, NilaiSas, NilaiSumatifTp, TemplateDeskripsi, AutoNarasiService, Illuminate\Database\Eloquent\Relations\BelongsTo, Illuminate\Database\Eloquent\Relations\HasMany

### Community 235 - "Role"
Cohesion: 0.14
Nodes (5): ManajemenKaryawan, Role, UserSeeder, GuruStudentClassDisplayTest, KenaikanKelasExportTest

### Community 252 - "detail-gaji-guru.blade.php"
Cohesion: 0.25
Nodes (7): openPreview({{ $sd->id }}), closePreview, deleteSalary({{ $sal->id }}), deleteSelected, openDetailModal({{ $sal->id }}), openPreview({{ $sal->id }}), $set(

### Community 253 - "ESignatureService"
Cohesion: 0.18
Nodes (3): DocumentVerificationController, ESignatureService, ESignatureTest

### Community 268 - "Siswa"
Cohesion: 0.08
Nodes (4): ManajemenSiswa, Dashboard, DataAlumni, Siswa

### Community 277 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 278 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

## Knowledge Gaps
- **484 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+479 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **69 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Siswa` connect `Siswa` to `MataPelajaran`, `GuruMapelKelas`, `Semester`, `DetailTagihanSiswa`, `RekapAbsensiSiswa`, `Kelas`, `ManajemenSurat`, `Rapor`, `Illuminate\Database\Eloquent\Model`, `NilaiTahfidz`, `TabunganSiswa`, `Livewire\Component`, `PenilaianP5.php`, `Notifikasi`, `ManajemenRemedial`, `ManajemenTagihan`, `OverviewPembayaran`, `.run`, `Pengeluaran`, `Tagihan`, `AbsensiSiswa`, `InputNilaiSiswa`, `TahunAjaran`, `ProsesKenaikanKelas`, `KelolaRapor`, `InputNilaiSumatif.php`, `Role`?**
  _High betweenness centrality (0.034) - this node is a cross-community bridge._
- **Why does `Kelas` connect `Kelas` to `MataPelajaran`, `GuruMapelKelas`, `Semester`, `LaporanTunggakan`, `RekapAbsensiSiswa`, `Siswa`, `Illuminate\Database\Eloquent\Model`, `NilaiTahfidz`, `TabunganSiswa`, `Livewire\Component`, `PenilaianP5.php`, `ManajemenRemedial`, `OverviewPembayaran`, `.run`, `Tagihan`, `TahunAjaran`, `ManajemenKelas`, `ProsesKenaikanKelas`, `KelolaRapor`, `InputNilaiSumatif.php`, `Role`?**
  _High betweenness centrality (0.033) - this node is a cross-community bridge._
- **Why does `User` connect `Livewire\Component` to `Pengeluaran`, `Illuminate\Http\Request`, `UserFactory.php`, `Semester`, `Pengaturan`, `Notifikasi`, `ProductionDataSeeder.php`, `Tagihan`, `ManajemenUser`, `Siswa`, `Guru`, `Role`, `TahunAjaran`, `Illuminate\Database\Seeder`, `Illuminate\Database\Eloquent\Model`, `.run`, `ESignatureService`?**
  _High betweenness centrality (0.032) - this node is a cross-community bridge._
- **Are the 69 inferred relationships involving `Siswa` (e.g. with `.handle()` and `.handle()`) actually correct?**
  _`Siswa` has 69 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _484 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `MataPelajaran` be split into smaller, more focused modules?**
  _Cohesion score 0.10158730158730159 - nodes in this community are weakly interconnected._
- **Should `Semester` be split into smaller, more focused modules?**
  _Cohesion score 0.125 - nodes in this community are weakly interconnected._