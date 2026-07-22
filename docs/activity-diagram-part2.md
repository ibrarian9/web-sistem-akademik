# 📐 Activity Diagram Lengkap — Sistem Informasi Akademik (Part 2)

> Activity Diagram untuk modul: **Murid**, **Finance**, **Kepala Sekolah**, **Koordinator**, dan **Shared**.

---

## 4. Murid / Portal Siswa

### 4.1 Lihat Jadwal Pelajaran

```mermaid
stateDiagram-v2
    [*] --> BukaJadwalPelajaran
    BukaJadwalPelajaran --> AmbilKelasLogin: Identifikasi kelas siswa
    AmbilKelasLogin --> QueryJadwalKelas
    QueryJadwalKelas --> TampilTabelJadwal: Hari, Jam, Mapel, Guru
    TampilTabelJadwal --> [*]
```

---

### 4.2 Lihat Kehadiran Saya

```mermaid
stateDiagram-v2
    [*] --> BukaKehadiranSaya
    BukaKehadiranSaya --> AmbilDataAbsensi
    AmbilDataAbsensi --> HitungStatistik

    state HitungStatistik {
        [*] --> TotalHadir
        [*] --> TotalSakit
        [*] --> TotalIzin
        [*] --> TotalAlpha
        TotalHadir --> HitungPersentase
        TotalSakit --> HitungPersentase
        TotalIzin --> HitungPersentase
        TotalAlpha --> HitungPersentase
        HitungPersentase --> [*]
    }

    HitungStatistik --> TampilRingkasan
    TampilRingkasan --> TampilDetailHarian
    TampilDetailHarian --> FilterBulanTahun: Filter per Bulan
    FilterBulanTahun --> TampilDetailHarian
    TampilDetailHarian --> [*]
```

---

### 4.3 Lihat Rapor & Nilai

```mermaid
stateDiagram-v2
    [*] --> BukaRaporNilai
    BukaRaporNilai --> PilihSemester
    PilihSemester --> CekRaporTerbit

    state CekRaporTerbit <<choice>>
    CekRaporTerbit --> TampilNilaiPerMapel: Rapor sudah terbit
    CekRaporTerbit --> TampilPesanBelumTerbit: Rapor belum terbit

    TampilNilaiPerMapel --> LihatDetail: Pengetahuan, Keterampilan, Sikap, Nilai Akhir, Predikat
    LihatDetail --> LihatCatatanWaliKelas
    LihatCatatanWaliKelas --> CetakPDFRapor: Klik Cetak

    state CetakPDFRapor {
        [*] --> GeneratePDF
        GeneratePDF --> SisipkanTTDElektronik
        SisipkanTTDElektronik --> SisipkanQRCode
        SisipkanQRCode --> DownloadPDF
        DownloadPDF --> [*]
    }

    CetakPDFRapor --> [*]
    TampilPesanBelumTerbit --> [*]
```

---

### 4.4 Lihat Tagihan SPP & Keuangan

```mermaid
stateDiagram-v2
    [*] --> BukaTagihanSPP
    BukaTagihanSPP --> AmbilTagihanSiswa
    AmbilTagihanSiswa --> TampilDaftarTagihan: Bulan, Nominal, Status
    TampilDaftarTagihan --> LihatTotalTunggakan
    TampilDaftarTagihan --> LihatRiwayatPembayaran: Tagihan Lunas
    LihatTotalTunggakan --> [*]
    LihatRiwayatPembayaran --> [*]
```

---

### 4.5 Ekstrakurikuler Saya

```mermaid
stateDiagram-v2
    [*] --> BukaEkstrakurikuler
    BukaEkstrakurikuler --> AmbilEkskulSiswa
    AmbilEkskulSiswa --> TampilDaftarEkskul: Nama, Pembina, Hari, Jam
    TampilDaftarEkskul --> [*]
```

---

### 4.6 Riwayat Aktivitas Akun

