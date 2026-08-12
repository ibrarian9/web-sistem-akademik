<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class JadwalRemedial extends Model
{
    use HasFactory, Auditable;

    protected $table = 'jadwal_remedials';

    protected $fillable = [
        'guru_id',
        'kelas_id',
        'mapel_id',
        'siswa_id',
        'topik_tp',
        'kategori',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'ruangan',
        'catatan',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function mapel()
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}
