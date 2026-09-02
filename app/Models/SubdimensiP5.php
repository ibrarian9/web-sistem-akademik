<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class SubdimensiP5 extends Model
{
    use HasFactory, Auditable;

    protected $table = 'subdimensi_p5';

    protected $fillable = [
        'dimensi_id',
        'nama_subdimensi',
        'urutan',
    ];

    public function dimensi()
    {
        return $this->belongsTo(DimensiP5::class, 'dimensi_id');
    }
}
