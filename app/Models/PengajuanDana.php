<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanDana extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_dana';

    protected $fillable = [
        'judul',
        'kategori',
        'nominal',
        'tanggal_pengajuan',
        'keterangan',
        'bukti_proposal',
        'pemohon_id',
        'status',
        'disetujui_koordinator_id',
        'tanggal_persetujuan_koordinator',
        'disetujui_kepala_yayasan_id',
        'tanggal_persetujuan_kepala_yayasan',
        'alasan_penolakan',
        'pengeluaran_id',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'tanggal_pengajuan' => 'date',
        'tanggal_persetujuan_koordinator' => 'datetime',
        'tanggal_persetujuan_kepala_yayasan' => 'datetime',
    ];

    public function pemohon()
    {
        return $this->belongsTo(User::class, 'pemohon_id');
    }

    public function disetujuiKoordinator()
    {
        return $this->belongsTo(User::class, 'disetujui_koordinator_id');
    }

    public function disetujuiKepalaYayasan()
    {
        return $this->belongsTo(User::class, 'disetujui_kepala_yayasan_id');
    }

    public function pengeluaran()
    {
        return $this->belongsTo(Pengeluaran::class, 'pengeluaran_id');
    }
}
