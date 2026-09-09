<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use App\Models\GuruMapelKelas;
use App\Models\Siswa;
use App\Models\AbsensiSiswa as AbsensiSiswaModel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AbsensiSiswa extends Component
{
    public ?int $kelas_id = null;
    public string $tanggal = '';

    // List of student attendance records
    public array $attendance = [];

    // Option lists
    public array $classes = [];
    public bool $isReadOnly = false;

    protected $rules = [
        'kelas_id' => 'required|exists:kelas,id',
        'tanggal' => 'required|date',
        'attendance.*.status' => 'required|in:hadir,sakit,izin,alpa',
        'attendance.*.catatan' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $this->tanggal = date('Y-m-d');
        $guru = auth()->user()->guru ?? null;
        if ($guru && $guru->isGuruPendamping()) {
            $this->isReadOnly = true;
        }
        $this->loadFilters();
    }

    public function loadFilters()
    {
        $guru = auth()->user()->guru;
        if (!$guru) {
            return;
        }

        // Get classes assigned to this teacher (from Mapel, Wali Kelas, Guru Tahfizh, or Shadow Teacher assignments)
        $assignments = GuruMapelKelas::with('kelas')
            ->where('guru_id', $guru->id)
            ->get();

        $gmkClasses = $assignments->pluck('kelas')->filter();
        $waliClasses = \App\Models\Kelas::where('guru_umum_id', $guru->id)->get();
        $tahfidzClasses = \App\Models\Kelas::where('guru_tahfidz_id', $guru->id)->get();

        $shadowClasses = collect();
        if ($guru->isGuruPendamping()) {
            $this->isReadOnly = true;
            $shadowClasses = $guru->siswaDidampingi()->with('kelas')->get()->pluck('kelas')->filter();
        }

        $this->classes = $gmkClasses->merge($waliClasses)->merge($tahfidzClasses)->merge($shadowClasses)->unique('id')->values()->toArray();

        if (empty($this->kelas_id) && count($this->classes) > 0) {
            $this->kelas_id = $this->classes[0]['id'];
            $this->loadStudents();
        }
    }

    public function setPresetDate(string $preset)
    {
        if ($preset === 'today') {
            $this->tanggal = Carbon::today()->toDateString();
        } elseif ($preset === 'yesterday') {
            $this->tanggal = Carbon::yesterday()->toDateString();
        }
        $this->loadStudents();
    }

    public function loadStudents()
    {
        if (!$this->kelas_id) {
            $this->attendance = [];
            return;
        }

        $guru = auth()->user()->guru ?? null;
        $studentQuery = Siswa::where(function ($q) {
                $q->where('kelas_id', $this->kelas_id)
                  ->orWhere('kelas_tahfidz_id', $this->kelas_id);
            })
            ->where('status', 'aktif')
            ->with('user');

        if ($guru && $guru->isGuruPendamping()) {
            $studentQuery->where('shadow_teacher_id', $guru->id);
        }

        $students = $studentQuery->get();

        $this->attendance = [];

        $existingRecords = AbsensiSiswaModel::where('kelas_id', $this->kelas_id)
            ->where('tanggal', $this->tanggal)
            ->whereIn('siswa_id', $students->pluck('id'))
            ->get()
            ->keyBy('siswa_id');

        foreach ($students as $student) {
            $existing = $existingRecords->get($student->id);

            $status = 'hadir';
            $catatan = '';
            if ($existing) {
                $catatan = $existing->catatan ?? '';
                if ($existing->status === 'tidak_hadir') {
                    $status = 'alpa';
                } elseif ($existing->status === 'izin') {
                    if (str_contains(strtolower($catatan), 'sakit')) {
                        $status = 'sakit';
                    } else {
                        $status = 'izin';
                    }
                } elseif ($existing->status === 'hadir') {
                    $status = 'hadir';
                }
            }

            $this->attendance[] = [
                'siswa_id' => $student->id,
                'nama' => $student->user->nama ?? '-',
                'nis' => $student->nis,
                'status' => $status,
                'catatan' => $catatan,
            ];
        }
    }

    public function updatedKelasId() { $this->loadStudents(); }
    public function updatedTanggal() { $this->loadStudents(); }

    public function setStatus(int $index, string $status)
    {
        if ($this->isReadOnly) {
            return;
        }
        if (isset($this->attendance[$index])) {
            $this->attendance[$index]['status'] = $status;
        }
    }

    public function setStatusAll(string $status)
    {
        if ($this->isReadOnly) {
            return;
        }
        foreach ($this->attendance as $index => $att) {
            $this->attendance[$index]['status'] = $status;
        }
    }

    public function save()
    {
        $guru = auth()->user()->guru;
        if (!$guru) {
            session()->flash('error', 'Data profil guru tidak ditemukan.');
            return;
        }

        if ($this->isReadOnly || $guru->isGuruPendamping()) {
            session()->flash('error', 'Akses dibatasi. Pengisian presensi harian siswa hanya dapat dilakukan oleh Guru Umum. Guru Pendamping hanya memiliki hak akses melihat.');
            return;
        }

        $this->validate();

        DB::transaction(function () use ($guru) {
            foreach ($this->attendance as $att) {
                $dbStatus = 'hadir';
                $catatan = $att['catatan'];
                
                if ($att['status'] === 'alpa') {
                    $dbStatus = 'tidak_hadir';
                } elseif ($att['status'] === 'izin') {
                    $dbStatus = 'izin';
                } elseif ($att['status'] === 'sakit') {
                    $dbStatus = 'izin';
                    $catatan = trim(($catatan ? $catatan . ' - ' : '') . 'Sakit');
                }

                AbsensiSiswaModel::updateOrCreate([
                    'siswa_id' => $att['siswa_id'],
                    'kelas_id' => $this->kelas_id,
                    'tanggal' => $this->tanggal,
                ], [
                    'guru_id' => $guru->id,
                    'status' => $dbStatus,
                    'catatan' => $catatan ?: null,
                ]);
            }
        });

        $msg = 'Kehadiran siswa berhasil disimpan.';
        session()->flash('message', $msg);
        $this->dispatch('show-alert', [
            'title' => 'Presensi Disimpan',
            'message' => $msg,
            'type' => 'create',
        ]);
        $this->loadStudents();
    }

    public function render()
    {
        $summary = [
            'total' => count($this->attendance),
            'hadir' => count(array_filter($this->attendance, fn($a) => ($a['status'] ?? '') === 'hadir')),
            'sakit' => count(array_filter($this->attendance, fn($a) => ($a['status'] ?? '') === 'sakit')),
            'izin' => count(array_filter($this->attendance, fn($a) => ($a['status'] ?? '') === 'izin')),
            'alpa' => count(array_filter($this->attendance, fn($a) => ($a['status'] ?? '') === 'alpa')),
        ];

        return view('livewire.guru.absensi-siswa', [
            'summary' => $summary,
        ])->layout('components.layouts.app', ['title' => 'Absensi Siswa']);
    }
}
