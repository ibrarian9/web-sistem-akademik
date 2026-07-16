<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use App\Models\GuruMapelKelas;
use App\Models\Siswa;
use App\Models\AbsensiSiswa as AbsensiSiswaModel;
use Illuminate\Support\Facades\DB;

class AbsensiSiswa extends Component
{
    public ?int $kelas_id = null;
    public string $tanggal = '';

    // List of student attendance records
    public array $attendance = [];

    // Option lists
    public array $classes = [];

    protected $rules = [
        'kelas_id' => 'required|exists:kelas,id',
        'tanggal' => 'required|date',
        'attendance.*.status' => 'required|in:hadir,sakit,izin,alpa',
        'attendance.*.catatan' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $this->tanggal = date('Y-m-d');
        $this->loadFilters();
    }

    public function loadFilters()
    {
        $guru = auth()->user()->guru;
        if (!$guru) {
            return;
        }

        // Get classes assigned to this teacher
        $assignments = GuruMapelKelas::with('kelas')
            ->where('guru_id', $guru->id)
            ->whereHas('semester.tahunAjaran', function ($q) {
                $q->where('status_aktif', true);
            })
            ->get();

        $this->classes = $assignments->pluck('kelas')->unique('id')->toArray();
    }

    public function loadStudents()
    {
        if (!$this->kelas_id) {
            $this->attendance = [];
            return;
        }

        $guru = auth()->user()->guru;
        $students = Siswa::where('kelas_id', $this->kelas_id)
            ->where('status', 'aktif')
            ->with('user')
            ->get();

        $this->attendance = [];

        foreach ($students as $student) {
            // Find existing attendance
            $existing = AbsensiSiswaModel::where([
                'siswa_id' => $student->id,
                'kelas_id' => $this->kelas_id,
                'guru_id' => $guru->id,
                'tanggal' => $this->tanggal,
            ])->first();

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

    public function setStatusAll(string $status)
    {
        foreach ($this->attendance as $index => $att) {
            $this->attendance[$index]['status'] = $status;
        }
    }

    public function save()
    {
        $this->validate();

        $guru = auth()->user()->guru;

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
                    'guru_id' => $guru->id,
                    'tanggal' => $this->tanggal,
                ], [
                    'status' => $dbStatus,
                    'catatan' => $catatan ?: null,
                ]);
            }
        });

        session()->flash('message', 'Kehadiran siswa berhasil disimpan.');
        $this->loadStudents();
    }

    public function render()
    {
        return view('livewire.guru.absensi-siswa')
            ->layout('components.layouts.app', ['title' => 'Absensi Siswa']);
    }
}
