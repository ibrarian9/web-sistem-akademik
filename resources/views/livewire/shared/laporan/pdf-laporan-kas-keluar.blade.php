<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Arus Kas Keluar</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #222;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 16px;
            margin: 0;
            color: #111;
        }
        .header p {
            margin: 2px 0 0 0;
            color: #555;
            font-size: 10px;
        }
        .title {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        .meta-info {
            margin-bottom: 10px;
        }
        .meta-info table {
            width: 100%;
        }
        .meta-info td {
            padding: 2px 0;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .report-table th {
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            padding: 5px;
            font-weight: bold;
            text-align: left;
            text-transform: uppercase;
            font-size: 9px;
        }
        .report-table td {
            border: 1px solid #ddd;
            padding: 5px;
        }
        .report-table tr:nth-child(even) {
            background-color: #fafafa;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-box {
            font-size: 11px;
            font-weight: bold;
            text-align: right;
            padding: 8px;
            background-color: #fef2f2;
            border: 1px solid #fca5a5;
            margin-bottom: 20px;
            color: #b91c1c;
        }
        .footer {
            margin-top: 30px;
            width: 100%;
        }
        .footer-table {
            width: 100%;
        }
        .footer-table td {
            text-align: center;
            width: 50%;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>YAYASAN PENDIDIKAN ISLAM</h1>
        <p>Jl. Kaliurang Km. 10, Sleman, D.I. Yogyakarta | Telp: (0274) 123456</p>
    </div>

    <div class="title">LAPORAN ARUS KAS KELUAR (PENGELUARAN)</div>

    <div class="meta-info">
        <table>
            <tr>
                <td style="width: 15%; font-weight: bold;">Kategori</td>
                <td style="width: 35%;">: {{ $kategori }}</td>
                <td style="width: 15%; font-weight: bold;">Tanggal Cetak</td>
                <td style="width: 35%;">: {{ date('d-m-Y H:i') }}</td>
            </tr>
        </table>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 20%;">Kategori</th>
                <th style="width: 40%;">Keterangan / Rincian</th>
                <th style="width: 20%; text-align: right;">Jumlah Pengeluaran</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row->tanggal ? date('d-m-Y', strtotime($row->tanggal)) : '-' }}</td>
                    <td>{{ $row->kategori->nama ?? '-' }}</td>
                    <td>{{ $row->keterangan ?? '-' }}</td>
                    <td class="text-right" style="color: #b91c1c; font-weight: bold;">Rp {{ number_format($row->jumlah, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 15px; color: #777;">
                        Tidak ada data arus kas keluar.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="total-box">
        TOTAL PENGELUARAN KAS: Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
    </div>

    <x-ttd-elektronik role="bendahara" docType="PEN" :docId="date('Ymd')" />
</body>
</html>
