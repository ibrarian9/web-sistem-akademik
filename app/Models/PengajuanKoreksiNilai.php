<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanKoreksiNilai extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_koreksi_nilai';

    protected $fillable = [
        'nilai_id',
        'nilai_baru',
        'alasan',
        'status',
        'diajukan_oleh_guru_id',
        'disetujui_oleh_user_id',
    ];

    public function nilai()
    {
        return $this->belongsTo(Nilai::class);
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'diajukan_oleh_guru_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh_user_id');
    }
}
