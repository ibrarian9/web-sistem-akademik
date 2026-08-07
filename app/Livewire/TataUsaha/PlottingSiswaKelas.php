<?php

namespace App\Livewire\TataUsaha;

use App\Models\Kelas;
use App\Models\Siswa;
use Livewire\Component;
use Livewire\WithPagination;

class PlottingSiswaKelas extends Component
{
    use WithPagination;

    public ?int $selected_kelas_id = null;
    public string $filter_jenis = 'semua'; // 'semua', 'umum', 'tahfidz'
    public string $search_roster = '';
    public string $search_candidates = '';
    public array $selected_siswa_ids = [];
    public bool $showAddModal = false;

    public int $perPage = 15;

    protected $queryString = [
        'selected_kelas_id' => ['except' => null],
        'filter_jenis' => ['except' => 'semua'],
    ];

    public function mount()
    {
        if (!$this->selected_kelas_id) {
            $firstKelas = Kelas::orderBy('jenis_kelas', 'asc')->orderBy('nama_kelas', 'asc')->first();
            if ($firstKelas) {
                $this->selected_kelas_id = $firstKelas->id;
            }
        }
    }

    public function updatedSelectedKelasId()
    {
        $this->resetPage();
        $this->selected_siswa_ids = [];
        $this->search_roster = '';
    }

    public function updatedFilterJenis()
    {
        $firstKelas = Kelas::when($this->filter_jenis !== 'semua', function ($q) {
            $q->where('jenis_kelas', $this->filter_jenis);
        })->orderBy('nama_kelas', 'asc')->first();

        if ($firstKelas) {
            $this->selected_kelas_id = $firstKelas->id;
        } else {
            $this->selected_kelas_id = null;
        }
        $this->resetPage();
    }

    public function openAddModal()
    {
        $this->selected_siswa_ids = [];
        $this->search_candidates = '';
        $this->showAddModal = true;
    }

    public function closeAddModal()
    {
        $this->showAddModal = false;
        $this->selected_siswa_ids = [];
    }

    public function assignSiswaToKelas()
    {
        if (!$this->selected_kelas_id) {
            session()->flash('error', 'Silakan pilih kelas target terlebih dahulu.');
            return;
        }

        if (empty($this->selected_siswa_ids)) {
            session()->flash('error', 'Tidak ada siswa yang dipilih.');
            return;
        }

        $targetKelas = Kelas::findOrFail($this->selected_kelas_id);
        $count = count($this->selected_siswa_ids);

        if ($targetKelas->jenis_kelas === 'tahfidz') {
            Siswa::whereIn('id', $this->selected_siswa_ids)->update([
                'kelas_tahfidz_id' => $targetKelas->id,
            ]);
        } else {
            Siswa::whereIn('id', $this->selected_siswa_ids)->update([
                'kelas_id' => $targetKelas->id,
            ]);
        }

        session()->flash('message', "{$count} Murid berhasil dimasukkan ke {$targetKelas->nama_kelas}.");
        $this->showAddModal = false;
        $this->selected_siswa_ids = [];
    }

    public function unassignSiswa($siswaId)
    {
        if (!$this->selected_kelas_id) {
            return;
        }

        $targetKelas = Kelas::findOrFail($this->selected_kelas_id);
        $siswa = Siswa::find($siswaId);

        if (!$siswa) {
            return;
        }

        if ($targetKelas->jenis_kelas === 'tahfidz') {
            $siswa->update(['kelas_tahfidz_id' => null]);
        } else {
            $siswa->update(['kelas_id' => null]);
        }

        session()->flash('message', "Siswa {$siswa->user->nama} berhasil dikeluarkan dari {$targetKelas->nama_kelas}.");
    }

    public function render()
    {
        $kelasesQuery = Kelas::with(['guruUmum.user', 'guruTahfidz.user']);
        if ($this->filter_jenis !== 'semua') {
            $kelasesQuery->where('jenis_kelas', $this->filter_jenis);
        }
        $kelases = $kelasesQuery->orderBy('jenis_kelas', 'asc')->orderBy('nama_kelas', 'asc')->get();

        $selectedKelas = $this->selected_kelas_id ? Kelas::with(['guruUmum.user', 'guruTahfidz.user'])->find($this->selected_kelas_id) : null;

        $rosterQuery = Siswa::query();
        if ($selectedKelas) {
            if ($selectedKelas->jenis_kelas === 'tahfidz') {
                $rosterQuery->where('kelas_tahfidz_id', $selectedKelas->id);
            } else {
                $rosterQuery->where('kelas_id', $selectedKelas->id);
            }
        } else {
            $rosterQuery->whereRaw('1 = 0');
        }

        if (!empty($this->search_roster)) {
            $q = $this->search_roster;
            $rosterQuery->where(function ($sq) use ($q) {
                $sq->where('nis', 'like', "%{$q}%")
                  ->orWhere('nisn', 'like', "%{$q}%")
                  ->orWhereHas('user', function ($uq) use ($q) {
                      $uq->where('nama', 'like', "%{$q}%");
                  });
            });
        }

        $roster = $rosterQuery->with(['user', 'kelas', 'kelasTahfidz'])->latest()->paginate($this->perPage);

        // Candidate students for Add Modal (Students not in this target class or unassigned)
        $candidates = collect();
        if ($this->showAddModal && $selectedKelas) {
            $candidateQuery = Siswa::with(['user', 'kelas', 'kelasTahfidz'])->where('status', 'aktif');

            if ($selectedKelas->jenis_kelas === 'tahfidz') {
                $candidateQuery->where(function ($cq) use ($selectedKelas) {
                    $cq->whereNull('kelas_tahfidz_id')
                      ->orWhere('kelas_tahfidz_id', '!=', $selectedKelas->id);
                });
            } else {
                $candidateQuery->where(function ($cq) use ($selectedKelas) {
                    $cq->whereNull('kelas_id')
                      ->orWhere('kelas_id', '!=', $selectedKelas->id);
                });
            }

            if (!empty($this->search_candidates)) {
                $cqTerm = $this->search_candidates;
                $candidateQuery->where(function ($sq) use ($cqTerm) {
                    $sq->where('nis', 'like', "%{$cqTerm}%")
                      ->orWhere('nisn', 'like', "%{$cqTerm}%")
                      ->orWhereHas('user', function ($uq) use ($cqTerm) {
                          $uq->where('nama', 'like', "%{$cqTerm}%");
                      });
                });
            }

            $candidates = $candidateQuery->limit(50)->get();
        }

        return view('livewire.tata-usaha.plotting-siswa-kelas', [
            'kelases' => $kelases,
            'selectedKelas' => $selectedKelas,
            'roster' => $roster,
            'candidates' => $candidates,
        ])->layout('components.layouts.app', ['title' => 'Plotting Siswa Per-Kelas']);
    }
}
