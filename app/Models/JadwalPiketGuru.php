<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPiketGuru extends Model
{
    use HasFactory;

    protected $table = 'jadwal_piket_guru';

    protected $fillable = [
        'guru_id',
        'hari',
        'semester_id',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}
