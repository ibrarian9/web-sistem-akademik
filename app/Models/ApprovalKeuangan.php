<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class ApprovalKeuangan extends Model
{
    use HasFactory, Auditable;

    protected $table = 'approval_keuangan';

    protected $fillable = [
        'pemohon_id',
        'tipe_aksi',
        'fitur',
        'model_type',
        'model_id',
        'judul',
        'data_lama',
        'data_baru',
        'alasan',
        'status',
        'disetujui_oleh',
        'tanggal_disetujui',
        'catatan_approval',
    ];

    protected $casts = [
        'data_lama' => 'array',
        'data_baru' => 'array',
        'tanggal_disetujui' => 'datetime',
    ];

    public function pemohon()
    {
        return $this->belongsTo(User::class, 'pemohon_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function scopeMenunggu($query)
    {
        return $query->where('status', 'menunggu');
    }

    public function scopeDisetujui($query)
    {
        return $query->where('status', 'disetujui');
    }

    public function scopeDitolak($query)
    {
        return $query->where('status', 'ditolak');
    }

    public function scopeDibatalkan($query)
    {
        return $query->where('status', 'dibatalkan');
    }

    public function isMenunggu(): bool
    {
        return $this->status === 'menunggu';
    }

    public function isDisetujui(): bool
    {
        return $this->status === 'disetujui';
    }

    public function isDitolak(): bool
    {
        return $this->status === 'ditolak';
    }

    public function isDibatalkan(): bool
    {
        return $this->status === 'dibatalkan';
    }
}
