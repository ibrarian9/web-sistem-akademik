<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Guru extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'guru';

    protected $fillable = [
        'user_id',
        'nip',
        'nik',
        'jenis_guru',
        'status_kepegawaian',
        'pendidikan',
        'grade_guru',
        'status_pernikahan',
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

    public function getNiyAttribute()
    {
        return $this->attributes['nip'] ?? null;
    }

    public function setNiyAttribute($value)
    {
        $this->attributes['nip'] = $value;
    }


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

    public function capaianGurus()
    {
        return $this->hasMany(CapaianGuru::class, 'guru_id');
    }

    public function catatanPendampingan()
    {
        return $this->hasMany(CatatanPendampingan::class, 'guru_id');
    }

    public function siswaDidampingi()
    {
        return $this->hasMany(Siswa::class, 'shadow_teacher_id');
    }

    public function scopeShadowTeacher($query)
    {
        return $query->where(function ($q) {
            $q->where('jenis_guru', 'pendamping')
              ->orWhereHas('user.role', function ($rq) {
                  $rq->whereIn('nama', ['shadow_teacher', 'pendamping']);
              });
        });
    }

    public function isGuruPendamping(): bool
    {
        if (strtolower($this->jenis_guru ?? '') === 'pendamping') {
            return true;
        }

        if ($this->relationLoaded('user') && $this->user) {
            $roleName = strtolower($this->user->role?->nama ?? '');
            return in_array($roleName, ['shadow_teacher', 'pendamping']);
        }

        return in_array(strtolower($this->user?->role?->nama ?? ''), ['shadow_teacher', 'pendamping']);
    }
}
