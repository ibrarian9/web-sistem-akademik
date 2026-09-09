<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pendampingan - {{ $siswa->user->nama ?? 'Siswa' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 15mm 15mm;
        }
        body { 
            font-family: Arial, Helvetica, sans-serif; 
            font-size: 9.5pt; 
            color: #0f172a; 
            line-height: 1.35; 
            margin: 0; 
            padding: 0; 
        }
        .header-title { 
            text-align: center; 
            margin-bottom: 14px; 
            border-bottom: 2px solid #047857;
            padding-bottom: 8px;
        }
        .header-title h1 { 
            margin: 0; 
            font-size: 13pt; 
            font-weight: bold; 
            text-transform: uppercase; 
            letter-spacing: 0.5px; 
            color: #064e3b;
        }
        .header-title h2 { 
            margin: 3px 0 0 0; 
            font-size: 10pt; 
            font-weight: bold; 
            text-transform: uppercase; 
            color: #334155; 
        }
        .header-title p {
            margin: 2px 0 0 0;
            font-size: 8pt;
            color: #64748b;
        }

        .meta-table { 
            width: 100%; 
            margin-bottom: 12px; 
            font-size: 9pt; 
            border-collapse: collapse; 
        }
        .meta-table td { 
            padding: 2.5px 4px; 
            vertical-align: top; 
        }

        /* Banner Penjelasan Skala Kualitatif */
        .skala-banner {
            border: 1px solid #047857;
            background-color: #f0fdf4;
            border-radius: 5px;
            padding: 6px 8px;
            margin-bottom: 12px;
            font-size: 8pt;
        }
        .skala-title {
            font-weight: bold;
            color: #064e3b;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .skala-grid {
            width: 100%;
            border-collapse: collapse;
        }
        .skala-grid td {
            padding: 1px 4px;
            vertical-align: top;
        }
        .badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 7.5pt;
        }
        .badge-bb { background-color: #ffe4e6; color: #9f1239; border: 1px solid #f43f5e; }
        .badge-mb { background-color: #fef3c7; color: #92400e; border: 1px solid #f59e0b; }
        .badge-bsh { background-color: #d1fae5; color: #065f46; border: 1px solid #10b981; }
        .badge-bsb { background-color: #dbeafe; color: #1e40af; border: 1px solid #3b82f6; }

        /* Tabel Rekap Capaian */
        .table-laporan { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 12px; 
            font-size: 8.5pt; 
        }
        .table-laporan th, .table-laporan td { 
            border: 1px solid #475569; 
            padding: 5px 6px; 
            vertical-align: top; 
        }
        .table-laporan th { 
            background-color: #e2e8f0; 
            font-weight: bold; 
            color: #0f172a; 
            text-align: center; 
            font-size: 8.5pt; 
        }
        
        .section-header { 
            background-color: #f1f5f9; 
            font-weight: bold; 
            color: #0f172a; 
            padding: 4px 6px; 
            border: 1px solid #475569; 
            font-size: 8.5pt; 
            text-transform: uppercase;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .box-rekomendasi { 
            border: 1px solid #475569; 
            background-color: #fafaf9;
            padding: 8px 10px; 
            font-size: 8.5pt; 
            margin-bottom: 14px;
            border-radius: 4px;
        }

        .ttd-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
            font-size: 8.5pt; 
            page-break-inside: avoid;
        }
        .ttd-table td { 
            width: 33.33%; 
            text-align: center; 
            vertical-align: top; 
            padding: 0 8px; 
        }
        .ttd-space { height: 50px; }
    </style>
</head>
<body>

    <!-- KOP / HEADER LAPORAN -->
    <div class="header-title">
        <h1>{{ $namaSekolah }}</h1>
        <h2>LAPORAN CAPAIAN PERKEMBANGAN PENDAMPINGAN KHUSUS (INKLUSI)</h2>
        <p>{{ $alamatSekolah }} | Periode: {{ $periodeTitle }}</p>
    </div>

    <!-- IDENTITAS SISWA & PENDAMPING -->
    <table class="meta-table">
        <tr>
            <td style="width: 18%;" class="font-bold">Nama Peserta Didik</td>
            <td style="width: 2%;">:</td>
            <td style="width: 32%;" class="font-bold">{{ strtoupper($siswa->user->nama ?? '-') }}</td>
            <td style="width: 18%;" class="font-bold">Kelas Umum</td>
            <td style="width: 2%;">:</td>
            <td style="width: 28%;">{{ $siswa->kelas->nama_kelas ?? 'Belum Diplot' }}</td>
        </tr>
        <tr>
            <td class="font-bold">NIS / NISN</td>
            <td>:</td>
            <td>{{ $siswa->nis ?: '-' }} / {{ $siswa->nisn ?: '-' }}</td>
            <td class="font-bold">Halaqah Tahfizh</td>
            <td>:</td>
            <td>{{ $siswa->kelasTahfidz->nama_kelas ?? 'Halaqah Reguler' }}</td>
        </tr>
        <tr>
            <td class="font-bold">Tempat, Tanggal Lahir</td>
            <td>:</td>
            <td>{{ $siswa->tempat_lahir ?: '-' }}, {{ $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('d/m/Y') : '-' }}</td>
            <td class="font-bold">Guru Pendamping</td>
            <td>:</td>
            <td class="font-bold" style="color: #047857;">{{ $namaGuruPendamping }}</td>
        </tr>
        <tr>
            <td class="font-bold">Jenis Kelamin</td>
            <td>:</td>
            <td>{{ $siswa->jenis_kelamin === 'P' ? 'Perempuan' : 'Laki-laki' }}</td>
            <td class="font-bold">Tahun Ajaran / Sem.</td>
            <td>:</td>
            <td>{{ $activeSemester->tahunAjaran->tahun ?? '-' }} / {{ $activeSemester->nama ?? '-' }}</td>
        </tr>
    </table>

    <!-- STANDARISASI PENILAIAN KUALITATIF KETERANGAN -->
    <div class="skala-banner">
        <div class="skala-title">Kriteria Skala Capaian Perkembangan Kualitatif:</div>
        <table class="skala-grid">
            <tr>
                <td style="width: 25%;">
                    <span class="badge badge-bb">BB</span> : <b>Belum Berkembang</b> (Perlu bimbingan penuh)
                </td>
                <td style="width: 25%;">
                    <span class="badge badge-mb">MB</span> : <b>Mulai Berkembang</b> (Mulai tampak kemampuan)
                </td>
                <td style="width: 25%;">
                    <span class="badge badge-bsh">BSH</span> : <b>Berkembang Sesuai Harapan</b> (Konsisten)
                </td>
                <td style="width: 25%;">
                    <span class="badge badge-bsb">BSB</span> : <b>Berkembang Sangat Baik</b> (Mandiri/Inisiatif)
                </td>
            </tr>
        </table>
    </div>

    <!-- TABEL REKAPITULASI CAPAIAN PERKEMBANGAN PER ASPEK -->
    <table class="table-laporan">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 22%; text-align: left;">Aspek Pengamatan</th>
                <th style="width: 14%;">Capaian Akhir</th>
                <th style="width: 37%; text-align: left;">Deskripsi & Catatan Pengamatan Guru</th>
                <th style="width: 22%; text-align: left;">Rekomendasi / Tindak Lanjut</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach ($rekapAspek as $aspekKey => $item)
                <tr>
                    <td class="text-center font-bold">{{ $no++ }}</td>
                    <td class="font-bold" style="color: #0f172a;">{{ $item['nama'] }}</td>
                    <td class="text-center">
                        @if ($item['capaian_terakhir'] === 'BB')
                            <span class="badge badge-bb">BB</span><br><span style="font-size: 7pt;">Belum Berkembang</span>
                        @elseif ($item['capaian_terakhir'] === 'MB')
                            <span class="badge badge-mb">MB</span><br><span style="font-size: 7pt;">Mulai Berkembang</span>
                        @elseif ($item['capaian_terakhir'] === 'BSH')
                            <span class="badge badge-bsh">BSH</span><br><span style="font-size: 7pt;">Sesuai Harapan</span>
                        @elseif ($item['capaian_terakhir'] === 'BSB')
                            <span class="badge badge-bsb">BSB</span><br><span style="font-size: 7pt;">Sangat Baik</span>
                        @else
                            <span style="color: #94a3b8; font-style: italic;">-</span>
                        @endif
                    </td>
                    <td>{{ $item['catatan'] }}</td>
                    <td>{{ $item['rekomendasi'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- KOTAK REKOMENDASI DAN TINDAK LANJUT UMUM -->
    <div class="box-rekomendasi">
        <div class="font-bold" style="color: #064e3b; text-transform: uppercase; margin-bottom: 4px;">
            Kesimpulan Rekomendasi & Rencana Tindak Lanjut Pendampingan:
        </div>
        @if ($rekomendasiUmum->isNotEmpty())
            <ul style="margin: 0; padding-left: 18px;">
                @foreach ($rekomendasiUmum as $rec)
                    <li style="margin-bottom: 2px;">{{ $rec }}</li>
                @endforeach
            </ul>
        @else
            <p style="margin: 0; color: #64748b; font-style: italic;">
                Pertahankan pendampingan intensif bersama orang tua dan guru kelas untuk memfasilitasi optimalisasi kemandirian serta interaksi sosial ananda.
            </p>
        @endif
    </div>

    <!-- TANDA TANGAN RESMI -->
    <table class="ttd-table">
        <tr>
            <td>
                Mengetahui,<br>
                Orang Tua / Wali Siswa
                <div class="ttd-space"></div>
                <b>( .................................................. )</b>
            </td>
            <td>
                Pekanbaru, {{ $tanggalCetak }}<br>
                Guru Pendamping Khusus
                <div class="ttd-space"></div>
                <b>{{ $namaGuruPendamping }}</b>
            </td>
            <td>
                Menyetujui,<br>
                Kepala Sekolah
                <div class="ttd-space"></div>
                <b>{{ $kepalaSekolah }}</b>
            </td>
        </tr>
    </table>

</body>
</html>
