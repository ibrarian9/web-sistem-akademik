<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\GajiGuru;
use App\Models\Guru;
use App\Models\Peminjaman;
use App\Models\Pengeluaran;
use Illuminate\Support\Facades\DB;

class DetailGajiGuru extends Component
{
    use WithPagination;

    public int $guruId;
    public ?Guru $guru = null;

    // Filters
    public string $filterTahun = '';
    public string $filterBulan = '';
    public string $filterStatus = '';
    public string $search = '';

    // Bulk selection
    public array $selectedGajiIds = [];
    public bool $selectAll = false;

    // Modals
    public bool $showDetailModal = false;
    public ?GajiGuru $selectedSalaryDetail = null;

    public bool $showPreviewModal = false;
    public ?int $previewSalaryId = null;

    protected $queryString = [
        'filterTahun' => ['except' => ''],
        'filterBulan' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'search' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public function mount(int $guruId)
    {
        $this->guruId = $guruId;
        $this->guru = Guru::with(['user', 'peminjamans'])->findOrFail($guruId);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterTahun()
    {
        $this->resetPage();
    }

    public function updatedFilterBulan()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $query = $this->getBaseQuery();
            $this->selectedGajiIds = $query->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedGajiIds = [];
        }
    }

    public function clearFilters()
    {
        $this->filterTahun = '';
        $this->filterBulan = '';
        $this->filterStatus = '';
        $this->search = '';
        $this->resetPage();
        $this->selectedGajiIds = [];
        $this->selectAll = false;
    }

    public function openDetailModal(int $id)
    {
        $this->selectedSalaryDetail = GajiGuru::with(['guru.user', 'pengeluaran'])->find($id);
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedSalaryDetail = null;
    }

    public function openPreview(int $id)
    {
        $this->previewSalaryId = $id;
        $this->showPreviewModal = true;
    }

    public function closePreview()
    {
        $this->showPreviewModal = false;
        $this->previewSalaryId = null;
    }

    public function deleteSalary(int $id)
    {
        $gaji = GajiGuru::where('guru_id', $this->guruId)->findOrFail($id);

        DB::transaction(function () use ($gaji) {
            if ($gaji->status === 'dibayar') {
                if ($gaji->pengeluaran_id) {
                    $pengeluaran = Pengeluaran::find($gaji->pengeluaran_id);
                    if ($pengeluaran) {
                        $pengeluaran->delete();
                    }
                }

                if ($gaji->potongan_peminjaman > 0) {
                    $loan = Peminjaman::where('guru_id', $gaji->guru_id)->first();
                    if ($loan) {
                        $loan->update([
                            'sisa_pinjaman' => $loan->sisa_pinjaman + $gaji->potongan_peminjaman,
                            'status' => 'berjalan'
                        ]);
                    }
                }
            }

            $gaji->delete();
        });

        $this->selectedGajiIds = array_values(array_diff($this->selectedGajiIds, [(string)$id]));
        session()->flash('message', 'Data gaji berhasil dihapus.');
    }

    public function deleteSelected()
    {
        if (empty($this->selectedGajiIds)) {
            return;
        }

        $salaries = GajiGuru::where('guru_id', $this->guruId)
            ->whereIn('id', $this->selectedGajiIds)
            ->get();
        $count = $salaries->count();

        DB::transaction(function () use ($salaries) {
            foreach ($salaries as $gaji) {
                if ($gaji->status === 'dibayar') {
                    if ($gaji->pengeluaran_id) {
                        $pengeluaran = Pengeluaran::find($gaji->pengeluaran_id);
                        if ($pengeluaran) {
                            $pengeluaran->delete();
                        }
                    }

                    if ($gaji->potongan_peminjaman > 0) {
                        $loan = Peminjaman::where('guru_id', $gaji->guru_id)->first();
                        if ($loan) {
                            $loan->update([
                                'sisa_pinjaman' => $loan->sisa_pinjaman + $gaji->potongan_peminjaman,
                                'status' => 'berjalan'
                            ]);
                        }
                    }
                }

                $gaji->delete();
            }
        });

        $this->selectedGajiIds = [];
        $this->selectAll = false;
        session()->flash('message', "Berhasil menghapus {$count} data riwayat gaji terpilih.");
    }

    protected function getBaseQuery()
    {
        $query = GajiGuru::with(['guru.user', 'pengeluaran'])
            ->where('guru_id', $this->guruId);

        if ($this->filterTahun) {
            $query->where('tahun', $this->filterTahun);
        }

        if ($this->filterBulan) {
            $query->where('bulan', $this->filterBulan);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->search) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('catatan', 'like', $searchTerm)
                  ->orWhere('jabatan', 'like', $searchTerm)
                  ->orWhere('sumber_dana', 'like', $searchTerm);
            });
        }

        return $query;
    }

    public function render()
    {
        // 1. Base query for stats and records of this teacher
        $statQuery = $this->getBaseQuery();
        $statRecords = (clone $statQuery)->get();

        // 2. Metrics calculation for this teacher
        $paidRecords = $statRecords->where('status', 'dibayar');
        $statTotalDibayar = $paidRecords->sum('total_diterima');
        $statTotalPokok = $statRecords->sum('gaji_pokok') + $statRecords->sum('gaji_berkala');
        $statTotalInsentif = $statRecords->sum(function ($sal) {
            return $sal->insentif + $sal->honor_ekskul + $sal->insentif_bpjs + $sal->insentif_maghrib_mengaji;
        });
        $statTotalKasbon = $paidRecords->sum('potongan_peminjaman');
        $statCountDibayar = $paidRecords->count();
        $statCountDraft = $statRecords->where('status', 'draft')->count();
        $statTotalRecords = $statRecords->count();

        // Active Loans / Kasbon Info for this teacher
        $activeLoan = Peminjaman::where('guru_id', $this->guruId)
            ->where('status', 'disetujui')
            ->where('sisa_pinjaman', '>', 0)
            ->first();

        // 3. Paginated records
        $salaries = $this->getBaseQuery()
            ->orderBy('tahun', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        $listBulan = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        return view('livewire.finance.detail-gaji-guru', [
            'salaries' => $salaries,
            'listBulan' => $listBulan,
            'statTotalDibayar' => $statTotalDibayar,
            'statTotalPokok' => $statTotalPokok,
            'statTotalInsentif' => $statTotalInsentif,
            'statTotalKasbon' => $statTotalKasbon,
            'statCountDibayar' => $statCountDibayar,
            'statCountDraft' => $statCountDraft,
            'statTotalRecords' => $statTotalRecords,
            'activeLoan' => $activeLoan,
        ])->layout('components.layouts.app', ['title' => 'Riwayat Gaji: ' . ($this->guru->user->nama ?? 'Guru') . ' - Yayasan F3']);
    }
}
