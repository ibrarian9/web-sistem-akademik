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
use App\Models\LingkupMateri;
use App\Models\NilaiSumatifTp;
use App\Models\NilaiSas;
use App\Models\RaporDetail;
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
                'babs' => collect(),
                'kelas' => null,
                'mapel' => null,
                'semester' => null
            ];
        }

        $kelas = Kelas::find($this->kelasId);
        $mapel = MataPelajaran::find($this->mapelId);
        $semester = Semester::with('tahunAjaran')->find($this->semesterId);

        $students = Siswa::with('user')
            ->where('kelas_id', $this->kelasId)
            ->where('siswa.status', 'aktif')
            ->join('users', 'siswa.user_id', '=', 'users.id')
            ->orderBy('users.nama', 'asc')
            ->select('siswa.*')
            ->get();

        $babs = LingkupMateri::where('mapel_id', $this->mapelId)
            ->with(['tujuanPembelajaran'])
            ->orderBy('urutan', 'asc')
            ->get();

        $sumatifTps = NilaiSumatifTp::whereIn('siswa_id', $students->pluck('id'))
            ->where('semester_id', $this->semesterId)
            ->get();

        $sasScores = NilaiSas::whereIn('siswa_id', $students->pluck('id'))
            ->where('mapel_id', $this->mapelId)
            ->where('semester_id', $this->semesterId)
            ->get()
            ->keyBy('siswa_id');

        $raporDetails = RaporDetail::whereHas('rapor', function ($q) use ($students) {
            $q->whereIn('siswa_id', $students->pluck('id'))
              ->where('semester_id', $this->semesterId);
        })
        ->where('mapel_id', $this->mapelId)
        ->with('rapor')
        ->get()
        ->keyBy(fn($d) => $d->rapor->siswa_id);

        $matrix = [];
        foreach ($students as $siswa) {
            $babGrades = [];
            $allBabScores = [];

            foreach ($babs as $bab) {
                $tpIds = $bab->tujuanPembelajaran->pluck('id')->toArray();
                if (!empty($tpIds)) {
                    $scores = $sumatifTps->where('siswa_id', $siswa->id)->whereIn('tp_id', $tpIds);
                    if ($scores->isNotEmpty()) {
                        $avg = round($scores->avg('nilai'), 1);
                        $babGrades[$bab->id] = $avg;
                        $allBabScores[] = $avg;
                    } else {
                        $babGrades[$bab->id] = null;
                    }
                } else {
                    $babGrades[$bab->id] = null;
                }
            }

            $sasRecord = $sasScores[$siswa->id] ?? null;
            $sasVal = $sasRecord ? (float) ($sasRecord->nilai_sas !== null ? $sasRecord->nilai_sas : $sasRecord->nilai) : null;

            $detail = $raporDetails[$siswa->id] ?? null;

            if ($detail && $detail->nilai_akhir !== null) {
                $finalScore = (float) $detail->nilai_akhir;
                $predikat = $detail->predikat ?? 'D';
            } else {
                $comps = $allBabScores;
                if ($sasVal !== null) {
                    $comps[] = $sasVal;
                }
                $finalScore = count($comps) > 0 ? round(array_sum($comps) / count($comps), 2) : 0.00;

                $predikat = 'D';
                if ($finalScore >= 90) $predikat = 'A';
                elseif ($finalScore >= 80) $predikat = 'B';
                elseif ($finalScore >= 70) $predikat = 'C';
            }

            $matrix[] = [
                'siswa' => $siswa,
                'babGrades' => $babGrades,
                'nilaiSas' => $sasVal,
                'finalGrade' => $finalScore,
                'predikat' => $predikat
            ];
        }

        return [
            'matrix' => $matrix,
            'babs' => $babs,
            'kelas' => $kelas,
            'mapel' => $mapel,
            'semester' => $semester
        ];
    }

    public function downloadPdf()
    {
        $data = $this->getMatrixData();
        if (!$data['kelas'] || !$data['mapel'] || !$data['semester'] || empty($data['matrix'])) {
            session()->flash('error', 'Tidak ada data nilai siswa untuk dicetak.');
            return;
        }

        $pdfData = [
            'matrix' => $data['matrix'],
            'babs' => $data['babs'],
            'kelas' => $data['kelas'],
            'mapel' => $data['mapel'],
            'semester' => $data['semester'],
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('livewire.shared.laporan.pdf-rekap-nilai', $pdfData)
            ->setPaper('a4', 'landscape');

        $filename = 'rekap-nilai-' . strtolower(str_replace(' ', '-', $data['kelas']->nama_kelas)) . '-' . strtolower(str_replace(' ', '-', $data['mapel']->nama_mapel)) . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
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
