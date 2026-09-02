<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekapitulasi Tata Kelola Dana BOS</title>
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
            font-size: 15px;
            margin: 0;
            color: #111;
            text-transform: uppercase;
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
            letter-spacing: 0.5px;
        }
        .meta-info {
            margin-bottom: 12px;
        }
        .meta-info table {
            width: 100%;
        }
        .meta-info td {
            padding: 2px 0;
            font-size: 9.5px;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .report-table th {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 6px 5px;
            font-weight: bold;
            text-align: left;
            text-transform: uppercase;
            font-size: 8.5px;
            color: #1e293b;
        }
        .report-table td {
            border: 1px solid #cbd5e1;
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
        .summary-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .summary-box {
            padding: 8px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            text-align: center;
        }
        .summary-title {
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
        }
        .summary-val {
            font-size: 12px;
            font-weight: 800;
            margin-top: 2px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $namaSekolah }}</h1>
        <p>{{ $alamatSekolah }} | Telp: {{ $noTelepon }}</p>
    </div>

    <div class="title">BUKU KAS REKAPITULASI DANA BANTUAN OPERASIONAL SEKOLAH (BOS)</div>

    <div class="meta-info">
        <table>
            <tr>
                <td style="width: 15%; font-weight: bold;">Periode</td>
                <td style="width: 35%;">: {{ $periodeText }}</td>
                <td style="width: 15%; font-weight: bold;">Tahun Ajaran</td>
                <td style="width: 35%;">: {{ $tahunAjaran }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Jenis Mutasi</td>
                <td>: {{ $jenisText }}</td>
                <td style="font-weight: bold;">Tanggal Cetak</td>
                <td>: {{ date('d-m-Y H:i') }} WIB</td>
            </tr>
        </table>
    </div>

    <!-- Summary Box -->
    <table class="summary-grid">
        <tr>
            <td style="width: 33.3%; padding-right: 5px;">
                <div class="summary-box" style="background-color: #f0fdf4; border-color: #bbf7d0;">
                    <div class="summary-title" style="color: #166534;">Total Penerimaan BOS</div>
                    <div class="summary-val" style="color: #15803d;">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</div>
                </div>
            </td>
            <td style="width: 33.3%; padding: 0 5px;">
                <div class="summary-box" style="background-color: #fff1f2; border-color: #fecdd3;">
                    <div class="summary-title" style="color: #9f1239;">Total Belanja BOS</div>
                    <div class="summary-val" style="color: #be123c;">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</div>
                </div>
            </td>
            <td style="width: 33.3%; padding-left: 5px;">
                <div class="summary-box" style="background-color: #f0f9ff; border-color: #bae6fd;">
                    <div class="summary-title" style="color: #075985;">Sisa Saldo Kas BOS</div>
                    <div class="summary-val" style="color: #0284c7;">Rp {{ number_format($saldoBos, 0, ',', '.') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 12%; text-align: center;">Tanggal</th>
                <th style="width: 10%; text-align: center;">Jenis</th>
                <th style="width: 20%;">Kategori</th>
                <th style="width: 27%;">Uraian / Keterangan</th>
                <th style="width: 13%; text-align: right;">Penerimaan (Rp)</th>
                <th style="width: 13%; text-align: right;">Belanja (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $row->tanggal ? $row->tanggal->format('d/m/Y') : '-' }}</td>
                    <td class="text-center">
                        @if ($row->jenis === 'masuk')
                            <span style="color: #15803d; font-weight: bold;">Penerimaan</span>
                        @else
                            <span style="color: #be123c; font-weight: bold;">Belanja</span>
                        @endif
                    </td>
                    <td><strong>{{ $row->kategori }}</strong></td>
                    <td>{{ $row->keterangan }}</td>
                    <td class="text-right">
                        @if ($row->jenis === 'masuk')
                            <strong style="color: #15803d;">Rp {{ number_format($row->nominal, 0, ',', '.') }}</strong>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">
                        @if ($row->jenis === 'keluar')
                            <strong style="color: #be123c;">Rp {{ number_format($row->nominal, 0, ',', '.') }}</strong>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 15px; color: #777;">
                        Tidak ada catatan transaksi dana BOS yang ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f8fafc; font-weight: bold;">
                <td colspan="5" class="text-right" style="padding: 6px;">TOTAL KESELURUHAN:</td>
                <td class="text-right" style="color: #15803d;">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #be123c;">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <x-ttd-elektronik role="bendahara" docType="BOS" :docId="date('Ymd')" />
</body>
</html>
