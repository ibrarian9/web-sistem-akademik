<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Semester;
use App\Models\Rapor;
use App\Models\RaporDetail;
use App\Models\MataPelajaran;
use App\Models\KomponenNilai;
use App\Models\Nilai;
use App\Models\Notifikasi;
use App\Models\GuruMapelKelas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class KelolaRapor extends Component
{
    public $kelasId;
    public $siswaId;
    public $catatanWaliKelas;
    public $tanggalTerbit;
    
    public $activeSemester;
    public $myClasses = [];
    public $students = [];
    public $existingRapor = null;

    protected $rules = [
        'kelasId' => 'required|exists:kelas,id',
        'siswaId' => 'required|exists:siswa,id',
        'tanggalTerbit' => 'required|date',
        'catatanWaliKelas' => 'nullable|string|max:1000',
    ];

    public function mount()
    {
        // 1. Get active semester
        $activeSem = DB::table('semester')
            ->join('tahun_ajaran', 'semester.tahun_ajaran_id', '=', 'tahun_ajaran.id')
            ->where('tahun_ajaran.status_aktif', true)
            ->where('semester.status_aktif', true)
            ->select('semester.id')
            ->first();

        if ($activeSem) {
            $this->activeSemester = Semester::with('tahunAjaran')->find($activeSem->id);
        } else {
            $this->activeSemester = Semester::with('tahunAjaran')->latest()->first();
        }

        $this->tanggalTerbit = date('Y-m-d');

        // 2. Find classes where current teacher is Wali Kelas (guru_umum_id or guru_tahfidz_id)
        $user = Auth::user();
        if ($user && $user->role->nama === 'guru' && $user->guru) {
            $guruId = $user->guru->id;
            $this->myClasses = Kelas::where('guru_umum_id', $guruId)
                ->orWhere('guru_tahfidz_id', $guruId)
                ->get();
                
            if ($this->myClasses->count() > 0) {
                $this->kelasId = $this->myClasses->first()->id;
                $this->updatedKelasId();
            }
        }
    }

    public function updatedKelasId()
    {
        $this->siswaId = null;
        $this->students = [];
        $this->existingRapor = null;
        $this->catatanWaliKelas = '';

        if ($this->kelasId) {
            $this->students = Siswa::where('kelas_id', $this->kelasId)
                ->where('siswa.status', 'aktif')
                ->join('users', 'siswa.user_id', '=', 'users.id')
                ->orderBy('users.nama', 'asc')
                ->select('siswa.*')
                ->get();

            if ($this->students->count() > 0) {
                $this->siswaId = $this->students->first()->id;
                $this->updatedSiswaId();
            }
        }
    }

    public function updatedSiswaId()
    {
        $this->existingRapor = null;
        $this->catatanWaliKelas = '';
        
        if ($this->siswaId && $this->activeSemester) {
            $this->existingRapor = Rapor::where([
                'siswa_id' => $this->siswaId,
                'semester_id' => $this->activeSemester->id,
            ])->first();

            if ($this->existingRapor) {
                $this->catatanWaliKelas = $this->existingRapor->catatan_wali_kelas;
                $this->tanggalTerbit = $this->existingRapor->tanggal_terbit instanceof Carbon 
                    ? $this->existingRapor->tanggal_terbit->format('Y-m-d')
                    : Carbon::parse($this->existingRapor->tanggal_terbit)->format('Y-m-d');
            } else {
                $this->tanggalTerbit = date('Y-m-d');
            }
        }
    }

    public function getCalculatedPreviewGradesProperty()
    {
        if (!$this->siswaId || !$this->activeSemester || !$this->kelasId) {
            return [];
        }

        // Get subjects taught in this class
        $mapelIds = GuruMapelKelas::where('kelas_id', $this->kelasId)
            ->pluck('mapel_id')
            ->unique();
        $subjects = MataPelajaran::whereIn('id', $mapelIds)->orderBy('nama_mapel')->get();

        // Get all student's grades for this class, student, and active semester
        $allNilais = Nilai::where([
            'siswa_id' => $this->siswaId,
            'kelas_id' => $this->kelasId,
            'semester_id' => $this->activeSemester->id,
        ])->get();

        $components = KomponenNilai::all();
        $componentsByCategory = $components->groupBy('kategori');

        $preview = [];

        foreach ($subjects as $mapel) {
            $mapelNilais = $allNilais->where('mapel_id', $mapel->id);

            // 1. Calculate category averages
            $categoryScores = [];
            foreach (['pengetahuan', 'keterampilan', 'sikap', 'keagamaan'] as $cat) {
                $catComps = $componentsByCategory->get($cat, collect());
                $catGrades = [];
                foreach ($catComps as $comp) {
                    $compNilais = $mapelNilais->where('komponen_nilai_id', $comp->id);
                    if ($compNilais->count() > 0) {
                        $catGrades[] = $compNilais->avg('nilai');
                    }
                }
                $categoryScores[$cat] = count($catGrades) > 0 ? round(array_sum($catGrades) / count($catGrades), 2) : null;
            }

            // 2. Calculate overall Nilai Akhir (weighted average of components)
            $finalGrade = 0.00;
            $totalWeight = 0.00;
            foreach ($components as $comp) {
                $compNilais = $mapelNilais->where('komponen_nilai_id', $comp->id);
                if ($compNilais->count() > 0) {
                    $avg = $compNilais->avg('nilai');
                    $finalGrade += $avg * ($comp->bobot / 100);
                    $totalWeight += $comp->bobot;
                }
            }

            $nilaiAkhir = $totalWeight > 0 ? round($finalGrade / ($totalWeight / 100), 2) : 0.00;

            // 3. Determine Predicate
            $predikat = 'E';
            if ($nilaiAkhir >= 90) {
                $predikat = 'A';
            } elseif ($nilaiAkhir >= 80) {
                $predikat = 'B';
            } elseif ($nilaiAkhir >= 70) {
                $predikat = 'C';
            } elseif ($nilaiAkhir >= 60) {
                $predikat = 'D';
            }

            $preview[] = [
                'mapel_id' => $mapel->id,
                'nama_mapel' => $mapel->nama_mapel,
                'nilai_pengetahuan' => $categoryScores['pengetahuan'],
                'nilai_keterampilan' => $categoryScores['keterampilan'],
                'nilai_sikap' => $categoryScores['sikap'],
                'nilai_keagamaan' => $categoryScores['keagamaan'],
                'nilai_akhir' => $nilaiAkhir,
                'predikat' => $predikat,
            ];
        }

        return $preview;
    }

    public function publishRapor()
    {
        $this->validate();

        if (!$this->activeSemester) {
            session()->flash('error', 'Semester aktif tidak ditemukan.');
            return;
        }

        $siswa = Siswa::find($this->siswaId);
        if (!$siswa) {
            session()->flash('error', 'Siswa tidak ditemukan.');
            return;
        }

        $grades = $this->calculatedPreviewGrades;
        if (count($grades) === 0) {
            session()->flash('error', 'Tidak ada data nilai atau mata pelajaran untuk siswa ini pada semester aktif.');
            return;
        }

        DB::beginTransaction();
        try {
            // 1. Create or update Rapor header
            $rapor = Rapor::updateOrCreate([
                'siswa_id' => $this->siswaId,
                'semester_id' => $this->activeSemester->id,
            ], [
                'kelas_id' => $this->kelasId,
                'catatan_wali_kelas' => $this->catatanWaliKelas ?: null,
                'tanggal_terbit' => $this->tanggalTerbit,
            ]);

            // 2. Create or update Rapor details
            foreach ($grades as $grade) {
                RaporDetail::updateOrCreate([
                    'rapor_id' => $rapor->id,
                    'mapel_id' => $grade['mapel_id'],
                ], [
                    'nilai_pengetahuan' => $grade['nilai_pengetahuan'],
                    'nilai_keterampilan' => $grade['nilai_keterampilan'],
                    'nilai_sikap' => $grade['nilai_sikap'],
                    'nilai_keagamaan' => $grade['nilai_keagamaan'],
                    'nilai_akhir' => $grade['nilai_akhir'],
                    'predikat' => $grade['predikat'],
                ]);
            }

            // 3. Create Notification for Student
            $message = "Rapor Hasil Belajar Anda untuk Semester " . ucfirst($this->activeSemester->semester) . " (" . ($this->activeSemester->tahunAjaran->nama ?? '') . ") telah resmi diterbitkan oleh wali kelas.";
            Notifikasi::create([
                'user_id' => $siswa->user_id,
                'siswa_id' => $siswa->id,
                'judul' => 'Rapor Resmi Diterbitkan',
                'isi_pesan' => $message,
                'jenis' => 'rapor_terbit',
                'channel' => 'in_app',
                'status_kirim' => 'terkirim',
                'dikirim_pada' => now(),
            ]);

            // 4. Log to Audit/Activity log
            DB::table('activity_log')->insert([
                'description' => 'Penerbitan Rapor Hasil Belajar',
                'subject_type' => 'App\Models\Rapor',
                'subject_id' => $rapor->id,
                'event' => $this->existingRapor ? 'updated' : 'created',
                'causer_type' => 'App\Models\User',
                'causer_id' => Auth::id(),
                'siswa_id' => $siswa->id,
                'properties' => json_encode(['kelas_id' => $this->kelasId, 'semester_id' => $this->activeSemester->id]),
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            session()->flash('success', 'Rapor siswa ' . $siswa->user->nama . ' berhasil diterbitkan.');
            $this->updatedSiswaId(); // refresh state
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menerbitkan rapor: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.guru.kelola-rapor')
            ->layout('components.layouts.app', ['title' => 'Kelola Rapor Kelas']);
    }
}
