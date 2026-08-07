<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DimensiP5 extends Model
{
    use HasFactory;

    protected $table = 'dimensi_p5';

    protected $fillable = [
        'nama_dimensi',
        'urutan',
    ];

    public function subdimensi()
    {
        return $this->hasMany(SubdimensiP5::class, 'dimensi_id')->orderBy('urutan', 'asc');
    }

    public function subdimensis()
    {
        return $this->subdimensi();
    }

    public function subdimensiP5()
    {
        return $this->subdimensi();
    }
}
