<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatSurat extends Model
{
    use HasFactory;

    protected $table = 'riwayat_surat';

    protected $fillable = [
        'nomor_surat',
        'jenis_surat',
        'penerima_nama',
        'tanggal_surat',
        'payload_json',
        'created_by',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
        'payload_json' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
