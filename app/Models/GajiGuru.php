<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class GajiGuru extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'gaji_guru';

    protected $fillable = [
        'guru_id',
        'pengeluaran_id',
        'bulan',
        'tahun',
        'gaji_pokok',
        'gaji_berkala',
        'jumlah_ekskul',
        'honor_ekskul',
        'insentif',
        'insentif_bpjs',
        'insentif_maghrib_mengaji',
        'potongan_sosial',
        'potongan_peminjaman',
        'potongan_bpjstk',
        'potongan_lainnya',
        'total_bruto',
        'total_diterima',
        'tanggal_bayar',
        'status',
        'sumber_dana',
        'jam_kerja',
        'jabatan',
    ];

    protected $casts = [
        'gaji_pokok' => 'decimal:2',
        'gaji_berkala' => 'decimal:2',
        'jumlah_ekskul' => 'integer',
        'honor_ekskul' => 'decimal:2',
        'insentif' => 'decimal:2',
        'insentif_bpjs' => 'decimal:2',
        'insentif_maghrib_mengaji' => 'decimal:2',
        'potongan_sosial' => 'decimal:2',
        'potongan_peminjaman' => 'decimal:2',
        'potongan_bpjstk' => 'decimal:2',
        'potongan_lainnya' => 'decimal:2',
        'total_bruto' => 'decimal:2',
        'total_diterima' => 'decimal:2',
        'tanggal_bayar' => 'date',
    ];

    /**
     * Hitung total penerimaan bruto
     */
    public function getTotalPenerimaanAttribute(): float
    {
        return floatval($this->gaji_pokok)
            + floatval($this->gaji_berkala)
            + floatval($this->honor_ekskul)
            + floatval($this->insentif)
            + floatval($this->insentif_bpjs)
            + floatval($this->insentif_maghrib_mengaji);
    }

    /**
     * Hitung total potongan
     */
    public function getTotalPotonganAttribute(): float
    {
        return floatval($this->potongan_sosial)
            + floatval($this->potongan_peminjaman)
            + floatval($this->potongan_bpjstk)
            + floatval($this->potongan_lainnya);
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function pengeluaran()
    {
        return $this->belongsTo(Pengeluaran::class);
    }
}
