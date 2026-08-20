<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Models\PemasukanKas;
use App\Traits\WithDateFilter;
use Livewire\WithPagination;

class ArusKasMasuk extends Component
{
    use WithPagination, WithDateFilter;

    // Modal state
    public bool $showCreateModal = false;

    // Filters
    public string $filterKategori = '';
    public string $search = '';

    // Bulk selection
    public array $selectedIds = [];
    public bool $selectAll = false;

    // Create Income Form properties
    public string $kategori = 'Infaq';
    public float $jumlah = 0.00;
    public string $tanggal = '';
    public string $keterangan = '';

    public array $kategoriOptions = [
        'Infaq',
        'Sedekah Subuh',
        'Maghrib Mengaji',
        'Donasi',
        'Sponsor / Acara',
        'Hibah Yayasan',
        'Lainnya'
    ];

    protected $rules = [
        'kategori' => 'required|string',
        'jumlah' => 'required|numeric|min:1000',
        'tanggal' => 'required|date',
        'keterangan' => 'nullable|string|max:500',
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
        $query = PemasukanKas::query();

        if ($this->filterKategori !== '') {
            $query->where('kategori', $this->filterKategori);
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

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->reset(['jumlah', 'keterangan']);
        $this->tanggal = date('Y-m-d');
        $this->kategori = 'Infaq';
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    public function saveIncome()
    {
        $this->validate();

        PemasukanKas::create([
            'kategori' => $this->kategori,
            'jumlah' => $this->jumlah,
            'tanggal' => $this->tanggal,
            'keterangan' => $this->keterangan,
            'petugas_id' => auth()->id(),
        ]);

        session()->flash('message', 'Pemasukan kas yayasan (' . $this->kategori . ') berhasil dicatat.');

        $this->showCreateModal = false;
        $this->reset(['jumlah', 'keterangan']);
        $this->tanggal = date('Y-m-d');
        $this->kategori = 'Infaq';
        $this->resetPage();
    }

    public function deleteIncome(int $id)
    {
        $item = PemasukanKas::findOrFail($id);
        $item->delete();

        session()->flash('message', 'Catatan pemasukan kas berhasil dihapus.');
    }

    public function bulkDelete()
    {
        if (empty($this->selectedIds)) {
            return;
        }

        $count = PemasukanKas::whereIn('id', $this->selectedIds)->delete();
        session()->flash('message', "Berhasil menghapus {$count} catatan pemasukan kas yayasan.");

        $this->resetSelection();
        $this->resetPage();
    }

    public function exportPdf()
    {
        $query = PemasukanKas::with('petugas')->orderBy('tanggal', 'desc');

        if ($this->filterKategori !== '') {
            $query->where('kategori', $this->filterKategori);
        }

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('kategori', 'like', '%' . $this->search . '%')
                  ->orWhere('keterangan', 'like', '%' . $this->search . '%');
            });
        }

        $this->applyDateFilter($query, 'tanggal');

        $data = $query->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('livewire.shared.laporan.pdf-laporan-kas-masuk', [
            'data' => $data,
            'kategori' => $this->filterKategori ?: 'Semua Kategori',
            'totalPemasukan' => $data->sum('jumlah'),
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'laporan_kas_masuk_' . date('Ymd_His') . '.pdf');
    }

    public function render()
    {
        $query = PemasukanKas::with('petugas')->orderBy('tanggal', 'desc');

        if ($this->filterKategori !== '') {
            $query->where('kategori', $this->filterKategori);
        }

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('kategori', 'like', '%' . $this->search . '%')
                  ->orWhere('keterangan', 'like', '%' . $this->search . '%');
            });
        }

        $this->applyDateFilter($query, 'tanggal');

        $pemasukans = $query->paginate(15);
        $totalPemasukanKas = PemasukanKas::sum('jumlah');

        return view('livewire.finance.arus-kas-masuk', [
            'pemasukans' => $pemasukans,
            'totalPemasukanKas' => $totalPemasukanKas,
        ])->layout('components.layouts.app', ['title' => 'Arus Kas Masuk Yayasan']);
    }
}
