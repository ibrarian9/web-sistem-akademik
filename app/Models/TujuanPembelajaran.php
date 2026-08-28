<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Auditable;

class TujuanPembelajaran extends Model
{
    use Auditable;

    protected $table = 'tujuan_pembelajaran';

    protected $fillable = [
        'lingkup_materi_id',
        'deskripsi_tp',
        'urutan',
    ];

    public function lingkupMateri(): BelongsTo
    {
        return $this->belongsTo(LingkupMateri::class, 'lingkup_materi_id');
    }

    public function nilaiSumatif(): HasMany
    {
        return $this->hasMany(NilaiSumatifTp::class, 'tp_id');
    }
}