```mermaid
stateDiagram-v2
    [*] --> BukaRiwayatAktivitas
    BukaRiwayatAktivitas --> QueryLogAktivitas
    QueryLogAktivitas --> TampilDaftarLog: Waktu, Aksi, Deskripsi
    TampilDaftarLog --> [*]
```

---

## 5. Finance / Keuangan

### 5.1 Dashboard Keuangan

```mermaid
stateDiagram-v2
    [*] --> BukaDashboardFinance
    BukaDashboardFinance --> HitungStatistikBulanan

    state HitungStatistikBulanan {
        [*] --> QueryPemasukanBulanIni
        [*] --> QueryPengeluaranBulanIni
        [*] --> QueryTunggakanAktif
        QueryPemasukanBulanIni --> HitungKasBersih
        QueryPengeluaranBulanIni --> HitungKasBersih
        HitungKasBersih --> [*]
        QueryTunggakanAktif --> [*]
    }

    HitungStatistikBulanan --> TampilKartuStatistik
    TampilKartuStatistik --> TampilGrafikTren
    TampilGrafikTren --> [*]
```

---

### 5.2 Overview Pembayaran Siswa

```mermaid
stateDiagram-v2
    [*] --> BukaOverviewPembayaran
    BukaOverviewPembayaran --> SetFilter: Kelas, Tahun Ajaran, Status
    SetFilter --> QueryDataPembayaran
    QueryDataPembayaran --> TampilTabelSiswa

    state InputPembayaranCepat {
        [*] --> BukaFormKasir
        BukaFormKasir --> IsiNominalDibayar
        IsiNominalDibayar --> PilihMetodeBayar: Tunai / Transfer / E-Wallet
        PilihMetodeBayar --> SimpanPembayaran
        SimpanPembayaran --> CekLunasPenuh
        CekLunasPenuh --> UpdateStatusLunas: Lunas penuh
        CekLunasPenuh --> UpdateSisaTagihan: Sebagian
        UpdateStatusLunas --> CetakResi
        UpdateSisaTagihan --> CetakResi
        CetakResi --> [*]
    }

    TampilTabelSiswa --> InputPembayaranCepat: Klik Input Bayar
    TampilTabelSiswa --> LihatRiwayat: Klik Detail
    InputPembayaranCepat --> TampilTabelSiswa
    LihatRiwayat --> [*]
```

---

### 5.3 Manajemen Tagihan SPP

```mermaid
stateDiagram-v2
    [*] --> BukaManajemenTagihan
    BukaManajemenTagihan --> PilihMode

    state PilihMode <<choice>>
    PilihMode --> ModePerorangan: Tab Perorangan
    PilihMode --> ModeOtomatis: Tab Otomatis / Bulk

    state ModePerorangan {
        [*] --> PilihSiswa
        PilihSiswa --> PilihJenisTagihan
        PilihJenisTagihan --> IsiBulanNominalTempo
        IsiBulanNominalTempo --> SimpanTagihanTunggal
        SimpanTagihanTunggal --> [*]
    }

    state ModeOtomatis {
        [*] --> PilihBulanSPP
        PilihBulanSPP --> SetNominalSPP
        SetNominalSPP --> SetJatuhTempo
        SetJatuhTempo --> GenerateBulkTagihan
        GenerateBulkTagihan --> BuatTagihanSemuaSiswaAktif
        BuatTagihanSemuaSiswaAktif --> [*]
    }

    ModePerorangan --> TampilDaftarTagihan
    ModeOtomatis --> TampilDaftarTagihan
    TampilDaftarTagihan --> FilterSearch: Filter & Search
    FilterSearch --> TampilDaftarTagihan
    TampilDaftarTagihan --> [*]
```

---

### 5.4 Input Pembayaran (Kasir)

