<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kuitansi Bukti Pembayaran #{{ $pembayaran->id }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11pt;
            color: #1e293b;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 16pt;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0f172a;
        }
        .header p {
            font-size: 9pt;
            color: #64748b;
            margin: 4px 0 0 0;
        }
        .title-box {
            text-align: center;
            margin-bottom: 24px;
        }
        .title-box h2 {
            font-size: 13pt;
            margin: 0;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .title-box span {
            font-size: 9pt;
            color: #64748b;
        }
        .info-table {
            width: 100%;
            margin-bottom: 25px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 6px 4px;
            vertical-align: top;
        }
        .info-table td.label {
            width: 140px;
            font-weight: bold;
            color: #475569;
        }
        .box-nominal {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 30px;
        }
        .box-nominal table {
            width: 100%;
        }
        .box-nominal td {
            font-weight: bold;
            font-size: 14pt;
            color: #0f172a;
        }
        .footer-signature {
            width: 100%;
            margin-top: 40px;
        }
        .footer-signature td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        .signature-space {
            height: 65px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>SISTEM INFORMASI AKADEMIK & KEUANGAN YAYASAN</h1>
        <p>Kuitansi Resmi Bukti Pembayaran Administrasi Sekolah</p>
    </div>

    <div class="title-box">
        <h2>BUKTI PEMBAYARAN RESMI</h2>
        <span>No. Transaksi: {{ $pembayaran->no_resi ?: ('KW/' . date('Ym', strtotime($pembayaran->tanggal_bayar)) . '/' . sprintf('%05d', $pembayaran->id)) }}</span>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Telah Diterima Dari</td>
            <td>: <strong>{{ $pembayaran->tagihan->siswa->user->nama ?? '-' }}</strong> (NIS: {{ $pembayaran->tagihan->siswa->nis ?? '-' }})</td>
        </tr>
        <tr>
            <td class="label">Jenis Pembayaran</td>
            <td>: {{ $pembayaran->tagihan->jenisTagihan->nama ?? '-' }} {{ $pembayaran->tagihan->bulan ? 'Bulan ' . $pembayaran->tagihan->bulan : '' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Bayar</td>
            <td>: {{ date('d F Y', strtotime($pembayaran->tanggal_bayar)) }}</td>
        </tr>
        <tr>
            <td class="label">Metode Pembayaran</td>
            <td>: {{ strtoupper($pembayaran->metode_bayar) }}</td>
        </tr>
        <tr>
            <td class="label">Petugas Kasir</td>
            <td>: {{ $pembayaran->petugas->nama ?? '-' }}</td>
        </tr>
    </table>

    <div class="box-nominal">
        <table>
            <tr>
                <td style="font-size: 10pt; color: #64748b; font-weight: normal;">Jumlah Terbayar:</td>
                <td style="text-align: right;">Rp {{ number_format($pembayaran->nominal_dibayar, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <table class="footer-signature">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%; text-align: center; vertical-align: top;">
                <x-ttd-elektronik 
                    role="bendahara" 
                    docType="RES" 
                    :docId="$pembayaran->id" 
                    :user="$pembayaran->petugas ?? ($staffFinance ?? null)" 
                    title="Staf Keuangan / Bendahara" 
                    :tanggal="date('d F Y', strtotime($pembayaran->tanggal_bayar))" 
                />
            </td>
        </tr>
    </table>

</body>
</html>
