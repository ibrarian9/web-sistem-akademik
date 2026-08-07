<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetHafalanTahfidz extends Model
{
    use HasFactory;

    protected $table = 'target_hafalan_tahfidz';

    protected $fillable = [
        'kelas_id',
        'semester_id',
        'target_juz',
        'target_surah',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}
