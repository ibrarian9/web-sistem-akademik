<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Tunggakan Pembayaran Siswa</title>
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
        .signature-space {
            height: 50px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>YAYASAN PENDIDIKAN ISLAM</h1>
        <p>Jl. Kaliurang Km. 10, Sleman, D.I. Yogyakarta | Telp: (0274) 123456</p>
    </div>

    <div class="title">LAPORAN TUNGGAKAN PEMBAYARAN SISWA</div>

    <div class="meta-info">
        <table>
            <tr>
                <td style="width: 12%; font-weight: bold;">Kelas</td>
                <td style="width: 38%;">: {{ $kelas }}</td>
                <td style="width: 15%; font-weight: bold;">Tahun Ajaran</td>
                <td style="width: 35%;">: {{ $tahunAjaran }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Tanggal Cetak</td>
                <td>: {{ date('d-m-Y H:i') }}</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Nama Siswa</th>
                <th style="width: 8%;">Kelas</th>
                <th style="width: 15%;">Jenis Tagihan</th>
                <th style="width: 10%; text-align: center;">Bulan</th>
                <th style="width: 12%; text-align: right;">Nominal</th>
                <th style="width: 12%; text-align: right;">Dibayar</th>
                <th style="width: 13%; text-align: right;">Sisa Tunggakan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row->siswa->user->nama ?? '-' }}</td>
                    <td>{{ $row->siswa->kelas->nama_kelas ?? '-' }}</td>
                    <td>{{ $row->jenisTagihan->nama ?? '-' }}</td>
                    <td class="text-center">{{ $row->bulan ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($row->nominal, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($row->total_dibayar, 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #b91c1c; font-weight: bold;">Rp {{ number_format($row->nominal - $row->total_dibayar, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 15px; color: #777;">
                        Tidak ada data tunggakan pembayaran siswa.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="total-box">
        TOTAL TUNGGAKAN KESELURUHAN: Rp {{ number_format($totalTunggakan, 0, ',', '.') }}
    </div>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td>
                    &nbsp;
                </td>
                <td>
                    Sleman, {{ date('d-m-Y') }}<br>
                    Bendahara Yayasan,
                    <div class="signature-space"></div>
                    <strong>Siti Aminah, S.E.</strong>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
