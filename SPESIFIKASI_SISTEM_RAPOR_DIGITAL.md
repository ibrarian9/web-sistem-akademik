# Spesifikasi Sistem: Aplikasi Rapor Digital Kurikulum Merdeka
### Hasil Reverse-Engineering dari `RAPOR_VERSI_NSD_25_10_B_KELAS_6.xlsx`

> Dokumen ini adalah hasil pembongkaran struktur, relasi data, dan logika perhitungan dari file Excel rapor (45 sheet) menjadi spesifikasi yang siap dipakai sebagai acuan pengembangan **sistem akademik berbasis website**. Contoh data siswa pada dokumen ini **disamarkan/disintesis**, bukan data asli anak-anak di file sumber — hanya nama field dan strukturnya yang diambil dari file asli.

---

## 1. Ringkasan Sistem Sumber

File Excel ini adalah **generator rapor Kurikulum Merdeka** untuk jenjang SD, dipakai oleh satu wali kelas untuk satu rombel (Kelas VI, Fase C, ±50 siswa, semester berjalan). Excel ini bekerja seperti aplikasi mini: input nilai per Tujuan Pembelajaran (TP) → dihitung otomatis → narasi deskripsi capaian dibuat otomatis → digabung ke leger → dicetak jadi rapor per siswa (cover + isi + kokurikuler).

Karakteristik penting yang harus dipertahankan saat dipindah ke website:

- **Struktur kurikulum berjenjang**: Mapel → Lingkup Materi (maks. 10) → Tujuan Pembelajaran/TP (maks. 5 per lingkup materi) → Nilai Sumatif per TP per siswa.
- **Auto-narasi**: sistem otomatis mencari skor TP **tertinggi** dan **terendah** milik siswa dalam satu mapel, lalu merangkainya jadi kalimat deskripsi rapor ("Ananda X menunjukkan penguasaan dalam...", "...membutuhkan penguatan dalam...").
- **Nilai akhir mapel** = rata-rata dari rata-rata tiap lingkup materi + nilai Sumatif Akhir Semester (SAS).
- **Kokurikuler (Projek Penguatan Profil Pelajar Pancasila/P5)** dinilai terpisah dari mapel akademik, dengan 7 dimensi × beberapa sub-dimensi, melalui 3 jalur proyek (Lintas Disiplin Ilmu, 7 Kebiasaan Anak Indonesia Hebat, Cara Lain), masing-masing dengan hingga 5 titik sumatif.
- **Leger** adalah rekap induk (denormalized) — satu baris per siswa berisi seluruh nilai mapel, narasi, kehadiran, ekskul, catatan wali kelas, ranking, dan status naik kelas.
- **Output cetak** (Sampul Rapor, Isi Semester 1/2, Rapor Kokurikuler) adalah halaman cetak per siswa yang menarik data dari seluruh sheet di atas — ini setara dengan fitur "generate/export PDF rapor" di website nanti.

---

## 2. Peran Pengguna (Aktor) yang Disarankan

| Peran | Kebutuhan Utama |
|---|---|
| **Admin Sekolah / Operator** | Kelola data sekolah, tahun ajaran, kelas, mapel, kurikulum (TP), input data siswa |
| **Wali Kelas** | Input kehadiran, catatan wali kelas, ekskul, kokurikuler, finalisasi & cetak rapor |
| **Guru Mata Pelajaran** | Input nilai sumatif per TP untuk mapel yang diampu |
| **Kepala Sekolah** | Lihat/approve leger, tanda tangan digital rapor, laporan rekap sekolah |
| **Orang Tua / Siswa (opsional)** | Lihat & unduh rapor anaknya saja (read-only, per akun) |

---

## 3. Alur Kerja End-to-End

