<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $judul ?? 'Laporan Pengeluaran Keuangan' }}</title>
    <style>
        @page {
            margin: 1.5cm 1.5cm 2cm 1.5cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9px;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #065f46;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .school-name {
            font-size: 14px;
            font-weight: 800;
            color: #065f46;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .school-sub {
            font-size: 8px;
            color: #64748b;
            margin-top: 2px;
        }
        .doc-title {
            text-align: center;
            font-size: 12px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 12px;
            font-size: 8.5px;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 2px 4px;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .report-table th {
            background-color: #065f46;
            color: #ffffff;
            border: 1px solid #047857;
            padding: 6px 5px;
            font-weight: bold;
            text-align: left;
            text-transform: uppercase;
            font-size: 8px;
            letter-spacing: 0.3px;
        }
        .report-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 6px;
            font-size: 8.5px;
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
            font-size: 10px;
            font-weight: 800;
            text-align: right;
            padding: 8px 12px;
            background-color: #fff1f2;
            border: 1px solid #fecdd3;
            color: #be123c;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .catatan-box {
            background-color: #f0fdf4;
            border: 1px dashed #86efac;
            padding: 8px;
            border-radius: 4px;
            font-size: 8px;
            color: #166534;
            margin-bottom: 15px;
        }
        .footer-table {
            width: 100%;
            margin-top: 15px;
            page-break-inside: avoid;
        }
        .footer-table td {
            text-align: center;
            width: 50%;
            font-size: 8.5px;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 70%;">
                <div class="school-name">{{ $namaSekolah ?? 'PONDOK PESANTREN & SEKOLAH ISLAM TERPADU' }}</div>
                <div class="school-sub">{{ $alamatSekolah ?? 'Jl. Pendidikan Karakter Islami No. 123' }} | Telp: {{ $noTelepon ?? '(0274) 123456' }}</div>
            </td>
            <td style="width: 30%; text-align: right;">
                <span style="display: inline-block; padding: 3px 8px; background-color: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 7.5px; font-weight: bold; color: #475569;">
                    DOKUMEN KEUANGAN RESMI
                </span>
            </td>
        </tr>
    </table>

    <div class="doc-title">{{ $judul ?? 'LAPORAN PENGELUARAN KEUANGAN YAYASAN' }}</div>

    <table class="meta-table">
        <tr>
            <td style="width: 14%; font-weight: bold; color: #475569;">Periode:</td>
            <td style="width: 36%; font-weight: bold;">{{ $periodeText ?? ($startDate && $endDate ? \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') . ' s/d ' . \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') : 'Semua Periode') }}</td>
            <td style="width: 14%; font-weight: bold; color: #475569;">Filter Bulan:</td>
            <td style="width: 36%;">{{ $bulan ?: 'Semua Bulan' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; color: #475569;">Pos/Kategori:</td>
            <td style="font-weight: bold; color: #065f46;">{{ $kategori ?? 'Semua Kategori' }}</td>
            <td style="font-weight: bold; color: #475569;">Tanggal Cetak:</td>
            <td>{{ now()->translatedFormat('d F Y, H:i') }} WIB</td>
        </tr>
    </table>

    @if (!empty($catatan))
        <div class="catatan-box">
            <strong>Catatan / Keterangan:</strong> {{ $catatan }}
        </div>
    @endif

    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 4%; text-align: center;">No</th>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 20%;">Kategori / Pos</th>
                <th style="width: 40%;">Keterangan & Rincian Pengeluaran</th>
                <th style="width: 12%; text-align: center;">Petugas</th>
                <th style="width: 12%; text-align: right;">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row->tanggal ? \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d M Y') : '-' }}</td>
                    <td style="font-weight: bold; color: #0f172a;">{{ $row->kategori->nama ?? 'Operasional' }}</td>
                    <td>{{ $row->keterangan ?? '-' }}</td>
                    <td class="text-center" style="font-size: 8px; color: #64748b;">{{ $row->petugas->nama ?? '-' }}</td>
                    <td class="text-right" style="font-weight: bold; color: #b91c1c;">Rp {{ number_format($row->jumlah, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 15px; color: #94a3b8;">
                        Tidak ada transaksi pengeluaran yang tercatat pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="total-box">
        TOTAL PENGELUARAN KESELURUHAN: Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
    </div>

    @if (!empty($penandatangan))
        <table class="footer-table">
            <tr>
                <td></td>
                <td>
                    <div>{{ \App\Models\Pengaturan::getValue('kota', 'Pekanbaru') }}, {{ now()->translatedFormat('d F Y') }}</div>
                    <div style="margin-top: 3px; font-weight: bold;">{{ $jabatanPenandatangan ?? 'Bendahara Yayasan' }}</div>
                    <div style="height: 45px;"></div>
                    <div style="font-weight: bold; text-decoration: underline;">{{ $penandatangan }}</div>
                </td>
            </tr>
        </table>
    @else
        <x-ttd-elektronik role="bendahara" docType="PEN" :docId="date('Ymd')" />
    @endif
</body>
</html>
