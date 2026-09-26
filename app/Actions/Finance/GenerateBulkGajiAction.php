<?php

namespace App\Actions\Finance;

use App\Models\GajiGuru;
use App\Services\SalaryCalculationService;
use Illuminate\Support\Facades\DB;

class GenerateBulkGajiAction
{
    public function __construct(
        protected SalaryCalculationService $calculationService
    ) {}

    /**
     * Generate bulk draft gaji guru untuk item-item terpilih
     *
     * @param array $selectedItems
     * @param string $bulan
     * @param int $tahun
     * @return int Jumlah draft gaji yang berhasil dibuat
     */
    public function execute(array $selectedItems, string $bulan, int $tahun): int
    {
        $createdCount = 0;

        DB::transaction(function () use ($selectedItems, $bulan, $tahun, &$createdCount) {
            $selectedGuruIds = array_filter(array_column($selectedItems, 'guru_id'));
            $existingGuruIds = GajiGuru::whereIn('guru_id', $selectedGuruIds)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->pluck('guru_id')
                ->flip()
                ->toArray();

            foreach ($selectedItems as $item) {
                if (empty($item['guru_id']) || isset($existingGuruIds[$item['guru_id']])) {
                    continue;
                }

                $bruto = $this->calculationService->calculateBruto(
                    floatval($item['gaji_pokok'] ?? 0),
                    floatval($item['gaji_berkala'] ?? 0),
                    floatval($item['honor_ekskul'] ?? 0),
                    floatval($item['insentif'] ?? 0),
                    floatval($item['insentif_bpjs'] ?? 0),
                    floatval($item['insentif_maghrib_mengaji'] ?? 0)
                );

                $potongan = $this->calculationService->calculatePotongan(
                    floatval($item['potongan_sosial'] ?? 0),
                    floatval($item['potongan_peminjaman'] ?? 0),
                    floatval($item['potongan_bpjstk'] ?? 0),
                    floatval($item['potongan_lainnya'] ?? 0)
                );

                $thp = $this->calculationService->calculateNetTakeHomePay($bruto, $potongan);

                try {
                    $salary = GajiGuru::firstOrCreate(
                        [
                            'guru_id' => $item['guru_id'],
                            'bulan' => $bulan,
                            'tahun' => $tahun,
                        ],
                        [
                            'gaji_pokok' => floatval($item['gaji_pokok'] ?? 0),
                            'gaji_berkala' => floatval($item['gaji_berkala'] ?? 0),
                            'jumlah_ekskul' => intval($item['jumlah_ekskul'] ?? 0),
                            'honor_ekskul' => floatval($item['honor_ekskul'] ?? 0),
                            'insentif' => floatval($item['insentif'] ?? 0),
                            'insentif_bpjs' => floatval($item['insentif_bpjs'] ?? 0),
                            'insentif_maghrib_mengaji' => floatval($item['insentif_maghrib_mengaji'] ?? 0),
                            'potongan_sosial' => floatval($item['potongan_sosial'] ?? 0),
                            'potongan_peminjaman' => floatval($item['potongan_peminjaman'] ?? 0),
                            'potongan_bpjstk' => floatval($item['potongan_bpjstk'] ?? 0),
                            'potongan_lainnya' => floatval($item['potongan_lainnya'] ?? 0),
                            'total_bruto' => $bruto,
                            'total_diterima' => $thp,
                            'tanggal_bayar' => now()->toDateString(),
                            'status' => 'draft',
                            'sumber_dana' => $item['sumber_dana'] ?? 'Yayasan',
                            'jam_kerja' => $item['jam_kerja'] ?? '07.00-14.00',
                            'jabatan' => $item['jabatan'] ?? 'Guru',
                        ]
                    );

                    if ($salary->wasRecentlyCreated) {
                        $createdCount++;
                    }
                } catch (\Illuminate\Database\UniqueConstraintViolationException | \Illuminate\Database\QueryException $e) {
                    if ($e->getCode() == 23000 || str_contains($e->getMessage(), '1062 Duplicate entry')) {
                        continue;
                    }
                    throw $e;
                }
            }
        });

        return $createdCount;
    }
}
