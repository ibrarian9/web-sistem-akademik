<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>RAPOR TAHFIZH - {{ $siswa->user->nama ?? $siswa->nama_panggilan }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 15mm 15mm 15mm;
        }
        body { 
            font-family: Arial, Helvetica, sans-serif; 
            font-size: 10.5pt; 
            color: #0f172a; 
            line-height: 1.35; 
            margin: 0; 
            padding: 0; 
        }
        .header-title { text-align: center; margin-bottom: 15px; }
        .header-title h1 { margin: 0; font-size: 14pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; color: #78350f; }
        .header-title h2 { margin: 2px 0 0 0; font-size: 12pt; font-weight: bold; text-transform: uppercase; color: #92400e; }

        .meta-table { width: 100%; margin-bottom: 15px; font-size: 10pt; border-collapse: collapse; }
        .meta-table td { padding: 3px 4px; vertical-align: top; }

        .table-tahfidz { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 9.5pt; }
        .table-tahfidz th, .table-tahfidz td { border: 1px solid #78350f; padding: 6px 8px; vertical-align: middle; }
        .table-tahfidz th { background-color: #fef3c7; font-weight: bold; color: #78350f; text-align: center; font-size: 9.5pt; }

        .bg-amber-header { background-color: #fef3c7; font-weight: bold; color: #78350f; padding: 6px 8px; border: 1px solid #78350f; }

        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }

        .box-border { border: 1px solid #78350f; padding: 10px; min-height: 70px; font-size: 9.5pt; }
    </style>
</head>
<body>

    <div class="header-title">
        <h1>SD TAHFIZH F3</h1>
        <h2>LAPORAN HASIL BELAJAR TAHFIZH AL-QUR'AN</h2>
    </div>

    <table class="meta-table">
        <tr>
            <td width="20%">Nama Santri</td>
            <td width="30%">: <strong>{{ strtoupper($siswa->user->nama ?? $siswa->nama_panggilan) }}</strong></td>
            <td width="20%">Halaqah / Kelas</td>
            <td width="30%">: {{ $siswa->kelas->nama_kelas ?? 'Halaqah Al-Mulk' }}</td>
        </tr>
        <tr>
            <td>NIS / NISN</td>
            <td>: {{ $siswa->nis }} / {{ $siswa->nisn }}</td>
            <td>Fase / Semester</td>
            <td>: C / {{ ucfirst($rapor->semester->semester ?? 'Ganjil') }}</td>
        </tr>
        <tr>
            <td>Sekolah</td>
            <td>: SD TAHFIZH F3</td>
            <td>Tahun Pelajaran</td>
            <td>: {{ $rapor->semester->tahunAjaran->nama ?? '2026/2027' }}</td>
        </tr>
    </table>

    <!-- Matriks Penilaian Mutaba'ah Tahfizh -->
    <table class="table-tahfidz">
        <thead>
            <tr>
                <th width="5%">NO</th>
                <th width="30%">ASPEK EVALUASI TAHFIZH</th>
                <th width="45%">MATERI / JUZ / SURAH</th>
                <th width="20%">NILAI (0-100)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center font-bold">1</td>
                <td class="font-bold">TAHSIN AL-QUR'AN</td>
                <td>{{ $nilaiTahfidz->materi_tahsin ?? 'Al-Baqarah (4-5)' }}</td>
                <td class="text-center font-bold">{{ $nilaiTahfidz && $nilaiTahfidz->nilai_tahsin !== null ? round($nilaiTahfidz->nilai_tahsin) : '90' }}</td>
            </tr>
            <tr>
                <td class="text-center font-bold" rowspan="2">2</td>
                <td class="font-bold">MURAJA'AH BERSAMA</td>
                <td>{{ $nilaiTahfidz->murajaah_bersama ?? 'Juz 30' }}</td>
                <td class="text-center font-bold" rowspan="2">{{ $nilaiTahfidz && $nilaiTahfidz->nilai_murajaah !== null ? round($nilaiTahfidz->nilai_murajaah) : '85' }}</td>
            </tr>
            <tr>
                <td class="font-bold">MURAJA'AH MANDIRI</td>
                <td>{{ $nilaiTahfidz->murajaah_mandiri ?? 'Al-Baqarah (1-30)' }}</td>
            </tr>
            <tr>
                <td class="text-center font-bold">3</td>
                <td class="font-bold">TAHFIZH - KITABAH</td>
                <td>{{ $nilaiTahfidz->materi_kitabah ?? 'Al-Baqarah (39-40)' }}</td>
                <td class="text-center font-bold">{{ $nilaiTahfidz && $nilaiTahfidz->nilai_kitabah !== null ? round($nilaiTahfidz->nilai_kitabah) : '90' }}</td>
            </tr>
            <tr>
                <td class="text-center font-bold">4</td>
                <td class="font-bold">TAHFIZH - ZIYADAH</td>
                <td>{{ $nilaiTahfidz->materi_ziyadah ?? 'Al-Baqarah (39-40)' }}</td>
                <td class="text-center font-bold">{{ $nilaiTahfidz && $nilaiTahfidz->nilai_ziyadah !== null ? round($nilaiTahfidz->nilai_ziyadah) : '90' }}</td>
            </tr>
        </tbody>
    </table>

    <table class="table-tahfidz" style="margin-top: 10px;">
        <tr>
            <td width="35%" class="bg-amber-header">TOTAL CAPAIAN HAFALAN</td>
            <td width="65%" class="font-bold">{{ $tahfidzDetail->total_juz_dihafal ?? 1 }} Juz (Surah: {{ $tahfidzDetail->daftar_surah_lulus ?? 'Al-Baqarah' }})</td>
        </tr>
        <tr>
            <td class="bg-amber-header">PREDIKAT KEAGAMAAN</td>
            <td class="font-bold">{{ $nilaiTahfidz->predikat_keagamaan ?? 'Sangat Baik' }}</td>
        </tr>
    </table>

    <div style="margin-top: 15px;">
        <div class="bg-amber-header">CATATAN USTADZ PEMBIMBING TAHFIZH</div>
        <div class="box-border">
            {{ $nilaiTahfidz->catatan_ustadz ?? $rapor->catatan_wali_kelas ?? 'Alhamdulillah perkembangan hafalan santri sangat baik, makhraj fasih, dan tajwid lancar. Tingkatkan kualitas muraja\'ah harian di rumah.' }}
        </div>
    </div>

    <!-- Tanda Tangan 3 Pihak -->
    <table style="width: 100%; margin-top: 30px; border-collapse: collapse; font-size: 9.5pt;">
        <tr>
            <td width="33%" text-align="center" style="text-align: center;">
                Mengetahui,<br>Orang Tua / Wali<br><br><br><br>
                <strong>( ..................................... )</strong>
            </td>
            <td width="34%" text-align="center" style="text-align: center;">
                {{ \App\Models\Pengaturan::getValue('kota', 'Pekanbaru') }}, {{ date('d F Y', strtotime($rapor->tanggal_terbit ?? date('Y-m-d'))) }}<br>
                Ustadz Pembimbing Tahfizh<br><br><br><br>
                <strong><u>Ustadz Nurul Mina, S.Pd.</u></strong>
            </td>
            <td width="33%" text-align="center" style="text-align: center;">
                Mengetahui,<br>Kepala Sekolah<br><br><br><br>
                <strong><u>Dr. H. M. Yusuf, M.A.</u></strong>
            </td>
        </tr>
    </table>

</body>
</html>
