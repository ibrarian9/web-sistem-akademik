# Rencana Perbaikan UI/UX Menu Guru (Bab, Materi, Nilai, Absensi, & Tutorial)

Dokumen ini memuat rencana aksi sistematis beserta status penyelesaian untuk meningkatkan *user-friendliness*, keterbacaan, kebersihan *whitespace*, serta kemudahan bagi guru awam teknologi pada sistem akademik sekolah.

---

## 1. Modul Pembuatan Bab & TP (Kurikulum Merdeka)
**Lokasi File**:
- `app/Livewire/Guru/ManajemenKurikulumMerdeka.php`
- `resources/views/livewire/guru/manajemen-kurikulum-merdeka.blade.php`

### Masalah Teridentifikasi:
1. Dropdown Mata Pelajaran menampilkan seluruh mata pelajaran sekolah tanpa memfilter mapel yang diampu oleh guru yang sedang login.
2. Kartu "Template Frasa Auto-Narasi Capaian Rapor" memakan ruang vertikal ~250px di bagian paling atas sebelum daftar Bab terlihat (*above the fold*), membuat guru awam bingung.
3. Tombol "Tambah TP" ganda (di header atas dan di dalam kartu Bab).
4. Input "Kategori" pada modal Bab berupa text field bebas berlabel "Opsional" tanpa petunjuk kategori yang valid.

### Status Implementasi: [SELESAI]
1. **Prioritas Mapel Pengampu**: `ManajemenKurikulumMerdeka.php` secara otomatis mendeteksi mata pelajaran yang ditugaskan kepada guru yang login (melalui `GuruMapelKelas`), mengelompokkannya di bagian atas dengan label *"Mata Pelajaran Diampu"*.
2. **Collapsible Accordion Frasa Rapor**: Kartu form frasa auto-narasi dibungkus dengan Alpine.js akordeon (default tertutup). Menghemat ~200px ruang vertikal sehingga daftar Bab langsung terlihat tanpa perlu menggulir.
3. **Pilihan Kategori Terarah**: Input kategori diubah menjadi dropdown preset terarah: `sumatif` (*Sumatif Lingkup Materi*), `formatif` (*Formatif / Pengayaan*), `praktek` (*Praktik / Portofolio*).
4. **Penyederhanaan Tombol Header (Rekomendasi Audit 1)**: Menghapus tombol ganda *"Tambah TP"* di header atas. Tombol *"Tambah TP"* diletakkan kontekstual langsung di tiap kartu Bab (`openTpModal($lm->id)`).
5. **Badge Status Otomatis (Rekomendasi Audit 3)**: Menambahkan badge hijau kontras *"Standar Sekolah Aktif (Otomatis)"* dan penjelasan bahwa pengaturan ini opsional.

---

## 2. Modul Pengisian Nilai (Matriks Nilai Sumatif TP & SAS)
**Lokasi File**:
- `app/Livewire/Guru/InputNilaiSumatif.php`
- `resources/views/livewire/guru/input-nilai-sumatif.blade.php`

### Masalah Teridentifikasi:
1. Tidak ada peringatan *unsaved changes* (jika guru mengetik nilai lalu tidak sengaja menutup browser/berpindah menu, seluruh input hilang).
2. Tidak ada validasi batas angka 0 - 100 di sisi backend sehingga angka saltik (misal 850) berisiko tersimpan.
3. Header tabel TP memotong judul secara ekstrem tanpa konteks Bab yang jelas.
4. Tampilan default menampilkan seluruh Bab sekaligus sehingga tabel langsung melebar puluhan kolom secara horizontal.

### Status Implementasi: [SELESAI]
1. **Unsaved Changes Guard**: Menambahkan pelindung Alpine.js + `window.onbeforeunload` dan badge peringatan visual *"Nilai belum disimpan"* yang menyala saat guru mengetik angka.
2. **Validasi Rentang Nilai (0 - 100)**: Validasi ketat di `saveMatrix()` untuk memastikan angka `0 <= nilai <= 100`, serta visual feedback border merah jika input > 100.
3. **Format Header TP Informatif**: Menampilkan label Bab dan urutan TP (contoh: `B1.TP1`) agar guru langsung paham materi yang dinilai tanpa bergantung pada tooltip.
4. **Default Filter Bab Pertama (Rekomendasi Audit 4)**: Saat membuka form atau berganti mapel, sistem otomatis memfilter ke Bab 1 terlebih dahulu untuk mencegah tabel langsung melebar 25+ kolom di awal. Ditambahkan opsi *"-- Semua Bab (Tampilkan Seluruh TP) --"* untuk melihat matriks menyeluruh.

---

## 3. Modul Pengisian Absensi Siswa
**Lokasi File**:
- `app/Livewire/Guru/AbsensiSiswa.php`
- `resources/views/livewire/guru/absensi-siswa.blade.php`

