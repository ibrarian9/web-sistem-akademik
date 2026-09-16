<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class PemasukanKas extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'pemasukan_kas';

    protected $fillable = [
        'kategori',
        'jumlah',
        'tanggal',
        'keterangan',
        'petugas_id',
        'bukti',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal' => 'date',
    ];

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function scopePeriode($query, ?string $startDate = null, ?string $endDate = null)
    {
        return $query->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
            $q->whereBetween('tanggal', [$startDate, $endDate]);
        });
    }

    public function scopeBulanTahun($query, ?int $bulan = null, ?int $tahun = null)
    {
        return $query->when($bulan, fn($q) => $q->whereMonth('tanggal', $bulan))
            ->when($tahun, fn($q) => $q->whereYear('tanggal', $tahun));
    }
}
