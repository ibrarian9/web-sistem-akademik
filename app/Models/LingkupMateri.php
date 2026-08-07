<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LingkupMateri extends Model
{
    protected $table = 'lingkup_materi';

    protected $fillable = [
        'mapel_id',
        'nama_lingkup_materi',
        'kategori',
        'urutan',
    ];

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }

    public function tujuanPembelajaran(): HasMany
    {
        return $this->hasMany(TujuanPembelajaran::class, 'lingkup_materi_id')->orderBy('urutan', 'asc');
    }
}
