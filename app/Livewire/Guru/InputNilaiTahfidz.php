<?php

namespace App\Livewire\Guru;

use App\Models\Kelas;
use App\Models\NilaiTahfidz;
use App\Models\Rapor;
use App\Models\RaporTahfidzDetail;
use App\Models\Semester;
use App\Models\Siswa;
use Livewire\Component;

class InputNilaiTahfidz extends Component
{
    public $kelas_id;
    public $semester_id;
    public $search = '';

    public bool $showScoreModal = false;

    public $siswa_id;
    public $surah = 'Al-Baqarah';
    public $juz = 1;

    // Mutaba'ah SD TAHFIZH F3 Fields (All Optional)
    public $materi_tahsin = '';
    public $nilai_tahsin = '';

    public $murajaah_bersama = '';
    public $murajaah_mandiri = '';
    public $nilai_murajaah = '';

    public $materi_kitabah = '';
    public $nilai_kitabah = '';

    public $materi_ziyadah = '';
    public $nilai_ziyadah = '';

    public $catatan_ustadz = '';

    public $editingId = null;

    protected $rules = [
        'siswa_id' => 'required|exists:siswa,id',
        'nilai_tahsin' => 'nullable|numeric|min:0|max:100',
        'nilai_murajaah' => 'nullable|numeric|min:0|max:100',
        'nilai_kitabah' => 'nullable|numeric|min:0|max:100',
        'nilai_ziyadah' => 'nullable|numeric|min:0|max:100',
    ];

    public function mount()
    {
        // Access Guard: Block pure Guru Umum from Input Tahfizh
        $user = auth()->user();
        if ($user && $user->role?->nama === 'guru' && $user->guru) {
            $jenis = strtolower($user->guru->jenis_guru);
            if ($jenis === 'umum') {
                abort(403, 'Akses ditolak. Modul Mutaba\'ah Tahfizh khusus untuk Guru Tahfizh.');
            }
        }

        $activeSemester = Semester::where('status_aktif', true)->first() ?? Semester::first();
        if ($activeSemester) {
            $this->semester_id = $activeSemester->id;
        }

        // Filter classes for logged in Tahfizh Teacher (Halaqah Tahfizh + Academic Classes taught)
        if ($user && $user->role?->nama === 'guru' && $user->guru) {
            $guruId = $user->guru->id;
            $tahfidzKelasIds = Siswa::whereNotNull('kelas_tahfidz_id')
                ->whereHas('kelasTahfidz', function ($q) use ($guruId) {
                    $q->where('guru_tahfidz_id', $guruId);
                })
                ->pluck('kelas_tahfidz_id')
                ->toArray();

            $myClasses = Kelas::where('guru_tahfidz_id', $guruId)
                ->orWhereIn('id', $tahfidzKelasIds)
                ->get();

            if ($myClasses->isNotEmpty()) {
                $this->kelas_id = $myClasses->first()->id;
            } else {
                $this->kelas_id = null;
            }
        } else {
            $kelas = Kelas::whereNotNull('guru_tahfidz_id')->first() ?? Kelas::first();
            if ($kelas) {
                $this->kelas_id = $kelas->id;
            }
        }
    }

    public function openScoreModal($siswaId = null)
    {
        $this->resetScoreForm();

        if ($siswaId) {
            $this->siswa_id = $siswaId;
        } else {
            if ($this->kelas_id) {
                $siswas = Siswa::where('kelas_tahfidz_id', $this->kelas_id)
                    ->orWhere('kelas_id', $this->kelas_id)
                    ->get();
                if ($siswas && $siswas->count() > 0) {
                    $this->siswa_id = $siswas->first()->id;
                }
            }
        }

        $this->showScoreModal = true;
    }

    public function closeScoreModal()
    {
        $this->showScoreModal = false;
        $this->resetScoreForm();
    }

