<?php

namespace App\Services;

use App\Models\LingkupMateri;
use App\Models\NilaiSas;
use App\Models\NilaiSumatifTp;
use App\Models\Siswa;
use App\Models\TemplateDeskripsi;
use App\Models\TujuanPembelajaran;
use Illuminate\Support\Facades\DB;

class AutoNarasiService
{
    /**
     * Calculate final grade and generate auto-narrative description for Kurikulum Merdeka.
     */
    public function generateForMapel(int $siswaId, int $mapelId, int $semesterId): array
    {
        $siswa = Siswa::find($siswaId);
        $nama = $siswa ? ($siswa->nama_panggilan ?? $siswa->user->nama ?? 'Siswa') : 'Siswa';

        // 1. Get Lingkup Materi & TPs
        $lingkupMateris = LingkupMateri::where('mapel_id', $mapelId)
            ->with(['tujuanPembelajaran'])
            ->orderBy('urutan', 'asc')
            ->get();

        $lingkupAverages = [];
        $allTpScores = [];

        foreach ($lingkupMateris as $lm) {
            $tpIds = $lm->tujuanPembelajaran->pluck('id')->toArray();
            if (empty($tpIds)) {
                continue;
            }

            $scores = NilaiSumatifTp::where('siswa_id', $siswaId)
                ->where('semester_id', $semesterId)
                ->whereIn('tp_id', $tpIds)
                ->get();

            if ($scores->isNotEmpty()) {
                $avg = $scores->avg('nilai');
                $lingkupAverages[] = $avg;

                foreach ($scores as $s) {
                    $tp = $lm->tujuanPembelajaran->firstWhere('id', $s->tp_id);
                    if ($tp) {
                        $allTpScores[] = [
                            'tp' => $tp,
                            'score' => (float) $s->nilai,
                            'urutan' => $tp->urutan,
                        ];
                    }
                }
            }
        }

        // 2. Fetch Nilai SAS
        $nilaiSasRecord = NilaiSas::where('siswa_id', $siswaId)
            ->where('mapel_id', $mapelId)
            ->where('semester_id', $semesterId)
            ->first();

        $nilaiSas = $nilaiSasRecord ? (float) $nilaiSasRecord->nilai : null;

        // 3. Calculate Nilai Akhir
        $components = $lingkupAverages;
        if ($nilaiSas !== null) {
            $components[] = $nilaiSas;
        }

        $nilaiAkhir = count($components) > 0 ? array_sum($components) / count($components) : 0;
        $nilaiAkhirFormatted = round($nilaiAkhir, 2);

        // Determine Predikat
        $predikat = 'D';
        if ($nilaiAkhirFormatted >= 85) {
            $predikat = 'A';
        } elseif ($nilaiAkhirFormatted >= 75) {
            $predikat = 'B';
        } elseif ($nilaiAkhirFormatted >= 65) {
            $predikat = 'C';
        }

        // 4. Auto-Narasi Highest & Lowest TP Score
        $template = TemplateDeskripsi::where('mapel_id', $mapelId)->first();
        $frasaHighest = $template ? $template->frasa_tertinggi : 'menunjukkan penguasaan dalam';
        $frasaLowest = $template ? $template->frasa_terendah : 'membutuhkan penguatan dalam';

        $deskripsiHighest = '';
        $deskripsiLowest = '';
        $narasiFull = '';

        if (!empty($allTpScores)) {
            // Sort to find highest (max score, min urutan)
            usort($allTpScores, function ($a, $b) {
                if ($a['score'] == $b['score']) {
                    return $a['urutan'] <=> $b['urutan'];
                }
                return $b['score'] <=> $a['score'];
            });

            $highest = $allTpScores[0];

            // Sort to find lowest (min score, min urutan)
            usort($allTpScores, function ($a, $b) {
                if ($a['score'] == $b['score']) {
                    return $a['urutan'] <=> $b['urutan'];
                }
                return $a['score'] <=> $b['score'];
            });

            $lowest = $allTpScores[0];

            $deskripsiHighest = trim($frasaHighest) . ' ' . $highest['tp']->deskripsi_tp;
            $deskripsiLowest = trim($frasaLowest) . ' ' . $lowest['tp']->deskripsi_tp;

            $narasiFull = "Ananda {$nama} {$deskripsiHighest}. Ananda {$nama} {$deskripsiLowest}.";
        }

        return [
            'nilai_akhir' => $nilaiAkhirFormatted,
            'predikat' => $predikat,
            'deskripsi_tertinggi' => $deskripsiHighest,
            'deskripsi_terendah' => $deskripsiLowest,
            'narasi_capaian_full' => $narasiFull,
        ];
    }
}
