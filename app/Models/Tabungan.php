<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Tabungan extends Model
{
    use HasFactory, Auditable;

    protected $table = 'tabungans';

    protected $fillable = [
        'siswa_id',
        'petugas_id',
        'kode_transaksi',
        'jenis',
        'nominal',
        'saldo_akhir',
        'tanggal',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
        'saldo_akhir' => 'decimal:2',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
