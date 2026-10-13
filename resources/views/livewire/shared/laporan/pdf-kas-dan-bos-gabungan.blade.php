<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Jurnal Kas & Dana BOS Gabungan</title>
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
        .summary-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            border-radius: 4px;
            text-align: center;
        }
        .summary-label {
            font-size: 7.5px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
        }
        .summary-val {
            font-size: 10px;
            font-weight: bold;
            margin-top: 2px;
        }
        .data-table {
            border: 1px solid #cbd5e1;
            margin-bottom: 12px;
        }
        .data-table th {
            background-color: #065f46;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 7.5px;
            padding: 4px;
            border: 1px solid #047857;
            text-align: left;
        }
        .data-table td {
            padding: 4px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
            font-size: 7.5px;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-emerald {
            color: #047857;
            font-weight: bold;
        }
        .text-rose {
            color: #b91c1c;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
        }
        .badge-bos {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-kas {
            background-color: #e0e7ff;
            color: #3730a3;
        }
        .badge-masuk {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-keluar {
            background-color: #ffe4e6;
            color: #9f1239;
        }
        .footer-table {
            margin-top: 15px;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>

    <!-- Header Instansi -->
    <table class="header-table">
        <tr>
            <td style="width: 70%;">
                <div class="school-name">{{ $schoolHeader['namaSekolah'] }}</div>
                <div class="school-sub">{{ $schoolHeader['alamatSekolah'] }} | Telp: {{ $schoolHeader['noTelepon'] }}</div>
            </td>
            <td style="width: 30%; text-align: right;">
                <div style="font-size: 8px; color: #64748b;">KONSOLIDASI ARUS KAS & BOS</div>
                <div style="font-size: 7.5px; color: #94a3b8;">Dicetak: {{ $printedAt }}</div>
            </td>
        </tr>
    </table>

    <div class="doc-title">JURNAL PEMBUKUAN KAS OPERASIONAL & DANA BOS GABUNGAN</div>

    <!-- Info Filter & Metadata -->
    <table class="meta-table">
        <tr>
            <td style="width: 15%; font-weight: bold;">Periode:</td>
            <td style="width: 35%;">{{ $periodeText }}</td>
            <td style="width: 15%; font-weight: bold;">Sumber Dana:</td>
            <td style="width: 35%;">{{ $sumberText }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Arah Aliran:</td>
            <td>{{ $jenisText }}</td>
            <td style="font-weight: bold;">Kategori:</td>
            <td>{{ $kategoriText }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Petugas Cetak:</td>
            <td colspan="3">{{ $printedBy }}</td>
        </tr>
    </table>

    <!-- Ringkasan Eksekutif -->
    <table style="margin-bottom: 10px;">
        <tr>
            <td style="width: 25%; padding-right: 4px;">
                <div class="summary-box">
                    <div class="summary-label">Total Pemasukan</div>
                    <div class="summary-val text-emerald">Rp {{ number_format($metrics['total_masuk'], 0, ',', '.') }}</div>
                    <div style="font-size: 6.5px; color: #64748b; margin-top: 1px;">Kas: {{ number_format($metrics['masuk_kas'], 0, ',', '.') }} | BOS: {{ number_format($metrics['masuk_bos'], 0, ',', '.') }}</div>
                </div>
            </td>
            <td style="width: 25%; padding-right: 4px; padding-left: 4px;">
                <div class="summary-box">
                    <div class="summary-label">Total Pengeluaran</div>
                    <div class="summary-val text-rose">Rp {{ number_format($metrics['total_keluar'], 0, ',', '.') }}</div>
                    <div style="font-size: 6.5px; color: #64748b; margin-top: 1px;">Kas: {{ number_format($metrics['keluar_kas'], 0, ',', '.') }} | BOS: {{ number_format($metrics['keluar_bos'], 0, ',', '.') }}</div>
                </div>
            </td>
            <td style="width: 25%; padding-right: 4px; padding-left: 4px;">
                <div class="summary-box">
                    <div class="summary-label">Saldo Bersih (Net)</div>
                    <div class="summary-val {{ $metrics['net_balance'] >= 0 ? 'text-emerald' : 'text-rose' }}">
                        Rp {{ number_format($metrics['net_balance'], 0, ',', '.') }}
                    </div>
                    <div style="font-size: 6.5px; color: #64748b; margin-top: 1px;">{{ $metrics['net_balance'] >= 0 ? 'SURPLUS LIKUIDITAS' : 'DEFISIT LIKUIDITAS' }}</div>
                </div>
            </td>
            <td style="width: 25%; padding-left: 4px;">
                <div class="summary-box">
                    <div class="summary-label">Total Transaksi</div>
                    <div class="summary-val" style="color: #0f172a;">{{ $metrics['count_total'] }} Transaksi</div>
                    <div style="font-size: 6.5px; color: #64748b; margin-top: 1px;">{{ $metrics['count_masuk'] }} Masuk | {{ $metrics['count_keluar'] }} Keluar</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Tabel Data Transaksi -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 6%;">No</th>
                <th style="width: 10%;">Tanggal</th>
                <th style="width: 11%;">Sumber Dana</th>
                <th style="width: 8%;" class="text-center">Jenis</th>
                <th style="width: 15%;">Kategori</th>
                <th style="width: 26%;">Uraian Transaksi</th>
                <th style="width: 12%;" class="text-right">Masuk (Rp)</th>
                <th style="width: 12%;" class="text-right">Keluar (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $idx => $t)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>{{ $t->tanggal ? $t->tanggal->format('d/m/Y') : '-' }}</td>
                    <td>
                        @if ($t->sumber === 'dana_bos')
                            <span class="badge badge-bos">Dana BOS</span>
                        @else
                            <span class="badge badge-kas">Kas Sekolah</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if ($t->type === 'masuk')
                            <span class="badge badge-masuk">Masuk</span>
                        @else
                            <span class="badge badge-keluar">Keluar</span>
                        @endif
                    </td>
                    <td><b>{{ $t->kategori }}</b></td>
                    <td>
                        {{ $t->keterangan }}
                        @if ($t->no_resi)
                            <div style="font-size: 6.5px; color: #64748b;">Resi: {{ $t->no_resi }}</div>
                        @endif
                    </td>
                    <td class="text-right {{ $t->nominal_masuk > 0 ? 'text-emerald' : '' }}">
                        {{ $t->nominal_masuk > 0 ? number_format($t->nominal_masuk, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-right {{ $t->nominal_keluar > 0 ? 'text-rose' : '' }}">
                        {{ $t->nominal_keluar > 0 ? number_format($t->nominal_keluar, 0, ',', '.') : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 10px; color: #64748b;">
                        Tidak ada transaksi pada filter yang dipilih.
                    </td>
                </tr>
            @endforelse
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="6" class="text-right" style="padding: 5px;">TOTAL PERIODE INI:</td>
                <td class="text-right text-emerald" style="padding: 5px;">
                    Rp {{ number_format($metrics['total_masuk'], 0, ',', '.') }}
                </td>
                <td class="text-right text-rose" style="padding: 5px;">
                    Rp {{ number_format($metrics['total_keluar'], 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Tanda Tangan Footer -->
    <table class="footer-table">
        <tr>
            <td style="width: 33%; text-align: center;">
                <div>Mengetahui,</div>
                <div style="font-weight: bold; margin-top: 2px;">Kepala Sekolah</div>
                <div style="height: 35px;"></div>
                <div style="font-weight: bold; text-decoration: underline;">( ......................................... )</div>
                <div style="font-size: 7px; color: #64748b;">NIP. .....................................</div>
            </td>
            <td style="width: 34%;"></td>
            <td style="width: 33%; text-align: center;">
                <div>Pekanbaru, {{ date('d F Y') }}</div>
                <div style="font-weight: bold; margin-top: 2px;">Bendahara / Staf Keuangan</div>
                <div style="height: 35px;"></div>
                <div style="font-weight: bold; text-decoration: underline;">{{ $printedBy }}</div>
                <div style="font-size: 7px; color: #64748b;">Bagian Keuangan & Pengelolaan Dana</div>
            </td>
        </tr>
    </table>

</body>
</html>
