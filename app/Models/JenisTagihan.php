<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisTagihan extends Model
{
    use HasFactory;

    protected $table = 'jenis_tagihan';

    protected $fillable = [
        'nama',
        'kategori',
        'default_nominal',
        'is_blocking',
    ];

    protected $casts = [
        'default_nominal' => 'decimal:2',
        'is_blocking' => 'boolean',
    ];

    public function tagihans()
    {
        return $this->hasMany(Tagihan::class);
    }
}
