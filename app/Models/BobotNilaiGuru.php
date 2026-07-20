<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BobotNilaiGuru extends Model
{
    use HasFactory;

    protected $table = 'bobot_nilai_guru';

    protected $fillable = [
        'guru_mapel_kelas_id',
        'komponen_nilai_id',
        'bobot',
    ];

    public function guruMapelKelas()
    {
        return $this->belongsTo(GuruMapelKelas::class, 'guru_mapel_kelas_id');
    }

    public function komponenNilai()
    {
        return $this->belongsTo(KomponenNilai::class, 'komponen_nilai_id');
    }
}
