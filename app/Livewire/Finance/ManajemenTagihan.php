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
use Illuminate\Support\Facades\DB;

class ManajemenTagihan extends Component
{
    use WithPagination, WithDateFilter;

    // Filters for Main Table
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

    // Create / Release Tagihan Form properties
    public string $releaseMode = 'bulk'; // 'single' | 'bulk'
    public string $bulkTarget = 'custom'; // 'custom' (Pilih Beberapa Siswa Lintas Kelas) | 'class' (Per Kelas) | 'all' (Seluruh Siswa)
    
    // Single Mode properties
    public ?int $release_kelas_id = null;
    public string $studentSearch = '';
    public ?int $single_siswa_id = null;
    public string $selectedStudentName = '';
    public string $selectedStudentNis = '';
    public string $selectedStudentKelas = '';

    // Bulk Custom Multi-Select properties
    public array $bulkSelectedSiswaIds = [];
    public string $bulkSearchStudent = '';
    public ?int $bulkSearchKelasId = null;

    // Tagihan Form values
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

    // Bulk selection (Siswa IDs for deletion)
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

    public function setReleaseMode(string $mode)
    {
        $this->releaseMode = $mode;
        $this->resetValidation();
    }

    public function setBulkTarget(string $target)
    {
        $this->bulkTarget = $target;
        $this->resetValidation();
    }

    // Single student selection methods
    public function selectStudent(int $id)
    {
        $siswa = Siswa::with('user', 'kelas')->find($id);
        if ($siswa) {
            $this->single_siswa_id = $siswa->id;
            $this->selectedStudentName = $siswa->user->nama ?? '-';
            $this->selectedStudentNis = $siswa->nis ?? '-';
            $this->selectedStudentKelas = $siswa->kelas->nama_kelas ?? '-';
            $this->studentSearch = '';
        }
    }

    public function clearSelectedStudent()
    {
        $this->single_siswa_id = null;
        $this->selectedStudentName = '';
        $this->selectedStudentNis = '';
        $this->selectedStudentKelas = '';
        $this->studentSearch = '';
    }

    // Multi-student batch selection methods
    public function addSiswaToBulk(int $siswaId)
    {
        if (!in_array($siswaId, $this->bulkSelectedSiswaIds)) {
            $this->bulkSelectedSiswaIds[] = $siswaId;
        }
    }

    public function removeSiswaFromBulk(int $siswaId)
    {
        $this->bulkSelectedSiswaIds = array_values(array_diff($this->bulkSelectedSiswaIds, [$siswaId]));
    }

    public function clearBulkSelected()
    {
        $this->bulkSelectedSiswaIds = [];
    }

    public function addAllFoundToBulk()
    {
        $bQuery = Siswa::where('status', 'aktif');
        if ($this->bulkSearchKelasId) {
            $bQuery->where('kelas_id', $this->bulkSearchKelasId);
        }
        if (trim($this->bulkSearchStudent) !== '') {
            $bQuery->where(function ($q) {
                $q->whereHas('user', function ($uq) {
                    $uq->where('nama', 'like', '%' . $this->bulkSearchStudent . '%');
                })->orWhere('nis', 'like', '%' . $this->bulkSearchStudent . '%');
            });
        }
        $foundIds = $bQuery->pluck('id')->toArray();
        $this->bulkSelectedSiswaIds = array_values(array_unique(array_merge($this->bulkSelectedSiswaIds, $foundIds)));
    }

    public function openCreateModal(?int $siswaId = null)
    {
        $this->resetValidation();
        $this->releaseMode = $siswaId ? 'single' : 'bulk';
        $this->bulkTarget = 'custom';
        $this->release_kelas_id = null;
        $this->studentSearch = '';
        $this->single_siswa_id = null;
        $this->selectedStudentName = '';
        $this->selectedStudentNis = '';
        $this->selectedStudentKelas = '';
        $this->bulkSelectedSiswaIds = [];
        $this->bulkSearchStudent = '';
        $this->bulkSearchKelasId = null;

        if ($siswaId) {
            $this->selectStudent($siswaId);
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
            'nominal' => 'required|numeric|min:0',
            'jatuh_tempo' => 'required|date',
        ], [
            'single_siswa_id.required' => 'Pilih siswa penerima tagihan terlebih dahulu.',
        ]);

