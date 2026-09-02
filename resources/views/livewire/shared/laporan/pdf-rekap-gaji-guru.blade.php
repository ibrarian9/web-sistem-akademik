<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekapitulasi Pembayaran Gaji Guru</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5px;
            color: #333;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #222;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 14px;
            margin: 0;
            color: #111;
            text-transform: uppercase;
        }
        .header p {
            margin: 2px 0 0 0;
            color: #555;
            font-size: 9px;
        }
        .title {
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .meta-info {
            margin-bottom: 10px;
        }
        .meta-info table {
            width: 100%;
        }
        .meta-info td {
            padding: 1.5px 0;
            font-size: 8.5px;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .report-table th {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 5px 3px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 7.5px;
            color: #1e293b;
        }
        .report-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 3px;
            font-size: 8px;
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
        .summary-box {
            font-size: 9.5px;
            font-weight: bold;
            text-align: right;
            padding: 6px 10px;
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $namaSekolah }}</h1>
        <p>{{ $alamatSekolah }} | Telp: {{ $noTelepon }}</p>
    </div>

    <div class="title">DAFTAR REKAPITULASI PEMBAYARAN GAJI & HONOR GURU / KARYAWAN</div>

    <div class="meta-info">
        <table>
            <tr>
                <td style="width: 12%; font-weight: bold;">Bulan & Tahun</td>
                <td style="width: 38%;">: {{ $bulan }} {{ $tahun }}</td>
                <td style="width: 12%; font-weight: bold;">Status Penggajian</td>
                <td style="width: 38%;">: {{ $statusText }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Total Personel</td>
                <td>: {{ $data->count() }} Guru / Staf</td>
                <td style="font-weight: bold;">Tanggal Cetak</td>
                <td>: {{ date('d-m-Y H:i') }} WIB</td>
            </tr>
        </table>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 14%; text-align: left;">Nama Guru / Staf</th>
                <th style="width: 8%;">Jabatan</th>
                <th style="width: 7%;">Gaji Pokok</th>
                <th style="width: 7%;">Gaji Berkala</th>
                <th style="width: 6%;">Ekskul</th>
                <th style="width: 7%;">Insentif</th>
                <th style="width: 8%;">Total</th>
                <th style="width: 6%;">Pot. Sosial</th>
                <th style="width: 6%;">Pot. Pinjam</th>
                <th style="width: 6%;">Pot. BPJS</th>
                <th style="width: 7%;">Tot. Potongan</th>
                <th style="width: 9%;">Total Diterima</th>
                <th style="width: 6%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $index => $row)
                @php
                    $totalInsentifRow = (float)$row->insentif + (float)$row->insentif_bpjs + (float)$row->insentif_maghrib_mengaji;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $row->guru->user->nama ?? '-' }}</strong>
                        @if($row->guru->nip)
                            <br><span style="font-size: 7px; color: #64748b;">NIP: {{ $row->guru->nip }}</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $row->jabatan ?: ($row->guru->jabatan ?? '-') }}</td>
                    <td class="text-right">Rp {{ number_format($row->gaji_pokok, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($row->gaji_berkala, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($row->honor_ekskul, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($totalInsentifRow, 0, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: bold; color: #1e293b;">Rp {{ number_format($row->total_bruto, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($row->potongan_sosial, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($row->potongan_peminjaman, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($row->potongan_bpjstk, 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #be123c;">Rp {{ number_format($row->total_potongan, 0, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: 800; color: #15803d; background-color: #f0fdf4;">Rp {{ number_format($row->total_diterima, 0, ',', '.') }}</td>
                    <td class="text-center">
                        @if ($row->status === 'dibayar')
                            <strong style="color: #15803d;">LUNAS</strong>
                        @else
                            <span style="color: #d97706;">DRAFT</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="14" class="text-center" style="padding: 15px; color: #777;">
                        Tidak ada data penggajian guru untuk periode dan filter yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="3" class="text-right" style="padding: 5px;">JUMLAH TOTAL:</td>
                <td class="text-right">Rp {{ number_format($data->sum('gaji_pokok'), 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($data->sum('gaji_berkala'), 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($data->sum('honor_ekskul'), 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($data->sum(fn($g) => $g->insentif + $g->insentif_bpjs + $g->insentif_maghrib_mengaji), 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($data->sum('total_bruto'), 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($data->sum('potongan_sosial'), 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($data->sum('potongan_peminjaman'), 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($data->sum('potongan_bpjstk'), 0, ',', '.') }}</td>
                <td class="text-right" style="color: #be123c;">Rp {{ number_format($data->sum('total_potongan'), 0, ',', '.') }}</td>
                <td class="text-right" style="color: #15803d; font-weight: 800; background-color: #dcfce7;">Rp {{ number_format($data->sum('total_diterima'), 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="summary-box">
        TOTAL PENGELUARAN GAJI BERSIH (NETTO): <span style="font-size: 11px; color: #15803d;">Rp {{ number_format($data->sum('total_diterima'), 0, ',', '.') }}</span>
    </div>

    <x-ttd-elektronik role="bendahara" docType="GAJI" :docId="date('Ymd')" />
</body>
</html>
