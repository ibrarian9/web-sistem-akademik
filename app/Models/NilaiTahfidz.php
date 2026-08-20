<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class NilaiTahfidz extends Model
{
    use HasFactory, Auditable;

    protected $table = 'nilai_tahfidz';

    protected $fillable = [
        'siswa_id',
        'semester_id',
        'tanggal',
        'surah',
        'juz',
        'nilai_kelancaran',
        'nilai_tajwid',
        'predikat_keagamaan',
        'catatan_ustadz',
        'materi_tahsin',
        'nilai_tahsin',
        'murajaah_bersama',
        'murajaah_mandiri',
        'nilai_murajaah',
        'materi_kitabah',
        'nilai_kitabah',
        'materi_ziyadah',
        'nilai_ziyadah',
        'tanggapan_orang_tua',
        'tanggal_tanggapan',
        'dikirim_oleh_nama',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_tanggapan' => 'datetime',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}
