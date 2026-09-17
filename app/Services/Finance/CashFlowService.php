<?php

namespace App\Services\Finance;

use App\Models\GajiGuru;
use App\Models\PemasukanKas;
use App\Models\Pembayaran;
use App\Models\Peminjaman;
use App\Models\Pengeluaran;
use App\Models\Tabungan;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CashFlowService
{
    /**
     * Get unified collection of transactions based on active tab, stream, search, and date filters.
     */
    public static function getUnifiedTransactions(array $filters): Collection
    {
        $transactions = collect();

        $tab = $filters['tab'] ?? 'semua';
        $stream = $filters['stream'] ?? 'semua';
        $search = $filters['search'] ?? '';
        $filterPeriode = $filters['filter_periode'] ?? 'semua';
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;
        $filterKategoriMasuk = $filters['filter_kategori_masuk'] ?? '';
        $filterKategoriKeluar = $filters['filter_kategori_keluar'] ?? null;
        $nominalMin = $filters['nominal_min'] ?? null;
        $nominalMax = $filters['nominal_max'] ?? null;
        $filterMetode = $filters['filter_metode'] ?? 'semua';

        $hasCategoryKeluar = ($filterKategoriKeluar !== null && $filterKategoriKeluar !== '');
        $hasCategoryMasuk = ($filterKategoriMasuk !== null && $filterKategoriMasuk !== '');

        // 🟢 INFLOW STREAMS
        if (($tab === 'semua' || $tab === 'masuk') && !$hasCategoryKeluar) {
            // Stream 1: Pembayaran Tagihan / SPP
            if ($stream === 'semua' || $stream === 'spp' || $stream === 'pembayaran_spp') {
                $sppTbl = Pembayaran::with(['tagihan.siswa.user', 'tagihan.siswa.kelas', 'tagihan.jenisTagihan', 'petugas'])
                    ->where('is_void', false)->latest('tanggal_bayar');

                if ($hasCategoryMasuk) {
                    $sppTbl->whereHas('tagihan.jenisTagihan', fn($sq) => $sq->where('nama', $filterKategoriMasuk));
                }

                if ($search !== '') {
                    $sppTbl->where(function ($q) use ($search) {
                        $q->where('no_resi', 'like', '%' . $search . '%')
                          ->orWhereHas('tagihan.siswa.user', fn($sq) => $sq->where('nama', 'like', '%' . $search . '%'))
                          ->orWhereHas('tagihan.jenisTagihan', fn($sq) => $sq->where('nama', 'like', '%' . $search . '%'));
                    });
                }
                FinanceReportService::applyDateFilter($sppTbl, 'tanggal_bayar', $filterPeriode, $startDate, $endDate);

                foreach ($sppTbl->get() as $item) {
                    $siswaNama = $item->tagihan->siswa->user->nama ?? 'Siswa';
                    $kelasNama = $item->tagihan->siswa->kelas->nama_kelas ?? '-';
                    $jenisNama = $item->tagihan->jenisTagihan->nama ?? 'Tagihan';
                    $bulan = $item->tagihan->bulan ? ' (' . $item->tagihan->bulan . ')' : '';

                    $transactions->push((object) [
                        'id' => 'spp_' . $item->id,
                        'raw_id' => $item->id,
                        'tanggal' => $item->tanggal_bayar ? Carbon::parse($item->tanggal_bayar) : Carbon::now(),
                        'type' => 'masuk',
                        'stream' => 'spp',
                        'stream_label' => 'SPP & Tagihan',
                        'stream_badge' => 'emerald',
                        'kategori' => $jenisNama,
                        'keterangan' => $siswaNama . ' - Kelas ' . $kelasNama . $bulan,
                        'nominal_masuk' => (float) $item->nominal_dibayar,
                        'nominal_keluar' => 0.00,
                        'metode_resi' => $item->metode_bayar ?: 'Tunai',
                        'no_resi' => $item->no_resi,
                        'petugas' => $item->petugas->nama ?? 'Kasir',
                        'can_delete' => false,
                        'can_edit' => false,
                        'bukti' => $item->bukti_bayar,
                    ]);
                }
            }

            // Stream 2: Kas Masuk Yayasan (Infaq / Donasi)
            if ($stream === 'semua' || $stream === 'infaq' || $stream === 'kas_yayasan') {
                $kasTbl = PemasukanKas::with('petugas')->latest('tanggal');
                if ($hasCategoryMasuk) {
                    $kasTbl->where('kategori', $filterKategoriMasuk);
                }
                if ($search !== '') {
                    $kasTbl->where(function ($q) use ($search) {
                        $q->where('kategori', 'like', '%' . $search . '%')
                          ->orWhere('keterangan', 'like', '%' . $search . '%');
                    });
                }
                FinanceReportService::applyDateFilter($kasTbl, 'tanggal', $filterPeriode, $startDate, $endDate);

                foreach ($kasTbl->get() as $item) {
                    $transactions->push((object) [
                        'id' => 'infaq_' . $item->id,
                        'raw_id' => $item->id,
                        'tanggal' => $item->tanggal ? Carbon::parse($item->tanggal) : Carbon::now(),
                        'type' => 'masuk',
                        'stream' => 'infaq',
                        'stream_label' => 'Kas Yayasan',
                        'stream_badge' => 'amber',
                        'kategori' => $item->kategori,
                        'keterangan' => $item->keterangan ?: 'Penerimaan infaq/donasi yayasan',
                        'nominal_masuk' => (float) $item->jumlah,
                        'nominal_keluar' => 0.00,
                        'metode_resi' => 'Kas Tunai / Transfer Bank',
                        'no_resi' => null,
                        'petugas' => $item->petugas->nama ?? 'Bendahara',
                        'can_delete' => true,
                        'can_edit' => false,
                        'bukti' => null,
                    ]);
                }
            }

            // Stream 3: Setoran Tabungan Siswa
            if (($stream === 'semua' || $stream === 'tabungan') && (!$hasCategoryMasuk || $filterKategoriMasuk === 'Tabungan Siswa' || str_contains(strtolower($filterKategoriMasuk), 'tabungan'))) {
                $tabTbl = Tabungan::with(['siswa.user', 'siswa.kelas', 'petugas'])->where('jenis', 'setor')->latest('tanggal');
                if ($search !== '') {
                    $tabTbl->whereHas('siswa.user', fn($q) => $q->where('nama', 'like', '%' . $search . '%'));
                }
                FinanceReportService::applyDateFilter($tabTbl, 'tanggal', $filterPeriode, $startDate, $endDate);

                foreach ($tabTbl->get() as $item) {
                    $siswaNama = $item->siswa->user->nama ?? 'Siswa';
                    $kelasNama = $item->siswa->kelas->nama_kelas ?? '-';

                    $transactions->push((object) [
                        'id' => 'tab_' . $item->id,
                        'raw_id' => $item->id,
                        'tanggal' => $item->tanggal ? Carbon::parse($item->tanggal) : Carbon::now(),
                        'type' => 'masuk',
                        'stream' => 'tabungan',
                        'stream_label' => 'Setoran Tabungan',
                        'stream_badge' => 'purple',
                        'kategori' => 'Tabungan Siswa',
                        'keterangan' => 'Setor: ' . $siswaNama . ' (' . $kelasNama . ')',
                        'nominal_masuk' => (float) $item->nominal,
                        'nominal_keluar' => 0.00,
                        'metode_resi' => 'Setoran Tunai',
                        'no_resi' => null,
                        'petugas' => $item->petugas->nama ?? 'Petugas Tabungan',
                        'can_delete' => false,
                        'can_edit' => false,
                        'bukti' => null,
                    ]);
                }
            }
        }

        // 🔴 OUTFLOW STREAMS
        if (($tab === 'semua' || $tab === 'keluar') && !$hasCategoryMasuk) {
            // Stream 4: Operasional Yayasan
            if ($stream === 'semua' || $stream === 'operasional') {
                $opTbl = Pengeluaran::with(['kategori', 'petugas'])->whereDoesntHave('gajiGuru')->latest('tanggal');
                if ($hasCategoryKeluar) {
                    $opTbl->where('kategori_pengeluaran_id', $filterKategoriKeluar);
                }
                if ($search !== '') {
                    $opTbl->where(function ($q) use ($search) {
                        $q->where('keterangan', 'like', '%' . $search . '%')
                          ->orWhereHas('kategori', fn($sq) => $sq->where('nama', 'like', '%' . $search . '%'));
                    });
                }
                FinanceReportService::applyDateFilter($opTbl, 'tanggal', $filterPeriode, $startDate, $endDate);

                foreach ($opTbl->get() as $item) {
                    $transactions->push((object) [
                        'id' => 'op_' . $item->id,
                        'raw_id' => $item->id,
                        'tanggal' => $item->tanggal ? Carbon::parse($item->tanggal) : Carbon::now(),
                        'type' => 'keluar',
                        'stream' => 'operasional',
                        'stream_label' => 'Operasional Yayasan',
                        'stream_badge' => 'rose',
                        'kategori' => $item->kategori->nama ?? 'Umum',
                        'keterangan' => $item->keterangan ?: 'Beban operasional kas yayasan',
                        'nominal_masuk' => 0.00,
                        'nominal_keluar' => (float) $item->jumlah,
                        'metode_resi' => 'Kas Tunai / Transfer Bank',
                        'no_resi' => null,
                        'petugas' => $item->petugas->nama ?? 'Bendahara',
                        'can_delete' => true,
                        'can_edit' => true,
                        'bukti' => $item->bukti,
                    ]);
                }
            }

            // Stream 5: Gaji Guru
            if (($stream === 'semua' || $stream === 'gaji') && !$hasCategoryKeluar) {
                $gajiTbl = GajiGuru::with(['guru.user'])->where('status', 'dibayar')->latest('tanggal_bayar');
                if ($search !== '') {
                    $gajiTbl->whereHas('guru.user', fn($q) => $q->where('nama', 'like', '%' . $search . '%'));
                }
                FinanceReportService::applyDateFilter($gajiTbl, 'tanggal_bayar', $filterPeriode, $startDate, $endDate);

                foreach ($gajiTbl->get() as $item) {
                    $transactions->push((object) [
                        'id' => 'gaji_' . $item->id,
                        'raw_id' => $item->id,
                        'tanggal' => $item->tanggal_bayar ? Carbon::parse($item->tanggal_bayar) : Carbon::now(),
                        'type' => 'keluar',
                        'stream' => 'gaji',
                        'stream_label' => 'Gaji & Honor Guru',
                        'stream_badge' => 'violet',
                        'kategori' => 'Honorarium & Gaji',
                        'keterangan' => 'Gaji ' . ($item->guru->user->nama ?? 'Guru') . ' (' . $item->bulan . ' ' . $item->tahun . ')',
                        'nominal_masuk' => 0.00,
                        'nominal_keluar' => (float) $item->total_diterima,
                        'metode_resi' => 'Payroll Transfer',
                        'no_resi' => null,
                        'petugas' => 'Sistem Payroll',
                        'can_delete' => false,
                        'can_edit' => false,
                        'bukti' => null,
                    ]);
                }
            }

            // Stream 6: Kasbon Guru
            if (($stream === 'semua' || $stream === 'kasbon' || $stream === 'peminjaman') && !$hasCategoryKeluar) {
                $loanTbl = Peminjaman::with(['guru.user'])->latest('tanggal_pinjam');
                if ($search !== '') {
                    $loanTbl->whereHas('guru.user', fn($q) => $q->where('nama', 'like', '%' . $search . '%'));
                }
                FinanceReportService::applyDateFilter($loanTbl, 'tanggal_pinjam', $filterPeriode, $startDate, $endDate);

                foreach ($loanTbl->get() as $item) {
                    $transactions->push((object) [
                        'id' => 'loan_' . $item->id,
                        'raw_id' => $item->id,
                        'tanggal' => $item->tanggal_pinjam ? Carbon::parse($item->tanggal_pinjam) : Carbon::now(),
                        'type' => 'keluar',
                        'stream' => 'kasbon',
                        'stream_label' => 'Kasbon Guru',
                        'stream_badge' => 'teal',
                        'kategori' => 'Fasilitas Kasbon',
                        'keterangan' => 'Pencairan kasbon: ' . ($item->guru->user->nama ?? 'Guru') . ' (Tenor ' . $item->tenor_bulan . ' Bln)',
                        'nominal_masuk' => 0.00,
                        'nominal_keluar' => (float) $item->nominal,
                        'metode_resi' => 'Pencairan Tunai / Transfer',
                        'no_resi' => null,
                        'petugas' => 'Finance',
                        'can_delete' => false,
                        'can_edit' => false,
                        'bukti' => null,
                    ]);
                }
            }
        }

        // Filter by Nominal Range (Min/Max)
        if ($nominalMin !== null && $nominalMin !== '') {
            $minVal = unmask_rupiah($nominalMin);
            $transactions = $transactions->filter(function ($t) use ($minVal) {
                $val = $t->type === 'masuk' ? $t->nominal_masuk : $t->nominal_keluar;
                return $val >= $minVal;
            });
        }

        if ($nominalMax !== null && $nominalMax !== '') {
            $maxVal = unmask_rupiah($nominalMax);
            $transactions = $transactions->filter(function ($t) use ($maxVal) {
                $val = $t->type === 'masuk' ? $t->nominal_masuk : $t->nominal_keluar;
                return $val <= $maxVal;
            });
        }

        // Filter by Payment Method
        if ($filterMetode && $filterMetode !== 'semua') {
            $transactions = $transactions->filter(function ($t) use ($filterMetode) {
                $method = strtolower($filterMetode);
                $resi = strtolower($t->metode_resi ?? '');
                if ($method === 'tunai') {
                    return str_contains($resi, 'tunai') || str_contains($resi, 'cash');
                } elseif ($method === 'transfer') {
                    return str_contains($resi, 'transfer') || str_contains($resi, 'bank') || str_contains($resi, 'payroll');
                } elseif ($method === 'qris') {
                    return str_contains($resi, 'qris');
                }
                return str_contains($resi, $method);
            });
        }

        return $transactions->sortByDesc(fn($item) => $item->tanggal->timestamp)->values();
    }

    /**
     * Calculate summary metrics for all cash flow streams.
     */
    public static function calculateMetrics(array $filters): array
    {
        $filterPeriode = $filters['filter_periode'] ?? 'semua';
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;

        // Inflow
        $sppQuery = Pembayaran::where('is_void', false);
        FinanceReportService::applyDateFilter($sppQuery, 'tanggal_bayar', $filterPeriode, $startDate, $endDate);
        $totalTagihanSpp = (float) $sppQuery->sum('nominal_dibayar');

        $kasMasukQuery = PemasukanKas::query();
        FinanceReportService::applyDateFilter($kasMasukQuery, 'tanggal', $filterPeriode, $startDate, $endDate);
        $totalKasYayasan = (float) $kasMasukQuery->sum('jumlah');

        $tabunganQuery = Tabungan::where('jenis', 'setor');
        FinanceReportService::applyDateFilter($tabunganQuery, 'tanggal', $filterPeriode, $startDate, $endDate);
        $totalTabunganSetor = (float) $tabunganQuery->sum('nominal');

        $totalInflow = $totalTagihanSpp + $totalKasYayasan + $totalTabunganSetor;

        // Outflow
        $opQuery = Pengeluaran::whereDoesntHave('gajiGuru');
        FinanceReportService::applyDateFilter($opQuery, 'tanggal', $filterPeriode, $startDate, $endDate);
        $totalOperasional = (float) $opQuery->sum('jumlah');

        $gajiQuery = GajiGuru::where('status', 'dibayar');
        FinanceReportService::applyDateFilter($gajiQuery, 'tanggal_bayar', $filterPeriode, $startDate, $endDate);
        $totalGaji = (float) $gajiQuery->sum('total_diterima');

        $loanQuery = Peminjaman::query();
        FinanceReportService::applyDateFilter($loanQuery, 'tanggal_pinjam', $filterPeriode, $startDate, $endDate);
        $totalKasbon = (float) $loanQuery->sum('nominal');

        $totalOutflow = $totalOperasional + $totalGaji + $totalKasbon;

        return [
            'totalInflow' => $totalInflow,
            'totalOutflow' => $totalOutflow,
            'netCashFlow' => $totalInflow - $totalOutflow,
            'totalTagihanSpp' => $totalTagihanSpp,
            'totalKasYayasan' => $totalKasYayasan,
            'totalTabunganSetor' => $totalTabunganSetor,
            'totalOperasional' => $totalOperasional,
            'totalGaji' => $totalGaji,
            'totalKasbon' => $totalKasbon,
        ];
    }

    /**
     * Compute 6-Month Inflow vs Outflow Comparison Trend for chart visualizations.
     */
    public static function calculateMonthlyTrend(int $months = 6): array
    {
        $monthlyChartData = [];
        $maxMonthVal = 1;

        for ($i = $months - 1; $i >= 0; $i--) {
            $mCarbon = Carbon::now()->subMonths($i);
            $year = $mCarbon->year;
            $monthNum = $mCarbon->month;
            $monthLabel = $mCarbon->locale('id')->isoFormat('MMM YYYY');

            // Inflow
            $mSpp = (float) Pembayaran::where('is_void', false)->whereYear('tanggal_bayar', $year)->whereMonth('tanggal_bayar', $monthNum)->sum('nominal_dibayar');
            $mInfaq = (float) PemasukanKas::whereYear('tanggal', $year)->whereMonth('tanggal', $monthNum)->sum('jumlah');
            $mTab = (float) Tabungan::where('jenis', 'setor')->whereYear('tanggal', $year)->whereMonth('tanggal', $monthNum)->sum('nominal');
            $mInflowTotal = $mSpp + $mInfaq + $mTab;

            // Outflow
            $mOp = (float) Pengeluaran::whereDoesntHave('gajiGuru')->whereYear('tanggal', $year)->whereMonth('tanggal', $monthNum)->sum('jumlah');
            $mGaji = (float) GajiGuru::where('status', 'dibayar')
                ->where(function ($q) use ($year, $monthNum, $mCarbon) {
                    $q->whereYear('tanggal_bayar', $year)->whereMonth('tanggal_bayar', $monthNum)
                      ->orWhere(function ($sq) use ($year, $mCarbon) {
                          $sq->where('tahun', $year)->where('bulan', $mCarbon->locale('id')->isoFormat('MMMM'));
                      });
                })->sum('total_diterima');
            $mLoan = (float) Peminjaman::whereYear('tanggal_pinjam', $year)->whereMonth('tanggal_pinjam', $monthNum)->sum('nominal');
            $mOutflowTotal = $mOp + $mGaji + $mLoan;

            if ($mInflowTotal > $maxMonthVal) {
                $maxMonthVal = $mInflowTotal;
            }
            if ($mOutflowTotal > $maxMonthVal) {
                $maxMonthVal = $mOutflowTotal;
            }

            $monthlyChartData[] = [
                'label' => $monthLabel,
                'year' => $year,
                'month' => $monthNum,
                'inflow' => $mInflowTotal,
                'outflow' => $mOutflowTotal,
                'net' => $mInflowTotal - $mOutflowTotal,
            ];
        }

        foreach ($monthlyChartData as &$mItem) {
            $mItem['inflow_pct'] = $maxMonthVal > 0 ? round(($mItem['inflow'] / $maxMonthVal) * 100) : 0;
            $mItem['outflow_pct'] = $maxMonthVal > 0 ? round(($mItem['outflow'] / $maxMonthVal) * 100) : 0;
        }
        unset($mItem);

        return [
            'monthlyChartData' => $monthlyChartData,
            'maxMonthVal' => $maxMonthVal,
        ];
    }
}
