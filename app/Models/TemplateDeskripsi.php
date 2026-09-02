<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;

class TemplateDeskripsi extends Model
{
    use Auditable;
    protected $table = 'template_deskripsi';

    protected $fillable = [
        'mapel_id',
        'frasa_tertinggi',
        'frasa_terendah',
    ];

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }
}