```mermaid
stateDiagram-v2
    [*] --> BukaInputPembayaran
    BukaInputPembayaran --> SearchPilihSiswa
    SearchPilihSiswa --> TampilTagihanBelumLunas
    TampilTagihanBelumLunas --> PilihTagihan
    PilihTagihan --> IsiNominalBayar
    IsiNominalBayar --> PilihMetode: Tunai / Transfer / E-Wallet
    PilihMetode --> SimpanPembayaran

    state SimpanPembayaran {
        [*] --> CekLunas
        CekLunas --> SetLunas: Nominal >= Tagihan
        CekLunas --> SetSisaTagihan: Nominal < Tagihan
        SetLunas --> CatatTransaksi
        SetSisaTagihan --> CatatTransaksi
        CatatTransaksi --> [*]
    }

    SimpanPembayaran --> CetakResiPDF

    state CetakResiPDF {
        [*] --> RenderDataResi
        RenderDataResi --> SisipkanQRCode
        SisipkanQRCode --> GeneratePDFResi
        GeneratePDFResi --> [*]
    }

    CetakResiPDF --> [*]
```

---

### 5.5 Arus Kas Masuk Non-SPP

```mermaid
stateDiagram-v2
    [*] --> BukaArusKasMasuk
    BukaArusKasMasuk --> LihatTotalPemasukan
    LihatTotalPemasukan --> BukaFormInput

    state BukaFormInput {
        [*] --> PilihKategori: Infaq / Sedekah / Donasi / Wakaf
        PilihKategori --> IsiJumlah
        IsiJumlah --> IsiTanggal
        IsiTanggal --> IsiKeterangan
        IsiKeterangan --> SimpanTransaksi
        SimpanTransaksi --> [*]
    }

    BukaFormInput --> TampilRiwayatKasMasuk
    TampilRiwayatKasMasuk --> [*]
```

---

### 5.6 Arus Kas Keluar (Pengeluaran)

```mermaid
stateDiagram-v2
    [*] --> BukaArusKasKeluar
    BukaArusKasKeluar --> BukaFormPengeluaran

    state BukaFormPengeluaran {
        [*] --> PilihKategoriPengeluaran: Listrik, ATK, Pemeliharaan, dll
        PilihKategoriPengeluaran --> IsiJumlahPengeluaran
        IsiJumlahPengeluaran --> IsiTanggalPengeluaran
        IsiTanggalPengeluaran --> UploadBuktiTransaksi
        UploadBuktiTransaksi --> SimpanPengeluaran
        SimpanPengeluaran --> [*]
    }

    BukaFormPengeluaran --> TampilRiwayatPengeluaran
    TampilRiwayatPengeluaran --> [*]
```

---

### 5.7 Pengajuan Dana (Approval Bertingkat)

```mermaid
stateDiagram-v2
    [*] --> StafBuatPengajuan
    StafBuatPengajuan --> IsiFormPengajuan: Judul, Kategori, Nominal, Keterangan
    IsiFormPengajuan --> ValidasiForm
    ValidasiForm --> StatusMenungguKoordinator: status = menunggu_koordinator

    StatusMenungguKoordinator --> KoordinatorReview

    state KoordinatorReview <<choice>>
    KoordinatorReview --> CekThreshold: Disetujui
    KoordinatorReview --> StatusDitolak: Ditolak

    state CekThreshold <<choice>>
    CekThreshold --> StatusDisetujui: Nominal <= Rp 1 Juta
    CekThreshold --> StatusMenungguKepYayasan: Nominal > Rp 1 Juta

    StatusMenungguKepYayasan --> KepYayasanReview

    state KepYayasanReview <<choice>>
    KepYayasanReview --> StatusDisetujui: Disetujui
    KepYayasanReview --> StatusDitolak: Ditolak

    StatusDitolak --> [*]

    StatusDisetujui --> StafKlikCairkan: Cairkan Dana

    state RealisasiDana {
        [*] --> BuatRecordPengeluaran: Auto-catat ke Kas Keluar
        BuatRecordPengeluaran --> UpdateStatusDirealisasi
        UpdateStatusDirealisasi --> [*]
    }

    StafKlikCairkan --> RealisasiDana
    RealisasiDana --> [*]
```

