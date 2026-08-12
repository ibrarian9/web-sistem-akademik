<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class CapaianGuru extends Model
{
    use HasFactory, Auditable;

    protected $table = 'capaian_gurus';

    protected $fillable = [
        'guru_id',
        'penilai_id',
        'judul',
        'kategori',
        'tahun_ajaran_id',
        'semester_id',
        'link_gdrive',
        'deskripsi',
        'skor_nilai',
        'predikat',
        'catatan_evaluasi',
        'status_penilaian',
        'tanggal_penilaian',
    ];

    protected $casts = [
        'tanggal_penilaian' => 'date',
        'skor_nilai' => 'decimal:2',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function penilai()
    {
        return $this->belongsTo(User::class, 'penilai_id');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}
