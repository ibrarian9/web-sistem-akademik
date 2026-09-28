<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Traits\Auditable;

class Pengaturan extends Model
{
    use HasFactory, Auditable;

    protected $table = 'pengaturan';

    protected $fillable = [
        'key',
        'value',
        'keterangan',
    ];

    protected static function booted()
    {
        static::saved(function ($model) {
            Cache::forget('setting_' . $model->key);
            Cache::forget('all_app_settings');
        });

        static::deleted(function ($model) {
            Cache::forget('setting_' . $model->key);
            Cache::forget('all_app_settings');

            $mirrors = [
                'nama_sekolah' => 'nama_instansi',
                'nama_instansi' => 'nama_sekolah',
                'alamat_sekolah' => 'alamat_instansi',
                'alamat_instansi' => 'alamat_sekolah',
                'no_telepon' => 'telepon_instansi',
                'telepon_instansi' => 'no_telepon',
            ];
            if (isset($mirrors[$model->key])) {
                Cache::forget('setting_' . $mirrors[$model->key]);
            }
        });
    }

    public static function getValue(string $key, $default = null)
    {
        return Cache::remember('setting_' . $key, 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            if ($setting && $setting->value !== null && $setting->value !== '') {
                return $setting->value;
            }

            $fallbacks = [
                'nama_sekolah' => 'nama_instansi',
                'nama_instansi' => 'nama_sekolah',
                'alamat_sekolah' => 'alamat_instansi',
                'alamat_instansi' => 'alamat_sekolah',
                'no_telepon' => 'telepon_instansi',
                'telepon_instansi' => 'no_telepon',
            ];

            if (isset($fallbacks[$key])) {
                $alt = self::where('key', $fallbacks[$key])->first();
                if ($alt && $alt->value !== null && $alt->value !== '') {
                    return $alt->value;
                }
            }

            return $setting ? $setting->value : $default;
        });
    }

    public static function setValue(string $key, $value, ?string $keterangan = null): void
    {
        self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value ?? '',
                'keterangan' => $keterangan ?? ucfirst(str_replace('_', ' ', $key)),
            ]
        );
        Cache::forget('setting_' . $key);
        Cache::forget('all_app_settings');

        $mirrors = [
            'nama_sekolah' => 'nama_instansi',
            'nama_instansi' => 'nama_sekolah',
            'alamat_sekolah' => 'alamat_instansi',
            'alamat_instansi' => 'alamat_sekolah',
            'no_telepon' => 'telepon_instansi',
            'telepon_instansi' => 'no_telepon',
        ];

        if (isset($mirrors[$key])) {
            $mirrorKey = $mirrors[$key];
            self::updateOrCreate(
                ['key' => $mirrorKey],
                ['value' => $value ?? '']
            );
            Cache::forget('setting_' . $mirrorKey);
        }
    }

    public static function getAllSettings(): array
    {
        return Cache::remember('all_app_settings', 3600, function () {
            return self::pluck('value', 'key')->toArray();
        });
    }
}
