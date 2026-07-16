<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siswa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'siswa';

    protected $fillable = [
        'user_id',
        'nis',
        'nisn',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'nama_wali',
        'no_hp_wali',
        'kelas_id',
        'tanggal_masuk',
        'status',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function riwayatKelas()
    {
        return $this->hasMany(SiswaKelas::class);
    }

    public function nilais()
    {
        return $this->hasMany(Nilai::class);
    }

    public function rapors()
    {
        return $this->hasMany(Rapor::class);
    }

    public function absensi()
    {
        return $this->hasMany(AbsensiSiswa::class);
    }

    public function tagihans()
    {
        return $this->hasMany(Tagihan::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(\Spatie\Activitylog\Models\Activity::class, 'siswa_id');
    }
}
