<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Models\DanaBos as BosModel;
use App\Models\TahunAjaran;
use App\Traits\WithDateFilter;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class DanaBos extends Component
{
    use WithPagination, WithDateFilter, WithFileUploads;

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
    public $bukti_foto = null;

    // Edit Transaction & Bukti Form properties
    public bool $showEditModal = false;
    public ?int $editingTransactionId = null;
    public string $edit_jenis = 'masuk';
    public string $edit_tanggal = '';
    public float $edit_nominal = 0.00;
    public string $edit_kategori = '';
    public string $edit_keterangan = '';
    public ?string $edit_existing_bukti = null;
    public $edit_bukti_foto = null;

    // Lightbox Preview Modal
    public bool $showPreviewBuktiModal = false;
    public ?string $previewBuktiUrl = null;
    public ?string $previewBuktiTitle = null;

    protected $rules = [
        'jenis' => 'required|in:masuk,keluar',
        'tanggal' => 'required|date',
        'nominal' => 'required|numeric|min:1',
        'kategori' => 'required|string|max:255',
        'keterangan' => 'required|string|max:1000',
        'bukti_foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
    ];

    protected $messages = [
        'bukti_foto.image' => 'File bukti transaksi harus berupa foto/gambar.',
        'bukti_foto.mimes' => 'Format foto hanya boleh JPG, JPEG, PNG, atau WEBP.',
        'bukti_foto.max' => 'Ukuran file foto bukti maksimal 2MB.',
        'edit_bukti_foto.image' => 'File bukti transaksi harus berupa foto/gambar.',
        'edit_bukti_foto.mimes' => 'Format foto hanya boleh JPG, JPEG, PNG, atau WEBP.',
        'edit_bukti_foto.max' => 'Ukuran file foto bukti maksimal 2MB.',
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
        $this->reset(['nominal', 'kategori', 'keterangan', 'bukti_foto']);
        $this->tanggal = date('Y-m-d');
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->bukti_foto = null;
        $this->resetValidation();
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

        $pathBukti = null;
        if ($this->bukti_foto) {
            $pathBukti = $this->bukti_foto->store('bukti_dana_bos', 'public');
        }

        BosModel::create([
            'tahun_ajaran_id' => $activeTA->id,
            'jenis' => $this->jenis,
            'tanggal' => $this->tanggal,
            'nominal' => $this->nominal,
            'kategori' => $this->kategori,
            'keterangan' => $this->keterangan,
            'bukti' => $pathBukti,
        ]);

        session()->flash('message', 'Transaksi Dana BOS (' . ($this->jenis === 'masuk' ? 'Penerimaan' : 'Pengeluaran/Realisasi') . ') berhasil direkam.');
        
        $this->showCreateModal = false;
        $this->reset(['nominal', 'kategori', 'keterangan', 'bukti_foto']);
        $this->tanggal = date('Y-m-d');
        $this->resetPage();
    }

    public function openEditModal(int $id)
    {
        if (auth()->user()->isSuperAdmin2() || auth()->user()->isKepalaSekolah()) {
            session()->flash('error', 'Akses Ditolak: Anda hanya memiliki hak akses pemantauan (Lihat Saja).');
            return;
        }

        $this->resetValidation();
        $tx = BosModel::findOrFail($id);
        $this->editingTransactionId = $tx->id;
        $this->edit_jenis = $tx->jenis;
        $this->edit_tanggal = $tx->tanggal ? $tx->tanggal->format('Y-m-d') : date('Y-m-d');
        $this->edit_nominal = (float) $tx->nominal;
        $this->edit_kategori = $tx->kategori;
        $this->edit_keterangan = $tx->keterangan ?: '';
        $this->edit_existing_bukti = $tx->bukti;
        $this->edit_bukti_foto = null;
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingTransactionId = null;
        $this->edit_bukti_foto = null;
        $this->resetValidation();
    }

    public function updateTransaction()
    {
        if (auth()->user()->isSuperAdmin2() || auth()->user()->isKepalaSekolah()) {
            session()->flash('error', 'Akses Ditolak: Anda hanya memiliki hak akses pemantauan (Lihat Saja).');
            return;
        }

        $this->validate([
            'edit_jenis' => 'required|in:masuk,keluar',
            'edit_tanggal' => 'required|date',
            'edit_nominal' => 'required|numeric|min:1',
            'edit_kategori' => 'required|string|max:255',
            'edit_keterangan' => 'required|string|max:1000',
            'edit_bukti_foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $tx = BosModel::findOrFail($this->editingTransactionId);

        $pathBukti = $tx->bukti;
        if ($this->edit_bukti_foto) {
            if ($tx->bukti && Storage::disk('public')->exists($tx->bukti)) {
                Storage::disk('public')->delete($tx->bukti);
            }
            $pathBukti = $this->edit_bukti_foto->store('bukti_dana_bos', 'public');
        }

        $tx->update([
            'jenis' => $this->edit_jenis,
            'tanggal' => $this->edit_tanggal,
            'nominal' => $this->edit_nominal,
            'kategori' => $this->edit_kategori,
            'keterangan' => $this->edit_keterangan,
            'bukti' => $pathBukti,
        ]);

        session()->flash('message', 'Transaksi Dana BOS dan bukti transaksi berhasil diperbarui.');
        $this->closeEditModal();
        $this->resetPage();
    }

    public function deleteEditBukti()
    {
        if (auth()->user()->isSuperAdmin2() || auth()->user()->isKepalaSekolah()) {
            session()->flash('error', 'Akses Ditolak: Anda hanya memiliki hak akses pemantauan (Lihat Saja).');
            return;
        }

        if ($this->editingTransactionId) {
            $tx = BosModel::findOrFail($this->editingTransactionId);
            if ($tx->bukti && Storage::disk('public')->exists($tx->bukti)) {
                Storage::disk('public')->delete($tx->bukti);
            }
            $tx->update(['bukti' => null]);
            $this->edit_existing_bukti = null;
            session()->flash('message', 'Foto bukti transaksi Dana BOS berhasil dihapus.');
        }
    }

    public function openPreviewBukti(?string $path, ?string $title = 'Bukti Transaksi BOS')
    {
        $this->previewBuktiUrl = $path ? asset('storage/' . $path) : null;
        $this->previewBuktiTitle = $title;
        $this->showPreviewBuktiModal = true;
    }

    public function closePreviewBukti()
    {
        $this->showPreviewBuktiModal = false;
        $this->previewBuktiUrl = null;
        $this->previewBuktiTitle = null;
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

        if ($tx->bukti && Storage::disk('public')->exists($tx->bukti)) {
            Storage::disk('public')->delete($tx->bukti);
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

        $items = BosModel::whereIn('id', $this->selectedIds)->get();
        $count = 0;
        foreach ($items as $item) {
            if ($item->bukti && Storage::disk('public')->exists($item->bukti)) {
                Storage::disk('public')->delete($item->bukti);
            }
            $item->delete();
            $count++;
        }

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
