<?php

namespace App\Livewire\SuperAdmin\TataKelola;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\JadwalPelajaran;
use App\Models\GuruMapelKelas;
use App\Services\JadwalService;

class ManajemenJadwal extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterHari = '';
    public ?int $filterKelasId = null;
    public int $perPage = 15;

    // View mode & selected class for timetable matrix
    public string $viewMode = 'grid'; // 'grid' or 'table'
    public ?int $selectedKelasId = null;

    // Form fields
    public ?int $jadwalId = null;
    public ?int $guru_mapel_kelas_id = null;
    public string $hari = 'senin';
    public string $jam_mulai = '07:30';
    public string $jam_selesai = '09:00';

    public bool $isFormOpen = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterHari' => ['except' => ''],
        'filterKelasId' => ['except' => null],
        'viewMode' => ['except' => 'grid'],
        'selectedKelasId' => ['except' => null],
    ];

    public function selectKelas(int $kelasId)
    {
        $this->selectedKelasId = $kelasId;
        $this->filterKelasId = $kelasId;
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterHari()
    {
        $this->resetPage();
    }

    public function updatingFilterKelasId()
    {
        if ($this->filterKelasId) {
            $this->selectedKelasId = $this->filterKelasId;
        }
        $this->resetPage();
    }

    public function openCreate()
    {
        $this->resetForm();
        if ($this->selectedKelasId) {
            $firstAsg = GuruMapelKelas::where('kelas_id', $this->selectedKelasId)->first();
            if ($firstAsg) {
                $this->guru_mapel_kelas_id = $firstAsg->id;
            }
        }
        $this->isFormOpen = true;
    }

    public function openCreateForDay(string $hari, ?int $kelasId = null)
    {
        $this->resetForm();
        $this->hari = strtolower($hari);
        if ($kelasId) {
            $this->selectedKelasId = $kelasId;
            $this->filterKelasId = $kelasId;
            $firstAsg = GuruMapelKelas::where('kelas_id', $kelasId)->first();
            if ($firstAsg) {
                $this->guru_mapel_kelas_id = $firstAsg->id;
            }
        }
        $this->isFormOpen = true;
    }

    public function openEdit(int $id)
    {
        $this->resetForm();
        $jadwal = JadwalPelajaran::with('guruMapelKelas')->findOrFail($id);
        $this->jadwalId = $jadwal->id;
        $this->guru_mapel_kelas_id = $jadwal->guru_mapel_kelas_id;
        $this->hari = $jadwal->hari;
        $this->jam_mulai = date('H:i', strtotime($jadwal->jam_mulai));
        $this->jam_selesai = date('H:i', strtotime($jadwal->jam_selesai));

        if ($jadwal->guruMapelKelas) {
            $this->selectedKelasId = $jadwal->guruMapelKelas->kelas_id;
        }

        $this->isFormOpen = true;
    }

    public function save(JadwalService $jadwalService)
    {
        $this->validate([
            'guru_mapel_kelas_id' => 'required|exists:guru_mapel_kelas,id',
            'hari' => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ], [
            'guru_mapel_kelas_id.required' => 'Mata pelajaran kelas wajib dipilih.',
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
        ]);

        // Run anti-clash check using JadwalService
        $conflict = $jadwalService->checkConflict(
            $this->guru_mapel_kelas_id,
            $this->hari,
            $this->jam_mulai,
            $this->jam_selesai,
            $this->jadwalId
        );

        if ($conflict) {
            $this->addError('guru_mapel_kelas_id', $conflict['message']);
            return;
        }

        JadwalPelajaran::updateOrCreate(
            ['id' => $this->jadwalId],
            [
                'guru_mapel_kelas_id' => $this->guru_mapel_kelas_id,
                'hari' => $this->hari,
                'jam_mulai' => $this->jam_mulai,
                'jam_selesai' => $this->jam_selesai,
            ]
        );

        session()->flash('message', 'Jadwal pelajaran berhasil disimpan.');
        $this->isFormOpen = false;
        $this->resetForm();
    }

    public function delete(int $id)
    {
        JadwalPelajaran::findOrFail($id)->delete();
        session()->flash('message', 'Jadwal pelajaran berhasil dihapus.');
    }

    private function resetForm()
    {
        $this->jadwalId = null;
        $this->guru_mapel_kelas_id = null;
        $this->hari = 'senin';
        $this->jam_mulai = '07:30';
        $this->jam_selesai = '09:00';
    }

    public function render()
    {
        $kelases = \App\Models\Kelas::orderBy('nama_kelas')->get();

        if (is_null($this->selectedKelasId) && $kelases->count() > 0) {
            $this->selectedKelasId = $kelases->first()->id;
        }

        $activeKelas = $kelases->firstWhere('id', $this->selectedKelasId);

        // Fetch Weekly Timetable Grid for selected class
        $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
        $weeklyGrid = [];

        if ($this->selectedKelasId) {
            $classSchedules = JadwalPelajaran::with([
                'guruMapelKelas.kelas',
                'guruMapelKelas.mapel',
                'guruMapelKelas.guru.user'
            ])
            ->whereHas('guruMapelKelas', function ($q) {
                $q->where('kelas_id', $this->selectedKelasId);
            })
            ->orderBy('jam_mulai')
            ->get();

            foreach ($days as $day) {
                $weeklyGrid[$day] = $classSchedules->filter(fn($item) => strtolower($item->hari) === $day)->values();
            }
        }

        // Fetch list table schedules
        $jadwals = JadwalPelajaran::with([
            'guruMapelKelas.kelas',
            'guruMapelKelas.mapel',
            'guruMapelKelas.guru.user',
            'guruMapelKelas.semester.tahunAjaran'
        ])
        ->when($this->filterKelasId, function ($query) {
            $query->whereHas('guruMapelKelas', function ($q) {
                $q->where('kelas_id', $this->filterKelasId);
            });
        })
        ->when($this->filterHari, function ($query) {
            $query->where('hari', $this->filterHari);
        })
        ->where(function ($query) {
            $query->whereHas('guruMapelKelas.kelas', function ($q) {
                $q->where('nama_kelas', 'like', '%' . $this->search . '%');
            })
            ->orWhereHas('guruMapelKelas.mapel', function ($q) {
                $q->where('nama_mapel', 'like', '%' . $this->search . '%');
            })
            ->orWhereHas('guruMapelKelas.guru.user', function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%');
            });
        })
        ->orderByRaw("FIELD(hari, 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu')")
        ->orderBy('jam_mulai')
        ->paginate($this->perPage);

        // Fetch options for select: [Kelas] [Mapel] - [Guru] (Current active semester / year)
        $assignments = GuruMapelKelas::with(['kelas', 'mapel', 'guru.user', 'semester.tahunAjaran'])
            ->whereHas('semester.tahunAjaran', function ($q) {
                $q->where('status_aktif', true);
            })
            ->get();

        $assignmentsGrouped = $assignments->groupBy(fn($item) => $item->kelas->nama_kelas ?? 'Lainnya');

        // Existing schedules helper for form modal
        $formExistingSchedules = collect();
        if ($this->isFormOpen && $this->guru_mapel_kelas_id) {
            $asg = $assignments->firstWhere('id', $this->guru_mapel_kelas_id);
            if ($asg) {
                $formExistingSchedules = JadwalPelajaran::with(['guruMapelKelas.mapel', 'guruMapelKelas.guru.user'])
                    ->whereHas('guruMapelKelas', function ($q) use ($asg) {
                        $q->where('kelas_id', $asg->kelas_id);
                    })
                    ->where('hari', strtolower($this->hari))
                    ->when($this->jadwalId, fn($q) => $q->where('id', '!=', $this->jadwalId))
                    ->orderBy('jam_mulai')
                    ->get();
            }
        }

        return view('livewire.super-admin.tata-kelola.manajemen-jadwal', [
            'jadwals' => $jadwals,
            'assignments' => $assignments,
            'assignmentsGrouped' => $assignmentsGrouped,
            'kelases' => $kelases,
            'activeKelas' => $activeKelas,
            'days' => $days,
            'weeklyGrid' => $weeklyGrid,
            'formExistingSchedules' => $formExistingSchedules,
        ])->layout('components.layouts.app', ['title' => 'Jadwal Pelajaran']);
    }
}