---

### 5.8 Manajemen Gaji Guru (Payroll)

```mermaid
stateDiagram-v2
    [*] --> BukaManajemenGaji
    BukaManajemenGaji --> KlikGenerateDraf

    state GenerateDrafGaji {
        [*] --> PilihBulanTahun
        PilihBulanTahun --> AmbilSemuaGuruAktif
        AmbilSemuaGuruAktif --> LoopPerGuru

        state LoopPerGuru {
            [*] --> CekDuplikatDraf
            CekDuplikatDraf --> SkipGuru: Sudah ada
            CekDuplikatDraf --> HitungKomponen: Belum ada
            HitungKomponen --> SetGajiPokok
            SetGajiPokok --> SetInsentifBPJS
            SetInsentifBPJS --> SetInsentifMaghrib
            SetInsentifMaghrib --> CekPinjamanAktif
            CekPinjamanAktif --> HitungPotonganCicilan: Ada pinjaman
            CekPinjamanAktif --> NoPotongan: Tidak ada
            HitungPotonganCicilan --> HitungTotal
            NoPotongan --> HitungTotal
            HitungTotal --> SimpanDraf
            SimpanDraf --> [*]
            SkipGuru --> [*]
        }

        LoopPerGuru --> [*]
    }

    KlikGenerateDraf --> GenerateDrafGaji
    GenerateDrafGaji --> TampilDaftarSlipDraft

    state ProsesBayarGaji {
        [*] --> BuatPengeluaranKas
        BuatPengeluaranKas --> PotongSisaPinjaman
        PotongSisaPinjaman --> CekSisaPinjaman
        CekSisaPinjaman --> SetPinjamanLunas: Sisa = 0
        CekSisaPinjaman --> PinjamanBerjalan: Sisa > 0
        SetPinjamanLunas --> UpdateStatusDibayar
        PinjamanBerjalan --> UpdateStatusDibayar
        UpdateStatusDibayar --> KirimNotifikasiGuru
        KirimNotifikasiGuru --> [*]
    }

    TampilDaftarSlipDraft --> EditDraf: Sesuaikan komponen
    TampilDaftarSlipDraft --> ProsesBayarGaji: Klik Bayar
    TampilDaftarSlipDraft --> HapusDraf: Hapus draft
    EditDraf --> TampilDaftarSlipDraft
    ProsesBayarGaji --> CetakSlipGajiPDF
    CetakSlipGajiPDF --> [*]
```

---

### 5.9 Manajemen Peminjaman / Kasbon Guru

```mermaid
stateDiagram-v2
    [*] --> BukaManajemenPeminjaman
    BukaManajemenPeminjaman --> TampilDaftarPinjaman

    state TambahPinjaman {
        [*] --> PilihGuru
        PilihGuru --> IsiNominalPinjaman
        IsiNominalPinjaman --> IsiTenorBulan
        IsiTenorBulan --> HitungCicilanPerBulan: nominal / tenor
        HitungCicilanPerBulan --> SimpanPinjaman: sisa = nominal, status = berjalan
        SimpanPinjaman --> [*]
    }

    TampilDaftarPinjaman --> TambahPinjaman: Klik Tambah
    TambahPinjaman --> TampilDaftarPinjaman

    TampilDaftarPinjaman --> FilterStatus: Berjalan / Lunas
    FilterStatus --> TampilDaftarPinjaman

    note right of TampilDaftarPinjaman: Potongan otomatis saat payroll gaji guru
```

---

### 5.10 Laporan Pemasukan

