<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomponenNilai extends Model
{
    use HasFactory;

    protected $table = 'komponen_nilai';

    protected $fillable = [
        'nama',
        'kategori',
        'bobot',
        'berlaku_untuk',
        'urutan',
    ];

    protected $casts = [
        'bobot' => 'decimal:2',
        'urutan' => 'integer',
    ];

    public function nilais()
    {
        return $this->hasMany(Nilai::class);
    }
}
