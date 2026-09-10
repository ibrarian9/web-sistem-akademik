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

    /**
     * Local Scope: Filter only SPP bills
     */
    public function scopeSpp($query)
    {
        return $query->whereHas('jenisTagihan', fn($q) => $q->where('nama', 'like', '%SPP%'));
    }

    /**
     * Local Scope: Filter non-SPP bills
     */
    public function scopeNonSpp($query)
    {
        return $query->whereHas('jenisTagihan', fn($q) => $q->where('nama', 'not like', '%SPP%'));
    }

    /**
     * Local Scope: Filter by status
     */
    public function scopeFilterStatus($query, ?string $status = null)
    {
        return $query->when($status, fn($q) => $q->where('status', $status));
    }

    /**
     * Local Scope: Filter by tahun ajaran ID
     */
    public function scopeFilterAcademicYear($query, ?int $tahunAjaranId = null)
    {
        return $query->when($tahunAjaranId, fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranId));
    }

    /**
     * Local Scope: Filter by bulan
     */
    public function scopeFilterBulan($query, ?string $bulan = null)
    {
        return $query->when($bulan, fn($q) => $q->where('bulan', $bulan));
    }

    /**
     * Local Scope: Search by nama siswa or NIS
     */
    public function scopeSearchSiswa($query, ?string $search = null)
    {
        return $query->when($search, function ($q) use ($search) {
            $q->whereHas('siswa.user', function ($uq) use ($search) {
                $uq->where('nama', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            })->orWhereHas('siswa', function ($sq) use ($search) {
                $sq->where('nis', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%");
            });
        });
    }
}

