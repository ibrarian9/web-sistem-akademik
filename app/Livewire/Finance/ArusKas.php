<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;
use App\Models\PemasukanKas;
use Livewire\WithPagination;

class ArusKas extends Component
{
    use WithPagination;

    // View type: 'pengeluaran' or 'pemasukan'
    public string $type = 'pengeluaran';

    // Filters
    public ?int $filterKategori = null;
    public string $filterKategoriPemasukan = '';

    // Create Expense Form properties
    public ?int $kategori_pengeluaran_id = null;
    public float $jumlah = 0.00;
    public string $tanggal = '';
    public string $keterangan = '';

    // Create Income Form properties
    public string $kategori_pemasukan = 'Infaq';
    public float $jumlah_pemasukan = 0.00;
    public string $tanggal_pemasukan = '';
    public string $keterangan_pemasukan = '';

    // Options list
    public array $categories = [];
    public array $pemasukanCategories = [
        'Infaq',
        'Sedekah Subuh',
        'Maghrib Mengaji',
        'Donasi',
        'Sponsor / Acara',
        'Lainnya'
    ];

    public function mount()
    {
        $this->categories = KategoriPengeluaran::orderBy('nama')->get()->toArray();
        $this->tanggal = date('Y-m-d');
        $this->tanggal_pemasukan = date('Y-m-d');

        if (request()->query('type') === 'pemasukan') {
            $this->type = 'pemasukan';
        }
    }

    public function setType(string $newType)
    {
        $this->type = $newType;
        $this->resetPage();
    }

    public function saveExpense()
    {
        $this->validate([
            'kategori_pengeluaran_id' => 'required|exists:kategori_pengeluaran,id',
            'jumlah' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
            'keterangan' => 'required|string|max:1000',
        ]);

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

    public function saveIncome()
    {
        $this->validate([
            'kategori_pemasukan' => 'required|string',
            'jumlah_pemasukan' => 'required|numeric|min:1',
            'tanggal_pemasukan' => 'required|date',
            'keterangan_pemasukan' => 'nullable|string|max:1000',
        ]);

        PemasukanKas::create([
            'kategori' => $this->kategori_pemasukan,
            'jumlah' => $this->jumlah_pemasukan,
            'tanggal' => $this->tanggal_pemasukan,
            'keterangan' => $this->keterangan_pemasukan,
            'petugas_id' => auth()->id(),
        ]);

        session()->flash('message', 'Pemasukan kas non-SPP (' . $this->kategori_pemasukan . ') berhasil direkam.');
        $this->reset(['jumlah_pemasukan', 'keterangan_pemasukan']);
        $this->resetPage();
    }

    public function render()
    {
        $totalPemasukanKas = PemasukanKas::sum('jumlah');
        $totalPengeluaranKas = Pengeluaran::sum('jumlah');

        if ($this->type === 'pemasukan') {
            $query = PemasukanKas::with('petugas')->latest();
            if ($this->filterKategoriPemasukan) {
                $query->where('kategori', $this->filterKategoriPemasukan);
            }
            $items = $query->paginate(15);
        } else {
            $query = Pengeluaran::with(['kategori', 'petugas'])->latest();
            if ($this->filterKategori) {
                $query->where('kategori_pengeluaran_id', $this->filterKategori);
            }
            $items = $query->paginate(15);
        }

        return view('livewire.finance.arus-kas', [
            'items' => $items,
            'totalPemasukanKas' => $totalPemasukanKas,
            'totalPengeluaranKas' => $totalPengeluaranKas,
        ])->layout('components.layouts.app', ['title' => 'Arus Kas Operasional']);
    }
}
