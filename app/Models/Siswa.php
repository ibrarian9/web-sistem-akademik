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
        'shadow_teacher_id',
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

    public function getNamaPanggilanAttribute()
    {
        if (!empty($this->attributes['nama_panggilan'])) {
            return $this->attributes['nama_panggilan'];
        }

        if ($this->relationLoaded('user') && $this->user) {
            $parts = explode(' ', trim($this->user->nama));
            return $parts[0] ?? $this->user->nama;
        }

        return $this->user?->nama ? explode(' ', trim($this->user->nama))[0] : null;
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

    public function shadowTeacher()
    {
        return $this->belongsTo(Guru::class, 'shadow_teacher_id');
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

    public function nilaiSumatifTp()
    {
        return $this->hasMany(NilaiSumatifTp::class, 'siswa_id');
    }

    public function nilaiSas()
    {
        return $this->hasMany(NilaiSas::class, 'siswa_id');
    }

    public function catatanPendampingan()
    {
        return $this->hasMany(CatatanPendampingan::class, 'siswa_id');
    }

    /**
     * Scope query to students assigned to a specific shadow teacher.
     * Supports Guru ID, User ID, Guru model, User model, or default to current auth user.
     */
    public function scopeForShadowTeacher($query, $teacher = null)
    {
        $guruId = null;

        if ($teacher === null) {
            $user = auth()->user();
            $guruId = $user?->guru?->id ?? $user?->id;
        } elseif ($teacher instanceof \App\Models\Guru) {
            $guruId = $teacher->id;
        } elseif ($teacher instanceof \App\Models\User) {
            $guruId = $teacher->guru?->id ?? $teacher->id;
        } elseif (is_numeric($teacher)) {
            $guruExists = \App\Models\Guru::where('id', $teacher)->exists();
            if ($guruExists) {
                $guruId = (int) $teacher;
            } else {
                $guruId = \App\Models\Guru::where('user_id', $teacher)->value('id') ?? (int) $teacher;
            }
        }

        return $query->where('shadow_teacher_id', $guruId);
    }

    /**
     * Strict scope: if currently authenticated user is a Guru Pendamping,
     * automatically constrain the query to only their assigned students.
     */
    public function scopeScopedForUser($query, $user = null)
    {
        $user = $user ?? auth()->user();
        if (!$user) {
            return $query;
        }

        $guru = $user->guru;
        if ($guru && $guru->isGuruPendamping()) {
            return $query->where('shadow_teacher_id', $guru->id);
        }

        return $query;
    }

    /**
     * Local Scope: Filter only active students
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Local Scope: Filter students by class ID
     */
    public function scopeByKelas($query, $kelasId)
    {
        return $query->when($kelasId, fn($q) => $q->where('kelas_id', $kelasId));
    }

    /**
     * Local Scope: Search students by name, NIS, NISN, or username
     */
    public function scopeSearch($query, ?string $search = null)
    {
        return $query->when($search, function ($q) use ($search) {
            $q->where('nis', 'like', "%{$search}%")
                ->orWhere('nisn', 'like', "%{$search}%")
                ->orWhereHas('user', function ($uq) use ($search) {
                    $uq->where('nama', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                });
        });
    }
}

