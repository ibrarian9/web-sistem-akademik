<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_ajaran';

    protected $fillable = ['nama', 'status_aktif'];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];

    public function semesters()
    {
        return $this->hasMany(Semester::class);
    }

    public function tagihans()
    {
        return $this->hasMany(Tagihan::class);
    }

    public function danaBos()
    {
        return $this->hasMany(DanaBos::class);
    }

    public function kalenderAkademiks()
    {
        return $this->hasMany(KalenderAkademik::class);
    }
}
