<?php

namespace App\Actions\Finance;

use App\Models\Tagihan;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;

class DeleteTagihanAction
{
    /**
     * Reverts associated payments (adjusting student deposit if needed) and deletes the tagihan.
     */
    public function execute(Tagihan $tagihan, ?int $fallbackSiswaId = null): void
    {
        DB::transaction(function () use ($tagihan, $fallbackSiswaId) {
            $siswa = $tagihan->siswa ?: ($fallbackSiswaId ? Siswa::find($fallbackSiswaId) : null);

            // Revert and delete any payments associated with this tagihan
            if ($tagihan->pembayarans && $tagihan->pembayarans->count() > 0) {
                foreach ($tagihan->pembayarans as $pembayaran) {
                    if ($pembayaran->metode_bayar === 'Deposit' && $pembayaran->nominal_dibayar > 0 && $siswa) {
                        $siswa->increment('saldo_deposit', $pembayaran->nominal_dibayar);
                    }
                    if ($pembayaran->kelebihan_bayar > 0 && $siswa) {
                        $siswa->decrement('saldo_deposit', min(floatval($siswa->saldo_deposit), floatval($pembayaran->kelebihan_bayar)));
                    }
                    $pembayaran->delete();
                }
            }

            $tagihan->delete();
        });
    }
}
