<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, Auditable;

    protected $fillable = [
        'nama',
        'username',
        'email',
        'password',
        'role_id',
        'no_hp',
        'alamat',
        'status',
        'ttd_digital',
        'nip',
        'jabatan',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function guru()
    {
        return $this->hasOne(Guru::class);
    }

    public function siswa()
    {
        return $this->hasOne(Siswa::class);
    }

    public function notifikasis()
    {
        return $this->hasMany(Notifikasi::class);
    }

    public function isSuperAdmin(): bool
    {
        return in_array($this->role?->nama, ['super_admin', 'founder']);
    }

    public function isSuperAdmin2(): bool
    {
        return in_array($this->role?->nama, ['super_admin_2', 'pengawas']);
    }

    public function isReadOnlyAdmin(): bool
    {
        return in_array($this->role?->nama, ['super_admin_2', 'pengawas']);
    }

    public function canApproveFinancial(): bool
    {
        return in_array($this->role?->nama, ['super_admin', 'super_admin_2', 'founder', 'pengawas']);
    }

    public function isKepalaSekolah(): bool
    {
        return $this->role?->nama === 'kepala_sekolah';
    }
}
