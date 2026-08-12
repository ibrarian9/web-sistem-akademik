# Graph Report - web-sistem-akademik  (2026-08-13)

## Corpus Check
- 345 files · ~211,667 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1655 nodes · 3129 edges · 233 communities (167 shown, 66 thin omitted)
- Extraction: 83% EXTRACTED · 17% INFERRED · 0% AMBIGUOUS · INFERRED: 544 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `33ecf636`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- MataPelajaran
- ManajemenJadwal
- ManajemenKalenderAkademik
- Illuminate\Database\Eloquent\Model
- Notifikasi
- 1. Super Admin
- Kelas.php
- .getMatrixData
- scripts
- Siswa
- 5. Finance / Keuangan
- Pengaturan
- Kelas
- 4. Model Data (Entitas & Field)
- Tagihan
- ManajemenSurat
- AbsensiSiswa
- Semester
- ManajemenGajiGuru
- devDependencies
- JenisTagihan
- .run
- 5. Finance / Keuangan
- 1. Super Admin
- Perencanaan Sistem Informasi Akademik (Kurikulum Merdeka & Tahfizh) & Keuangan Yayasan
- ManajemenPiketGuru
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
- CapaianPengembanganGuru
- Livewire\Component
- ManajemenRemedial
- pengajuan-dana.blade.php
- manajemen-gaji-guru.blade.php
- manajemen-kurikulum-merdeka.blade.php
- require-dev
- manajemen-jadwal.blade.php
- PengajuanDana
- Nilai
- composer.json
- ESignatureService
- config
- TabunganSiswa
- RekapAbsensiSiswa
- AppServiceProvider
- manajemen-komponen-nilai.blade.php
- manajemen-surat.blade.php
- CapaianGuru
- setup
- overview-pembayaran.blade.php
- psr-4
- manajemen-kalender-akademik.blade.php
- manajemen-mapel.blade.php
- AbsensiDiri
- PemasukanKas
- data-alumni.blade.php
- UserFactory.php
- notifications-list.blade.php
- input-pembayaran.blade.php
- README.md
- InputAbsensiKaryawan
- manajemen-koreksi-nilai.blade.php
- setoran-tahfidz.blade.php
- PengaturanBobotNilai
- AbsensiGuru.php
- SystemErrorLog
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
- Guru
- Siswa.php
- TagihanSpp
- User
- ManajemenKelas
- KehadiranSaya
- manajemen-remedial.blade.php
- RekapAbsensiGuru
- Guru.php
- DanaBos
- tabungan-siswa.blade.php
- FinanceReportController
- ManajemenGuru
- .loadDashboardData
- capaian-pengembangan-diri.blade.php
- capaian-pengembangan-guru.blade.php
- JadwalMengajar
- RiwayatAktivitas
- clearLog
- extra
- dev

