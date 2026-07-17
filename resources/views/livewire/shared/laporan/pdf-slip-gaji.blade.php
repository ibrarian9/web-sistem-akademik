<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Slip Gaji - {{ $gaji->guru->user->nama ?? '-' }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #222;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
            color: #111;
        }
        .header p {
            margin: 3px 0 0 0;
            color: #555;
            font-size: 11px;
        }
        .title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .info-table td.label {
            width: 15%;
            font-weight: bold;
            color: #555;
        }
        .info-table td.value {
            width: 35%;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            background-color: #f5f5f5;
            padding: 5px 8px;
            margin-bottom: 10px;
            border-left: 3px solid #16a34a;
        }
        .details-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .details-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #eee;
        }
        .details-table td.amount {
            text-align: right;
            font-family: monospace;
            font-size: 13px;
        }
        .total-box {
            margin-top: 20px;
            padding: 10px;
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            text-align: right;
        }
        .total-box .label {
            font-size: 12px;
            font-weight: bold;
            color: #166534;
        }
        .total-box .amount {
            font-size: 16px;
            font-weight: bold;
            color: #15803d;
            font-family: monospace;
        }
        .footer {
            margin-top: 50px;
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
            height: 60px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>YAYASAN PENDIDIKAN ISLAM</h1>
        <p>Jl. Kaliurang Km. 10, Sleman, D.I. Yogyakarta | Telp: (0274) 123456</p>
    </div>

    <div class="title">SLIP GAJI GURU</div>

    <table class="info-table">
        <tr>
            <td class="label">Nama Guru</td>
            <td class="value">: {{ $gaji->guru->user->nama ?? '-' }}</td>
            <td class="label">Bulan</td>
            <td class="value">: {{ $gaji->bulan }}</td>
        </tr>
        <tr>
            <td class="label">NIP</td>
            <td class="value">: {{ $gaji->guru->nip ?? '-' }}</td>
            <td class="label">Tahun</td>
            <td class="value">: {{ $gaji->tahun }}</td>
        </tr>
        <tr>
            <td class="label">Jabatan</td>
            <td class="value">: Guru {{ ucfirst($gaji->guru->jenis_guru ?? '-') }}</td>
            <td class="label">Tanggal</td>
            <td class="value">: {{ $gaji->tanggal_bayar ? $gaji->tanggal_bayar->format('d-m-Y') : date('d-m-Y') }}</td>
        </tr>
    </table>

    <div class="section-title">Penerimaan (Earnings)</div>
    <table class="details-table">
        <tr>
            <td>Gaji Pokok</td>
            <td class="amount">Rp {{ number_format($gaji->gaji_pokok, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Insentif BPJS Kesehatan & Ketenagakerjaan</td>
            <td class="amount">Rp {{ number_format($gaji->insentif_bpjs, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Insentif Kegiatan Maghrib Mengaji</td>
            <td class="amount">Rp {{ number_format($gaji->insentif_maghrib_mengaji, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="section-title">Potongan (Deductions)</div>
    <table class="details-table">
        <tr>
            <td>Potongan Peminjaman / Kasbon</td>
            <td class="amount" style="color: #b91c1c;">- Rp {{ number_format($gaji->potongan_peminjaman, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Potongan Lain-lain</td>
            <td class="amount" style="color: #b91c1c;">- Rp {{ number_format($gaji->potongan_lainnya, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="total-box">
        <span class="label">TOTAL DITERIMA (NETO):</span>
        <span class="amount">Rp {{ number_format($gaji->total_diterima, 0, ',', '.') }}</span>
    </div>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td>
                    Penerima,
                    <div class="signature-space"></div>
                    <strong>{{ $gaji->guru->user->nama ?? '-' }}</strong>
                </td>
                <td>
                    Bendahara Yayasan,
                    <div class="signature-space"></div>
                    <strong>Siti Aminah, S.E.</strong>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
