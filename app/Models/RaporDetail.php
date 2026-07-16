<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RaporDetail extends Model
{
    use HasFactory;

    protected $table = 'rapor_detail';

    protected $fillable = [
        'rapor_id',
        'mapel_id',
        'nilai_pengetahuan',
        'nilai_keterampilan',
        'nilai_sikap',
        'nilai_keagamaan',
        'nilai_akhir',
        'predikat',
    ];

    protected $casts = [
        'nilai_pengetahuan' => 'decimal:2',
        'nilai_keterampilan' => 'decimal:2',
        'nilai_sikap' => 'decimal:2',
        'nilai_keagamaan' => 'decimal:2',
        'nilai_akhir' => 'decimal:2',
    ];

    public function rapor()
    {
        return $this->belongsTo(Rapor::class);
    }

    public function mapel()
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }
}
