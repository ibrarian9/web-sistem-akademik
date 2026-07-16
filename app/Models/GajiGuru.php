<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GajiGuru extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'gaji_guru';

    protected $fillable = [
        'guru_id',
        'pengeluaran_id',
        'bulan',
        'tahun',
        'gaji_pokok',
        'insentif_bpjs',
        'insentif_maghrib_mengaji',
        'potongan_peminjaman',
        'potongan_lainnya',
        'total_diterima',
        'tanggal_bayar',
        'status',
    ];

    protected $casts = [
        'gaji_pokok' => 'decimal:2',
        'insentif_bpjs' => 'decimal:2',
        'insentif_maghrib_mengaji' => 'decimal:2',
        'potongan_peminjaman' => 'decimal:2',
        'potongan_lainnya' => 'decimal:2',
        'total_diterima' => 'decimal:2',
        'tanggal_bayar' => 'date',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function pengeluaran()
    {
        return $this->belongsTo(Pengeluaran::class);
    }
}
