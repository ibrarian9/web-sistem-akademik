<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class PresensiEkstrakurikuler extends Model
{
    use HasFactory, Auditable;

    protected $table = 'presensi_ekstrakurikuler';

    protected $fillable = [
        'kegiatan_ekstrakurikuler_id',
        'siswa_id',
        'status_kehadiran',
        'nilai',
        'catatan',
    ];

    protected $casts = [
        'nilai' => 'float',
    ];

    public function kegiatan()
    {
        return $this->belongsTo(KegiatanEkstrakurikuler::class, 'kegiatan_ekstrakurikuler_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}