        $activeTA = TahunAjaran::where('status_aktif', true)->first();
        if (!$activeTA) {
            session()->flash('error', 'Tidak ada tahun ajaran aktif.');
            $this->dispatch('show-alert', ['title' => 'Peringatan', 'message' => 'Tidak ada tahun ajaran aktif.', 'type' => 'warning']);
            return;
        }

        $siswa = Siswa::with('user')->findOrFail($this->single_siswa_id);

        // Check if duplicate tagihan already exists
        $existing = Tagihan::where('siswa_id', $siswa->id)
            ->where('jenis_tagihan_id', $this->jenis_tagihan_id)
            ->where('tahun_ajaran_id', $activeTA->id)
            ->where('bulan', $this->bulan)
            ->first();

        if ($existing) {
            $msg = "Siswa ini sudah memiliki tagihan " . ($existing->jenisTagihan->nama ?? 'Tagihan') . " untuk periode " . $this->bulan . ".";
            session()->flash('warning', $msg);
            $this->dispatch('show-alert', [
                'title' => 'Tagihan Sudah Ada',
                'message' => $msg,
                'type' => 'warning',
            ]);
            $this->addError('bulan', $msg);
            return;
        }

        $status = ($this->nominal <= 0) ? 'lunas' : 'belum_bayar';

        Tagihan::create([
            'siswa_id' => $siswa->id,
            'jenis_tagihan_id' => $this->jenis_tagihan_id,
            'tahun_ajaran_id' => $activeTA->id,
            'bulan' => $this->bulan,
            'nominal' => $this->nominal,
            'total_dibayar' => 0.00,
            'status' => $status,
            'jatuh_tempo' => $this->jatuh_tempo,
        ]);

        $msg = "Berhasil merilis tagihan untuk siswa " . ($siswa->user->nama ?? 'Siswa') . ($this->nominal <= 0 ? " (Nominal Rp 0 - Otomatis Lunas)." : ".");
        session()->flash('message', $msg);
        $this->dispatch('show-alert', [
            'title' => 'Tagihan Diterbitkan',
            'message' => $msg,
            'type' => 'create',
        ]);

        $this->clearSelectedStudent();
        $this->showCreateModal = false;
        
        if ($this->showDetailModal && $this->selectedSiswaId) {
            $this->loadSelectedSiswa();
        }
        
