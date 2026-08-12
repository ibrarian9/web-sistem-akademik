# Graph Report - web-sistem-akademik  (2026-08-11)

## Corpus Check
- 329 files · ~205,562 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1591 nodes · 2990 edges · 221 communities (160 shown, 61 thin omitted)
- Extraction: 82% EXTRACTED · 18% INFERRED · 0% AMBIGUOUS · INFERRED: 528 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `33ecf636`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- MataPelajaran
- AbsensiSiswa
- ManajemenKalenderAkademik
- Illuminate\Database\Eloquent\Model
- Notifikasi
- 1. Super Admin
- ProductionDataSeeder.php
- .getMatrixData
- scripts
- Siswa
- 5. Finance / Keuangan
- PemasukanKas
- Kelas
- 4. Model Data (Entitas & Field)
- Tagihan
- Guru
- Pengaturan
- NilaiTahfidz
- ManajemenGajiGuru
- devDependencies
- JenisTagihan
- Semester
- 5. Finance / Keuangan
- 1. Super Admin
- Perencanaan Sistem Informasi Akademik (Kurikulum Merdeka & Tahfizh) & Keuangan Yayasan
- PengajuanDana
- NilaiP5
- AREA AUDIT & ASPEK EVALUASI
- auth.php
- Prompt: Review Logika Bisnis — Sistem Informasi Akademik (Kurikulum Merdeka & Tahfizh) & Keuangan Yayasan
- TahunAjaran
- GuruMapelKelas
- Pembayaran
- ProsesKenaikanKelas
- require
- InputPembayaran
- Role
- Standar Desain UI Komponen (Buttons, Cards, Modals & Alerts) — SIAKAD
- .run
- OverviewPembayaran
- Siswa.php
- Livewire\Component
- ManajemenRemedial
- pengajuan-dana.blade.php
- manajemen-gaji-guru.blade.php
- manajemen-kurikulum-merdeka.blade.php
- require-dev
- manajemen-jadwal.blade.php
- ESignatureService
- KomponenNilai
- composer.json
- User
- config
- TabunganSiswa
- RekapAbsensiSiswa
- AppServiceProvider
- manajemen-komponen-nilai.blade.php
- manajemen-surat.blade.php
- ManajemenSiswa
- setup
- overview-pembayaran.blade.php
- psr-4
- manajemen-kalender-akademik.blade.php
- manajemen-mapel.blade.php
- Dashboard
- InputNilaiSiswa
- data-alumni.blade.php
- UserFactory.php
- notifications-list.blade.php
- input-pembayaran.blade.php
- README.md
- ManajemenPiketGuru
- manajemen-koreksi-nilai.blade.php
- setoran-tahfidz.blade.php
- PengaturanBobotNilai
- TestCase
- DimensiP5
- arus-kas-masuk.blade.php
- manajemen-tagihan.blade.php
- shared/notification-dropdown.blade.php
- proses-kenaikan-kelas.blade.php
- kelola-rapor.blade.php
- penilaian-p5.blade.php
- rapor-nilai.blade.php
- deletePiket({{ $p->id }})
- openScoreModal({{ $s->id }})
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
- Rapor
- rules/graphify.md
- workflows/graphify.md
- Pengeluaran
- KelolaRapor
- Controller
- Nilai
- TagihanSpp
- post-create-project-cmd
- .paySalary
- .handle
- manajemen-remedial.blade.php
- RiwayatAktivitas
- ProfilSaya
- JadwalRemedial
- autoload-dev
- tabungan-siswa.blade.php

## God Nodes (most connected - your core abstractions)
1. `Siswa` - 83 edges
2. `User` - 77 edges
3. `Kelas` - 60 edges
4. `Guru` - 44 edges
5. `Tagihan` - 36 edges
6. `Semester` - 33 edges
7. `Role` - 28 edges
8. `TahunAjaran` - 28 edges
9. `GuruMapelKelas` - 25 edges
10. `MataPelajaran` - 23 edges

## Surprising Connections (you probably didn't know these)
- `createUserWithRole()` --calls--> `Role`  [INFERRED]
  tests/Feature/RbacAndNavigationTest.php → app/Models/Role.php
- `createUserWithRole()` --calls--> `User`  [EXTRACTED]
  tests/Feature/RbacAndNavigationTest.php → app/Models/User.php
