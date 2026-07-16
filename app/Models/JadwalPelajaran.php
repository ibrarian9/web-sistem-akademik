<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPelajaran extends Model
{
    use HasFactory;

    protected $table = 'jadwal_pelajaran';

    protected $fillable = [
        'guru_mapel_kelas_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
    ];

    public function guruMapelKelas()
    {
        return $this->belongsTo(GuruMapelKelas::class);
    }
}
