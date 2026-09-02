<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Models\PemasukanKas;
use App\Models\Pembayaran;
use App\Models\Tabungan;
use App\Traits\WithDateFilter;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class ArusKasMasuk extends Component
{
    use WithPagination, WithDateFilter;

    // Stream selector: 'semua', 'pembayaran_spp', 'kas_yayasan', 'tabungan'
    public string $stream = 'semua';

    // Modal state
    public bool $showCreateModal = false;

    // Filters
    public string $filterKategori = '';
    public string $search = '';

    // Bulk selection
    public array $selectedIds = [];
    public bool $selectAll = false;

    // Create Income Form properties (Kas Masuk Yayasan)
    public string $kategori = 'Infaq';
    public float $jumlah = 0.00;
    public string $tanggal = '';
    public string $keterangan = '';

    public array $kategoriOptions = [
        'Infaq',
        'Sedekah Subuh',
        'Maghrib Mengaji',
        'Donasi',
        'Sponsor / Acara',
        'Hibah Yayasan',
        'Lainnya'
    ];

    protected $queryString = [
        'stream' => ['except' => 'semua'],
        'filterPeriode' => ['except' => 'semua'],
        'startDate' => ['except' => null],
        'endDate' => ['except' => null],
        'filterKategori' => ['except' => ''],
        'search' => ['except' => ''],
    ];

    protected $rules = [
        'kategori' => 'required|string',
        'jumlah' => 'required|numeric|min:1000',
        'tanggal' => 'required|date',
        'keterangan' => 'nullable|string|max:500',
    ];

    public function mount()
    {
        $this->tanggal = date('Y-m-d');
    }

    public function selectStream(string $stream)
    {
        $this->stream = $stream;
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatingFilterKategori()
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = $this->getCurrentIds();
        } else {
            $this->selectedIds = [];
        }
    }

    public function resetSelection()
    {
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    protected function getCurrentIds(): array
    {
        $query = PemasukanKas::query();

        if ($this->filterKategori !== '') {
            $query->where('kategori', $this->filterKategori);
        }

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('kategori', 'like', '%' . $this->search . '%')
                  ->orWhere('keterangan', 'like', '%' . $this->search . '%');
            });
        }

        $this->applyDateFilter($query, 'tanggal');

        return $query->pluck('id')->map(fn($id) => (string) $id)->toArray();
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->reset(['jumlah', 'keterangan']);
        $this->tanggal = date('Y-m-d');
        $this->kategori = 'Infaq';
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    public function saveIncome()
    {
        $this->validate();

        PemasukanKas::create([
            'kategori' => $this->kategori,
            'jumlah' => $this->jumlah,
            'tanggal' => $this->tanggal,
            'keterangan' => $this->keterangan,
            'petugas_id' => auth()->id(),
        ]);

        session()->flash('message', 'Pemasukan kas yayasan berhasil dicatat.');

        $this->showCreateModal = false;
        $this->reset(['jumlah', 'keterangan']);
        $this->tanggal = date('Y-m-d');
        $this->resetPage();
    }

    public function deleteIncome(int $id)
    {
        $item = PemasukanKas::findOrFail($id);
        $item->delete();

        session()->flash('message', 'Catatan pemasukan kas yayasan berhasil dihapus.');
    }

    public function bulkDelete()
    {
        if (empty($this->selectedIds)) {
            return;
        }

        $count = PemasukanKas::whereIn('id', $this->selectedIds)->delete();
        session()->flash('message', "Berhasil menghapus {$count} catatan pemasukan kas yayasan.");

        $this->resetSelection();
        $this->resetPage();
    }

    public function exportPdf()
    {
        $query = PemasukanKas::with('petugas')->orderBy('tanggal', 'desc');

        if ($this->filterKategori !== '') {
            $query->where('kategori', $this->filterKategori);
        }

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('kategori', 'like', '%' . $this->search . '%')
                  ->orWhere('keterangan', 'like', '%' . $this->search . '%');
            });
        }

        $this->applyDateFilter($query, 'tanggal');

        $data = $query->get();

        if ($data->isEmpty()) {
            session()->flash('error', 'Tidak dapat mengunduh PDF karena tidak ada catatan pemasukan pada periode terpilih.');
            return;
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('livewire.shared.laporan.pdf-laporan-kas-masuk', [
            'data' => $data,
            'kategori' => $this->filterKategori ?: 'Semua Kategori',
            'totalPemasukan' => $data->sum('jumlah'),
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'laporan_kas_masuk_' . date('Ymd_His') . '.pdf');
    }

    public function exportExcel()
    {
        $query = PemasukanKas::with('petugas')->orderBy('tanggal', 'desc');

        if ($this->filterKategori !== '') {
            $query->where('kategori', $this->filterKategori);
        }

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('kategori', 'like', '%' . $this->search . '%')
                  ->orWhere('keterangan', 'like', '%' . $this->search . '%');
            });
        }

        $this->applyDateFilter($query, 'tanggal');

        $data = $query->get();

        if ($data->isEmpty()) {
            session()->flash('error', 'Tidak ada data pemasukan kas untuk diekspor ke Excel.');
            return;
        }

        $filename = 'rekap-kas-masuk-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'No',
                'Tanggal',
                'Kategori Pemasukan',
                'Nominal (Rp)',
                'Keterangan',
                'Petugas Pencatat'
            ]);

            foreach ($data as $index => $item) {
                fputcsv($file, [
                    $index + 1,
                    $item->tanggal ? Carbon::parse($item->tanggal)->translatedFormat('d M Y') : '-',
                    $item->kategori,
                    number_format($item->jumlah, 0, ',', '.'),
                    $item->keterangan ?: '-',
                    $item->petugas->nama ?? 'Sistem'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        // 1. Calculate Summary Metrics for the Active Date Filter (Non-BOS)
        // Stream 1: Tagihan / SPP Siswa
        $sppQuery = Pembayaran::where('is_void', false);
        $this->applyDateFilter($sppQuery, 'tanggal_bayar');
        $totalTagihanSpp = (float) $sppQuery->sum('nominal_dibayar');

        // Stream 2: Kas Masuk Yayasan (Infaq / Sedekah / Donasi)
        $kasQuery = PemasukanKas::query();
        $this->applyDateFilter($kasQuery, 'tanggal');
        $totalKasYayasan = (float) $kasQuery->sum('jumlah');

        // Stream 3: Setoran Tabungan Siswa
        $tabunganQuery = Tabungan::where('jenis', 'setor');
        $this->applyDateFilter($tabunganQuery, 'tanggal');
        $totalTabunganSetor = (float) $tabunganQuery->sum('nominal');

        $totalInflowAll = $totalTagihanSpp + $totalKasYayasan + $totalTabunganSetor;

        // 2. Compute 6-Month Inflow Trend for Interactive Stacked Bar Chart
        $monthlyChartData = [];
        $maxMonthTotal = 1;

        for ($i = 5; $i >= 0; $i--) {
            $monthCarbon = Carbon::now()->subMonths($i);
            $year = $monthCarbon->year;
            $monthNum = $monthCarbon->month;
            $monthLabel = $monthCarbon->locale('id')->isoFormat('MMM YYYY');

            $mSpp = (float) Pembayaran::where('is_void', false)
                ->whereYear('tanggal_bayar', $year)
                ->whereMonth('tanggal_bayar', $monthNum)
                ->sum('nominal_dibayar');

            $mKas = (float) PemasukanKas::whereYear('tanggal', $year)
                ->whereMonth('tanggal', $monthNum)
                ->sum('jumlah');

            $mTab = (float) Tabungan::where('jenis', 'setor')
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $monthNum)
                ->sum('nominal');

            $mTotal = $mSpp + $mKas + $mTab;
            if ($mTotal > $maxMonthTotal) {
                $maxMonthTotal = $mTotal;
            }

            $monthlyChartData[] = [
                'label' => $monthLabel,
                'year' => $year,
                'month' => $monthNum,
                'spp' => $mSpp,
                'kas_yayasan' => $mKas,
                'tabungan' => $mTab,
                'total' => $mTotal,
            ];
        }

        // Add percentage height for CSS bar chart
        foreach ($monthlyChartData as &$mItem) {
            $mItem['height_percentage'] = $maxMonthTotal > 0 ? round(($mItem['total'] / $maxMonthTotal) * 100) : 0;
            $mItem['spp_pct'] = $mItem['total'] > 0 ? round(($mItem['spp'] / $mItem['total']) * 100) : 0;
            $mItem['kas_pct'] = $mItem['total'] > 0 ? round(($mItem['kas_yayasan'] / $mItem['total']) * 100) : 0;
            $mItem['tab_pct'] = $mItem['total'] > 0 ? round(($mItem['tabungan'] / $mItem['total']) * 100) : 0;
        }
        unset($mItem);

        // 3. Compute Top Inflow Category Breakdown
        $categoryBreakdown = [];
        $rawKasCategories = PemasukanKas::selectRaw('kategori, sum(jumlah) as total_nominal')
            ->groupBy('kategori')
            ->orderByDesc('total_nominal')
            ->take(5)
            ->get();

        foreach ($rawKasCategories as $rc) {
            $catNominal = (float) $rc->total_nominal;
            $catPct = $totalKasYayasan > 0 ? round(($catNominal / $totalKasYayasan) * 100, 1) : 0;
            $categoryBreakdown[] = [
                'nama' => $rc->kategori,
                'nominal' => $catNominal,
                'percentage' => $catPct,
            ];
        }

        // 4. Query & Unify Inflow Transactions for Data Table
        $unifiedItems = collect();

        // Stream 1: Pembayaran Tagihan / SPP Siswa
        if ($this->stream === 'semua' || $this->stream === 'pembayaran_spp') {
            $sppTableQuery = Pembayaran::with(['tagihan.siswa.user', 'tagihan.siswa.kelas', 'tagihan.jenisTagihan', 'petugas'])
                ->where('is_void', false)
                ->latest('tanggal_bayar');

            if ($this->search !== '') {
                $sppTableQuery->where(function ($q) {
                    $q->where('no_resi', 'like', '%' . $this->search . '%')
                      ->orWhereHas('tagihan.siswa.user', function ($sq) {
                          $sq->where('nama', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('tagihan.jenisTagihan', function ($sq) {
                          $sq->where('nama', 'like', '%' . $this->search . '%');
                      });
                });
            }
            $this->applyDateFilter($sppTableQuery, 'tanggal_bayar');

            foreach ($sppTableQuery->get() as $item) {
                $siswaNama = $item->tagihan->siswa->user->nama ?? 'Siswa';
                $kelasNama = $item->tagihan->siswa->kelas->nama_kelas ?? '-';
                $jenisNama = $item->tagihan->jenisTagihan->nama ?? 'Tagihan';
                $bulan = $item->tagihan->bulan ? ' (' . $item->tagihan->bulan . ')' : '';

                $unifiedItems->push((object) [
                    'id' => 'spp_' . $item->id,
                    'raw_id' => $item->id,
                    'tanggal' => $item->tanggal_bayar ? Carbon::parse($item->tanggal_bayar) : Carbon::now(),
                    'stream' => 'pembayaran_spp',
                    'stream_label' => 'SPP & Tagihan Siswa',
                    'stream_badge' => 'emerald',
                    'kategori' => $jenisNama,
                    'keterangan' => $siswaNama . ' - Kelas ' . $kelasNama . $bulan,
                    'nominal' => (float) $item->nominal_dibayar,
                    'metode' => $item->metode_bayar ?: 'Tunai',
                    'no_resi' => $item->no_resi,
                    'petugas' => $item->petugas->nama ?? 'Kasir',
                    'can_delete' => false,
                ]);
            }
        }

        // Stream 2: Kas Masuk Yayasan (Infaq / Donasi)
        if ($this->stream === 'semua' || $this->stream === 'kas_yayasan') {
            $kasTableQuery = PemasukanKas::with('petugas')->latest('tanggal');
            if ($this->filterKategori !== '') {
                $kasTableQuery->where('kategori', $this->filterKategori);
            }
            if ($this->search !== '') {
                $kasTableQuery->where(function ($q) {
                    $q->where('kategori', 'like', '%' . $this->search . '%')
                      ->orWhere('keterangan', 'like', '%' . $this->search . '%');
                });
            }
            $this->applyDateFilter($kasTableQuery, 'tanggal');

            foreach ($kasTableQuery->get() as $item) {
                $unifiedItems->push((object) [
                    'id' => 'kas_' . $item->id,
                    'raw_id' => $item->id,
                    'tanggal' => $item->tanggal ? Carbon::parse($item->tanggal) : Carbon::now(),
                    'stream' => 'kas_yayasan',
                    'stream_label' => 'Kas Masuk Yayasan',
                    'stream_badge' => 'amber',
                    'kategori' => $item->kategori,
                    'keterangan' => $item->keterangan ?: 'Penerimaan kas non-SPP yayasan',
                    'nominal' => (float) $item->jumlah,
                    'metode' => 'Kas Tunai / Transfer',
                    'no_resi' => null,
                    'petugas' => $item->petugas->nama ?? 'Bendahara',
                    'can_delete' => true,
                ]);
            }
        }

        // Stream 3: Setoran Tabungan Siswa
        if ($this->stream === 'semua' || $this->stream === 'tabungan') {
            $tabTableQuery = Tabungan::with(['siswa.user', 'siswa.kelas', 'petugas'])
                ->where('jenis', 'setor')
                ->latest('tanggal');

            if ($this->search !== '') {
                $tabTableQuery->whereHas('siswa.user', function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%');
                });
            }
            $this->applyDateFilter($tabTableQuery, 'tanggal');

            foreach ($tabTableQuery->get() as $item) {
                $siswaNama = $item->siswa->user->nama ?? 'Siswa';
                $kelasNama = $item->siswa->kelas->nama_kelas ?? '-';

                $unifiedItems->push((object) [
                    'id' => 'tab_' . $item->id,
                    'raw_id' => $item->id,
                    'tanggal' => $item->tanggal ? Carbon::parse($item->tanggal) : Carbon::now(),
                    'stream' => 'tabungan',
                    'stream_label' => 'Setoran Tabungan',
                    'stream_badge' => 'purple',
                    'kategori' => 'Tabungan Siswa',
                    'keterangan' => 'Setor Tabungan: ' . $siswaNama . ' (Kelas ' . $kelasNama . ')',
                    'nominal' => (float) $item->nominal,
                    'metode' => 'Setoran Tunai',
                    'no_resi' => null,
                    'petugas' => $item->petugas->nama ?? 'Petugas Tabungan',
                    'can_delete' => false,
                ]);
            }
        }

        // Sort unified collection by date descending
        $sortedItems = $unifiedItems->sortByDesc(fn($item) => $item->tanggal->timestamp)->values();

        // Paginate manually
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 15;
        $currentItems = $sortedItems->slice(($page - 1) * $perPage, $perPage)->values();
        $paginatedInflows = new LengthAwarePaginator(
            $currentItems,
            $sortedItems->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return view('livewire.finance.arus-kas-masuk', [
            'paginatedInflows' => $paginatedInflows,
            'totalInflowAll' => $totalInflowAll,
            'totalTagihanSpp' => $totalTagihanSpp,
            'totalKasYayasan' => $totalKasYayasan,
            'totalTabunganSetor' => $totalTabunganSetor,
            'monthlyChartData' => $monthlyChartData,
            'maxMonthTotal' => $maxMonthTotal,
            'categoryBreakdown' => $categoryBreakdown,
        ])->layout('components.layouts.app', ['title' => 'Gabungan Arus Kas Masuk']);
    }
}
