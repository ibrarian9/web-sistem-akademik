<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapor Hasil Belajar - {{ $siswa->user->nama ?? '-' }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #222;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
            color: #111;
        }
        .header p {
            margin: 3px 0 0 0;
            color: #555;
            font-size: 11px;
        }
        .title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 25px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .info-table td.label {
            width: 18%;
            font-weight: bold;
            color: #555;
        }
        .info-table td.value {
            width: 32%;
        }
        .rapor-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .rapor-table th {
            background-color: #f5f5f5;
            border: 1px solid #111;
            padding: 6px;
            font-weight: bold;
            text-align: left;
            text-transform: uppercase;
            font-size: 10px;
        }
        .rapor-table td {
            border: 1px solid #111;
            padding: 6px;
        }
        .text-center {
            text-align: center;
        }
        .text-bold {
            font-weight: bold;
        }
        .comment-section {
            border: 1px solid #111;
            padding: 10px;
            background-color: #fafafa;
            margin-top: 15px;
            border-radius: 4px;
        }
        .comment-section h4 {
            margin: 0 0 5px 0;
            font-size: 11px;
            text-transform: uppercase;
            color: #444;
        }
        .comment-section p {
            margin: 0;
            font-style: italic;
            color: #222;
        }
        .footer {
            margin-top: 40px;
            width: 100%;
        }
        .footer-table {
            width: 100%;
        }
        .footer-table td {
            text-align: center;
            width: 33%;
        }
        .signature-space {
            height: 60px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>YAYASAN PENDIDIKAN ISLAM</h1>
        <p>Jl. Kaliurang Km. 10, Sleman, D.I. Yogyakarta | Telp: (0274) 123456</p>
    </div>

    <div class="title">LAPORAN HASIL BELAJAR (RAPOR)</div>

    <table class="info-table">
        <tr>
            <td class="label">Nama Siswa</td>
            <td class="value">: {{ $siswa->user->nama ?? '-' }}</td>
            <td class="label">Kelas</td>
            <td class="value">: {{ $siswa->kelas->nama_kelas ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">NIS / NISN</td>
            <td class="value">: {{ $siswa->nis ?? '-' }} / {{ $siswa->nisn ?? '-' }}</td>
            <td class="label">Semester</td>
            <td class="value">: {{ $rapor->semester->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Sekolah</td>
            <td class="value">: Madrasah Yayasan Pendidikan Islam</td>
            <td class="label">Tahun Ajaran</td>
            <td class="value">: {{ $rapor->semester->tahunAjaran->nama ?? '-' }}</td>
        </tr>
    </table>

    <table class="rapor-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 35%;">Mata Pelajaran</th>
                <th style="width: 10%; text-align: center;">Kognitif</th>
                <th style="width: 10%; text-align: center;">Psikomotor</th>
                <th style="width: 10%; text-align: center;">Afektif</th>
                <th style="width: 10%; text-align: center;">Keagamaan</th>
                <th style="width: 10%; text-align: center;">Nilai Akhir</th>
                <th style="width: 10%; text-align: center;">Predikat</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($raporDetails as $index => $detail)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-bold">{{ $detail['mapel']['nama_mapel'] ?? '-' }}</td>
                    <td class="text-center">{{ $detail['nilai_pengetahuan'] ? floatval($detail['nilai_pengetahuan']) : '-' }}</td>
                    <td class="text-center">{{ $detail['nilai_keterampilan'] ? floatval($detail['nilai_keterampilan']) : '-' }}</td>
                    <td class="text-center">{{ $detail['nilai_sikap'] ? floatval($detail['nilai_sikap']) : '-' }}</td>
                    <td class="text-center">{{ $detail['nilai_keagamaan'] ? floatval($detail['nilai_keagamaan']) : '-' }}</td>
                    <td class="text-center text-bold" style="color: #4338ca;">{{ floatval($detail['nilai_akhir']) }}</td>
                    <td class="text-center text-bold uppercase" style="color: #047857;">{{ $detail['predikat'] ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 15px; color: #777;">
                        Belum ada data mata pelajaran pada rapor ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($rapor->catatan_wali_kelas)
        <div class="comment-section">
            <h4>Catatan Wali Kelas:</h4>
            <p>"{{ $rapor->catatan_wali_kelas }}"</p>
        </div>
    @endif

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td style="width: 30%; vertical-align: top;">
                    <div style="height: 15px; line-height: 15px; margin-bottom: 3px;">&nbsp;</div>
                    <div style="font-weight: bold; margin-bottom: 4px; font-size: 10px;">Orang Tua / Wali Siswa,</div>
                    <div class="signature-space" style="height: 70px;"></div>
                    ..................................
                </td>
                <td style="width: 35%; vertical-align: top;">
                    <x-ttd-elektronik 
                        role="wali_kelas" 
                        docType="RAP" 
                        :docId="$rapor->id" 
                        :user="$siswa->kelas->waliKelas->user ?? null" 
                        title="Wali Kelas" 
                        :showLocation="false" 
                    />
                </td>
                <td style="width: 35%; vertical-align: top;">
                    <x-ttd-elektronik 
                        role="kepala_sekolah" 
                        docType="RAP" 
                        :docId="$rapor->id" 
                        title="Kepala Madrasah" 
                        :tanggal="$rapor->tanggal_terbit ? \Carbon\Carbon::parse($rapor->tanggal_terbit)->format('d M Y') : date('d M Y')" 
                    />
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