```mermaid
stateDiagram-v2
    [*] --> BukaLaporanPemasukan
    BukaLaporanPemasukan --> SetFilterTanggal: Tanggal Awal & Akhir
    SetFilterTanggal --> FilterMetodeBayar: Tunai / Transfer / E-Wallet
    FilterMetodeBayar --> QueryTransaksi
    QueryTransaksi --> TampilTabelPemasukan
    TampilTabelPemasukan --> HitungTotalPeriode
    HitungTotalPeriode --> EksporPDF: Generate PDF + TTD
    HitungTotalPeriode --> EksporExcel: Download .xlsx
    EksporPDF --> [*]
    EksporExcel --> [*]
```

---

### 5.11 Laporan Pengeluaran

```mermaid
stateDiagram-v2
    [*] --> BukaLaporanPengeluaran
    BukaLaporanPengeluaran --> SetFilterPeriode: Tanggal + Kategori
    SetFilterPeriode --> QueryPengeluaran
    QueryPengeluaran --> TampilTabelPengeluaran
    TampilTabelPengeluaran --> HitungTotal
    HitungTotal --> EksporPDFPengeluaran: PDF
    HitungTotal --> EksporExcelPengeluaran: Excel
    EksporPDFPengeluaran --> [*]
    EksporExcelPengeluaran --> [*]
```

---

### 5.12 Laporan Tunggakan Siswa

```mermaid
stateDiagram-v2
    [*] --> BukaLaporanTunggakan
    BukaLaporanTunggakan --> FilterKelasTA: Filter Kelas + Tahun Ajaran
    FilterKelasTA --> QueryTunggakan
    QueryTunggakan --> TampilDaftarSiswaMenunggak
    TampilDaftarSiswaMenunggak --> LihatDetailPiutang: Sisa piutang per siswa
    LihatDetailPiutang --> EksporSuratTagihan: PDF Surat Tagihan
    LihatDetailPiutang --> EksporExcelTunggakan: Download .xlsx
    EksporSuratTagihan --> [*]
    EksporExcelTunggakan --> [*]
```

---

## 6. Kepala Sekolah

### 6.1 Dashboard Pemantauan Eksekutif

```mermaid
stateDiagram-v2
    [*] --> LoginKepalaSekolah
    LoginKepalaSekolah --> DashboardEksekutif
    DashboardEksekutif --> LihatTotalSiswa
    DashboardEksekutif --> LihatTotalGuru
    DashboardEksekutif --> LihatTotalKelas
    DashboardEksekutif --> LihatIndikatorAkademik
    DashboardEksekutif --> LihatOverviewKeuangan
    LihatTotalSiswa --> [*]
    LihatTotalGuru --> [*]
    LihatTotalKelas --> [*]
    LihatIndikatorAkademik --> [*]
    LihatOverviewKeuangan --> [*]
```

---

## 7. Koordinator

### 7.1 Persetujuan Koreksi Nilai Siswa

```mermaid
stateDiagram-v2
    [*] --> BukaMenuKoreksiNilai
    BukaMenuKoreksiNilai --> TampilPengajuanPending
    TampilPengajuanPending --> PilihPengajuan: Klik baris pengajuan
    PilihPengajuan --> LihatDetail: Guru, Siswa, Mapel, Nilai Lama, Nilai Baru

    state Keputusan <<choice>>
    LihatDetail --> Keputusan

    state ProsesSetujui {
        [*] --> UpdateNilaiDiTabelNilai
        UpdateNilaiDiTabelNilai --> CekRaporSudahTerbit
        CekRaporSudahTerbit --> RecalculateRaporDetail: Rapor ada
        CekRaporSudahTerbit --> SkipRecalculate: Rapor belum ada

        state RecalculateRaporDetail {
            [*] --> AmbilSemuaNilaiMapel
            AmbilSemuaNilaiMapel --> HitungUlangKategori
            HitungUlangKategori --> HitungUlangNilaiAkhir
            HitungUlangNilaiAkhir --> TentukanPredikatBaru
            TentukanPredikatBaru --> UpdateRaporDetail
            UpdateRaporDetail --> [*]
        }

        RecalculateRaporDetail --> SetStatusDisetujui
        SkipRecalculate --> SetStatusDisetujui
        SetStatusDisetujui --> KirimNotifGuru: Notifikasi disetujui
        KirimNotifGuru --> [*]
    }

    state ProsesTolak {
        [*] --> SetStatusDitolak
        SetStatusDitolak --> KirimNotifPenolakan: Notifikasi ditolak
        KirimNotifPenolakan --> [*]
    }

    Keputusan --> ProsesSetujui: Setujui
    Keputusan --> ProsesTolak: Tolak

    ProsesSetujui --> TampilPengajuanPending
    ProsesTolak --> TampilPengajuanPending
```

