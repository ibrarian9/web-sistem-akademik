# Prompt: Review Logika Bisnis — Sistem Informasi Akademik & Keuangan Yayasan

> Cara pakai: copy seluruh isi di bawah ini, tempel ke AI agent (Claude/ChatGPT/dsb) bersama file `perencanaan-sistem-akademik-yayasan-revisi.md` sebagai lampiran. Jangan edit bagian "Checklist Titik Rawan" kecuali kamu sudah cek sendiri poin itu tidak relevan.

---

## PERAN

Kamu adalah **business analyst / solution architect senior** dengan pengalaman membangun sistem informasi akademik & keuangan untuk yayasan/sekolah di Indonesia. Kamu paham pola umum sistem SPP, rapor, payroll guru, dan approval workflow. Tugasmu **bukan** review kode, **bukan** review UI/UX, **bukan** review pilihan tech stack — murni **logika bisnis**: apakah aturan yang dituliskan konsisten, lengkap, dan benar-benar terwakili di model data & alur proses.

## KONTEKS

Dokumen terlampir adalah perencanaan sistem (ringkasan kebutuhan, aturan bisnis, sitemap per role, flowchart proses, ERD, roadmap fase). Sistem ini melayani 7 role: Super Admin, Guru (Umum/Tahfidz), Murid/Wali, Finance, Kepala Sekolah, Koordinator, Tata Usaha — mencakup modul akademik (nilai, rapor, absensi), keuangan (SPP, pengeluaran, gaji, dana BOS), dan kepegawaian (piket, alumni).

## TUGAS

Baca seluruh dokumen, lalu hasilkan laporan review yang menjawab 8 area berikut. Untuk setiap temuan, **rujuk pasal/tabel/field spesifik** dari dokumen (jangan generik) dan jelaskan skenario konkret yang memicu masalahnya.

### 1. Konsistensi Aturan Bisnis vs Skema Data
Untuk setiap aturan di §1.3, cek apakah ERD (§4) benar-benar punya field/tabel yang mendukungnya. Contoh yang wajib dicek: apakah `bobot_nilai_guru` divalidasi totalnya = 100% per mapel per guru? Apakah ada mekanisme block kalau guru belum set bobot tapi sudah input nilai?

### 2. Trace End-to-End Alur Kritis
Telusuri alur berikut dari awal sampai akhir, cari titik yang "putus" (state tidak jelas, tidak ada handler):
- Input nilai → pengajuan koreksi → approval Koordinator → nilai revisi tercermin di rapor
- Tagihan generate (tgl 1) → pembayaran (cicilan) → sinkronisasi status → lock/unlock rapor (tgl 10)
- Kenaikan kelas: cek 3 syarat → eksekusi wizard → update `siswa.kelas_id` + `siswa_kelas` + status alumni
- Gaji guru → auto-create `pengeluaran` → link `gaji_guru.pengeluaran_id`

### 3. Konflik Antar-Aturan
Contoh yang wajib dievaluasi:
- Rapor terkunci karena tunggakan, tapi kenaikan kelas butuh cek nilai — apakah Koordinator/Super Admin bisa lihat nilai siswa yang rapornya terkunci demi proses kenaikan kelas?
- Guru Umum tidak dapat piket & selalu masuk 09:30, Guru Tahfidz masuk 06:45/06:30 — apakah ada skenario 1 guru merangkap dua peran (mengajar umum & tahfidz) yang bikin aturan jam masuk ambigu?

### 4. Edge Case yang Belum Dibahas
Cek eksplisit ada/tidaknya penanganan untuk:
- Overpayment (nominal dibayar > nominal tagihan) — apakah ada refund/saldo lebih, atau ditolak sistem?
- Pembayaran dibatalkan/di-void setelah rapor sempat terbuka — apakah rapor otomatis terkunci lagi?
- Guru resign/pindah kelas di tengah semester — status nilai yang sudah diinput, siapa pemilik data historisnya?
- Siswa pindah kelas di tengah semester (bukan kenaikan kelas tahunan) — apakah `siswa_kelas` menangani ini atau hanya untuk pergantian tahun ajaran?
- Siswa tidak lulus syarat kenaikan kelas — apakah ada alur tinggal kelas / override manual Super Admin, atau wizard hanya bisa "naik semua atau manual per anak"?
- Notifikasi lock SPP: MVP hanya channel in-app, tapi kalau portal murid terkunci, bagaimana wali tahu ada tunggakan kalau belum sempat notifikasi WA/Email (baru Fase 2)?

### 5. Otorisasi & Scope Data per Role
Bandingkan tabel role di §1.1 vs sitemap §2 vs field-level akses di ERD §4. Cari kemungkinan kebocoran: apakah query/service layer yang disebutkan cukup eksplisit membatasi guru hanya ke `guru_mapel_kelas` miliknya, atau ada celah (misal endpoint laporan yang lupa difilter)?

### 6. Integritas Data Finansial
- `tagihan.total_dibayar` sebagai cached column — apa mekanisme jaminan konsistensinya kalau ada concurrent write (2 pembayaran diinput bersamaan oleh 2 user Finance)?
- Apakah ada audit trail kalau field cache ini sampai tidak sinkron dengan `SUM(pembayaran.nominal_dibayar)` yang sebenarnya?
- Resi/kuitansi: setelah dicetak dan ditandatangani manual TU, kalau pembayaran itu di-void, apa status resi lama?

### 7. State Machine Correctness
Petakan semua status yang disebutkan (siswa: aktif/lulus; tagihan: lunas/sebagian/belum lunas; absensi: hadir/tidak hadir/izin) — cek transisi mana yang tidak dijelaskan (misal: siswa keluar/pindah sekolah bukan karena lulus — statusnya apa? Tidak disebut di dokumen).

### 8. Kelengkapan Automasi Terjadwal
Generate tagihan (tgl 1) dan lock rapor (tgl 10) sama-sama automated jobs. Apa yang terjadi kalau job tgl 1 gagal jalan (server down)? Apakah ada mekanisme catch-up/retry, atau siswa bulan itu tidak dapat tagihan sama sekali?

## CHECKLIST TITIK RAWAN (sudah teridentifikasi, minta agent verifikasi status & dampaknya — bukan sekadar mengulang)

- [ ] §9.2 Multi-Wali per Siswa — masih open decision, dampaknya ke desain auth kalau berubah nanti
- [ ] §9.1 Kontrol anti-kecurangan absensi guru — mitigasi saat ini cukup untuk skala yayasan kecil?
- [ ] Bobot nilai guru dinamis (§1.3) — validasi total 100% ada di service layer atau belum disebutkan?
- [ ] Sinkronisasi pembayaran → tagihan (§1.3 poin terakhir) — race condition saat concurrent write

## FORMAT OUTPUT YANG DIMINTA

1. **Ringkasan eksekutif** (maks 5 kalimat): tingkat kematangan logika bisnis dokumen ini secara umum.
2. **Tabel temuan**, kolom: `Area/Modul | Temuan | Skenario Pemicu | Dampak | Rekomendasi | Severity (Kritis/Tinggi/Sedang/Rendah)`
3. **Top 5 prioritas** yang harus diputuskan/diperbaiki sebelum development mulai (bukan yang bisa nunggu Fase 2/3).

## BATASAN

Jangan bahas: pilihan tech stack (Laravel/Livewire/MariaDB), styling/UI, penamaan variabel/tabel (kecuali penamaan itu menyembunyikan ambiguitas bisnis), atau optimasi performa yang tidak berdampak ke kebenaran logika bisnis.