```
1. Admin setup: Data Sekolah, Tahun Ajaran/Semester, Kelas, Wali Kelas
2. Admin/Guru setup: Daftar Mata Pelajaran (Intrakurikuler + Ekstrakurikuler)
3. Admin/Guru setup: Kurikulum → Lingkup Materi & Tujuan Pembelajaran per mapel
4. Admin: Input/Import Data Siswa (biodata, ortu/wali, kesehatan)
5. Guru Mapel: Input nilai sumatif per TP per siswa, per mapel  ──┐
6. Guru Mapel: Input nilai SAS per mapel                          ├─▶ Sistem hitung otomatis:
7. Wali Kelas: Input nilai Kokurikuler (P5) per dimensi            │   - rata-rata per lingkup materi
8. Wali Kelas: Input Ekstrakurikuler, Kehadiran, Catatan          │   - nilai akhir rapor per mapel
                                                                   │   - deskripsi capaian tertinggi/terendah
                                                                  ─┘
9. Sistem: Susun Leger (rekap seluruh siswa 1 kelas)
10. Kepala Sekolah/Wali Kelas: Review & Finalisasi
11. Sistem: Generate Rapor (Cover + Isi + Kokurikuler) → PDF per siswa
12. Distribusi: cetak, atau akses online oleh orang tua
```

---

## 4. Model Data (Entitas & Field)

Berikut pemetaan tiap sheet Excel menjadi entitas data. Nama field diterjemahkan dari header asli agar konsisten dipakai sebagai nama kolom database/API.

### 4.1 `sekolah` (dari sheet **SEKOLAH**) — 1 record per sekolah
| Field | Sumber Kolom Excel | Tipe |
|---|---|---|
| nama_sekolah | Nama Sekolah | string |
| npsn, nss | NPSN, NSS | string |
| alamat, kode_pos, desa_kelurahan, kecamatan, kabupaten_kota, provinsi | — | string |
| website, email | — | string |
| nama_kepala_sekolah, nip_kepala_sekolah | — | string |
| nama_wali_kelas, nip_wali_kelas | — | string (idealnya relasi ke tabel `guru`) |
| kelas, fase, semester, tahun_ajaran | Kelas, Fase, Semester, Tahun Ajaran | string |
| tempat_tanggal_rapor | — | string/date |
| jumlah_siswa | — | integer (sebaiknya dihitung otomatis, bukan input manual) |

### 4.2 `siswa` (dari sheet **SISWA**) — biodata murid
Field inti: `no_urut, nis, nisn (unique), nama_lengkap, nama_panggilan, jenis_kelamin (L/P), tempat_lahir, tanggal_lahir, agama, pendidikan_sebelumnya, alamat_siswa`.

Data orang tua/wali (idealnya tabel terpisah `wali_siswa` relasi 1-ke-banyak ke `siswa`): `nama_ayah, pekerjaan_ayah, nama_ibu, pekerjaan_ibu, alamat_orang_tua (jalan/desa/kecamatan/kab-kota/provinsi), nama_wali, pekerjaan_wali, alamat_wali, no_telepon_wali`.

Data kesehatan & fisik: `tinggi_badan, berat_badan, kondisi_pendengaran, kondisi_penglihatan, kondisi_gigi, kondisi_lainnya`.

Data ekstrakurikuler ringkas per siswa: `kegiatan_ekskul_1..N` (skor/keterangan per semester — di website sebaiknya jadi tabel relasi `siswa_ekskul`, bukan kolom berulang).

Data akhir: `naik_tingkal_kelas_romawi, naik_tinggal_kelas_latin` (mis. "IV" / "(Empat)" — kelas tujuan tahun ajaran berikutnya).

> ⚠️ **Catatan privasi**: kolom biodata ortu, alamat, dan kondisi kesehatan adalah data pribadi anak. Saat membangun website, terapkan kontrol akses ketat (RBAC), enkripsi at-rest untuk kolom sensitif, dan audit log akses.

### 4.3 `mata_pelajaran` (dari sheet **MAPEL**)
| Field | Keterangan |
|---|---|
| id_mapel | PK |
| nama_mapel | Pendidikan Agama, Pendidikan Pancasila, Bahasa Indonesia, Matematika, IPAS, Seni Budaya, PJOK, Bahasa Inggris, Budaya Melayu Riau (mapel lokal/muatan lokal), dst. |
| jenis | `intrakurikuler` / `ekstrakurikuler` |
| urutan | untuk urutan tampil di rapor |

