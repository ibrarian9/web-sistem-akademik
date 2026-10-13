<?php

namespace App\Services\Finance;

use App\Models\DanaBos;
use App\Models\GajiGuru;
use App\Models\JenisTagihan;
use App\Models\KategoriPengeluaran;
use App\Models\PemasukanKas;
use App\Models\Pembayaran;
use App\Models\Peminjaman;
use App\Models\Pengeluaran;
use App\Models\Tabungan;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ConsolidatedCashService
{
    /**
     * Get consolidated transactions merging both Arus Kas (General Cash Flow) and Dana BOS.
     *
     * @param array $filters
     * @return Collection
     */
    public static function getTransactions(array $filters): Collection
    {
        $transactions = collect();

        $sumber = $filters['sumber'] ?? 'semua'; // 'semua', 'kas_umum', 'dana_bos'
        $jenis = $filters['jenis'] ?? 'semua';   // 'semua', 'masuk', 'keluar'
        $search = trim($filters['search'] ?? '');
        $filterPeriode = $filters['filter_periode'] ?? 'semua';
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;
        $filterKategori = trim($filters['kategori'] ?? '');
        $nominalMin = $filters['nominal_min'] ?? null;
        $nominalMax = $filters['nominal_max'] ?? null;

        $includeKas = ($sumber === 'semua' || $sumber === 'kas_umum');
        $includeBos = ($sumber === 'semua' || $sumber === 'dana_bos');
        $includeMasuk = ($jenis === 'semua' || $jenis === 'masuk');
        $includeKeluar = ($jenis === 'semua' || $jenis === 'keluar');

        // ==========================================
        // 1. DANA BOS (MASUK & KELUAR)
        // ==========================================
        if ($includeBos) {
            $bosQuery = DanaBos::query()->latest('tanggal');

            if ($jenis === 'masuk') {
                $bosQuery->where('jenis', 'masuk');
            } elseif ($jenis === 'keluar') {
                $bosQuery->where('jenis', 'keluar');
            }

            if ($filterKategori !== '') {
                $bosQuery->where(function ($bq) use ($filterKategori) {
                    $bq->where('kategori', 'like', '%' . $filterKategori . '%');
                    if (str_contains(strtolower($filterKategori), 'dana bos') || strtolower($filterKategori) === 'bos') {
                        $bq->orWhereNull('kategori')->orWhere('kategori', '');
                    }
                });
            }

            if ($search !== '') {
                $bosQuery->where(function ($q) use ($search) {
                    $q->where('kategori', 'like', '%' . $search . '%')
                      ->orWhere('keterangan', 'like', '%' . $search . '%');
                });
            }

            FinanceReportService::applyDateFilter($bosQuery, 'tanggal', $filterPeriode, $startDate, $endDate);

            foreach ($bosQuery->get() as $item) {
                $isMasuk = ($item->jenis === 'masuk');
                $nom = (float) $item->nominal;

                $transactions->push((object) [
                    'id' => 'bos_' . $item->id,
                    'raw_id' => $item->id,
                    'tanggal' => $item->tanggal ? Carbon::parse($item->tanggal) : Carbon::now(),
                    'sumber' => 'dana_bos',
                    'sumber_label' => 'Dana BOS',
                    'sumber_badge' => 'amber',
                    'type' => $isMasuk ? 'masuk' : 'keluar',
                    'type_label' => $isMasuk ? 'Pemasukan' : 'Pengeluaran',
                    'type_badge' => $isMasuk ? 'emerald' : 'rose',
                    'kategori' => $item->kategori ?: 'Dana BOS',
                    'keterangan' => $item->keterangan ?: ($isMasuk ? 'Penerimaan Alokasi Dana BOS' : 'Belanja Operasional Dana BOS'),
                    'nominal_masuk' => $isMasuk ? $nom : 0.00,
                    'nominal_keluar' => $isMasuk ? 0.00 : $nom,
                    'nominal' => $nom,
                    'metode_resi' => 'Rekening BOS',
                    'no_resi' => null,
                    'petugas' => 'Tim BOS Sekolah',
                    'bukti' => $item->bukti,
                    'stream' => $isMasuk ? 'dana_bos_masuk' : 'dana_bos_keluar',
                ]);
            }
        }

        // ==========================================
        // 2. ARUS KAS UMUM (INFLOW)
        // ==========================================
        if ($includeKas && $includeMasuk) {
            // A. Pembayaran SPP & Tagihan Siswa
            $sppQuery = Pembayaran::with(['tagihan.siswa.user', 'tagihan.siswa.kelas', 'tagihan.jenisTagihan', 'petugas'])
                ->where('is_void', false)
                ->latest('tanggal_bayar');

            if ($filterKategori !== '') {
                $sppQuery->whereHas('tagihan.jenisTagihan', fn($sq) => $sq->where('nama', 'like', '%' . $filterKategori . '%'));
            }

            if ($search !== '') {
                $sppQuery->where(function ($q) use ($search) {
                    $q->where('no_resi', 'like', '%' . $search . '%')
                      ->orWhereHas('tagihan.siswa.user', fn($sq) => $sq->where('nama', 'like', '%' . $search . '%'))
                      ->orWhereHas('tagihan.jenisTagihan', fn($sq) => $sq->where('nama', 'like', '%' . $search . '%'));
                });
            }

            FinanceReportService::applyDateFilter($sppQuery, 'tanggal_bayar', $filterPeriode, $startDate, $endDate);

            foreach ($sppQuery->get() as $item) {
                $siswaNama = $item->tagihan->siswa->user->nama ?? 'Siswa';
                $kelasNama = $item->tagihan->siswa->kelas->nama_kelas ?? '-';
                $jenisNama = $item->tagihan->jenisTagihan->nama ?? 'Tagihan SPP';
                $bulan = $item->tagihan->bulan ? ' (' . $item->tagihan->bulan . ')' : '';
                $nom = (float) $item->nominal_dibayar;

                $transactions->push((object) [
                    'id' => 'spp_' . $item->id,
                    'raw_id' => $item->id,
                    'tanggal' => $item->tanggal_bayar ? Carbon::parse($item->tanggal_bayar) : Carbon::now(),
                    'sumber' => 'kas_umum',
                    'sumber_label' => 'Kas Sekolah',
                    'sumber_badge' => 'indigo',
                    'type' => 'masuk',
                    'type_label' => 'Pemasukan',
                    'type_badge' => 'emerald',
                    'kategori' => $jenisNama,
                    'keterangan' => "Setoran {$jenisNama} {$siswaNama} (Kelas {$kelasNama}){$bulan}",
                    'nominal_masuk' => $nom,
                    'nominal_keluar' => 0.00,
                    'nominal' => $nom,
                    'metode_resi' => $item->metode_bayar ?: 'Tunai',
                    'no_resi' => $item->no_resi,
                    'petugas' => $item->petugas->nama ?? 'Kasir Pembayaran',
                    'bukti' => $item->bukti_bayar,
                    'stream' => 'pembayaran_spp',
                ]);
            }

            // B. Pemasukan Kas Yayasan (Infaq / Sedekah / Donasi)
            $kasMasukQuery = PemasukanKas::with('petugas')->latest('tanggal');

            if ($filterKategori !== '') {
                $kasMasukQuery->where('kategori', 'like', '%' . $filterKategori . '%');
            }

            if ($search !== '') {
                $kasMasukQuery->where(function ($q) use ($search) {
                    $q->where('kategori', 'like', '%' . $search . '%')
                      ->orWhere('keterangan', 'like', '%' . $search . '%');
                });
            }

            FinanceReportService::applyDateFilter($kasMasukQuery, 'tanggal', $filterPeriode, $startDate, $endDate);

            foreach ($kasMasukQuery->get() as $item) {
                $nom = (float) $item->jumlah;

                $transactions->push((object) [
                    'id' => 'infaq_' . $item->id,
                    'raw_id' => $item->id,
                    'tanggal' => $item->tanggal ? Carbon::parse($item->tanggal) : Carbon::now(),
                    'sumber' => 'kas_umum',
                    'sumber_label' => 'Kas Sekolah',
                    'sumber_badge' => 'indigo',
                    'type' => 'masuk',
                    'type_label' => 'Pemasukan',
                    'type_badge' => 'emerald',
                    'kategori' => $item->kategori ?: 'Pemasukan Kas',
                    'keterangan' => $item->keterangan ?: 'Penerimaan Kas Yayasan / Donasi',
                    'nominal_masuk' => $nom,
                    'nominal_keluar' => 0.00,
                    'nominal' => $nom,
                    'metode_resi' => 'Kas Tunai / Transfer',
                    'no_resi' => null,
                    'petugas' => $item->petugas->nama ?? 'Bendahara',
                    'bukti' => $item->bukti,
                    'stream' => 'kas_yayasan',
                ]);
            }

            // C. Setoran Tabungan Siswa
            if ($filterKategori === '' || str_contains(strtolower($filterKategori), 'tabungan')) {
                $tabQuery = Tabungan::with(['siswa.user', 'siswa.kelas', 'petugas'])
                    ->where('jenis', 'setor')
                    ->latest('tanggal');

                if ($search !== '') {
                    $tabQuery->whereHas('siswa.user', fn($q) => $q->where('nama', 'like', '%' . $search . '%'));
                }

                FinanceReportService::applyDateFilter($tabQuery, 'tanggal', $filterPeriode, $startDate, $endDate);

                foreach ($tabQuery->get() as $item) {
                    $siswaNama = $item->siswa->user->nama ?? 'Siswa';
                    $kelasNama = $item->siswa->kelas->nama_kelas ?? '-';
                    $nom = (float) $item->nominal;

                    $transactions->push((object) [
                        'id' => 'tab_' . $item->id,
                        'raw_id' => $item->id,
                        'tanggal' => $item->tanggal ? Carbon::parse($item->tanggal) : Carbon::now(),
                        'sumber' => 'kas_umum',
                        'sumber_label' => 'Kas Sekolah',
                        'sumber_badge' => 'indigo',
                        'type' => 'masuk',
                        'type_label' => 'Pemasukan',
                        'type_badge' => 'emerald',
                        'kategori' => 'Tabungan Siswa',
                        'keterangan' => "Setoran Tabungan: {$siswaNama} (Kelas {$kelasNama})",
                        'nominal_masuk' => $nom,
                        'nominal_keluar' => 0.00,
                        'nominal' => $nom,
                        'metode_resi' => 'Setoran Tunai',
                        'no_resi' => null,
                        'petugas' => $item->petugas->nama ?? 'Petugas Tabungan',
                        'bukti' => $item->bukti,
                        'stream' => 'tabungan',
                    ]);
                }
            }
        }

        // ==========================================
        // 3. ARUS KAS UMUM (OUTFLOW)
        // ==========================================
        if ($includeKas && $includeKeluar) {
            // A. Beban Operasional Umum Yayasan
            $opQuery = Pengeluaran::with(['kategori', 'petugas'])
                ->whereDoesntHave('gajiGuru')
                ->latest('tanggal');

            if ($filterKategori !== '') {
                $opQuery->whereHas('kategori', fn($sq) => $sq->where('nama', 'like', '%' . $filterKategori . '%'));
            }

            if ($search !== '') {
                $opQuery->where(function ($q) use ($search) {
                    $q->where('keterangan', 'like', '%' . $search . '%')
                      ->orWhereHas('kategori', fn($sq) => $sq->where('nama', 'like', '%' . $search . '%'));
                });
            }

            FinanceReportService::applyDateFilter($opQuery, 'tanggal', $filterPeriode, $startDate, $endDate);

            foreach ($opQuery->get() as $item) {
                $nom = (float) $item->jumlah;

                $transactions->push((object) [
                    'id' => 'op_' . $item->id,
                    'raw_id' => $item->id,
                    'tanggal' => $item->tanggal ? Carbon::parse($item->tanggal) : Carbon::now(),
                    'sumber' => 'kas_umum',
                    'sumber_label' => 'Kas Sekolah',
                    'sumber_badge' => 'indigo',
                    'type' => 'keluar',
                    'type_label' => 'Pengeluaran',
                    'type_badge' => 'rose',
                    'kategori' => $item->kategori->nama ?? 'Operasional Sekolah',
                    'keterangan' => $item->keterangan ?: 'Beban Pengeluaran Operasional',
                    'nominal_masuk' => 0.00,
                    'nominal_keluar' => $nom,
                    'nominal' => $nom,
                    'metode_resi' => 'Kas Tunai / Transfer',
                    'no_resi' => null,
                    'petugas' => $item->petugas->nama ?? 'Bendahara',
                    'bukti' => $item->bukti,
                    'stream' => 'operasional',
                ]);
            }

            // B. Penggajian Guru & Karyawan
            if ($filterKategori === '' || str_contains(strtolower($filterKategori), 'gaji') || str_contains(strtolower($filterKategori), 'honor')) {
                $gajiQuery = GajiGuru::with(['guru.user'])
                    ->where('status', 'dibayar')
                    ->latest('tanggal_bayar');

                if ($search !== '') {
                    $gajiQuery->whereHas('guru.user', fn($q) => $q->where('nama', 'like', '%' . $search . '%'));
                }

                FinanceReportService::applyDateFilter($gajiQuery, 'tanggal_bayar', $filterPeriode, $startDate, $endDate);

                foreach ($gajiQuery->get() as $item) {
                    $nom = (float) $item->total_diterima;
                    $guruNama = $item->guru->user->nama ?? 'Guru / Karyawan';

                    $transactions->push((object) [
                        'id' => 'gaji_' . $item->id,
                        'raw_id' => $item->id,
                        'tanggal' => $item->tanggal_bayar ? Carbon::parse($item->tanggal_bayar) : Carbon::now(),
                        'sumber' => 'kas_umum',
                        'sumber_label' => 'Kas Sekolah',
                        'sumber_badge' => 'indigo',
                        'type' => 'keluar',
                        'type_label' => 'Pengeluaran',
                        'type_badge' => 'rose',
                        'kategori' => 'Gaji & Honorarium',
                        'keterangan' => "Payroll Gaji: {$guruNama} ({$item->bulan} {$item->tahun})",
                        'nominal_masuk' => 0.00,
                        'nominal_keluar' => $nom,
                        'nominal' => $nom,
                        'metode_resi' => 'Payroll Transfer',
                        'no_resi' => null,
                        'petugas' => 'Sistem Payroll',
                        'bukti' => $item->bukti_bayar,
                        'stream' => 'gaji',
                    ]);
                }
            }

            // C. Kasbon / Pinjaman Guru
            if ($filterKategori === '' || str_contains(strtolower($filterKategori), 'kasbon') || str_contains(strtolower($filterKategori), 'pinjam')) {
                $kasbonQuery = Peminjaman::with(['guru.user'])->latest('tanggal_pinjam');

                if ($search !== '') {
                    $kasbonQuery->whereHas('guru.user', fn($q) => $q->where('nama', 'like', '%' . $search . '%'));
                }

                FinanceReportService::applyDateFilter($kasbonQuery, 'tanggal_pinjam', $filterPeriode, $startDate, $endDate);

                foreach ($kasbonQuery->get() as $item) {
                    $nom = (float) $item->nominal;
                    $guruNama = $item->guru->user->nama ?? 'Guru';

                    $transactions->push((object) [
                        'id' => 'loan_' . $item->id,
                        'raw_id' => $item->id,
                        'tanggal' => $item->tanggal_pinjam ? Carbon::parse($item->tanggal_pinjam) : Carbon::now(),
                        'sumber' => 'kas_umum',
                        'sumber_label' => 'Kas Sekolah',
                        'sumber_badge' => 'indigo',
                        'type' => 'keluar',
                        'type_label' => 'Pengeluaran',
                        'type_badge' => 'rose',
                        'kategori' => 'Kasbon Guru',
                        'keterangan' => "Pencairan Kasbon: {$guruNama} (Tenor {$item->tenor_bulan} Bulan)",
                        'nominal_masuk' => 0.00,
                        'nominal_keluar' => $nom,
                        'nominal' => $nom,
                        'metode_resi' => 'Pencairan Kas',
                        'no_resi' => null,
                        'petugas' => 'Bendahara',
                        'bukti' => $item->bukti,
                        'stream' => 'kasbon',
                    ]);
                }
            }
        }

        // ==========================================
        // 4. POST-FILTERING (NOMINAL RANGE)
        // ==========================================
        if ($nominalMin !== null && $nominalMin !== '') {
            $minVal = unmask_rupiah($nominalMin);
            $transactions = $transactions->filter(fn($t) => $t->nominal >= $minVal);
        }

        if ($nominalMax !== null && $nominalMax !== '') {
            $maxVal = unmask_rupiah($nominalMax);
            $transactions = $transactions->filter(fn($t) => $t->nominal <= $maxVal);
        }

        // Sort descending by timestamp
        return $transactions->sortByDesc(fn($item) => $item->tanggal->timestamp)->values();
    }

    /**
     * Compute comprehensive metrics for consolidated transactions.
     *
     * @param Collection $transactions
     * @return array
     */
    public static function calculateMetrics(Collection $transactions): array
    {
        $totalMasuk = (float) $transactions->where('type', 'masuk')->sum('nominal_masuk');
        $totalKeluar = (float) $transactions->where('type', 'keluar')->sum('nominal_keluar');
        $netBalance = $totalMasuk - $totalKeluar;

        // Sub-breakdowns
        $masukKas = (float) $transactions->where('type', 'masuk')->where('sumber', 'kas_umum')->sum('nominal_masuk');
        $masukBos = (float) $transactions->where('type', 'masuk')->where('sumber', 'dana_bos')->sum('nominal_masuk');

        $keluarKas = (float) $transactions->where('type', 'keluar')->where('sumber', 'kas_umum')->sum('nominal_keluar');
        $keluarBos = (float) $transactions->where('type', 'keluar')->where('sumber', 'dana_bos')->sum('nominal_keluar');

        $netKas = $masukKas - $keluarKas;
        $netBos = $masukBos - $keluarBos;

        return [
            'total_masuk' => $totalMasuk,
            'total_keluar' => $totalKeluar,
            'net_balance' => $netBalance,
            'masuk_kas' => $masukKas,
            'masuk_bos' => $masukBos,
            'keluar_kas' => $keluarKas,
            'keluar_bos' => $keluarBos,
            'net_kas' => $netKas,
            'net_bos' => $netBos,
            'count_total' => $transactions->count(),
            'count_masuk' => $transactions->where('type', 'masuk')->count(),
            'count_keluar' => $transactions->where('type', 'keluar')->count(),
            'count_kas' => $transactions->where('sumber', 'kas_umum')->count(),
            'count_bos' => $transactions->where('sumber', 'dana_bos')->count(),
        ];
    }

    /**
     * Calculate monthly trend (last N months) for consolidated cash flow (Kas Sekolah + Dana BOS)
     *
     * @param int $months
     * @return array
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

            // 1. Kas Sekolah Inflow
            $mSpp = (float) Pembayaran::where('is_void', false)->whereYear('tanggal_bayar', $year)->whereMonth('tanggal_bayar', $monthNum)->sum('nominal_dibayar');
            $mInfaq = (float) PemasukanKas::whereYear('tanggal', $year)->whereMonth('tanggal', $monthNum)->sum('jumlah');
            $mTab = (float) Tabungan::where('jenis', 'setor')->whereYear('tanggal', $year)->whereMonth('tanggal', $monthNum)->sum('nominal');
            $kasInflow = $mSpp + $mInfaq + $mTab;

            // 2. Kas Sekolah Outflow
            $mOp = (float) Pengeluaran::whereDoesntHave('gajiGuru')->whereYear('tanggal', $year)->whereMonth('tanggal', $monthNum)->sum('jumlah');
            $mGaji = (float) GajiGuru::where('status', 'dibayar')
                ->where(function ($q) use ($year, $monthNum, $mCarbon) {
                    $q->whereYear('tanggal_bayar', $year)->whereMonth('tanggal_bayar', $monthNum)
                      ->orWhere(function ($sq) use ($year, $mCarbon) {
                          $sq->where('tahun', $year)->where('bulan', $mCarbon->locale('id')->isoFormat('MMMM'));
                      });
                })->sum('total_diterima');
            $mLoan = (float) Peminjaman::whereYear('tanggal_pinjam', $year)->whereMonth('tanggal_pinjam', $monthNum)->sum('nominal');
            $kasOutflow = $mOp + $mGaji + $mLoan;

            // 3. Dana BOS Inflow & Outflow
            $bosInflow = (float) DanaBos::where('jenis', 'masuk')->whereYear('tanggal', $year)->whereMonth('tanggal', $monthNum)->sum('nominal');
            $bosOutflow = (float) DanaBos::where('jenis', 'keluar')->whereYear('tanggal', $year)->whereMonth('tanggal', $monthNum)->sum('nominal');

            // 4. Combined
            $totalInflow = $kasInflow + $bosInflow;
            $totalOutflow = $kasOutflow + $bosOutflow;

            if ($totalInflow > $maxMonthVal) {
                $maxMonthVal = $totalInflow;
            }
            if ($totalOutflow > $maxMonthVal) {
                $maxMonthVal = $totalOutflow;
            }

            $monthlyChartData[] = [
                'label' => $monthLabel,
                'year' => $year,
                'month' => $monthNum,
                'kas_inflow' => $kasInflow,
                'kas_outflow' => $kasOutflow,
                'bos_inflow' => $bosInflow,
                'bos_outflow' => $bosOutflow,
                'inflow' => $totalInflow,
                'outflow' => $totalOutflow,
                'net' => $totalInflow - $totalOutflow,
            ];
        }

        foreach ($monthlyChartData as &$mItem) {
            $mItem['inflow_pct'] = $maxMonthVal > 0 ? round(($mItem['inflow'] / $maxMonthVal) * 100) : 0;
            $mItem['outflow_pct'] = $maxMonthVal > 0 ? round(($mItem['outflow'] / $maxMonthVal) * 100) : 0;
            $mItem['kas_inflow_share'] = $mItem['inflow'] > 0 ? round(($mItem['kas_inflow'] / $mItem['inflow']) * 100) : 0;
            $mItem['bos_inflow_share'] = $mItem['inflow'] > 0 ? round(($mItem['bos_inflow'] / $mItem['inflow']) * 100) : 0;
        }
        unset($mItem);

        return [
            'monthlyChartData' => $monthlyChartData,
            'maxMonthVal' => $maxMonthVal,
        ];
    }

    /**
     * Get available category groups based on active source and flow direction.
     *
     * @param string $sumber 'semua', 'kas_umum', 'dana_bos'
     * @param string $jenis  'semua', 'masuk', 'keluar'
     * @return array<string, array<string>>
     */
    public static function getCategoryGroups(string $sumber = 'semua', string $jenis = 'semua'): array
    {
        $groups = [];

        $includeKas = ($sumber === 'semua' || $sumber === 'kas_umum');
        $includeBos = ($sumber === 'semua' || $sumber === 'dana_bos');
        $includeMasuk = ($jenis === 'semua' || $jenis === 'masuk');
        $includeKeluar = ($jenis === 'semua' || $jenis === 'keluar');

        if ($includeKas && $includeMasuk) {
            $kasIn = array_values(array_unique(array_filter(array_merge(
                JenisTagihan::distinct()->pluck('nama')->toArray(),
                PemasukanKas::distinct()->pluck('kategori')->toArray(),
                ['Tabungan Siswa']
            ))));
            sort($kasIn);
            if (!empty($kasIn)) {
                $groups['Kas Operasional (Pemasukan)'] = $kasIn;
            }
        }

        if ($includeKas && $includeKeluar) {
            $kasOut = array_values(array_unique(array_filter(array_merge(
                KategoriPengeluaran::where('nama', '!=', 'Dana BOS')->where('nama', '!=', 'Gaji Guru')->pluck('nama')->toArray(),
                ['Gaji & Honorarium', 'Kasbon Guru']
            ))));
            sort($kasOut);
            if (!empty($kasOut)) {
                $groups['Kas Operasional (Pengeluaran)'] = $kasOut;
            }
        }

        if ($includeBos) {
            $bosQuery = DanaBos::query()->distinct()->whereNotNull('kategori')->where('kategori', '!=', '');
            if ($jenis === 'masuk') {
                $bosQuery->where('jenis', 'masuk');
            } elseif ($jenis === 'keluar') {
                $bosQuery->where('jenis', 'keluar');
            }
            $bosCats = $bosQuery->pluck('kategori')->filter()->unique()->sort()->values()->all();
            if (!empty($bosCats)) {
                $label = match ($jenis) {
                    'masuk' => 'Dana BOS (Penerimaan)',
                    'keluar' => 'Dana BOS (Belanja)',
                    default => 'Dana BOS',
                };
                $groups[$label] = $bosCats;
            }
        }

        return $groups;
    }
}
