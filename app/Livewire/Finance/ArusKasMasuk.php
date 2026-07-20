<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Models\PemasukanKas;
use Livewire\WithPagination;

class ArusKasMasuk extends Component
{
    use WithPagination;

    // Filters
    public string $filterKategori = '';
    public string $search = '';

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

        session()->flash('message', 'Pemasukan kas non-SPP (' . $this->kategori . ') berhasil dicatat.');

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

    public function render()
    {
        $query = PemasukanKas::with('petugas')->latest('tanggal');

        if ($this->filterKategori !== '') {
            $query->where('kategori', $this->filterKategori);
        }

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('kategori', 'like', '%' . $this->search . '%')
                  ->orWhere('keterangan', 'like', '%' . $this->search . '%');
            });
        }

        $pemasukans = $query->paginate(15);
        $totalPemasukanKas = PemasukanKas::sum('jumlah');

        return view('livewire.finance.arus-kas-masuk', [
            'pemasukans' => $pemasukans,
            'totalPemasukanKas' => $totalPemasukanKas,
        ])->layout('components.layouts.app', ['title' => 'Arus Kas Masuk']);
    }
}