### Masalah Teridentifikasi:
1. Ilusi data tersimpan setelah menekan tombol aksi masal "Semua Hadir" tanpa menekan tombol simpan.
2. Bug penargetan loading pada `loadingTarget="selectedKelasId, tanggal"`.
3. Kolom NIS memakan lebar 112px secara terpisah padahal jarang dipakai saat presensi.
4. Guru harus menggulir ke paling bawah untuk menekan tombol simpan setelah klik aksi masal.
5. Tombol status pada layar kecil/mobile relatif sempit dan rentan salah klik.

### Status Implementasi: [SELESAI]
1. **Badge Status Penyimpanan**: Menampilkan banner peringatan interaktif setelah tombol "Semua Hadir" atau tombol status lainnya diklik: *"Status presensi telah diubah. Pastikan menekan tombol 'Simpan Seluruh Kehadiran' di bawah"*.
2. **Perbaikan `loadingTarget`**: Target Livewire loading diperbaiki menjadi `loadingTarget="kelas_id, tanggal"`.
3. **Penyatuan Kolom NIS ke Nama Siswa**: NIS digabungkan sebagai teks sekunder di bawah nama siswa. Menghemat 112px lebar tabel dan memberikan whitespace lebih lega.
4. **Tombol Simpan Cepat Atas (Rekomendasi Audit 2)**: Menambahkan tombol *"Simpan Presensi"* di Header kartu atas dan tombol *"Simpan Cepat"* tepat di sebelah tombol aksi masal (*"Semua Hadir"*).
5. **Tombol Mobile Ramah Sentuhan (Rekomendasi Audit 5)**: Memperbesar touch target (`min-h-[38px]`, `px-3.5 py-2`, `touch-manipulation`), kontras warna tinggi (WCAG AA), dan pembungkus fleksibel bebas overflow horizontal.

---

## 4. Modul Tutorial & Panduan Sistem
**Lokasi File**:
- `app/Livewire/Shared/TutorialDanFaq.php`

### Masalah Teridentifikasi:
1. Kategori Guru di Pusat Bantuan belum memiliki panduan untuk:
   - Pengisian Nilai Sumatif TP & SAS.
   - Pencatatan Presensi Harian Siswa & Penggunaan Fitur Masal.
2. FAQ untuk guru masih terbatas.

### Status Implementasi: [SELESAI]
1. **2 Tutorial Interaktif Lengkap**:
   - Tutorial "Panduan Input Nilai Sumatif TP & SAS (Format Spreadsheet Cepat)"
   - Tutorial "Panduan Presensi Harian Siswa & Penggunaan Fitur Masal"
2. **2 FAQ Baru Khusus Guru**:
   - *"Apakah nilai di tabel Sumatif otomatis tersimpan saat saya mengetik angka?"*
   - *"Mengapa setelah saya klik 'Semua Hadir' muncul peringatan bahwa presensi belum tersimpan?"*

---

## 5. Modul Tutorial Interaktif Driver.js (Spotlight Onboarding)
**Lokasi File**:
- `resources/js/app.js`
- `resources/css/app.css`
- `resources/views/livewire/guru/manajemen-kurikulum-merdeka.blade.php`
- `resources/views/livewire/guru/input-nilai-sumatif.blade.php`
- `resources/views/livewire/guru/absensi-siswa.blade.php`

### Status Implementasi: [SELESAI]
1. **Pemasangan Paket Driver.js**: `npm install driver.js` terpasang dan dibundel rapi di Vite.
2. **Helper Global Siakad Tour**: Menyediakan fungsi `window.startSiakadTour` dan `window.checkAutoTour` dengan label bahasa Indonesia ramah guru awam (*"Lanjut →"*, *"← Kembali"*, *"Selesai & Paham ✓"*).
3. **Styling Kustom SIAKAD**: Popover putih dengan aksen Emerald (`#047857`), font Plus Jakarta Sans, sudut membulat `rounded-2xl`, dan kontras tinggi WCAG AA.
4. **Trigger Otomatis & Mandiri**:
   - Berjalan otomatis pada kunjungan pertama guru (`localStorage`).
   - Tombol bantuan *"Panduan Interaktif"* tersedia di header setiap modul agar guru dapat membuka kembali panduan kapan saja.

---

## 6. Hasil Pengujian Otomatis & Penjaminan Mutu

Seluruh suite pengujian telah dijalankan dan **100% Lulus (0 Gagal)**:
1. `GuruMenuUsabilityEnhancementTest.php`: **5 passed (31 assertions)**
2. `GuruAkademikTest.php`: **10 passed (28 assertions)**
3. `PenilaianTpDanSasTest.php`: **5 passed (34 assertions)**
4. `npm run build`: **Built in 2.17s (0 errors)**
5. `graphify update .`: **Sinkronisasi 3293 nodes, 6659 edges, 458 communities**
