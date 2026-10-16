<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Tagihan Kasir Pembayaran Siswa</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #1c1917;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1c1917;
            padding-bottom: 8px;
            margin-bottom: 14px;
        }
        .header h1 {
            font-size: 16px;
            margin: 0;
            color: #0c0a09;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header p {
            margin: 3px 0 0 0;
            color: #44403c;
            font-size: 10px;
        }
        .title {
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 12px;
            text-transform: uppercase;
            color: #0f172a;
            letter-spacing: 0.3px;
        }
        .meta-info {
            margin-bottom: 12px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 6px 10px;
        }
        .meta-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-info td {
            padding: 2px 4px;
            font-size: 9.5px;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .report-table th {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 6px 5px;
            font-weight: bold;
            text-align: left;
            text-transform: uppercase;
            font-size: 8.5px;
            color: #334155;
        }
        .report-table td {
            border: 1px solid #e2e8f0;
            padding: 5px;
            font-size: 9px;
        }
        .report-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-box {
            font-size: 10.5px;
            margin-bottom: 20px;
            background-color: #fff7ed;
            border: 1px solid #fdba74;
            border-radius: 4px;
            padding: 8px 12px;
        }
        .total-table {
            width: 100%;
            border-collapse: collapse;
        }
        .total-table td {
            padding: 2px 4px;
        }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            font-size: 8px;
            font-weight: bold;
            border-radius: 3px;
            text-transform: uppercase;
        }
        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $namaSekolah ?? 'PONDOK PESANTREN & SEKOLAH ISLAM TERPADU' }}</h1>
        <p>{{ $alamatSekolah ?? 'Jl. Pendidikan Karakter Islami No. 123' }} | Telp/WA: {{ $noTelepon ?? '(0274) 123456' }}</p>
    </div>

    <div class="title">DAFTAR TAGIHAN PEMBAYARAN SISWA (KASIR)</div>

    <div class="meta-info">
        <table>
            <tr>
                <td style="width: 15%; font-weight: bold; color: #475569;">Kelas</td>
                <td style="width: 35%;">: {{ $kelas }}</td>
                <td style="width: 18%; font-weight: bold; color: #475569;">Jenis Tagihan</td>
                <td style="width: 32%;">: {{ $jenisTagihan }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; color: #475569;">Bulan / Periode</td>
                <td>: {{ $periodeText }}</td>
                <td style="font-weight: bold; color: #475569;">Filter Waktu</td>
                <td>: {{ $waktuText }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; color: #475569;">Kata Kunci</td>
                <td>: {{ $search !== '' ? $search : 'Semua Data' }}</td>
                <td style="font-weight: bold; color: #475569;">Tanggal Cetak</td>
                <td>: {{ date('d/m/Y H:i') }} WIB</td>
            </tr>
        </table>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 4%;" class="text-center">No</th>
                <th style="width: 9%;" class="text-center">NIS</th>
                <th style="width: 22%;">Nama Siswa</th>
                <th style="width: 8%;" class="text-center">Kelas</th>
                <th style="width: 14%;">Jenis Tagihan</th>
                <th style="width: 9%;" class="text-center">Periode</th>
                <th style="width: 9%;" class="text-center">Jatuh Tempo</th>
                <th style="width: 8%;" class="text-right">Nominal</th>
                <th style="width: 8%;" class="text-right">Dibayar</th>
                <th style="width: 9%;" class="text-right">Sisa Tagihan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $index => $row)
                @php
                    $sisa = max(0, floatval($row->nominal) - floatval($row->total_dibayar));
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $row->siswa->nis ?? '-' }}</td>
                    <td>{{ $row->siswa->user->nama ?? '-' }}</td>
                    <td class="text-center">{{ $row->siswa->kelas->nama_kelas ?? '-' }}</td>
                    <td>{{ $row->jenisTagihan->nama ?? '-' }}</td>
                    <td class="text-center">{{ $row->bulan ?: 'Non-Bulan' }}</td>
                    <td class="text-center">{{ $row->jatuh_tempo ? date('d/m/Y', strtotime($row->jatuh_tempo)) : '-' }}</td>
                    <td class="text-right">Rp {{ number_format($row->nominal, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($row->total_dibayar, 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #b91c1c; font-weight: bold;">Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 18px; color: #64748b;">
                        Tidak ada data tagihan yang sesuai dengan filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="total-box">
        <table class="total-table">
            <tr>
                <td style="width: 33%; font-weight: bold; color: #431407;">
                    Total Tagihan: Rp {{ number_format($totalNominal, 0, ',', '.') }}
                </td>
                <td style="width: 33%; font-weight: bold; color: #166534; text-align: center;">
                    Total Terbayar: Rp {{ number_format($totalDibayar, 0, ',', '.') }}
                </td>
                <td style="width: 34%; font-weight: bold; color: #991b1b; text-align: right;">
                    Total Sisa Tagihan: Rp {{ number_format($totalSisa, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

    <x-ttd-elektronik role="bendahara" docType="TAG" :docId="date('Ymd')" />
</body>
</html>
