<?php

namespace App\Actions\Finance;

use App\Models\Tagihan;
use Illuminate\Support\Facades\DB;

class ReleaseBillsAction
{
    /**
     * Hitung tanggal jatuh tempo otomatis (fix tanggal 10) dengan transisi tahun ajaran
     */
    public function calculateDueDateForMonth(
        string $monthName,
        ?string $baseDueDate = null,
        ?string $tahunAjaranNama = null,
        string $periodeTipe = 'single'
    ): string {
        $monthNumbers = [
            'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
            'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
            'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12,
        ];

        if (!isset($monthNumbers[$monthName])) {
            return date('Y-m-10');
        }

        $targetMonth = $monthNumbers[$monthName];
        $targetDay = 10; // Fix tanggal 10 setiap bulannya

        $year = (int) date('Y');
        if ($tahunAjaranNama && str_contains($tahunAjaranNama, '/')) {
            $parts = explode('/', $tahunAjaranNama);
            $y1 = (int) trim($parts[0]);
            $y2 = (int) trim($parts[1]);

            if ($periodeTipe === 'full_year_jan_des') {
                if (!empty($baseDueDate)) {
                    try {
                        $year = \Carbon\Carbon::parse($baseDueDate)->year;
                    } catch (\Exception $e) {
                        $year = $y2;
                    }
                } else {
                    $year = $y2;
                }
            } else {
                // Bulan Juli - Desember pada tahun pertama ($y1), Januari - Juni pada tahun kedua ($y2)
                $year = ($targetMonth >= 7) ? $y1 : $y2;
            }
        } elseif (!empty($baseDueDate)) {
            try {
                $year = \Carbon\Carbon::parse($baseDueDate)->year;
            } catch (\Exception $e) {
                $year = (int) date('Y');
            }
        }

        return sprintf('%04d-%02d-%02d', $year, $targetMonth, $targetDay);
    }

    /**
     * Rilis tagihan untuk 1 siswa
     */
    public function releaseForStudent(
        int $siswaId,
        int $jenisTagihanId,
        int $tahunAjaranId,
        float $nominal,
        array $targetMonths,
        string $periodeTipe = 'single',
        ?string $tahunAjaranNama = null
    ): array {
        $createdCount = 0;
        $skippedCount = 0;

        DB::transaction(function () use (
            $siswaId,
            $jenisTagihanId,
            $tahunAjaranId,
            $nominal,
            $targetMonths,
            $periodeTipe,
            $tahunAjaranNama,
            &$createdCount,
            &$skippedCount
        ) {
            $status = ($nominal <= 0) ? 'lunas' : 'belum_bayar';

            foreach ($targetMonths as $m) {
                $exists = Tagihan::where('siswa_id', $siswaId)
                    ->where('jenis_tagihan_id', $jenisTagihanId)
                    ->where('tahun_ajaran_id', $tahunAjaranId)
                    ->where('bulan', $m)
                    ->exists();

                if (!$exists) {
                    $dueDate = $this->calculateDueDateForMonth($m, null, $tahunAjaranNama, $periodeTipe);
                    Tagihan::create([
                        'siswa_id' => $siswaId,
                        'jenis_tagihan_id' => $jenisTagihanId,
                        'tahun_ajaran_id' => $tahunAjaranId,
                        'bulan' => $m,
                        'nominal' => $nominal,
                        'total_dibayar' => 0.00,
                        'status' => $status,
                        'jatuh_tempo' => $dueDate,
                    ]);
                    $createdCount++;
                } else {
                    $skippedCount++;
                }
            }
        });

        return [
            'created' => $createdCount,
            'skipped' => $skippedCount,
        ];
    }
}
