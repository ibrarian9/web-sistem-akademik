<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Slip Gaji - {{ $gaji->guru->user->nama ?? 'Guru' }} - {{ $gaji->bulan }} {{ $gaji->tahun }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 20mm 15mm 20mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .doc-title-container {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 16px;
        }
        .doc-title {
            font-size: 14pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0f172a;
            margin: 0;
        }
        .doc-subtitle {
            font-size: 9.5pt;
            font-weight: 700;
            color: #047857;
            margin-top: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-grid-table {
            width: 100%;
            margin-bottom: 14px;
            border-collapse: collapse;
            font-size: 9.5pt;
        }
        .info-grid-table td {
            padding: 3px 2px;
            vertical-align: top;
        }
        .info-grid-table td.lbl {
            width: 18%;
            font-weight: 700;
            color: #475569;
        }
        .info-grid-table td.val {
            width: 32%;
            font-weight: 600;
            color: #0f172a;
        }
        .salary-breakdown-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 9.5pt;
        }
        .salary-breakdown-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 9pt;
            padding: 7px 10px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }
        .salary-breakdown-table td {
            padding: 6px 10px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .salary-breakdown-table td.num {
            text-align: right;
            font-family: 'Courier New', Courier, monospace;
            font-weight: 700;
            font-size: 10pt;
        }
        .subtotal-row td {
            background-color: #f8fafc;
            font-weight: 800;
            border-top: 1.5px solid #94a3b8;
        }
        .take-home-box {
            background-color: #ecfdf5;
            border: 1.5px solid #10b981;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 12px;
        }
        .take-home-table {
            width: 100%;
            border-collapse: collapse;
        }
        .take-home-table td {
            vertical-align: middle;
        }
        .take-home-lbl {
            font-size: 10.5pt;
            font-weight: 800;
            color: #065f46;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .take-home-val {
            text-align: right;
            font-size: 14pt;
            font-weight: 900;
            color: #047857;
            font-family: 'Courier New', Courier, monospace;
        }
        .terbilang-box {
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 5px;
            padding: 6px 10px;
            font-size: 8.5pt;
            font-style: italic;
            color: #334155;
            margin-bottom: 18px;
        }
        .terbilang-box strong {
            font-style: normal;
            color: #0f172a;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: 800;
            text-transform: uppercase;
        }
        .status-paid {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }
        .status-draft {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }
    </style>
</head>
<body>
    <!-- Judul Dokumen Tanpa Kop -->
    <div class="doc-title-container">
        <h1 class="doc-title">SLIP GAJI & PENGHASILAN GURU</h1>
        <div class="doc-subtitle">PERIODE: {{ strtoupper($gaji->bulan) }} {{ $gaji->tahun }}</div>
    </div>

    <!-- Informasi Biodata Guru -->
    <table class="info-grid-table">
        <tr>
            <td class="lbl">Nama Guru / GTK</td>
            <td class="val">: {{ $gaji->guru->user->nama ?? '-' }}</td>
            <td class="lbl">Bulan / Tahun</td>
            <td class="val">: {{ $gaji->bulan }} {{ $gaji->tahun }}</td>
        </tr>
        <tr>
            <td class="lbl">NIY / NIP</td>
            <td class="val">: {{ $gaji->guru->niy ?? ($gaji->guru->nip ?? '-') }}</td>
            <td class="lbl">Tanggal Pembayaran</td>
            <td class="val">: {{ $gaji->tanggal_bayar ? $gaji->tanggal_bayar->format('d/m/Y') : date('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="lbl">Jabatan / Bidang</td>
            <td class="val">: Guru {{ ucfirst($gaji->guru->jenis_guru ?? 'Umum') }}</td>
            <td class="lbl">Status Pembayaran</td>
            <td class="val">: 
                @if ($gaji->status === 'dibayar')
                    <span class="status-badge status-paid">LUNAS / DIBAYAR</span>
                @else
                    <span class="status-badge status-draft">DRAF BELUM DIBAYAR</span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="lbl">Status Kepegawaian</td>
            <td class="val">: {{ ucwords(str_replace('_', ' ', $gaji->guru->status_kepegawaian ?? 'Honorer')) }}</td>
            <td class="lbl">Pendidikan Terakhir</td>
            <td class="val">: {{ $gaji->guru->pendidikan ?? '-' }}</td>
        </tr>
    </table>

    <!-- Rincian Penghasilan & Potongan -->
    @php
        $totalPenerimaan = floatval($gaji->gaji_pokok) + floatval($gaji->insentif_bpjs) + floatval($gaji->insentif_maghrib_mengaji);
        $totalPotongan = floatval($gaji->potongan_peminjaman) + floatval($gaji->potongan_lainnya);
    @endphp

    <table class="salary-breakdown-table">
        <thead>
            <tr>
                <th style="width: 50%;">A. PENERIMAAN (EARNINGS)</th>
                <th style="width: 50%;">B. POTONGAN (DEDUCTIONS)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 0;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="border: none; padding: 5px 8px;">1. Gaji Pokok</td>
                            <td style="border: none; padding: 5px 8px; text-align: right;" class="num">Rp {{ number_format($gaji->gaji_pokok, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td style="border: none; padding: 5px 8px;">2. Insentif BPJS Kesehatan & TK</td>
                            <td style="border: none; padding: 5px 8px; text-align: right;" class="num">Rp {{ number_format($gaji->insentif_bpjs, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td style="border: none; padding: 5px 8px;">3. Insentif Mengajar / Maghrib Mengaji</td>
                            <td style="border: none; padding: 5px 8px; text-align: right;" class="num">Rp {{ number_format($gaji->insentif_maghrib_mengaji, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </td>
                <td style="padding: 0;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="border: none; padding: 5px 8px;">1. Angsuran Pinjaman / Kasbon</td>
                            <td style="border: none; padding: 5px 8px; text-align: right; color: #dc2626;" class="num">Rp {{ number_format($gaji->potongan_peminjaman, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td style="border: none; padding: 5px 8px;">2. Potongan Lain-lain</td>
                            <td style="border: none; padding: 5px 8px; text-align: right; color: #dc2626;" class="num">Rp {{ number_format($gaji->potongan_lainnya, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td style="border: none; padding: 5px 8px; color: #94a3b8;">-</td>
                            <td style="border: none; padding: 5px 8px; text-align: right; color: #94a3b8;" class="num">-</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="subtotal-row">
                <td style="padding: 6px 8px;">
                    <div style="display: flex; justify-content: space-between;">
                        <span>Total Penerimaan Bruto:</span>
                        <span class="num" style="float: right; color: #047857;">Rp {{ number_format($totalPenerimaan, 0, ',', '.') }}</span>
                    </div>
                </td>
                <td style="padding: 6px 8px;">
                    <div style="display: flex; justify-content: space-between;">
                        <span>Total Potongan:</span>
                        <span class="num" style="float: right; color: #dc2626;">Rp {{ number_format($totalPotongan, 0, ',', '.') }}</span>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Total Gaji Bersih Box -->
    <div class="take-home-box">
        <table class="take-home-table">
            <tr>
                <td class="take-home-lbl">TOTAL DITERIMA (TAKE HOME PAY):</td>
                <td class="take-home-val">Rp {{ number_format($gaji->total_diterima, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <!-- Terbilang -->
    <div class="terbilang-box">
        <strong>Terbilang:</strong> <em># {{ $terbilang ?? '-' }} #</em>
    </div>

    <!-- Tanda Tangan & QR Code Verifikasi -->
    <x-ttd-elektronik role="bendahara" docType="SLI" :docId="$gaji->id" />
</body>
</html>
