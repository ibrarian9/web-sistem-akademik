<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;
use Livewire\WithPagination;

class ArusKas extends Component
{
    use WithPagination;

    // Filters
    public ?int $filterKategori = null;

    // Create Form properties
    public ?int $kategori_pengeluaran_id = null;
    public float $jumlah = 0.00;
    public string $tanggal = '';
    public string $keterangan = '';

    // Options list
    public array $categories = [];

    protected $rules = [
        'kategori_pengeluaran_id' => 'required|exists:kategori_pengeluaran,id',
        'jumlah' => 'required|numeric|min:1',
        'tanggal' => 'required|date',
        'keterangan' => 'required|string|max:1000',
    ];

    public function mount()
    {
        $this->categories = KategoriPengeluaran::orderBy('nama')->get()->toArray();
        $this->tanggal = date('Y-m-d');
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

        session()->flash('message', 'Pengeluaran operasional berhasil direkam.');
        $this->reset(['kategori_pengeluaran_id', 'jumlah', 'keterangan']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Pengeluaran::with(['kategori', 'petugas'])
            ->latest();

        if ($this->filterKategori) {
            $query->where('kategori_pengeluaran_id', $this->filterKategori);
        }

        $expenses = $query->paginate(15);

        return view('livewire.finance.arus-kas', [
            'expenses' => $expenses
        ])->layout('components.layouts.app', ['title' => 'Arus Kas Pengeluaran']);
    }
}
