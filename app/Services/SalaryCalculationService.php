<?php

namespace App\Services;

use App\Models\Peminjaman;

class SalaryCalculationService
{
    /**
     * Hitung total penghasilan kotor (Bruto)
     */
    public function calculateBruto(
        float $gajiPokok = 0.0,
        float $gajiBerkala = 0.0,
        float $honorEkskul = 0.0,
        float $insentif = 0.0,
        float $insentifBpjs = 0.0,
        float $insentifMaghrib = 0.0
    ): float {
        return floatval($gajiPokok)
            + floatval($gajiBerkala)
            + floatval($honorEkskul)
            + floatval($insentif)
            + floatval($insentifBpjs)
            + floatval($insentifMaghrib);
    }

    /**
     * Hitung total potongan gaji
     */
    public function calculatePotongan(
        float $potonganSosial = 0.0,
        float $potonganPinjaman = 0.0,
        float $potonganBpjstk = 0.0,
        float $potonganLainnya = 0.0
    ): float {
        return floatval($potonganSosial)
            + floatval($potonganPinjaman)
            + floatval($potonganBpjstk)
            + floatval($potonganLainnya);
    }

    /**
     * Hitung gaji bersih yang diterima (Take Home Pay)
     */
    public function calculateNetTakeHomePay(float $totalBruto, float $totalPotongan): float
    {
        return max(0.0, $totalBruto - $totalPotongan);
    }

    /**
     * Dapatkan nominal potongan cicilan pinjaman/kasbon aktif guru
     */
    public function resolveActiveLoanDeduction(int $guruId): float
    {
        $activeLoan = Peminjaman::where('guru_id', $guruId)
            ->where('status', 'berjalan')
            ->where('sisa_pinjaman', '>', 0)
            ->first();

        if (!$activeLoan) {
            return 0.00;
        }

        return min(floatval($activeLoan->cicilan_per_bulan), floatval($activeLoan->sisa_pinjaman));
    }
}
