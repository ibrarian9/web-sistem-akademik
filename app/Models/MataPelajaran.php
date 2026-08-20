<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class MataPelajaran extends Model
{
    use HasFactory, Auditable;

    protected $table = 'mata_pelajaran';

    protected $fillable = [
        'nama_mapel',
        'jenis',
        'deskripsi',
        'kkm',
    ];

    public function guruMapelKelas()
    {
        return $this->hasMany(GuruMapelKelas::class, 'mapel_id');
    }

    public function nilais()
    {
        return $this->hasMany(Nilai::class, 'mapel_id');
    }

    public function raporDetails()
    {
        return $this->hasMany(RaporDetail::class, 'mapel_id');
    }
}
