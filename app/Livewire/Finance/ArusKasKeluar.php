<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;
use App\Models\GajiGuru;
use App\Models\Peminjaman;
use App\Traits\WithDateFilter;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class ArusKasKeluar extends Component
{
    use WithPagination, WithDateFilter;

    // Stream selector: 'semua', 'operasional', 'gaji', 'peminjaman' (Dana BOS dipisah)
    public string $stream = 'semua';

    // Modal state for recording new operational cash outflow
    public bool $showCreateModal = false;

    // Filters
    public ?int $filterKategori = null;
    public string $search = '';

    // Bulk selection
    public array $selectedIds = [];
    public bool $selectAll = false;

    // Create Expense Form properties
    public ?int $kategori_pengeluaran_id = null;
    public float $jumlah = 0.00;
    public string $tanggal = '';
    public string $keterangan = '';

    public array $categories = [];

    protected $queryString = [
        'stream' => ['except' => 'semua'],
        'filterPeriode' => ['except' => 'semua'],
        'startDate' => ['except' => null],
        'endDate' => ['except' => null],
        'filterKategori' => ['except' => null],
        'search' => ['except' => ''],
    ];

    protected $rules = [
        'kategori_pengeluaran_id' => 'required|exists:kategori_pengeluaran,id',
        'jumlah' => 'required|numeric|min:1000',
        'tanggal' => 'required|date',
        'keterangan' => 'nullable|string|max:500',
    ];

    public function mount()
    {
        $this->categories = KategoriPengeluaran::orderBy('nama')->get()->toArray();
        $this->tanggal = date('Y-m-d');
        if (!empty($this->categories)) {
            $this->kategori_pengeluaran_id = $this->categories[0]['id'];
        }
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
        $query = Pengeluaran::query();

        if ($this->filterKategori) {
            $query->where('kategori_pengeluaran_id', $this->filterKategori);
        }

        if ($this->search !== '') {
            $query->where('keterangan', 'like', '%' . $this->search . '%');
        }

        $this->applyDateFilter($query, 'tanggal');

        return $query->pluck('id')->map(fn($id) => (string) $id)->toArray();
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->reset(['jumlah', 'keterangan']);
        $this->tanggal = date('Y-m-d');
        if (!empty($this->categories)) {
            $this->kategori_pengeluaran_id = $this->categories[0]['id'];
        }
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    public function saveExpense()
    {
        $this->validate();

        Pengeluaran::create([
            'kategori_pengeluaran_id' => $this->kategori_pengeluaran_id,
            'jumlah' => $this->jumlah,
            'tanggal' => $this->tanggal,
            'keterangan' => $this->keterangan,
            'petugas_id' => auth()->id(),
        ]);

        session()->flash('message', 'Pengeluaran kas operasional yayasan berhasil dicatat.');

        $this->showCreateModal = false;
        $this->reset(['jumlah', 'keterangan']);
        $this->tanggal = date('Y-m-d');
        $this->resetPage();
    }

    public function deleteExpense(int $id)
    {
        $item = Pengeluaran::findOrFail($id);
        $item->delete();

        session()->flash('message', 'Catatan pengeluaran kas berhasil dihapus.');
    }

    public function bulkDelete()
    {
        if (empty($this->selectedIds)) {
            return;
        }

        $count = Pengeluaran::whereIn('id', $this->selectedIds)->delete();
        session()->flash('message', "Berhasil menghapus {$count} catatan pengeluaran kas.");

        $this->resetSelection();
        $this->resetPage();
    }

    public function exportPdf()
    {
        $query = Pengeluaran::with(['kategori', 'petugas'])->orderBy('tanggal', 'desc');

        if ($this->filterKategori) {
            $query->where('kategori_pengeluaran_id', $this->filterKategori);
        }

        if ($this->search !== '') {
            $query->where('keterangan', 'like', '%' . $this->search . '%');
        }

        $this->applyDateFilter($query, 'tanggal');

        $data = $query->get();

        if ($data->isEmpty()) {
            session()->flash('error', 'Tidak dapat mengunduh PDF karena tidak ada catatan pengeluaran pada periode terpilih.');
            return;
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('livewire.shared.laporan.pdf-laporan-kas-keluar', [
            'data' => $data,
            'kategori' => $this->filterKategori ? (\App\Models\KategoriPengeluaran::find($this->filterKategori)?->nama ?? 'Semua') : 'Semua Kategori',
            'totalPengeluaran' => $data->sum('jumlah'),
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'laporan_kas_keluar_' . date('Ymd_His') . '.pdf');
    }

    public function exportExcel()
    {
        $query = Pengeluaran::with(['kategori', 'petugas'])->orderBy('tanggal', 'desc');

        if ($this->filterKategori) {
            $query->where('kategori_pengeluaran_id', $this->filterKategori);
        }

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('keterangan', 'like', '%' . $this->search . '%')
                  ->orWhereHas('kategori', function ($kq) {
                      $kq->where('nama', 'like', '%' . $this->search . '%');
                  });
            });
        }

        $this->applyDateFilter($query, 'tanggal');

        $data = $query->get();

        if ($data->isEmpty()) {
            session()->flash('error', 'Tidak ada data pengeluaran kas untuk diekspor ke Excel.');
            return;
        }

        $filename = 'rekap-kas-keluar-' . date('Y-m-d') . '.csv';

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
                'Kategori Pengeluaran',
                'Nominal (Rp)',
                'Keterangan',
                'Petugas Pencatat'
            ]);

            foreach ($data as $index => $item) {
                fputcsv($file, [
                    $index + 1,
                    $item->tanggal ? Carbon::parse($item->tanggal)->translatedFormat('d M Y') : '-',
                    $item->kategori->nama ?? 'Umum',
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
        // 1. Calculate Summary Metrics for the Active Date Filter (Tanpa Dana BOS)
        // Operational Outflow (Yayasan)
        $opQuery = Pengeluaran::query();
        $this->applyDateFilter($opQuery, 'tanggal');
        $totalOperasional = (float) $opQuery->sum('jumlah');

        // Teacher Payroll Outflow
        $gajiQuery = GajiGuru::where('status', 'dibayar');
        $this->applyDateFilter($gajiQuery, 'tanggal_bayar');
        $totalGaji = (float) $gajiQuery->sum('total_diterima');

        // Teacher Loans (Kasbon) Outflow
        $loanQuery = Peminjaman::query();
        $this->applyDateFilter($loanQuery, 'tanggal_pinjam');
        $totalPeminjaman = (float) $loanQuery->sum('nominal');

        $totalOutflowAll = $totalOperasional + $totalGaji + $totalPeminjaman;

        // 2. Compute 6-Month Outflow Trend for Interactive Chart
        $monthlyChartData = [];
        $maxMonthTotal = 1;

        for ($i = 5; $i >= 0; $i--) {
            $monthCarbon = Carbon::now()->subMonths($i);
            $year = $monthCarbon->year;
            $monthNum = $monthCarbon->month;
            $monthLabel = $monthCarbon->locale('id')->isoFormat('MMM YYYY');

            $mOp = (float) Pengeluaran::whereYear('tanggal', $year)->whereMonth('tanggal', $monthNum)->sum('jumlah');
            $mGaji = (float) GajiGuru::where('status', 'dibayar')
                ->where(function ($q) use ($year, $monthNum, $monthCarbon) {
                    $q->whereYear('tanggal_bayar', $year)->whereMonth('tanggal_bayar', $monthNum)
                      ->orWhere(function ($sq) use ($year, $monthCarbon) {
                          $sq->where('tahun', $year)->where('bulan', $monthCarbon->locale('id')->isoFormat('MMMM'));
                      });
                })->sum('total_diterima');
            $mLoan = (float) Peminjaman::whereYear('tanggal_pinjam', $year)->whereMonth('tanggal_pinjam', $monthNum)->sum('nominal');

            $mTotal = $mOp + $mGaji + $mLoan;
            if ($mTotal > $maxMonthTotal) {
                $maxMonthTotal = $mTotal;
            }

            $monthlyChartData[] = [
                'label' => $monthLabel,
                'year' => $year,
                'month' => $monthNum,
                'operasional' => $mOp,
                'gaji' => $mGaji,
                'peminjaman' => $mLoan,
                'total' => $mTotal,
            ];
        }

        // Add percentage height for CSS bar chart
        foreach ($monthlyChartData as &$mItem) {
            $mItem['height_percentage'] = $maxMonthTotal > 0 ? round(($mItem['total'] / $maxMonthTotal) * 100) : 0;
            $mItem['op_pct'] = $mItem['total'] > 0 ? round(($mItem['operasional'] / $mItem['total']) * 100) : 0;
            $mItem['gaji_pct'] = $mItem['total'] > 0 ? round(($mItem['gaji'] / $mItem['total']) * 100) : 0;
            $mItem['loan_pct'] = $mItem['total'] > 0 ? round(($mItem['peminjaman'] / $mItem['total']) * 100) : 0;
        }
        unset($mItem);

        // 3. Compute Top Expense Category Breakdown
        $categoryBreakdown = [];
        $rawCategories = Pengeluaran::with('kategori')
            ->selectRaw('kategori_pengeluaran_id, sum(jumlah) as total_nominal')
            ->groupBy('kategori_pengeluaran_id')
            ->orderByDesc('total_nominal')
            ->take(5)
            ->get();

        foreach ($rawCategories as $rc) {
            $catName = $rc->kategori->nama ?? 'Umum / Lainnya';
            $catNominal = (float) $rc->total_nominal;
            $catPct = $totalOperasional > 0 ? round(($catNominal / $totalOperasional) * 100, 1) : 0;
            $categoryBreakdown[] = [
                'nama' => $catName,
                'nominal' => $catNominal,
                'percentage' => $catPct,
            ];
        }

        // 4. Query & Unify Outflow Transactions for Data Table (Non-BOS)
        $unifiedItems = collect();

        // Stream 1: Operasional Yayasan
        if ($this->stream === 'semua' || $this->stream === 'operasional') {
            $opTableQuery = Pengeluaran::with(['kategori', 'petugas'])->latest('tanggal');
            if ($this->filterKategori) {
                $opTableQuery->where('kategori_pengeluaran_id', $this->filterKategori);
            }
            if ($this->search !== '') {
                $opTableQuery->where('keterangan', 'like', '%' . $this->search . '%');
            }
            $this->applyDateFilter($opTableQuery, 'tanggal');

            foreach ($opTableQuery->get() as $item) {
                $unifiedItems->push((object) [
                    'id' => 'op_' . $item->id,
                    'raw_id' => $item->id,
                    'tanggal' => $item->tanggal ? Carbon::parse($item->tanggal) : Carbon::now(),
                    'stream' => 'operasional',
                    'stream_label' => 'Operasional Yayasan',
                    'stream_badge' => 'rose',
                    'kategori' => $item->kategori->nama ?? 'Umum',
                    'keterangan' => $item->keterangan ?: 'Beban operasional kas yayasan',
                    'nominal' => (float) $item->jumlah,
                    'petugas' => $item->petugas->nama ?? 'Bendahara',
                    'can_delete' => true,
                ]);
            }
        }

        // Stream 2: Gaji & Honor Guru
        if ($this->stream === 'semua' || $this->stream === 'gaji') {
            $gajiTableQuery = GajiGuru::with(['guru.user'])->where('status', 'dibayar')->latest('tanggal_bayar');
            if ($this->search !== '') {
                $gajiTableQuery->whereHas('guru.user', function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%');
                });
            }
            $this->applyDateFilter($gajiTableQuery, 'tanggal_bayar');

            foreach ($gajiTableQuery->get() as $item) {
                $unifiedItems->push((object) [
                    'id' => 'gaji_' . $item->id,
                    'raw_id' => $item->id,
                    'tanggal' => $item->tanggal_bayar ? Carbon::parse($item->tanggal_bayar) : Carbon::now(),
                    'stream' => 'gaji',
                    'stream_label' => 'Gaji & Honor Guru',
                    'stream_badge' => 'purple',
                    'kategori' => 'Honorarium & Gaji',
                    'keterangan' => 'Gaji ' . ($item->guru->user->nama ?? 'Guru') . ' (' . $item->bulan . ' ' . $item->tahun . ')',
                    'nominal' => (float) $item->total_diterima,
                    'petugas' => 'Sistem Payroll',
                    'can_delete' => false,
                ]);
            }
        }

        // Stream 3: Peminjaman / Kasbon Guru
        if ($this->stream === 'semua' || $this->stream === 'peminjaman') {
            $loanTableQuery = Peminjaman::with(['guru.user'])->latest('tanggal_pinjam');
            if ($this->search !== '') {
                $loanTableQuery->whereHas('guru.user', function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%');
                });
            }
            $this->applyDateFilter($loanTableQuery, 'tanggal_pinjam');

            foreach ($loanTableQuery->get() as $item) {
                $unifiedItems->push((object) [
                    'id' => 'loan_' . $item->id,
                    'raw_id' => $item->id,
                    'tanggal' => $item->tanggal_pinjam ? Carbon::parse($item->tanggal_pinjam) : Carbon::now(),
                    'stream' => 'peminjaman',
                    'stream_label' => 'Kasbon / Pinjaman Guru',
                    'stream_badge' => 'emerald',
                    'kategori' => 'Fasilitas Kasbon',
                    'keterangan' => 'Pencairan kasbon: ' . ($item->guru->user->nama ?? 'Guru') . ' (Tenor ' . $item->tenor_bulan . ' Bln)',
                    'nominal' => (float) $item->nominal,
                    'petugas' => 'Finance',
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
        $paginatedOutflows = new LengthAwarePaginator(
            $currentItems,
            $sortedItems->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return view('livewire.finance.arus-kas-keluar', [
            'paginatedOutflows' => $paginatedOutflows,
            'totalOutflowAll' => $totalOutflowAll,
            'totalOperasional' => $totalOperasional,
            'totalGaji' => $totalGaji,
            'totalPeminjaman' => $totalPeminjaman,
            'monthlyChartData' => $monthlyChartData,
            'maxMonthTotal' => $maxMonthTotal,
            'categoryBreakdown' => $categoryBreakdown,
        ])->layout('components.layouts.app', ['title' => 'Gabungan Arus Kas Keluar']);
    }
}
