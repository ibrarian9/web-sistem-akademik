<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Models\DanaBos as BosModel;
use App\Models\TahunAjaran;
use Livewire\WithPagination;

class DanaBos extends Component
{
    use WithPagination;

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

    public function saveTransaction()
    {
        $this->validate();

        $activeTA = TahunAjaran::where('status_aktif', true)->first();
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

        session()->flash('message', 'Transaksi Dana BOS berhasil direkam.');
        $this->reset(['nominal', 'kategori', 'keterangan']);
        $this->resetPage();
    }

    public function render()
    {
        $transactions = BosModel::with('tahunAjaran')
            ->latest()
            ->paginate(15);

        return view('livewire.finance.dana-bos', [
            'transactions' => $transactions
        ])->layout('components.layouts.app', ['title' => 'Tata Kelola Dana BOS']);
    }
}
