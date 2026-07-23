<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Nilai Siswa - {{ $kelas->nama_kelas }} - {{ $mapel->nama_mapel }}</title>
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
            border-bottom: 2px solid #16a34a;
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
            padding: 3px 0;
            font-size: 10px;
        }
        .info-label {
            font-weight: bold;
            color: #555;
            width: 110px;
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
            padding: 6px 4px;
            text-align: center;
        }
        .data-table td {
            border: 1px solid #e4e4e7;
            padding: 5px 4px;
            text-align: center;
        }
        .student-name {
            text-align: left !important;
            padding-left: 6px !important;
            font-weight: bold;
        }
        .final-grade {
            font-weight: bold;
            background-color: #f0fdf4;
            color: #166534;
        }
        .pred-a { color: #166534; font-weight: bold; }
        .pred-b { color: #1d4ed8; font-weight: bold; }
        .pred-c { color: #c2410c; font-weight: bold; }
        .pred-d { color: #a16207; font-weight: bold; }
        .pred-e { color: #b91c1c; font-weight: bold; }
        .legend {
            margin-top: 15px;
            font-size: 8px;
            color: #666;
        }
        .footer-table {
            width: 100%;
            margin-top: 25px;
        }
        .footer-table td {
            width: 50%;
            vertical-align: top;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="header-title">REKAP NILAI AKADEMIK SISWA</div>
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
            <td class="info-label" style="text-align: right; padding-right: 10px;">Mata Pelajaran</td>
            <td class="info-value">: {{ $mapel->nama_mapel }}</td>
        </tr>
        <tr>
            <td class="info-label">Semester</td>
            <td class="info-value">: {{ $semester->nama_semester }} (T.A. {{ $semester->tahunAjaran->nama ?? '-' }})</td>
            <td class="info-label" style="text-align: right; padding-right: 10px;">Wali Kelas</td>
            <td class="info-value">: {{ $kelas->guruUmum?->user?->nama ?? '-' }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 70px;">NIS</th>
                <th style="text-align: left; padding-left: 6px;">Nama Siswa</th>
                @foreach ($components as $comp)
                    <th style="width: 65px;">
                        {{ $comp->nama }}
                        <div style="font-size: 7px; color: #71717a; font-weight: normal;">{{ intval($comp->bobot) }}%</div>
                    </th>
                @endforeach
                <th style="width: 65px; background-color: #dcfce7; color: #166534;">Nilai Akhir</th>
                <th style="width: 45px;">Predikat</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($matrix as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-family: monospace;">{{ $row['siswa']->nis }}</td>
                    <td class="student-name">{{ $row['siswa']->user?->nama ?? '-' }}</td>
                    @foreach ($components as $comp)
                        @php
                            $val = $row['compGrades'][$comp->id];
                        @endphp
                        <td style="{{ is_null($val) ? 'color: #a1a1aa;' : 'font-weight: 600;' }}">
                            {{ is_null($val) ? '-' : $val }}
                        </td>
                    @endforeach
                    <td class="final-grade">{{ $row['finalGrade'] }}</td>
                    <td>
                        @php
                            $pClass = 'pred-e';
                            if ($row['predikat'] === 'A') $pClass = 'pred-a';
                            elseif ($row['predikat'] === 'B') $pClass = 'pred-b';
                            elseif ($row['predikat'] === 'C') $pClass = 'pred-c';
                            elseif ($row['predikat'] === 'D') $pClass = 'pred-d';
                        @endphp
                        <span class="{{ $pClass }}">{{ $row['predikat'] }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="legend">
        <strong>Rumus Nilai Akhir:</strong> Penjumlahan Rata-rata Nilai per Komponen x (Bobot Komponen / 100). |
        <strong>Predikat:</strong> A (>= 90), B (80-89), C (70-79), D (60-69), E (< 60).
    </div>

    <x-ttd-elektronik role="guru" docType="NIL" :docId="$kelas->id . '-' . $mapel->id" />

</body>
</html>
