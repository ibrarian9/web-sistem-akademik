<?php

namespace App\Actions\Finance;

use App\Models\Tagihan;
use App\Models\Siswa;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\DB;
use App\Services\AuditLogger;

class AdjustTagihanExcessAction
{
    /**
     * Recalculates payments' kelebihan_bayar and synchronizes the student's saldo_deposit
     * when a tagihan's nominal is updated.
     *
     * @param Tagihan $tagihan The updated tagihan model
     * @param float|null $oldNominal Optional old nominal for audit logging
     * @return array Summary of adjustments made
     */
    public function execute(Tagihan $tagihan, ?float $oldNominal = null): array
    {
        return DB::transaction(function () use ($tagihan, $oldNominal) {
            $siswa = $tagihan->siswa;
            if (!$siswa) {
                return ['adjusted' => false, 'reason' => 'No student associated'];
            }

            $pembayarans = $tagihan->pembayarans()
                ->where('is_void', false)
                ->orderBy('tanggal_bayar', 'asc')
                ->orderBy('id', 'asc')
                ->lockForUpdate()
                ->get();

            if ($pembayarans->isEmpty()) {
                return ['adjusted' => false, 'reason' => 'No payments found'];
            }

            $currentNominal = floatval($tagihan->nominal);
            $totalPaid = floatval($pembayarans->sum('nominal_dibayar'));
            $oldRecordedExcess = floatval($pembayarans->sum('kelebihan_bayar'));
            $newExpectedTotalExcess = max(0.0, $totalPaid - $currentNominal);

            $deltaExcess = $newExpectedTotalExcess - $oldRecordedExcess;

            // Recalculate each payment's kelebihan_bayar sequentially
            $runningPaid = 0.0;
            foreach ($pembayarans as $p) {
                $pNominal = floatval($p->nominal_dibayar);
                $prevRunning = $runningPaid;
                $runningPaid += $pNominal;

                if ($runningPaid <= $currentNominal) {
                    $pExcess = 0.0;
                } elseif ($prevRunning >= $currentNominal) {
                    $pExcess = $pNominal;
                } else {
                    $pExcess = $runningPaid - $currentNominal;
                }

                if (abs(floatval($p->kelebihan_bayar) - $pExcess) > 0.001) {
                    $p->update(['kelebihan_bayar' => $pExcess]);
                }
            }

            // Adjust student's saldo_deposit by deltaExcess
            $currentDeposit = floatval($siswa->saldo_deposit);
            $depositChange = 0.0;

            if ($deltaExcess > 0) {
                // Nominal decreased -> excess increased -> credit to student deposit
                $siswa->increment('saldo_deposit', $deltaExcess);
                $depositChange = $deltaExcess;
            } elseif ($deltaExcess < 0) {
                // Nominal increased -> excess decreased -> debit from student deposit
                $absDelta = abs($deltaExcess);
                $deduct = min($currentDeposit, $absDelta);
                if ($deduct > 0) {
                    $siswa->decrement('saldo_deposit', $deduct);
                }
                $depositChange = -$deduct;
            }

            // Ensure tagihan total_dibayar and status are synchronized
            $status = 'belum_bayar';
            if ($currentNominal <= 0 || $totalPaid >= $currentNominal) {
                $status = 'lunas';
            } elseif ($totalPaid > 0) {
                $status = 'sebagian';
            }

            $tagihan->update([
                'total_dibayar' => $totalPaid,
                'status' => $status,
            ]);

            // Audit log if there was an actual change in excess or deposit
            if (abs($deltaExcess) > 0.001) {
                AuditLogger::log(
                    'updated',
                    "Sinkronisasi Kelebihan Bayar Tagihan ID: {$tagihan->id} ({$tagihan->bulan}) - Siswa: " . ($siswa->user->nama ?? 'Siswa') . 
                    " | Perubahan Deposit: Rp " . number_format($depositChange, 0, ',', '.') . 
                    " | Saldo Deposit Baru: Rp " . number_format($siswa->fresh()->saldo_deposit, 0, ',', '.'),
                    $tagihan,
                    ['log_name' => 'keuangan']
                );
            }

            return [
                'adjusted' => true,
                'tagihan_id' => $tagihan->id,
                'siswa_id' => $siswa->id,
                'siswa_nama' => $siswa->user->nama ?? 'Siswa',
                'current_nominal' => $currentNominal,
                'total_paid' => $totalPaid,
                'old_excess' => $oldRecordedExcess,
                'new_excess' => $newExpectedTotalExcess,
                'delta_excess' => $deltaExcess,
                'deposit_change' => $depositChange,
                'remaining_deposit' => floatval($siswa->fresh()->saldo_deposit),
            ];
        });
    }
}
