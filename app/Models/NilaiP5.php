<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiP5 extends Model
{
    use HasFactory;

    protected $table = 'nilai_p5';

    protected $fillable = [
        'siswa_id',
        'proyek_id',
        'subdimensi_p5_id',
        'titik_sumatif',
        'semester_id',
        'nilai',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function proyek()
    {
        return $this->belongsTo(ProyekP5::class, 'proyek_id');
    }

    public function subdimensiP5()
    {
        return $this->belongsTo(SubdimensiP5::class, 'subdimensi_p5_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}
