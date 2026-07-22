<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pemasukan Keuangan</title>
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
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
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

    <div class="title">LAPORAN PEMASUKAN KEUANGAN YAYASAN</div>

    <div class="meta-info">
        <table>
            <tr>
                <td style="width: 15%; font-weight: bold;">Mulai Tanggal</td>
                <td style="width: 35%;">: {{ date('d-m-Y', strtotime($startDate)) }}</td>
                <td style="width: 15%; font-weight: bold;">Sampai Tanggal</td>
                <td style="width: 35%;">: {{ date('d-m-Y', strtotime($endDate)) }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Jenis Tagihan</td>
                <td>: {{ $jenisTagihan }}</td>
                <td style="font-weight: bold;">Metode Bayar</td>
                <td>: {{ $metodeBayar }}</td>
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
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 15%;">Tanggal Bayar</th>
                <th style="width: 25%;">Nama Siswa</th>
                <th style="width: 10%;">Kelas</th>
                <th style="width: 20%;">Jenis Tagihan / Periode</th>
                <th style="width: 10%; text-align: center;">Metode</th>
                <th style="width: 15%; text-align: right;">Jumlah Pemasukan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row->tanggal_bayar ? $row->tanggal_bayar->format('d-m-Y') : '-' }}</td>
                    <td>{{ $row->tagihan->siswa->user->nama ?? '-' }}</td>
                    <td>{{ $row->tagihan->siswa->kelas->nama_kelas ?? '-' }}</td>
                    <td>
                        {{ $row->tagihan->jenisTagihan->nama ?? '-' }} 
                        @if($row->tagihan->bulan)
                            ({{ $row->tagihan->bulan }})
                        @endif
                    </td>
                    <td class="text-center">{{ $row->metode_bayar }}</td>
                    <td class="text-right">Rp {{ number_format($row->nominal_dibayar, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 15px; color: #777;">
                        Tidak ada data pemasukan keuangan yang ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="total-box" style="color: #166534;">
        TOTAL PEMASUKAN KESELURUHAN: Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
    </div>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <x-ttd-elektronik 
                        role="bendahara" 
                        docType="PEM" 
                        :docId="date('Ymd')" 
                        title="Bendahara Keuangan Yayasan" 
                        :showLocation="false" 
                    />
                </td>
                <td style="width: 50%; vertical-align: top;">
                    <x-ttd-elektronik 
                        role="kepala_sekolah" 
                        docType="PEM" 
                        :docId="date('Ymd')" 
                        title="Kepala Sekolah / Madrasah" 
                    />
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
