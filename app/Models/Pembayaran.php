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
        'tagihan_id',
        'tanggal_bayar',
        'nominal_dibayar',
        'metode_bayar',
        'bukti_bayar',
        'petugas_id',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'nominal_dibayar' => 'decimal:2',
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
