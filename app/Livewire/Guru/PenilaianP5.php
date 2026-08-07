<?php

namespace App\Livewire\Guru;

use App\Models\DimensiP5;
use App\Models\Kelas;
use App\Models\NilaiP5;
use App\Models\ProyekP5;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SubdimensiP5;
use Livewire\Component;

class PenilaianP5 extends Component
{
    public $kelas_id;
    public $proyek_id;
    public $semester_id;

    public $nilaiP5Matrix = []; // [siswa_id][subdimensi_id][titik_sumatif] = nilai (1..4)

    public function mount()
    {
        // Access Guard: Block Guru Tahfizh
        $user = auth()->user();
        if ($user && $user->role?->nama === 'guru' && $user->guru) {
            $jenis = strtolower($user->guru->jenis_guru);
            if ($jenis === 'tahfidz' || $jenis === 'tahfizh') {
                session()->flash('error', 'Akses ditolak. Guru Tahfizh tidak mengelola modul Kurikulum Merdeka.');
                return redirect()->route('guru.input-tahfidz');
            }
        }

        $activeSemester = Semester::where('status_aktif', true)->first() ?? Semester::first();
        if ($activeSemester) {
            $this->semester_id = $activeSemester->id;
        }

        // Filter assigned classes for Guru Umum
        if ($user && $user->role?->nama === 'guru' && $user->guru) {
            $guruId = $user->guru->id;
            $myClasses = Kelas::where('guru_umum_id', $guruId)
                ->orWhereHas('guruMapelKelas', function ($q) use ($guruId) {
                    $q->where('guru_id', $guruId);
                })
                ->get();
            if ($myClasses->isNotEmpty()) {
                $this->kelas_id = $myClasses->first()->id;
            } else {
                $this->kelas_id = null;
            }
        } else {
            $kelas = Kelas::first();
            if ($kelas) {
                $this->kelas_id = $kelas->id;
            }
        }

        $proyek = ProyekP5::first();
        if (!$proyek) {
            $proyek = ProyekP5::create(['nama_proyek' => 'Lintas Disiplin Ilmu']);
            ProyekP5::create(['nama_proyek' => '7 Kebiasaan Anak Indonesia Hebat']);
            ProyekP5::create(['nama_proyek' => 'Cara Lain']);
        }
        $this->proyek_id = $proyek->id;

        $this->loadP5Matrix();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['kelas_id', 'proyek_id', 'semester_id'])) {
            $this->loadP5Matrix();
        }
    }

    public function loadP5Matrix()
    {
        $this->nilaiP5Matrix = [];

        if (!$this->kelas_id || !$this->proyek_id || !$this->semester_id) {
            return;
        }

        $siswas = Siswa::where('kelas_id', $this->kelas_id)->pluck('id');

        $scores = NilaiP5::whereIn('siswa_id', $siswas)
            ->where('proyek_id', $this->proyek_id)
            ->where('semester_id', $this->semester_id)
            ->get();

        foreach ($scores as $sc) {
            $this->nilaiP5Matrix[$sc->siswa_id][$sc->subdimensi_p5_id][$sc->titik_sumatif] = (int)$sc->nilai;
        }
    }

    public function saveMatrix()
    {
        $user = auth()->user();
        if ($user && $user->role?->nama === 'guru' && $user->guru) {
            $guruId = $user->guru->id;
            $allowedClass = Kelas::where('id', $this->kelas_id)
                ->where(function ($q) use ($guruId) {
                    $q->where('guru_umum_id', $guruId)
                      ->orWhereHas('guruMapelKelas', function ($mq) use ($guruId) {
                          $mq->where('guru_id', $guruId);
                      });
                })->first();

            if (!$allowedClass) {
                session()->flash('error', 'Akses ditolak. Anda hanya diperbolehkan menginput nilai untuk kelas bimbingan Anda.');
                return;
            }
        }

        if (!$this->kelas_id || !$this->proyek_id || !$this->semester_id) {
            session()->flash('error', 'Pilih kelas, proyek, dan semester terlebih dahulu.');
            return;
        }

        foreach ($this->nilaiP5Matrix as $siswaId => $subdimensiGroup) {
            foreach ($subdimensiGroup as $subdimensiId => $titikGroup) {
                foreach ($titikGroup as $titikSumatif => $nilaiSkala) {
                    if ($nilaiSkala !== '' && $nilaiSkala !== null) {
                        NilaiP5::updateOrCreate(
                            [
                                'siswa_id' => $siswaId,
                                'proyek_id' => $this->proyek_id,
                                'subdimensi_p5_id' => $subdimensiId,
                                'titik_sumatif' => (int)$titikSumatif,
                                'semester_id' => $this->semester_id,
                            ],
                            [
                                'nilai' => (int)$nilaiSkala,
                            ]
                        );
                    }
                }
            }
        }

        session()->flash('message', 'Penilaian P5 berhasil disimpan.');
        $this->loadP5Matrix();
    }

    public function render()
    {
        $user = auth()->user();
        if ($user && $user->role?->nama === 'guru' && $user->guru) {
            $guruId = $user->guru->id;
            $kelases = Kelas::where('guru_umum_id', $guruId)
                ->orWhereHas('guruMapelKelas', function ($q) use ($guruId) {
                    $q->where('guru_id', $guruId);
                })
                ->get();
        } else {
            $kelases = Kelas::all();
        }

        $proyeks = ProyekP5::all();
        $semesters = Semester::orderBy('id', 'desc')->get();
        $dimensis = DimensiP5::with('subdimensi')->get();


        $siswas = $this->kelas_id ? Siswa::where('kelas_id', $this->kelas_id)->get() : collect();

        return view('livewire.guru.penilaian-p5', [
            'kelases' => $kelases,
            'proyeks' => $proyeks,
            'semesters' => $semesters,
            'dimensis' => $dimensis,
            'siswas' => $siswas,
        ])->layout('components.layouts.app', ['title' => 'Penilaian P5 (Projek Profil Pelajar Pancasila)']);
    }
}
