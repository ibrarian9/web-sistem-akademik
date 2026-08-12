# Graph Report - web-sistem-akademik  (2026-08-09)

## Corpus Check
- 315 files · ~200,312 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1533 nodes · 2890 edges · 206 communities (159 shown, 47 thin omitted)
- Extraction: 83% EXTRACTED · 17% INFERRED · 0% AMBIGUOUS · INFERRED: 502 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `33ecf636`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- MataPelajaran
- GuruMapelKelas
- ManajemenKalenderAkademik
- Illuminate\Database\Eloquent\Model
- Notifikasi
- 1. Super Admin
- AbsensiSiswa
- scripts
- Siswa
- 5. Finance / Keuangan
- PemasukanKas
- Kelas
- 4. Model Data (Entitas & Field)
- Tagihan
- Guru
- Illuminate\Database\Seeder
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
- ProfilSaya
- Prompt: Review Logika Bisnis — Sistem Informasi Akademik (Kurikulum Merdeka & Tahfizh) & Keuangan Yayasan
- TahunAjaran
- .run
- Pembayaran
- ProsesKenaikanKelas
- require
- InputPembayaran
- Pengaturan
- Standar Desain UI Komponen (Buttons, Cards, Modals & Alerts) — SIAKAD
- .run
- OverviewPembayaran
- Livewire\Component
- ProductionDataSeeder.php
- Role
- pengajuan-dana.blade.php
- manajemen-gaji-guru.blade.php
- manajemen-kurikulum-merdeka.blade.php
- require-dev
- manajemen-jadwal.blade.php
- ManajemenKaryawan
- .handle
- composer.json
- User
- config
- Kelas.php
- RekapAbsensiSiswa
- AppServiceProvider
- manajemen-komponen-nilai.blade.php
- manajemen-surat.blade.php
- InputAbsensiKaryawan
- setup
- overview-pembayaran.blade.php
- psr-4
- manajemen-kalender-akademik.blade.php
- manajemen-mapel.blade.php
- Dashboard
- DanaBos
- data-alumni.blade.php
- UserFactory.php
- notifications-list.blade.php
- input-pembayaran.blade.php
- README.md
- TestCase
- manajemen-koreksi-nilai.blade.php
- setoran-tahfidz.blade.php
- keywords
- Guru.php
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
- post-autoload-dump
- rules/graphify.md
- workflows/graphify.md
- Pengeluaran
- RiwayatAktivitas

## God Nodes (most connected - your core abstractions)
1. `Siswa` - 76 edges
2. `User` - 75 edges
3. `Kelas` - 58 edges
4. `Guru` - 43 edges
5. `Tagihan` - 36 edges
6. `Semester` - 33 edges
7. `Rapor` - 29 edges
8. `Role` - 28 edges
9. `TahunAjaran` - 28 edges
10. `GuruMapelKelas` - 24 edges

## Surprising Connections (you probably didn't know these)
- `createUserWithRole()` --calls--> `Role`  [INFERRED]
  tests/Feature/RbacAndNavigationTest.php → app/Models/Role.php
- `createUserWithRole()` --calls--> `User`  [EXTRACTED]
  tests/Feature/RbacAndNavigationTest.php → app/Models/User.php
- `FinanceExportController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/FinanceExportController.php → app/Http/Controllers/Controller.php
- `RaporPdfController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/RaporPdfController.php → app/Http/Controllers/Controller.php
- `ManajemenKoreksiNilai` --inherits--> `ManajemenKoreksiNilai`  [EXTRACTED]
  app/Livewire/Pengawas/ManajemenKoreksiNilai.php → app/Livewire/Koordinator/ManajemenKoreksiNilai.php

## Import Cycles
- None detected.

## Communities (206 total, 47 thin omitted)

### Community 0 - "MataPelajaran"
Cohesion: 0.06
Nodes (12): InputNilaiSumatif, ManajemenKurikulumMerdeka, ManajemenMapel, LingkupMateri, MataPelajaran, NilaiSas, NilaiSumatifTp, TemplateDeskripsi (+4 more)

### Community 1 - "GuruMapelKelas"
Cohesion: 0.06
Nodes (9): Dashboard, JadwalMengajar, PengaturanBobotNilai, JadwalPelajaran, RekapNilai, ManajemenJadwal, BobotNilaiGuru, GuruMapelKelas (+1 more)

### Community 2 - "ManajemenKalenderAkademik"
Cohesion: 0.15
Nodes (3): RekapAbsensiGuru, ManajemenKalenderAkademik, KalenderAkademik

### Community 3 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.12
Nodes (8): AbsensiSiswa, DanaBos, JadwalPelajaran, ProyekP5, SubdimensiP5, TargetHafalanTahfidz, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Database\Eloquent\Model

### Community 4 - "Notifikasi"
Cohesion: 0.09
Nodes (7): KelolaRapor, ManajemenKoreksiNilai, ManajemenKoreksiNilai, NotificationDropdown, NotificationsList, Notifikasi, PengajuanKoreksiNilai

