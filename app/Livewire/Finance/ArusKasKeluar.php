<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;
use App\Traits\WithDateFilter;
use Livewire\WithPagination;

class ArusKasKeluar extends Component
{
    use WithPagination, WithDateFilter;

    // Modal state
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

        session()->flash('message', 'Pengeluaran kas yayasan & operasional berhasil dicatat.');

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

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('livewire.shared.laporan.pdf-laporan-kas-keluar', [
            'data' => $data,
            'kategori' => $this->filterKategori ? (\App\Models\KategoriPengeluaran::find($this->filterKategori)?->nama ?? 'Semua') : 'Semua Kategori',
            'totalPengeluaran' => $data->sum('jumlah'),
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'laporan_kas_keluar_' . date('Ymd_His') . '.pdf');
    }

    public function render()
    {
        $query = Pengeluaran::with(['kategori', 'petugas'])->latest('tanggal');

        if ($this->filterKategori) {
            $query->where('kategori_pengeluaran_id', $this->filterKategori);
        }

        if ($this->search !== '') {
            $query->where('keterangan', 'like', '%' . $this->search . '%');
        }

        $this->applyDateFilter($query, 'tanggal');

        $pengeluarans = $query->paginate(15);
        $totalPengeluaranKas = Pengeluaran::sum('jumlah');

        return view('livewire.finance.arus-kas-keluar', [
            'pengeluarans' => $pengeluarans,
            'totalPengeluaranKas' => $totalPengeluaranKas,
        ])->layout('components.layouts.app', ['title' => 'Arus Kas Keluar Yayasan']);
    }
}
