<?php

namespace App\Actions\Finance;

use App\Models\GajiGuru;
use App\Models\Peminjaman;
use App\Models\Pengeluaran;
use App\Models\KategoriPengeluaran;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class DisburseGajiAction
{
    /**
     * Eksekusi pencairan / pembayaran gaji guru
     *
     * @param GajiGuru $gaji
     * @param string|null $pathBukti
     * @param string|null $tanggalBayar
     * @param string|null $catatan
     * @param int|null $petugasId
     * @return Pengeluaran
     */
    public function execute(
        GajiGuru $gaji,
        ?string $pathBukti = null,
        ?string $tanggalBayar = null,
        ?string $catatan = null,
        ?int $petugasId = null
    ): Pengeluaran {
        return DB::transaction(function () use ($gaji, $pathBukti, $tanggalBayar, $catatan, $petugasId) {
            $kategori = KategoriPengeluaran::firstOrCreate(
                ['nama' => 'Gaji Guru'],
                ['jenis' => 'operasional']
            );

            $keterangan = "Honorarium Pegawai Yayasan: " . ($gaji->guru->user->nama ?? 'Guru') . " (" . ($gaji->jabatan ?: 'Guru') . ") - Periode " . $gaji->bulan . " " . $gaji->tahun;
            if ($catatan) {
                $keterangan .= " (Catatan: " . $catatan . ")";
            }

            $pengeluaran = Pengeluaran::create([
                'kategori_pengeluaran_id' => $kategori->id,
                'jumlah' => $gaji->total_diterima,
                'tanggal' => $tanggalBayar ?: now()->toDateString(),
                'keterangan' => $keterangan,
                'petugas_id' => $petugasId ?: auth()->id(),
                'bukti' => $pathBukti,
            ]);

            // Potong saldo pinjaman jika ada
            if ($gaji->potongan_peminjaman > 0) {
                $activeLoan = Peminjaman::where('guru_id', $gaji->guru_id)
                    ->where('status', 'berjalan')
                    ->where('sisa_pinjaman', '>', 0)
                    ->first();

                if ($activeLoan) {
                    $newSisa = max(0, $activeLoan->sisa_pinjaman - $gaji->potongan_peminjaman);
                    $status = $newSisa <= 0 ? 'lunas' : 'berjalan';

                    $activeLoan->update([
                        'sisa_pinjaman' => $newSisa,
                        'status' => $status
                    ]);
                }
            }

            // Update status gaji
            $gaji->update([
                'status' => 'dibayar',
                'pengeluaran_id' => $pengeluaran->id,
                'tanggal_bayar' => $tanggalBayar ?: now()->toDateString(),
                'bukti_bayar' => $pathBukti,
            ]);

            // Kirim notifikasi
            if ($gaji->guru?->user_id) {
                NotificationService::send(
                    $gaji->guru->user_id,
                    'Gaji Telah Dibayarkan',
                    "Honorarium/Gaji Anda untuk periode {$gaji->bulan} {$gaji->tahun} sebesar Rp " . number_format($gaji->total_diterima, 0, ',', '.') . " telah berhasil diproses pada " . date('d-m-Y') . ".",
                    'sistem',
                    ['in_app']
                );
            }

            return $pengeluaran;
        });
    }
}
