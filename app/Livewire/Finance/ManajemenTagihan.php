<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Models\Tagihan;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\JenisTagihan;
use App\Models\TahunAjaran;
use App\Traits\WithDateFilter;
use App\Traits\WithCurrencySanitizer;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class ManajemenTagihan extends Component
{
    use WithPagination, WithDateFilter, WithCurrencySanitizer;

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
    public string $periodeTipe = 'single'; // 'single' | 'full_year_jan_des' | 'full_year_juli_juni' | 'custom_range'
    public string $bulan = 'Juli';
    public string $bulan_mulai = 'Juli';
    public string $bulan_selesai = 'Desember';
    public float $nominal = 0.00;
    public string $jatuh_tempo = '';

    // Quick Detail Modal properties
    public bool $showQuickDetailModal = false;
    public ?int $quickDetailSiswaId = null;
    public ?Siswa $quickDetailSiswa = null;

    // Kategori Tagihan (Jenis Tagihan) Management properties
    public bool $showKategoriModal = false;
    public ?int $editingKategoriId = null;
    public string $kategori_nama = '';
    public string $kategori_tipe = 'rutin'; // 'rutin' | 'one_time' | 'tahunan' | 'semester'
    public float $kategori_nominal = 0.00;
    public bool $kategori_is_blocking = true;
    public string $searchKategori = '';

    // Edit Tagihan Form properties
    public ?int $editingTagihanId = null;
    public ?int $edit_jenis_tagihan_id = null;
    public string $edit_bulan = 'Juli';
    public float $edit_nominal = 0.00;
    public string $edit_jatuh_tempo = '';
    public float $edit_total_dibayar = 0.00;
    public string $edit_siswa_nama = '';
    public string $edit_alasan = '';
    public string $delete_alasan = '';

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

    public array $standardMonths = [
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'
    ];

    public function mount()
    {
        $this->classes = Kelas::orderBy('nama_kelas')->get()->toArray();
        $this->refreshJenisTagihans();
        $this->jatuh_tempo = date('Y-m-10');
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
                if (in_array($this->filterStatus, ['belum_bayar', 'sebagian'])) {
                    $q->where('status', $this->filterStatus)->jatuhTempo();
                } else {
                    $q->where('status', $this->filterStatus);
                }
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
        if (auth()->user()->role?->nama === 'super_admin_2') {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $this->resetValidation();
        $this->releaseMode = $siswaId ? 'single' : 'bulk';
        $this->bulkTarget = 'custom';
        $this->periodeTipe = 'single';
        $this->bulan_mulai = 'Juli';
        $this->bulan_selesai = 'Desember';
        $this->release_kelas_id = null;
        $this->studentSearch = '';
        $this->single_siswa_id = null;
        $this->selectedStudentName = '';
        $this->selectedStudentNis = '';
        $this->selectedStudentKelas = '';
        $this->bulkSelectedSiswaIds = [];
        $this->bulkSearchStudent = '';
        $this->bulkSearchKelasId = null;
        $this->jatuh_tempo = date('Y-m-10');

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

    public function openQuickDetail(int $siswaId)
    {
        $this->quickDetailSiswaId = $siswaId;
        $this->quickDetailSiswa = Siswa::with(['user', 'kelas', 'tagihans' => function ($q) {
            $q->with(['jenisTagihan', 'tahunAjaran', 'pembayarans'])->latest();
        }])->find($siswaId);
        $this->showQuickDetailModal = true;
    }

    public function closeQuickDetailModal()
    {
        $this->showQuickDetailModal = false;
        $this->quickDetailSiswaId = null;
        $this->quickDetailSiswa = null;
    }

    // =========================================================================
    // KATEGORI TAGIHAN (JENIS TAGIHAN) CRUD METHODS
    // =========================================================================

    public function openKategoriModal(?int $id = null)
    {
        if (auth()->user()->role?->nama === 'super_admin_2') {
            $this->dispatch('show-alert', [
                'title' => 'Akses Ditolak',
                'message' => 'Super Admin 2 hanya memiliki hak akses Lihat Saja.',
                'type' => 'danger',
            ]);
            return;
        }

        $this->resetValidation();
        if ($id) {
            $this->editKategori($id);
        } else {
            $this->resetKategoriForm();
        }
        $this->showKategoriModal = true;
    }

    public function closeKategoriModal()
    {
        $this->showKategoriModal = false;
        $this->resetKategoriForm();
        $this->resetValidation();
    }

    public function resetKategoriForm()
    {
        $this->editingKategoriId = null;
        $this->kategori_nama = '';
        $this->kategori_tipe = 'rutin';
        $this->kategori_nominal = 0.00;
        $this->kategori_is_blocking = true;
    }

    public function editKategori(int $id)
    {
        $jt = JenisTagihan::findOrFail($id);
        $this->editingKategoriId = $jt->id;
        $this->kategori_nama = $jt->nama;
        $this->kategori_tipe = $jt->kategori;
        $this->kategori_nominal = floatval($jt->default_nominal ?? 0.00);
        $this->kategori_is_blocking = (bool) $jt->is_blocking;
    }

    public function saveKategori()
    {
        if (auth()->user()->role?->nama === 'super_admin_2') {
            $this->dispatch('show-alert', [
                'title' => 'Akses Ditolak',
                'message' => 'Super Admin 2 hanya memiliki hak akses Lihat Saja.',
                'type' => 'danger',
            ]);
            return;
        }

        $this->sanitizeCurrencies(['kategori_nominal']);

        $this->validate([
            'kategori_nama' => 'required|string|max:100',
            'kategori_tipe' => 'required|in:rutin,one_time,tahunan,semester,per_6_bulan',
            'kategori_nominal' => 'required|numeric|min:0',
            'kategori_is_blocking' => 'boolean',
        ], [
            'kategori_nama.required' => 'Nama kategori tagihan wajib diisi.',
            'kategori_tipe.required' => 'Tipe frekuensi pembayaran wajib dipilih.',
            'kategori_tipe.in' => 'Tipe frekuensi pembayaran tidak valid.',
            'kategori_nominal.required' => 'Nominal standar wajib diisi.',
            'kategori_nominal.numeric' => 'Nominal standar harus berupa angka.',
            'kategori_nominal.min' => 'Nominal standar minimal Rp 0.',
        ]);

        try {
            $result = app(\App\Actions\Finance\ManageKategoriTagihanAction::class)->save([
                'nama' => $this->kategori_nama,
                'tipe' => $this->kategori_tipe,
                'nominal' => $this->kategori_nominal,
                'is_blocking' => $this->kategori_is_blocking,
            ], $this->editingKategoriId);

            if (!$result['isEdit'] && $this->showCreateModal) {
                $this->jenis_tagihan_id = $result['jt']->id;
                $this->nominal = floatval($result['jt']->default_nominal ?? 0.00);
            }

            $this->refreshJenisTagihans();
            $this->resetKategoriForm();

            $this->dispatch('show-alert', [
                'title' => $result['title'],
                'message' => $result['message'],
                'type' => $result['type'],
            ]);
        } catch (\Throwable $e) {
            $this->dispatch('show-alert', [
                'title' => 'Gagal Menyimpan Kategori',
                'message' => $e->getMessage(),
                'type' => 'danger',
            ]);
        }
    }

    public function deleteKategori(int $id)
    {
        if (auth()->user()->role?->nama === 'super_admin_2') {
            $this->dispatch('show-alert', [
                'title' => 'Akses Ditolak',
                'message' => 'Super Admin 2 hanya memiliki hak akses Lihat Saja.',
                'type' => 'danger',
            ]);
            return;
        }

        try {
            $result = app(\App\Actions\Finance\ManageKategoriTagihanAction::class)->delete($id);

            if ($result['success']) {
                $this->refreshJenisTagihans();
                if ($this->editingKategoriId === $id) {
                    $this->resetKategoriForm();
                }
            }

            $this->dispatch('show-alert', [
                'title' => $result['title'],
                'message' => $result['message'],
                'type' => $result['type'],
            ]);
        } catch (\Throwable $e) {
            $this->dispatch('show-alert', [
                'title' => 'Gagal Menghapus Kategori',
                'message' => $e->getMessage(),
                'type' => 'danger',
            ]);
        }
    }

    public function refreshJenisTagihans()
    {
        $this->jenisTagihans = JenisTagihan::where('nama', 'not like', '%Infaq%')
            ->where('nama', 'not like', '%Sedekah%')
            ->where('nama', 'not like', '%Donasi%')
            ->orderBy('nama')
            ->get()
            ->toArray();
    }

    public function getKategoriListProperty()
    {
        $query = JenisTagihan::where('nama', 'not like', '%Infaq%')
            ->where('nama', 'not like', '%Sedekah%')
            ->where('nama', 'not like', '%Donasi%');

        if (!empty(trim($this->searchKategori))) {
            $query->where('nama', 'like', '%' . trim($this->searchKategori) . '%');
        }

        return $query->withCount('tagihans')->orderBy('nama')->get();
    }

    public function setPresetRange(string $start, string $end)
    {
        $this->bulan_mulai = $start;
        $this->bulan_selesai = $end;
    }

    public function getMonthsBetween(string $start, string $end): array
    {
        $form = new \App\Livewire\Forms\Finance\ReleaseTagihanForm($this, 'releaseForm');
        return $form->getMonthsBetween($start, $end);
    }

    public function getTargetMonths(): array
    {
        $form = new \App\Livewire\Forms\Finance\ReleaseTagihanForm($this, 'releaseForm');
        $form->periodeTipe = $this->periodeTipe;
        $form->bulan = $this->bulan;
        $form->bulan_mulai = $this->bulan_mulai;
        $form->bulan_selesai = $this->bulan_selesai;
        return $form->getTargetMonths();
    }

    protected function calculateDueDateForMonth(string $monthName, ?string $baseDueDate = null, ?string $tahunAjaranNama = null): string
    {
        $form = new \App\Livewire\Forms\Finance\ReleaseTagihanForm($this, 'releaseForm');
        $form->periodeTipe = $this->periodeTipe;
        return $form->calculateDueDateForMonth($monthName, $baseDueDate, $tahunAjaranNama);
    }

    public function createSingleTagihan()
    {
        if (auth()->user()->role?->nama === 'super_admin_2') {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $this->sanitizeCurrencies(['nominal']);

        $rules = [
            'single_siswa_id' => 'required|exists:siswa,id',
            'jenis_tagihan_id' => 'required|exists:jenis_tagihan,id',
            'nominal' => 'required|numeric|min:0',
        ];

        if ($this->periodeTipe === 'single') {
            $rules['bulan'] = 'required|string|max:50';
        } elseif ($this->periodeTipe === 'custom_range') {
            $rules['bulan_mulai'] = 'required|string|in:' . implode(',', $this->standardMonths);
            $rules['bulan_selesai'] = 'required|string|in:' . implode(',', $this->standardMonths);
        }

        $this->validate($rules, [
            'single_siswa_id.required' => 'Pilih siswa penerima tagihan terlebih dahulu.',
        ]);

        $activeTA = TahunAjaran::where('status_aktif', true)->first();
        if (!$activeTA) {
            session()->flash('error', 'Tidak ada tahun ajaran aktif.');
            $this->dispatch('show-alert', ['title' => 'Peringatan', 'message' => 'Tidak ada tahun ajaran aktif.', 'type' => 'warning']);
            return;
        }

        $siswa = Siswa::with('user')->findOrFail($this->single_siswa_id);
        $targetMonths = $this->getTargetMonths();
        $createdCount = 0;
        $skippedCount = 0;

        DB::transaction(function () use ($siswa, $activeTA, $targetMonths, &$createdCount, &$skippedCount) {
            $status = ($this->nominal <= 0) ? 'lunas' : 'belum_bayar';

            foreach ($targetMonths as $m) {
                $exists = Tagihan::where('siswa_id', $siswa->id)
                    ->where('jenis_tagihan_id', $this->jenis_tagihan_id)
                    ->where('tahun_ajaran_id', $activeTA->id)
                    ->where('bulan', $m)
                    ->exists();

                if (!$exists) {
                    $monthDueDate = $this->calculateDueDateForMonth($m, $this->jatuh_tempo, $activeTA->nama);
                    Tagihan::create([
                        'siswa_id' => $siswa->id,
                        'jenis_tagihan_id' => $this->jenis_tagihan_id,
                        'tahun_ajaran_id' => $activeTA->id,
                        'bulan' => $m,
                        'nominal' => $this->nominal,
                        'total_dibayar' => 0.00,
                        'status' => $status,
                        'jatuh_tempo' => $monthDueDate,
                    ]);
                    $createdCount++;
                } else {
                    $skippedCount++;
                }
            }
        });

        if ($createdCount === 0 && $skippedCount > 0) {
            $msg = "Tidak ada tagihan baru dibuat. Siswa ini sudah memiliki tagihan untuk seluruh periode yang dipilih.";
            session()->flash('warning', $msg);
            $this->dispatch('show-alert', [
                'title' => 'Tagihan Sudah Ada',
                'message' => $msg,
                'type' => 'warning',
            ]);
            return;
        }

        $siswaName = $siswa->user->nama ?? 'Siswa';
        if (count($targetMonths) > 1) {
            $rangeLabel = ($this->periodeTipe === 'custom_range') 
                ? "{$this->bulan_mulai} - {$this->bulan_selesai}" 
                : ($this->periodeTipe === 'full_year_jan_des' ? 'Januari - Desember' : 'Juli - Juni');
            $msg = "Berhasil menerbitkan {$createdCount} tagihan ({$rangeLabel}) untuk siswa {$siswaName}" . ($skippedCount > 0 ? " ({$skippedCount} bulan dilewati karena sudah ada)." : ".");
        } else {
            $singleMonth = $targetMonths[0] ?? $this->bulan;
            $msg = "Berhasil merilis tagihan periode {$singleMonth} untuk siswa {$siswaName}" . ($this->nominal <= 0 ? " (Nominal Rp 0 - Otomatis Lunas)." : ".");
        }

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
        
        if ($this->showQuickDetailModal && $this->quickDetailSiswaId) {
            $this->openQuickDetail($this->quickDetailSiswaId);
        }
        
        $this->resetPage();
    }

    public function createBulkTagihan()
    {
        if (auth()->user()->role?->nama === 'super_admin_2') {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $this->sanitizeCurrencies(['nominal']);

        $rules = [
            'jenis_tagihan_id' => 'required|exists:jenis_tagihan,id',
            'nominal' => 'required|numeric|min:0',
        ];

        if ($this->periodeTipe === 'single') {
            $rules['bulan'] = 'required|string|max:50';
        } elseif ($this->periodeTipe === 'custom_range') {
            $rules['bulan_mulai'] = 'required|string|in:' . implode(',', $this->standardMonths);
            $rules['bulan_selesai'] = 'required|string|in:' . implode(',', $this->standardMonths);
        }

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

        $targetMonths = $this->getTargetMonths();
        $createdCount = 0;
        $skippedCount = 0;

        DB::transaction(function () use ($targetStudents, $activeTA, $targetMonths, &$createdCount, &$skippedCount) {
            $status = ($this->nominal <= 0) ? 'lunas' : 'belum_bayar';

            foreach ($targetStudents as $siswa) {
                foreach ($targetMonths as $m) {
                    $exists = Tagihan::where('siswa_id', $siswa->id)
                        ->where('jenis_tagihan_id', $this->jenis_tagihan_id)
                        ->where('tahun_ajaran_id', $activeTA->id)
                        ->where('bulan', $m)
                        ->exists();

                    if (!$exists) {
                        $monthDueDate = $this->calculateDueDateForMonth($m, $this->jatuh_tempo, $activeTA->nama);
                        Tagihan::create([
                            'siswa_id' => $siswa->id,
                            'jenis_tagihan_id' => $this->jenis_tagihan_id,
                            'tahun_ajaran_id' => $activeTA->id,
                            'bulan' => $m,
                            'nominal' => $this->nominal,
                            'total_dibayar' => 0.00,
                            'status' => $status,
                            'jatuh_tempo' => $monthDueDate,
                        ]);
                        $createdCount++;
                    } else {
                        $skippedCount++;
                    }
                }
            }
        });

        if ($createdCount === 0 && $skippedCount > 0) {
            $msg = "Tidak ada tagihan baru yang diterbitkan. {$skippedCount} siswa dilewati karena sudah memiliki tagihan ini.";
            session()->flash('warning', $msg);
            $this->dispatch('show-alert', [
                'title' => 'Tagihan Sudah Ada',
                'message' => $msg,
                'type' => 'warning',
            ]);
        } elseif ($createdCount > 0 && $skippedCount > 0) {
            $msg = "Berhasil merilis {$createdCount} data tagihan ({$skippedCount} tagihan dilewati karena sudah ada).";
            session()->flash('message', $msg);
            $this->dispatch('show-alert', [
                'title' => 'Rilis Selesai',
                'message' => $msg,
                'type' => 'create',
            ]);
        } else {
            $msg = "Berhasil merilis {$createdCount} data tagihan untuk " . count($targetStudents) . " siswa.";
            session()->flash('message', $msg);
            $this->dispatch('show-alert', [
                'title' => 'Rilis Selesai',
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

        if ($this->showQuickDetailModal && $this->quickDetailSiswaId) {
            $this->openQuickDetail($this->quickDetailSiswaId);
        }
    }

    public function openEditModal(int $tagihanId)
    {
        $tagihan = Tagihan::with(['siswa.user', 'jenisTagihan'])->findOrFail($tagihanId);
        
        $this->resetValidation();
        $this->editingTagihanId = $tagihan->id;
        $this->resetValidation();
        if (auth()->user()->role?->nama === 'super_admin_2') {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $t = Tagihan::with(['siswa.user', 'jenisTagihan'])->findOrFail($tagihanId);
        
        $this->editingTagihanId = $t->id;
        $this->edit_jenis_tagihan_id = $t->jenis_tagihan_id;
        $this->edit_bulan = $t->bulan;
        $this->edit_nominal = floatval($t->nominal);
        $this->edit_jatuh_tempo = $t->jatuh_tempo ? date('Y-m-d', strtotime($t->jatuh_tempo)) : '';
        $this->edit_total_dibayar = floatval($t->total_dibayar);
        $this->edit_siswa_nama = ($t->siswa->user->nama ?? 'Siswa') . ' (' . ($t->jenisTagihan->nama ?? 'Tagihan') . ' - ' . $t->bulan . ')';
        $this->edit_alasan = '';
        
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingTagihanId = null;
        $this->edit_alasan = '';
        $this->resetValidation();
    }

    public function saveEditTagihan()
    {
        if (auth()->user()->role?->nama === 'super_admin_2') {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $tagihan = Tagihan::with(['siswa.user', 'jenisTagihan'])->findOrFail($this->editingTagihanId);

        $this->sanitizeCurrencies(['edit_nominal']);

        $rules = [
            'editingTagihanId' => 'required|exists:tagihan,id',
            'edit_jenis_tagihan_id' => 'required|exists:jenis_tagihan,id',
            'edit_bulan' => 'required|string|max:50',
            'edit_nominal' => 'required|numeric|min:0',
            'edit_jatuh_tempo' => 'required|date',
        ];

        $userRole = auth()->user()->role->nama ?? '';
        if ($userRole === 'finance') {
            $rules['edit_alasan'] = 'required|string|min:5|max:500';
        }

        $this->validate($rules, [
            'edit_alasan.required' => 'Alasan perubahan wajib diisi untuk persetujuan Super Admin atau Super Admin 2.',
        ]);

        if ($this->edit_nominal < $tagihan->total_dibayar) {
            $this->addError('edit_nominal', 'Nominal tagihan baru tidak boleh lebih kecil dari jumlah yang sudah dibayarkan (Rp ' . number_format($tagihan->total_dibayar, 0, ',', '.') . ').');
            return;
        }

        // IF USER IS FINANCE -> CREATE APPROVAL REQUEST
        if ($userRole === 'finance') {
            \App\Services\FinancialApprovalService::createRequest(
                auth()->user(),
                'edit',
                'tagihan',
                $tagihan,
                [
                    'jenis_tagihan_id' => $this->edit_jenis_tagihan_id,
                    'bulan' => $this->edit_bulan,
                    'nominal' => $this->edit_nominal,
                    'jatuh_tempo' => $this->edit_jatuh_tempo,
                ],
                $this->edit_alasan,
                "Edit Tagihan: " . ($tagihan->siswa->user->nama ?? 'Siswa') . " - {$tagihan->bulan} (Rp " . number_format($tagihan->nominal, 0, ',', '.') . " -> Rp " . number_format($this->edit_nominal, 0, ',', '.') . ")"
            );

            $msg = 'Permohonan edit tagihan telah diajukan ke Super Admin / Super Admin 2 untuk disetujui.';
            session()->flash('message', $msg);
            $this->dispatch('show-alert', [
                'title' => 'Menunggu Approval',
                'message' => $msg,
                'type' => 'info',
            ]);

            $this->closeEditModal();
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

    public function deleteTagihan(int $id, ?string $alasan = null)
    {
        if (auth()->user()->role?->nama === 'super_admin_2') {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $tagihan = Tagihan::with(['siswa.user', 'jenisTagihan'])->findOrFail($id);
        $userRole = auth()->user()->role->nama ?? '';

        // If finance, route to approval request
        if ($userRole === 'finance') {
            $reason = $alasan ?: ($this->delete_alasan ?: 'Penghapusan tagihan diajukan oleh staf keuangan');
            \App\Services\FinancialApprovalService::createRequest(
                auth()->user(),
                'hapus',
                'tagihan',
                $tagihan,
                null,
                $reason,
                "Hapus Tagihan: " . ($tagihan->siswa->user->nama ?? 'Siswa') . " - {$tagihan->bulan} (Rp " . number_format($tagihan->nominal, 0, ',', '.') . ")"
            );

            $msg = 'Permohonan penghapusan tagihan telah diajukan ke Super Admin / Super Admin 2 untuk disetujui.';
            session()->flash('message', $msg);
            $this->dispatch('show-alert', [
                'title' => 'Menunggu Approval',
                'message' => $msg,
                'type' => 'info',
            ]);

            return;
        }

        if (!$this->isFounder()) {
            session()->flash('error', 'Akses Ditolak: Hanya Founder / Super Admin yang berhak menghapus data tagihan.');
            $this->dispatch('show-alert', [
                'title' => 'Akses Ditolak',
                'message' => 'Hanya Founder / Super Admin yang berhak menghapus data tagihan.',
                'type' => 'danger',
            ]);
            return;
        }

        if ($tagihan->total_dibayar > 0) {
            session()->flash('error', 'Tagihan ini sudah pernah dibayar sebagian/lunas, tidak dapat dihapus.');
            $this->dispatch('show-alert', [
                'title' => 'Peringatan',
                'message' => 'Tagihan ini sudah pernah dibayar sebagian/lunas, tidak dapat dihapus.',
                'type' => 'warning',
            ]);
            return;
        }

        app(\App\Actions\Finance\DeleteTagihanAction::class)->execute($tagihan);
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
                if (in_array($this->filterStatus, ['belum_bayar', 'sebagian'])) {
                    $q->where('status', $this->filterStatus)->jatuhTempo();
                } else {
                    $q->where('status', $this->filterStatus);
                }
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
                if (in_array($this->filterStatus, ['belum_bayar', 'sebagian'])) {
                    $q->where('status', $this->filterStatus)->jatuhTempo();
                } else {
                    $q->where('status', $this->filterStatus);
                }
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
