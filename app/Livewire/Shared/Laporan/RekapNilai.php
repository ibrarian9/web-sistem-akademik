<?php

namespace App\Livewire\Shared\Laporan;

use Livewire\Component;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\MataPelajaran;
use App\Models\Semester;
use App\Models\KomponenNilai;
use App\Models\Nilai;
use App\Models\GuruMapelKelas;
use Illuminate\Support\Facades\DB;

class RekapNilai extends Component
{
    public $kelasId;
    public $mapelId;
    public $semesterId;

    public function mount()
    {
        $activeSemester = DB::table('semester')
            ->join('tahun_ajaran', 'semester.tahun_ajaran_id', '=', 'tahun_ajaran.id')
            ->where('tahun_ajaran.status_aktif', true)
            ->where('semester.status_aktif', true)
            ->select('semester.id')
            ->first();

        if ($activeSemester) {
            $this->semesterId = $activeSemester->id;
        } else {
            $this->semesterId = Semester::latest()->first()->id ?? null;
        }

        $classes = $this->getAvailableClasses();
        if ($classes->count() > 0) {
            $this->kelasId = $classes->first()->id;
        }

        $this->updatedKelasId();
    }

    public function getAvailableClasses()
    {
        $user = auth()->user();
        if ($user->role->nama === 'guru') {
            $guru = $user->guru;
            if ($guru) {
                $kelasIds = Kelas::where('guru_umum_id', $guru->id)
                    ->orWhere('guru_tahfidz_id', $guru->id)
                    ->pluck('id')
                    ->merge(
                        GuruMapelKelas::where('guru_id', $guru->id)->pluck('kelas_id')
                    )
                    ->unique();
                return Kelas::whereIn('id', $kelasIds)->get();
            }
            return collect();
        }
        return Kelas::all();
    }

    public function updatedKelasId()
    {
        $mapels = $this->getAvailableMapels();
        if ($mapels->count() > 0) {
            $this->mapelId = $mapels->first()->id;
        } else {
            $this->mapelId = null;
        }
    }

    public function getAvailableMapels()
    {
        if (!$this->kelasId) {
            return collect();
        }

        $user = auth()->user();
        if ($user->role->nama === 'guru') {
            $guru = $user->guru;
            if ($guru) {
                $mapelIds = GuruMapelKelas::where('guru_id', $guru->id)
                    ->where('kelas_id', $this->kelasId)
                    ->pluck('mapel_id')
                    ->unique();
                return MataPelajaran::whereIn('id', $mapelIds)->get();
            }
            return collect();
        }

        // Admin/TU can choose any mapel associated with this class
        $mapelIds = GuruMapelKelas::where('kelas_id', $this->kelasId)
            ->pluck('mapel_id')
            ->unique();
        return MataPelajaran::whereIn('id', $mapelIds)->get();
    }

    public function getMatrixData()
    {
        if (!$this->kelasId || !$this->mapelId || !$this->semesterId) {
            return [
                'matrix' => [],
                'components' => [],
                'kelas' => null,
                'mapel' => null,
                'semester' => null
            ];
        }

        $kelas = Kelas::find($this->kelasId);
        $mapel = MataPelajaran::find($this->mapelId);
        $semester = Semester::with('tahunAjaran')->find($this->semesterId);

        $students = Siswa::where('kelas_id', $this->kelasId)
            ->where('siswa.status', 'aktif')
            ->join('users', 'siswa.user_id', '=', 'users.id')
            ->orderBy('users.nama', 'asc')
            ->select('siswa.*')
            ->get();

        $components = KomponenNilai::orderBy('urutan')->get();
        $nilais = Nilai::where([
            'kelas_id' => $this->kelasId,
            'mapel_id' => $this->mapelId,
            'semester_id' => $this->semesterId,
        ])->get();

        $matrix = [];
        foreach ($students as $siswa) {
            $compGrades = [];
            $finalGrade = 0.00;
            $totalWeight = 0.00;

            foreach ($components as $comp) {
                // Get all grades for this student and this component
                $studentCompNilais = $nilais->where('siswa_id', $siswa->id)
                    ->where('komponen_nilai_id', $comp->id);
                
                if ($studentCompNilais->count() > 0) {
                    // Average the grades if multiple entries exist
                    $avg = $studentCompNilais->avg('nilai');
                    $compGrades[$comp->id] = round($avg, 2);
                    
                    // Add to final grade calculation (weighted)
                    $finalGrade += $avg * ($comp->bobot / 100);
                    $totalWeight += $comp->bobot;
                } else {
                    $compGrades[$comp->id] = null;
                }
            }

            // Calculate predicate
            $predikat = 'E';
            if ($finalGrade >= 90) {
                $predikat = 'A';
            } elseif ($finalGrade >= 80) {
                $predikat = 'B';
            } elseif ($finalGrade >= 70) {
                $predikat = 'C';
            } elseif ($finalGrade >= 60) {
                $predikat = 'D';
            }

            $matrix[] = [
                'siswa' => $siswa,
                'compGrades' => $compGrades,
                'finalGrade' => round($finalGrade, 2),
                'predikat' => $predikat
            ];
        }

        return [
            'matrix' => $matrix,
            'components' => $components,
            'kelas' => $kelas,
            'mapel' => $mapel,
            'semester' => $semester
        ];
    }

    public function render()
    {
        $classes = $this->getAvailableClasses();
        $mapels = $this->getAvailableMapels();
        $semesters = Semester::with('tahunAjaran')->orderBy('id', 'desc')->get();
        $data = $this->getMatrixData();

        return view('livewire.shared.laporan.rekap-nilai', array_merge($data, [
            'classes' => $classes,
            'mapels' => $mapels,
            'semesters' => $semesters
        ]))->layout('components.layouts.app', ['title' => 'Rekap Nilai Siswa']);
    }
}