Catatan: template mendukung hingga **10 mapel intrakurikuler** (sheet M1–M10, S1–S10) dan daftar ekskul terpisah (Pramuka, Olahraga, Seni, dst).

### 4.4 `lingkup_materi` (dari sheet **TP-MATERI**)
Nama "bab"/ruang lingkup materi per mapel per fase. Maks. 10 lingkup materi per mapel (di data contoh: untuk Pendidikan Agama dipecah lagi menurut agama — Islam materi 1-5, Kristen materi 1-5 — karena satu kelas berisi siswa lintas agama).

| Field | Keterangan |
|---|---|
| id_lingkup_materi | PK |
| id_mapel | FK → mata_pelajaran |
| nama_lingkup_materi | mis. "Materi Islam 1", "Seni Rupa 2" |
| urutan | 1–10 |
| kategori (opsional) | mis. "Islam"/"Kristen" untuk Pendidikan Agama |

### 4.5 `tujuan_pembelajaran` / TP (dari sheet **TP**)
Butir capaian belajar yang dinilai. Maks. 5 TP per lingkup materi (≈50 TP per mapel).

| Field | Keterangan |
|---|---|
| id_tp | PK |
| id_lingkup_materi | FK |
| deskripsi_tp | teks tujuan pembelajaran, mis. "membaca q.s. ad-dhuha", "membaca bilangan cacah dari 100.000–1.000.000" |
| urutan | 1–5 dalam lingkup materinya |

### 4.6 `nilai_sumatif_tp` (dari sheet **S1–S10**) — nilai mentah, tabel fakta utama
| Field | Keterangan |
|---|---|
| id | PK |
| id_siswa | FK |
| id_tp | FK |
| id_semester_ajaran | FK (agar histori antar semester/tahun tidak tertimpa) |
| nilai | angka 0–100 |

Ini pengganti langsung dari 50 kolom nilai per baris siswa di tiap sheet `S{n}` — di database relasional sebaiknya **long format** (1 baris = 1 nilai), bukan wide format seperti Excel, supaya jumlah TP per mapel bisa fleksibel tanpa mendesain ulang skema.

### 4.7 `nilai_akhir_mapel` (dari sheet **M1–M10**, hasil kalkulasi)
| Field | Keterangan | Rumus asal (Excel) |
|---|---|---|
| id_siswa, id_mapel, id_semester_ajaran | FK | — |
| rata_rata_per_lingkup_materi[] | rata-rata nilai TP dalam satu lingkup materi | `AVERAGE(rentang 5 kolom TP)` |
| nilai_sas | nilai Sumatif Akhir Semester (input manual guru) | — |
| nilai_rapor | nilai akhir mapel yang tampil di rapor | `AVERAGE(semua rata-rata lingkup materi, nilai_sas)` |

### 4.8 `template_deskripsi` (dari sheet **DESKRIPSI**)
Kalimat pembuka narasi capaian, berbeda tiap mapel, satu untuk nilai tertinggi & satu untuk nilai terendah.

| id_mapel | frasa_capaian_tertinggi | frasa_capaian_terendah |
|---|---|---|
| Pendidikan Agama | "menunjukkan penguasaan dalam" | "sudah berkembang dalam" |
| Pendidikan Pancasila | "menunjukkan pemahaman dalam" | "membutuhkan bimbingan dalam" |
| Matematika | "menunjukkan penguasaan dalam" | "membutuhkan penguatan dalam" |
| Kokurikuler (P5) | "sudah mahir dalam penerapan subdimensi" | "dan sudah mulai berkembang dalam penerapan subdimensi" |
| *(dst. per mapel — 1 baris per mapel di sheet asal)* | | |

