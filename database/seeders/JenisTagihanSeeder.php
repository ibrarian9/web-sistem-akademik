<?php

namespace Database\Seeders;

use App\Models\JenisTagihan;
use Illuminate\Database\Seeder;

class JenisTagihanSeeder extends Seeder
{
    public function run(): void
    {
        $jenis = [
            [
                'nama' => 'SPP',
                'kategori' => 'rutin',
                'default_nominal' => 350000.00,
                'is_blocking' => true,
            ],
            [
                'nama' => 'Uang Pembangunan',
                'kategori' => 'one_time',
                'default_nominal' => 1500000.00,
                'is_blocking' => true,
            ],
            [
                'nama' => 'Uang Seragam',
                'kategori' => 'one_time',
                'default_nominal' => 500000.00,
                'is_blocking' => false,
            ],
            [
                'nama' => 'Uang Buku',
                'kategori' => 'tahunan',
                'default_nominal' => 600000.00,
                'is_blocking' => true,
            ],
            [
                'nama' => 'Sertifikasi',
                'kategori' => 'rutin',
                'default_nominal' => 0.00,
                'is_blocking' => false,
            ],
            [
                'nama' => 'Qurban',
                'kategori' => 'one_time',
                'default_nominal' => 0.00,
                'is_blocking' => false,
            ],
            [
                'nama' => 'Uang Pendaftaran',
                'kategori' => 'one_time',
                'default_nominal' => 150000.00,
                'is_blocking' => true,
            ],
            [
                'nama' => 'Uang Tahunan',
                'kategori' => 'tahunan',
                'default_nominal' => 200000.00,
                'is_blocking' => true,
            ],
        ];

        foreach ($jenis as $item) {
            JenisTagihan::firstOrCreate(['nama' => $item['nama']], $item);
        }
    }
}
