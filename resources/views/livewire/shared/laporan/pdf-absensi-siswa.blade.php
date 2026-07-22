<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Absensi Siswa - {{ $kelas->nama_kelas }}</title>
    <style>
        @page {
            margin: 1.5cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9px;
            color: #333333;
            line-height: 1.2;
        }
        .header {
            margin-bottom: 15px;
            border-bottom: 2px solid #22c55e;
            padding-bottom: 10px;
        }
        .header table {
            width: 100%;
        }
        .header-title {
            font-size: 16px;
            font-weight: bold;
            color: #15803d;
        }
        .header-meta {
            font-size: 10px;
            color: #666;
            text-align: right;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 2px 0;
            font-size: 10px;
        }
        .info-label {
            font-weight: bold;
            color: #555;
            width: 100px;
        }
        .info-value {
            color: #333;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .data-table th {
            background-color: #f4f4f5;
            color: #27272a;
            font-weight: bold;
            border: 1px solid #d4d4d8;
            padding: 5px 2px;
            text-align: center;
        }
        .data-table td {
            border: 1px solid #e4e4e7;
            padding: 4px 2px;
            text-align: center;
        }
        .student-name {
            text-align: left !important;
            padding-left: 5px !important;
            font-weight: bold;
            max-width: 150px;
            overflow: hidden;
            white-space: nowrap;
        }
        .status-h {
            color: #166534;
            background-color: #dcfce7;
            font-weight: bold;
        }
        .status-i {
            color: #92400e;
            background-color: #fef3c7;
            font-weight: bold;
        }
        .status-a {
            color: #991b1b;
            background-color: #fee2e2;
            font-weight: bold;
        }
        .status-l {
            color: #1e40af;
            background-color: #dbeafe;
            font-weight: bold;
        }
        .status-empty {
            color: #d4d4d8;
        }
        .summary-col {
            font-weight: bold;
            background-color: #f4f4f5;
        }
        .rate-col {
            font-weight: bold;
            background-color: #e4e4e7;
        }
        .legend {
            margin-top: 15px;
            font-size: 8px;
            color: #666;
        }
        .legend span {
            margin-right: 15px;
            display: inline-block;
        }
        .legend-box {
            display: inline-block;
            width: 12px;
            height: 12px;
            line-height: 12px;
            text-align: center;
            border-radius: 2px;
            font-weight: bold;
            margin-right: 3px;
            font-size: 8px;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="header-title">REKAP ABSENSI SISWA</div>
                    <div style="font-size: 11px; font-weight: bold; color: #555;">SISTEM AKADEMIK YAYASAN</div>
                </td>
                <td class="header-meta">
                    Tanggal Cetak: {{ date('d-m-Y H:i') }}
                </td>
            </tr>
        </table>
    </div>

    <table class="info-table">
        <tr>
            <td class="info-label">Kelas</td>
            <td class="info-value">: {{ $kelas->nama_kelas }}</td>
            <td class="info-label" style="text-align: right; padding-right: 10px;">Periode</td>
            <td class="info-value">: {{ $namaBulan }} {{ $tahun }}</td>
        </tr>
        <tr>
            <td class="info-label">Wali Kelas</td>
            <td class="info-value">: {{ $kelas->guruUmum->user->nama ?? '-' }}</td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 140px; text-align: left; padding-left: 5px;">Nama Siswa</th>
                @for ($day = 1; $day <= $daysInMonth; $day++)
                    <th style="width: 18px;">{{ $day }}</th>
                @endfor
                <th style="width: 22px; background-color: #dcfce7; color: #166534;">H</th>
                <th style="width: 22px; background-color: #fef3c7; color: #92400e;">I</th>
                <th style="width: 22px; background-color: #fee2e2; color: #991b1b;">A</th>
                <th style="width: 32px; background-color: #e4e4e7;">%</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($matrix as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="student-name">
                        {{ $row['siswa']->user->nama }}
                        <div style="font-size: 7px; color: #888; font-weight: normal;">NIS: {{ $row['siswa']->nis }}</div>
                    </td>
                    @for ($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $status = $row['days'][$day];
                            $cellClass = 'status-empty';
                            $cellText = '•';
                            
                            if ($status === 'hadir') {
                                $cellClass = 'status-h';
                                $cellText = 'H';
                            } elseif ($status === 'izin') {
                                $cellClass = 'status-i';
                                $cellText = 'I';
                            } elseif ($status === 'tidak_hadir') {
                                $cellClass = 'status-a';
                                $cellText = 'A';
                            } elseif ($status === 'libur') {
                                $cellClass = 'status-l';
                                $cellText = 'L';
                            }
                        @endphp
                        <td class="{{ $cellClass }}">{{ $cellText }}</td>
                    @endfor
                    <td class="summary-col" style="color: #166534;">{{ $row['hadir'] }}</td>
                    <td class="summary-col" style="color: #92400e;">{{ $row['izin'] }}</td>
                    <td class="summary-col" style="color: #991b1b;">{{ $row['tidak_hadir'] }}</td>
                    <td class="rate-col">{{ $row['rate'] }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="legend">
        <strong>Keterangan:</strong>
        <span>
            <span class="legend-box status-h">H</span> Hadir
        </span>
        <span>
            <span class="legend-box status-i">I</span> Izin
        </span>
        <span>
            <span class="legend-box status-a">A</span> Alpa / Tidak Hadir
        </span>
        <span>
            <span class="status-empty">•</span> Belum Diinput
        </span>
    </div>

    <div style="margin-top: 30px; width: 100%;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <x-ttd-elektronik 
                        role="wali_kelas" 
                        docType="ABS" 
                        :docId="$kelas->id" 
                        :user="$kelas->guruUmum->user ?? null" 
                        title="Wali Kelas" 
                        :showLocation="false" 
                    />
                </td>
                <td style="width: 50%; vertical-align: top;">
                    <x-ttd-elektronik 
                        role="kepala_sekolah" 
                        docType="ABS" 
                        :docId="$kelas->id" 
                        title="Kepala Sekolah / Madrasah" 
                    />
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