### Community 5 - "1. Super Admin"
Cohesion: 0.06
Nodes (32): 1.10 Manajemen Pengaturan Sistem & TTD, 1.1 Dashboard Super Admin, 1.2 Manajemen User, 1.3 Manajemen Siswa, 1.4 Manajemen Guru, 1.5 Manajemen Kelas, 1.6 Manajemen Jadwal, 1.7 Manajemen Mata Pelajaran (+24 more)

### Community 6 - "AbsensiSiswa"
Cohesion: 0.16
Nodes (3): AbsensiSiswa, Dashboard, KehadiranSaya

### Community 8 - "scripts"
Cohesion: 0.13
Nodes (15): scripts, dev, post-create-project-cmd, post-update-cmd, pre-package-uninstall, test, Composer\\Config::disableProcessTimeout, Illuminate\\Foundation\\ComposerScripts::prePackageUninstall (+7 more)

### Community 9 - "Siswa"
Cohesion: 0.11
Nodes (3): ManajemenSiswa, DataAlumni, Siswa

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
Cohesion: 0.13
Nodes (4): TagihanSpp, Dashboard, Tagihan, AutomatedSppGenerationTest

### Community 15 - "Guru"
Cohesion: 0.06
Nodes (7): ManajemenGuru, ManajemenKelas, Dashboard, ManajemenSurat, Guru, RiwayatSurat, GuruRoleAccessTest

### Community 16 - "Illuminate\Database\Seeder"
Cohesion: 0.14
Nodes (7): DatabaseSeeder, JenisTagihanSeeder, KategoriPengeluaranSeeder, KomponenNilaiSeeder, PengaturanSeeder, RoleSeeder, Illuminate\Database\Seeder

### Community 17 - "NilaiTahfidz"
Cohesion: 0.14
Nodes (5): RaporPdfController, InputNilaiTahfidz, SetoranTahfidz, NilaiTahfidz, RaporTahfidzDetail

### Community 18 - "ManajemenGajiGuru"
Cohesion: 0.14
Nodes (3): ManajemenGajiGuru, GajiGuru, NotificationService

### Community 19 - "devDependencies"
Cohesion: 0.11
Nodes (17): concurrently, laravel-vite-plugin, devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite (+9 more)

### Community 21 - "Semester"
Cohesion: 0.15
Nodes (4): Semester, DemoDataSeeder, TahfidzMutabaahSeeder, TahfidzParentFeedbackTest

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

### Community 28 - "ProfilSaya"
Cohesion: 0.12
Nodes (7): FinanceExportController, RoleMiddleware, Login, ProfilSaya, Closure, Illuminate\Http\Request, Symfony\Component\HttpFoundation\Response

### Community 29 - "Prompt: Review Logika Bisnis — Sistem Informasi Akademik (Kurikulum Merdeka & Tahfizh) & Keuangan Yayasan"
Cohesion: 0.15
Nodes (12): 1. Konsistensi Penilaian Kurikulum Merdeka & Auto-Narasi, 2. Isosiasi & Integrasi Model Tahfizh vs Rombel Umum, 3. Keamanan & Integritas QR Code Keabsahan Dokumen, 4. Trace End-to-End Alur Kritis, 5. Edge Cases & Penanganan Transisi State, AREA AUDIT & TUGAS REVIEW, BATASAN REVIEW, CHECKLIST TITIK RAWAN KHUSUS (Wajib Diverifikasi Statusnya) (+4 more)

### Community 30 - "TahunAjaran"
Cohesion: 0.16
Nodes (3): LaporanTunggakan, TahunAjaran, KalenderAkademikTest

### Community 31 - ".run"
Cohesion: 0.06
Nodes (10): InputNilaiSiswa, RaporNilai, ManajemenKomponenNilai, Ekstrakurikuler, KomponenNilai, Nilai, RaporDetail, SiswaEkstrakurikuler (+2 more)

### Community 32 - "Pembayaran"
Cohesion: 0.13
Nodes (3): LaporanPemasukan, Dashboard, Pembayaran

### Community 34 - "require"
Cohesion: 0.15
Nodes (13): require, barryvdh/laravel-dompdf, blade-ui-kit/blade-icons, chillerlan/php-qrcode, laravel/framework, laravel/octane, laravel/tinker, livewire/livewire (+5 more)

### Community 36 - "Pengaturan"
Cohesion: 0.07
Nodes (10): Controller, DocumentVerificationController, FinanceReportController, VerifikasiDokumenController, ManajemenPengaturan, ManajemenPiketGuru, JadwalPiketGuru, Pengaturan (+2 more)

