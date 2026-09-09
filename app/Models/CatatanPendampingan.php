<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class CatatanPendampingan extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'catatan_pendampingan';

    protected $fillable = [
        'siswa_id',
        'guru_id',
        'semester_id',
        'tanggal',
        'aspek',
        'hasil_perkembangan',
        'catatan',
        'rekomendasi',
        'periode',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public const ASPEK_LIST = [
        'Komunikasi',
        'Sosial/Interaksi',
        'Kemandirian',
        'Perilaku',
        'Konsentrasi/Fokus',
        'Aktivitas Belajar',
        'Motorik',
    ];

    public const SKALA_LIST = [
        'BB' => [
            'kode' => 'BB',
            'nama' => 'Belum Berkembang',
            'deskripsi' => 'Siswa belum menunjukkan capaian kemampuan yang diharapkan dan memerlukan bimbingan penuh.',
            'badge' => 'rose',
        ],
        'MB' => [
            'kode' => 'MB',
            'nama' => 'Mulai Berkembang',
            'deskripsi' => 'Siswa mulai menunjukkan capaian kemampuan namun masih perlu dorongan dan pendampingan.',
            'badge' => 'amber',
        ],
        'BSH' => [
            'kode' => 'BSH',
            'nama' => 'Berkembang Sesuai Harapan',
            'deskripsi' => 'Siswa menunjukkan capaian kemampuan secara konsisten sesuai target capaian pendampingan.',
            'badge' => 'emerald',
        ],
        'BSB' => [
            'kode' => 'BSB',
            'nama' => 'Berkembang Sangat Baik',
            'deskripsi' => 'Siswa telah menguasai capaian kemampuan secara mandiri dan dapat berinisiatif secara positif.',
            'badge' => 'blue',
        ],
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function getHasilPerkembanganLabelAttribute(): string
    {
        return self::SKALA_LIST[$this->hasil_perkembangan]['nama'] ?? $this->hasil_perkembangan;
    }

    public function getHasilPerkembanganBadgeAttribute(): string
    {
        return self::SKALA_LIST[$this->hasil_perkembangan]['badge'] ?? 'stone';
    }

    public function getPeriodeLabelAttribute(): string
    {
        return match ($this->periode) {
            'tengah_semester' => 'Tengah Semester',
            'akhir_semester' => 'Akhir Semester',
            default => ucfirst(str_replace('_', ' ', $this->periode ?? '')),
        };
    }

    public function scopePeriode($query, ?string $periode)
    {
        if ($periode && $periode !== 'semua') {
            return $query->where('periode', $periode);
        }
        return $query;
    }

    public function scopeAspek($query, ?string $aspek)
    {
        if ($aspek && $aspek !== 'semua') {
            return $query->where('aspek', $aspek);
        }
        return $query;
    }

    public function scopeSiswa($query, $siswaId)
    {
        if ($siswaId) {
            return $query->where('siswa_id', $siswaId);
        }
        return $query;
    }
}
