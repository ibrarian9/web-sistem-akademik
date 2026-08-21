<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;
use App\Models\PemasukanKas;
use App\Models\Pembayaran;
use App\Models\Tabungan;
use App\Models\GajiGuru;
use App\Models\Peminjaman;
use App\Traits\WithDateFilter;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class ArusKas extends Component
{
    use WithPagination, WithDateFilter;

    // Active Tab: 'semua', 'masuk', 'keluar'
    public string $tab = 'semua';

    // Stream selector: 'semua', 'spp', 'infaq', 'tabungan', 'operasional', 'gaji', 'kasbon'
    public string $stream = 'semua';

    // Filters
    public string $search = '';
    public ?int $filterKategoriKeluar = null;
    public string $filterKategoriMasuk = '';

    // Modals
    public bool $showIncomeModal = false;
    public bool $showExpenseModal = false;

    // Form: Kas Masuk Yayasan
    public string $kategori_masuk = 'Infaq';
    public float $jumlah_masuk = 0.00;
    public string $tanggal_masuk = '';
    public string $keterangan_masuk = '';

    // Form: Kas Keluar Operasional
    public ?int $kategori_pengeluaran_id = null;
    public float $jumlah_keluar = 0.00;
    public string $tanggal_keluar = '';
    public string $keterangan_keluar = '';

    // Aliases for compatibility
    public float $jumlah = 0.00;
    public string $tanggal = '';
    public string $keterangan = '';
    public string $kategori = 'Infaq';

    public array $kategoriKeluarOptions = [];
    public array $kategoriMasukOptions = [
        'Infaq',
        'Sedekah Subuh',
        'Maghrib Mengaji',
        'Donasi',
        'Sponsor / Acara',
        'Hibah Yayasan',
        'Lainnya'
    ];

    protected $queryString = [
        'tab' => ['except' => 'semua'],
        'stream' => ['except' => 'semua'],
        'filterPeriode' => ['except' => 'semua'],
        'startDate' => ['except' => null],
        'endDate' => ['except' => null],
        'search' => ['except' => ''],
    ];

    public function mount()
    {
        $this->kategoriKeluarOptions = KategoriPengeluaran::orderBy('nama')->get()->toArray();
        if (!empty($this->kategoriKeluarOptions)) {
            $this->kategori_pengeluaran_id = $this->kategoriKeluarOptions[0]['id'];
        }
        $this->tanggal_masuk = date('Y-m-d');
        $this->tanggal_keluar = date('Y-m-d');
        $this->tanggal = date('Y-m-d');
    }

    public function selectTab(string $tab)
    {
        $this->tab = $tab;
        $this->stream = 'semua';
        $this->resetPage();
    }

    public function selectStream(string $stream)
    {
        $this->stream = $stream;
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterKategoriKeluar()
    {
        $this->resetPage();
    }

    public function updatingFilterKategoriMasuk()
    {
        $this->resetPage();
    }

    // Modal Kas Masuk
    public function openIncomeModal()
    {
        $this->resetValidation();
        $this->reset(['jumlah_masuk', 'keterangan_masuk']);
        $this->tanggal_masuk = date('Y-m-d');
        $this->kategori_masuk = 'Infaq';
        $this->showIncomeModal = true;
    }

    public function closeIncomeModal()
    {
        $this->showIncomeModal = false;
    }

    public function saveIncome()
    {
        $kat = $this->kategori_masuk ?: $this->kategori;
        $amount = $this->jumlah_masuk ?: $this->jumlah;
        $date = $this->tanggal_masuk ?: ($this->tanggal ?: date('Y-m-d'));
        $desc = $this->keterangan_masuk ?: $this->keterangan;

        $this->kategori_masuk = $kat;
        $this->jumlah_masuk = $amount;
        $this->tanggal_masuk = $date;
        $this->keterangan_masuk = $desc;

        $this->validate([
            'kategori_masuk' => 'required|string',
            'jumlah_masuk' => 'required|numeric|min:1000',
            'tanggal_masuk' => 'required|date',
            'keterangan_masuk' => 'nullable|string|max:500',
        ]);

        PemasukanKas::create([
            'kategori' => $kat,
            'jumlah' => $amount,
            'tanggal' => $date,
            'keterangan' => $desc,
            'petugas_id' => auth()->id(),
        ]);

        session()->flash('message', 'Penerimaan kas masuk yayasan berhasil dicatat.');
        $this->showIncomeModal = false;
        $this->reset(['jumlah_masuk', 'keterangan_masuk', 'jumlah', 'keterangan']);
        $this->resetPage();
    }

    public function deleteIncome(int $id)
    {
        $item = PemasukanKas::findOrFail($id);
        $item->delete();
        session()->flash('message', 'Catatan kas masuk yayasan berhasil dihapus.');
    }

    // Modal Kas Keluar
    public function openExpenseModal()
    {
        $this->resetValidation();
        $this->reset(['jumlah_keluar', 'keterangan_keluar', 'jumlah', 'keterangan']);
        $this->tanggal_keluar = date('Y-m-d');
        $this->tanggal = date('Y-m-d');
        if (!empty($this->kategoriKeluarOptions)) {
            $this->kategori_pengeluaran_id = $this->kategoriKeluarOptions[0]['id'];
        }
        $this->showExpenseModal = true;
    }

    public function closeExpenseModal()
    {
        $this->showExpenseModal = false;
    }

    public function saveExpense()
    {
        $amount = $this->jumlah_keluar > 0 ? $this->jumlah_keluar : $this->jumlah;
        $date = $this->tanggal_keluar ?: ($this->tanggal ?: date('Y-m-d'));
        $desc = $this->keterangan_keluar ?: $this->keterangan;

        $this->jumlah_keluar = $amount;
        $this->tanggal_keluar = $date;
        $this->keterangan_keluar = $desc;

        $this->validate([
            'kategori_pengeluaran_id' => 'required|exists:kategori_pengeluaran,id',
            'jumlah_keluar' => 'required|numeric|min:1000',
            'tanggal_keluar' => 'required|date',
            'keterangan_keluar' => 'nullable|string|max:500',
        ]);

        Pengeluaran::create([
            'kategori_pengeluaran_id' => $this->kategori_pengeluaran_id,
            'jumlah' => $amount,
            'tanggal' => $date,
            'keterangan' => $desc,
            'petugas_id' => auth()->id(),
        ]);

        session()->flash('message', 'Pengeluaran kas operasional yayasan berhasil dicatat.');
        $this->showExpenseModal = false;
        $this->reset(['jumlah_keluar', 'keterangan_keluar', 'jumlah', 'keterangan']);
        $this->resetPage();
    }

    public function deleteExpense(int $id)
    {
        $item = Pengeluaran::findOrFail($id);
        $item->delete();
        session()->flash('message', 'Catatan pengeluaran kas berhasil dihapus.');
    }

    public function exportPdf()
    {
        // 1. Gather all transactions for active date filter
        $inflowQuery = PemasukanKas::with('petugas')->latest('tanggal');
        $this->applyDateFilter($inflowQuery, 'tanggal');
        $inflows = $inflowQuery->get();

        $outflowQuery = Pengeluaran::with(['kategori', 'petugas'])->latest('tanggal');
        $this->applyDateFilter($outflowQuery, 'tanggal');
        $outflows = $outflowQuery->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('livewire.shared.laporan.pdf-laporan-kas-keluar', [
            'data' => $outflows,
            'kategori' => 'Laporan Arus Kas Terpadu',
            'totalPengeluaran' => $outflows->sum('jumlah'),
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'laporan_arus_kas_' . date('Ymd_His') . '.pdf');
    }

    public function render()
    {
        // 1. Calculate Inflow Metrics (Non-BOS)
        $sppQuery = Pembayaran::where('is_void', false);
        $this->applyDateFilter($sppQuery, 'tanggal_bayar');
        $totalTagihanSpp = (float) $sppQuery->sum('nominal_dibayar');

        $kasMasukQuery = PemasukanKas::query();
        $this->applyDateFilter($kasMasukQuery, 'tanggal');
        $totalKasYayasan = (float) $kasMasukQuery->sum('jumlah');

        $tabunganQuery = Tabungan::where('jenis', 'setor');
        $this->applyDateFilter($tabunganQuery, 'tanggal');
        $totalTabunganSetor = (float) $tabunganQuery->sum('nominal');

        $totalInflow = $totalTagihanSpp + $totalKasYayasan + $totalTabunganSetor;

        // 2. Calculate Outflow Metrics (Non-BOS)
        $opQuery = Pengeluaran::query();
        $this->applyDateFilter($opQuery, 'tanggal');
        $totalOperasional = (float) $opQuery->sum('jumlah');

        $gajiQuery = GajiGuru::where('status', 'dibayar');
        $this->applyDateFilter($gajiQuery, 'tanggal_bayar');
        $totalGaji = (float) $gajiQuery->sum('total_diterima');

        $loanQuery = Peminjaman::query();
        $this->applyDateFilter($loanQuery, 'tanggal_pinjam');
        $totalKasbon = (float) $loanQuery->sum('nominal');

        $totalOutflow = $totalOperasional + $totalGaji + $totalKasbon;

        // Net Cash Flow
        $netCashFlow = $totalInflow - $totalOutflow;

        // 3. Compute 6-Month Inflow vs Outflow Comparison Trend (Chart Data)
        $monthlyChartData = [];
        $maxMonthVal = 1;

        for ($i = 5; $i >= 0; $i--) {
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
            $mOp = (float) Pengeluaran::whereYear('tanggal', $year)->whereMonth('tanggal', $monthNum)->sum('jumlah');
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

        // 4. Query & Unify All Transactions for Data Table
        $transactions = collect();

        // 🟢 INFLOW STREAMS (Include when tab = 'semua' or 'masuk')
        if ($this->tab === 'semua' || $this->tab === 'masuk') {
            // Stream 1: Pembayaran Tagihan / SPP
            if ($this->stream === 'semua' || $this->stream === 'spp') {
                $sppTbl = Pembayaran::with(['tagihan.siswa.user', 'tagihan.siswa.kelas', 'tagihan.jenisTagihan', 'petugas'])
                    ->where('is_void', false)->latest('tanggal_bayar');

                if ($this->search !== '') {
                    $sppTbl->where(function ($q) {
                        $q->where('no_resi', 'like', '%' . $this->search . '%')
                          ->orWhereHas('tagihan.siswa.user', fn($sq) => $sq->where('nama', 'like', '%' . $this->search . '%'))
                          ->orWhereHas('tagihan.jenisTagihan', fn($sq) => $sq->where('nama', 'like', '%' . $this->search . '%'));
                    });
                }
                $this->applyDateFilter($sppTbl, 'tanggal_bayar');

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
                    ]);
                }
            }

            // Stream 2: Kas Masuk Yayasan (Infaq / Donasi)
            if ($this->stream === 'semua' || $this->stream === 'infaq') {
                $kasTbl = PemasukanKas::with('petugas')->latest('tanggal');
                if ($this->filterKategoriMasuk !== '') {
                    $kasTbl->where('kategori', $this->filterKategoriMasuk);
                }
                if ($this->search !== '') {
                    $kasTbl->where(function ($q) {
                        $q->where('kategori', 'like', '%' . $this->search . '%')
                          ->orWhere('keterangan', 'like', '%' . $this->search . '%');
                    });
                }
                $this->applyDateFilter($kasTbl, 'tanggal');

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
                        'metode_resi' => 'Kas Tunai / Transfer',
                        'no_resi' => null,
                        'petugas' => $item->petugas->nama ?? 'Bendahara',
                        'can_delete' => true,
                    ]);
                }
            }

            // Stream 3: Setoran Tabungan Siswa
            if ($this->stream === 'semua' || $this->stream === 'tabungan') {
                $tabTbl = Tabungan::with(['siswa.user', 'siswa.kelas', 'petugas'])->where('jenis', 'setor')->latest('tanggal');
                if ($this->search !== '') {
                    $tabTbl->whereHas('siswa.user', fn($q) => $q->where('nama', 'like', '%' . $this->search . '%'));
                }
                $this->applyDateFilter($tabTbl, 'tanggal');

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
                    ]);
                }
            }
        }

        // 🔴 OUTFLOW STREAMS (Include when tab = 'semua' or 'keluar')
        if ($this->tab === 'semua' || $this->tab === 'keluar') {
            // Stream 4: Operasional Yayasan
            if ($this->stream === 'semua' || $this->stream === 'operasional') {
                $opTbl = Pengeluaran::with(['kategori', 'petugas'])->latest('tanggal');
                if ($this->filterKategoriKeluar) {
                    $opTbl->where('kategori_pengeluaran_id', $this->filterKategoriKeluar);
                }
                if ($this->search !== '') {
                    $opTbl->where('keterangan', 'like', '%' . $this->search . '%');
                }
                $this->applyDateFilter($opTbl, 'tanggal');

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
                        'metode_resi' => 'Kas Tunai / Bank',
                        'no_resi' => null,
                        'petugas' => $item->petugas->nama ?? 'Bendahara',
                        'can_delete' => true,
                    ]);
                }
            }

            // Stream 5: Gaji Guru
            if ($this->stream === 'semua' || $this->stream === 'gaji') {
                $gajiTbl = GajiGuru::with(['guru.user'])->where('status', 'dibayar')->latest('tanggal_bayar');
                if ($this->search !== '') {
                    $gajiTbl->whereHas('guru.user', fn($q) => $q->where('nama', 'like', '%' . $this->search . '%'));
                }
                $this->applyDateFilter($gajiTbl, 'tanggal_bayar');

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
                    ]);
                }
            }

            // Stream 6: Kasbon Guru
            if ($this->stream === 'semua' || $this->stream === 'kasbon') {
                $loanTbl = Peminjaman::with(['guru.user'])->latest('tanggal_pinjam');
                if ($this->search !== '') {
                    $loanTbl->whereHas('guru.user', fn($q) => $q->where('nama', 'like', '%' . $this->search . '%'));
                }
                $this->applyDateFilter($loanTbl, 'tanggal_pinjam');

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
                        'metode_resi' => 'Pencairan Kasbon',
                        'no_resi' => null,
                        'petugas' => 'Finance',
                        'can_delete' => false,
                    ]);
                }
            }
        }

        // Sort unified collection by date descending
        $sortedTransactions = $transactions->sortByDesc(fn($item) => $item->tanggal->timestamp)->values();

        // Paginate manually
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 15;
        $currentItems = $sortedTransactions->slice(($page - 1) * $perPage, $perPage)->values();
        $paginatedTransactions = new LengthAwarePaginator(
            $currentItems,
            $sortedTransactions->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return view('livewire.finance.arus-kas', [
            'paginatedTransactions' => $paginatedTransactions,
            'totalInflow' => $totalInflow,
            'totalOutflow' => $totalOutflow,
            'netCashFlow' => $netCashFlow,
            'totalTagihanSpp' => $totalTagihanSpp,
            'totalKasYayasan' => $totalKasYayasan,
            'totalTabunganSetor' => $totalTabunganSetor,
            'totalOperasional' => $totalOperasional,
            'totalGaji' => $totalGaji,
            'totalKasbon' => $totalKasbon,
            'monthlyChartData' => $monthlyChartData,
            'maxMonthVal' => $maxMonthVal,
        ])->layout('components.layouts.app', ['title' => 'Arus Kas (Cash Flow) Terpadu']);
    }
}
