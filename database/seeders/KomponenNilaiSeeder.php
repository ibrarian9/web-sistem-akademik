<?php

namespace Database\Seeders;

use App\Models\KomponenNilai;
use Illuminate\Database\Seeder;

class KomponenNilaiSeeder extends Seeder
{
    public function run(): void
    {
        $komponens = [
            [
                'nama' => 'UH (Ulangan Harian)',
                'kategori' => 'pengetahuan',
                'bobot' => 25.00,
                'berlaku_untuk' => 'umum',
                'urutan' => 1,
            ],
            [
                'nama' => 'PTS (Penilaian Tengah Semester)',
                'kategori' => 'pengetahuan',
                'bobot' => 25.00,
                'berlaku_untuk' => 'umum',
                'urutan' => 2,
            ],
            [
                'nama' => 'PAS (Penilaian Akhir Semester)',
                'kategori' => 'pengetahuan',
                'bobot' => 30.00,
                'berlaku_untuk' => 'umum',
                'urutan' => 3,
            ],
            [
                'nama' => 'Praktik',
                'kategori' => 'keterampilan',
                'bobot' => 10.00,
                'berlaku_untuk' => 'umum',
                'urutan' => 4,
            ],
            [
                'nama' => 'Proyek',
                'kategori' => 'keterampilan',
                'bobot' => 10.00,
                'berlaku_untuk' => 'umum',
                'urutan' => 5,
            ],
            [
                'nama' => 'Tahfidz',
                'kategori' => 'keagamaan',
                'bobot' => 50.00,
                'berlaku_untuk' => 'tahfidz',
                'urutan' => 6,
            ],
            [
                'nama' => 'Hafalan',
                'kategori' => 'keagamaan',
                'bobot' => 50.00,
                'berlaku_untuk' => 'tahfidz',
                'urutan' => 7,
            ],
            [
                'nama' => 'Sikap',
                'kategori' => 'sikap',
                'bobot' => 100.00,
                'berlaku_untuk' => 'semua',
                'urutan' => 8,
            ],
        ];

        foreach ($komponens as $komponen) {
            KomponenNilai::firstOrCreate(['nama' => $komponen['nama']], $komponen);
        }
    }
}
