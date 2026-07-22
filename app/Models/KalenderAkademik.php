<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KalenderAkademik extends Model
{
    use HasFactory;

    protected $table = 'kalender_akademik';

    protected $fillable = [
        'tahun_ajaran_id',
        'nama_kegiatan',
        'jenis',
        'tanggal_mulai',
        'tanggal_selesai',
        'liburkan_presensi',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'liburkan_presensi' => 'boolean',
    ];

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    /**
     * Scope to find holidays on a specific date string (Y-m-d).
     */
    public static function isHolidayDate(string $dateStr): bool
    {
        return self::where('liburkan_presensi', true)
            ->whereDate('tanggal_mulai', '<=', $dateStr)
            ->whereDate('tanggal_selesai', '>=', $dateStr)
            ->exists();
    }
}
