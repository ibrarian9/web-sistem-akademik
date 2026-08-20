<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Models\Tagihan;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use App\Traits\WithDateFilter;
use Livewire\WithPagination;

class ManajemenTagihan extends Component
{
    use WithPagination, WithDateFilter;

    // Filters
    public ?int $filterKelas = null;
    public ?int $filterJenis = null;
    public string $filterStatus = '';
    public string $filterBulan = ''; // NEW Month filter
    public string $search = '';

    // Modals
    public bool $showCreateModal = false;
    public bool $showDetailModal = false;

    // Selected Student for Detail Modal
    public ?int $selectedSiswaId = null;
    public ?Siswa $selectedSiswa = null;

    // Create Tagihan Form properties
    public ?int $single_siswa_id = null;
    public ?int $jenis_tagihan_id = null;
    public string $bulan = 'Juli';
    public float $nominal = 0.00;
    public string $jatuh_tempo = '';

    // Bulk selection (Siswa IDs)
    public array $selectedIds = [];
    public bool $selectAll = false;

    // Option lists
    public array $classes = [];
    public array $jenisTagihans = [];
    public array $bulanOptions = [
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Semester Ganjil', 'Semester Genap', 'Tahunan'
    ];

    public function mount()
    {
        $this->classes = Kelas::orderBy('nama_kelas')->get()->toArray();
        $this->jenisTagihans = JenisTagihan::where('nama', 'not like', '%Infaq%')
            ->where('nama', 'not like', '%Sedekah%')
            ->where('nama', 'not like', '%Donasi%')
            ->get()
            ->toArray();
        $this->jatuh_tempo = date('Y-m-d', strtotime('+30 days'));
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatingFilterKelas()
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatingFilterJenis()
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatingFilterBulan()
    {
        $this->resetPage();
        $this->resetSelection();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = $this->getCurrentStudentIds();
        } else {
            $this->selectedIds = [];
        }
    }

