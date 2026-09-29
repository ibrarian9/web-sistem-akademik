<?php

namespace App\Services\Finance;

use App\Actions\Finance\GenerateBulkGajiAction;
use App\Models\GajiGuru;
use App\Models\Guru;
use App\Services\SalaryCalculationService;

class SalaryBulkGeneratorService
{
    public function __construct(
        protected SalaryCalculationService $calculationService,
        protected GenerateBulkGajiAction $generateAction
    ) {}

    public const LIST_BULAN = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    /**
     * Resolve the most recent previous salary for each teacher prior to the target period.
     *
     * @param array<int> $guruIds
     * @param string $bulan
     * @param int $tahun
     * @return array<int, GajiGuru|null> [guru_id => GajiGuru|null]
     */
    public function resolvePreviousSalaries(array $guruIds, string $bulan, int $tahun): array
    {
        if (empty($guruIds)) {
            return [];
        }

        $bulanMap = array_flip(self::LIST_BULAN);
        $targetMonthIndex = $bulanMap[$bulan] ?? (intval(date('n')) - 1);
        $targetPeriodScore = ($tahun * 12) + $targetMonthIndex;

        // Determine exact previous month
        if ($targetMonthIndex > 0) {
            $prevBulan = self::LIST_BULAN[$targetMonthIndex - 1];
            $prevTahun = $tahun;
        } else {
            $prevBulan = 'Desember';
            $prevTahun = $tahun - 1;
        }

        // Single batch query for historical salaries up to current year
        $historicalRecords = GajiGuru::whereIn('guru_id', $guruIds)
            ->where('tahun', '<=', $tahun)
            ->get()
            ->groupBy('guru_id');

        $result = [];

        foreach ($guruIds as $guruId) {
            if (!isset($historicalRecords[$guruId])) {
                $result[$guruId] = null;
                continue;
            }

            $teacherSalaries = $historicalRecords[$guruId];

            // 1. Try to find the exact preceding month
            $exactSalary = $teacherSalaries->first(function ($r) use ($prevBulan, $prevTahun) {
                return $r->bulan === $prevBulan && intval($r->tahun) === intval($prevTahun);
            });

            if ($exactSalary) {
                $result[$guruId] = $exactSalary;
                continue;
            }

            // 2. Fall back to the most recent record strictly prior to the target period
            $priorSalary = $teacherSalaries
                ->filter(function ($r) use ($bulanMap, $targetPeriodScore) {
                    $mIdx = $bulanMap[$r->bulan] ?? -1;
                    if ($mIdx === -1) {
                        return false;
                    }
                    return (intval($r->tahun) * 12 + $mIdx) < $targetPeriodScore;
                })
                ->sortByDesc(function ($r) use ($bulanMap) {
                    return (intval($r->tahun) * 12 + ($bulanMap[$r->bulan] ?? 0));
                })
                ->first();

            $result[$guruId] = $priorSalary;
        }

        return $result;
    }

