<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tagihan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tagihan';

    protected $fillable = [
        'siswa_id',
        'jenis_tagihan_id',
        'tahun_ajaran_id',
        'bulan',
        'nominal',
        'total_dibayar',
        'status',
        'jatuh_tempo',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'total_dibayar' => 'decimal:2',
        'jatuh_tempo' => 'date',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function jenisTagihan()
    {
        return $this->belongsTo(JenisTagihan::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class);
    }
}
