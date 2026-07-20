<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PemasukanKas extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pemasukan_kas';

    protected $fillable = [
        'kategori',
        'jumlah',
        'tanggal',
        'keterangan',
        'petugas_id',
        'bukti',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal' => 'date',
    ];

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
