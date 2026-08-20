<?php

namespace App\Traits;

use App\Services\AuditLogger;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            $name = class_basename($model);
            $identifier = static::resolveAuditableIdentifier($model);
            $logName = static::resolveAuditableLogName($model);
            
            AuditLogger::log(
                'created',
                "Membuat data {$name} ({$identifier})",
                $model,
                [
                    'log_name' => $logName,
                    'changes' => $model->getAttributes(),
                ]
            );
        });

        static::updated(function ($model) {
            $name = class_basename($model);
            $identifier = static::resolveAuditableIdentifier($model);
            $logName = static::resolveAuditableLogName($model);
            
            AuditLogger::log(
                'updated',
                "Memperbarui data {$name} ({$identifier})",
                $model,
                [
                    'log_name' => $logName,
                    'changes' => $model->getChanges(),
                ]
            );
        });

        static::deleted(function ($model) {
            $name = class_basename($model);
            $identifier = static::resolveAuditableIdentifier($model);
            $logName = static::resolveAuditableLogName($model);
            
            AuditLogger::log(
                'deleted',
                "Menghapus data {$name} ({$identifier})",
                $model,
                [
                    'log_name' => $logName,
                    'changes' => $model->getAttributes(),
                ]
            );
        });
    }

    protected static function resolveAuditableIdentifier($model): string
    {
        return $model->no_resi 
            ?? $model->kode_transaksi 
            ?? $model->nama 
            ?? $model->name 
            ?? $model->nis 
            ?? $model->username 
            ?? $model->judul 
            ?? $model->kategori 
            ?? "#{$model->getKey()}";
    }

    protected static function resolveAuditableLogName($model): string
    {
        $name = class_basename($model);
        if (in_array($name, ['Pembayaran', 'Tagihan', 'Tabungan', 'PemasukanKas', 'Pengeluaran', 'DanaBos', 'PengajuanDana', 'GajiGuru', 'Peminjaman', 'JenisTagihan', 'KategoriPengeluaran'])) {
            return 'keuangan';
        }
        if (in_array($name, ['Nilai', 'NilaiSas', 'NilaiSumatifTp', 'NilaiTahfidz', 'AbsensiSiswa', 'AbsensiGuru', 'Rapor', 'RaporDetail'])) {
            return 'akademik';
        }
        return 'tata_kelola';
    }
}
