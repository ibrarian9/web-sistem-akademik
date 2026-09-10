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
        'bukti_bayar',
        'sumber_dana',
        'jam_kerja',
        'jabatan',
    ];

    public function getBuktiBayarAttribute($value)
    {
        return $value ?: $this->pengeluaran?->bukti;
    }

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

    /**
     * Local Scope: Filter by bulan and tahun
     */
    public function scopeFilterPeriod($query, ?string $bulan = null, ?int $tahun = null)
    {
        return $query
            ->when($bulan, fn($q) => $q->where('bulan', $bulan))
            ->when($tahun, fn($q) => $q->where('tahun', $tahun));
    }

    /**
     * Local Scope: Filter by status ('draft', 'dibayar', etc.)
     */
    public function scopeFilterStatus($query, ?string $status = null)
    {
        return $query->when($status, fn($q) => $q->where('status', $status));
    }

    /**
     * Local Scope: Search by nama guru, NIP, or username
     */
    public function scopeSearchGuru($query, ?string $search = null)
    {
        return $query->when($search, function ($q) use ($search) {
            $q->whereHas('guru.user', function ($uq) use ($search) {
                $uq->where('nama', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            })->orWhereHas('guru', function ($gq) use ($search) {
                $gq->where('nip', 'like', "%{$search}%");
            })->orWhere('jabatan', 'like', "%{$search}%");
        });
    }
}

