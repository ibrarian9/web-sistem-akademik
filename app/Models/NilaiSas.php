<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;

class NilaiSas extends Model
{
    use Auditable;

    protected $table = 'nilai_sas';

    protected $fillable = [
        'siswa_id',
        'mapel_id',
        'semester_id',
        'nilai',
        'nilai_uh',
        'nilai_uts',
        'nilai_sas',
    ];

    public function getNilaiSasAttribute($value)
    {
        return $value ?? $this->attributes['nilai'] ?? null;
    }

    public function setNilaiSasAttribute($value)
    {
        $this->attributes['nilai_sas'] = $value;
        $this->attributes['nilai'] = $value;
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}