        $this->resetPage();
    }

    public function createBulkTagihan()
    {
        $rules = [
            'jenis_tagihan_id' => 'required|exists:jenis_tagihan,id',
            'bulan' => 'required|string|max:50',
            'nominal' => 'required|numeric|min:0',
            'jatuh_tempo' => 'required|date',
        ];

        if ($this->bulkTarget === 'class') {
            $rules['release_kelas_id'] = 'required|exists:kelas,id';
        }

        $this->validate($rules, [
            'release_kelas_id.required' => 'Pilih kelas target untuk rilis massal.',
        ]);

        if ($this->bulkTarget === 'custom' && count($this->bulkSelectedSiswaIds) === 0) {
            $this->addError('bulkSelectedSiswaIds', 'Pilih minimal 1 siswa penerima tagihan.');
            return;
        }

        $activeTA = TahunAjaran::where('status_aktif', true)->first();
        if (!$activeTA) {
            session()->flash('error', 'Tidak ada tahun ajaran aktif.');
            $this->dispatch('show-alert', ['title' => 'Peringatan', 'message' => 'Tidak ada tahun ajaran aktif.', 'type' => 'warning']);
            return;
        }

        $targetStudents = collect();
        if ($this->bulkTarget === 'custom') {
            $targetStudents = Siswa::where('status', 'aktif')->whereIn('id', $this->bulkSelectedSiswaIds)->get();
        } elseif ($this->bulkTarget === 'class') {
            $targetStudents = Siswa::where('status', 'aktif')->where('kelas_id', $this->release_kelas_id)->get();
        } elseif ($this->bulkTarget === 'all') {
            $targetStudents = Siswa::where('status', 'aktif')->get();
        }

        if ($targetStudents->isEmpty()) {
            session()->flash('error', 'Tidak ada siswa aktif pada target yang dipilih.');
            $this->dispatch('show-alert', ['title' => 'Peringatan', 'message' => 'Tidak ada siswa aktif pada target yang dipilih.', 'type' => 'warning']);
            return;
        }

        $createdCount = 0;
        $skippedCount = 0;

        DB::transaction(function () use ($targetStudents, $activeTA, &$createdCount, &$skippedCount) {
            $status = ($this->nominal <= 0) ? 'lunas' : 'belum_bayar';

            foreach ($targetStudents as $siswa) {
                // Check if duplicate tagihan exists
                $exists = Tagihan::where('siswa_id', $siswa->id)
                    ->where('jenis_tagihan_id', $this->jenis_tagihan_id)
                    ->where('tahun_ajaran_id', $activeTA->id)
                    ->where('bulan', $this->bulan)
                    ->exists();

                if (!$exists) {
                    Tagihan::create([
                        'siswa_id' => $siswa->id,
                        'jenis_tagihan_id' => $this->jenis_tagihan_id,
                        'tahun_ajaran_id' => $activeTA->id,
                        'bulan' => $this->bulan,
                        'nominal' => $this->nominal,
                        'total_dibayar' => 0.00,
                        'status' => $status,
                        'jatuh_tempo' => $this->jatuh_tempo,
                    ]);
                    $createdCount++;
                } else {
                    $skippedCount++;
                }
            }
        });

        if ($createdCount === 0 && $skippedCount > 0) {
            $msg = "Tidak ada tagihan baru yang diterbitkan. ({$skippedCount} siswa dilewati karena sudah memiliki tagihan ini).";
            session()->flash('warning', $msg);
            $this->dispatch('show-alert', [
                'title' => 'Tagihan Sudah Ada',
                'message' => $msg,
                'type' => 'warning',
            ]);
        } elseif ($createdCount > 0 && $skippedCount > 0) {
            $msg = "Berhasil merilis tagihan untuk {$createdCount} siswa ({$skippedCount} siswa dilewati karena sudah memiliki tagihan ini).";
            session()->flash('message', $msg);
            $this->dispatch('show-alert', [
                'title' => 'Rilis Massal Selesai',
                'message' => $msg,
                'type' => 'create',
            ]);
        } else {
            $msg = "Berhasil merilis tagihan untuk {$createdCount} siswa.";
            session()->flash('message', $msg);
            $this->dispatch('show-alert', [
                'title' => 'Rilis Massal Selesai',
                'message' => $msg,
                'type' => 'create',
            ]);
        }

        $this->bulkSelectedSiswaIds = [];
        $this->showCreateModal = false;
        $this->resetPage();

        if ($this->showDetailModal && $this->selectedSiswaId) {
            $this->loadSelectedSiswa();
        }
    }

    public function openEditModal(int $tagihanId)
    {
        $tagihan = Tagihan::with(['siswa.user', 'jenisTagihan'])->findOrFail($tagihanId);
        
        $this->resetValidation();
        $this->editingTagihanId = $tagihan->id;
        $this->resetValidation();
        $t = Tagihan::with(['siswa.user', 'jenisTagihan'])->findOrFail($tagihanId);
        
        $this->editingTagihanId = $t->id;
        $this->edit_jenis_tagihan_id = $t->jenis_tagihan_id;
        $this->edit_bulan = $t->bulan;
        $this->edit_nominal = floatval($t->nominal);
        $this->edit_jatuh_tempo = $t->jatuh_tempo ? date('Y-m-d', strtotime($t->jatuh_tempo)) : '';
        $this->edit_total_dibayar = floatval($t->total_dibayar);
        $this->edit_siswa_nama = ($t->siswa->user->nama ?? 'Siswa') . ' (' . ($t->jenisTagihan->nama ?? 'Tagihan') . ' - ' . $t->bulan . ')';
        
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingTagihanId = null;
        $this->resetValidation();
    }

    public function saveEditTagihan()
    {
        $this->validate([
            'editingTagihanId' => 'required|exists:tagihan,id',
            'edit_jenis_tagihan_id' => 'required|exists:jenis_tagihan,id',
            'edit_bulan' => 'required|string|max:50',
            'edit_nominal' => 'required|numeric|min:0',
            'edit_jatuh_tempo' => 'required|date',
        ]);

        $tagihan = Tagihan::findOrFail($this->editingTagihanId);

        if ($this->edit_nominal < $tagihan->total_dibayar) {
            $this->addError('edit_nominal', 'Nominal tagihan baru tidak boleh lebih kecil dari jumlah yang sudah dibayarkan (Rp ' . number_format($tagihan->total_dibayar, 0, ',', '.') . ').');
            return;
        }

        // Recalculate status based on new nominal
        $status = 'belum_bayar';
        if ($this->edit_nominal <= 0 || $tagihan->total_dibayar >= $this->edit_nominal) {
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
        // Query Siswa grouped with tagihans for Main Table
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

        // Search Autocomplete for Single Mode
        $searchedStudents = [];
        if ($this->showCreateModal && $this->releaseMode === 'single') {
            $modalStudentQuery = Siswa::where('status', 'aktif')->with('user', 'kelas');
            
            if ($this->release_kelas_id) {
                $modalStudentQuery->where('kelas_id', $this->release_kelas_id);
            }

            if (trim($this->studentSearch) !== '') {
                $modalStudentQuery->where(function ($q) {
                    $q->whereHas('user', function ($uq) {
                        $uq->where('nama', 'like', '%' . $this->studentSearch . '%');
                    })->orWhere('nis', 'like', '%' . $this->studentSearch . '%');
                });
                $searchedStudents = $modalStudentQuery->limit(8)->get();
            }
        }

        // Search for Multi-Select Lintas Kelas in Bulk Mode
        $bulkSearchedStudents = [];
        $selectedStudentsList = [];
        if ($this->showCreateModal && $this->releaseMode === 'bulk') {
            if ($this->bulkTarget === 'custom') {
                $bQuery = Siswa::where('status', 'aktif')->with('user', 'kelas');
                if ($this->bulkSearchKelasId) {
                    $bQuery->where('kelas_id', $this->bulkSearchKelasId);
                }
                if (trim($this->bulkSearchStudent) !== '') {
                    $bQuery->where(function ($q) {
                        $q->whereHas('user', function ($uq) {
                            $uq->where('nama', 'like', '%' . $this->bulkSearchStudent . '%');
                        })->orWhere('nis', 'like', '%' . $this->bulkSearchStudent . '%');
                    });
                }
                $bulkSearchedStudents = $bQuery->limit(10)->get();

                if (!empty($this->bulkSelectedSiswaIds)) {
                    $selectedStudentsList = Siswa::whereIn('id', $this->bulkSelectedSiswaIds)->with('user', 'kelas')->get();
                }
            }
        }

        // Total Target Count for Bulk Summary
        $bulkStudentCount = 0;
        if ($this->showCreateModal && $this->releaseMode === 'bulk') {
            if ($this->bulkTarget === 'custom') {
                $bulkStudentCount = count($this->bulkSelectedSiswaIds);
            } elseif ($this->bulkTarget === 'class') {
                $bulkStudentCount = $this->release_kelas_id ? Siswa::where('status', 'aktif')->where('kelas_id', $this->release_kelas_id)->count() : 0;
            } elseif ($this->bulkTarget === 'all') {
                $bulkStudentCount = Siswa::where('status', 'aktif')->count();
            }
        }

        return view('livewire.finance.manajemen-tagihan', [
            'students' => $students,
            'searchedStudents' => $searchedStudents,
            'bulkSearchedStudents' => $bulkSearchedStudents,
            'selectedStudentsList' => $selectedStudentsList,
            'bulkStudentCount' => $bulkStudentCount,
            'isFounder' => $this->isFounder(),
        ])->layout('components.layouts.app', ['title' => 'Manajemen Tagihan']);
    }
}