    public function saveScore()
    {
        $this->validate();

        $user = auth()->user();
        if ($user && $user->role?->nama === 'guru' && $user->guru) {
            $guruId = $user->guru->id;
            $targetSiswa = Siswa::find($this->siswa_id);

            $isMyStudent = $targetSiswa && (
                ($targetSiswa->kelasTahfidz && $targetSiswa->kelasTahfidz->guru_tahfidz_id == $guruId) ||
                ($targetSiswa->kelas && $targetSiswa->kelas->guru_tahfidz_id == $guruId) ||
                ($targetSiswa->kelas_tahfidz_id == $this->kelas_id) ||
                ($targetSiswa->kelas_id == $this->kelas_id)
            );

            if (!$isMyStudent) {
                session()->flash('error', 'Akses ditolak. Anda hanya diperbolehkan menginput nilai untuk santri bimbingan Tahfizh Anda.');
                return;
            }
        }

        $tahsinScore = (is_numeric($this->nilai_tahsin) && $this->nilai_tahsin !== '') ? (float)$this->nilai_tahsin : null;
        $murajaahScore = (is_numeric($this->nilai_murajaah) && $this->nilai_murajaah !== '') ? (float)$this->nilai_murajaah : null;
        $kitabahScore = (is_numeric($this->nilai_kitabah) && $this->nilai_kitabah !== '') ? (float)$this->nilai_kitabah : null;
        $ziyadahScore = (is_numeric($this->nilai_ziyadah) && $this->nilai_ziyadah !== '') ? (float)$this->nilai_ziyadah : null;

        $validScores = array_filter([$tahsinScore, $murajaahScore, $kitabahScore, $ziyadahScore], fn($v) => $v !== null);
        $avgScore = count($validScores) > 0 ? array_sum($validScores) / count($validScores) : 85;

        $kelancaran = $murajaahScore ?? ($tahsinScore ?? ($ziyadahScore ?? $avgScore));
        $tajwid = $tahsinScore ?? ($ziyadahScore ?? ($murajaahScore ?? $avgScore));
        $predikat = $avgScore >= 85 ? 'Sangat Baik' : ($avgScore >= 75 ? 'Baik' : 'Cukup');

        $surahText = $this->materi_ziyadah ?: ($this->materi_tahsin ?: 'Al-Baqarah');

        NilaiTahfidz::updateOrCreate(
            [
                'siswa_id' => $this->siswa_id,
                'semester_id' => $this->semester_id,
            ],
            [
                'surah' => $surahText,
                'juz' => $this->juz ?: 1,
                'materi_tahsin' => $this->materi_tahsin ?: null,
                'nilai_tahsin' => $tahsinScore,
                'murajaah_bersama' => $this->murajaah_bersama ?: null,
                'murajaah_mandiri' => $this->murajaah_mandiri ?: null,
                'nilai_murajaah' => $murajaahScore,
                'materi_kitabah' => $this->materi_kitabah ?: null,
                'nilai_kitabah' => $kitabahScore,
                'materi_ziyadah' => $this->materi_ziyadah ?: null,
                'nilai_ziyadah' => $ziyadahScore,
                'nilai_kelancaran' => $kelancaran,
                'nilai_tajwid' => $tajwid,
                'predikat_keagamaan' => $predikat,
                'catatan_ustadz' => $this->catatan_ustadz ?: null,
            ]
        );

        $this->updateRaporTahfidzSummary($this->siswa_id);

        $this->showScoreModal = false;
        $this->resetScoreForm();
        session()->flash('message', 'Catatan Mutaba\'ah Tahfizh berhasil disimpan.');
    }

    public function editScore($id)
    {
        $nt = NilaiTahfidz::findOrFail($id);
        $this->editingId = $nt->id;
        $this->siswa_id = $nt->siswa_id;
        $this->juz = $nt->juz ?: 1;
        $this->surah = $nt->surah ?: '';
        $this->materi_tahsin = $nt->materi_tahsin ?: '';
        $this->nilai_tahsin = $nt->nilai_tahsin !== null ? round($nt->nilai_tahsin) : '';
        $this->murajaah_bersama = $nt->murajaah_bersama ?: '';
        $this->murajaah_mandiri = $nt->murajaah_mandiri ?: '';
        $this->nilai_murajaah = $nt->nilai_murajaah !== null ? round($nt->nilai_murajaah) : '';
        $this->materi_kitabah = $nt->materi_kitabah ?: '';
        $this->nilai_kitabah = $nt->nilai_kitabah !== null ? round($nt->nilai_kitabah) : '';
        $this->materi_ziyadah = $nt->materi_ziyadah ?: '';
        $this->nilai_ziyadah = $nt->nilai_ziyadah !== null ? round($nt->nilai_ziyadah) : '';
        $this->catatan_ustadz = $nt->catatan_ustadz ?: '';
        $this->showScoreModal = true;
    }

    public function deleteScore($id)
    {
        $nt = NilaiTahfidz::find($id);
        if (!$nt) {
            return;
        }
        $siswaId = $nt->siswa_id;
        $nt->delete();

        $this->updateRaporTahfidzSummary($siswaId);
        session()->flash('message', 'Catatan Mutaba\'ah Tahfizh berhasil dihapus.');
    }

    public function resetScoreForm()
    {
        $this->editingId = null;
        $this->materi_tahsin = '';
        $this->nilai_tahsin = '';
        $this->murajaah_bersama = '';
        $this->murajaah_mandiri = '';
        $this->nilai_murajaah = '';
        $this->materi_kitabah = '';
        $this->nilai_kitabah = '';
        $this->materi_ziyadah = '';
        $this->nilai_ziyadah = '';
        $this->catatan_ustadz = '';
    }

