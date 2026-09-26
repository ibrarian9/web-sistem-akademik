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

    /**
     * Build preview items for bulk salary draft generation for active teachers.
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

        $items = [];

        foreach ($activeGurus as $guru) {
            if (isset($existingGajiGuruIds[$guru->id])) {
                continue;
            }

            $isTetap = in_array(strtolower($guru->status_kepegawaian ?? ''), ['tetap_yayasan', 'gty', 'pns']);
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

            $potonganPeminjaman = $loanDeductions[$guru->id] ?? 0.00;

            $totalBruto = $this->calculationService->calculateBruto($gajiPokok, $gajiBerkala, $honorEkskul, $insentif, $insentifBpjs, $insentifMaghrib);
            $totalPotongan = $this->calculationService->calculatePotongan($potonganSosial, $potonganPeminjaman, $potonganBpjstk, $potonganLainnya);
            $totalDiterima = $this->calculationService->calculateNetTakeHomePay($totalBruto, $totalPotongan);

            $jabatan = $guru->jabatan ?: ($guru->jenis_guru === 'tahfidz' ? 'Wali Tahfizh' : 'Guru Pengajar');
            $jamKerja = $isTetap ? '07.00-14.00 (Fleksibel)' : '07.00-14.00';

            $items[$guru->id] = [
                'selected' => true,
                'guru_id' => $guru->id,
                'nama' => $guru->user->nama ?? '-',
                'nip' => $guru->niy ?? ($guru->nip ?? '-'),
                'jabatan' => $jabatan,
                'jam_kerja' => $jamKerja,
                'sumber_dana' => 'Yayasan',
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
