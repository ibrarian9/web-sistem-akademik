<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tagihan;
use App\Models\Siswa;
use App\Actions\Finance\AdjustTagihanExcessAction;

class SyncTagihanExcessCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finance:sync-tagihan-excess 
                            {--siswa_id= : ID Siswa tertentu untuk disinkronkan}
                            {--dry-run : Simulasi pengecekan tanpa menyimpan perubahan ke database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi kelebihan bayar tagihan dan saldo deposit siswa yang tidak konsisten akibat perubahan nominal tagihan';

    /**
     * Execute the console command.
     */
    public function handle(AdjustTagihanExcessAction $action): int
    {
        $this->info('Memulai pengecekan konsistensi nominal tagihan, kelebihan bayar, dan saldo deposit siswa...');

        $siswaIdOption = $this->option('siswa_id');
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('[DRY-RUN MODE] Tidak ada perubahan yang akan disimpan ke database.');
        }

        $query = Tagihan::with(['siswa.user', 'pembayarans' => function ($q) {
            $q->where('is_void', false);
        }])
        ->whereHas('pembayarans', function ($q) {
            $q->where('is_void', false);
        });

        if ($siswaIdOption) {
            $query->where('siswa_id', (int) $siswaIdOption);
        }

        $tagihans = $query->get();
        $discrepancies = [];
        $totalAdjusted = 0;

        foreach ($tagihans as $tagihan) {
            $totalPaid = floatval($tagihan->pembayarans->sum('nominal_dibayar'));
            $oldExcess = floatval($tagihan->pembayarans->sum('kelebihan_bayar'));
            $nominal = floatval($tagihan->nominal);
            $expectedExcess = max(0.0, $totalPaid - $nominal);

            if (abs($expectedExcess - $oldExcess) > 0.001) {
                $delta = $expectedExcess - $oldExcess;
                $siswa = $tagihan->siswa;
                $currentDeposit = floatval($siswa?->saldo_deposit ?? 0.0);

                $discrepancies[] = [
                    'tagihan_id' => $tagihan->id,
                    'siswa' => ($siswa?->user?->nama ?? 'Siswa') . ' (ID: ' . ($siswa?->id ?? '-') . ')',
                    'bulan' => $tagihan->bulan ?: '-',
                    'nominal' => 'Rp ' . number_format($nominal, 0, ',', '.'),
                    'total_bayar' => 'Rp ' . number_format($totalPaid, 0, ',', '.'),
                    'kelebihan_lama' => 'Rp ' . number_format($oldExcess, 0, ',', '.'),
                    'kelebihan_baru' => 'Rp ' . number_format($expectedExcess, 0, ',', '.'),
                    'delta_deposit' => ($delta >= 0 ? '+' : '') . 'Rp ' . number_format($delta, 0, ',', '.'),
                    'saldo_deposit_saat_ini' => 'Rp ' . number_format($currentDeposit, 0, ',', '.'),
                ];

                if (!$isDryRun) {
                    $action->execute($tagihan);
                    $totalAdjusted++;
                }
            }
        }

        if (empty($discrepancies)) {
            $this->info('Seluruh data tagihan, kelebihan bayar, dan saldo deposit siswa sudah konsisten dan sinkron.');
            return Command::SUCCESS;
        }

        $this->table(
            ['Tagihan ID', 'Siswa', 'Periode', 'Nominal Tagihan', 'Total Bayar', 'Kelebihan Lama', 'Kelebihan Baru', 'Penyesuaian Deposit', 'Deposit Saat Ini'],
            $discrepancies
        );

        if ($isDryRun) {
            $this->warn("Ditemukan " . count($discrepancies) . " tagihan yang memerlukan sinkronisasi.");
            $this->info("Jalankan tanpa opsi --dry-run untuk menerapkan penyesuaian: php artisan finance:sync-tagihan-excess");
        } else {
            $this->info("Berhasil menyinkronkan {$totalAdjusted} tagihan dan saldo deposit siswa.");
        }

        return Command::SUCCESS;
    }
}
