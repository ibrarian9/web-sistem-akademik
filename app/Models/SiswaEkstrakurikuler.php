<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiswaEkstrakurikuler extends Model
{
    use HasFactory;

    protected $table = 'siswa_ekstrakurikuler';

    protected $fillable = [
        'siswa_id',
        'ekstrakurikuler_id',
        'semester_id',
        'predikat',
        'catatan',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function ekstrakurikuler()
    {
        return $this->belongsTo(Ekstrakurikuler::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}
