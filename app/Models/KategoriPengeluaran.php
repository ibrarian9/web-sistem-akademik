<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class KategoriPengeluaran extends Model
{
    use HasFactory, Auditable;

    protected $table = 'kategori_pengeluaran';

    protected $fillable = [
        'nama',
        'jenis',
    ];

    public function pengeluarans()
    {
        return $this->hasMany(Pengeluaran::class);
    }
}