## God Nodes (most connected - your core abstractions)
1. `Siswa` - 84 edges
2. `User` - 84 edges
3. `Kelas` - 61 edges
4. `Guru` - 49 edges
5. `Tagihan` - 36 edges
6. `Semester` - 34 edges
7. `Role` - 29 edges
8. `TahunAjaran` - 29 edges
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
- `FinanceReportController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/FinanceReportController.php → app/Http/Controllers/Controller.php

## Import Cycles
- None detected.

## Communities (233 total, 66 thin omitted)

### Community 0 - "MataPelajaran"
Cohesion: 0.07
Nodes (12): InputNilaiSumatif, ManajemenKurikulumMerdeka, ManajemenMapel, LingkupMateri, MataPelajaran, NilaiSas, NilaiSumatifTp, TemplateDeskripsi (+4 more)

### Community 3 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.11
Nodes (8): AbsensiSiswa, DanaBos, JadwalPelajaran, ProyekP5, SubdimensiP5, TargetHafalanTahfidz, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Database\Eloquent\Model

### Community 4 - "Notifikasi"
Cohesion: 0.11
Nodes (6): ManajemenKoreksiNilai, ManajemenKoreksiNilai, NotificationDropdown, NotificationsList, Notifikasi, PengajuanKoreksiNilai

### Community 5 - "1. Super Admin"
Cohesion: 0.06
Nodes (32): 1.10 Manajemen Pengaturan Sistem & TTD, 1.1 Dashboard Super Admin, 1.2 Manajemen User, 1.3 Manajemen Siswa, 1.4 Manajemen Guru, 1.5 Manajemen Kelas, 1.6 Manajemen Jadwal, 1.7 Manajemen Mata Pelajaran (+24 more)

### Community 8 - "scripts"
Cohesion: 0.13
Nodes (15): scripts, post-autoload-dump, post-create-project-cmd, post-update-cmd, pre-package-uninstall, test, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump, Illuminate\\Foundation\\ComposerScripts::prePackageUninstall (+7 more)

### Community 9 - "Siswa"
Cohesion: 0.11
Nodes (3): ManajemenSiswa, DataAlumni, Siswa

### Community 10 - "5. Finance / Keuangan"
Cohesion: 0.06
Nodes (32): 4.1 Dashboard Murid, 4.2 Jadwal Pelajaran Saya, 4.3 Kehadiran Saya, 4.4 Rapor & Nilai, 4.5 Tagihan SPP & Keuangan, 4.6 Ekstrakurikuler Saya, 4.7 Riwayat Aktivitas, 4. Murid / Portal Siswa (+24 more)

### Community 11 - "Pengaturan"
Cohesion: 0.19
Nodes (4): VerifikasiDokumenController, ManajemenPengaturan, Pengaturan, PengaturanSeeder

### Community 12 - "Kelas"
Cohesion: 0.08
Nodes (4): Dashboard, Dashboard, PlottingSiswaKelas, Kelas

### Community 13 - "4. Model Data (Entitas & Field)"
Cohesion: 0.06
Nodes (30): 10. Rekomendasi Tahapan Pengembangan (Roadmap), 1. Ringkasan Sistem Sumber, 2. Peran Pengguna (Aktor) yang Disarankan, 3. Alur Kerja End-to-End, 4.10 `ekstrakurikuler` (dari sheet **EKSKUL**), 4.11 `kehadiran` & `catatan_wali_kelas`, 4.12 `leger` (dari sheet **LEGER**) — VIEW, bukan tabel fisik, 4.13 Output Cetak Rapor (dari sheet **SAMPUL RAPOR**, **ISI SEMESTER 1/2**, **RAPOR INKUL**) (+22 more)

### Community 14 - "Tagihan"
Cohesion: 0.19
Nodes (3): Tagihan, Command, AutomatedSppGenerationTest

### Community 17 - "Semester"
Cohesion: 0.12
Nodes (5): InputNilaiTahfidz, SetoranTahfidz, NilaiTahfidz, Semester, TahfidzMutabaahSeeder

### Community 18 - "ManajemenGajiGuru"
Cohesion: 0.15
Nodes (3): ManajemenGajiGuru, GajiGuru, NotificationService

### Community 19 - "devDependencies"
Cohesion: 0.11
Nodes (17): concurrently, laravel-vite-plugin, devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite (+9 more)

### Community 21 - ".run"
Cohesion: 0.13
Nodes (6): Ekstrakurikuler, RaporDetail, SiswaEkstrakurikuler, SiswaKelas, ProductionDataSeeder, RaporOrangTuaSeeder

### Community 22 - "5. Finance / Keuangan"
Cohesion: 0.07
Nodes (29): 4.1 Lihat Jadwal Pelajaran, 4.2 Lihat Kehadiran Saya, 4.3 Lihat Rapor & Nilai, 4.4 Lihat Tagihan SPP & Keuangan, 4.5 Ekstrakurikuler Saya, 4.6 Riwayat Aktivitas Akun, 4. Murid / Portal Siswa, 5.10 Laporan Pemasukan (+21 more)

### Community 23 - "1. Super Admin"
Cohesion: 0.07
Nodes (28): 1.10 Pengaturan Sistem & TTD Elektronik, 1.1 Login & Redirect berdasarkan Role, 1.2 Manajemen User (CRUD), 1.3 Manajemen Siswa, 1.4 Manajemen Guru, 1.5 Manajemen Kelas, 1.6 Manajemen Jadwal Pelajaran, 1.7 Manajemen Mata Pelajaran (+20 more)

### Community 24 - "Perencanaan Sistem Informasi Akademik (Kurikulum Merdeka & Tahfizh) & Keuangan Yayasan"
Cohesion: 0.09
Nodes (21): 1.1 Peran Pengguna (Role Aktor), 1.2 Dual Architecture: Kurikulum Merdeka Umum vs Model Tahfizh, 1.3 Aturan Bisnis Kunci, 1. Ringkasan Kebutuhan & Aturan Bisnis, 2. Arsitektur Informasi (Sitemap per Role), 3.1 Flowchart Verifikasi Keabsahan Dokumen via QR Code, 3. Flowchart Proses Bisnis & Verifikasi QR Code, 4.1 Detail Struktur Tabel Database (+13 more)

### Community 26 - "NilaiP5"
Cohesion: 0.19
Nodes (3): PenilaianP5, DimensiP5, NilaiP5

### Community 27 - "AREA AUDIT & ASPEK EVALUASI"
Cohesion: 0.14
Nodes (13): 1. Konsistensi Design System & Visual Aesthetics, 2. Responsivitas Lintas Layar (Cross-Device & Cross-Resolution), 3. Kejelasan Alur Pengguna (User Flow & Micro-Interactions), 4. Efisiensi Form Input Nilai (Ergonomi Kerja Guru), 5. Aksesibilitas (Accessibility / WCAG AA) & Feedback UI, 6. Desain Output Cetak PDF Rapor & Resi STT, AREA AUDIT & ASPEK EVALUASI, BATASAN REVIEW (+5 more)

### Community 28 - "auth.php"
Cohesion: 0.09
Nodes (9): FinanceExportController, RoleMiddleware, Login, ProfilSaya, AuditLogger, bootAuditable(), Closure, Illuminate\Http\Request (+1 more)

### Community 29 - "Prompt: Review Logika Bisnis — Sistem Informasi Akademik (Kurikulum Merdeka & Tahfizh) & Keuangan Yayasan"
Cohesion: 0.15
Nodes (12): 1. Konsistensi Penilaian Kurikulum Merdeka & Auto-Narasi, 2. Isosiasi & Integrasi Model Tahfizh vs Rombel Umum, 3. Keamanan & Integritas QR Code Keabsahan Dokumen, 4. Trace End-to-End Alur Kritis, 5. Edge Cases & Penanganan Transisi State, AREA AUDIT & TUGAS REVIEW, BATASAN REVIEW, CHECKLIST TITIK RAWAN KHUSUS (Wajib Diverifikasi Statusnya) (+4 more)

### Community 30 - "TahunAjaran"
Cohesion: 0.13
Nodes (4): LaporanTunggakan, TahunAjaran, DemoDataSeeder, KalenderAkademikTest

### Community 32 - "Pembayaran"
Cohesion: 0.11
Nodes (4): Dashboard, LaporanPemasukan, Dashboard, Pembayaran

### Community 34 - "require"
Cohesion: 0.15
Nodes (13): require, barryvdh/laravel-dompdf, blade-ui-kit/blade-icons, chillerlan/php-qrcode, laravel/framework, laravel/octane, laravel/tinker, livewire/livewire (+5 more)

### Community 36 - "Role"
Cohesion: 0.05
Nodes (16): ManajemenUser, ManajemenKaryawan, Role, DatabaseSeeder, JenisTagihanSeeder, KategoriPengeluaranSeeder, KomponenNilaiSeeder, ProductionAccountsSeeder (+8 more)

### Community 37 - "Standar Desain UI Komponen (Buttons, Cards, Modals & Alerts) — SIAKAD"
Cohesion: 0.18
Nodes (10): 1. Standar Desain Kartu (Cards), 2. Standar Desain Tombol (Buttons), 3. Integrasi MicroModal.js untuk Alert & Konfirmasi Dialog, 4. Standar Warna Status (Status Badges), **A. Primary Content Card**, **A. Struktur HTML MicroModal (`resources/views/components/layouts/app.blade.php`)**, **B. Cara Penggunaan di JavaScript / Alpine.js**, **B. Hero / Header Banner Card** (+2 more)

### Community 38 - ".run"
Cohesion: 0.22
Nodes (3): ManajemenPeminjaman, Peminjaman, FinanceSeeder

### Community 41 - "Livewire\Component"
Cohesion: 0.12
Nodes (5): ArusMasuk, EkstrakurikulerSaya, AuditLog, Livewire\Component, Livewire\WithPagination

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

### Community 49 - "Nilai"
Cohesion: 0.09
Nodes (5): InputNilaiSiswa, RaporNilai, ManajemenKomponenNilai, KomponenNilai, Nilai

### Community 50 - "composer.json"
Cohesion: 0.14
Nodes (13): autoload-dev, psr-4, description, keywords, license, minimum-stability, name, prefer-stable (+5 more)

### Community 51 - "ESignatureService"
Cohesion: 0.19
Nodes (3): DocumentVerificationController, ESignatureService, ESignatureTest

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

### Community 65 - "PemasukanKas"
Cohesion: 0.19
Nodes (3): ArusKas, ArusKasMasuk, PemasukanKas

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

### Community 71 - "InputAbsensiKaryawan"
Cohesion: 0.23
Nodes (3): InputAbsensiKaryawan, AbsensiGuru, Livewire\WithFileUploads

### Community 108 - "proses-kenaikan-kelas.blade.php"
Cohesion: 0.50
Nodes (3): prosesKenaikan, $set(, toggleTinggalKelas({{ $siswa->id }})

### Community 200 - "Rapor"
Cohesion: 0.20
Nodes (4): Controller, RaporPdfController, Rapor, RaporTahfidzDetail

### Community 203 - "Pengeluaran"
Cohesion: 0.13
Nodes (4): ArusKasKeluar, LaporanPengeluaran, KategoriPengeluaran, Pengeluaran

### Community 206 - "Siswa.php"
Cohesion: 0.17
Nodes (3): GenerateMonthlySpp, GenerateMonthlySppCommand, Illuminate\Console\Command

### Community 208 - "User"
Cohesion: 0.13
Nodes (5): User, Illuminate\Foundation\Auth\User, Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Notifications\Notifiable, createUserWithRole()

### Community 211 - "manajemen-remedial.blade.php"
Cohesion: 0.33
Nodes (5): delete({{ $item->id }}), openCreate, openEdit({{ $item->id }}), $set(, updateStatus({{ $item->id }}, 

### Community 218 - "tabungan-siswa.blade.php"
Cohesion: 0.50
Nodes (3): closeModals, openHistoryModal({{ $siswa->id }}), openTransactionModal({{ $siswa->id }}, 

### Community 224 - "capaian-pengembangan-diri.blade.php"
Cohesion: 0.40
Nodes (4): closeModal, delete({{ $item->id }}), openCreate, openEdit({{ $item->id }})

### Community 225 - "capaian-pengembangan-guru.blade.php"
Cohesion: 0.50
Nodes (3): openEvaluateModal({{ $item->id }}), closeModal, delete({{ $item->id }})

### Community 231 - "extra"
Cohesion: 0.67
Nodes (3): extra, laravel, dont-discover

### Community 232 - "dev"
Cohesion: 0.67
Nodes (3): dev, Composer\\Config::disableProcessTimeout, npx concurrently -c \"#93c5fd,#c4b5fd,#fb7185,#fdba74\" \"php artisan serve\" \"php artisan queue:listen --tries=1 --timeout=0\" \"php artisan pail --timeout=0\" \"npm run dev\" --names=server,queue,logs,vite --kill-others

## Knowledge Gaps
- **358 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+353 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **66 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Kelas` connect `Kelas` to `MataPelajaran`, `Illuminate\Database\Eloquent\Model`, `Kelas.php`, `.getMatrixData`, `Siswa`, `Semester`, `JenisTagihan`, `.run`, `NilaiP5`, `TahunAjaran`, `Pembayaran`, `ProsesKenaikanKelas`, `InputPembayaran`, `Role`, `ManajemenRemedial`, `TabunganSiswa`, `RekapAbsensiSiswa`, `KelolaRapor`, `ManajemenKelas`, `Guru.php`?**
  _High betweenness centrality (0.030) - this node is a cross-community bridge._
