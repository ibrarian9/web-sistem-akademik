<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Siswa extends Model
{
    use HasFactory, SoftDeletes, Auditable;

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
        'kelas_tahfidz_id',
        'saldo_deposit',
        'tanggal_masuk',
        'status',
        'tahun_lulus',
        'catatan_alumni',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
        'saldo_deposit' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function kelasUmum()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function kelasTahfidz()
    {
        return $this->belongsTo(Kelas::class, 'kelas_tahfidz_id');
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

    public function ekstrakurikuler()
    {
        return $this->hasMany(SiswaEkstrakurikuler::class);
    }

    public function tabungans()
    {
        return $this->hasMany(Tabungan::class, 'siswa_id');
    }

    public function latestTabungan()
    {
        return $this->hasOne(Tabungan::class, 'siswa_id')->latestOfMany('id');
    }
}
