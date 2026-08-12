<?php

namespace App\Traits;

use App\Services\AuditLogger;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            $name = class_basename($model);
            $identifier = $model->nama ?? $model->name ?? $model->judul ?? $model->kode_transaksi ?? "#{$model->getKey()}";
            AuditLogger::log(
                'created',
                "Membuat data {$name} ({$identifier})",
                $model,
                ['changes' => $model->getAttributes()]
            );
        });

        static::updated(function ($model) {
            $name = class_basename($model);
            $identifier = $model->nama ?? $model->name ?? $model->judul ?? $model->kode_transaksi ?? "#{$model->getKey()}";
            AuditLogger::log(
                'updated',
                "Memperbarui data {$name} ({$identifier})",
                $model,
                ['changes' => $model->getChanges()]
            );
        });

        static::deleted(function ($model) {
            $name = class_basename($model);
            $identifier = $model->nama ?? $model->name ?? $model->judul ?? $model->kode_transaksi ?? "#{$model->getKey()}";
            AuditLogger::log(
                'deleted',
                "Menghapus data {$name} ({$identifier})",
                $model,
                ['changes' => $model->getAttributes()]
            );
        });
    }
}