### 4.9 Kokurikuler / P5 (dari sheet **KOKUR**, **LINTAS**, **7KAIH**, **CARA LAIN**)
Struktur penilaian **Projek Penguatan Profil Pelajar Pancasila**, terpisah dari nilai akademik:

- **7 Dimensi**: Keimanan & Ketakwaan kepada Tuhan YME, Kewargaan, Penalaran Kritis, Kreativitas, Kolaborasi, Kemandirian, Kesehatan, Komunikasi (di data ada 8 label — kemungkinan 1 dimensi sudah termasuk beberapa sub yang tumpang tindih; perlu dikonfirmasi ke pedoman P5 terbaru).
- Tiap dimensi punya **2–4 sub-dimensi** (mis. dimensi "Kewargaan" → kewargaan lokal, nasional, global).
- Ada **3 jalur/model proyek** yang identik strukturnya: `lintas_disiplin_ilmu`, `tujuh_kebiasaan_anak_indonesia_hebat`, `cara_lain` — masing-masing dinilai di **5 titik sumatif** (Sumatif 1–5) per sub-dimensi, dengan skala kualitatif (mis. 1–4, bukan 0–100).
- Sheet `KOKUR` adalah rekap gabungan ketiga jalur + auto-narasi capaian tertinggi/terendah (pola formula sama seperti nilai akademik: `LARGE`/`SMALL` + `HLOOKUP` + `CONCATENATE`).

Rancangan tabel:
```
dimensi_p5(id, nama_dimensi, urutan)
subdimensi_p5(id, id_dimensi, nama_subdimensi, urutan)
proyek_p5(id, nama_proyek)  -- 'Lintas Disiplin Ilmu' | '7 Kebiasaan Anak Indonesia Hebat' | 'Cara Lain'
nilai_p5(id, id_siswa, id_proyek, id_subdimensi, titik_sumatif [1-5], nilai, id_semester_ajaran)
```
Nilai akhir & narasi P5 dihitung dengan logika yang sama seperti §5.2 di bawah, tapi lintas-proyek-dan-subdimensi.

### 4.10 `ekstrakurikuler` (dari sheet **EKSKUL**)
| Field | Keterangan |
|---|---|
| id_siswa | FK |
| nama_ekskul | Pramuka, Olahraga, Seni, dll |
| deskripsi_capaian | narasi bebas per siswa per ekskul (diinput guru pembina, bukan hasil kalkulasi) |

