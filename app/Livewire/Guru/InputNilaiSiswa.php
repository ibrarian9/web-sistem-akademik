<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use App\Models\GuruMapelKelas;
use App\Models\Siswa;
use App\Models\KomponenNilai;
use App\Models\Nilai;
use Illuminate\Support\Facades\DB;

class InputNilaiSiswa extends Component
{
    public ?int $kelas_id = null;
    public ?int $mapel_id = null;
    public ?int $komponen_nilai_id = null;
    public string $tanggal = '';

    // List of student grades
    public array $grades = [];

    // Option lists
    public array $classes = [];
    public array $subjects = [];
    public array $components = [];

    protected $rules = [
        'kelas_id' => 'required|exists:kelas,id',
        'mapel_id' => 'required|exists:mata_pelajaran,id',
        'komponen_nilai_id' => 'required|exists:komponen_nilai,id',
        'tanggal' => 'required|date',
        'grades.*.nilai' => 'required|numeric|min:0|max:100',
        'grades.*.catatan' => 'nullable|string|max:255',
    ];

    protected $messages = [
        'grades.*.nilai.required' => 'Nilai wajib diisi.',
        'grades.*.nilai.numeric' => 'Nilai harus berupa angka.',
        'grades.*.nilai.min' => 'Nilai minimal 0.',
        'grades.*.nilai.max' => 'Nilai maksimal 100.',
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

        // Get assignments
        $assignments = GuruMapelKelas::with(['kelas', 'mapel', 'semester'])
            ->where('guru_id', $guru->id)
            ->whereHas('semester.tahunAjaran', function ($q) {
                $q->where('status_aktif', true);
            })
            ->get();

        // Extract classes & mapels
        $this->classes = $assignments->pluck('kelas')->unique('id')->toArray();
        $this->subjects = $assignments->pluck('mapel')->unique('id')->toArray();
        $this->components = KomponenNilai::orderBy('urutan')->get()->toArray();
    }

    public function loadStudents()
    {
        if (!$this->kelas_id || !$this->mapel_id || !$this->komponen_nilai_id) {
            $this->grades = [];
            return;
        }

        $guru = auth()->user()->guru;
        $activeSemester = DB::table('semester')
            ->join('tahun_ajaran', 'semester.tahun_ajaran_id', '=', 'tahun_ajaran.id')
            ->where('tahun_ajaran.status_aktif', true)
            ->where('semester.status_aktif', true)
            ->select('semester.id')
            ->first();

        if (!$activeSemester) {
            session()->flash('error', 'Tidak ada semester aktif saat ini.');
            return;
        }

        $students = Siswa::where('kelas_id', $this->kelas_id)
            ->where('status', 'aktif')
            ->with('user')
            ->get();

        $this->grades = [];

        foreach ($students as $student) {
            // Find existing grade
            $existing = Nilai::where([
                'siswa_id' => $student->id,
                'kelas_id' => $this->kelas_id,
                'mapel_id' => $this->mapel_id,
                'guru_id' => $guru->id,
                'semester_id' => $activeSemester->id,
                'komponen_nilai_id' => $this->komponen_nilai_id,
                'tanggal' => $this->tanggal,
            ])->first();

            $this->grades[] = [
                'siswa_id' => $student->id,
                'nama' => $student->user->nama ?? '-',
                'nis' => $student->nis,
                'nilai' => $existing ? floatval($existing->nilai) : '',
                'catatan' => $existing ? $existing->catatan : '',
            ];
        }
    }

    public float $selectedMapelKkm = 70.00;

    // Load students on filter change
    public function updatedKelasId() { $this->loadStudents(); }
    public function updatedMapelId() 
    { 
        if ($this->mapel_id) {
            $mapel = \App\Models\MataPelajaran::find($this->mapel_id);
            $this->selectedMapelKkm = floatval($mapel->kkm ?? 70.00);
        }
        $this->loadStudents(); 
    }
    public function updatedKomponenNilaiId() { $this->loadStudents(); }
    public function updatedTanggal() { $this->loadStudents(); }

    public function save()
    {
        $this->validate();

        $guru = auth()->user()->guru;
        $activeSemester = DB::table('semester')
            ->join('tahun_ajaran', 'semester.tahun_ajaran_id', '=', 'tahun_ajaran.id')
            ->where('tahun_ajaran.status_aktif', true)
            ->where('semester.status_aktif', true)
            ->select('semester.id')
            ->first();

        if (!$activeSemester) {
            session()->flash('error', 'Semester aktif tidak ditemukan.');
            return;
        }

        DB::transaction(function () use ($guru, $activeSemester) {
            foreach ($this->grades as $g) {
                Nilai::updateOrCreate([
                    'siswa_id' => $g['siswa_id'],
                    'kelas_id' => $this->kelas_id,
                    'mapel_id' => $this->mapel_id,
                    'guru_id' => $guru->id,
                    'semester_id' => $activeSemester->id,
                    'komponen_nilai_id' => $this->komponen_nilai_id,
                    'tanggal' => $this->tanggal,
                ], [
                    'nilai' => $g['nilai'],
                    'catatan' => $g['catatan'] ?: null,
                ]);
            }
        });

        session()->flash('message', 'Nilai siswa berhasil disimpan.');
        $this->loadStudents();
    }

    public function render()
    {
        return view('livewire.guru.input-nilai-siswa')
            ->layout('components.layouts.app', ['title' => 'Input Nilai Siswa']);
    }
}
