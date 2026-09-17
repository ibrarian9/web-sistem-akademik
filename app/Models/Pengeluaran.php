<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Pengeluaran extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'pengeluaran';

    protected $fillable = [
        'kategori_pengeluaran_id',
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

    public function kategori()
    {
        return $this->belongsTo(KategoriPengeluaran::class, 'kategori_pengeluaran_id');
    }

    public function kategoriPengeluaran()
    {
        return $this->belongsTo(KategoriPengeluaran::class, 'kategori_pengeluaran_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function gajiGuru()
    {
        return $this->hasOne(GajiGuru::class);
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
