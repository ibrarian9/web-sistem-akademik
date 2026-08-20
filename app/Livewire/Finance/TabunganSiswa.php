<?php

namespace App\Livewire\Finance;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Tabungan;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class TabunganSiswa extends Component
{
    use WithPagination;

    public $search = '';
    public $filterKelas = '';

    // Modal Transaction State
    public $showTransactionModal = false;
    public $siswa_id = null;
    public $selectedSiswaNama = '';
    public $selectedSiswaSaldo = 0;
    public $jenis = 'setor';
    public $nominal = '';
    public $tanggal = '';
    public $keterangan = '';

    // Modal History State
    public $showHistoryModal = false;
    public $selectedSiswaHistory = null;
    public $historyTransactions = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'filterKelas' => ['except' => ''],
    ];

    public function mount()
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role->nama ?? '', ['finance', 'super_admin', 'kepala_sekolah'])) {
            abort(403, 'Akses Ditolak: Fitur Manajemen Tabungan khusus untuk Bendahara / Finance.');
        }

        $this->tanggal = date('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterKelas()
    {
        $this->resetPage();
    }

    public function openTransactionModal($siswaId, $jenis = 'setor')
    {
        $this->resetValidation();
        $siswa = Siswa::with('user')->find($siswaId);
        if (!$siswa) {
            return;
        }

        $this->siswa_id = $siswa->id;
        $this->selectedSiswaNama = $siswa->user->nama ?? ('Siswa #' . $siswa->nis);
        $this->selectedSiswaSaldo = $this->getCurrentSaldo($siswa->id);
        $this->jenis = $jenis;
        $this->nominal = '';
        $this->tanggal = date('Y-m-d');
        $this->keterangan = '';
        $this->showTransactionModal = true;
    }

    public function openHistoryModal($siswaId)
    {
        $siswa = Siswa::with(['user', 'kelas'])->find($siswaId);
        if (!$siswa) {
            return;
        }

        $this->selectedSiswaHistory = $siswa;
        $this->historyTransactions = Tabungan::with('petugas')
            ->where('siswa_id', $siswaId)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $this->showHistoryModal = true;
    }

    public function closeModals()
    {
        $this->showTransactionModal = false;
        $this->showHistoryModal = false;
        $this->resetValidation();
    }

    public function saveTransaction()
    {
        $this->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'jenis' => 'required|in:setor,tarik',
            'nominal' => 'required|numeric|min:1000',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $currentSaldo = $this->getCurrentSaldo($this->siswa_id);

        if ($this->jenis === 'tarik' && $this->nominal > $currentSaldo) {
            $this->addError('nominal', 'Nominal penarikan (Rp ' . number_format($this->nominal, 0, ',', '.') . ') melebihi saldo tabungan saat ini (Rp ' . number_format($currentSaldo, 0, ',', '.') . ').');
            return;
        }

        $newSaldo = ($this->jenis === 'setor')
            ? ($currentSaldo + $this->nominal)
            : ($currentSaldo - $this->nominal);

        $kodeTx = 'TAB-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        Tabungan::create([
            'siswa_id' => $this->siswa_id,
            'petugas_id' => auth()->id(),
            'kode_transaksi' => $kodeTx,
            'jenis' => $this->jenis,
            'nominal' => $this->nominal,
            'saldo_akhir' => $newSaldo,
            'tanggal' => $this->tanggal,
            'keterangan' => $this->keterangan ?: ($this->jenis === 'setor' ? 'Setoran Tabungan Siswa' : 'Penarikan Tabungan Siswa'),
        ]);

        session()->flash('success', 'Transaksi tabungan ' . ($this->jenis === 'setor' ? 'setoran' : 'penarikan') . ' berhasil dicatat!');

        $this->closeModals();
    }

    private function getCurrentSaldo($siswaId)
    {
        $latest = Tabungan::where('siswa_id', $siswaId)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        return $latest ? (float) $latest->saldo_akhir : 0;
    }

    public function render()
    {
        $kelasList = Kelas::orderBy('nama_kelas', 'asc')->get();

        $siswaQuery = Siswa::with(['user', 'kelas', 'latestTabungan']);

        if ($this->search) {
            $siswaQuery->where(function ($q) {
                $q->whereHas('user', function ($uq) {
                    $uq->where('nama', 'like', '%' . $this->search . '%');
                })
                ->orWhere('nis', 'like', '%' . $this->search . '%')
                ->orWhere('nisn', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterKelas) {
            $siswaQuery->where('kelas_id', $this->filterKelas);
        }

        $siswas = $siswaQuery->orderBy('nis', 'asc')->paginate(12);

        // Compute summary metrics
        $totalSetorAll = Tabungan::where('jenis', 'setor')->sum('nominal');
        $totalTarikAll = Tabungan::where('jenis', 'tarik')->sum('nominal');
        $totalSaldoGlobal = $totalSetorAll - $totalTarikAll;
        $jumlahSiswaMenabung = Tabungan::distinct('siswa_id')->count('siswa_id');

        return view('livewire.finance.tabungan-siswa', [
            'siswas' => $siswas,
            'kelasList' => $kelasList,
            'totalSetorAll' => $totalSetorAll,
            'totalTarikAll' => $totalTarikAll,
            'totalSaldoGlobal' => $totalSaldoGlobal,
            'jumlahSiswaMenabung' => $jumlahSiswaMenabung,
        ])->layout('components.layouts.app', ['title' => 'Manajemen Tabungan Siswa']);
    }
}
