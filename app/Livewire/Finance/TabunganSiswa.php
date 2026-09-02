<?php

namespace App\Livewire\Finance;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Tabungan;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class TabunganSiswa extends Component
{
    use WithPagination;

    // Filter Saldo Siswa Table
    public $search = '';
    public $filterKelas = '';

    // Filter Riwayat / Jurnal Mutasi Seluruh Siswa Table
    public string $historySearch = '';
    public string $historyJenis = '';
    public string $historyFilterKelas = '';
    public string $historyFilterPeriode = 'semua';
    public ?string $historyStartDate = null;
    public ?string $historyEndDate = null;

    // Modal Transaction State
    public $showTransactionModal = false;
    public $siswa_id = null;
    public $selectedSiswaNama = '';
    public $selectedSiswaSaldo = 0;
    public $jenis = 'setor';
    public $nominal = '';
    public $tanggal = '';
    public $keterangan = '';

    // Modal Edit Transaction State
    public bool $showEditTransactionModal = false;
    public ?int $editingTabunganId = null;
    public string $edit_jenis = 'setor';
    public float $edit_nominal = 0.00;
    public string $edit_tanggal = '';
    public string $edit_keterangan = '';
    public string $edit_siswa_nama = '';

    // Modal History 1 Siswa State
    public $showHistoryModal = false;
    public $selectedSiswaHistory = null;
    public $historyTransactions = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'filterKelas' => ['except' => ''],
        'historySearch' => ['except' => ''],
        'historyJenis' => ['except' => ''],
        'historyFilterKelas' => ['except' => ''],
        'historyFilterPeriode' => ['except' => 'semua'],
    ];

    public function mount()
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role->nama ?? '', ['finance', 'super_admin', 'founder', 'kepala_sekolah'])) {
            abort(403, 'Akses Ditolak: Fitur Manajemen Tabungan khusus untuk Bendahara / Finance & Founder.');
        }

        $this->tanggal = date('Y-m-d');
    }

    public function isFounder(): bool
    {
        $role = auth()->user()->role->nama ?? '';
        return in_array($role, ['super_admin', 'founder']);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterKelas()
    {
        $this->resetPage();
    }

    public function updatingHistorySearch()
    {
        $this->resetPage('historyPage');
    }

    public function updatingHistoryJenis()
    {
        $this->resetPage('historyPage');
    }

    public function updatingHistoryFilterKelas()
    {
        $this->resetPage('historyPage');
    }

    public function updatingHistoryFilterPeriode()
    {
        $this->resetPage('historyPage');
    }

    public function updatingHistoryStartDate()
    {
        $this->resetPage('historyPage');
    }

    public function updatingHistoryEndDate()
    {
        $this->resetPage('historyPage');
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
        $this->loadHistory($siswaId);
        $this->showHistoryModal = true;
    }

    protected function loadHistory($siswaId)
    {
        $this->historyTransactions = Tabungan::with('petugas')
            ->where('siswa_id', $siswaId)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get();
    }

    public function closeModals()
    {
        $this->showTransactionModal = false;
        $this->showEditTransactionModal = false;
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

        $msg = 'Transaksi tabungan ' . ($this->jenis === 'setor' ? 'setoran' : 'penarikan') . ' berhasil dicatat!';
        session()->flash('success', $msg);
        $this->dispatch('show-alert', [
            'title' => 'Transaksi Dicatat',
            'message' => $msg,
            'type' => 'create',
        ]);

        $this->closeModals();
    }

    public function openEditTransaction(int $tabunganId)
    {
        $tx = Tabungan::with('siswa.user')->findOrFail($tabunganId);
        
        $this->resetValidation();
        $this->editingTabunganId = $tx->id;
        $this->edit_jenis = $tx->jenis;
        $this->edit_nominal = floatval($tx->nominal);
        $this->edit_tanggal = $tx->tanggal ? $tx->tanggal->format('Y-m-d') : date('Y-m-d');
        $this->edit_keterangan = $tx->keterangan ?? '';
        $this->edit_siswa_nama = $tx->siswa->user->nama ?? ('Siswa #' . $tx->siswa->nis);

        $this->showEditTransactionModal = true;
    }

    public function closeEditTransactionModal()
    {
        $this->showEditTransactionModal = false;
        $this->editingTabunganId = null;
        $this->resetValidation();
    }

    public function saveEditTransaction()
    {
        $this->validate([
            'editingTabunganId' => 'required|exists:tabungans,id',
            'edit_jenis' => 'required|in:setor,tarik',
            'edit_nominal' => 'required|numeric|min:1000',
            'edit_tanggal' => 'required|date',
            'edit_keterangan' => 'nullable|string|max:255',
        ]);

        $tx = Tabungan::findOrFail($this->editingTabunganId);
        $siswaId = $tx->siswa_id;

        DB::transaction(function () use ($tx, $siswaId) {
            $tx->update([
                'jenis' => $this->edit_jenis,
                'nominal' => $this->edit_nominal,
                'tanggal' => $this->edit_tanggal,
                'keterangan' => $this->edit_keterangan,
            ]);

            $this->recalculateStudentTabunganBalances($siswaId);
        });

        $msg = 'Transaksi tabungan berhasil diperbarui.';
        session()->flash('success', $msg);
        $this->dispatch('show-alert', [
            'title' => 'Transaksi Diperbarui',
            'message' => $msg,
            'type' => 'edit',
        ]);

        $this->closeEditTransactionModal();

        if ($this->showHistoryModal && $this->selectedSiswaHistory) {
            $this->loadHistory($this->selectedSiswaHistory->id);
        }
    }

    public function deleteTransaction(int $id)
    {
        if (!$this->isFounder()) {
            session()->flash('error', 'Akses Ditolak: Hanya Founder / Super Admin yang berhak menghapus catatan transaksi tabungan.');
            $this->dispatch('show-alert', [
                'title' => 'Akses Ditolak',
                'message' => 'Hanya Founder / Super Admin yang berhak menghapus catatan transaksi tabungan.',
                'type' => 'danger',
            ]);
            return;
        }

        $tx = Tabungan::findOrFail($id);
        $siswaId = $tx->siswa_id;

        DB::transaction(function () use ($tx, $siswaId) {
            $tx->delete();
            $this->recalculateStudentTabunganBalances($siswaId);
        });

        $msg = 'Catatan transaksi tabungan berhasil dihapus.';
        session()->flash('success', $msg);
        $this->dispatch('show-alert', [
            'title' => 'Transaksi Dihapus',
            'message' => $msg,
            'type' => 'delete',
        ]);

        if ($this->showHistoryModal && $this->selectedSiswaHistory) {
            $this->loadHistory($this->selectedSiswaHistory->id);
        }
    }

    public function recalculateStudentTabunganBalances(int $siswaId): void
    {
        $transactions = Tabungan::where('siswa_id', $siswaId)
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $runningBalance = 0.00;
        foreach ($transactions as $t) {
            if ($t->jenis === 'setor') {
                $runningBalance += (float) $t->nominal;
            } elseif ($t->jenis === 'tarik') {
                $runningBalance -= (float) $t->nominal;
            }
            $t->updateQuietly(['saldo_akhir' => $runningBalance]);
        }
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

        // 1. Query Data Siswa & Saldo Tabungan
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

        $siswas = $siswaQuery->orderBy('nis', 'asc')->paginate(10, ['*'], 'page');

        // 2. Query History Seluruh Riwayat Transaksi Mutasi Tabungan
        $historyQuery = Tabungan::with(['siswa.user', 'siswa.kelas', 'petugas']);

        if ($this->historySearch) {
            $historyQuery->where(function ($q) {
                $q->where('kode_transaksi', 'like', '%' . $this->historySearch . '%')
                  ->orWhere('keterangan', 'like', '%' . $this->historySearch . '%')
                  ->orWhereHas('siswa.user', function ($uq) {
                      $uq->where('nama', 'like', '%' . $this->historySearch . '%');
                  })
                  ->orWhereHas('siswa', function ($sq) {
                      $sq->where('nis', 'like', '%' . $this->historySearch . '%');
                  })
                  ->orWhereHas('petugas', function ($pq) {
                      $pq->where('nama', 'like', '%' . $this->historySearch . '%');
                  });
            });
        }

        if ($this->historyJenis && in_array($this->historyJenis, ['setor', 'tarik'])) {
            $historyQuery->where('jenis', $this->historyJenis);
        }

        if ($this->historyFilterKelas) {
            $historyQuery->whereHas('siswa', function ($sq) {
                $sq->where('kelas_id', $this->historyFilterKelas);
            });
        }

        if ($this->historyFilterPeriode === 'hari_ini') {
            $historyQuery->whereDate('tanggal', date('Y-m-d'));
        } elseif ($this->historyFilterPeriode === 'kemarin') {
            $historyQuery->whereDate('tanggal', date('Y-m-d', strtotime('-1 day')));
        } elseif ($this->historyFilterPeriode === 'minggu_ini') {
            $historyQuery->whereBetween('tanggal', [now()->startOfWeek()->format('Y-m-d'), now()->endOfWeek()->format('Y-m-d')]);
        } elseif ($this->historyFilterPeriode === 'bulan_ini') {
            $historyQuery->whereBetween('tanggal', [now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d')]);
        } elseif ($this->historyFilterPeriode === 'custom' || ($this->historyStartDate && $this->historyEndDate)) {
            if ($this->historyStartDate && $this->historyEndDate) {
                $historyQuery->whereBetween('tanggal', [$this->historyStartDate, $this->historyEndDate]);
            } elseif ($this->historyStartDate) {
                $historyQuery->whereDate('tanggal', '>=', $this->historyStartDate);
            } elseif ($this->historyEndDate) {
                $historyQuery->whereDate('tanggal', '<=', $this->historyEndDate);
            }
        }

        $allHistoryTransactions = $historyQuery->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate(12, ['*'], 'historyPage');

        // Compute summary metrics
        $totalSetorAll = Tabungan::where('jenis', 'setor')->sum('nominal');
        $totalTarikAll = Tabungan::where('jenis', 'tarik')->sum('nominal');
        $totalSaldoGlobal = $totalSetorAll - $totalTarikAll;
        $jumlahSiswaMenabung = Tabungan::distinct('siswa_id')->count('siswa_id');

        return view('livewire.finance.tabungan-siswa', [
            'siswas' => $siswas,
            'allHistoryTransactions' => $allHistoryTransactions,
            'kelasList' => $kelasList,
            'totalSetorAll' => $totalSetorAll,
            'totalTarikAll' => $totalTarikAll,
            'totalSaldoGlobal' => $totalSaldoGlobal,
            'jumlahSiswaMenabung' => $jumlahSiswaMenabung,
            'isFounder' => $this->isFounder(),
        ])->layout('components.layouts.app', ['title' => 'Manajemen Tabungan Siswa']);
    }
}
