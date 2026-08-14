# Graph Report - web-sistem-akademik  (2026-08-14)

## Corpus Check
- 353 files · ~222,790 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1702 nodes · 3239 edges · 234 communities (169 shown, 65 thin omitted)
- Extraction: 83% EXTRACTED · 17% INFERRED · 0% AMBIGUOUS · INFERRED: 566 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `881d4cb8`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- MataPelajaran
- ManajemenJadwal
- ManajemenKalenderAkademik
- Illuminate\Database\Eloquent\Model
- Notifikasi
- 1. Super Admin
- ProductionDataSeeder.php
- Illuminate\Database\Seeder
- scripts
- Siswa
- 5. Finance / Keuangan
- InputAbsensiKaryawan
- Kelas
- 4. Model Data (Entitas & Field)
- InputPembayaran
- ManajemenSurat
- Pengaturan
- Semester
- ManajemenGajiGuru
- devDependencies
- ManajemenTagihan
- AbsensiSiswa
- 5. Finance / Keuangan
- 1. Super Admin
- Perencanaan Sistem Informasi Akademik (Kurikulum Merdeka & Tahfizh) & Keuangan Yayasan
- Siswa.php
- NilaiP5
- AREA AUDIT & ASPEK EVALUASI
- TabunganSiswa
- Prompt: Review Logika Bisnis — Sistem Informasi Akademik (Kurikulum Merdeka & Tahfizh) & Keuangan Yayasan
- TahunAjaran
- .loadDashboardData
- Pembayaran
- ProsesKenaikanKelas
- require
- CapaianGuru
- Tagihan
- Standar Desain UI Komponen (Buttons, Cards, Modals & Alerts) — SIAKAD
- Role
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
- KehadiranSaya
- config
- ProfilSaya
- RekapAbsensiSiswa
- AppServiceProvider
- manajemen-komponen-nilai.blade.php
- manajemen-surat.blade.php
- setup
- overview-pembayaran.blade.php
- psr-4
- manajemen-kalender-akademik.blade.php
- manajemen-mapel.blade.php
- Pengeluaran
- PemasukanKas
- data-alumni.blade.php
- UserFactory.php
- notifications-list.blade.php
- input-pembayaran.blade.php
- README.md
- AuditLog
- manajemen-koreksi-nilai.blade.php
- setoran-tahfidz.blade.php
- User
- RiwayatAktivitas
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
- audit-log.blade.php
- rules/graphify.md
- workflows/graphify.md
- ManajemenGuru
- GuruMapelKelas
- Guru
- .run
- ManajemenKaryawan
- Livewire\WithPagination
- ManajemenKelas
- Dashboard
- manajemen-remedial.blade.php
- Rapor
- TutorialDanFaq
- .getMatrixData
- PengaturanBobotNilai
- tabungan-siswa.blade.php
- TagihanSpp
- .mount
- extra
- capaian-pengembangan-diri.blade.php
- capaian-pengembangan-guru.blade.php
- dev
- tutorial-dan-faq.blade.php
- system-error-log.blade.php
- JadwalMengajar

## God Nodes (most connected - your core abstractions)
1. `User` - 91 edges
2. `Siswa` - 87 edges
3. `Kelas` - 64 edges
4. `Guru` - 52 edges
5. `Semester` - 38 edges
6. `Tagihan` - 36 edges
7. `TahunAjaran` - 34 edges
8. `Role` - 33 edges
9. `GuruMapelKelas` - 25 edges
10. `ManajemenKalenderAkademik` - 24 edges

## Surprising Connections (you probably didn't know these)
- `createUserWithRole()` --calls--> `Role`  [INFERRED]
  tests/Feature/RbacAndNavigationTest.php → app/Models/Role.php
- `createUserWithRole()` --calls--> `User`  [EXTRACTED]
  tests/Feature/RbacAndNavigationTest.php → app/Models/User.php
- `FinanceReportController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/FinanceReportController.php → app/Http/Controllers/Controller.php
- `VerifikasiDokumenController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/VerifikasiDokumenController.php → app/Http/Controllers/Controller.php
- `ManajemenKoreksiNilai` --inherits--> `ManajemenKoreksiNilai`  [EXTRACTED]
  app/Livewire/Pengawas/ManajemenKoreksiNilai.php → app/Livewire/Koordinator/ManajemenKoreksiNilai.php

## Import Cycles
- None detected.

## Communities (234 total, 65 thin omitted)