### 4.11 `kehadiran` & `catatan_wali_kelas`
Dari kolom di sheet **SISWA**/**LEGER**: `jumlah_sakit, jumlah_izin, jumlah_alfa` per siswa per semester.
Dari sheet **CATATAN**: `catatan_bebas` per siswa (teks manual guru), plus versi auto-narasi `"Ananda {nama} {catatan}."` (dari formula `=$E$5&" "&E10&" "&F10&"."`).

### 4.12 `leger` (dari sheet **LEGER**) — VIEW, bukan tabel fisik
Leger murni **agregasi** dari entitas 4.2–4.11 (terbukti dari formula: `='M1'!P9`, `='S1'!BJ10`, `=SISWA!D10`, dst). Di website, ini sebaiknya diimplementasi sebagai **view/query gabungan** atau endpoint laporan, bukan tabel yang disimpan terpisah — supaya tidak ada duplikasi data & risiko data tidak sinkron.

Kolom leger per siswa mencakup: nilai akhir tiap mapel + narasi tertinggi/rendahnya, nilai tertinggi/terendah per mapel di kelas, nilai ekskul, kehadiran (S/I/A), catatan wali kelas, jumlah nilai, rata-rata, **ranking** (perlu dihitung server-side, bukan manual seperti di Excel), status naik/tinggal kelas.

### 4.13 Output Cetak Rapor (dari sheet **SAMPUL RAPOR**, **ISI SEMESTER 1/2**, **RAPOR INKUL**)
Ini bukan entitas data baru, melainkan **template render/PDF** yang menarik dari seluruh entitas di atas:
- **Sampul Rapor**: identitas sekolah, siswa, foto, tahun ajaran (halaman cover).
- **Isi Semester 1/2**: tabel nilai per mapel + deskripsi, kokurikuler, ekskul, kehadiran, catatan, tanda tangan kepala sekolah/wali kelas/orang tua.
- **Rapor Inkul**: halaman khusus hasil kokurikuler P5 (di file sumber tampak berbasis elemen grafis/text box, bukan sekadar sel, jadi kemungkinan berisi format visual yang lebih bebas — perlu dicek ulang manual di Excel bila detail layout ingin ditiru persis).

Beberapa sheet lain (`INPUT DATA`, `DATA PRIBADI`, `KOKURIKULER`, `INPUT NILAI`, `HOME`) hampir kosong dari sisi data sel — kemungkinan besar berfungsi sebagai **halaman navigasi/menu** (tombol/hyperlink antar sheet) dalam file Excel-nya, setara dengan **halaman dashboard/menu utama** di website nanti.

---

## 5. Logika Bisnis / Kalkulasi (diterjemahkan dari rumus Excel)

### 5.1 Nilai Akhir Mapel
```
rata_rata_lingkup_materi[i] = AVERAGE(nilai TP dalam lingkup_materi[i])   // abaikan lingkup materi kosong
nilai_rapor_mapel = AVERAGE( semua rata_rata_lingkup_materi yang terisi, nilai_sas )
```

### 5.2 Auto-Narasi Deskripsi Capaian (dipakai untuk mapel akademik & P5)
```
tp_tertinggi   = TP dengan nilai TERTINGGI milik siswa ini, dalam mapel ini
tp_terendah    = TP dengan nilai TERENDAH milik siswa ini, dalam mapel ini
                 (Excel pakai LARGE/SMALL + HLOOKUP; di aplikasi web cukup ORDER BY nilai LIMIT 1)

kalimat_atas   = frasa_capaian_tertinggi[mapel] + " " + deskripsi_tp_tertinggi + "."
kalimat_bawah  = frasa_capaian_terendah[mapel] + " " + deskripsi_tp_terendah + "."

narasi_rapor   = "Ananda " + nama_panggilan + " " + kalimat_atas
                 + " Ananda " + nama_panggilan + " " + kalimat_bawah
```
**Catatan penting untuk implementasi web**: jika ada TP yang nilainya sama-sama tertinggi (seri/tie), Excel `LARGE(...,1)` hanya mengambil satu secara implisit berdasarkan urutan kolom. Sebaiknya di web logic ini didefinisikan eksplisit (mis. TP dengan urutan paling awal yang menang) agar hasilnya konsisten dan bisa diaudit.

### 5.3 Ranking Kelas
Tidak ditemukan rumus otomatis murni di leger untuk ranking (kemungkinan manual/sort manual di Excel). Untuk web: `RANK() OVER (PARTITION BY kelas, semester ORDER BY nilai_rata_rata DESC)`.

### 5.4 Validasi yang Perlu Ditambahkan (celah di versi Excel)
- Sheet `LINTAS`/`7KAIH`/`CARA LAIN` menghasilkan banyak `#DIV/0!` ketika sub-dimensi belum diisi nilai — di web, tangani dengan nilai default `NULL`/"belum dinilai", bukan galat pembagian.
- Beberapa kolom bantu (mis. label lingkup materi bernilai `0`) menandakan slot mapel/materi ke-10 belum dipakai — di web sebaiknya jumlah mapel/lingkup materi/TP **dinamis** (tidak dibatasi 10/5 seperti keterbatasan grid Excel).

---

## 6. Rancangan Skema Basis Data (Relasional)

```sql
-- Master
CREATE TABLE sekolah (
  id SERIAL PRIMARY KEY,
  nama_sekolah VARCHAR(150), npsn VARCHAR(20), nss VARCHAR(20),
  alamat TEXT, kode_pos VARCHAR(10), desa_kelurahan VARCHAR(100),
  kecamatan VARCHAR(100), kabupaten_kota VARCHAR(100), provinsi VARCHAR(100),
  website VARCHAR(150), email VARCHAR(150),
  nama_kepala_sekolah VARCHAR(150), nip_kepala_sekolah VARCHAR(30)
);

CREATE TABLE tahun_ajaran (
  id SERIAL PRIMARY KEY,
  sekolah_id INT REFERENCES sekolah(id),
  tahun_ajaran VARCHAR(10),      -- '2025/2026'
  semester VARCHAR(5),           -- 'I' / 'II'
  aktif BOOLEAN DEFAULT true
);

CREATE TABLE kelas (
  id SERIAL PRIMARY KEY,
  sekolah_id INT REFERENCES sekolah(id),
  tahun_ajaran_id INT REFERENCES tahun_ajaran(id),
  nama_kelas VARCHAR(20),        -- 'VI'
  fase VARCHAR(5),               -- 'C'
  wali_kelas_id INT REFERENCES guru(id)
);

CREATE TABLE guru (
  id SERIAL PRIMARY KEY,
  nama VARCHAR(150), nip VARCHAR(30), sekolah_id INT REFERENCES sekolah(id)
);

-- Siswa
CREATE TABLE siswa (
  id SERIAL PRIMARY KEY,
  kelas_id INT REFERENCES kelas(id),
  nis VARCHAR(20), nisn VARCHAR(20) UNIQUE,
  nama_lengkap VARCHAR(150), nama_panggilan VARCHAR(50),
  jenis_kelamin CHAR(1), tempat_lahir VARCHAR(100), tanggal_lahir DATE,
  agama VARCHAR(30), pendidikan_sebelumnya VARCHAR(100), alamat TEXT,
  tinggi_badan NUMERIC, berat_badan NUMERIC,
  kondisi_pendengaran VARCHAR(50), kondisi_penglihatan VARCHAR(50), kondisi_gigi VARCHAR(50)
);

CREATE TABLE wali_siswa (
  id SERIAL PRIMARY KEY,
  siswa_id INT REFERENCES siswa(id),
  tipe VARCHAR(10),              -- 'ayah' | 'ibu' | 'wali'
  nama VARCHAR(150), pekerjaan VARCHAR(100), alamat TEXT, no_telepon VARCHAR(20)
);

-- Kurikulum
CREATE TABLE mata_pelajaran (
  id SERIAL PRIMARY KEY,
  nama_mapel VARCHAR(100), jenis VARCHAR(20), urutan INT
);

CREATE TABLE lingkup_materi (
  id SERIAL PRIMARY KEY,
  mapel_id INT REFERENCES mata_pelajaran(id),
  nama_lingkup_materi VARCHAR(150), kategori VARCHAR(50), urutan INT
);

CREATE TABLE tujuan_pembelajaran (
  id SERIAL PRIMARY KEY,
  lingkup_materi_id INT REFERENCES lingkup_materi(id),
  deskripsi TEXT, urutan INT
);

CREATE TABLE template_deskripsi (
  mapel_id INT REFERENCES mata_pelajaran(id),
  frasa_tertinggi VARCHAR(200), frasa_terendah VARCHAR(200)
);

-- Penilaian akademik
CREATE TABLE nilai_sumatif_tp (
  id SERIAL PRIMARY KEY,
  siswa_id INT REFERENCES siswa(id),
  tp_id INT REFERENCES tujuan_pembelajaran(id),
  tahun_ajaran_id INT REFERENCES tahun_ajaran(id),
  nilai NUMERIC(5,2),
  UNIQUE(siswa_id, tp_id, tahun_ajaran_id)
);

CREATE TABLE nilai_sas (
  siswa_id INT REFERENCES siswa(id),
  mapel_id INT REFERENCES mata_pelajaran(id),
  tahun_ajaran_id INT REFERENCES tahun_ajaran(id),
  nilai NUMERIC(5,2),
  PRIMARY KEY (siswa_id, mapel_id, tahun_ajaran_id)
);
-- nilai_rapor per mapel: dihitung on-the-fly / materialized view dari nilai_sumatif_tp + nilai_sas

-- P5 / Kokurikuler
CREATE TABLE dimensi_p5 (id SERIAL PRIMARY KEY, nama VARCHAR(100), urutan INT);
CREATE TABLE subdimensi_p5 (id SERIAL PRIMARY KEY, dimensi_id INT REFERENCES dimensi_p5(id), nama VARCHAR(150), urutan INT);
CREATE TABLE proyek_p5 (id SERIAL PRIMARY KEY, nama VARCHAR(100));  -- Lintas Disiplin / 7 Kebiasaan / Cara Lain

CREATE TABLE nilai_p5 (
  id SERIAL PRIMARY KEY,
  siswa_id INT REFERENCES siswa(id),
  proyek_id INT REFERENCES proyek_p5(id),
  subdimensi_id INT REFERENCES subdimensi_p5(id),
  titik_sumatif SMALLINT,        -- 1..5
  nilai SMALLINT,                -- skala kualitatif, mis. 1-4
  tahun_ajaran_id INT REFERENCES tahun_ajaran(id)
);

-- Non-akademik
CREATE TABLE ekstrakurikuler_siswa (
  siswa_id INT REFERENCES siswa(id),
  nama_ekskul VARCHAR(100), deskripsi_capaian TEXT,
  tahun_ajaran_id INT REFERENCES tahun_ajaran(id)
);

CREATE TABLE kehadiran (
  siswa_id INT REFERENCES siswa(id),
  tahun_ajaran_id INT REFERENCES tahun_ajaran(id),
  sakit INT DEFAULT 0, izin INT DEFAULT 0, alfa INT DEFAULT 0
);

CREATE TABLE catatan_wali_kelas (
  siswa_id INT REFERENCES siswa(id),
  tahun_ajaran_id INT REFERENCES tahun_ajaran(id),
  catatan TEXT
);

CREATE TABLE status_kenaikan_kelas (
  siswa_id INT REFERENCES siswa(id),
  tahun_ajaran_id INT REFERENCES tahun_ajaran(id),
  status VARCHAR(20),            -- 'naik' | 'tinggal'
  kelas_tujuan VARCHAR(20)
);
```

---

## 7. Rancangan Modul/Fitur Website

| Modul | Fitur | Peran Akses |
|---|---|---|
| **Dashboard** | Ringkasan kelas, status pengisian nilai (progress per mapel/guru) | Semua |
| **Master Data** | CRUD Sekolah, Tahun Ajaran, Kelas, Guru | Admin |
| **Data Siswa** | CRUD biodata, import massal (CSV/Excel), foto siswa | Admin |
| **Kurikulum** | CRUD Mapel → Lingkup Materi → TP; kelola template deskripsi | Admin/Guru |
| **Input Nilai** | Form input nilai per TP (per mapel, per kelas), input SAS | Guru Mapel |
| **Kokurikuler (P5)** | Input nilai per sub-dimensi × proyek × titik sumatif | Wali Kelas |
| **Ekskul, Kehadiran, Catatan** | Input per siswa per semester | Wali Kelas |
| **Leger / Rekap Kelas** | Tabel gabungan seluruh nilai + ranking otomatis, bisa difilter & diekspor | Wali Kelas, Kepsek |
| **Generate Rapor** | Preview & export PDF (cover + isi + kokurikuler) per siswa / massal | Wali Kelas, Kepsek |
| **Portal Orang Tua (opsional)** | Login khusus, lihat/unduh rapor anak sendiri | Orang tua |
| **Log Aktivitas** | Audit siapa mengubah nilai apa, kapan (penting untuk integritas nilai) | Admin |

---

## 8. Pemetaan Migrasi: Sheet Excel → Tabel Sistem Baru

| Sheet Excel | Tujuan di Sistem Baru |
|---|---|
| SEKOLAH | `sekolah`, `tahun_ajaran`, `kelas` |
| SISWA | `siswa`, `wali_siswa`, `kehadiran` (kolom S/I/A), `status_kenaikan_kelas` |
| MAPEL | `mata_pelajaran` |
| TP-MATERI | `lingkup_materi` |
| TP | `tujuan_pembelajaran` |
| S1–S10 | `nilai_sumatif_tp` (nilai mentah), narasi dihitung ulang via §5.2 (jangan disalin sebagai teks statis) |
| M1–M10 | Hasil kalkulasi `nilai_rapor` per mapel (materialized view, bukan tabel input) |
| DESKRIPSI | `template_deskripsi` |
| EKSKUL | `ekstrakurikuler_siswa` |
| CATATAN | `catatan_wali_kelas` |
| KOKUR, LINTAS, 7KAIH, CARA LAIN | `dimensi_p5`, `subdimensi_p5`, `proyek_p5`, `nilai_p5` |
| LEGER | Tidak dimigrasi sebagai tabel — jadi laporan/view gabungan |
| SAMPUL RAPOR, ISI SEMESTER 1/2, RAPOR INKUL | Template PDF/render halaman rapor |
| HOME, INPUT DATA, DATA PRIBADI, KOKURIKULER, INPUT NILAI | Tidak berisi data — jadi referensi struktur menu/navigasi dashboard |

**Strategi migrasi data siswa yang sudah ada**: tulis script (Python/`openpyxl` atau `pandas`) yang membaca sheet `SISWA`, `S1–S10`, `EKSKUL`, `CATATAN` dari file asal, lalu insert ke tabel baru sesuai pemetaan di atas. Karena struktur Excel adalah *wide format* dan skema baru *long format*, migrasi butuh tahap "unpivot" untuk nilai TP.

---

## 9. Catatan Kebutuhan Cetak (agar hasil PDF sesuai format resmi)

- Ukuran kertas **A4 potret**, mengikuti tata letak resmi rapor Kurikulum Merdeka (identitas sekolah & siswa di header, tabel nilai per mapel dengan kolom deskripsi capaian, bagian ekskul, kokurikuler P5 terpisah, kehadiran, catatan wali kelas, tanda tangan kepala sekolah & wali kelas & orang tua, serta halaman sampul dengan foto siswa).
- Sediakan opsi cetak **1 siswa** maupun **massal (1 kelas sekaligus, digabung jadi satu PDF/zip)**.
- Font & tata letak sebaiknya bisa disesuaikan per sekolah (logo, kop surat) — jadikan ini bagian dari pengaturan `sekolah`.

---

## 10. Rekomendasi Tahapan Pengembangan (Roadmap)

1. **Fase 1 — Master data**: sekolah, tahun ajaran, kelas, guru, siswa, mapel, kurikulum (TP).
2. **Fase 2 — Input nilai akademik**: form input nilai TP per guru mapel + kalkulasi nilai rapor otomatis.
3. **Fase 3 — Auto-narasi**: mesin pembuat deskripsi capaian (§5.2) + editor manual (guru boleh mengoreksi hasil auto-generate sebelum finalisasi).
4. **Fase 4 — Kokurikuler P5**: input nilai P5 + narasi otomatis, sama pola dengan Fase 3.
5. **Fase 5 — Non-akademik**: ekskul, kehadiran, catatan wali kelas.
6. **Fase 6 — Leger & Ranking**: laporan rekap kelas real-time.
7. **Fase 7 — Generate Rapor PDF**: cover + isi + kokurikuler, cetak satuan/massal.
8. **Fase 8 — Portal orang tua** (opsional) & **audit log**.

---

### Catatan Penutup
Dokumen ini fokus pada **struktur data dan logika**, bukan pilihan teknologi — silakan sesuaikan dengan stack yang tim kembangkan gunakan (mis. Laravel/Django/Node untuk backend, React/Vue untuk frontend, PostgreSQL/MySQL untuk basis data). Jika dibutuhkan, saya juga bisa bantu buatkan skema ini dalam bentuk file `.sql` siap-jalan, atau prototipe halaman input nilai/leger dalam bentuk mockup web.
