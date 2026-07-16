<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiGuru extends Model
{
    use HasFactory;

    protected $table = 'absensi_guru';

    protected $fillable = [
        'guru_id',
        'tanggal',
        'waktu_datang',
        'waktu_pulang',
        'status',
        'catatan',
        'diinput_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function causer()
    {
        return $this->belongsTo(User::class, 'diinput_oleh');
    }
}
