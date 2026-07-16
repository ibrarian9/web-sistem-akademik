<?php

namespace Database\Seeders;

use App\Models\KategoriPengeluaran;
use Illuminate\Database\Seeder;

class KategoriPengeluaranSeeder extends Seeder
{
    public function run(): void
    {
        $kategoris = [
            ['nama' => 'Gaji Guru', 'jenis' => 'operasional'],
            ['nama' => 'Insentif BPJS', 'jenis' => 'operasional'],
            ['nama' => 'Insentif Maghrib Mengaji', 'jenis' => 'operasional'],
            ['nama' => 'Sosial', 'jenis' => 'non_operasional'],
            ['nama' => 'Potongan Peminjaman', 'jenis' => 'penyesuaian'],
            ['nama' => 'Dana BOS', 'jenis' => 'bos'],
            ['nama' => 'Lainnya', 'jenis' => 'umum'],
        ];

        foreach ($kategoris as $kategori) {
            KategoriPengeluaran::firstOrCreate(['nama' => $kategori['nama']], $kategori);
        }
    }
}
