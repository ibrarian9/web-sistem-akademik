<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class KegiatanEkstrakurikuler extends Model
{
    use HasFactory, Auditable;

    protected $table = 'kegiatan_ekstrakurikuler';

    protected $fillable = [
        'ekstrakurikuler_id',
        'semester_id',
        'tanggal',
        'nama_kegiatan',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function ekstrakurikuler()
    {
        return $this->belongsTo(Ekstrakurikuler::class, 'ekstrakurikuler_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function presensis()
    {
        return $this->hasMany(PresensiEkstrakurikuler::class, 'kegiatan_ekstrakurikuler_id');
    }
}
