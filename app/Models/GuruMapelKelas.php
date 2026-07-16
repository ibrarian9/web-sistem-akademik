<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuruMapelKelas extends Model
{
    use HasFactory;

    protected $table = 'guru_mapel_kelas';

    protected $fillable = [
        'guru_id',
        'kelas_id',
        'mapel_id',
        'semester_id',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mapel()
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function jadwalPelajarans()
    {
        return $this->hasMany(JadwalPelajaran::class);
    }
}
