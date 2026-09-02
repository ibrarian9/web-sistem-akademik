<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class ProyekP5 extends Model
{
    use HasFactory, Auditable;

    protected $table = 'proyek_p5';

    protected $fillable = [
        'nama_proyek',
        'deskripsi',
    ];
}
