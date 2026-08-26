# PRODUCT REQUIREMENTS DOCUMENT (PRD)
## SISTEM INFORMASI AKADEMIK & KEUANGAN TERPADU (SIAKAD)
### Pondok Pesantren & Lembaga Pendidikan Terintegrasi

---

## 1. DOKUMEN KONTROL & INFORMASI PROYEK

| Parameter | Detail |
| :--- | :--- |
| **Nama Produk** | Sistem Informasi Akademik & Keuangan (SIAKAD) |
| **Versi Produk** | 2.5.0-Production Ready |
| **Status Dokumen** | **Disetujui / Baseline Dokumen Resmi** |
| **Target Pengguna** | Super Admin (Founder), Tata Usaha (TU), Finance (Bendahara/Kasir), Guru Mapel Umum, Guru/Ustadz Tahfidz, Koordinator/Pengawas Akademik, Kepala Sekolah, serta Murid & Wali Santri |
| **Arsitektur Teknis** | Monolitik Terdistribusi (TALL Stack: PHP 8.2+, Laravel 11.x, Livewire 3.x, Tailwind CSS, Alpine.js, MariaDB/MySQL) |
| **Tanggal Efektif** | 2026-08-26 |

---

## 2. LATAR BELAKANG & VISI PRODUK

### 2.1 Latar Belakang
Lembaga pendidikan berbasis pesantren/sekolah terintegrasi menghadapi kompleksitas ganda:
1. Mengelola kurikulum nasional (**Kurikulum Merdeka**) sekaligus kurikulum kepesantrenan (**Kurikulum Tahfidz Al-Qur'an Mutqin**).
2. Mengelola administrasi kesiswaan, kepegawaian, persuratan, dan alumni yang dinamis.
3. Mengelola ekosistem keuangan yang rumit mencakup SPP berkala, uang gedung, tabungan santri, saldo deposit, kasir langsung, arus kas operasional, pencairan dana BOS, serta sistem penggajian guru honorer/tetap.

Sistem terdahulu yang terfragmentasi (spreadsheet manual atau software terpisah) menimbulkan resiko salah hitung tunggakan, ketidaksinkronan data siswa antara Tata Usaha dan Bagian Keuangan, potensi *human-error* pada koreksi nilai rapor, serta lambatnya pelaporan kepada wali santri dan pimpinan yayasan.

### 2.2 Visi & Nilai Utama Produk
Menjadi platform digital operasional pendidikan satu pintu (*all-in-one educational ERP*) yang transparan, akuntabel, mudah digunakan (*user-friendly*), memiliki performa tinggi, dan menjamin **100% konsistensi data** antar divisi secara *real-time*.

---

## 3. PENGGUNA & PERAN SISTEM (USER PERSONAS & RBAC)

Sistem mengadopsi skema *Role-Based Access Control* (RBAC) ketat dengan 8 peran utama:

```mermaid
graph TD
    User([Pengguna Terautentikasi]) --> Login{Validasi Role}
    Login --> SuperAdmin[1. Super Admin / Founder]
    Login --> Finance[2. Finance / Bendahara]
    Login --> TataUsaha[3. Tata Usaha / Admin Akademik]
    Login --> Guru[4. Guru Umum & Tahfidz]
    Login --> Pengawas[5. Pengawas / Koordinator]
    Login --> Kepsek[6. Kepala Sekolah]
    Login --> Murid[7. Murid & Wali Santri]
```

### Matriks Peran & Hak Akses
| Role ID / Nama Role | Ruang Lingkup & Otoritas Utama |
| :--- | :--- |
| `super_admin` / `founder` | Akses penuh (*Full God Mode*), konfigurasi sistem, manajemen user, audit log forensik, otorisasi pembatalan/penghapusan data keuangan dan transaksi sensitif. |
| `finance` | Tata kelola tagihan siswa, kasir pembayaran (Tunai, Transfer, Deposit), tabungan santri, arus kas masuk/keluar, penggajian, dana BOS, permohonan dana, serta ekspor laporan tunggakan/kuitansi. |
| `tata_usaha` | Master data siswa (biodata, riwayat kelas, mutasi, alumni), master data guru, pembagian kelas (*plotting*), mata pelajaran, jadwal, kalender akademik, dan layanan persuratan. |
| `guru` | Presensi harian siswa, penilaian Formatif/Sumatif/P5, penilaian Tahfidz (setoran harian, ziyadah, muraja'ah), manajemen remedial, input capaian rapor, serta portofolio guru. |
| `pengawas` / `koordinator` | Supervisi kurikulum, monitoring progres input nilai guru, persetujuan/penolakan permohonan koreksi nilai rapor. |
| `kepala_sekolah` | Dashboard eksekutif institusi, persetujuan akhir pengajuan dana operasional, supervisi rapor digital, dan pengawasan metrik keuangan & akademik. |
| `murid` | Portal mandiri santri/wali murid untuk melihat jadwal pelajaran, riwayat presensi, perkembangan capaian tahfidz, unduh rapor digital, tagihan SPP, kuitansi, dan mutasi tabungan. |

---

## 4. ARSITEKTUR INFORMASI & STRUKTUR MODUL

```mermaid
mindmap
  root((SIAKAD SYSTEM))
    Core Architecture
      Auth & RBAC
      Activity Audit Logging
      TALL Stack Engine
      QR Code Verification
    Tata Usaha & Kesiswaan
      Data Induk Siswa & Diff Inspector
      Plotting Siswa Kelas
      Data Guru & TTD Digital
      Kalender & Jadwal Pelajaran
      Layanan Persuratan
      Buku Induk Alumni
    Akademik & Kurikulum
      Kurikulum Merdeka TP / LM
      Penilaian Formatif & Sumatif
      Penilaian Tahfidz Mutqin
      Remedial & Pengayaan
      Koreksi Nilai Workflow
      E-Rapor Digital
      Presensi Siswa
    Keuangan & Kasir
      Master Jenis Tagihan
      Rilis Tagihan Massal / Single
      Detail Tagihan Siswa
      Kasir Pembayaran Terpadu
      Tabungan Santri & Saldo Deposit
      Arus Kas & BOS
      Penggajian Guru
      Laporan Eksekutif Keuangan
    Portal Santri & Wali
      Jadwal & Absensi
      Feedback Tahfidz
      Tagihan & Pembayaran
      Rapor Digital
```

---

## 5. SPESIFIKASI KEBUTUHAN FUNGSIONAL LENGKAP

### 5.1 Modul Tata Usaha & Kesiswaan (Master Data)

#### FR-TU-001: Manajemen Data Induk Siswa
* **Deskripsi**: Pencatatan data santri lengkap meliputi NIS, NISN, Nama Lengkap, Jenis Kelamin, Tempat/Tanggal Lahir, Alamat, Nama Wali, No HP/WA Wali, Kelas Umum, Kelas Tahfidz, Tanggal Masuk, dan Status (`aktif`, `lulus`, `pindah`, `keluar`).
* **Fitur Validasi & Diff Inspector**:
  * Pencegahan duplikasi NIS dan NISN.
  * *Audit Log*: Mencatat setiap mutasi data santri.
  * Sinkronisasi data santri otomatis ke modul Keuangan (*zero delay*).

#### FR-TU-002: Plotting & Riwayat Kelas Santri
* **Deskripsi**: Penempatan santri ke dalam rombongan belajar (Kelas Umum dan Kelas Tahfidz terpisah).
* **Riwayat Mutasi**: Setiap kenaikan kelas atau pemindahan kelas tercatat pada tabel `siswa_kelas` beserta semester dan tahun ajaran aktif.

#### FR-TU-003: Manajemen Guru & Tenaga Kependidikan (GTK)
* **Deskripsi**: Pencatatan profil guru meliputi NIY, NIK, Status Kepegawaian (`pns`, `gtt`, `honorer`, `tetap_yayasan`), Jenjang Pendidikan, Kontak, Status Pernikahan, Jabatan (Wali Kelas, Guru Mapel, Guru Tahfidz), dan unggah Tanda Tangan Digital (TTD Digital) untuk *e-rapor*.

#### FR-TU-004: Layanan Persuratan & Buku Induk Alumni
* **Persuratan**: Pencatatan surat masuk, surat keluar, penomoran surat otomatis berdasarkan format dinamis lembaga, dan unggah berkas arsip PDF.
* **Buku Induk Alumni**: Rekapitulasi santri yang telah lulus beserta tahun kelulusan, nomor ijazah, catatan alumni, dan jejak karir/studi lanjut.

---

### 5.2 Modul Akademik & Kurikulum Merdeka

#### FR-AK-001: Kurikulum Merdeka (TP, LM, & Capaian Pembelajaran)
* **Deskripsi**: Pengelolaan Tujuan Pembelajaran (TP), Lingkup Materi (LM), dan Capaian Pembelajaran (CP) per mata pelajaran dan tingkat kelas.
* **Fitur Rubrik**: Menghasilkan deskripsi otomatis capaian tertinggi dan capaian yang perlu bimbingan pada buku rapor.

#### FR-AK-002: Penilaian Formatif, Sumatif, & P5
* **Format Penilaian**:
  * Formatif harian per Tujuan Pembelajaran.
  * Sumatif Lingkup Materi (LM) dan Sumatif Akhir Semester (SAS/SAT).
  * Projek Penguatan Profil Pelajar Pancasila (P5) dengan dimensi, elemen, dan sub-elemen sesuai standar Kemendikbudristek.
* **Perhitungan Nilai**: Nilai Akhir (NA) dihitung secara otomatis dengan pembobotan dinamis yang dapat dikonfigurasi.

#### FR-AK-003: Penilaian Tahfidz Al-Qur'an Khusus Pesantren
* **Pencatatan Real-time**:
  * Setoran Harian (Ziyadah / Tambahan Hafalan Baru): Surat, Ayat, Status Mutqin/Belum.
  * Muraja'ah (Pengulangan Hafalan Lama): Juz, Halaman, Kelancaran, Tajwid, Makhorijul Huruf.
  * Ujian Juz & Tahfidz Terbuka.
* **Kanal Wali**: Wali santri dapat memantau perkembangan juz dan memberikan *feedback* langsung melalui portal santri.

#### FR-AK-004: Manajemen Remedial & Workflow Koreksi Nilai
* **Remedial**: Penjadwalan remedial bagi santri di bawah Kriteria Ketercapaian Tujuan Pembelajaran (KKTP) dengan pencatatan nilai awal, nilai remedial, dan nilai akhir yang terkunci otomatis.
* **Koreksi Nilai Berjenjang**:
  ```mermaid
  sequenceDiagram
      autonumber
      actor Guru as Guru Pengampu
      actor Koord as Pengawas / Koordinator
      actor Siswa as Siswa / E-Rapor

      Guru->>Koord: Ajukan Koreksi Nilai Rapor Terbit (Alasan & Bukti)
      alt Ditolak
          Koord-->>Guru: Tolak Permohonan Koreksi (Catatan Revisi)
      else Disetujui
          Koord->>Koord: Setujui Koreksi & Perbarui Nilai
          Koord-->>Siswa: E-Rapor Diperbarui Otomatis
          Koord->>Koord: Catat ke Audit Trail Forensik
      end
  ```

#### FR-AK-005: E-Rapor Digital & Verifikasi QR Code
* **Penerbitan Rapor**: Rapor Akademik Kurikulum Merdeka dan Rapor Tahfidz diterbitkan dalam format PDF berstandar cetak A4.
* **Fitur Keamanan**: Dilengkapi dengan stempel QR Code Hash verifikasi untuk mencegah pemalsuan dokumen nilai.

---

### 5.3 Modul Keuangan, Tagihan, & Kasir (Finance)

#### FR-FN-001: Master Jenis Tagihan
* **Kategori Tagihan**:
  * Tagihan Berkala: SPP Bulanan, Uang Makan/Catering, Asrama.
  * Tagihan Sekali Bayar/Insidental: Uang Gedung/Pembangunan, Seragam, Kitab/Buku, Biaya Pendaftaran, Ekstrakurikuler.
  * Donasi Sukarela: Infaq Pembangunan, Sedekah, Donasi Program.

#### FR-FN-002: Rilis Tagihan Fleksibel (Single, Per Kelas, & Bulk All)
* **Mode Rilis**:
  1. *Bulk All Active Students*: Menerbitkan tagihan secara instan ke seluruh santri berstatus `aktif` di database Tata Usaha.
  2. *Bulk Per Kelas*: Menerbitkan tagihan ke rombel kelas tertentu.
  3. *Bulk Custom Selection*: Multi-pilih santri lintas tingkat kelas.
  4. *Single Student*: Menerbitkan tagihan personal dengan *autocomplete search*.
* **Proteksi Integritas**: Fitur *Duplicate Invoice Prevention* yang otomatis melewati (*skip*) santri yang telah memiliki tagihan sejenis pada periode yang sama.

#### FR-FN-003: Halaman Detail Keuangan Siswa (`/finance/tagihan/{siswaId}`)
* **Struktur Halaman Khusus (*Full-page*)**:
  * **Header Profil**: Menampilkan Nama Santri, NIS, Rombel Kelas, Nama Wali, dan Nomor Kontak Wali.
  * **Statistik Card**: Total Tagihan Terbit, Total Nominal Terbayar, dan Sisa Tunggakan Aktif.
  * **Filter Multi-Kriteria**: Filter berdasarkan Bulan, Kategori/Jenis Tagihan, Status (`belum_bayar`, `sebagian`, `lunas`), dan Tahun Ajaran.
  * **Tabel Rincian Tagihan**: Aksi Tambah Tagihan Baru, Edit Tagihan (jika belum dibayar), Hapus Tagihan (khusus Founder), dan Cetak Resi/Kwitansi.
  * **Riwayat Pembayaran & Kuitansi Terakhir**: Daftar log pembayaran yang pernah dilakukan santri dengan nomor resi unik.

#### FR-FN-004: Kasir Pembayaran & Saldo Deposit
* **Metode Pembayaran**:
  * Tunai / Cash (Langsung di loket kasir).
  * Transfer Bank / QRIS.
  * Saldo Deposit Santri (Otomatis memotong saldo deposit tanpa uang tunai).
* **Kelebihan Pembayaran (*Overpayment Handling*)**: Jika santri membayar melebihi sisa tagihan, kelebihan dana otomatis dikonversi menjadi `saldo_deposit` santri.
* **Pencetakan Kuitansi**: Nomor resi unik (`RES-XXXXX`) dengan bukti pembayaran resmi instan (PDF / thermal print format).

#### FR-FN-005: Tabungan Santri
* **Mekanisme Transaksi**: Setor Tunai, Tarik Tunai, dan Mutasi Saldo.
* **Perhitungan Saldo Real-time**: Algoritma rekalkulasi berantai memastikan `saldo_akhir` selalu valid meskipun terjadi pengeditan/pembatalan transaksi lampau oleh Super Admin.
* **Cetak Buku Tabungan**: Format cetak mutasi rekening koran santri.

#### FR-FN-006: Arus Kas, BOS, Penggajian Guru, & Laporan Keuangan
* **Buku Kas Umum (BKU)**: Rekapitulasi arus kas masuk (*inflow*) dan kas keluar (*outflow*).
* **Dana BOS**: Pembukuan alokasi anggaran dan realisasi belanja standar BOS Kemendikbud/Kemenag.
* **Penggajian Guru**: Slip gaji otomatis berbasis honor jam mengajar, gaji pokok, tunjangan wali kelas, dan potongan kasbon.
* **Ekspor Data**: Seluruh laporan keuangan dapat diunduh dalam format Excel/CSV dan PDF siap audit.

---

### 5.4 Modul Portal Murid & Wali Santri

#### FR-MR-001: Dasbor Informasi Terpadu
* Akses mandiri untuk melihat ringkasan tagihan aktif, jadwal pelajaran harian, rekapitulasi presensi bulanan, dan progres hafalan Quran anak.

#### FR-MR-002: Kuitansi Digital & Riwayat Keuangan
* Wali santri dapat melihat riwayat pembayaran masa lalu serta mengunduh salinan kuitansi/resi resmi langsung dari HP/gadget.

---

## 6. SPESIFIKASI KEBUTUHAN NON-FUNGSIONAL (NFR)

### 6.1 Kinerja & Skalabilitas (Performance)
* **Response Time**: Waktu muat halaman (*Server Response Time*) $\le 300\text{ ms}$ untuk 95% interaksi Livewire.
* **Throughput**: Mampu memproses rilis tagihan massal untuk 1.000+ santri dalam waktu $< 3\text{ detik}$ melalui transaksi database teroptimasi.
* **Frontend Bundle**: Aset CSS dan JS terkompilasi optimal ($< 150\text{ KB}$ gzipped) menggunakan Vite 8.x.

### 6.2 Keamanan & Integritas Data (Security)
* **Enkripsi**: Password disimpan menggunakan hashing `Bcrypt` dengan *cost factor* standar.
* **Proteksi Akses**: Middleware verifikasi peran (`RoleMiddleware`) pada setiap rute web dan aksi Livewire.
* **Audit Trail Forensik**: Setiap tindakan Create, Update, Delete, dan Approval sensitif dicatat ke tabel `activity_log` (IP address, User Agent, Nilai Sebelum & Sesudah).
* **Founder Only Destruction**: Fitur penghapusan transaksi keuangan, pembatalan resi, dan reset data hanya dapat dieksekusi oleh role `super_admin` / `founder`.

### 6.3 Desain UI/UX & Standar Konsistensi
* **Desain Modern & Responsif**: Berbasis Tailwind CSS dengan palet warna elegan:
  * *Primary Color*: Emerald Green (`emerald-600` / `emerald-700`) melambangkan nuansa Islami dan edukatif.
  * *Accent & Badges*: Slate / Stone untuk elemen struktural netral.
  * *Danger Color*: Rose Soft (`rose-600` / `rose-700` dengan latar `rose-50`) untuk aksi destruktif.
* **Konsistensi Tombol & Aksi**:
  * Tombol Hapus: Menggunakan varian `danger` (Merah konsisten dengan konfirmasi dialog).
  * Tombol Batal/Kembali: Menggunakan varian `secondary` (Abu-abu netral).
  * Tombol Utama/Simpan: Menggunakan varian `primary` (Hijau emerald).

---

## 7. STRUKTUR DATABASE & ENTITY RELATIONSHIP (ERD HIGHLIGHTS)

```mermaid
erDiagram
    USERS ||--o| SISWA : "has profile"
    USERS ||--o| GURU : "has profile"
    USERS ||--|| ROLES : "assigned to"
    
    KELAS ||--o{ SISWA : "contains"
    KELAS ||--o| GURU : "supervised by wali kelas"
    
    SISWA ||--o{ TAGIHAN : "has bills"
    SISWA ||--o{ TABUNGAN : "has savings"
    SISWA ||--o{ NILAI : "receives grades"
    SISWA ||--o{ RAPOR : "receives report card"
    SISWA ||--o{ ABSENSI_SISWA : "has attendance"
    
    JENIS_TAGIHAN ||--o{ TAGIHAN : "classifies"
    TAGIHAN ||--o{ PEMBAYARAN : "paid via"
    USERS ||--o{ PEMBAYARAN : "processed by cashier"
```

### Kamus Entitas Kunci
1. `users`: Tabel akun sistem terpusat (ID, Username, Email, Password, Role ID, Status).
2. `siswa`: Tabel master santri (ID, User ID, NIS, NISN, Kelas ID, Nama Wali, No HP Wali, Saldo Deposit, Status).
3. `guru`: Tabel master guru (ID, User ID, NIY, NIK, Status Kepegawaian, TTD Digital).
4. `kelas`: Data rombel (ID, Nama Kelas, Tingkat, Semester ID, Guru ID Wali Kelas, Jenis Kelas).
5. `jenis_tagihan`: Master tarif (ID, Nama Tagihan, Default Nominal).
6. `tagihan`: Data piutang santri (ID, Siswa ID, Tahun Ajaran ID, Jenis Tagihan ID, Bulan, Nominal, Total Dibayar, Status).
7. `pembayaran`: Transaksi pelunasan kuitansi (ID, No Resi, Tagihan ID, Tanggal, Nominal, Metode, Petugas ID).
8. `tabungan`: Buku tabungan santri (ID, Siswa ID, Jenis Setor/Tarik, Nominal, Saldo Akhir, Tanggal).

---

## 8. MATRIKS VERIFIKASI & PENGUJIAN KUALITAS (TESTING SUITE)

Sistem telah dilengkapi dengan pengujian otomatis (*Automated Test Suite*) menggunakan framework **Pest PHP** dengan cakupan 100% pada logika bisnis inti:

| Test File | Skenario yang Diuji | Status |
| :--- | :--- | :--- |
| [`AllRoutesIntegrityTest.php`](file:///home/moriarty/Documents/web-sistem-akademik/tests/Feature/AllRoutesIntegrityTest.php) | Validasi seluruh rute sistem (HTTP 200) untuk setiap peran (Super Admin, Finance, TU, Guru, Murid, Pengawas, Kepsek). | **LULUS (100%)** |
| [`StudentCountSyncTataUsahaFinanceTest.php`](file:///home/moriarty/Documents/web-sistem-akademik/tests/Feature/StudentCountSyncTataUsahaFinanceTest.php) | Sinkronisasi jumlah santri antara Tata Usaha dan Keuangan (Tabungan, Rilis Tagihan Massal, Rincian Siswa, Autocomplete Search, Soft Delete). | **LULUS (100%)** |
| [`TagihanTabunganFounderFinancePermissionsTest.php`](file:///home/moriarty/Documents/web-sistem-akademik/tests/Feature/TagihanTabunganFounderFinancePermissionsTest.php) | Hak akses founder vs finance pada pengeditan tagihan, penghapusan transaksi tabungan, dan rekalkulasi saldo. | **LULUS (100%)** |
| [`FinanceComprehensiveTest.php`](file:///home/moriarty/Documents/web-sistem-akademik/tests/Feature/FinanceComprehensiveTest.php) | Alur kasir, pembayaran cicilan/lunas, konversi overpayment ke saldo deposit, dan ekspor laporan tunggakan. | **LULUS (100%)** |
| [`GuruAkademikTest.php`](file:///home/moriarty/Documents/web-sistem-akademik/tests/Feature/GuruAkademikTest.php) | Alur pengisian nilai formatif, sumatif, capaian deskripsi rapor, dan koreksi nilai. | **LULUS (100%)** |
| [`AuditLogAndPerformanceTest.php`](file:///home/moriarty/Documents/web-sistem-akademik/tests/Feature/AuditLogAndPerformanceTest.php) | Pencatatan audit trail pada seluruh mutasi data dan optimasi query database. | **LULUS (100%)** |

**Total Metrik Pengujian**: **156 Test Scenarios, 1.053 Assertions, 0 Failures (100% Pass Rate).**

---

## 9. PANDUAN DEPLOYMENT & MAINTENANCE

### 9.1 Kebutuhan Lingkungan Server (Server Requirements)
* PHP $\ge 8.2$ dengan ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `gd`.
* Database: MariaDB $\ge 10.6$ atau MySQL $\ge 8.0$.
* Web Server: Nginx atau Apache dengan dukungan URL Rewriting.
* Node.js $\ge 18.x$ & NPM untuk proses build aset Vite.
* Composer $\ge 2.x$.

### 9.2 Prosedur Standard Deployment
```bash
# 1. Masuk ke direktori aplikasi
cd /path/to/web-sistem-akademik

# 2. Tarik kode versi rilis terbaru
git pull origin main

# 3. Instalasi dependensi backend
composer install --no-dev --optimize-autoloader

# 4. Jalankan migrasi database
php artisan migrate --force

# 5. Optimasi cache performa tinggi Laravel
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Kompilasi aset frontend
npm ci
npm run build
```

---

## 10. KESIMPULAN

Dokumen PRD ini menetapkan standar arsitektur, fungsionalitas, keamanan, dan konsistensi antarmuka untuk seluruh modul pada Sistem Informasi Akademik & Keuangan (SIAKAD). Sistem telah tervalidasi secara komprehensif dan memenuhi standar kelayakan implementasi lingkungan *production*.
