<?php

namespace Database\Seeders;

use App\Models\Pengaturan;
use Illuminate\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'jam_masuk_guru',
                'value' => '07:00',
                'keterangan' => 'Jam masuk standar guru untuk perhitungan keterlambatan absensi diri.',
            ],
            [
                'key' => 'toleransi_telat_menit',
                'value' => '15',
                'keterangan' => 'Toleransi keterlambatan dalam menit sebelum status otomatis dihitung Telat.',
            ],
        ];

        foreach ($settings as $setting) {
            Pengaturan::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