    /**
     * Build preview items for bulk salary draft generation for active teachers.
     * Prefills values from the teacher's previous month's salary (or most recent prior salary),
     * and falls back to status-based defaults if no previous record exists.
     *
     * @return array<int, array>
     */
    public function buildPreviewItems(string $bulan, int $tahun): array
    {
        $activeGurus = Guru::with('user')->where('status_aktif', true)->get();
        if ($activeGurus->isEmpty()) {
            return [];
        }

        $guruIds = $activeGurus->pluck('id')->toArray();

        // 1. Batch query cek keberadaan draf gaji pada bulan & tahun terpilih
        $existingGajiGuruIds = GajiGuru::whereIn('guru_id', $guruIds)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->pluck('guru_id')
            ->flip()
            ->toArray();

        // 2. Batch query seluruh kasbon/pinjaman aktif untuk semua guru
        $loanDeductions = $this->calculationService->resolveActiveLoanDeductions($guruIds);

        // 3. Batch query data gaji periode sebelumnya untuk seluruh guru
        $previousSalaries = $this->resolvePreviousSalaries($guruIds, $bulan, $tahun);

        $items = [];

        foreach ($activeGurus as $guru) {
            if (isset($existingGajiGuruIds[$guru->id])) {
                continue;
            }

            $isTetap = in_array(strtolower($guru->status_kepegawaian ?? ''), ['tetap_yayasan', 'gty', 'pns']);
            $prev = $previousSalaries[$guru->id] ?? null;

            if ($prev) {
                $jabatan = $prev->jabatan ?: ($guru->jabatan ?: ($guru->jenis_guru === 'tahfidz' ? 'Wali Tahfizh' : 'Guru Pengajar'));
                $jamKerja = $prev->jam_kerja ?: ($isTetap ? '07.00-14.00 (Fleksibel)' : '07.00-14.00');
                $sumberDana = $prev->sumber_dana ?: 'Yayasan';

                $gajiPokok = floatval($prev->gaji_pokok);
                $gajiBerkala = floatval($prev->gaji_berkala);
                $jumlahEkskul = intval($prev->jumlah_ekskul);
                $honorEkskul = floatval($prev->honor_ekskul);
                $insentif = floatval($prev->insentif);
                $insentifBpjs = floatval($prev->insentif_bpjs);
                $insentifMaghrib = floatval($prev->insentif_maghrib_mengaji);

                $potonganSosial = floatval($prev->potongan_sosial ?: 10000.00);
                $potonganBpjstk = floatval($prev->potongan_bpjstk);
                $potonganLainnya = floatval($prev->potongan_lainnya);
                $hasPreviousSalary = true;
            } else {
                $jabatan = $guru->jabatan ?: ($guru->jenis_guru === 'tahfidz' ? 'Wali Tahfizh' : 'Guru Pengajar');
                $jamKerja = $isTetap ? '07.00-14.00 (Fleksibel)' : '07.00-14.00';
                $sumberDana = 'Yayasan';

                $gajiPokok = $isTetap ? 2000000.00 : 1000000.00;
                $gajiBerkala = $isTetap ? 120000.00 : 0.00;
                $jumlahEkskul = 0;
                $honorEkskul = 0.00;
                $insentif = $isTetap ? 500000.00 : 150000.00;
                $insentifBpjs = $isTetap ? 17928.00 : 0.00;
                $insentifMaghrib = 0.00;

                $potonganSosial = 10000.00;
                $potonganBpjstk = $isTetap ? 17928.00 : 0.00;
                $potonganLainnya = 0.00;
                $hasPreviousSalary = false;
            }

            // Loan deduction is ALWAYS dynamically resolved from active loan balance
            $potonganPeminjaman = $loanDeductions[$guru->id] ?? 0.00;

            $totalBruto = $this->calculationService->calculateBruto($gajiPokok, $gajiBerkala, $honorEkskul, $insentif, $insentifBpjs, $insentifMaghrib);
            $totalPotongan = $this->calculationService->calculatePotongan($potonganSosial, $potonganPeminjaman, $potonganBpjstk, $potonganLainnya);
            $totalDiterima = $this->calculationService->calculateNetTakeHomePay($totalBruto, $totalPotongan);

            $items[$guru->id] = [
                'selected' => true,
                'guru_id' => $guru->id,
                'nama' => $guru->user->nama ?? '-',
                'nip' => $guru->niy ?? ($guru->nip ?? '-'),
                'jabatan' => $jabatan,
                'jam_kerja' => $jamKerja,
                'sumber_dana' => $sumberDana,
                'gaji_pokok' => floatval($gajiPokok),
                'gaji_berkala' => floatval($gajiBerkala),
                'jumlah_ekskul' => intval($jumlahEkskul),
                'honor_ekskul' => floatval($honorEkskul),
                'insentif' => floatval($insentif),
                'insentif_bpjs' => floatval($insentifBpjs),
                'insentif_maghrib_mengaji' => floatval($insentifMaghrib),
                'potongan_sosial' => floatval($potonganSosial),
                'potongan_peminjaman' => floatval($potonganPeminjaman),
                'potongan_bpjstk' => floatval($potonganBpjstk),
                'potongan_lainnya' => floatval($potonganLainnya),
                'total_bruto' => floatval($totalBruto),
                'total_diterima' => floatval($totalDiterima),
                'has_previous_salary' => $hasPreviousSalary,
            ];
        }

        return $items;
    }

    /**
     * Recalculate totals for a single preview item row.
     */
    public function recalculateRow(array $item): array
    {
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

        $item['total_bruto'] = $bruto;
        $item['total_diterima'] = $this->calculationService->calculateNetTakeHomePay($bruto, $potongan);

        return $item;
    }

    /**
     * Generate bulk draft records.
     */
    public function generateDrafts(array $selectedItems, string $bulan, int $tahun): int
    {
        return $this->generateAction->execute($selectedItems, $bulan, $tahun);
    }
}
