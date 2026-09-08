<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ApprovalKeuangan;
use App\Services\FinancialApprovalService;

class ApprovalKeuanganIndex extends Component
{
    use WithPagination;

    // Filters
    public string $filterStatus = 'menunggu';
    public string $filterTipe = 'semua';
    public string $filterFitur = 'semua';
    public string $search = '';

    // Modal state
    public bool $showDetailModal = false;
    public bool $showApproveModal = false;
    public bool $showRejectModal = false;
    public bool $showCancelModal = false;
    public bool $showBulkApproveModal = false;
    public bool $showBulkCancelModal = false;

    // Bulk selection state
    public array $selectedIds = [];
    public bool $selectAll = false;
    public string $bulkApprovalNote = '';
    public string $bulkCancelReason = '';

    public ?int $selectedId = null;
    public ?ApprovalKeuangan $selectedApproval = null;
    public string $approvalNote = '';
    public string $rejectReason = '';
    public string $cancelReason = '';

    protected $queryString = [
        'filterStatus' => ['except' => 'menunggu'],
        'filterTipe' => ['except' => 'semua'],
        'filterFitur' => ['except' => 'semua'],
        'search' => ['except' => ''],
    ];

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = $this->getCurrentPageActionableIds();
        } else {
            $this->selectedIds = [];
        }
    }

    public function resetSelection()
    {
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function updatingPage()
    {
        $this->resetSelection();
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatingFilterTipe()
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatingFilterFitur()
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function openDetail(int $id)
    {
        $this->selectedId = $id;
        $this->selectedApproval = ApprovalKeuangan::with(['pemohon', 'approver'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function closeDetail()
    {
        $this->showDetailModal = false;
        $this->selectedApproval = null;
        $this->selectedId = null;
    }

    public function openApproveModal(int $id)
    {
        $this->canPerformApprovalCheck();
        $this->selectedId = $id;
        $this->selectedApproval = ApprovalKeuangan::with('pemohon')->findOrFail($id);
        $this->approvalNote = '';
        $this->showApproveModal = true;
    }

    public function closeApproveModal()
    {
        $this->showApproveModal = false;
        $this->approvalNote = '';
    }

    public function openRejectModal(int $id)
    {
        $this->canPerformApprovalCheck();
        $this->selectedId = $id;
        $this->selectedApproval = ApprovalKeuangan::with('pemohon')->findOrFail($id);
        $this->rejectReason = '';
        $this->showRejectModal = true;
    }

    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->rejectReason = '';
    }

    private function canPerformApprovalCheck(): void
    {
        $role = auth()->user()->role->nama ?? '';
        if (!in_array($role, ['super_admin', 'super_admin_2', 'founder'])) {
            session()->flash('error', 'Akses Ditolak: Hanya Super Admin atau Super Admin 2 yang memiliki wewenang memberikan persetujuan.');
            throw new \Exception('Unauthorized action');
        }
    }

    public function approve()
    {
        $this->canPerformApprovalCheck();

        $approval = ApprovalKeuangan::findOrFail($this->selectedId);

        try {
            FinancialApprovalService::approve($approval, auth()->user(), $this->approvalNote ?: null);
            session()->flash('success', "Permohonan {$approval->tipe_aksi} {$approval->fitur} berhasil disetujui.");
            $this->dispatch('show-alert', [
                'title' => 'Disetujui',
                'message' => "Permohonan {$approval->tipe_aksi} berhasil disetujui dan perubahan telah diterapkan ke sistem.",
                'type' => 'success',
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memproses persetujuan: ' . $e->getMessage());
        }

        $this->closeApproveModal();
        $this->closeDetail();
    }

    public function reject()
    {
        $this->canPerformApprovalCheck();

        $this->validate([
            'rejectReason' => 'required|string|min:5|max:1000',
        ], [
            'rejectReason.required' => 'Alasan penolakan wajib diisi agar staf keuangan mengetahui alasan dibatalkannya aksi ini.',
        ]);

        $approval = ApprovalKeuangan::findOrFail($this->selectedId);

        try {
            FinancialApprovalService::reject($approval, auth()->user(), $this->rejectReason);
            session()->flash('success', "Permohonan {$approval->tipe_aksi} {$approval->fitur} telah ditolak.");
            $this->dispatch('show-alert', [
                'title' => 'Permohonan Ditolak',
                'message' => "Permohonan {$approval->tipe_aksi} ditolak.",
                'type' => 'danger',
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menolak permohonan: ' . $e->getMessage());
        }

        $this->closeRejectModal();
        $this->closeDetail();
    }

    public function openCancelModal(int $id)
    {
        $approval = ApprovalKeuangan::with(['pemohon', 'approver'])->findOrFail($id);

        if (!$this->canCancelApproval($approval)) {
            session()->flash('error', 'Akses Ditolak: Anda tidak memiliki wewenang untuk membatalkan permohonan ini.');
            return;
        }

        $this->selectedId = $id;
        $this->selectedApproval = $approval;
        $this->cancelReason = '';
        $this->showCancelModal = true;
    }

    public function closeCancelModal()
    {
        $this->showCancelModal = false;
        $this->cancelReason = '';
    }

    public function canCancelApproval(?ApprovalKeuangan $approval = null): bool
    {
        if (!$approval || $approval->status !== 'menunggu') {
            return false;
        }

        $user = auth()->user();
        if (!$user) {
            return false;
        }

        $role = $user->role->nama ?? '';

        return $approval->pemohon_id === $user->id || in_array($role, ['finance', 'super_admin', 'super_admin_2', 'founder']);
    }

    public function cancelApproval()
    {
        $approval = ApprovalKeuangan::findOrFail($this->selectedId);

        if (!$this->canCancelApproval($approval)) {
            session()->flash('error', 'Akses Ditolak: Anda tidak memiliki wewenang untuk membatalkan permohonan ini.');
            $this->closeCancelModal();
            return;
        }

        try {
            FinancialApprovalService::cancel($approval, auth()->user(), $this->cancelReason ?: 'Dibatalkan oleh staf keuangan');
            session()->flash('success', "Permohonan {$approval->tipe_aksi} {$approval->fitur} berhasil dibatalkan.");
            $this->dispatch('show-alert', [
                'title' => 'Permohonan Dibatalkan',
                'message' => "Permohonan {$approval->tipe_aksi} berhasil dibatalkan.",
                'type' => 'info',
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal membatalkan permohonan: ' . $e->getMessage());
        }

        $this->closeCancelModal();
        $this->closeDetail();
    }

    public function getCurrentPageActionableIds(): array
    {
        $query = $this->getFilteredQuery();
        $query->where('status', 'menunggu');

        $user = auth()->user();
        $role = $user->role->nama ?? '';
        $canApprove = in_array($role, ['super_admin', 'super_admin_2', 'founder']);

        if (!$canApprove && $role !== 'finance') {
            $query->where('pemohon_id', $user->id);
        }

        return $query->paginate(15)->pluck('id')->map(fn($id) => (int) $id)->toArray();
    }

    public function getSelectedApprovalsProperty()
    {
        if (empty($this->selectedIds)) {
            return collect();
        }
        return ApprovalKeuangan::with('pemohon')
            ->whereIn('id', $this->selectedIds)
            ->get();
    }

    public function openBulkApproveModal()
    {
        $this->canPerformApprovalCheck();

        if (empty($this->selectedIds)) {
            session()->flash('error', 'Silakan pilih setidaknya satu permohonan persetujuan terlebih dahulu.');
            return;
        }

        $this->bulkApprovalNote = '';
        $this->showBulkApproveModal = true;
    }

    public function closeBulkApproveModal()
    {
        $this->showBulkApproveModal = false;
        $this->bulkApprovalNote = '';
    }

    public function bulkApprove()
    {
        $this->canPerformApprovalCheck();

        if (empty($this->selectedIds)) {
            session()->flash('error', 'Tidak ada permohonan yang dipilih.');
            $this->closeBulkApproveModal();
            return;
        }

        $approvals = ApprovalKeuangan::whereIn('id', $this->selectedIds)
            ->where('status', 'menunggu')
            ->get();

        if ($approvals->isEmpty()) {
            session()->flash('error', 'Tidak ada permohonan berstatus menunggu yang dapat disetujui.');
            $this->closeBulkApproveModal();
            $this->resetSelection();
            return;
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($approvals as $approval) {
            try {
                FinancialApprovalService::approve($approval, auth()->user(), $this->bulkApprovalNote ?: 'Disetujui massal');
                $successCount++;
            } catch (\Exception $e) {
                $failCount++;
            }
        }

        $this->closeBulkApproveModal();
        $this->resetSelection();

        if ($failCount === 0) {
            session()->flash('success', "Berhasil menyetujui {$successCount} permohonan keuangan sekaligus.");
        } else {
            session()->flash('warning', "Persetujuan massal selesai: {$successCount} berhasil disetujui, {$failCount} gagal.");
        }

        $this->dispatch('show-alert', [
            'title' => 'Persetujuan Massal Selesai',
            'message' => "{$successCount} permohonan berhasil disetujui.",
            'type' => 'success',
        ]);
    }

    public function openBulkCancelModal()
    {
        if (empty($this->selectedIds)) {
            session()->flash('error', 'Silakan pilih setidaknya satu permohonan persetujuan terlebih dahulu.');
            return;
        }

        $this->bulkCancelReason = '';
        $this->showBulkCancelModal = true;
    }

    public function closeBulkCancelModal()
    {
        $this->showBulkCancelModal = false;
        $this->bulkCancelReason = '';
    }

    public function bulkCancel()
    {
        if (empty($this->selectedIds)) {
            session()->flash('error', 'Tidak ada permohonan yang dipilih.');
            $this->closeBulkCancelModal();
            return;
        }

        $approvals = ApprovalKeuangan::whereIn('id', $this->selectedIds)
            ->where('status', 'menunggu')
            ->get();

        if ($approvals->isEmpty()) {
            session()->flash('error', 'Tidak ada permohonan berstatus menunggu yang dapat dibatalkan.');
            $this->closeBulkCancelModal();
            $this->resetSelection();
            return;
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($approvals as $approval) {
            if (!$this->canCancelApproval($approval)) {
                $failCount++;
                continue;
            }

            try {
                FinancialApprovalService::cancel($approval, auth()->user(), $this->bulkCancelReason ?: 'Dibatalkan secara massal');
                $successCount++;
            } catch (\Exception $e) {
                $failCount++;
            }
        }

        $this->closeBulkCancelModal();
        $this->resetSelection();

        if ($failCount === 0) {
            session()->flash('success', "Berhasil membatalkan {$successCount} permohonan persetujuan.");
        } else {
            session()->flash('warning', "Pembatalan massal: {$successCount} berhasil dibatalkan, {$failCount} tidak dapat dibatalkan.");
        }

        $this->dispatch('show-alert', [
            'title' => 'Pembatalan Massal Selesai',
            'message' => "{$successCount} permohonan berhasil dibatalkan.",
            'type' => 'info',
        ]);
    }

    protected function getFilteredQuery()
    {
        $query = ApprovalKeuangan::with(['pemohon', 'approver'])->latest();

        if ($this->filterStatus !== 'semua') {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterTipe !== 'semua') {
            $query->where('tipe_aksi', $this->filterTipe);
        }

        if ($this->filterFitur !== 'semua') {
            $query->where('fitur', $this->filterFitur);
        }

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('judul', 'like', '%' . $this->search . '%')
                  ->orWhere('alasan', 'like', '%' . $this->search . '%')
                  ->orWhereHas('pemohon', function ($sub) {
                      $sub->where('nama', 'like', '%' . $this->search . '%');
                  });
            });
        }

        return $query;
    }

    public function render()
    {
        $userRole = auth()->user()->role->nama ?? '';
        $canApprove = in_array($userRole, ['super_admin', 'super_admin_2', 'founder']);

        $query = $this->getFilteredQuery();
        $approvals = $query->paginate(15);

        $counts = [
            'total' => ApprovalKeuangan::count(),
            'menunggu' => ApprovalKeuangan::where('status', 'menunggu')->count(),
            'disetujui' => ApprovalKeuangan::where('status', 'disetujui')->count(),
            'ditolak' => ApprovalKeuangan::where('status', 'ditolak')->count(),
            'dibatalkan' => ApprovalKeuangan::where('status', 'dibatalkan')->count(),
        ];

        return view('livewire.finance.approval-keuangan', [
            'approvals' => $approvals,
            'counts' => $counts,
            'canApprove' => $canApprove,
        ])->layout('components.layouts.app', ['title' => 'Persetujuan Aksi Keuangan']);
    }
}