### Community 0 - "MataPelajaran"
Cohesion: 0.07
Nodes (12): InputNilaiSumatif, ManajemenKurikulumMerdeka, ManajemenMapel, LingkupMateri, MataPelajaran, NilaiSas, NilaiSumatifTp, TemplateDeskripsi (+4 more)

### Community 3 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.11
Nodes (10): AbsensiSiswa, DanaBos, JadwalPelajaran, JadwalRemedial, ProyekP5, SubdimensiP5, TargetHafalanTahfidz, Illuminate\Database\Eloquent\Factories\HasFactory (+2 more)

### Community 4 - "Notifikasi"
Cohesion: 0.09
Nodes (8): ManajemenKoreksiNilai, ManajemenKoreksiNilai, NotificationDropdown, NotificationsList, Notifikasi, PengajuanKoreksiNilai, AuditLogger, bootAuditable()

### Community 5 - "1. Super Admin"
Cohesion: 0.06
Nodes (32): 1.10 Manajemen Pengaturan Sistem & TTD, 1.1 Dashboard Super Admin, 1.2 Manajemen User, 1.3 Manajemen Siswa, 1.4 Manajemen Guru, 1.5 Manajemen Kelas, 1.6 Manajemen Jadwal, 1.7 Manajemen Mata Pelajaran (+24 more)

### Community 6 - "ProductionDataSeeder.php"
Cohesion: 0.15
Nodes (4): GenerateMonthlySpp, GenerateMonthlySppCommand, Illuminate\Console\Command, Illuminate\Database\Eloquent\SoftDeletes

### Community 7 - "Illuminate\Database\Seeder"
Cohesion: 0.11
Nodes (8): DatabaseSeeder, JenisTagihanSeeder, KategoriPengeluaranSeeder, KomponenNilaiSeeder, PengaturanSeeder, ProductionAccountsSeeder, RoleSeeder, Illuminate\Database\Seeder

### Community 8 - "scripts"
Cohesion: 0.13
Nodes (15): scripts, post-autoload-dump, post-create-project-cmd, post-update-cmd, pre-package-uninstall, test, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump, Illuminate\\Foundation\\ComposerScripts::prePackageUninstall (+7 more)

### Community 9 - "Siswa"
Cohesion: 0.09
Nodes (4): ManajemenSiswa, DataAlumni, Siswa, UserSeeder

### Community 10 - "5. Finance / Keuangan"
Cohesion: 0.06
Nodes (32): 4.1 Dashboard Murid, 4.2 Jadwal Pelajaran Saya, 4.3 Kehadiran Saya, 4.4 Rapor & Nilai, 4.5 Tagihan SPP & Keuangan, 4.6 Ekstrakurikuler Saya, 4.7 Riwayat Aktivitas, 4. Murid / Portal Siswa (+24 more)

### Community 11 - "InputAbsensiKaryawan"
Cohesion: 0.12
Nodes (5): AbsensiDiri, RekapAbsensiGuru, InputAbsensiKaryawan, AbsensiGuru, Livewire\WithFileUploads

### Community 12 - "Kelas"
Cohesion: 0.10
Nodes (3): PlottingSiswaKelas, Kelas, DemoDataSeeder

### Community 13 - "4. Model Data (Entitas & Field)"
Cohesion: 0.06
Nodes (30): 10. Rekomendasi Tahapan Pengembangan (Roadmap), 1. Ringkasan Sistem Sumber, 2. Peran Pengguna (Aktor) yang Disarankan, 3. Alur Kerja End-to-End, 4.10 `ekstrakurikuler` (dari sheet **EKSKUL**), 4.11 `kehadiran` & `catatan_wali_kelas`, 4.12 `leger` (dari sheet **LEGER**) — VIEW, bukan tabel fisik, 4.13 Output Cetak Rapor (dari sheet **SAMPUL RAPOR**, **ISI SEMESTER 1/2**, **RAPOR INKUL**) (+22 more)

### Community 16 - "Pengaturan"
Cohesion: 0.10
Nodes (7): VerifikasiDokumenController, ManajemenPengaturan, ManajemenPiketGuru, JadwalPiketGuru, Pengaturan, ESignatureService, ESignatureTest

### Community 17 - "Semester"
Cohesion: 0.11
Nodes (5): InputNilaiTahfidz, SetoranTahfidz, NilaiTahfidz, Semester, TahfidzMutabaahSeeder

