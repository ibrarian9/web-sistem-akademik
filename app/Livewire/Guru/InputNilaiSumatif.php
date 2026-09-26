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
            $firstLm = LingkupMateri::where('mapel_id', $mapel->id)->orderBy('urutan', 'asc')->first();
            if ($firstLm) {
                $this->lingkup_materi_id = $firstLm->id;
            }
        }

        $this->loadMatrixData();
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'mapel_id') {
            $firstLm = LingkupMateri::where('mapel_id', $this->mapel_id)->orderBy('urutan', 'asc')->first();
            $this->lingkup_materi_id = $firstLm ? $firstLm->id : null;
        }

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
            $this->nilaiTpMatrix[$sTp->siswa_id][$sTp->tp_id] = round($sTp->nilai);
        }

        // Load Nilai SAS Matrix
        if ($this->mapel_id) {
            $nilaiSases = NilaiSas::whereIn('siswa_id', $siswas)
                ->where('mapel_id', $this->mapel_id)
                ->where('semester_id', $this->semester_id)
                ->get();

            foreach ($nilaiSases as $sas) {
                $this->nilaiSasMatrix[$sas->siswa_id] = $sas->nilai_sas !== null ? round($sas->nilai_sas) : ($sas->nilai !== null ? round($sas->nilai) : '');
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

        // Validasi ketat batas nilai 0 - 100 untuk mencegah kesalahan input
        foreach ($this->nilaiTpMatrix as $siswaId => $tpValues) {
            foreach ($tpValues as $tpId => $nilaiVal) {
                if ($nilaiVal !== '' && $nilaiVal !== null) {
                    if (!is_numeric($nilaiVal) || (float)$nilaiVal < 0 || (float)$nilaiVal > 100) {
                        session()->flash('error', 'Semua nilai TP harus berupa angka antara 0 sampai 100.');
                        return;
                    }
                }
            }
        }

        foreach ($this->nilaiSasMatrix as $siswaId => $sasVal) {
            if ($sasVal !== '' && $sasVal !== null) {
                if (!is_numeric($sasVal) || (float)$sasVal < 0 || (float)$sasVal > 100) {
                    session()->flash('error', 'Semua nilai SAS harus berupa angka antara 0 sampai 100.');
                    return;
                }
            }
        }

        $now = now();

        // 1. Batch Upsert Sumatif TP in chunks
        $tpRows = [];
        foreach ($this->nilaiTpMatrix as $siswaId => $tpValues) {
            foreach ($tpValues as $tpId => $nilaiVal) {
                if ($nilaiVal !== '' && $nilaiVal !== null && is_numeric($nilaiVal)) {
                    $tpRows[] = [
                        'siswa_id' => (int)$siswaId,
                        'tp_id' => (int)$tpId,
                        'semester_id' => (int)$this->semester_id,
                        'nilai' => (float)$nilaiVal,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        if (!empty($tpRows)) {
            foreach (array_chunk($tpRows, 250) as $chunk) {
                NilaiSumatifTp::upsert($chunk, ['siswa_id', 'tp_id', 'semester_id'], ['nilai', 'updated_at']);
            }
        }

        // 2. Batch Upsert Nilai SAS & Batch Sync Rapor Detail
        if ($this->mapel_id) {
            $siswas = Siswa::where(function ($q) {
                $q->where('kelas_id', $this->kelas_id)
                  ->orWhere('kelas_tahfidz_id', $this->kelas_id);
            })->pluck('id');

            $allSiswaIds = array_values(array_unique(array_filter(array_merge(
                array_map('intval', array_keys($this->nilaiTpMatrix)),
                array_map('intval', array_keys($this->nilaiSasMatrix)),
                $siswas->toArray()
            ))));

            $sasRows = [];
            foreach ($allSiswaIds as $siswaId) {
                $sasVal = $this->nilaiSasMatrix[$siswaId] ?? null;
                $hasSas = ($sasVal !== '' && $sasVal !== null && is_numeric($sasVal));

                if ($hasSas) {
                    $sasRows[] = [
                        'siswa_id' => (int)$siswaId,
                        'mapel_id' => (int)$this->mapel_id,
                        'semester_id' => (int)$this->semester_id,
                        'nilai' => (float)$sasVal,
                        'nilai_sas' => (float)$sasVal,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if (!empty($sasRows)) {
                foreach (array_chunk($sasRows, 250) as $chunk) {
                    NilaiSas::upsert($chunk, ['siswa_id', 'mapel_id', 'semester_id'], ['nilai', 'nilai_sas', 'updated_at']);
                }
            }

            // Sync to Rapor Detail via high-performance batch generation
            $this->syncRaporDetailsForClass($allSiswaIds);
        }

        $this->dispatch('scores-saved');
        session()->flash('message', 'Matriks Nilai Sumatif TP & SAS berhasil disimpan.');
        $this->loadMatrixData();
    }

    public function saveAllScores()
    {
        return $this->saveMatrix();
    }

    /**
     * Batch synchronize rapor and rapor detail for a class.
     */
    protected function syncRaporDetailsForClass(array $siswaIds)
    {
        if (empty($siswaIds) || !$this->mapel_id || !$this->semester_id) {
            return;
        }

        $siswas = Siswa::whereIn('id', $siswaIds)->get()->keyBy('id');
        if ($siswas->isEmpty()) {
            return;
        }

        $autoNarasiService = app(AutoNarasiService::class);
        $batchNarratives = $autoNarasiService->generateForMapelBatch($siswaIds, (int)$this->mapel_id, (int)$this->semester_id);

        // Fetch or create Rapors
        $existingRapors = Rapor::whereIn('siswa_id', $siswaIds)
            ->where('semester_id', $this->semester_id)
            ->where('tipe_rapor', 'akademik')
            ->get()
            ->keyBy('siswa_id');

        $now = now();
        $today = date('Y-m-d');
        $newRapors = [];

        foreach ($siswaIds as $siswaId) {
            if (!$existingRapors->has($siswaId)) {
                $siswa = $siswas->get($siswaId);
                if ($siswa) {
                    $newRapors[] = [
                        'siswa_id' => $siswaId,
                        'semester_id' => (int)$this->semester_id,
                        'tipe_rapor' => 'akademik',
                        'kelas_id' => $siswa->kelas_id,
                        'catatan_wali_kelas' => 'Tingkatkan semangat belajar dan keaktifan di kelas.',
                        'tanggal_terbit' => $today,
                        'status' => 'draft',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        if (!empty($newRapors)) {
            Rapor::insert($newRapors);
            $existingRapors = Rapor::whereIn('siswa_id', $siswaIds)
                ->where('semester_id', $this->semester_id)
                ->where('tipe_rapor', 'akademik')
                ->get()
                ->keyBy('siswa_id');
        }

        // Prepare RaporDetail rows for bulk upsert
        $raporDetailRows = [];
        foreach ($siswaIds as $siswaId) {
            $rapor = $existingRapors->get($siswaId);
            $narrative = $batchNarratives[$siswaId] ?? null;

            if ($rapor && $narrative) {
                $raporDetailRows[] = [
                    'rapor_id' => (int)$rapor->id,
                    'mapel_id' => (int)$this->mapel_id,
                    'nilai_pengetahuan' => $narrative['nilai_akhir'],
                    'nilai_keterampilan' => $narrative['nilai_akhir'],
                    'nilai_akhir' => $narrative['nilai_akhir'],
                    'predikat' => $narrative['predikat'],
                    'deskripsi_tertinggi' => $narrative['deskripsi_tertinggi'] ?: null,
                    'deskripsi_terendah' => $narrative['deskripsi_terendah'] ?: null,
                    'narasi_capaian_full' => $narrative['narasi_capaian_full'] ?: null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (!empty($raporDetailRows)) {
            foreach (array_chunk($raporDetailRows, 250) as $chunk) {
                RaporDetail::upsert(
                    $chunk,
                    ['rapor_id', 'mapel_id'],
                    [
                        'nilai_pengetahuan',
                        'nilai_keterampilan',
                        'nilai_akhir',
                        'predikat',
                        'deskripsi_tertinggi',
                        'deskripsi_terendah',
                        'narasi_capaian_full',
                        'updated_at',
                    ]
                );
            }
        }
    }

    protected function syncRaporDetailForSiswa($siswaId)
    {
        $this->syncRaporDetailsForClass([(int)$siswaId]);
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
            $lingkupMateris = LingkupMateri::where('mapel_id', $this->mapel_id)->orderBy('urutan', 'asc')->get();
        }

        $siswas = $this->kelas_id ? Siswa::where(function ($q) {
            $q->where('kelas_id', $this->kelas_id)
              ->orWhere('kelas_tahfidz_id', $this->kelas_id);
        })->with('user')->get() : collect();

        $tpQuery = TujuanPembelajaran::query();
        if ($this->lingkup_materi_id) {
            $tpQuery->where('lingkup_materi_id', $this->lingkup_materi_id);
        } else if ($this->mapel_id) {
            $tpQuery->whereHas('lingkupMateri', function ($q) {
                $q->where('mapel_id', $this->mapel_id);
            });
        }
        $tps = $tpQuery->orderBy('urutan', 'asc')->get();

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
