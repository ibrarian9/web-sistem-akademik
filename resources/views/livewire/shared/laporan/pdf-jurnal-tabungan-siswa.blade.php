<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Jurnal Riwayat Mutasi Tabungan Siswa</title>
    <style>
        @page {
            margin: 1.5cm 1.5cm 2cm 1.5cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5px;
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
        .summary-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .summary-box {
            padding: 6px 10px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            text-align: center;
        }
        .summary-title {
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
        }
        .summary-val {
            font-size: 11px;
            font-weight: 800;
            margin-top: 2px;
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
            padding: 5px 4px;
            font-weight: bold;
            text-align: left;
            text-transform: uppercase;
            font-size: 8px;
        }
        .report-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 5px;
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
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 70%;">
                <div class="school-name">{{ $namaSekolah ?? 'PONDOK PESANTREN & SEKOLAH ISLAM TERPADU' }}</div>
                <div class="school-sub">{{ $alamatSekolah ?? 'Jl. Pendidikan Karakter Islami, Pekanbaru' }} | Telp: {{ $noTelepon ?? '(0761) 123456' }}</div>
            </td>
            <td style="width: 30%; text-align: right;">
                <span style="display: inline-block; padding: 3px 8px; background-color: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 7.5px; font-weight: bold; color: #475569;">
                    JURNAL TABUNGAN RESMI
                </span>
            </td>
        </tr>
    </table>

    <div class="doc-title">JURNAL & RIWAYAT MUTASI TABUNGAN SISWA</div>

    <table class="meta-table">
        <tr>
            <td style="width: 15%; font-weight: bold; color: #475569;">Periode:</td>
            <td style="width: 35%; font-weight: bold;">{{ $periodeText }}</td>
            <td style="width: 15%; font-weight: bold; color: #475569;">Filter Kelas:</td>
            <td style="width: 35%;">{{ $namaKelas }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; color: #475569;">Jenis Mutasi:</td>
            <td style="font-weight: bold; color: #065f46;">{{ $jenisText }}</td>
            <td style="font-weight: bold; color: #475569;">Tanggal Cetak:</td>
            <td>{{ now()->translatedFormat('d F Y, H:i') }} WIB</td>
        </tr>
    </table>

    <!-- Summary Box -->
    <table class="summary-grid">
        <tr>
            <td style="width: 33.3%; padding-right: 4px;">
                <div class="summary-box" style="background-color: #f0fdf4; border-color: #bbf7d0;">
                    <div class="summary-title" style="color: #166534;">Total Mutasi Setor (+)</div>
                    <div class="summary-val" style="color: #15803d;">Rp {{ number_format($totalSetor, 0, ',', '.') }}</div>
                </div>
            </td>
            <td style="width: 33.3%; padding: 0 4px;">
                <div class="summary-box" style="background-color: #fff1f2; border-color: #fecdd3;">
                    <div class="summary-title" style="color: #9f1239;">Total Mutasi Tarik (-)</div>
                    <div class="summary-val" style="color: #be123c;">Rp {{ number_format($totalTarik, 0, ',', '.') }}</div>
                </div>
            </td>
            <td style="width: 33.3%; padding-left: 4px;">
                <div class="summary-box" style="background-color: #f0f9ff; border-color: #bae6fd;">
                    <div class="summary-title" style="color: #075985;">Total Transaksi Mutasi</div>
                    <div class="summary-val" style="color: #0284c7;">{{ number_format($data->count(), 0, ',', '.') }} Transaksi</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 4%; text-align: center;">No</th>
                <th style="width: 10%;">Tanggal</th>
                <th style="width: 12%;">Kode Mutasi</th>
                <th style="width: 20%;">Nama Siswa</th>
                <th style="width: 8%; text-align: center;">Kelas</th>
                <th style="width: 8%; text-align: center;">Jenis</th>
                <th style="width: 12%; text-align: right;">Nominal (Rp)</th>
                <th style="width: 12%; text-align: right;">Saldo Akhir (Rp)</th>
                <th style="width: 14%;">Petugas / Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row->tanggal ? \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d M Y') : '-' }}</td>
                    <td style="font-family: monospace; font-weight: bold;">{{ $row->kode_transaksi }}</td>
                    <td>
                        <strong>{{ $row->siswa->user->nama ?? '-' }}</strong>
                        <div style="font-size: 7px; color: #64748b;">NIS: {{ $row->siswa->nis ?? '-' }}</div>
                    </td>
                    <td class="text-center">{{ $row->siswa->kelas->nama_kelas ?? '-' }}</td>
                    <td class="text-center" style="font-weight: bold; color: {{ $row->jenis === 'setor' ? '#166534' : '#b91c1c' }};">
                        {{ strtoupper($row->jenis) }}
                    </td>
                    <td class="text-right" style="font-weight: bold; color: {{ $row->jenis === 'setor' ? '#166534' : '#b91c1c' }};">
                        {{ $row->jenis === 'setor' ? '+' : '-' }} Rp {{ number_format($row->nominal, 0, ',', '.') }}
                    </td>
                    <td class="text-right" style="font-weight: bold; color: #0284c7;">
                        Rp {{ number_format($row->saldo_akhir, 0, ',', '.') }}
                    </td>
                    <td>
                        <div>{{ $row->petugas->nama ?? 'Sistem' }}</div>
                        @if ($row->keterangan)
                            <div style="font-size: 7px; color: #64748b; font-style: italic;">{{ $row->keterangan }}</div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 15px; color: #94a3b8;">
                        Tidak ada riwayat mutasi tabungan yang sesuai dengan kriteria filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f8fafc; font-weight: bold;">
                <td colspan="6" class="text-right" style="padding: 5px;">TOTAL MUTASI:</td>
                <td class="text-right" style="color: #15803d;">+ Rp {{ number_format($totalSetor, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #be123c;">- Rp {{ number_format($totalTarik, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <x-ttd-elektronik role="bendahara" docType="TAB" :docId="date('Ymd')" />
</body>
</html>
