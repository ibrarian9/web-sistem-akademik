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
    public string $filterBulan = '';
    public string $search = '';

    // Modals
    public bool $showCreateModal = false;
    public bool $showEditModal = false;
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

    // Edit Tagihan Form properties
    public ?int $editingTagihanId = null;
    public ?int $edit_jenis_tagihan_id = null;
    public string $edit_bulan = 'Juli';
    public float $edit_nominal = 0.00;
    public string $edit_jatuh_tempo = '';
    public float $edit_total_dibayar = 0.00;
    public string $edit_siswa_nama = '';

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

    public function isFounder(): bool
    {
        $role = auth()->user()->role->nama ?? '';
        return in_array($role, ['super_admin', 'founder']);
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
            $this->dispatch('show-alert', ['title' => 'Peringatan', 'message' => 'Tidak ada tahun ajaran aktif.', 'type' => 'warning']);
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

        $msg = "Berhasil merilis tagihan untuk siswa " . ($siswa->user->nama ?? 'Siswa') . ".";
        session()->flash('message', $msg);
        $this->dispatch('show-alert', [
            'title' => 'Tagihan Diterbitkan',
            'message' => $msg,
            'type' => 'create',
        ]);

        $this->reset(['single_siswa_id', 'jenis_tagihan_id', 'bulan', 'nominal']);
        $this->showCreateModal = false;
        
        if ($this->showDetailModal && $this->selectedSiswaId) {
            $this->loadSelectedSiswa();
        }
        
        $this->resetPage();
    }

    public function openEditModal(int $tagihanId)
    {
        $tagihan = Tagihan::with(['siswa.user', 'jenisTagihan'])->findOrFail($tagihanId);
        
        $this->resetValidation();
        $this->editingTagihanId = $tagihan->id;
        $this->edit_jenis_tagihan_id = $tagihan->jenis_tagihan_id;
        $this->edit_bulan = $tagihan->bulan ?? 'Juli';
        $this->edit_nominal = floatval($tagihan->nominal);
        $this->edit_total_dibayar = floatval($tagihan->total_dibayar);
        $this->edit_jatuh_tempo = $tagihan->jatuh_tempo ? $tagihan->jatuh_tempo->format('Y-m-d') : date('Y-m-d', strtotime('+30 days'));
        $this->edit_siswa_nama = $tagihan->siswa->user->nama ?? ('Siswa #' . $tagihan->siswa->nis);

        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetValidation();
        $this->editingTagihanId = null;
    }

    public function saveEditTagihan()
    {
        $this->validate([
            'editingTagihanId' => 'required|exists:tagihan,id',
            'edit_jenis_tagihan_id' => 'required|exists:jenis_tagihan,id',
            'edit_bulan' => 'required|string|max:50',
            'edit_nominal' => 'required|numeric|min:1',
            'edit_jatuh_tempo' => 'required|date',
        ]);

        $tagihan = Tagihan::findOrFail($this->editingTagihanId);

        if ($this->edit_nominal < $tagihan->total_dibayar) {
            $this->addError('edit_nominal', 'Nominal tagihan baru tidak boleh lebih kecil dari jumlah yang sudah dibayarkan (Rp ' . number_format($tagihan->total_dibayar, 0, ',', '.') . ').');
            return;
        }

        // Recalculate status based on new nominal
        $status = 'belum_bayar';
        if ($tagihan->total_dibayar >= $this->edit_nominal) {
            $status = 'lunas';
        } elseif ($tagihan->total_dibayar > 0) {
            $status = 'sebagian';
        }

        $tagihan->update([
            'jenis_tagihan_id' => $this->edit_jenis_tagihan_id,
            'bulan' => $this->edit_bulan,
            'nominal' => $this->edit_nominal,
            'status' => $status,
            'jatuh_tempo' => $this->edit_jatuh_tempo,
        ]);

        $msg = 'Tagihan berhasil diperbarui.';
        session()->flash('message', $msg);
        $this->dispatch('show-alert', [
            'title' => 'Tagihan Diperbarui',
            'message' => $msg,
            'type' => 'edit',
        ]);

        $this->closeEditModal();

        if ($this->showDetailModal && $this->selectedSiswaId) {
            $this->loadSelectedSiswa();
        }
    }

    public function deleteTagihan(int $id)
    {
        if (!$this->isFounder()) {
            session()->flash('error', 'Akses Ditolak: Hanya Founder / Super Admin yang berhak menghapus data tagihan.');
            $this->dispatch('show-alert', [
                'title' => 'Akses Ditolak',
                'message' => 'Hanya Founder / Super Admin yang berhak menghapus data tagihan.',
                'type' => 'danger',
            ]);
            return;
        }

        $tagihan = Tagihan::findOrFail($id);

        if ($tagihan->total_dibayar > 0) {
            session()->flash('error', 'Tagihan ini sudah pernah dibayar sebagian/lunas, tidak dapat dihapus.');
            $this->dispatch('show-alert', [
                'title' => 'Peringatan',
                'message' => 'Tagihan ini sudah pernah dibayar sebagian/lunas, tidak dapat dihapus.',
                'type' => 'warning',
            ]);
            return;
        }

        $tagihan->delete();
        $msg = 'Tagihan berhasil dihapus/dibatalkan.';
        session()->flash('message', $msg);
        $this->dispatch('show-alert', [
            'title' => 'Tagihan Dihapus',
            'message' => $msg,
            'type' => 'delete',
        ]);

        if ($this->showDetailModal && $this->selectedSiswaId) {
            $this->loadSelectedSiswa();
        }
    }

    public function bulkDelete()
    {
        if (!$this->isFounder()) {
            session()->flash('error', 'Akses Ditolak: Hanya Founder / Super Admin yang berhak menghapus data tagihan.');
            $this->dispatch('show-alert', [
                'title' => 'Akses Ditolak',
                'message' => 'Hanya Founder / Super Admin yang berhak menghapus data tagihan.',
                'type' => 'danger',
            ]);
            return;
        }

        if (empty($this->selectedIds)) {
            return;
        }

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
        $this->dispatch('show-alert', [
            'title' => 'Hapus Massal Tagihan',
            'message' => $msg,
            'type' => 'delete',
        ]);

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
            'isFounder' => $this->isFounder(),
        ])->layout('components.layouts.app', ['title' => 'Manajemen Tagihan']);
    }
}
