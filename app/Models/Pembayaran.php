<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pembayaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pembayaran';

    protected $fillable = [
        'no_resi',
        'tagihan_id',
        'tanggal_bayar',
        'nominal_dibayar',
        'kelebihan_bayar',
        'metode_bayar',
        'bukti_bayar',
        'is_void',
        'petugas_id',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'nominal_dibayar' => 'decimal:2',
        'kelebihan_bayar' => 'decimal:2',
        'is_void' => 'boolean',
    ];

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class);
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
