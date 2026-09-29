<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Traits\WithCurrencySanitizer;
use App\Models\Guru;
use App\Models\GajiGuru;
use App\Models\ApprovalKeuangan;
use App\Livewire\Finance\Concerns\HasSalaryBulkGeneration;
use App\Livewire\Finance\Concerns\HasSalaryCreation;
use App\Livewire\Finance\Concerns\HasSalaryEditing;
use App\Livewire\Finance\Concerns\HasSalaryPayment;
use App\Livewire\Finance\Concerns\HasSalaryDeletion;
use App\Livewire\Finance\Concerns\HasSalaryModals;
use Illuminate\View\View;

class ManajemenGajiGuru extends Component
{
    use WithPagination, WithFileUploads, WithCurrencySanitizer;
    use HasSalaryBulkGeneration;
    use HasSalaryCreation;
    use HasSalaryEditing;
    use HasSalaryPayment;
    use HasSalaryDeletion;
    use HasSalaryModals;

    // Filters
    public string $search = '';
    public string $filterStatus = ''; // 'draft', 'dibayar'
    public string $filterBulan = '';
    public string $filterTahun = '';

    // Bulk Actions State
    public array $selectedGajiIds = [];
    public bool $selectAll = false;

    public array $listBulan = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    public function mount(): void
    {
        $this->generateTahun = intval(date('Y'));
        $this->createTahun = intval(date('Y'));
        $this->filterTahun = date('Y');
        $this->generateBulan = $this->listBulan[intval(date('n')) - 1] ?? 'Januari';
        $this->createBulan = $this->listBulan[intval(date('n')) - 1] ?? 'Januari';
        $this->createTanggalBayar = date('Y-m-d');
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $query = GajiGuru::query();
            if ($this->search) {
                $query->whereHas('guru.user', function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%');
                });
            }
            if ($this->filterStatus) {
                $query->where('status', $this->filterStatus);
            }
            if ($this->filterBulan) {
                $query->where('bulan', $this->filterBulan);
            }
            if ($this->filterTahun) {
                $query->where('tahun', $this->filterTahun);
            }
            $this->selectedGajiIds = $query->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedGajiIds = [];
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterBulan(): void
    {
        $this->resetPage();
    }

    public function updatingFilterTahun(): void
    {
        $this->resetPage();
    }

    public function updatedCreateBulan(): void
    {
        if ($this->createGuruId) {
            $guru = \App\Models\Guru::with('user')->find($this->createGuruId);
            if ($guru) {
                $this->populateCreateDefaults($guru);
            }
        }
    }

    public function updatedCreateTahun(): void
    {
        if ($this->createGuruId) {
            $guru = \App\Models\Guru::with('user')->find($this->createGuruId);
            if ($guru) {
                $this->populateCreateDefaults($guru);
            }
        }
    }

    public function updated($propertyName): void
    {
        if (str_starts_with($propertyName, 'create') && !in_array($propertyName, ['createGuruId', 'createBuktiFoto', 'createBulan', 'createTahun'])) {
            $this->calculateCreateTotal();
        } elseif (str_starts_with($propertyName, 'edit') && !in_array($propertyName, ['editGuruId', 'edit_bukti_foto'])) {
            $this->calculateEditTotal();
        } elseif (str_starts_with($propertyName, 'generateItems.')) {
            $parts = explode('.', $propertyName);
            if (isset($parts[1]) && is_numeric($parts[1])) {
                $this->recalculateGenerateRow((int) $parts[1]);
            }
        }
    }

    public function render(): View
    {
        $query = GajiGuru::with(['guru.user', 'pengeluaran'])
            ->searchGuru($this->search ?: null)
            ->filterStatus($this->filterStatus ?: null)
            ->filterPeriod($this->filterBulan ?: null, $this->filterTahun ? intval($this->filterTahun) : null);

        // Base Query for Stats (matching active search & filters)
        $statsQuery = clone $query;
        $allMatchingSalaries = $statsQuery->get();

        $statTotalAnggaran = $allMatchingSalaries->sum('total_diterima');
        $statTotalDibayar = $allMatchingSalaries->where('status', 'dibayar')->sum('total_diterima');
        $statCountDibayar = $allMatchingSalaries->where('status', 'dibayar')->count();
        $statTotalDraft = $allMatchingSalaries->where('status', 'draft')->sum('total_diterima');
        $statCountDraft = $allMatchingSalaries->where('status', 'draft')->count();
        $statTotalKasbon = $allMatchingSalaries->sum('potongan_peminjaman');

        $salaries = $query->latest('id')->paginate(15);
        $activeGurusList = Guru::with('user')->where('status_aktif', true)->get();

        // History data for teacher if history modal is open
        $historySalaries = collect();
        $totalHistoryDibayarkan = 0;
        $totalHistoryKasbon = 0;
        $totalHistoryBulan = 0;

        if ($this->showHistoryModal && $this->historyGuruId) {
            $historyQuery = GajiGuru::where('guru_id', $this->historyGuruId);
            if ($this->historyFilterTahun) {
                $historyQuery->where('tahun', $this->historyFilterTahun);
            }
            $historySalaries = $historyQuery->orderBy('tahun', 'desc')->orderBy('id', 'desc')->get();
            $paidHistories = $historySalaries->where('status', 'dibayar');
            $totalHistoryDibayarkan = $paidHistories->sum('total_diterima');
            $totalHistoryKasbon = $paidHistories->sum('potongan_peminjaman');
            $totalHistoryBulan = $paidHistories->count();
        }

        $pendingApprovalIds = ApprovalKeuangan::where('model_type', GajiGuru::class)
            ->where('status', 'menunggu')
            ->pluck('model_id')
            ->toArray();

        return view('livewire.finance.manajemen-gaji-guru', [
            'salaries' => $salaries,
            'pendingApprovalIds' => $pendingApprovalIds,
            'activeGurusList' => $activeGurusList,
            'historySalaries' => $historySalaries,
            'totalHistoryDibayarkan' => $totalHistoryDibayarkan,
            'totalHistoryKasbon' => $totalHistoryKasbon,
            'totalHistoryBulan' => $totalHistoryBulan,
            'statTotalAnggaran' => $statTotalAnggaran,
            'statTotalDibayar' => $statTotalDibayar,
            'statCountDibayar' => $statCountDibayar,
            'statTotalDraft' => $statTotalDraft,
            'statCountDraft' => $statCountDraft,
            'statTotalKasbon' => $statTotalKasbon,
        ])->layout('components.layouts.app', ['title' => 'Manajemen Gaji Guru - Yayasan F3']);
    }
}
