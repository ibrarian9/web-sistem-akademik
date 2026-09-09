<?php

namespace App\Livewire\Guru;

use App\Models\CatatanPendampingan;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Semester;
use App\Models\Siswa;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class CatatanPendampinganIndex extends Component
{
    use WithPagination;

    // Active View Tab: 'daftar', 'rekap', 'pratinjau'
    public string $tab = 'daftar';

    // Filters for Daftar Tab
    public string $search = '';
    public ?int $filterKelasId = null;
    public ?int $filterSiswaId = null;
    public string $filterAspek = '';
    public string $filterHasil = '';
    public string $filterPeriode = 'semua';
    public ?string $filterTanggalMulai = null;
    public ?string $filterTanggalSelesai = null;

    // Filters for Rekap & Pratinjau Tab
    public ?int $selectedSiswaId = null;
    public string $rekapPeriode = 'tengah_semester'; // tengah_semester, akhir_semester, semua

    // Form Modal state
    public bool $showFormModal = false;
    public ?int $editingId = null;

    // Form properties
    public string $form_tanggal = '';
    public ?int $form_siswa_id = null;
    public string $form_aspek = 'Komunikasi';
    public string $form_hasil_perkembangan = 'BSH'; // BB, MB, BSH, BSB
    public string $form_catatan = '';
    public string $form_rekomendasi = '';
    public string $form_periode = 'tengah_semester';

    // Delete Confirmation state
    public ?int $deletingId = null;

    protected $queryString = [
        'tab' => ['except' => 'daftar'],
        'search' => ['except' => ''],
        'filterPeriode' => ['except' => 'semua'],
        'filterAspek' => ['except' => ''],
        'filterHasil' => ['except' => ''],
        'selectedSiswaId' => ['except' => null],
        'rekapPeriode' => ['except' => 'tengah_semester'],
    ];

    protected function rules(): array
    {
        return [
            'form_tanggal' => 'required|date',
            'form_siswa_id' => 'required|exists:siswa,id',
            'form_aspek' => 'required|in:' . implode(',', CatatanPendampingan::ASPEK_LIST),
            'form_hasil_perkembangan' => 'required|in:BB,MB,BSH,BSB',
            'form_catatan' => 'required|string|min:5|max:2000',
            'form_rekomendasi' => 'nullable|string|max:1000',
            'form_periode' => 'required|in:tengah_semester,akhir_semester',
        ];
    }

    protected $messages = [
        'form_tanggal.required' => 'Tanggal observasi pengamatan wajib diisi.',
        'form_siswa_id.required' => 'Pilih peserta didik yang diobservasi.',
        'form_aspek.required' => 'Pilih aspek pengamatan perkembangan.',
        'form_aspek.in' => 'Aspek pengamatan tidak valid.',
        'form_hasil_perkembangan.required' => 'Tentukan capaian kualitatif (BB, MB, BSH, atau BSB).',
        'form_hasil_perkembangan.in' => 'Skala capaian hanya boleh BB, MB, BSH, atau BSB.',
        'form_catatan.required' => 'Deskripsi catatan pengamatan guru wajib diisi.',
        'form_catatan.min' => 'Deskripsi catatan minimal 5 karakter.',
        'form_periode.required' => 'Tentukan periode evaluasi.',
    ];

    public function mount()
    {
        $this->form_tanggal = date('Y-m-d');

        // Auto-select initial student if available
        $firstSpecialStudent = Siswa::whereNotNull('shadow_teacher_id')->first();
        if ($firstSpecialStudent) {
            $this->selectedSiswaId = $firstSpecialStudent->id;
        } else {
            $anyStudent = Siswa::first();
            $this->selectedSiswaId = $anyStudent?->id;
        }

        // If current user is shadow teacher, preselect one of their students
        $user = auth()->user();
        if ($user && $user->guru) {
            $myStudent = Siswa::where('shadow_teacher_id', $user->guru->id)->first();
            if ($myStudent) {
                $this->selectedSiswaId = $myStudent->id;
            }
        }
    }

    public function selectTab(string $tab)
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterKelasId()
    {
        $this->resetPage();
    }

    public function updatingFilterAspek()
    {
        $this->resetPage();
    }

    public function updatingFilterHasil()
    {
        $this->resetPage();
    }

    public function updatingFilterPeriode()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset([
            'search',
            'filterKelasId',
            'filterSiswaId',
            'filterAspek',
            'filterHasil',
            'filterPeriode',
            'filterTanggalMulai',
            'filterTanggalSelesai',
        ]);
        $this->filterPeriode = 'semua';
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->editingId = null;
        $this->form_tanggal = date('Y-m-d');
        $this->form_aspek = CatatanPendampingan::ASPEK_LIST[0];
        $this->form_hasil_perkembangan = 'BSH';
        $this->form_catatan = '';
        $this->form_rekomendasi = '';
        $this->form_periode = 'tengah_semester';

        // Pre-fill student if selected in rekap or filter
        if ($this->selectedSiswaId) {
            $this->form_siswa_id = $this->selectedSiswaId;
        } else {
            $first = Siswa::whereNotNull('shadow_teacher_id')->first() ?? Siswa::first();
            $this->form_siswa_id = $first?->id;
        }

        $this->showFormModal = true;
    }

    public function openEditModal(int $id)
    {
        $this->resetValidation();
        $record = CatatanPendampingan::findOrFail($id);

        $this->editingId = $record->id;
        $this->form_tanggal = $record->tanggal->format('Y-m-d');
        $this->form_siswa_id = $record->siswa_id;
        $this->form_aspek = $record->aspek;
        $this->form_hasil_perkembangan = $record->hasil_perkembangan;
        $this->form_catatan = $record->catatan;
        $this->form_rekomendasi = $record->rekomendasi ?? '';
        $this->form_periode = $record->periode;

        $this->showFormModal = true;
    }

    public function closeFormModal()
    {
        $this->showFormModal = false;
        $this->editingId = null;
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->saveRecord();
    }

    public function saveRecord()
    {
        $user = auth()->user();
        if ($user->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Akun Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $this->validate();

        if ($user->guru && $user->guru->isGuruPendamping()) {
            $isAssigned = Siswa::where('id', $this->form_siswa_id)
                ->where('shadow_teacher_id', $user->guru->id)
                ->exists();

            if (!$isAssigned) {
                $this->addError('form_siswa_id', 'Akses Ditolak: Anda hanya dapat mencatat perkembangan untuk siswa yang ditugaskan kepada Anda.');
                return;
            }
        }

        $activeSemester = Semester::where('status_aktif', true)->first();
        $guruId = $user->guru?->id;

        // If guru is not set (e.g. logged in as admin), use existing or shadow teacher of student
        if (!$guruId) {
            $targetSiswa = Siswa::find($this->form_siswa_id);
            $guruId = $targetSiswa?->shadow_teacher_id ?? Guru::first()?->id;
        }

        if ($this->editingId) {
            $catatan = CatatanPendampingan::findOrFail($this->editingId);
            $catatan->update([
                'siswa_id' => $this->form_siswa_id,
                'guru_id' => $guruId,
                'semester_id' => $activeSemester?->id ?? $catatan->semester_id,
                'tanggal' => $this->form_tanggal,
                'aspek' => $this->form_aspek,
                'hasil_perkembangan' => $this->form_hasil_perkembangan,
                'catatan' => $this->form_catatan,
                'rekomendasi' => $this->form_rekomendasi ?: null,
                'periode' => $this->form_periode,
            ]);

            AuditLogger::log('updated', 'Memperbarui catatan pendampingan siswa: ' . ($catatan->siswa->user->nama ?? 'Siswa'), $catatan, [
                'log_name' => 'pendampingan',
                'aspek' => $this->form_aspek,
                'capaian' => $this->form_hasil_perkembangan,
            ]);

            session()->flash('message', 'Catatan pengamatan pendampingan berhasil diperbarui.');
        } else {
            $catatan = CatatanPendampingan::create([
                'siswa_id' => $this->form_siswa_id,
                'guru_id' => $guruId,
                'semester_id' => $activeSemester?->id,
                'tanggal' => $this->form_tanggal,
                'aspek' => $this->form_aspek,
                'hasil_perkembangan' => $this->form_hasil_perkembangan,
                'catatan' => $this->form_catatan,
                'rekomendasi' => $this->form_rekomendasi ?: null,
                'periode' => $this->form_periode,
            ]);

            AuditLogger::log('created', 'Menambahkan catatan pendampingan baru siswa: ' . ($catatan->siswa->user->nama ?? 'Siswa'), $catatan, [
                'log_name' => 'pendampingan',
                'aspek' => $this->form_aspek,
                'capaian' => $this->form_hasil_perkembangan,
            ]);

            session()->flash('message', 'Catatan pengamatan pendampingan berhasil disimpan.');
        }

        $this->selectedSiswaId = $this->form_siswa_id;
        $this->closeFormModal();
    }

    public function confirmDelete(int $id)
    {
        $this->deletingId = $id;
    }

    public function cancelDelete()
    {
        $this->deletingId = null;
    }

    public function deleteRecord(int $id)
    {
        $user = auth()->user();
        if ($user->isSuperAdmin2()) {
            session()->flash('error', 'Akses Ditolak: Super Admin 2 hanya memiliki hak akses Lihat Saja.');
            return;
        }

        $catatan = CatatanPendampingan::findOrFail($id);
        $namaSiswa = $catatan->siswa->user->nama ?? 'Siswa';

        AuditLogger::log('deleted', 'Menghapus catatan pendampingan siswa: ' . $namaSiswa, $catatan, [
            'log_name' => 'pendampingan',
            'aspek' => $catatan->aspek,
        ]);

        $catatan->delete();

        session()->flash('message', 'Catatan pendampingan berhasil dihapus.');
        $this->deletingId = null;
    }

    public function selectStudentForRekap(int $siswaId)
    {
        $this->selectedSiswaId = $siswaId;
    }

    /**
     * Get computed rekap data for the selected student.
     */
    public function getRekapDataProperty(): array
    {
        if (!$this->selectedSiswaId) {
            return [];
        }

        $query = CatatanPendampingan::with(['guru.user'])
            ->where('siswa_id', $this->selectedSiswaId);

        if ($this->rekapPeriode !== 'semua') {
            $query->where('periode', $this->rekapPeriode);
        }

        $allRecords = $query->orderBy('tanggal', 'asc')->get();

        $aspekBreakdown = [];
        foreach (CatatanPendampingan::ASPEK_LIST as $aspek) {
            $records = $allRecords->where('aspek', $aspek);
            $latest = $records->last();

            $counts = [
                'BB' => $records->where('hasil_perkembangan', 'BB')->count(),
                'MB' => $records->where('hasil_perkembangan', 'MB')->count(),
                'BSH' => $records->where('hasil_perkembangan', 'BSH')->count(),
                'BSB' => $records->where('hasil_perkembangan', 'BSB')->count(),
            ];

            $aspekBreakdown[$aspek] = [
                'nama' => $aspek,
                'total_observasi' => $records->count(),
                'capaian_terakhir' => $latest ? $latest->hasil_perkembangan : null,
                'capaian_label' => $latest ? $latest->hasil_perkembangan_label : 'Belum Ada Observasi',
                'badge' => $latest ? $latest->hasil_perkembangan_badge : 'stone',
                'catatan_terakhir' => $latest ? $latest->catatan : null,
                'rekomendasi_terakhir' => $latest ? $latest->rekomendasi : null,
                'tanggal_terakhir' => $latest ? $latest->tanggal->translatedFormat('d M Y') : null,
                'guru_terakhir' => $latest?->guru?->user?->nama ?? 'Guru Pendamping',
                'distribusi' => $counts,
                'riwayat' => $records,
            ];
        }

        $totalObservasi = $allRecords->count();
        $totalBB = $allRecords->where('hasil_perkembangan', 'BB')->count();
        $totalMB = $allRecords->where('hasil_perkembangan', 'MB')->count();
        $totalBSH = $allRecords->where('hasil_perkembangan', 'BSH')->count();
        $totalBSB = $allRecords->where('hasil_perkembangan', 'BSB')->count();

        return [
            'total_observasi' => $totalObservasi,
            'total_bb' => $totalBB,
            'total_mb' => $totalMB,
            'total_bsh' => $totalBSH,
            'total_bsb' => $totalBSB,
            'aspek_breakdown' => $aspekBreakdown,
            'rekomendasi_list' => $allRecords->whereNotNull('rekomendasi')->pluck('rekomendasi')->filter()->unique()->values()->toArray(),
        ];
    }

    public function render()
    {
        $user = auth()->user();
        $isGuru = $user && $user->guru;
        $isGuruPendamping = $isGuru && $user->guru->isGuruPendamping();

        // 1. Query Catatan Pendampingan for Daftar Tab
        $catatanQuery = CatatanPendampingan::with(['siswa.user', 'siswa.kelas', 'siswa.kelasTahfidz', 'guru.user'])
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('catatan', 'like', '%' . $this->search . '%')
                        ->orWhere('rekomendasi', 'like', '%' . $this->search . '%')
                        ->orWhereHas('siswa.user', fn($sq) => $sq->where('nama', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('siswa', fn($sq) => $sq->where('nis', 'like', '%' . $this->search . '%')->orWhere('nisn', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->filterKelasId, function ($q) {
                $q->whereHas('siswa', fn($sq) => $sq->where('kelas_id', $this->filterKelasId));
            })
            ->when($this->filterSiswaId, fn($q) => $q->where('siswa_id', $this->filterSiswaId))
            ->when($this->filterAspek, fn($q) => $q->where('aspek', $this->filterAspek))
            ->when($this->filterHasil, fn($q) => $q->where('hasil_perkembangan', $this->filterHasil))
            ->when($this->filterPeriode && $this->filterPeriode !== 'semua', fn($q) => $q->where('periode', $this->filterPeriode))
            ->when($this->filterTanggalMulai, fn($q) => $q->whereDate('tanggal', '>=', $this->filterTanggalMulai))
            ->when($this->filterTanggalSelesai, fn($q) => $q->whereDate('tanggal', '<=', $this->filterTanggalSelesai));

        // If Guru Pendamping, prioritize or show their students
        if ($isGuruPendamping) {
            $catatanQuery->where(function ($q) use ($user) {
                $q->where('guru_id', $user->guru->id)
                  ->orWhereHas('siswa', fn($sq) => $sq->where('shadow_teacher_id', $user->guru->id));
            });
        }

        $catatans = $catatanQuery->latest('tanggal')->latest('id')->paginate(12);

        // 2. Query Students for selection (Strictly Scoped for Shadow Teacher)
        $siswaQuery = Siswa::with(['user', 'kelas', 'kelasTahfidz', 'shadowTeacher.user']);
        if ($isGuruPendamping) {
            $siswaQuery->where('shadow_teacher_id', $user->guru->id);
        }

        $allStudents = $siswaQuery->whereHas('user')->get()->sortBy('user.nama');

        // Selected student model for Rekap & Pratinjau
        $selectedSiswa = null;
        if ($this->selectedSiswaId) {
            $selectedSiswaQuery = Siswa::with(['user', 'kelas.guruUmum.user', 'kelasTahfidz.guruTahfidz.user', 'shadowTeacher.user']);
            if ($isGuruPendamping) {
                $selectedSiswaQuery->where('shadow_teacher_id', $user->guru->id);
            }
            $selectedSiswa = $selectedSiswaQuery->find($this->selectedSiswaId);
        }

        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $aspekList = CatatanPendampingan::ASPEK_LIST;
        $skalaList = CatatanPendampingan::SKALA_LIST;

        return view('livewire.guru.catatan-pendampingan-index', [
            'catatans' => $catatans,
            'allStudents' => $allStudents,
            'selectedSiswa' => $selectedSiswa,
            'kelasList' => $kelasList,
            'aspekList' => $aspekList,
            'skalaList' => $skalaList,
            'rekapData' => $this->rekapData,
            'isGuruPendamping' => $isGuruPendamping,
        ])->layout('components.layouts.app', [
            'title' => 'Catatan Pendampingan Siswa Berkebutuhan Khusus',
        ]);
    }
}
