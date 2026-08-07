<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProyekP5 extends Model
{
    use HasFactory;

    protected $table = 'proyek_p5';

    protected $fillable = [
        'nama_proyek',
        'deskripsi',
    ];
}
