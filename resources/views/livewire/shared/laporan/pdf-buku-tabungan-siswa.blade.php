<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Buku Rekening Tabungan Siswa</title>
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
        .student-card {
            width: 100%;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 15px;
        }
        .student-card table {
            width: 100%;
        }
        .student-card td {
            padding: 2.5px 0;
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
            text-align: center;
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
        .saldo-box {
            font-size: 11px;
            font-weight: bold;
            text-align: right;
            padding: 8px 12px;
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $namaSekolah }}</h1>
        <p>{{ $alamatSekolah }} | Telp: {{ $noTelepon }}</p>
    </div>

    <div class="title">BUKU MUTASI TABUNGAN SANTRI / SISWA</div>

    <div class="student-card">
        <table>
            <tr>
                <td style="width: 18%; font-weight: bold;">Nama Siswa</td>
                <td style="width: 32%;">: <strong>{{ $siswa->user->nama ?? '-' }}</strong></td>
                <td style="width: 18%; font-weight: bold;">Kelas</td>
                <td style="width: 32%;">: {{ $siswa->kelas->nama_kelas ?? '-' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">NIS / ID</td>
                <td>: {{ $siswa->nis ?? '-' }}</td>
                <td style="font-weight: bold;">Nama Wali</td>
                <td>: {{ $siswa->nama_wali ?: '-' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Tanggal Cetak</td>
                <td>: {{ date('d-m-Y H:i') }} WIB</td>
                <td style="font-weight: bold;">Total Mutasi</td>
                <td>: {{ count($mutasi) }} Transaksi</td>
            </tr>
        </table>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 10%;">Jenis</th>
                <th style="width: 25%; text-align: left;">Keterangan / Uraian</th>
                <th style="width: 15%; text-align: right;">Setoran (+)</th>
                <th style="width: 15%; text-align: right;">Penarikan (-)</th>
                <th style="width: 18%; text-align: right;">Saldo (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($mutasi as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $row['tanggal'] }}</td>
                    <td class="text-center">
                        @if ($row['jenis'] === 'setor')
                            <span style="color: #15803d; font-weight: bold;">SETOR</span>
                        @else
                            <span style="color: #be123c; font-weight: bold;">TARIK</span>
                        @endif
                    </td>
                    <td>{{ $row['keterangan'] }}</td>
                    <td class="text-right">
                        @if ($row['jenis'] === 'setor')
                            <strong style="color: #15803d;">Rp {{ number_format($row['nominal'], 0, ',', '.') }}</strong>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">
                        @if ($row['jenis'] === 'tarik')
                            <strong style="color: #be123c;">Rp {{ number_format($row['nominal'], 0, ',', '.') }}</strong>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right" style="font-weight: 800; color: #0284c7; background-color: #f0f9ff;">
                        Rp {{ number_format($row['saldo_berjalan'], 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 15px; color: #777;">
                        Belum ada riwayat transaksi mutasi tabungan untuk siswa ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f8fafc; font-weight: bold;">
                <td colspan="4" class="text-right" style="padding: 6px;">TOTAL MUTASI:</td>
                <td class="text-right" style="color: #15803d;">Rp {{ number_format($totalSetor, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #be123c;">Rp {{ number_format($totalTarik, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #0284c7; font-weight: 800; background-color: #e0f2fe;">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="saldo-box" style="color: #0369a1;">
        SALDO AKHIR TABUNGAN SAAT INI: Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
    </div>

    <x-ttd-elektronik role="bendahara" docType="TAB" :docId="$siswa->id" />
</body>
</html>
