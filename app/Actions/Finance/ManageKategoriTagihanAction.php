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
            $jt->update([
                'nama' => $nama,
                'kategori' => $kategoriTipe,
                'default_nominal' => $nominal,
                'is_blocking' => $isBlocking,
            ]);

            AuditLogger::log('updated', 'Memperbarui kategori tagihan: ' . $jt->nama, $jt, [
                'log_name' => 'manajemen_tagihan',
            ]);

            return [
                'jt' => $jt,
                'isEdit' => true,
                'title' => 'Kategori Tagihan Diperbarui',
                'message' => 'Kategori tagihan "' . $jt->nama . '" berhasil diperbarui.',
                'type' => 'edit',
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
}
