<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guru extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'guru';

    protected $fillable = [
        'user_id',
        'nip',
        'jenis_guru',
        'status_kepegawaian',
        'tempat_lahir',
        'tanggal_lahir',
        'no_hp',
        'alamat',
        'tanggal_masuk',
        'status_aktif',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
        'status_aktif' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelasUmum()
    {
        return $this->hasMany(Kelas::class, 'guru_umum_id');
    }

    public function kelasTahfidz()
    {
        return $this->hasMany(Kelas::class, 'guru_tahfidz_id');
    }

    public function penugasanMapel()
    {
        return $this->hasMany(GuruMapelKelas::class);
    }

    public function absensi()
    {
        return $this->hasMany(AbsensiGuru::class);
    }

    public function nilais()
    {
        return $this->hasMany(Nilai::class);
    }

    public function absensiSiswas()
    {
        return $this->hasMany(AbsensiSiswa::class);
    }

    public function gajis()
    {
        return $this->hasMany(GajiGuru::class);
    }

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class);
    }
}
