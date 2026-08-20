<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class DanaBos extends Model
{
    use HasFactory, Auditable;

    protected $table = 'dana_bos';

    protected $fillable = [
        'tahun_ajaran_id',
        'jenis',
        'tanggal',
        'nominal',
        'kategori',
        'keterangan',
        'bukti',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
    ];

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }
}
