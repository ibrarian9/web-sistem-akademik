<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ekstrakurikuler extends Model
{
    use HasFactory;

    protected $table = 'ekstrakurikuler';

    protected $fillable = [
        'nama',
        'pembina_guru_id',
        'deskripsi',
    ];

    public function pembina()
    {
        return $this->belongsTo(Guru::class, 'pembina_guru_id');
    }

    public function siswaEkskul()
    {
        return $this->hasMany(SiswaEkstrakurikuler::class, 'ekstrakurikuler_id');
    }
}
