<?php

namespace App\Actions\Finance;

use App\Models\JenisTagihan;
use App\Models\Tagihan;
use App\Services\AuditLogger;

class ManageKategoriTagihanAction
{
    /**
     * Save (create or update) a JenisTagihan category.
     *
     * @return array{jt: JenisTagihan, isEdit: bool, title: string, message: string, type: string}
     */
    public function save(array $data, ?int $id = null): array
    {
        $nama = trim($data['nama']);
        $kategoriTipe = $data['tipe'] ?? 'rutin';
        $nominal = floatval($data['nominal'] ?? 0.00);
        $isBlocking = (bool) ($data['is_blocking'] ?? true);

        $isEdit = !empty($id);
        if ($isEdit) {
            $jt = JenisTagihan::findOrFail($id);
            $oldKategori = $jt->kategori;
            $jt->update([
                'nama' => $nama,
                'kategori' => $kategoriTipe,
                'default_nominal' => $nominal,
                'is_blocking' => $isBlocking,
            ]);

            $cleanedCount = 0;
            $nonRutinCategories = ['one_time', 'semester', 'per_6_bulan', 'tahunan', 'sekali_semester'];
            if ($oldKategori === 'rutin' && in_array($kategoriTipe, $nonRutinCategories)) {
                $cleanedCount = $this->cleanupDuplicateUnpaidBills($jt);
            }

            AuditLogger::log('updated', 'Memperbarui kategori tagihan: ' . $jt->nama, $jt, [
                'log_name' => 'manajemen_tagihan',
                'cleaned_duplicates' => $cleanedCount,
            ]);

            $message = 'Kategori tagihan "' . $jt->nama . '" berhasil diperbarui.';
            if ($cleanedCount > 0) {
                $message .= ' Sistem otomatis membersihkan ' . $cleanedCount . ' tagihan duplikat yang belum dibayar.';
            }

            return [
                'jt' => $jt,
                'isEdit' => true,
                'title' => 'Kategori Tagihan Diperbarui',
                'message' => $message,
                'type' => 'edit',
                'cleaned_duplicates' => $cleanedCount,
            ];
        }

        $jt = JenisTagihan::create([
            'nama' => $nama,
            'kategori' => $kategoriTipe,
            'default_nominal' => $nominal,
            'is_blocking' => $isBlocking,
        ]);

        AuditLogger::log('created', 'Menambahkan kategori tagihan baru: ' . $jt->nama, $jt, [
            'log_name' => 'manajemen_tagihan',
        ]);

        return [
            'jt' => $jt,
            'isEdit' => false,
            'title' => 'Kategori Tagihan Ditambahkan',
            'message' => 'Kategori tagihan baru "' . $jt->nama . '" berhasil ditambahkan.',
            'type' => 'create',
        ];
    }

    /**
     * Delete a category if not in use.
     *
     * @return array{success: bool, title: string, message: string, type: string}
     */
    public function delete(int $id): array
    {
        $jt = JenisTagihan::findOrFail($id);
        $countTagihan = Tagihan::where('jenis_tagihan_id', $id)->count();

        if ($countTagihan > 0) {
            return [
                'success' => false,
                'title' => 'Kategori Tidak Dapat Dihapus',
                'message' => 'Kategori "' . $jt->nama . '" sedang digunakan oleh ' . $countTagihan . ' data tagihan siswa. Kategori tidak dapat dihapus demi menjaga integritas data pembukuan.',
                'type' => 'warning',
            ];
        }

        $nama = $jt->nama;
        AuditLogger::log('deleted', 'Menghapus kategori tagihan: ' . $nama, $jt, [
            'log_name' => 'manajemen_tagihan',
        ]);

        $jt->delete();

        return [
            'success' => true,
            'title' => 'Kategori Tagihan Dihapus',
            'message' => 'Kategori tagihan "' . $nama . '" berhasil dihapus dari sistem.',
            'type' => 'delete',
        ];
    }

    /**
     * Cleans up duplicate unpaid bills for students when a category is changed from rutin to non-rutin.
     * Keeps the primary bill (or the one with payments). Only deletes duplicate bills where total_dibayar == 0.
     */
    public function cleanupDuplicateUnpaidBills(JenisTagihan $jt): int
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($jt) {
            $tagihans = Tagihan::where('jenis_tagihan_id', $jt->id)
                ->with('pembayarans')
                ->orderBy('siswa_id')
                ->orderBy('id', 'asc')
                ->get()
                ->groupBy(function ($t) {
                    return $t->siswa_id . '_' . ($t->tahun_ajaran_id ?? '0');
                });

            $deleteAction = app(\App\Actions\Finance\DeleteTagihanAction::class);
            $deletedCount = 0;

            foreach ($tagihans as $groupKey => $bills) {
                if ($bills->count() <= 1) {
                    continue;
                }

                // Identify primary bill: prefer bill that has payment; otherwise first bill
                $paidBill = $bills->first(fn($b) => (float)$b->total_dibayar > 0);
                $primaryBill = $paidBill ?: $bills->first();

                // All other bills for this student within the same academic year are candidates for cleanup
                $duplicates = $bills->reject(fn($b) => $b->id === $primaryBill->id);

                foreach ($duplicates as $dup) {
                    // Safety check: ONLY delete if no payments have been made on this duplicate bill
                    if ((float)$dup->total_dibayar == 0) {
                        $deleteAction->execute($dup);
                        $deletedCount++;
                    }
                }
            }

            return $deletedCount;
        });
    }
}
