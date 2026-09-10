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
        $className = class_basename($model);

        if ($className === 'Pengeluaran') {
            $cat = $model->kategoriPengeluaran->nama ?? ($model->kategori instanceof \Illuminate\Database\Eloquent\Model ? $model->kategori->nama : (is_string($model->kategori) ? $model->kategori : null));
            return $cat ?: ($model->keterangan ? \Illuminate\Support\Str::limit($model->keterangan, 30) : 'Pengeluaran Kas');
        }

        if ($className === 'Tagihan') {
            $j = $model->jenisTagihan->nama ?? 'Tagihan';
            $s = $model->siswa->user->nama ?? null;
            $b = $model->bulan ?? '';
            return $s ? "{$j} ({$b}) - {$s}" : "{$j} ({$b})";
        }

        if ($className === 'Pembayaran') {
            $s = $model->siswa->user->nama ?? 'Siswa';
            $resi = $model->no_resi ? " (Resi #{$model->no_resi})" : '';
            return "{$s}{$resi}";
        }

        if ($className === 'Siswa') {
            $nama = $model->user->nama ?? 'Siswa';
            $nis = $model->nis ? " (NIS: {$model->nis})" : '';
            return "{$nama}{$nis}";
        }

        if ($className === 'Guru') {
            return $model->user->nama ?? $model->nip ?? 'Guru';
        }

        if (isset($model->bulan) && isset($model->tahun)) {
            return "{$model->bulan} {$model->tahun}";
        }

        $candidates = [
            $model->no_resi ?? null,
            $model->nomor_surat ?? null,
            $model->kode_transaksi ?? null,
            $model->nama ?? null,
            $model->nama_lengkap ?? null,
            $model->nama_kelas ? "Kelas {$model->nama_kelas}" : null,
            $model->nama_kegiatan ?? null,
            $model->judul_lingkup_materi ?? null,
            $model->deskripsi_tp ?? null,
            $model->name ?? null,
            $model->nis ? "NIS: {$model->nis}" : null,
            $model->nip ? "NIP: {$model->nip}" : null,
            $model->username ?? null,
            $model->judul ?? null,
        ];

        foreach ($candidates as $candidate) {
            if ($candidate !== null && is_string($candidate) && trim($candidate) !== '') {
                return trim($candidate);
            }
        }

        if (isset($model->kategori)) {
            if ($model->kategori instanceof \Illuminate\Database\Eloquent\Model) {
                return $model->kategori->nama ?? $model->kategori->name ?? class_basename($model->kategori);
            }
            if (is_string($model->kategori) && trim($model->kategori) !== '') {
                return trim($model->kategori);
            }
        }

        return \App\Services\AuditLogFormatter::formatModelName(get_class($model));
    }

    protected static function resolveAuditableLogName($model): string
    {
        $name = class_basename($model);
        if (in_array($name, [
            'Pembayaran', 'Tagihan', 'Tabungan', 'PemasukanKas', 
            'Pengeluaran', 'DanaBos', 'PengajuanDana', 'GajiGuru', 
            'Peminjaman', 'JenisTagihan', 'KategoriPengeluaran'
        ])) {
            return 'keuangan';
        }
        if (in_array($name, [
            'Nilai', 'NilaiSas', 'NilaiSumatifTp', 'NilaiTahfidz', 'NilaiP5',
            'AbsensiSiswa', 'AbsensiGuru', 'Rapor', 'RaporDetail', 'RaporTahfidzDetail',
            'JadwalRemedial', 'LingkupMateri', 'TujuanPembelajaran', 'BobotNilaiGuru',
            'TargetHafalanTahfidz', 'TemplateDeskripsi'
        ])) {
            return 'akademik';
        }
        return 'tata_kelola';
    }
}
