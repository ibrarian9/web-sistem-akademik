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
            $existingGuruMap = GajiGuru::withTrashed()
                ->whereIn('guru_id', $selectedGuruIds)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->get()
                ->keyBy('guru_id');

            foreach ($selectedItems as $item) {
                if (empty($item['guru_id'])) {
                    continue;
                }

                $existing = $existingGuruMap->get($item['guru_id']);
                if ($existing && !$existing->trashed()) {
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

                $payload = [
                    'guru_id' => $item['guru_id'],
                    'bulan' => $bulan,
                    'tahun' => $tahun,
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
                ];

                try {
                    if ($existing && $existing->trashed()) {
                        $existing->restore();
                        $existing->update($payload);
                        $createdCount++;
                    } else {
                        GajiGuru::create($payload);
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
