<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasi';

    protected $fillable = [
        'user_id',
        'siswa_id',
        'judul',
        'isi_pesan',
        'jenis',
        'channel',
        'status_kirim',
        'dibaca_pada',
        'dikirim_pada',
        'tabel_terkait',
        'data_id_terkait',
    ];

    protected $casts = [
        'dibaca_pada' => 'datetime',
        'dikirim_pada' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
