<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Models\Guru;
use App\Models\Peminjaman;
use Livewire\WithPagination;
use App\Traits\WithCurrencySanitizer;
use App\Traits\WithDateFilter;

class ManajemenPeminjaman extends Component
{
    use WithPagination, WithCurrencySanitizer, WithDateFilter;

    // Filters
    public string $search = '';
    public string $filterStatus = ''; // 'berjalan', 'lunas'

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterPeriode' => ['except' => 'semua'],
        'startDate' => ['except' => null],
        'endDate' => ['except' => null],
    ];

    // Form fields
    public bool $showCreateModal = false;
    public ?int $guru_id = null;
    public $nominal = 0.00;
    public int $tenor_bulan = 1;
    public string $tanggal_pinjam = '';

    protected $rules = [
        'guru_id' => 'required|exists:guru,id',
        'nominal' => 'required|numeric|min:0',
        'tenor_bulan' => 'required|integer|min:1|max:60',
        'tanggal_pinjam' => 'required|date',
    ];

    public function mount()
    {
        $this->tanggal_pinjam = date('Y-m-d');
    }

    public function openCreateModal()
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $this->resetValidation();
        $this->reset(['guru_id', 'nominal', 'tenor_bulan']);
        $this->tanggal_pinjam = date('Y-m-d');
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetValidation();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterPeriode()
    {
        $this->resetPage();
    }

    public function updatingStartDate()
    {
        $this->resetPage();
    }

    public function updatingEndDate()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterStatus = '';
        $this->filterPeriode = 'semua';
        $this->startDate = null;
        $this->endDate = null;
        $this->resetPage();
    }

    public function getActiveFilterCountProperty(): int
    {
        $count = 0;
        if (!empty($this->search)) $count++;
        if (!empty($this->filterStatus)) $count++;
        if ($this->filterPeriode !== 'semua') $count++;
        if (!empty($this->startDate) || !empty($this->endDate)) $count++;
        return $count;
    }

    public function savePeminjaman()
    {
        if (auth()->user()->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $this->sanitizeCurrencies(['nominal']);

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
        $this->showCreateModal = false;
    }

    public function render()
    {
        $query = Peminjaman::with('guru.user');

        if ($this->search) {
            $query->whereHas('guru.user', function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                  ->orWhere('nip', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $this->applyDateFilter($query, 'tanggal_pinjam');

        $loans = $query->latest('tanggal_pinjam')->latest('id')->paginate(15);
        $gurus = Guru::where('status_aktif', true)->with('user')->get();

        return view('livewire.finance.manajemen-peminjaman', [
            'loans' => $loans,
            'gurus' => $gurus
        ])->layout('components.layouts.app', ['title' => 'Peminjaman / Kasbon Guru']);
    }
}
