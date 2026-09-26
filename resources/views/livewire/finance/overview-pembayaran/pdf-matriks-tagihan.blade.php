<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Matriks Tagihan dan Pembayaran Santri</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8px;
            color: #1c1917;
            line-height: 1.3;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header h1 {
            font-size: 15px;
            margin: 0 0 3px 0;
            color: #0f172a;
            font-weight: 900;
            letter-spacing: 0.5px;
        }
        .header p {
            margin: 1px 0;
            color: #475569;
            font-size: 9px;
        }
        .title-bar {
            text-align: center;
            margin-bottom: 12px;
        }
        .title-bar h2 {
            font-size: 12px;
            font-weight: 800;
            margin: 0 0 2px 0;
            text-transform: uppercase;
            color: #047857;
            letter-spacing: 0.5px;
        }
        .title-bar span {
            font-size: 9px;
            color: #64748b;
            font-weight: 600;
        }
        .meta-grid {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }
        .meta-grid td {
            font-size: 8.5px;
            padding: 2px 4px;
            vertical-align: top;
        }
        .stats-box {
            width: 100%;
            margin-bottom: 12px;
            border-collapse: collapse;
        }
        .stats-box td {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 6px 10px;
            text-align: center;
            font-size: 8px;
        }
        .stats-box .val {
            font-size: 10px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 2px;
        }
        .stats-box .val.green {
            color: #047857;
        }
        .stats-box .val.red {
            color: #b91c1c;
        }
        .matrix-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .matrix-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            border: 1px solid #cbd5e1;
            padding: 5px 3px;
            font-size: 7.5px;
            font-weight: 800;
            text-align: center;
            text-transform: uppercase;
        }
        .matrix-table th.th-spp {
            background-color: #ecfdf5;
            color: #065f46;
            border-bottom: 2px solid #059669;
        }
        .matrix-table th.th-other {
            background-color: #eff6ff;
            color: #1e40af;
            border-bottom: 2px solid #2563eb;
        }
        .matrix-table td {
            border: 1px solid #e2e8f0;
            padding: 4px 3px;
            font-size: 7.5px;
            vertical-align: middle;
        }
        .matrix-table tr:nth-child(even) td {
            background-color: #fafaf9;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .font-bold {
            font-weight: 700;
        }
        .badge {
            display: inline-block;
            padding: 1px 3px;
            border-radius: 2px;
            font-weight: 700;
            font-size: 7px;
            text-align: center;
        }
        .badge-lunas {
            color: #047857;
            background-color: #d1fae5;
        }
        .badge-sebagian {
            color: #b45309;
            background-color: #fef3c7;
        }
        .badge-belum {
            color: #b91c1c;
            background-color: #fee2e2;
        }
        .badge-none {
            color: #94a3b8;
        }
        .footer-total td {
            background-color: #f8fafc;
            border-top: 2px solid #0f172a;
            border-bottom: 2px solid #0f172a;
            font-weight: 800;
            font-size: 8px;
            padding: 6px 3px;
        }
        .signature-table {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
        }
        .signature-table td {
            width: 50%;
            text-align: center;
            font-size: 8.5px;
            vertical-align: top;
            padding: 0 20px;
        }
        .signature-space {
            height: 45px;
        }
    </style>