- `DocumentVerificationController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/DocumentVerificationController.php → app/Http/Controllers/Controller.php
- `FinanceExportController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/FinanceExportController.php → app/Http/Controllers/Controller.php
- `RaporPdfController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/RaporPdfController.php → app/Http/Controllers/Controller.php

## Import Cycles
- None detected.

## Communities (221 total, 61 thin omitted)

### Community 0 - "MataPelajaran"
Cohesion: 0.06
Nodes (13): InputNilaiSumatif, ManajemenKurikulumMerdeka, RaporNilai, ManajemenMapel, LingkupMateri, MataPelajaran, NilaiSas, NilaiSumatifTp (+5 more)

### Community 1 - "AbsensiSiswa"
Cohesion: 0.06
Nodes (8): AbsensiSiswa, Dashboard, JadwalMengajar, Dashboard, JadwalPelajaran, KehadiranSaya, ManajemenJadwal, JadwalService

### Community 2 - "ManajemenKalenderAkademik"
Cohesion: 0.07
Nodes (7): AbsensiDiri, RekapAbsensiGuru, InputAbsensiKaryawan, ManajemenKalenderAkademik, AbsensiGuru, KalenderAkademik, Livewire\WithFileUploads

### Community 3 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.08
Nodes (12): AbsensiSiswa, BobotNilaiGuru, DanaBos, Ekstrakurikuler, JadwalPelajaran, ProyekP5, RaporDetail, RaporTahfidzDetail (+4 more)

### Community 4 - "Notifikasi"
Cohesion: 0.11
Nodes (6): ManajemenKoreksiNilai, ManajemenKoreksiNilai, NotificationDropdown, NotificationsList, Notifikasi, PengajuanKoreksiNilai

### Community 5 - "1. Super Admin"
Cohesion: 0.06
Nodes (32): 1.10 Manajemen Pengaturan Sistem & TTD, 1.1 Dashboard Super Admin, 1.2 Manajemen User, 1.3 Manajemen Siswa, 1.4 Manajemen Guru, 1.5 Manajemen Kelas, 1.6 Manajemen Jadwal, 1.7 Manajemen Mata Pelajaran (+24 more)

### Community 8 - "scripts"
Cohesion: 0.14
Nodes (14): scripts, dev, post-autoload-dump, post-update-cmd, pre-package-uninstall, test, Composer\\Config::disableProcessTimeout, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump (+6 more)

### Community 10 - "5. Finance / Keuangan"
Cohesion: 0.06
Nodes (32): 4.1 Dashboard Murid, 4.2 Jadwal Pelajaran Saya, 4.3 Kehadiran Saya, 4.4 Rapor & Nilai, 4.5 Tagihan SPP & Keuangan, 4.6 Ekstrakurikuler Saya, 4.7 Riwayat Aktivitas, 4. Murid / Portal Siswa (+24 more)

### Community 11 - "PemasukanKas"
Cohesion: 0.19
Nodes (3): ArusKas, ArusKasMasuk, PemasukanKas

### Community 13 - "4. Model Data (Entitas & Field)"
Cohesion: 0.06
Nodes (30): 10. Rekomendasi Tahapan Pengembangan (Roadmap), 1. Ringkasan Sistem Sumber, 2. Peran Pengguna (Aktor) yang Disarankan, 3. Alur Kerja End-to-End, 4.10 `ekstrakurikuler` (dari sheet **EKSKUL**), 4.11 `kehadiran` & `catatan_wali_kelas`, 4.12 `leger` (dari sheet **LEGER**) — VIEW, bukan tabel fisik, 4.13 Output Cetak Rapor (dari sheet **SAMPUL RAPOR**, **ISI SEMESTER 1/2**, **RAPOR INKUL**) (+22 more)

### Community 14 - "Tagihan"
Cohesion: 0.18
Nodes (3): Dashboard, Tagihan, AutomatedSppGenerationTest

### Community 15 - "Guru"
Cohesion: 0.06
Nodes (7): ManajemenGuru, ManajemenKelas, Dashboard, ManajemenSurat, Guru, RiwayatSurat, GuruRoleAccessTest

### Community 17 - "NilaiTahfidz"
Cohesion: 0.17
Nodes (3): InputNilaiTahfidz, SetoranTahfidz, NilaiTahfidz

### Community 19 - "devDependencies"
Cohesion: 0.11
Nodes (17): concurrently, laravel-vite-plugin, devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite (+9 more)

### Community 21 - "Semester"
Cohesion: 0.15
Nodes (4): EkstrakurikulerSaya, Semester, SiswaEkstrakurikuler, ProductionDataSeeder

### Community 22 - "5. Finance / Keuangan"
Cohesion: 0.07
Nodes (29): 4.1 Lihat Jadwal Pelajaran, 4.2 Lihat Kehadiran Saya, 4.3 Lihat Rapor & Nilai, 4.4 Lihat Tagihan SPP & Keuangan, 4.5 Ekstrakurikuler Saya, 4.6 Riwayat Aktivitas Akun, 4. Murid / Portal Siswa, 5.10 Laporan Pemasukan (+21 more)

### Community 23 - "1. Super Admin"
Cohesion: 0.07
Nodes (28): 1.10 Pengaturan Sistem & TTD Elektronik, 1.1 Login & Redirect berdasarkan Role, 1.2 Manajemen User (CRUD), 1.3 Manajemen Siswa, 1.4 Manajemen Guru, 1.5 Manajemen Kelas, 1.6 Manajemen Jadwal Pelajaran, 1.7 Manajemen Mata Pelajaran (+20 more)

### Community 24 - "Perencanaan Sistem Informasi Akademik (Kurikulum Merdeka & Tahfizh) & Keuangan Yayasan"
Cohesion: 0.09
Nodes (21): 1.1 Peran Pengguna (Role Aktor), 1.2 Dual Architecture: Kurikulum Merdeka Umum vs Model Tahfizh, 1.3 Aturan Bisnis Kunci, 1. Ringkasan Kebutuhan & Aturan Bisnis, 2. Arsitektur Informasi (Sitemap per Role), 3.1 Flowchart Verifikasi Keabsahan Dokumen via QR Code, 3. Flowchart Proses Bisnis & Verifikasi QR Code, 4.1 Detail Struktur Tabel Database (+13 more)

### Community 27 - "AREA AUDIT & ASPEK EVALUASI"
Cohesion: 0.14
Nodes (13): 1. Konsistensi Design System & Visual Aesthetics, 2. Responsivitas Lintas Layar (Cross-Device & Cross-Resolution), 3. Kejelasan Alur Pengguna (User Flow & Micro-Interactions), 4. Efisiensi Form Input Nilai (Ergonomi Kerja Guru), 5. Aksesibilitas (Accessibility / WCAG AA) & Feedback UI, 6. Desain Output Cetak PDF Rapor & Resi STT, AREA AUDIT & ASPEK EVALUASI, BATASAN REVIEW (+5 more)

### Community 28 - "auth.php"
Cohesion: 0.17
Nodes (6): FinanceExportController, RoleMiddleware, Login, Closure, Illuminate\Http\Request, Symfony\Component\HttpFoundation\Response

### Community 29 - "Prompt: Review Logika Bisnis — Sistem Informasi Akademik (Kurikulum Merdeka & Tahfizh) & Keuangan Yayasan"
Cohesion: 0.15
Nodes (12): 1. Konsistensi Penilaian Kurikulum Merdeka & Auto-Narasi, 2. Isosiasi & Integrasi Model Tahfizh vs Rombel Umum, 3. Keamanan & Integritas QR Code Keabsahan Dokumen, 4. Trace End-to-End Alur Kritis, 5. Edge Cases & Penanganan Transisi State, AREA AUDIT & TUGAS REVIEW, BATASAN REVIEW, CHECKLIST TITIK RAWAN KHUSUS (Wajib Diverifikasi Statusnya) (+4 more)

### Community 30 - "TahunAjaran"
Cohesion: 0.15
Nodes (3): LaporanTunggakan, TahunAjaran, KalenderAkademikTest

### Community 31 - "GuruMapelKelas"
Cohesion: 0.15
Nodes (4): GuruMapelKelas, SiswaKelas, DemoDataSeeder, RaporOrangTuaSeeder

### Community 32 - "Pembayaran"
Cohesion: 0.12
Nodes (3): DanaBos, LaporanPemasukan, Pembayaran

### Community 34 - "require"
Cohesion: 0.15
Nodes (13): require, barryvdh/laravel-dompdf, blade-ui-kit/blade-icons, chillerlan/php-qrcode, laravel/framework, laravel/octane, laravel/tinker, livewire/livewire (+5 more)

### Community 36 - "Role"
Cohesion: 0.06
Nodes (13): ManajemenUser, ManajemenKaryawan, Role, DatabaseSeeder, JenisTagihanSeeder, KategoriPengeluaranSeeder, KomponenNilaiSeeder, PengaturanSeeder (+5 more)

### Community 37 - "Standar Desain UI Komponen (Buttons, Cards, Modals & Alerts) — SIAKAD"
Cohesion: 0.18
Nodes (10): 1. Standar Desain Kartu (Cards), 2. Standar Desain Tombol (Buttons), 3. Integrasi MicroModal.js untuk Alert & Konfirmasi Dialog, 4. Standar Warna Status (Status Badges), **A. Primary Content Card**, **A. Struktur HTML MicroModal (`resources/views/components/layouts/app.blade.php`)**, **B. Cara Penggunaan di JavaScript / Alpine.js**, **B. Hero / Header Banner Card** (+2 more)

### Community 38 - ".run"
Cohesion: 0.22
Nodes (3): ManajemenPeminjaman, Peminjaman, FinanceSeeder

### Community 41 - "Livewire\Component"
Cohesion: 0.09
Nodes (5): ArusMasuk, TabunganSaya, AuditLog, Livewire\Component, Livewire\WithPagination

### Community 43 - "pengajuan-dana.blade.php"
Cohesion: 0.22
Nodes (8): approveByKepalaYayasan({{ $item->id }}), approveByKoordinator({{ $item->id }}), openModal, openRejectModal({{ $item->id }}), realisasikanDana({{ $item->id }}), rejectPengajuan, closeModal, $set(

### Community 44 - "manajemen-gaji-guru.blade.php"
Cohesion: 0.22
Nodes (8): closeEditModal, closeGenerateModal, deleteDraft({{ $sal->id }}), generateDrafts, openEditModal({{ $sal->id }}), openGenerateModal, paySalary({{ $sal->id }}), saveEdit

### Community 45 - "manajemen-kurikulum-merdeka.blade.php"
Cohesion: 0.22
Nodes (8): closeLmModal, closeTpModal, editLingkupMateri({{ $lm->id }}), editTp({{ $tp->id }}), openLmModal, openTpModal, openTpModal({{ $lm->id }}), saveTemplate

### Community 46 - "require-dev"
Cohesion: 0.22
Nodes (9): require-dev, fakerphp/faker, laravel/pail, laravel/pao, laravel/pint, mockery/mockery, nunomaduro/collision, pestphp/pest (+1 more)

### Community 47 - "manajemen-jadwal.blade.php"
Cohesion: 0.22
Nodes (8): delete({{ $jadwal->id }}), delete({{ $sched->id }}), openCreateForDay(, openEdit({{ $jadwal->id }}), openEdit({{ $sched->id }}), openCreate, $set(, selectKelas({{ $k->id }})

### Community 50 - "composer.json"
Cohesion: 0.14
Nodes (13): description, extra, laravel, keywords, dont-discover, license, minimum-stability, name (+5 more)

### Community 51 - "User"
Cohesion: 0.15
Nodes (4): User, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, createUserWithRole()

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

### Community 60 - "overview-pembayaran.blade.php"
Cohesion: 0.40
Nodes (4): closeDetails, kirimReminder({{ $item[, viewDetails({{ $item[, voidPayment({{ $p->id }})

### Community 61 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 62 - "manajemen-kalender-akademik.blade.php"
Cohesion: 0.40
Nodes (4): delete({{ $event->id }}), openCreateModal, openEditModal({{ $event->id }}), closeModal

### Community 63 - "manajemen-mapel.blade.php"
Cohesion: 0.40
Nodes (4): delete({{ $mapel->id }}), openEdit({{ $mapel->id }}), openCreate, $set(

### Community 66 - "data-alumni.blade.php"
Cohesion: 0.50
Nodes (3): cancelEdit, editAlumni({{ $a->id }}), saveAlumni

### Community 68 - "notifications-list.blade.php"
Cohesion: 0.50
Nodes (3): markAsRead({{ $notif->id }}), markAllAsRead, $set(

### Community 69 - "input-pembayaran.blade.php"
Cohesion: 0.50
Nodes (3): pilihSiswaAndTagihan({{ $t->siswa_id }}, {{ $t->id }}), resetSelection, setMetodeBayar(

### Community 70 - "README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 75 - "TestCase"
Cohesion: 0.29
Nodes (3): Illuminate\Foundation\Testing\TestCase, TahfidzParentFeedbackTest, TestCase

### Community 108 - "proses-kenaikan-kelas.blade.php"
Cohesion: 0.50
Nodes (3): prosesKenaikan, $set(, toggleTinggalKelas({{ $siswa->id }})

### Community 200 - "Rapor"
Cohesion: 0.22
Nodes (3): DocumentVerificationController, RaporPdfController, Rapor

### Community 203 - "Pengeluaran"
Cohesion: 0.13
Nodes (4): ArusKasKeluar, LaporanPengeluaran, KategoriPengeluaran, Pengeluaran

### Community 205 - "Controller"
Cohesion: 0.22
Nodes (3): Controller, FinanceReportController, VerifikasiDokumenController

### Community 208 - "post-create-project-cmd"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 210 - ".handle"
Cohesion: 0.40
Nodes (4): GenerateMonthlySpp, GenerateMonthlySppCommand, Command, Illuminate\Console\Command

### Community 211 - "manajemen-remedial.blade.php"
Cohesion: 0.33
Nodes (5): delete({{ $item->id }}), openEdit({{ $item->id }}), openCreate, $set(, updateStatus({{ $item->id }}, 

### Community 215 - "autoload-dev"
Cohesion: 0.67
Nodes (3): autoload-dev, psr-4, Tests\\

### Community 218 - "tabungan-siswa.blade.php"
Cohesion: 0.50
Nodes (3): closeModals, openHistoryModal({{ $siswa->id }}), openTransactionModal({{ $siswa->id }}, 

## Knowledge Gaps
- **350 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+345 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **61 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Siswa` connect `Siswa` to `MataPelajaran`, `AbsensiSiswa`, `Illuminate\Database\Eloquent\Model`, `Notifikasi`, `ProductionDataSeeder.php`, `.getMatrixData`, `Kelas`, `Tagihan`, `Guru`, `NilaiTahfidz`, `JenisTagihan`, `Semester`, `NilaiP5`, `GuruMapelKelas`, `ProsesKenaikanKelas`, `InputPembayaran`, `Role`, `.run`, `OverviewPembayaran`, `Siswa.php`, `ManajemenRemedial`, `TabunganSiswa`, `RekapAbsensiSiswa`, `ManajemenSiswa`, `InputNilaiSiswa`, `Rapor`, `TestCase`, `KelolaRapor`, `DimensiP5`, `Nilai`, `.handle`?**
  _High betweenness centrality (0.050) - this node is a cross-community bridge._
