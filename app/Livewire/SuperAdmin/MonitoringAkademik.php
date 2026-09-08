<?php

namespace App\Livewire\SuperAdmin;

use App\Models\AbsensiGuru;
use App\Models\AbsensiSiswa;
use App\Models\Guru;
use App\Models\GuruMapelKelas;
use App\Models\Kelas;
use App\Models\LingkupMateri;
use App\Models\MataPelajaran;
use App\Models\NilaiSas;
use App\Models\NilaiSumatifTp;
use App\Models\RaporDetail;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\TemplateDeskripsi;
use App\Models\TujuanPembelajaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MonitoringAkademik extends Component
{
    public string $activeTab = 'progres_guru'; // 'progres_guru', 'nilai', 'kurikulum', 'absen'

    // KPI Header Properties
    public float $avgNilaiSekolah = 0.0;
    public int $totalBab = 0;
    public int $totalTp = 0;
    public float $persenHadirSiswaToday = 0.0;
    public int $guruHadirToday = 0;
    public int $totalGuruAktif = 0;

    // Filter Tab Progres Guru
    public ?int $progresKelasId = null;
    public ?int $progresMapelId = null;
    public string $searchProgresGuru = '';

    // Filter Tab 1: Nilai
    public ?int $selectedKelasId = null;
    public ?int $selectedMapelId = null;
    public ?int $selectedSemesterId = null;

    // Filter Tab 2: Kurikulum (Bab & TP)
    public ?int $kurikulumMapelId = null;
    public string $searchBabTp = '';

    // Filter Tab 3: Absensi
    public string $absenSubTab = 'siswa'; // 'siswa', 'guru'
    public string $selectedTanggal = '';
    public ?int $absenKelasId = null;
    public string $searchSiswaAbsen = '';
    public string $searchGuruAbsen = '';

    public function mount()
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role->nama ?? '', ['super_admin', 'super_admin_2'])) {
            abort(403, 'Akses khusus Super Administrator.');
        }

        $this->selectedTanggal = Carbon::today()->toDateString();

        // Get Active Semester
        $activeSemester = DB::table('semester')
            ->join('tahun_ajaran', 'semester.tahun_ajaran_id', '=', 'tahun_ajaran.id')
            ->where('tahun_ajaran.status_aktif', true)
            ->where('semester.status_aktif', true)
            ->select('semester.id')
            ->first();

        $this->selectedSemesterId = $activeSemester ? $activeSemester->id : (Semester::latest()->first()->id ?? null);

        // Default Kelas & Mapel
        $firstKelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->first();
        if ($firstKelas) {
            $this->selectedKelasId = $firstKelas->id;
        }

        $firstMapel = MataPelajaran::where(function ($q) {
            $q->where('jenis', 'intrakurikuler_umum')->orWhere('jenis', 'umum');
        })->first();

        if ($firstMapel) {
            $this->selectedMapelId = $firstMapel->id;
            $this->kurikulumMapelId = $firstMapel->id;
        }

        $this->loadKpiMetrics();
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['progres_guru', 'nilai', 'kurikulum', 'absen'])) {
            $this->activeTab = $tab;
        }
    }

    public function inspectNilai(int $kelasId, int $mapelId): void
    {
        $this->selectedKelasId = $kelasId;
        $this->selectedMapelId = $mapelId;
        $this->activeTab = 'nilai';
    }

    public function setAbsenSubTab(string $subTab): void
    {
        if (in_array($subTab, ['siswa', 'guru'])) {
            $this->absenSubTab = $subTab;
        }
    }

    public function loadKpiMetrics(): void
    {
        // 1. Rata-rata Nilai Rapor Semester Aktif
        $avgScore = RaporDetail::whereHas('rapor', function ($q) {
            if ($this->selectedSemesterId) {
                $q->where('semester_id', $this->selectedSemesterId);
            }
        })->whereNotNull('nilai_akhir')->avg('nilai_akhir');

        $this->avgNilaiSekolah = $avgScore ? round(floatval($avgScore), 1) : 0.0;

        // 2. Total Bab & TP
        $this->totalBab = LingkupMateri::count();
        $this->totalTp = TujuanPembelajaran::count();

        // 3. Kehadiran Siswa Hari Ini
        $totalAbsenSiswaToday = AbsensiSiswa::whereDate('tanggal', $this->selectedTanggal)->count();
        if ($totalAbsenSiswaToday > 0) {
            $hadirCount = AbsensiSiswa::whereDate('tanggal', $this->selectedTanggal)
                ->where('status', 'hadir')
                ->count();
            $this->persenHadirSiswaToday = round(($hadirCount / $totalAbsenSiswaToday) * 100, 1);
        } else {
            $this->persenHadirSiswaToday = 0.0;
        }

        // 4. Kehadiran Guru Hari Ini
        $this->totalGuruAktif = Guru::where('status_aktif', true)->count();
        $this->guruHadirToday = AbsensiGuru::whereDate('tanggal', $this->selectedTanggal)
            ->whereIn('status', ['hadir', 'terlambat'])
            ->count();
    }

    public function getMatrixNilaiProperty(): array
    {
        if (!$this->selectedKelasId || !$this->selectedMapelId || !$this->selectedSemesterId) {
            return [
                'students' => [],
                'babs' => collect(),
                'guruPengampu' => '-',
                'avgKelas' => 0.0,
                'distribusi' => ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0],
            ];
        }

        $students = Siswa::with('user')
            ->where('siswa.kelas_id', $this->selectedKelasId)
            ->where('siswa.status', 'aktif')
            ->join('users', 'siswa.user_id', '=', 'users.id')
            ->orderBy('users.nama', 'asc')
            ->select('siswa.*')
            ->get();

        $babs = LingkupMateri::where('mapel_id', $this->selectedMapelId)
            ->with(['tujuanPembelajaran'])
            ->orderBy('urutan', 'asc')
            ->get();

        $sumatifTps = NilaiSumatifTp::whereIn('siswa_id', $students->pluck('id'))
            ->where('semester_id', $this->selectedSemesterId)
            ->get();

        $sasScores = NilaiSas::whereIn('siswa_id', $students->pluck('id'))
            ->where('mapel_id', $this->selectedMapelId)
            ->where('semester_id', $this->selectedSemesterId)
            ->get()
            ->keyBy('siswa_id');

        $raporDetails = RaporDetail::whereHas('rapor', function ($q) use ($students) {
            $q->whereIn('siswa_id', $students->pluck('id'))
              ->where('semester_id', $this->selectedSemesterId);
        })
        ->where('mapel_id', $this->selectedMapelId)
        ->with('rapor')
        ->get()
        ->keyBy(fn($d) => $d->rapor->siswa_id);

        $guruMapel = GuruMapelKelas::where('kelas_id', $this->selectedKelasId)
            ->where('mapel_id', $this->selectedMapelId)
            ->with('guru.user')
            ->first();

        $guruPengampu = $guruMapel?->guru?->user?->nama ?? '-';

        $matrix = [];
        $totalScores = [];
        $distribusi = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0];

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
                $finalScore = count($comps) > 0 ? round(array_sum($comps) / count($comps), 1) : null;

                $predikat = '-';
                if ($finalScore !== null) {
                    if ($finalScore >= 90) $predikat = 'A';
                    elseif ($finalScore >= 80) $predikat = 'B';
                    elseif ($finalScore >= 70) $predikat = 'C';
                    else $predikat = 'D';
                }
            }

            if ($finalScore !== null) {
                $totalScores[] = $finalScore;
                if (isset($distribusi[$predikat])) {
                    $distribusi[$predikat]++;
                }
            }

            $matrix[] = [
                'siswa' => $siswa,
                'babGrades' => $babGrades,
                'nilaiSas' => $sasVal,
                'finalGrade' => $finalScore,
                'predikat' => $predikat,
            ];
        }

        $avgKelas = count($totalScores) > 0 ? round(array_sum($totalScores) / count($totalScores), 1) : 0.0;

        return [
            'students' => $matrix,
            'babs' => $babs,
            'guruPengampu' => $guruPengampu,
            'avgKelas' => $avgKelas,
            'distribusi' => $distribusi,
        ];
    }

    public function getKurikulumDataProperty(): array
    {
        if (!$this->kurikulumMapelId) {
            return [
                'mapel' => null,
                'babs' => collect(),
                'template' => null,
                'guruList' => collect(),
            ];
        }

        $mapel = MataPelajaran::find($this->kurikulumMapelId);

        $query = LingkupMateri::where('mapel_id', $this->kurikulumMapelId)
            ->with(['tujuanPembelajaran' => function ($q) {
                $q->orderBy('urutan', 'asc');
            }])
            ->orderBy('urutan', 'asc');

        if (!empty($this->searchBabTp)) {
            $search = '%' . $this->searchBabTp . '%';
            $query->where(function ($q) use ($search) {
                $q->where('nama_lingkup_materi', 'like', $search)
                  ->orWhereHas('tujuanPembelajaran', function ($tq) use ($search) {
                      $tq->where('deskripsi_tp', 'like', $search);
                  });
            });
        }

        $babs = $query->get();

        $template = TemplateDeskripsi::where('mapel_id', $this->kurikulumMapelId)->first();

        $guruList = GuruMapelKelas::where('mapel_id', $this->kurikulumMapelId)
            ->with(['guru.user', 'kelas'])
            ->get();

        return [
            'mapel' => $mapel,
            'babs' => $babs,
            'template' => $template,
            'guruList' => $guruList,
        ];
    }

    public function getAbsensiSiswaDataProperty(): array
    {
        $query = Siswa::with(['user', 'kelas'])
            ->where('siswa.status', 'aktif');

        if ($this->absenKelasId) {
            $query->where('siswa.kelas_id', $this->absenKelasId);
        }

        if (!empty($this->searchSiswaAbsen)) {
            $search = '%' . $this->searchSiswaAbsen . '%';
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('nama', 'like', $search);
                })->orWhere('siswa.nis', 'like', $search);
            });
        }

        $students = $query->join('users', 'siswa.user_id', '=', 'users.id')
            ->orderBy('users.nama', 'asc')
            ->select('siswa.*')
            ->get();

        $absenRecords = AbsensiSiswa::whereDate('tanggal', $this->selectedTanggal)
            ->whereIn('siswa_id', $students->pluck('id'))
            ->get()
            ->keyBy('siswa_id');

        $counts = ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpa' => 0, 'belum' => 0];

        $list = [];
        foreach ($students as $siswa) {
            $absen = $absenRecords[$siswa->id] ?? null;
            $status = $absen ? $absen->status : 'belum';
            if (isset($counts[$status])) {
                $counts[$status]++;
            }

            $list[] = [
                'siswa' => $siswa,
                'status' => $status,
                'catatan' => $absen?->catatan ?? '-',
            ];
        }

        return [
            'list' => $list,
            'counts' => $counts,
            'total' => count($students),
        ];
    }

    public function getAbsensiGuruDataProperty(): array
    {
        $query = Guru::with('user')
            ->where('guru.status_aktif', true);

        if (!empty($this->searchGuruAbsen)) {
            $search = '%' . $this->searchGuruAbsen . '%';
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('nama', 'like', $search);
                })->orWhere('guru.nip', 'like', $search);
            });
        }

        $gurus = $query->join('users', 'guru.user_id', '=', 'users.id')
            ->orderBy('users.nama', 'asc')
            ->select('guru.*')
            ->get();

        $absenRecords = AbsensiGuru::whereDate('tanggal', $this->selectedTanggal)
            ->whereIn('guru_id', $gurus->pluck('id'))
            ->get()
            ->keyBy('guru_id');

        $counts = ['hadir' => 0, 'terlambat' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0, 'belum' => 0];

        $list = [];
        foreach ($gurus as $guru) {
            $absen = $absenRecords[$guru->id] ?? null;
            $status = $absen ? $absen->status : 'belum';
            if (isset($counts[$status])) {
                $counts[$status]++;
            }

            $list[] = [
                'guru' => $guru,
                'waktu_datang' => $absen?->waktu_datang ?? '-',
                'waktu_pulang' => $absen?->waktu_pulang ?? '-',
                'status' => $status,
                'catatan' => $absen?->catatan ?? '-',
            ];
        }

        return [
            'list' => $list,
            'counts' => $counts,
            'total' => count($gurus),
        ];
    }

    public function getProgresGuruProperty()
    {
        $query = GuruMapelKelas::with(['guru.user', 'kelas', 'mapel'])
            ->when($this->selectedSemesterId, function ($q) {
                $q->where('semester_id', $this->selectedSemesterId);
            })
            ->when($this->progresKelasId, function ($q) {
                $q->where('kelas_id', $this->progresKelasId);
            })
            ->when($this->progresMapelId, function ($q) {
                $q->where('mapel_id', $this->progresMapelId);
            })
            ->when($this->searchProgresGuru, function ($q) {
                $search = $this->searchProgresGuru;
                $q->where(function ($sub) use ($search) {
                    $sub->whereHas('guru.user', function ($u) use ($search) {
                        $u->where('nama', 'like', '%' . $search . '%');
                    })->orWhereHas('mapel', function ($m) use ($search) {
                        $m->where('nama_mapel', 'like', '%' . $search . '%');
                    });
                });
            })
            ->get();

        return $query->map(function ($gmk) {
            $totalBab = LingkupMateri::where('mapel_id', $gmk->mapel_id)->count();
            $tpIds = TujuanPembelajaran::whereHas('lingkupMateri', fn($lm) => $lm->where('mapel_id', $gmk->mapel_id))->pluck('id');
            $totalTp = $tpIds->count();

            $totalSiswa = Siswa::where('kelas_id', $gmk->kelas_id)->where('siswa.status', 'aktif')->count();
            
            $gradedSiswaCount = 0;
            if ($totalSiswa > 0) {
                $gradedSiswaCount = Siswa::where('kelas_id', $gmk->kelas_id)
                    ->where('siswa.status', 'aktif')
                    ->where(function ($q) use ($tpIds, $gmk) {
                        if ($tpIds->isNotEmpty()) {
                            $q->whereHas('nilaiSumatifTp', function ($ntp) use ($tpIds) {
                                $ntp->whereIn('tp_id', $tpIds)
                                    ->where('semester_id', $this->selectedSemesterId);
                            });
                        }
                        $q->orWhereHas('nilaiSas', function ($nsas) use ($gmk) {
                            $nsas->where('mapel_id', $gmk->mapel_id)
                                ->where('semester_id', $this->selectedSemesterId);
                        });
                    })->count();
            }

            $persenNilai = $totalSiswa > 0 ? round(($gradedSiswaCount / $totalSiswa) * 100, 1) : 0;

            return [
                'id' => $gmk->id,
                'kelas_id' => $gmk->kelas_id,
                'mapel_id' => $gmk->mapel_id,
                'nama_kelas' => $gmk->kelas->nama_kelas ?? '-',
                'tingkat' => $gmk->kelas->tingkat ?? '-',
                'nama_mapel' => $gmk->mapel->nama_mapel ?? '-',
                'nama_guru' => $gmk->guru->user->nama ?? 'Belum Ditentukan',
                'nip_guru' => $gmk->guru->nip ?? ($gmk->guru->niy ?? '-'),
                'total_bab' => $totalBab,
                'total_tp' => $totalTp,
                'total_siswa' => $totalSiswa,
                'graded_siswa_count' => $gradedSiswaCount,
                'persen_nilai' => $persenNilai,
            ];
        });
    }

    public function render()
    {
        $classes = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $mapels = MataPelajaran::where(function ($q) {
            $q->where('jenis', 'intrakurikuler_umum')->orWhere('jenis', 'umum');
        })->get();
        $semesters = Semester::with('tahunAjaran')->orderByDesc('id')->get();

        return view('livewire.super-admin.monitoring-akademik', [
            'classes' => $classes,
            'mapels' => $mapels,
            'semesters' => $semesters,
            'progresGuruData' => $this->getProgresGuruProperty(),
            'matrixNilai' => $this->getMatrixNilaiProperty(),
            'kurikulumData' => $this->getKurikulumDataProperty(),
            'absenSiswaData' => $this->getAbsensiSiswaDataProperty(),
            'absenGuruData' => $this->getAbsensiGuruDataProperty(),
        ])->layout('components.layouts.app', ['title' => 'Monitoring Akademik']);
    }
}
