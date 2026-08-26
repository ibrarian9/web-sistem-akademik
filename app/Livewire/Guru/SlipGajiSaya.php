<?php

namespace App\Livewire\Guru;

use App\Models\GajiGuru;
use Livewire\Component;
use Livewire\WithPagination;

class SlipGajiSaya extends Component
{
    use WithPagination;

    public $filterTahun = '';
    public $filterBulan = '';
    public $filterStatus = '';

    // Modal PDF Preview
    public $showPreviewModal = false;
    public $previewSalaryId = null;
    public $previewSalary = null;

    public function mount()
    {
        $this->filterTahun = (string) date('Y');
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

    public function openPreview(int $id)
    {
        $user = auth()->user();
        if (!$user || !$user->guru) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Data guru tidak ditemukan.']);
            return;
        }

        $salary = GajiGuru::with('guru.user')->where('guru_id', $user->guru->id)->findOrFail($id);

        $this->previewSalaryId = $salary->id;
        $this->previewSalary = $salary;
        $this->showPreviewModal = true;
    }

    public function closePreview()
    {
        $this->showPreviewModal = false;
        $this->previewSalaryId = null;
        $this->previewSalary = null;
    }

    public function render()
    {
        $user = auth()->user();
        $guru = $user ? $user->guru : null;

        if (!$guru) {
            return view('livewire.guru.slip-gaji-saya', [
                'salaries' => collect([]),
                'totalTahunIni' => 0,
                'gajiTerakhir' => null,
                'totalKasbonPotong' => 0,
                'availableYears' => [date('Y')],
                'months' => $this->getMonthsList(),
            ]);
        }

        $query = GajiGuru::where('guru_id', $guru->id);

        if (!empty($this->filterTahun)) {
            $query->where('tahun', $this->filterTahun);
        }

        if (!empty($this->filterBulan)) {
            $query->where('bulan', $this->filterBulan);
        }

        if (!empty($this->filterStatus)) {
            $query->where('status', $this->filterStatus);
        }

        $salaries = $query->orderBy('tahun', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        // Summary KPI
        $currentYear = $this->filterTahun ?: date('Y');
        $totalTahunIni = GajiGuru::where('guru_id', $guru->id)
            ->where('tahun', $currentYear)
            ->where('status', 'dibayar')
            ->sum('total_diterima');

        $totalKasbonPotong = GajiGuru::where('guru_id', $guru->id)
            ->where('tahun', $currentYear)
            ->where('status', 'dibayar')
            ->sum('potongan_peminjaman');

        $gajiTerakhir = GajiGuru::where('guru_id', $guru->id)
            ->where('status', 'dibayar')
            ->orderBy('id', 'desc')
            ->first();

        $availableYears = GajiGuru::where('guru_id', $guru->id)
            ->distinct()
            ->pluck('tahun')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [date('Y')];
        } else {
            if (!in_array(date('Y'), $availableYears)) {
                $availableYears[] = date('Y');
            }
            rsort($availableYears);
        }

        return view('livewire.guru.slip-gaji-saya', [
            'salaries' => $salaries,
            'totalTahunIni' => $totalTahunIni,
            'gajiTerakhir' => $gajiTerakhir,
            'totalKasbonPotong' => $totalKasbonPotong,
            'availableYears' => $availableYears,
            'months' => $this->getMonthsList(),
        ]);
    }

    private function getMonthsList(): array
    {
        return [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
    }
}