    public function resetSelection()
    {
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    protected function getCurrentStudentIds(): array
    {
        $query = Siswa::whereHas('tagihans', function ($q) {
            if ($this->filterJenis) {
                $q->where('jenis_tagihan_id', $this->filterJenis);
            }
            if ($this->filterStatus) {
                $q->where('status', $this->filterStatus);
            }
            if ($this->filterBulan) {
                $q->where('bulan', $this->filterBulan);
            }
            $this->applyDateFilter($q, 'created_at');
        });

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('user', function ($sub) {
                    $sub->where('nama', 'like', '%' . $this->search . '%');
                })->orWhere('nis', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterKelas) {
            $query->where('kelas_id', $this->filterKelas);
        }

        return $query->pluck('id')->map(fn($id) => (string) $id)->toArray();
    }

    public function updatedJenisTagihanId($value)
    {
        if ($value) {
            $this->nominal = floatval(JenisTagihan::where('id', $value)->value('default_nominal') ?? 0.00);
        }
    }

    public function openCreateModal(?int $siswaId = null)
    {
        $this->resetValidation();
        if ($siswaId) {
            $this->single_siswa_id = $siswaId;
        }
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetValidation();
    }

    public function openDetail(int $siswaId)
    {
        $this->selectedSiswaId = $siswaId;
        $this->loadSelectedSiswa();
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedSiswaId = null;
        $this->selectedSiswa = null;
    }

    protected function loadSelectedSiswa()
    {
        if ($this->selectedSiswaId) {
            $this->selectedSiswa = Siswa::with(['user', 'kelas', 'tagihans' => function ($q) {
                $q->with(['jenisTagihan', 'tahunAjaran', 'pembayarans'])->latest();
            }])->find($this->selectedSiswaId);
        }
    }

    public function createSingleTagihan()
    {
        $this->validate([
            'single_siswa_id' => 'required|exists:siswa,id',
            'jenis_tagihan_id' => 'required|exists:jenis_tagihan,id',
            'bulan' => 'required|string|max:50',
            'nominal' => 'required|numeric|min:1',
            'jatuh_tempo' => 'required|date',
        ]);

        $activeTA = TahunAjaran::where('status_aktif', true)->first();
        if (!$activeTA) {
            session()->flash('error', 'Tidak ada tahun ajaran aktif.');
            return;
        }

        $siswa = Siswa::with('user')->findOrFail($this->single_siswa_id);

        Tagihan::create([
            'siswa_id' => $siswa->id,
            'jenis_tagihan_id' => $this->jenis_tagihan_id,
            'tahun_ajaran_id' => $activeTA->id,
            'bulan' => $this->bulan,
            'nominal' => $this->nominal,
            'total_dibayar' => 0.00,
            'status' => 'belum_bayar',
            'jatuh_tempo' => $this->jatuh_tempo,
        ]);

        session()->flash('message', "Berhasil merilis tagihan untuk siswa " . ($siswa->user->nama ?? 'Siswa') . ".");
        $this->reset(['single_siswa_id', 'jenis_tagihan_id', 'bulan', 'nominal']);
        $this->showCreateModal = false;
        
        if ($this->showDetailModal && $this->selectedSiswaId) {
            $this->loadSelectedSiswa();
        }
        
        $this->resetPage();
    }

    public function deleteTagihan(int $id)
    {
        $tagihan = Tagihan::findOrFail($id);

        if ($tagihan->total_dibayar > 0) {
            session()->flash('error', 'Tagihan ini sudah pernah dibayar sebagian/lunas, tidak dapat dihapus.');
            return;
        }

        $tagihan->delete();
        session()->flash('message', 'Tagihan berhasil dihapus/dibatalkan.');

        if ($this->showDetailModal && $this->selectedSiswaId) {
            $this->loadSelectedSiswa();
        }
    }

    public function bulkDelete()
    {
        if (empty($this->selectedIds)) {
            return;
        }

        // Support both Tagihan IDs and Siswa IDs in selectedIds
        $tagihans = Tagihan::where(function ($q) {
            $q->whereIn('id', $this->selectedIds)
              ->orWhereIn('siswa_id', $this->selectedIds);
        })->get();

        $deletedCount = 0;
        $skippedCount = 0;

        foreach ($tagihans as $tagihan) {
            if ($tagihan->total_dibayar == 0) {
                $tagihan->delete();
                $deletedCount++;
            } else {
                $skippedCount++;
            }
        }

        $msg = "Berhasil menghapus {$deletedCount} tagihan.";
        if ($skippedCount > 0) {
            $msg .= " ({$skippedCount} tagihan dilewati karena sudah ada pembayaran).";
        }

        session()->flash('message', $msg);
        $this->resetSelection();
        $this->resetPage();

        if ($this->showDetailModal && $this->selectedSiswaId) {
            $this->loadSelectedSiswa();
        }
    }

    public function render()
    {
        // Query Siswa grouped with tagihans
        $query = Siswa::whereHas('tagihans', function ($q) {
            if ($this->filterJenis) {
                $q->where('jenis_tagihan_id', $this->filterJenis);
            }
            if ($this->filterStatus) {
                $q->where('status', $this->filterStatus);
            }
            if ($this->filterBulan) {
                $q->where('bulan', $this->filterBulan);
            }
            $this->applyDateFilter($q, 'created_at');
        })->with(['user', 'kelas', 'tagihans' => function ($q) {
            if ($this->filterJenis) {
                $q->where('jenis_tagihan_id', $this->filterJenis);
            }
            if ($this->filterStatus) {
                $q->where('status', $this->filterStatus);
            }
            if ($this->filterBulan) {
                $q->where('bulan', $this->filterBulan);
            }
            $this->applyDateFilter($q, 'created_at');
            $q->with(['jenisTagihan', 'pembayarans']);
        }]);

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('user', function ($sub) {
                    $sub->where('nama', 'like', '%' . $this->search . '%');
                })->orWhere('nis', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterKelas) {
            $query->where('kelas_id', $this->filterKelas);
        }

        $students = $query->paginate(15);
        $allStudents = Siswa::where('status', 'aktif')->with('user', 'kelas')->get();

        return view('livewire.finance.manajemen-tagihan', [
            'students' => $students,
            'allStudents' => $allStudents,
        ])->layout('components.layouts.app', ['title' => 'Manajemen Tagihan']);
    }
}
