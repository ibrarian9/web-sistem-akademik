<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Ekstrakurikuler extends Model
{
    use HasFactory, Auditable;

    protected $table = 'ekstrakurikuler';

    protected $fillable = [
        'nama',
        'pembina_guru_id',
        'deskripsi',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'tempat',
        'kuota',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
        'kuota' => 'integer',
    ];

    public function pembina()
    {
        return $this->belongsTo(Guru::class, 'pembina_guru_id');
    }

    public function siswaEkskul()
    {
        return $this->hasMany(SiswaEkstrakurikuler::class, 'ekstrakurikuler_id');
    }

    public function kegiatans()
    {
        return $this->hasMany(KegiatanEkstrakurikuler::class, 'ekstrakurikuler_id')->orderBy('tanggal', 'desc');
    }
}