---

## 8. Shared / Umum

### 8.1 Profil Saya & Tanda Tangan Digital

```mermaid
stateDiagram-v2
    [*] --> BukaProfilSaya
    BukaProfilSaya --> TampilDataProfil

    state EditProfil {
        [*] --> UbahNama
        UbahNama --> UbahEmail
        UbahEmail --> UbahPassword
        UbahPassword --> SimpanProfil
        SimpanProfil --> [*]
    }

    state UploadFoto {
        [*] --> PilihFileGambar
        PilihFileGambar --> ValidasiFile
        ValidasiFile --> SimpanFotoProfil
        SimpanFotoProfil --> [*]
    }

    state TTDDigital {
        [*] --> PilihMetodeTTD
        PilihMetodeTTD --> UploadGambarTTD: Upload file
        PilihMetodeTTD --> GambarDiCanvas: Tanda tangan di web
        UploadGambarTTD --> SimpanTTD
        GambarDiCanvas --> SimpanTTD
        SimpanTTD --> [*]
    }

    TampilDataProfil --> EditProfil
    TampilDataProfil --> UploadFoto
    TampilDataProfil --> TTDDigital
    EditProfil --> TampilDataProfil
    UploadFoto --> TampilDataProfil
    TTDDigital --> TampilDataProfil
```

---

### 8.2 Verifikasi Dokumen Elektronik (Publik)

```mermaid
stateDiagram-v2
    [*] --> PihakKetigaScanQR
    PihakKetigaScanQR --> BukaURLVerifikasi: /verifikasi-dokumen/{code}
    BukaURLVerifikasi --> SistemCariDokumen

    state SistemCariDokumen <<choice>>
    SistemCariDokumen --> TampilInfoDokumen: Dokumen ditemukan
    SistemCariDokumen --> TampilTidakDitemukan: Dokumen tidak ada

    TampilInfoDokumen --> TampilStatusValid: Tanda tangan sah
    TampilTidakDitemukan --> [*]
    TampilStatusValid --> [*]
```

---

### 8.3 Generate Laporan PDF dengan TTD Elektronik

```mermaid
stateDiagram-v2
    [*] --> UserRequestCetakPDF
    UserRequestCetakPDF --> SistemGenerateDokumen

    state SistemGenerateDokumen {
        [*] --> AmbilDataLaporan
        AmbilDataLaporan --> AmbilTTDElektronik: Dari DB e_signatures
        AmbilTTDElektronik --> GenerateQRCodeVerifikasi: URL publik verifikasi
        GenerateQRCodeVerifikasi --> RenderPDF: Data + TTD + QR
        RenderPDF --> [*]
    }

    SistemGenerateDokumen --> UserDownloadPDF
    UserDownloadPDF --> [*]
```

---

> **Dokumen ini mencakup Activity Diagram untuk seluruh 52 menu** dalam Sistem Informasi Akademik, dibagi ke dalam 2 file:
> - `activity-diagram-part1.md` — Super Admin, Tata Usaha, Guru
> - `activity-diagram-part2.md` — Murid, Finance, Kepala Sekolah, Koordinator, Shared