    protected function updateRaporTahfidzSummary($siswaId)
    {
        if (!$siswaId || !$this->semester_id) {
            return;
        }

        $siswa = Siswa::find($siswaId);
        if (!$siswa) {
            return;
        }

        $records = NilaiTahfidz::where('siswa_id', $siswaId)
            ->where('semester_id', $this->semester_id)
            ->get();

        if ($records->isEmpty()) {
            return;
        }

        $scores = [];
        $surahList = [];
        $maxJuz = 1;

        foreach ($records as $r) {
            if ($r->nilai_tahsin !== null) $scores[] = (float)$r->nilai_tahsin;
            if ($r->nilai_murajaah !== null) $scores[] = (float)$r->nilai_murajaah;
            if ($r->nilai_kitabah !== null) $scores[] = (float)$r->nilai_kitabah;
            if ($r->nilai_ziyadah !== null) $scores[] = (float)$r->nilai_ziyadah;

            if ($r->surah) $surahList[] = $r->surah;
            if ($r->materi_ziyadah) $surahList[] = $r->materi_ziyadah;
            if ($r->juz > $maxJuz) $maxJuz = (int)$r->juz;
        }

        $rataRata = count($scores) > 0 ? (array_sum($scores) / count($scores)) : 85;
        $predikat = $rataRata >= 85 ? 'Sangat Baik' : ($rataRata >= 75 ? 'Baik' : 'Cukup');
        $surahSummary = implode(', ', array_unique(array_filter($surahList))) ?: 'Al-Baqarah';

        $rapor = Rapor::firstOrCreate(
            [
                'siswa_id' => $siswaId,
                'semester_id' => $this->semester_id,
                'tipe_rapor' => 'tahfizh',
            ],
            [
                'kelas_id' => $siswa->kelas_id,
                'catatan_wali_kelas' => 'Alhamdulillah, tingkatkan hafalan dan terus muraja\'ah.',
                'tanggal_terbit' => date('Y-m-d'),
                'status' => 'draft',
            ]
        );

        RaporTahfidzDetail::updateOrCreate(
            ['rapor_id' => $rapor->id],
            [
                'jumlah_juz' => $maxJuz,
                'surah_terakhir' => $surahSummary,
                'predikat_keagamaan' => $predikat,
            ]
        );
    }

    public function render()
    {
        $user = auth()->user();
        if ($user && $user->role?->nama === 'guru' && $user->guru) {
            $guruId = $user->guru->id;
            $tahfidzKelasIds = Siswa::whereNotNull('kelas_tahfidz_id')
                ->whereHas('kelasTahfidz', function ($q) use ($guruId) {
                    $q->where('guru_tahfidz_id', $guruId);
                })
                ->pluck('kelas_tahfidz_id')
                ->toArray();

            $kelases = Kelas::where('guru_tahfidz_id', $guruId)
                ->orWhereIn('id', $tahfidzKelasIds)
                ->get();
        } else {
            $kelases = Kelas::whereNotNull('guru_tahfidz_id')->get();
            if ($kelases->isEmpty()) {
                $kelases = Kelas::all();
            }
        }

        $semesters = Semester::orderBy('tahun_ajaran_id', 'desc')->get();

        $siswas = collect();
        $scores = collect();

        if ($this->kelas_id) {
            $siswaQuery = Siswa::with(['user', 'kelas', 'kelasTahfidz'])
                ->where(function($sq) {
                    $sq->where('kelas_tahfidz_id', $this->kelas_id)
                      ->orWhere('kelas_id', $this->kelas_id);
                });

            if (!empty($this->search)) {
                $q = $this->search;
                $siswaQuery->where(function ($sq) use ($q) {
                    $sq->where('nisn', 'like', "%{$q}%")
                      ->orWhere('nama_panggilan', 'like', "%{$q}%")
                      ->orWhereHas('user', function ($uq) use ($q) {
                          $uq->where('nama', 'like', "%{$q}%");
                      });
                });
            }
            $siswas = $siswaQuery->get();

            if ($this->semester_id) {
                $scores = NilaiTahfidz::whereIn('siswa_id', $siswas->pluck('id'))
                    ->where('semester_id', $this->semester_id)
                    ->get()
                    ->keyBy('siswa_id');
            }
        }

        return view('livewire.guru.input-nilai-tahfidz', [
            'kelases' => $kelases,
            'semesters' => $semesters,
            'siswas' => $siswas,
            'scores' => $scores,
        ]);
    }
}
