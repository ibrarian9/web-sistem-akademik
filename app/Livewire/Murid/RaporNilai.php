<?php

namespace App\Livewire\Murid;

use App\Models\LingkupMateri;
use App\Models\MataPelajaran;
use App\Models\NilaiP5;
use App\Models\NilaiSas;
use App\Models\NilaiSumatifTp;
use App\Models\Rapor;
use App\Models\Tagihan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class RaporNilai extends Component
{
    public bool $hasOutstanding = false;
    public array $rekapRaporUtama = [];
    public array $nilaiPerBab = [];
    public array $nilaiP5 = [];
    public string $activeTab = 'rekap'; // 'rekap' (Rekap Nilai Rapor), 'bab' (Nilai per-Bab), 'p5' (Kokurikuler P5)

    public function mount()
    {
        $this->checkOutstandingAndLoad();
    }

    public function checkOutstandingAndLoad()
    {
        $siswa = auth()->user()->siswa;
        if (!$siswa) {
            return;
        }

        // Check for unpaid/partially paid blocking bills past due date
        $this->hasOutstanding = Tagihan::where('siswa_id', $siswa->id)
            ->whereIn('status', ['belum_bayar', 'sebagian'])
            ->whereHas('jenisTagihan', function ($q) {
                $q->where('is_blocking', true);
            })
            ->whereDate('jatuh_tempo', '<=', Carbon::today())
            ->exists();

        if ($this->hasOutstanding) {
            return;
        }

        // Get active semester
        $activeSemester = DB::table('semester')
            ->join('tahun_ajaran', 'semester.tahun_ajaran_id', '=', 'tahun_ajaran.id')
            ->where('tahun_ajaran.status_aktif', true)
            ->where('semester.status_aktif', true)
            ->select('semester.id')
            ->first();

        if (!$activeSemester) {
            return;
        }

        $mapels = MataPelajaran::where(function ($q) {
            $q->where('jenis', 'intrakurikuler_umum')->orWhere('jenis', 'umum');
        })->get();

        // 1. Load Nilai per-Bab (Lingkup Materi Kurikulum Merdeka)
        // Siswa hanya melihat skor per-Bab, rincian per-TP hanya untuk Guru
        $sumatifTps = NilaiSumatifTp::where('siswa_id', $siswa->id)
            ->where('semester_id', $activeSemester->id)
            ->get();

        $lingkupMateris = LingkupMateri::whereIn('mapel_id', $mapels->pluck('id'))
            ->with(['tujuanPembelajaran'])
            ->orderBy('mapel_id')
            ->orderBy('urutan', 'asc')
            ->get();

        $groupedBab = [];
        $mapelBabAverages = [];

        foreach ($mapels as $mapel) {
            $babsForMapel = $lingkupMateris->where('mapel_id', $mapel->id);
            if ($babsForMapel->isEmpty()) {
                continue;
            }

            $babList = [];
            $validBabScores = [];

            foreach ($babsForMapel as $lm) {
                $tpIds = $lm->tujuanPembelajaran->pluck('id')->toArray();
                $scores = !empty($tpIds) ? $sumatifTps->whereIn('tp_id', $tpIds) : collect();

                $avgScore = $scores->isNotEmpty() ? round($scores->avg('nilai'), 1) : null;
                if ($avgScore !== null) {
                    $validBabScores[] = $avgScore;
                }

                $predikatBab = '-';
                if ($avgScore !== null) {
                    if ($avgScore >= 90) $predikatBab = 'A';
                    elseif ($avgScore >= 80) $predikatBab = 'B';
                    elseif ($avgScore >= 70) $predikatBab = 'C';
                    else $predikatBab = 'D';
                }

                $babList[] = [
                    'urutan' => $lm->urutan ?? (count($babList) + 1),
                    'judul' => $lm->nama_lingkup_materi ?? $lm->judul_lingkup_materi,
                    'nilai' => $avgScore,
                    'predikat' => $predikatBab,
                ];
            }

            $groupedBab[$mapel->id] = [
                'nama_mapel' => $mapel->nama_mapel,
                'babs' => $babList,
                'avg_mapel' => count($validBabScores) > 0 ? round(array_sum($validBabScores) / count($validBabScores), 1) : null,
            ];

            $mapelBabAverages[$mapel->id] = count($validBabScores) > 0 ? round(array_sum($validBabScores) / count($validBabScores), 1) : null;
        }

        $this->nilaiPerBab = $groupedBab;

        // 2. Load Rekap Nilai Rapor Utama (Rata-rata Bab, SAS, Nilai Akhir, Predikat)
        $rapor = Rapor::where('siswa_id', $siswa->id)
            ->where('semester_id', $activeSemester->id)
            ->with(['details'])
            ->first();

        $sasRecords = NilaiSas::where('siswa_id', $siswa->id)
            ->where('semester_id', $activeSemester->id)
            ->get()
            ->keyBy('mapel_id');

        $rekapUtama = [];
        foreach ($mapels as $mapel) {
            $sas = $sasRecords[$mapel->id] ?? null;
            $detail = $rapor?->details?->firstWhere('mapel_id', $mapel->id);

            $avgBab = $mapelBabAverages[$mapel->id] ?? null;
            $sasVal = $sas?->nilai_sas !== null ? floatval($sas->nilai_sas) : ($sas?->nilai !== null ? floatval($sas->nilai) : null);
            $nilaiAkhir = $detail?->nilai_akhir !== null ? floatval($detail->nilai_akhir) : null;
            $predikat = $detail?->predikat ?? '-';

            $rekapUtama[] = [
                'nama_mapel' => $mapel->nama_mapel,
                'avg_bab' => $avgBab,
                'nilai_sas' => $sasVal,
                'nilai_akhir' => $nilaiAkhir,
                'predikat' => $predikat,
            ];
        }

        $this->rekapRaporUtama = $rekapUtama;

        // 3. Load Capaian Kokurikuler P5
        $scoresP5 = NilaiP5::where('siswa_id', $siswa->id)
            ->where('semester_id', $activeSemester->id)
            ->with(['proyek', 'subdimensiP5.dimensiP5'])
            ->get();

        $groupedP5 = [];
        foreach ($scoresP5 as $sc) {
            $pName = $sc->proyek->nama_proyek ?? 'Projek P5';
            $dName = $sc->subdimensiP5->dimensiP5->nama_dimensi ?? 'Dimensi';
            $sName = $sc->subdimensiP5->nama_subdimensi ?? 'Sub-Dimensi';
            $val = intval($sc->nilai);
            $ratingLabel = match($val) {
                1 => 'BB (Belum Berkembang)',
                2 => 'MB (Mulai Berkembang)',
                3 => 'BSH (Berkembang Sesuai Harapan)',
                4 => 'SB (Sangat Berkembang)',
                default => '-'
            };

            $groupedP5[$pName][] = [
                'dimensi' => $dName,
                'subdimensi' => $sName,
                'nilai' => $val,
                'label' => $ratingLabel,
            ];
        }
        $this->nilaiP5 = $groupedP5;
    }

    public function setTab($tab)
    {
        if (in_array($tab, ['rekap', 'bab', 'p5'])) {
            $this->activeTab = $tab;
        }
    }

    public function render()
    {
        return view('livewire.murid.rapor-nilai')
            ->layout('components.layouts.app', ['title' => 'Nilai Akademik Murid']);
    }
}
