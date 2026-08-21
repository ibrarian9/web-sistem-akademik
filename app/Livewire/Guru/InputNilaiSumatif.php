<?php

namespace App\Livewire\Guru;

use App\Models\Kelas;
use App\Models\LingkupMateri;
use App\Models\MataPelajaran;
use App\Models\NilaiSas;
use App\Models\NilaiSumatifTp;
use App\Models\RaporDetail;
use App\Models\Rapor;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\TujuanPembelajaran;
use App\Services\AutoNarasiService;
use Livewire\Component;

class InputNilaiSumatif extends Component
{
    public $kelas_id;
    public $mapel_id;
    public $lingkup_materi_id;
    public $semester_id;

    public $nilaiTpMatrix = []; // [siswa_id][tp_id] = nilai
    public $nilaiSasMatrix = []; // [siswa_id] = nilai

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

        $mapel = MataPelajaran::where('jenis', 'intrakurikuler_umum')->first() ?? MataPelajaran::first();
        if ($mapel) {
            $this->mapel_id = $mapel->id;
        }

        $this->loadMatrixData();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['kelas_id', 'mapel_id', 'lingkup_materi_id', 'semester_id'])) {
            $this->loadMatrixData();
        }
    }

    public function loadMatrixData()
    {
        $this->nilaiTpMatrix = [];
        $this->nilaiSasMatrix = [];

        if (!$this->kelas_id || !$this->semester_id) {
            return;
        }

        $siswas = Siswa::where(function ($q) {
            $q->where('kelas_id', $this->kelas_id)
              ->orWhere('kelas_tahfidz_id', $this->kelas_id);
        })->pluck('id');

        // Load Nilai TP Matrix
        $sumatifTps = NilaiSumatifTp::whereIn('siswa_id', $siswas)
            ->where('semester_id', $this->semester_id)
            ->get();

        foreach ($sumatifTps as $sTp) {
            $this->nilaiTpMatrix[$sTp->siswa_id][$sTp->tujuan_pembelajaran_id] = round($sTp->nilai);
        }

        // Load Nilai SAS Matrix
        if ($this->mapel_id) {
            $nilaiSases = NilaiSas::whereIn('siswa_id', $siswas)
                ->where('mapel_id', $this->mapel_id)
                ->where('semester_id', $this->semester_id)
                ->get();

            foreach ($nilaiSases as $sas) {
                $this->nilaiSasMatrix[$sas->siswa_id] = round($sas->nilai_sas);
            }
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

        if (!$this->kelas_id || !$this->semester_id) {
            session()->flash('error', 'Pilih kelas dan semester terlebih dahulu.');
            return;
        }

        // Save Sumatif TP
        foreach ($this->nilaiTpMatrix as $siswaId => $tpValues) {
            foreach ($tpValues as $tpId => $nilaiVal) {
                if ($nilaiVal !== '' && $nilaiVal !== null && is_numeric($nilaiVal)) {
                    NilaiSumatifTp::updateOrCreate(
                        [
                            'siswa_id' => $siswaId,
                            'tujuan_pembelajaran_id' => $tpId,
                            'semester_id' => $this->semester_id,
                        ],
                        [
                            'nilai' => (float)$nilaiVal,
                        ]
                    );
                }
            }
        }

        // Save Nilai SAS & Sync Rapor Detail + Auto Narasi
        if ($this->mapel_id) {
            foreach ($this->nilaiSasMatrix as $siswaId => $sasVal) {
                if ($sasVal !== '' && $sasVal !== null && is_numeric($sasVal)) {
                    NilaiSas::updateOrCreate(
                        [
                            'siswa_id' => $siswaId,
                            'mapel_id' => $this->mapel_id,
                            'semester_id' => $this->semester_id,
                        ],
                        [
                            'nilai_sas' => (float)$sasVal,
                        ]
                    );

                    // Sync to Rapor Detail
                    $this->syncRaporDetailForSiswa($siswaId);
                }
            }
        }

        session()->flash('message', 'Matriks Nilai Sumatif TP & SAS berhasil disimpan.');
        $this->loadMatrixData();
    }

    public function saveAllScores()
    {
        return $this->saveMatrix();
    }

    protected function syncRaporDetailForSiswa($siswaId)
    {
        $siswa = Siswa::find($siswaId);
        if (!$siswa || !$this->mapel_id || !$this->semester_id) return;

        // Calculate Average Sumatif TP
        $tpScores = NilaiSumatifTp::where('siswa_id', $siswaId)
            ->where('semester_id', $this->semester_id)
            ->whereHas('tujuanPembelajaran.lingkupMateri', function ($q) {
                $q->where('mapel_id', $this->mapel_id);
            })
            ->pluck('nilai');

        $avgTp = $tpScores->count() > 0 ? $tpScores->avg() : 0;

        // Get Nilai SAS
        $sas = NilaiSas::where('siswa_id', $siswaId)
            ->where('mapel_id', $this->mapel_id)
            ->where('semester_id', $this->semester_id)
            ->first();

        $nilaiSasVal = $sas ? (float)$sas->nilai_sas : $avgTp;

        // Calculate Nilai Akhir
        $nilaiAkhir = ($avgTp + $nilaiSasVal) / 2;

        // Generate Auto Narasi
        $narasi = AutoNarasiService::generateNarasiRapor($siswaId, $this->mapel_id, $this->semester_id);

        $rapor = Rapor::firstOrCreate(
            [
                'siswa_id' => $siswaId,
                'semester_id' => $this->semester_id,
                'tipe_rapor' => 'akademik',
            ],
            [
                'kelas_id' => $siswa->kelas_id,
                'catatan_wali_kelas' => 'Tingkatkan semangat belajar dan keaktifan di kelas.',
                'tanggal_terbit' => date('Y-m-d'),
                'status' => 'draft',
            ]
        );

        RaporDetail::updateOrCreate(
            [
                'rapor_id' => $rapor->id,
                'mapel_id' => $this->mapel_id,
            ],
            [
                'nilai_pengetahuan' => $avgTp,
                'nilai_keterampilan' => $nilaiSasVal,
                'nilai_akhir' => $nilaiAkhir,
                'predikat' => AutoNarasiService::getPredikatFormatted($nilaiAkhir),
            ]
        );
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

        $mapels = MataPelajaran::orderBy('nama_mapel', 'asc')->get();
        $semesters = Semester::orderBy('id', 'desc')->get();

        $lingkupMateris = [];
        if ($this->mapel_id) {
            $lingkupMateris = LingkupMateri::where('mapel_id', $this->mapel_id)->get();
        }

        $siswas = $this->kelas_id ? Siswa::where(function ($q) {
            $q->where('kelas_id', $this->kelas_id)
              ->orWhere('kelas_tahfidz_id', $this->kelas_id);
        })->get() : collect();

        $tpQuery = TujuanPembelajaran::query();
        if ($this->lingkup_materi_id) {
            $tpQuery->where('lingkup_materi_id', $this->lingkup_materi_id);
        } else if ($this->mapel_id) {
            $tpQuery->whereHas('lingkupMateri', function ($q) {
                $q->where('mapel_id', $this->mapel_id);
            });
        }
        $tps = $tpQuery->get();

        return view('livewire.guru.input-nilai-sumatif', [
            'kelases' => $kelases,
            'mapels' => $mapels,
            'lingkupMateris' => $lingkupMateris,
            'semesters' => $semesters,
            'siswas' => $siswas,
            'tps' => $tps,
            'allTps' => $tps,
        ])->layout('components.layouts.app', ['title' => 'Input Nilai Sumatif TP & SAS']);

    }
}
