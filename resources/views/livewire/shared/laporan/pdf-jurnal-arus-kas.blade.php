<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Jurnal Arus Kas (Cash Flow)</title>
    <style>
        @page {
            margin: 1cm 1.2cm 1.5cm 1.2cm;
        }
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 8px;
            color: #1e293b;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        tr {
            page-break-inside: avoid;
        }
        .header-table {
            border-bottom: 2px solid #065f46;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }
        .school-name {
            font-size: 13px;
            font-weight: bold;
            color: #065f46;
            text-transform: uppercase;
        }
        .school-sub {
            font-size: 8px;
            color: #64748b;
            margin-top: 2px;
        }
        .doc-title {
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .meta-table {
            margin-bottom: 8px;
            font-size: 8px;
        }
        .meta-table td {
            padding: 2px;
        }
        .summary-grid {
            margin-bottom: 10px;
        }
        .summary-box {
            padding: 5px 8px;
            border: 1px solid #cbd5e1;
            text-align: center;
        }
        .summary-title {
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
        }
        .summary-val {
            font-size: 10px;
            font-weight: bold;
            margin-top: 2px;
        }
        .report-table th {
            background-color: #065f46;
            color: #ffffff;
            border: 1px solid #047857;
            padding: 4px;
            font-weight: bold;
            text-align: left;
            text-transform: uppercase;
            font-size: 7.5px;
        }
        .report-table td {
            border: 1px solid #cbd5e1;
            padding: 3.5px 4px;
            font-size: 7.5px;
            word-wrap: break-word;
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
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 75%;">
                <div class="school-name">{{ $namaSekolah ?? 'PONDOK PESANTREN & SEKOLAH ISLAM TERPADU' }}</div>
                <div class="school-sub">{{ $alamatSekolah ?? 'Jl. Pendidikan Karakter Islami, Pekanbaru' }} | Telp: {{ $noTelepon ?? '(0761) 123456' }}</div>
            </td>
            <td style="width: 25%; text-align: right;">
                <div style="padding: 2px 6px; background-color: #f1f5f9; border: 1px solid #cbd5e1; font-size: 7px; font-weight: bold; color: #475569; text-align: center;">
                    JURNAL KAS RESMI
                </div>
            </td>
        </tr>
    </table>

    <div class="doc-title">BUKU JURNAL ARUS KAS (CASH FLOW) TERPADU</div>

    <table class="meta-table">
        <tr>
            <td style="width: 15%; font-weight: bold; color: #475569;">Periode Mutasi:</td>
            <td style="width: 35%; font-weight: bold;">{{ $periodeText }}</td>
            <td style="width: 15%; font-weight: bold; color: #475569;">Filter Tab:</td>
            <td style="width: 35%; font-weight: bold; text-transform: uppercase;">{{ $tabText }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; color: #475569;">Filter Stream:</td>
            <td style="font-weight: bold; color: #065f46;">{{ $streamText }}</td>
            <td style="font-weight: bold; color: #475569;">Tanggal Cetak:</td>
            <td>{{ now()->translatedFormat('d F Y, H:i') }} WIB</td>
        </tr>
    </table>

    <!-- Summary Box -->
    <table class="summary-grid">
        <tr>
            <td style="width: 33.3%; padding-right: 3px;">
                <div class="summary-box" style="background-color: #f0fdf4; border-color: #bbf7d0;">
                    <div class="summary-title" style="color: #166534;">Total Kas Masuk (+)</div>
                    <div class="summary-val" style="color: #15803d;">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</div>
                </div>
            </td>
            <td style="width: 33.3%; padding: 0 3px;">
                <div class="summary-box" style="background-color: #fff1f2; border-color: #fecdd3;">
                    <div class="summary-title" style="color: #9f1239;">Total Kas Keluar (-)</div>
                    <div class="summary-val" style="color: #be123c;">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</div>
                </div>
            </td>
            <td style="width: 33.3%; padding-left: 3px;">
                <div class="summary-box" style="background-color: #f0f9ff; border-color: #bae6fd;">
                    <div class="summary-title" style="color: #075985;">Surplus / Saldo Bersih</div>
                    <div class="summary-val" style="color: {{ $netBalance >= 0 ? '#0284c7' : '#be123c' }};">
                        {{ $netBalance < 0 ? '- Rp ' : 'Rp ' }}{{ number_format(abs($netBalance), 0, ',', '.') }}
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 4%; text-align: center;">No</th>
                <th style="width: 10%;">Tanggal</th>
                <th style="width: 7%; text-align: center;">Tipe</th>
                <th style="width: 14%;">Stream / Sumber</th>
                <th style="width: 14%;">Kategori / Pos</th>
                <th style="width: 12%; text-align: right;">Kas Masuk (Rp)</th>
                <th style="width: 12%; text-align: right;">Kas Keluar (Rp)</th>
                <th style="width: 17%;">Keterangan & Rincian</th>
                <th style="width: 10%;">Metode / Resi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <div>{{ $row->tanggal ? \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d M Y') : '-' }}</div>
                    </td>
                    <td class="text-center" style="font-weight: bold; color: {{ $row->type === 'masuk' ? '#166534' : '#b91c1c' }};">
                        {{ strtoupper($row->type) }}
                    </td>
                    <td style="font-weight: bold;">{{ $row->stream_label }}</td>
                    <td>{{ $row->kategori }}</td>
                    <td class="text-right" style="font-weight: bold; color: #166534;">
                        {{ $row->nominal_masuk > 0 ? '+ Rp ' . number_format($row->nominal_masuk, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-right" style="font-weight: bold; color: #b91c1c;">
                        {{ $row->nominal_keluar > 0 ? '- Rp ' . number_format($row->nominal_keluar, 0, ',', '.') : '-' }}
                    </td>
                    <td>{{ $row->keterangan }}</td>
                    <td style="font-size: 7px;">
                        <div>{{ $row->metode_resi }}</div>
                        @if ($row->no_resi)
                            <div style="font-family: monospace; color: #64748b;">{{ $row->no_resi }}</div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 12px; color: #94a3b8;">
                        Tidak ada transaksi arus kas yang tercatat pada filter ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f8fafc; font-weight: bold;">
                <td colspan="5" class="text-right" style="padding: 4px;">TOTAL MUTASI KAS:</td>
                <td class="text-right" style="color: #15803d;">+ Rp {{ number_format($totalMasuk, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #be123c;">- Rp {{ number_format($totalKeluar, 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <x-ttd-elektronik role="bendahara" docType="KAS" :docId="date('Ymd')" />
</body>
</html>