- **Why does `User` connect `User` to `ManajemenKalenderAkademik`, `Illuminate\Database\Eloquent\Model`, `Role`, `UserFactory.php`, `ProductionDataSeeder.php`, `.run`, `Siswa.php`, `Livewire\Component`, `TestCase`, `Tagihan`, `Guru`, `Pengaturan`, `.paySalary`, `Semester`, `ManajemenSiswa`, `auth.php`, `TahunAjaran`, `GuruMapelKelas`?**
  _High betweenness centrality (0.034) - this node is a cross-community bridge._
- **Why does `KomponenNilai` connect `KomponenNilai` to `InputNilaiSiswa`, `Illuminate\Database\Eloquent\Model`, `Role`, `.getMatrixData`, `Siswa.php`, `PengaturanBobotNilai`, `Semester`, `GuruMapelKelas`?**
  _High betweenness centrality (0.016) - this node is a cross-community bridge._
- **Are the 67 inferred relationships involving `Siswa` (e.g. with `.handle()` and `.handle()`) actually correct?**
  _`Siswa` has 67 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _350 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `MataPelajaran` be split into smaller, more focused modules?**
  _Cohesion score 0.05698778833107191 - nodes in this community are weakly interconnected._
- **Should `AbsensiSiswa` be split into smaller, more focused modules?**
  _Cohesion score 0.06161616161616162 - nodes in this community are weakly interconnected._