<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    use HasFactory;

    protected $table = 'nilai';

    protected $fillable = [
        'siswa_id',
        'mapel_id',
        'guru_id',
        'kelas_id',
        'semester_id',
        'komponen_nilai_id',
        'tanggal',
        'nilai',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nilai' => 'decimal:2',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function mapel()
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function komponenNilai()
    {
        return $this->belongsTo(KomponenNilai::class);
    }
}
