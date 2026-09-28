<?php

namespace App\Livewire\Finance\Concerns;

use App\Models\GajiGuru;
use App\Models\Guru;

trait HasSalaryModals
{
    // Detail Modal State
    public bool $showDetailModal = false;
    public ?GajiGuru $selectedSalaryDetail = null;

    // Salary History Modal State
    public bool $showHistoryModal = false;
    public ?int $historyGuruId = null;
    public ?Guru $historyGuru = null;
    public string $historyFilterTahun = '';

    // PDF Preview Modal
    public bool $showPreviewModal = false;
    public ?int $previewSalaryId = null;
    public $previewSalary = null;

    public function openDetailModal(int $id): void
    {
        $this->selectedSalaryDetail = GajiGuru::with(['guru.user', 'pengeluaran'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->selectedSalaryDetail = null;
        $this->detailBuktiFoto = null;
    }

    public function openHistoryModal(int $guruId): void
    {
        $this->historyGuruId = $guruId;
        $this->historyGuru = Guru::with('user')->find($guruId);
        $this->historyFilterTahun = '';
        $this->showHistoryModal = true;
    }

    public function closeHistoryModal(): void
    {
        $this->showHistoryModal = false;
        $this->historyGuruId = null;
        $this->historyGuru = null;
        $this->historyFilterTahun = '';
    }

    public function openPreview(int $id): void
    {
        $salary = GajiGuru::with('guru.user')->findOrFail($id);
        $this->previewSalaryId = $salary->id;
        $this->previewSalary = $salary;
        $this->showPreviewModal = true;
    }

    public function closePreview(): void
    {
        $this->showPreviewModal = false;
        $this->previewSalaryId = null;
        $this->previewSalary = null;
    }
}
