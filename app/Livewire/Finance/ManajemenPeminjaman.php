<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Models\Guru;
use App\Models\Peminjaman;
use Livewire\WithPagination;

class ManajemenPeminjaman extends Component
{
    use WithPagination;

    // Filters
    public string $search = '';
    public string $filterStatus = ''; // 'berjalan', 'lunas'

    // Form fields
    public ?int $guru_id = null;
    public float $nominal = 0.00;
    public int $tenor_bulan = 1;
    public string $tanggal_pinjam = '';

    protected $rules = [
        'guru_id' => 'required|exists:guru,id',
        'nominal' => 'required|numeric|min:1000',
        'tenor_bulan' => 'required|integer|min:1|max:60',
        'tanggal_pinjam' => 'required|date',
    ];

    public function mount()
    {
        $this->tanggal_pinjam = date('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function savePeminjaman()
    {
        $this->validate();

        $cicilan = round($this->nominal / $this->tenor_bulan, 2);

        Peminjaman::create([
            'guru_id' => $this->guru_id,
            'tanggal_pinjam' => $this->tanggal_pinjam,
            'nominal' => $this->nominal,
            'tenor_bulan' => $this->tenor_bulan,
            'cicilan_per_bulan' => $cicilan,
            'sisa_pinjaman' => $this->nominal,
            'status' => 'berjalan',
        ]);

        session()->flash('message', 'Pinjaman kasbon guru berhasil dicatat.');
        $this->reset(['guru_id', 'nominal', 'tenor_bulan']);
        $this->tanggal_pinjam = date('Y-m-d');
    }

    public function render()
    {
        $query = Peminjaman::with('guru.user');

        if ($this->search) {
            $query->whereHas('guru.user', function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $loans = $query->latest()->paginate(15);
        $gurus = Guru::where('status_aktif', true)->with('user')->get();

        return view('livewire.finance.manajemen-peminjaman', [
            'loans' => $loans,
            'gurus' => $gurus
        ])->layout('components.layouts.app', ['title' => 'Peminjaman / Kasbon Guru']);
    }
}
