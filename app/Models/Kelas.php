<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kelas extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kelas';

    protected $fillable = [
        'nama_kelas',
        'tingkat',
        'semester_id',
        'guru_umum_id',
        'guru_tahfidz_id',
    ];

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function guruUmum()
    {
        return $this->belongsTo(Guru::class, 'guru_umum_id');
    }

    public function guruTahfidz()
    {
        return $this->belongsTo(Guru::class, 'guru_tahfidz_id');
    }

    public function siswas()
    {
        return $this->hasMany(Siswa::class);
    }

    public function siswaKelas()
    {
        return $this->hasMany(SiswaKelas::class);
    }

    public function guruMapelKelas()
    {
        return $this->hasMany(GuruMapelKelas::class);
    }

    public function nilais()
    {
        return $this->hasMany(Nilai::class);
    }

    public function rapors()
    {
        return $this->hasMany(Rapor::class);
    }

    public function absensiSiswas()
    {
        return $this->hasMany(AbsensiSiswa::class);
    }
}