</head>
<body>
    <!-- Kop Sekolah Resmi -->
    <div class="header">
        <h1>{{ strtoupper($namaSekolah) }}</h1>
        <p>{{ $alamatSekolah }} | Telp: {{ $noTelepon }}</p>
    </div>

    <!-- Judul Laporan -->
    <div class="title-bar">
        <h2>Laporan Matriks Tagihan & Status Pembayaran Santri</h2>
        <span>Periode: {{ $periodeText }} | Tahun Ajaran: {{ $tahunAjaran }}</span>
    </div>

    <!-- Metadata Laporan -->
    <table class="meta-grid">
        <tr>
            <td width="15%"><strong>Filter Kelas</strong></td>
            <td width="35%">: {{ $kelasNama }}</td>
            <td width="15%"><strong>Tanggal Cetak</strong></td>
            <td width="35%">: {{ $tanggalCetak }}</td>
        </tr>
    </table>

    <!-- Ringkasan Statistik -->
    <table class="stats-box">
        <tr>
            <td width="20%">
                <div>TOTAL SANTRI</div>
                <div class="val">{{ number_format($stats['total_siswa'] ?? 0) }} Santri</div>
            </td>
            <td width="20%">
                <div>STATUS LUNAS</div>
                <div class="val green">{{ number_format($stats['lunas_count'] ?? 0) }} Santri</div>
            </td>
            <td width="20%">
                <div>ADA TUNGGAKAN</div>
                <div class="val red">{{ number_format($stats['menunggak_count'] ?? 0) }} Santri</div>
            </td>
            <td width="20%">
                <div>TOTAL TERBAYAR</div>
                <div class="val green">Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</div>
            </td>
            <td width="20%">
                <div>TOTAL SISA TUNGGAKAN</div>
                <div class="val red">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    <!-- Tabel Matriks Tagihan Terpadu -->
    <table class="matrix-table">
        <thead>
            <tr>
                <th width="3%" rowspan="2">No</th>
                <th width="7%" rowspan="2">NIS</th>
                <th width="14%" rowspan="2" style="text-align: left; padding-left: 6px;">Nama Santri</th>
                <th width="6%" rowspan="2">Kelas</th>
                <th colspan="{{ count($months) }}" class="th-spp">Kolom SPP (6 Bulan)</th>
                @if (count($nonSppList) > 0)
                    <th colspan="{{ count($nonSppList) }}" class="th-other">Kategori Tagihan Lainnya (Non-SPP)</th>
                @endif
                <th width="8%" rowspan="2">Terbayar</th>
                <th width="8%" rowspan="2">Tunggakan</th>
                <th width="7%" rowspan="2">Status</th>
            </tr>
            <tr>
                @foreach ($months as $m)
                    <th class="th-spp" style="font-size: 7px;">{{ substr($m, 0, 3) }}</th>
                @endforeach
                @foreach ($nonSppList as $jt)
                    <th class="th-other" style="font-size: 7px;">{{ \Illuminate\Support\Str::limit($jt->nama, 10) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($siswas as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $item['nis'] ?: '-' }}</td>
                    <td style="font-weight: 600; padding-left: 5px;">{{ $item['nama'] }}</td>
                    <td class="text-center">{{ $item['kelas'] }}</td>

                    <!-- 6 Bulan SPP -->
                    @foreach ($months as $m)
                        @php
                            $mCell = $item['spp_months'][$m] ?? null;
                        @endphp
                        <td class="text-center">
                            @if (!$mCell || !$mCell['has_tagihan'])
                                <span class="badge badge-none">-</span>
                            @elseif ($mCell['status'] === 'lunas')
                                <span class="badge badge-lunas">Lunas</span>
                            @elseif ($mCell['status'] === 'sebagian')
                                <span class="badge badge-sebagian">Sbg: {{ number_format($mCell['sisa'] / 1000, 0) }}k</span>
                            @else
                                <span class="badge badge-belum">Rp {{ number_format($mCell['sisa'] / 1000, 0) }}k</span>
                            @endif
                        </td>
                    @endforeach

                    <!-- Non-SPP Kategori -->
                    @foreach ($nonSppList as $jt)
                        @php
                            $cCell = $item['non_spp_by_jenis'][$jt->id] ?? null;
                        @endphp
                        <td class="text-center">
                            @if (!$cCell || !$cCell['has_tagihan'])
                                <span class="badge badge-none">-</span>
                            @elseif ($cCell['status'] === 'lunas')
                                <span class="badge badge-lunas">Lunas</span>
                            @elseif ($cCell['status'] === 'sebagian')
                                <span class="badge badge-sebagian">Sbg: {{ number_format($cCell['sisa'] / 1000, 0) }}k</span>
                            @else
                                <span class="badge badge-belum">Rp {{ number_format($cCell['sisa'] / 1000, 0) }}k</span>
                            @endif
                        </td>
                    @endforeach

                    <!-- Ringkasan Angka -->
                    <td class="text-right" style="color: #047857; font-weight: 600;">Rp {{ number_format($item['total_terbayar'] ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #b91c1c; font-weight: 700;">Rp {{ number_format($item['total_tunggakan'] ?? 0, 0, ',', '.') }}</td>
                    <td class="text-center">
                        @if ($item['total_tunggakan'] <= 0)
                            <span class="badge badge-lunas">LUNAS</span>
                        @else
                            <span class="badge badge-belum">TUNGGAKAN</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 7 + count($months) + count($nonSppList) }}" class="text-center" style="padding: 15px; color: #64748b;">
                        Tidak ada data santri yang sesuai dengan kriteria filter yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="footer-total">
                <td colspan="4" class="text-center">REKAPITULASI TOTAL</td>
                <td colspan="{{ count($months) + count($nonSppList) }}" class="text-center" style="font-size: 7px; color: #64748b;">
                    Total {{ count($siswas) }} Santri
                </td>
                <td class="text-right" style="color: #047857;">Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #b91c1c;">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</td>
                <td class="text-center">-</td>
            </tr>
        </tfoot>
    </table>

    <!-- Tanda Tangan Pengesahan -->
    <table class="signature-table">
        <tr>
            <td>
                Mengetahui,<br>
                <strong>Kepala Sekolah</strong>
                <div class="signature-space"></div>
                <strong>( _________________________ )</strong>
            </td>
            <td>
                Dicetak Oleh,<br>
                <strong>Bendahara / Bagian Keuangan</strong>
                <div class="signature-space"></div>
                <strong>{{ auth()->user()->nama ?? 'Bagian Keuangan' }}</strong>
            </td>
        </tr>
    </table>
</body>
</html>
