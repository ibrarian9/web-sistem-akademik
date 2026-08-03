# Prompt: Review Logika Bisnis — Sistem Informasi Akademik (Kurikulum Merdeka & Tahfizh) & Keuangan Yayasan

> **Cara pakai**: Copy seluruh isi dokumen ini, tempel ke AI Agent (Claude / ChatGPT / DeepSeek / Gemini) bersama lampiran file **`perencanaan-sistem-akademik-yayasan.md`**.

---

## PERAN
Kamu adalah **Senior Business Analyst & Solution Architect** spesialis Sistem Informasi Akademik (SIAKAD) & Keuangan Sekolah/Yayasan di Indonesia. Kamu memiliki pemahaman mendalam tentang Kurikulum Merdeka, Sistem Penilaian Rapor Digital (TP, SAS, Auto-Narasi, P5), Model Pembelajaran Tahfizh, Sistem SPP & Payroll, serta Approval Workflow.

Tugas utama kamu adalah melakukan **Review Logika Bisnis**: mengaudit apakah aturan bisnis, alur proses, skema ERD, dan fitur yang dirancang konsisten, lengkap, tidak ada celah/edge case yang terlewat, dan benar-benar siap diimplementasikan ke dalam kode.

---

## KONTEKS SISTEM
Dokumen yang dilampirkan adalah **`perencanaan-sistem-akademik-yayasan.md`** yang mencakup:
1. **Dual Architecture Akademik**:
   - **Kurikulum Merdeka (Umum)**: Hirarki Mapel → Lingkup Materi → Tujuan Pembelajaran (TP) → Nilai Sumatif TP (`nilai_sumatif_tp`) + SAS (`nilai_sas`) → Mesin Auto-Narasi Capaian (TP tertinggi & terendah + Tie-breaker) → Kokurikuler P5 (7/8 Dimensi, 3 Proyek, 5 Titik Sumatif).
   - **Model Khusus Tahfizh**: Terpisah dari KM! Pengampu Ustadz Tahfizh, Kelas Tahfizh, Target Hafalan Surah/Juz, Tajwid, Mutabaah, dan Lembar Rapor Tahfizh khusus.
2. **Validasi Keabsahan Dokumen Berbasis QR Code**:
   - Seluruh PDF (Rapor KM, Rapor Tahfizh, Rapor P5, Resi Pembayaran) **tanpa tanda tangan basah/manual**, digantikan oleh **QR Code Verification Block** yang terhubung ke URL publik `/verifikasi/dokumen/{uuid}`.
3. **Keuangan & Lock Portal SPP**:
   - Auto-generate SPP tanggal 1, penguncian (lock) portal nilai & rapor mulai **tanggal 10** jika ada tunggakan *blocking* (`jatuh_tempo <= CURRENT_DATE`).
4. **Peran Pengguna (9 Role)**: Super Admin, Guru Umum, Guru/Ustadz Tahfizh, Wali Kelas, Murid/Wali, Finance, Kepala Sekolah, Koordinator, Tata Usaha, dan Akses Publik Verifikasi QR.

---

## AREA AUDIT & TUGAS REVIEW

Sebutkan secara eksplisit nama tabel, kolom, atau pasal dari dokumen `perencanaan-sistem-akademik-yayasan.md` untuk setiap poin berikut:

### 1. Konsistensi Penilaian Kurikulum Merdeka & Auto-Narasi
- Apakah rumus Nilai Rapor ($\text{AVERAGE}(\text{Lingkup Materi}, \text{SAS})$) dan aturan *tie-breaker* auto-narasi (TP urutan paling awal menang saat skor seri) sudah sepenuhnya tercover di skema `nilai_sumatif_tp`, `nilai_sas`, `template_deskripsi`, dan `rapor_detail`?
- Bagaimana penanganan jika seorang murid belum memiliki nilai Sumatif TP sama sekali di salah satu Lingkup Materi saat rapor di-generate?

### 2. Isosiasi & Integrasi Model Tahfizh vs Rombel Umum
- Apakah pemisahan antara `kelas` umum vs `kelas` tahfizh, serta `guru` umum vs `guru` tahfizh sudah aman dari potensi bentrok jadwal mengajar atau kebingungan hak akses guru dual-role?
- Apakah lembar Rapor Tahfizh tersimpan konsisten di `rapor_tahfidz_detail` dan terisolasi dengan benar dari nilai akademik Kurikulum Merdeka?

### 3. Keamanan & Integritas QR Code Keabsahan Dokumen
- Audit alur pencetakan PDF hingga verifikasi publik (`/verifikasi/dokumen/{uuid}`): Apakah `qr_code_hash` di tabel `rapor` dan `pembayaran` cukup aman dari potensi pemalsuan atau duplikasi?
- Apa yang terjadi jika pembayaran di-void atau status nilai direvisi pasca-penerbitan dokumen: apakah URL QR Code otomatis memperbarui status keabsahan dokumen secara *real-time*?

### 4. Trace End-to-End Alur Kritis
Telusuri alur berikut, cari titik yang "putus" atau membutuhkan handler tambahan:
- **Alur Nilai**: Form Sumatif TP → Auto-Narasi → Override Guru → Submit → Koreksi Nilai (Approval Koordinator) → Auto-Recalculate Snapshot `rapor_detail`.
- **Alur Keuangan**: Job SPP (tgl 1) → Lock Rapor (tgl 10) → Payment Input (Atomik DB Lock) → Generate Resi QR Code → Unlock Portal.
- **Alur Kenaikan Kelas & Alumni**: Syarat KKM & SPP → Wizard Kenaikan Kelas → Update `siswa.kelas_id`, `siswa_kelas`, dan Status Alumni.

### 5. Edge Cases & Penanganan Transisi State
- **Overpayment SPP**: Apakah sisa kelebihan bayar tersimpan aman di saldo deposit/kelebihan bayar?
- **Siswa Pindah/Keluar Mid-Year**: Apakah tagihan SPP masa depan otomatis di-set ke status `'batal'`?
- **Mutasi Kelas Mid-Year**: Bagaimana riwayat nilai siswa jika dipindah kelas di pertengahan semester?

---

## CHECKLIST TITIK RAWAN KHUSUS (Wajib Diverifikasi Statusnya)

- [ ] Multi-Role Guru (Satu ustadz mengajar mapel umum & tahfizh) — penugasan di `guru_mapel_kelas` terpisah.
- [ ] Aturan Lock Rapor Tanggal 10 — apakah `jatuh_tempo <= CURRENT_DATE` memutasi portal dengan benar tanpa memblokir tanggal 1–9.
- [ ] QR Code Expiration / Revocation — penanganan jika dokumen rapor ditarik kembali oleh sekolah.
- [ ] Atomisitas transaksi pembayaran & pencegahan *race condition* concurrent payment.

---

## FORMAT OUTPUT REVIEW YANG DIMINTA

1. **Ringkasan Eksekutif** (maks 5 kalimat): Tingkat kematangan logika bisnis SIAKAD ini.
2. **Tabel Temuan Audit**, kolom: `Area/Modul | Temuan / Celah | Skenario Pemicu | Dampak Bisnis | Rekomendasi Perbaikan | Severity (Kritis/Tinggi/Sedang/Rendah)`
3. **Top 5 Prioritas Perbaikan** sebelum tahap coding/development dimulai.

---

## BATASAN REVIEW
*Jangan review pilihan tech stack (Laravel/Livewire/MariaDB) atau tampilan UI. Fokus murni pada **Kebenaran Logika Bisnis, Konsistensi Skema Data, dan Integritas Flow Sistem SIAKAD**.*
