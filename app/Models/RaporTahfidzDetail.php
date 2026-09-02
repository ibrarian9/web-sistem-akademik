<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class RaporTahfidzDetail extends Model
{
    use HasFactory, Auditable;

    protected $table = 'rapor_tahfidz_detail';

    protected $fillable = [
        'rapor_id',
        'total_juz_dihafal',
        'daftar_surah_lulus',
        'nilai_tajwid_rata',
        'predikat_tahfidz',
        'catatan_khusus',
    ];

    public function rapor()
    {
        return $this->belongsTo(Rapor::class, 'rapor_id');
    }
}
