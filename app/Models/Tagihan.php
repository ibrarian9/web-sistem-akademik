<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Tagihan extends Model
{
    use HasFactory, SoftDeletes, Auditable;

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

    protected static function booted(): void
    {
        static::creating(function ($tagihan) {
            if (floatval($tagihan->nominal) <= 0) {
                $tagihan->status = 'lunas';
            }
        });

        static::updating(function ($tagihan) {
            if (floatval($tagihan->nominal) <= 0) {
                $tagihan->status = 'lunas';
            }
        });
    }

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
