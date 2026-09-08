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

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterTipe()
    {
        $this->resetPage();
    }

    public function updatingFilterFitur()
    {
        $this->resetPage();
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

    public function render()
    {
        $userRole = auth()->user()->role->nama ?? '';
        $canApprove = in_array($userRole, ['super_admin', 'super_admin_2', 'founder']);

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
