<?php

namespace App\Livewire\Finance\Concerns;

use App\Services\Finance\SalaryBulkGeneratorService;

trait HasSalaryBulkGeneration
{
    public bool $showGenerateModal = false;
    public string $generateBulan = 'Januari';
    public int $generateTahun = 2026;
    public array $generateItems = [];
    public bool $generateSelectAll = true;

    public function openGenerateModal(): void
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        if (!empty($this->filterBulan)) {
            $this->generateBulan = $this->filterBulan;
        }
        if (!empty($this->filterTahun)) {
            $this->generateTahun = intval($this->filterTahun);
        }

        $this->loadGeneratePreview();
        $this->showGenerateModal = true;
    }

    public function updatedGenerateBulan(): void
    {
        $this->loadGeneratePreview();
    }

    public function updatedGenerateTahun(): void
    {
        $this->loadGeneratePreview();
    }

    public function updatedGenerateSelectAll($value): void
    {
        foreach ($this->generateItems as $guruId => $item) {
            $this->generateItems[$guruId]['selected'] = (bool) $value;
        }
    }

    public function loadGeneratePreview(): void
    {
        $this->generateItems = app(SalaryBulkGeneratorService::class)->buildPreviewItems($this->generateBulan, $this->generateTahun);
        $this->generateSelectAll = true;
    }

    public function recalculateGenerateRow($guruId): void
    {
        if (isset($this->generateItems[$guruId])) {
            $this->generateItems[$guruId] = app(SalaryBulkGeneratorService::class)->recalculateRow($this->generateItems[$guruId]);
        }
    }

    public function closeGenerateModal(): void
    {
        $this->showGenerateModal = false;
        $this->generateItems = [];
    }

    public function generateDrafts(): void
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        if (empty($this->generateItems)) {
            $this->loadGeneratePreview();
        }

        $selectedItems = array_filter($this->generateItems, fn($item) => !empty($item['selected']));

        if (empty($selectedItems)) {
            session()->flash('error', 'Silakan pilih setidaknya satu guru untuk di-generate.');
            return;
        }

        $createdCount = app(SalaryBulkGeneratorService::class)->generateDrafts(
            $selectedItems,
            $this->generateBulan,
            $this->generateTahun
        );

        session()->flash('message', "Draf gaji berhasil digenerate dan disimpan untuk {$createdCount} pegawai.");
        $this->closeGenerateModal();
    }
}