### Community 18 - "ManajemenGajiGuru"
Cohesion: 0.08
Nodes (6): FinanceReportController, ManajemenGajiGuru, ManajemenPeminjaman, GajiGuru, Peminjaman, NotificationService

### Community 19 - "devDependencies"
Cohesion: 0.11
Nodes (17): concurrently, laravel-vite-plugin, devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite (+9 more)

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

### Community 29 - "Prompt: Review Logika Bisnis — Sistem Informasi Akademik (Kurikulum Merdeka & Tahfizh) & Keuangan Yayasan"
Cohesion: 0.15
Nodes (12): 1. Konsistensi Penilaian Kurikulum Merdeka & Auto-Narasi, 2. Isosiasi & Integrasi Model Tahfizh vs Rombel Umum, 3. Keamanan & Integritas QR Code Keabsahan Dokumen, 4. Trace End-to-End Alur Kritis, 5. Edge Cases & Penanganan Transisi State, AREA AUDIT & TUGAS REVIEW, BATASAN REVIEW, CHECKLIST TITIK RAWAN KHUSUS (Wajib Diverifikasi Statusnya) (+4 more)

### Community 30 - "TahunAjaran"
Cohesion: 0.19
Nodes (3): TahunAjaran, Command, KalenderAkademikTest

### Community 32 - "Pembayaran"
Cohesion: 0.12
Nodes (5): Dashboard, LaporanPemasukan, JenisTagihan, Pembayaran, FinanceSeeder

### Community 34 - "require"
Cohesion: 0.15
Nodes (13): require, barryvdh/laravel-dompdf, blade-ui-kit/blade-icons, chillerlan/php-qrcode, laravel/framework, laravel/octane, laravel/tinker, livewire/livewire (+5 more)

### Community 36 - "Tagihan"
Cohesion: 0.14
Nodes (3): LaporanTunggakan, Tagihan, AutomatedSppGenerationTest

### Community 37 - "Standar Desain UI Komponen (Buttons, Cards, Modals & Alerts) — SIAKAD"
Cohesion: 0.18
Nodes (10): 1. Standar Desain Kartu (Cards), 2. Standar Desain Tombol (Buttons), 3. Integrasi MicroModal.js untuk Alert & Konfirmasi Dialog, 4. Standar Warna Status (Status Badges), **A. Primary Content Card**, **A. Struktur HTML MicroModal (`resources/views/components/layouts/app.blade.php`)**, **B. Cara Penggunaan di JavaScript / Alpine.js**, **B. Hero / Header Banner Card** (+2 more)

### Community 38 - "Role"
Cohesion: 0.17
Nodes (5): ManajemenUser, Role, GuruStudentClassDisplayTest, KenaikanKelasExportTest, createUserWithRole()

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
Cohesion: 0.08
Nodes (6): InputNilaiSiswa, Dashboard, RaporNilai, ManajemenKomponenNilai, KomponenNilai, Nilai

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