### Community 37 - "Standar Desain UI Komponen (Buttons, Cards, Modals & Alerts) — SIAKAD"
Cohesion: 0.18
Nodes (10): 1. Standar Desain Kartu (Cards), 2. Standar Desain Tombol (Buttons), 3. Integrasi MicroModal.js untuk Alert & Konfirmasi Dialog, 4. Standar Warna Status (Status Badges), **A. Primary Content Card**, **A. Struktur HTML MicroModal (`resources/views/components/layouts/app.blade.php`)**, **B. Cara Penggunaan di JavaScript / Alpine.js**, **B. Hero / Header Banner Card** (+2 more)

### Community 38 - ".run"
Cohesion: 0.18
Nodes (4): ManajemenPeminjaman, Peminjaman, FinanceSeeder, ProductionDataSeeder

### Community 40 - "Livewire\Component"
Cohesion: 0.13
Nodes (4): ArusMasuk, AuditLog, Livewire\Component, Livewire\WithPagination

### Community 42 - "Role"
Cohesion: 0.20
Nodes (4): ManajemenUser, Role, UserSeeder, KenaikanKelasExportTest

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

### Community 49 - ".handle"
Cohesion: 0.40
Nodes (4): GenerateMonthlySpp, GenerateMonthlySppCommand, Command, Illuminate\Console\Command

### Community 50 - "composer.json"
Cohesion: 0.14
Nodes (13): autoload-dev, psr-4, description, extra, laravel, dont-discover, license, minimum-stability (+5 more)

### Community 51 - "User"
Cohesion: 0.13
Nodes (5): User, Illuminate\Foundation\Auth\User, Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Notifications\Notifiable, createUserWithRole()

### Community 52 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 56 - "manajemen-komponen-nilai.blade.php"
Cohesion: 0.33
Nodes (5): delete({{ $komponen[, openEdit({{ $komponen[, closeModal, openCreate, $set(

### Community 57 - "manajemen-surat.blade.php"
Cohesion: 0.33
Nodes (5): deleteRiwayat({{ $r->id }}), downloadCurrentPdf, downloadPdfById({{ $r->id }}), loadRiwayatSurat({{ $r->id }}), $set(

### Community 58 - "InputAbsensiKaryawan"
Cohesion: 0.15
Nodes (4): AbsensiDiri, InputAbsensiKaryawan, AbsensiGuru, Livewire\WithFileUploads

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

### Community 74 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 108 - "proses-kenaikan-kelas.blade.php"
Cohesion: 0.50
Nodes (3): prosesKenaikan, $set(, toggleTinggalKelas({{ $siswa->id }})

### Community 200 - "post-autoload-dump"
Cohesion: 0.67
Nodes (3): post-autoload-dump, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump, @php artisan package:discover --ansi

### Community 203 - "Pengeluaran"
Cohesion: 0.13
Nodes (4): ArusKasKeluar, LaporanPengeluaran, KategoriPengeluaran, Pengeluaran

## Knowledge Gaps
- **343 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+338 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **47 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Siswa` connect `Siswa` to `MataPelajaran`, `GuruMapelKelas`, `Illuminate\Database\Eloquent\Model`, `Notifikasi`, `AbsensiSiswa`, `Siswa.php`, `Kelas`, `Tagihan`, `Guru`, `NilaiTahfidz`, `JenisTagihan`, `Semester`, `NilaiP5`, `.run`, `Pembayaran`, `ProsesKenaikanKelas`, `InputPembayaran`, `.run`, `OverviewPembayaran`, `Role`, `.handle`, `RekapAbsensiSiswa`, `Guru.php`, `DimensiP5`?**
  _High betweenness centrality (0.040) - this node is a cross-community bridge._
- **Why does `User` connect `User` to `Illuminate\Database\Eloquent\Model`, `Siswa.php`, `Siswa`, `Tagihan`, `Guru`, `ManajemenGajiGuru`, `Semester`, `ProfilSaya`, `TahunAjaran`, `.run`, `Pengaturan`, `.run`, `Livewire\Component`, `ProductionDataSeeder.php`, `Role`, `ManajemenKaryawan`, `Kelas.php`, `InputAbsensiKaryawan`, `UserFactory.php`, `Guru.php`, `Notifikasi.php`?**
  _High betweenness centrality (0.022) - this node is a cross-community bridge._
- **Why does `Guru` connect `Guru` to `Pembayaran`, `ManajemenKalenderAkademik`, `Illuminate\Database\Eloquent\Model`, `Pengaturan`, `.run`, `Role`, `Guru.php`, `Tagihan`, `ManajemenKaryawan`, `ManajemenGajiGuru`, `Semester`, `InputAbsensiKaryawan`, `.run`?**
  _High betweenness centrality (0.016) - this node is a cross-community bridge._
- **Are the 61 inferred relationships involving `Siswa` (e.g. with `.handle()` and `.handle()`) actually correct?**
  _`Siswa` has 61 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _343 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `MataPelajaran` be split into smaller, more focused modules?**
  _Cohesion score 0.06400409626216078 - nodes in this community are weakly interconnected._
- **Should `GuruMapelKelas` be split into smaller, more focused modules?**
  _Cohesion score 0.062040816326530614 - nodes in this community are weakly interconnected._