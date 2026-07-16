<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';

    protected $fillable = [
        'guru_id',
        'tanggal_pinjam',
        'nominal',
        'tenor_bulan',
        'cicilan_per_bulan',
        'sisa_pinjaman',
        'status',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'nominal' => 'decimal:2',
        'cicilan_per_bulan' => 'decimal:2',
        'sisa_pinjaman' => 'decimal:2',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }
}