### Community 60 - "overview-pembayaran.blade.php"
Cohesion: 0.40
Nodes (4): closeDetails, kirimReminder({{ $item[, viewDetails({{ $item[, voidPayment({{ $p->id }})

### Community 61 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 62 - "manajemen-kalender-akademik.blade.php"
Cohesion: 0.18
Nodes (10): delete({{ $event->id }}), deleteTahunAjaran({{ $ta->id }}), openCreateModal, openEditModal({{ $event->id }}), openEditSemester({{ $s->id }}), openTahunAjaranModal, closeModal, $set( (+2 more)

### Community 63 - "manajemen-mapel.blade.php"
Cohesion: 0.40
Nodes (4): delete({{ $mapel->id }}), openEdit({{ $mapel->id }}), openCreate, $set(

### Community 64 - "Pengeluaran"
Cohesion: 0.13
Nodes (4): ArusKasKeluar, LaporanPengeluaran, KategoriPengeluaran, Pengeluaran

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

### Community 74 - "User"
Cohesion: 0.09
Nodes (8): Login, User, Illuminate\Foundation\Auth\User, Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, GuruDashboardTest, TahfidzParentFeedbackTest, TestCase

### Community 108 - "proses-kenaikan-kelas.blade.php"
Cohesion: 0.50
Nodes (3): prosesKenaikan, $set(, toggleTinggalKelas({{ $siswa->id }})

### Community 204 - "GuruMapelKelas"
Cohesion: 0.17
Nodes (3): Dashboard, GuruMapelKelas, JadwalService

### Community 206 - ".run"
Cohesion: 0.15
Nodes (5): Ekstrakurikuler, SiswaEkstrakurikuler, SiswaKelas, ProductionDataSeeder, RaporOrangTuaSeeder

### Community 208 - "Livewire\WithPagination"
Cohesion: 0.14
Nodes (3): ArusMasuk, DanaBos, Livewire\WithPagination

### Community 211 - "manajemen-remedial.blade.php"
Cohesion: 0.33
Nodes (5): delete({{ $item->id }}), openCreate, openEdit({{ $item->id }}), $set(, updateStatus({{ $item->id }}, 

### Community 212 - "Rapor"
Cohesion: 0.08
Nodes (12): Controller, DocumentVerificationController, FinanceExportController, RaporPdfController, RoleMiddleware, KelolaRapor, Rapor, RaporDetail (+4 more)

### Community 218 - "tabungan-siswa.blade.php"
Cohesion: 0.50
Nodes (3): closeModals, openHistoryModal({{ $siswa->id }}), openTransactionModal({{ $siswa->id }}, 

### Community 223 - "extra"
Cohesion: 0.67
Nodes (3): extra, laravel, dont-discover

### Community 224 - "capaian-pengembangan-diri.blade.php"
Cohesion: 0.40
Nodes (4): closeModal, delete({{ $item->id }}), openCreate, openEdit({{ $item->id }})

### Community 225 - "capaian-pengembangan-guru.blade.php"
Cohesion: 0.50
Nodes (3): openEvaluateModal({{ $item->id }}), closeModal, delete({{ $item->id }})

### Community 226 - "dev"
Cohesion: 0.67
Nodes (3): dev, Composer\\Config::disableProcessTimeout, npx concurrently -c \"#93c5fd,#c4b5fd,#fb7185,#fdba74\" \"php artisan serve\" \"php artisan queue:listen --tries=1 --timeout=0\" \"php artisan pail --timeout=0\" \"npm run dev\" --names=server,queue,logs,vite --kill-others

### Community 230 - "system-error-log.blade.php"
Cohesion: 0.50
Nodes (3): clearLog, closeErrorDetail, openErrorDetail({{ $log[

## Knowledge Gaps
- **371 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+366 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **65 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Illuminate\Database\Eloquent\Model`, `Notifikasi`, `ProductionDataSeeder.php`, `Illuminate\Database\Seeder`, `Siswa`, `InputAbsensiKaryawan`, `Kelas`, `Pengaturan`, `Semester`, `Siswa.php`, `TahunAjaran`, `Pembayaran`, `Tagihan`, `Role`, `Livewire\Component`, `RevisiBusinessLogicTest.php`, `UserFactory.php`, `ManajemenGuru`, `Guru`, `.run`, `ManajemenKaryawan`, `Livewire\WithPagination`?**
  _High betweenness centrality (0.037) - this node is a cross-community bridge._
- **Why does `Siswa` connect `Siswa` to `MataPelajaran`, `Illuminate\Database\Eloquent\Model`, `ProductionDataSeeder.php`, `Kelas`, `InputPembayaran`, `ManajemenSurat`, `Semester`, `ManajemenTagihan`, `AbsensiSiswa`, `Siswa.php`, `NilaiP5`, `TabunganSiswa`, `TahunAjaran`, `Pembayaran`, `ProsesKenaikanKelas`, `Tagihan`, `Role`, `OverviewPembayaran`, `ManajemenRemedial`, `Nilai`, `RekapAbsensiSiswa`, `.run`, `Dashboard`, `Rapor`, `.getMatrixData`, `.mount`?**
  _High betweenness centrality (0.030) - this node is a cross-community bridge._
- **Why does `PengajuanDana` connect `PengajuanDana` to `Pengeluaran`, `Pembayaran`, `Illuminate\Database\Eloquent\Model`, `ProductionDataSeeder.php`, `.run`?**
  _High betweenness centrality (0.017) - this node is a cross-community bridge._
- **Are the 70 inferred relationships involving `Siswa` (e.g. with `.handle()` and `.handle()`) actually correct?**
  _`Siswa` has 70 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _371 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `MataPelajaran` be split into smaller, more focused modules?**
  _Cohesion score 0.06604324956165984 - nodes in this community are weakly interconnected._
- **Should `Illuminate\Database\Eloquent\Model` be split into smaller, more focused modules?**
  _Cohesion score 0.10695187165775401 - nodes in this community are weakly interconnected._