- **Why does `Siswa` connect `Siswa` to `MataPelajaran`, `Illuminate\Database\Eloquent\Model`, `Notifikasi`, `Kelas.php`, `.getMatrixData`, `Kelas`, `Tagihan`, `ManajemenSurat`, `AbsensiSiswa`, `Semester`, `JenisTagihan`, `.run`, `NilaiP5`, `TahunAjaran`, `Pembayaran`, `ProsesKenaikanKelas`, `InputPembayaran`, `Role`, `.run`, `OverviewPembayaran`, `ManajemenRemedial`, `Nilai`, `TabunganSiswa`, `RekapAbsensiSiswa`, `Rapor`, `KelolaRapor`, `Siswa.php`, `Guru.php`?**
  _High betweenness centrality (0.029) - this node is a cross-community bridge._
- **Why does `User` connect `User` to `Illuminate\Database\Eloquent\Model`, `Role`, `UserFactory.php`, `Kelas.php`, `InputAbsensiKaryawan`, `.run`, `Livewire\Component`, `Siswa`, `Tagihan`, `Siswa.php`, `Semester`, `ESignatureService`, `Guru.php`, `.run`, `TahunAjaran`, `InputNilaiSumatif.php`, `auth.php`, `ManajemenGuru`?**
  _High betweenness centrality (0.028) - this node is a cross-community bridge._
- **Are the 67 inferred relationships involving `Siswa` (e.g. with `.handle()` and `.handle()`) actually correct?**
  _`Siswa` has 67 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _358 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `MataPelajaran` be split into smaller, more focused modules?**
  _Cohesion score 0.06604324956165984 - nodes in this community are weakly interconnected._
- **Should `Illuminate\Database\Eloquent\Model` be split into smaller, more focused modules?**
  _Cohesion score 0.10935143288084465 - nodes in this community are weakly interconnected._