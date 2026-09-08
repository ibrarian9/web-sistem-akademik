<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Models\DanaBos as BosModel;
use App\Models\TahunAjaran;
use App\Traits\WithDateFilter;
use Livewire\WithPagination;

class DanaBos extends Component
{
    use WithPagination, WithDateFilter;

    // Modal state
    public bool $showCreateModal = false;

    // Filters
    public string $filterJenis = 'semua'; // 'semua', 'masuk', 'keluar'
    public string $search = '';

    // Bulk selection
    public array $selectedIds = [];
    public bool $selectAll = false;

    // Create Form properties
    public string $jenis = 'masuk';
    public string $tanggal = '';
    public float $nominal = 0.00;
    public string $kategori = '';
    public string $keterangan = '';

    protected $rules = [
        'jenis' => 'required|in:masuk,keluar',
        'tanggal' => 'required|date',
        'nominal' => 'required|numeric|min:1',
        'kategori' => 'required|string|max:255',
        'keterangan' => 'required|string|max:1000',
    ];

    public function mount()
    {
        $this->tanggal = date('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatingFilterJenis()
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
        $query = BosModel::query();

        if ($this->filterJenis !== 'semua') {
            $query->where('jenis', $this->filterJenis);
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

    public function openCreateModal(string $defaultJenis = 'masuk')
    {
        if (auth()->user()->isSuperAdmin2() || auth()->user()->isKepalaSekolah()) {
            session()->flash('error', 'Akses Ditolak: Anda hanya memiliki hak akses pemantauan (Lihat Saja).');
            return;
        }

        $this->resetValidation();
        $this->jenis = in_array($defaultJenis, ['masuk', 'keluar']) ? $defaultJenis : 'masuk';
        $this->reset(['nominal', 'kategori', 'keterangan']);
        $this->tanggal = date('Y-m-d');
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    public function selectTab(string $tab)
    {
        $this->filterJenis = $tab;
        $this->resetSelection();
        $this->resetPage();
    }

    public function saveTransaction()
    {
        if (auth()->user()->isSuperAdmin2() || auth()->user()->isKepalaSekolah()) {
            session()->flash('error', 'Akses Ditolak: Anda hanya memiliki hak akses pemantauan (Lihat Saja).');
            return;
        }

        $this->validate();

        $activeTA = TahunAjaran::where('status_aktif', true)->first() ?? TahunAjaran::latest()->first();
        if (!$activeTA) {
            session()->flash('error', 'Tidak ada tahun ajaran aktif.');
            return;
        }

        BosModel::create([
            'tahun_ajaran_id' => $activeTA->id,
            'jenis' => $this->jenis,
            'tanggal' => $this->tanggal,
            'nominal' => $this->nominal,
            'kategori' => $this->kategori,
            'keterangan' => $this->keterangan,
        ]);

        session()->flash('message', 'Transaksi Dana BOS (' . ($this->jenis === 'masuk' ? 'Penerimaan' : 'Pengeluaran/Realisasi') . ') berhasil direkam.');
        
        $this->showCreateModal = false;
        $this->reset(['nominal', 'kategori', 'keterangan']);
        $this->tanggal = date('Y-m-d');
        $this->resetPage();
    }

    public function deleteTransaction(int $id, ?string $alasan = null)
    {
        if (auth()->user()->isSuperAdmin2() || auth()->user()->isKepalaSekolah()) {
            session()->flash('error', 'Akses Ditolak: Anda hanya memiliki hak akses pemantauan (Lihat Saja).');
            return;
        }

        $tx = BosModel::findOrFail($id);
        $userRole = auth()->user()->role->nama ?? '';

        if ($userRole === 'finance') {
            $reason = $alasan ?: 'Penghapusan catatan transaksi Dana BOS diajukan oleh staf keuangan';
            \App\Services\FinancialApprovalService::createRequest(
                auth()->user(),
                'hapus',
                'dana_bos',
                $tx,
                null,
                $reason,
                "Hapus Transaksi BOS: {$tx->kategori} (" . strtoupper($tx->jenis) . ") - Rp " . number_format($tx->nominal, 0, ',', '.') . " (" . ($tx->tanggal ? $tx->tanggal->format('d/m/Y') : '-') . ")"
            );

            session()->flash('message', 'Permohonan penghapusan transaksi Dana BOS telah diajukan ke Super Admin / Super Admin 2 untuk disetujui.');
            return;
        }

        $tx->delete();

        session()->flash('message', 'Catatan transaksi Dana BOS berhasil dihapus.');
    }

    public function bulkDelete()
    {
        if (auth()->user()->isSuperAdmin2() || auth()->user()->isKepalaSekolah()) {
            session()->flash('error', 'Akses Ditolak: Anda hanya memiliki hak akses pemantauan (Lihat Saja).');
            return;
        }

        if (auth()->user()->role?->nama === 'finance') {
            session()->flash('error', 'Akses Ditolak: Penghapusan massal tidak diizinkan untuk staf keuangan. Silakan ajukan penghapusan per transaksi agar dapat disetujui Super Admin.');
            return;
        }

        if (empty($this->selectedIds)) {
            return;
        }

        $count = BosModel::whereIn('id', $this->selectedIds)->delete();
        session()->flash('message', "Berhasil menghapus {$count} catatan transaksi Dana BOS.");

        $this->resetSelection();
        $this->resetPage();
    }

    public function render()
    {
        $totalMasuk = BosModel::where('jenis', 'masuk')->sum('nominal');
        $totalKeluar = BosModel::where('jenis', 'keluar')->sum('nominal');
        $saldoBos = $totalMasuk - $totalKeluar;

        $query = BosModel::with('tahunAjaran')->latest('tanggal');

        if ($this->filterJenis !== 'semua') {
            $query->where('jenis', $this->filterJenis);
        }

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('kategori', 'like', '%' . $this->search . '%')
                  ->orWhere('keterangan', 'like', '%' . $this->search . '%');
            });
        }

        $this->applyDateFilter($query, 'tanggal');

        $transactions = $query->paginate(15);

        return view('livewire.finance.dana-bos', [
            'transactions' => $transactions,
            'totalMasuk' => $totalMasuk,
            'totalKeluar' => $totalKeluar,
            'saldoBos' => $saldoBos,
        ])->layout('components.layouts.app', ['title' => 'Tata Kelola Dana BOS']);
    }
}
