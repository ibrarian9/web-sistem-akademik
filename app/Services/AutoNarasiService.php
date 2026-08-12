<?php

namespace App\Services;

use App\Models\LingkupMateri;
use App\Models\MataPelajaran;
use App\Models\NilaiSas;
use App\Models\NilaiSumatifTp;
use App\Models\NilaiTahfidz;
use App\Models\RaporTahfidzDetail;
use App\Models\Siswa;
use App\Models\TemplateDeskripsi;

class AutoNarasiService
{
    /**
     * Calculate final grade and generate auto-narrative description for Kurikulum Merdeka & Tahfizh Core Curriculum.
     */
    public function generateForMapel(int $siswaId, int $mapelId, int $semesterId): array
    {
        $siswa = Siswa::find($siswaId);
        $nama = $siswa ? ($siswa->nama_panggilan ?? $siswa->user->nama ?? 'Siswa') : 'Siswa';
        $mapel = MataPelajaran::find($mapelId);

        // Special handling if Mapel is Tahfizh / Tahfidz (Core Curriculum of SD Tahfizh)
        if ($mapel && (strtolower($mapel->jenis) === 'tahfidz' || str_contains(strtolower($mapel->nama_mapel), 'tahfi'))) {
            $tahfidzNarasi = $this->generateForTahfidz($siswaId, $semesterId);
            return [
                'nilai_akhir' => max(85, $tahfidzNarasi['avg_kelancaran']),
                'predikat' => substr($tahfidzNarasi['predikat_tahfidz'], 0, 1) ?: 'A',
                'deskripsi_tertinggi' => 'menunjukkan kelancaran setoran mutqin pada surah ' . $tahfidzNarasi['daftar_surah_lulus'],
                'deskripsi_terendah' => 'membutuhkan penguatan muraja\'ah mandiri di rumah',
                'narasi_capaian_full' => $tahfidzNarasi['narasi_tahfidz_full'],
            ];
        }

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

    /**
     * Generate specialized auto-narrative description for Tahfizh Al-Qur'an (Core Curriculum).
     */
    public function generateForTahfidz(int $siswaId, int $semesterId): array
    {
        $siswa = Siswa::find($siswaId);
        $nama = $siswa ? ($siswa->nama_panggilan ?? $siswa->user->nama ?? 'Santri') : 'Santri';

        $tahfidzDetail = RaporTahfidzDetail::whereHas('rapor', function ($q) use ($siswaId, $semesterId) {
            $q->where('siswa_id', $siswaId)->where('semester_id', $semesterId);
        })->first();

        $nilaiTahfidzList = NilaiTahfidz::where('siswa_id', $siswaId)
            ->where('semester_id', $semesterId)
            ->get();

        $totalJuz = $tahfidzDetail->total_juz_dihafal ?? ($nilaiTahfidzList->max('juz') ?: 1);
        $surahLulus = $tahfidzDetail->daftar_surah_lulus ?? ($nilaiTahfidzList->pluck('surah')->filter()->implode(', ') ?: 'Juz 30');
        $predikat = $tahfidzDetail->predikat_tahfidz ?? ($nilaiTahfidzList->first()->predikat_keagamaan ?? 'Sangat Baik');

        $avgTajwid = $nilaiTahfidzList->avg('nilai_tajwid') ?? 88;
        $avgKelancaran = $nilaiTahfidzList->avg('nilai_kelancaran') ?? 90;

        $evalTajwid = $avgTajwid >= 90 ? 'sangat fasih dan makhraj tepat' : ($avgTajwid >= 80 ? 'lancar sesuai kaidah tajwid' : 'perlu pembimbingan makhraj');
        $evalKelancaran = $avgKelancaran >= 90 ? 'sangat mutqin (lancar tanpa rintangan)' : 'lancar dalam hafalan';

        $narasiFull = "Alhamdulillah, Ananda {$nama} telah menyelesaikan hafalan {$totalJuz} Juz ({$surahLulus}) dengan predikat {$predikat}. Setoran hafalan {$evalKelancaran} serta kualitas tajwid {$evalTajwid}.";

        return [
            'total_juz_dihafal' => $totalJuz,
            'daftar_surah_lulus' => $surahLulus,
            'predikat_tahfidz' => $predikat,
            'avg_tajwid' => round($avgTajwid, 1),
            'avg_kelancaran' => round($avgKelancaran, 1),
            'narasi_tahfidz_full' => $narasiFull,
        ];
    }
}
