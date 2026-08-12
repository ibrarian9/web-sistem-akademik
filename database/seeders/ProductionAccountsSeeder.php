<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProductionAccountsSeeder extends Seeder
{
    /**
     * Seed 3 initial production accounts: 1 Founder (Super Admin), 1 Tata Usaha, 1 Keuangan.
     */
    public function run(): void
    {
        // 1. Ensure Roles exist
        $roleSuperAdmin = Role::firstOrCreate(['nama' => 'super_admin']);
        $roleTataUsaha  = Role::firstOrCreate(['nama' => 'tata_usaha']);
        $roleFinance    = Role::firstOrCreate(['nama' => 'finance']);

        // 2. Akun 1: Founder (Super Admin)
        $founder = User::updateOrCreate(
            ['username' => 'marwansyah'],
            [
                'nama'       => 'Marwansyah',
                'email'      => 'marwansyah@gmail.com',
                'password'   => Hash::make('password123'),
                'role_id'    => $roleSuperAdmin->id,
                'status'     => 'aktif',
                'jabatan'    => 'Founder / Pembina Yayasan',
                'no_hp'      => '081234567890',
                'alamat'     => 'SD Tahfizh F3, Pekanbaru',
            ]
        );

        // 3. Akun 2: Tata Usaha
        $tatausaha = User::updateOrCreate(
            ['username' => 'aulliahaf'],
            [
                'nama'       => 'Aulia',
                'email'      => 'aulliahaf@gmail.com',
                'password'   => Hash::make('05062003'),
                'role_id'    => $roleTataUsaha->id,
                'status'     => 'aktif',
                'jabatan'    => 'Kepala Tata Usaha',
                'no_hp'      => '081234567891',
                'alamat'     => 'SD Tahfizh F3, Pekanbaru',
            ]
        );

        // 4. Akun 3: Keuangan (Bendahara)
        $keuangan = User::updateOrCreate(
            ['username' => 'keuangan'],
            [
                'nama'       => 'keuangan',
                'email'      => 'keuangan@gmail.com',
                'password'   => Hash::make('keuanganf3#2026'),
                'role_id'    => $roleFinance->id,
                'status'     => 'aktif',
                'jabatan'    => 'Bendahara Sekolah',
                'no_hp'      => '081234567892',
                'alamat'     => 'SD Tahfizh F3, Pekanbaru',
            ]
        );

        $this->command?->info("=================================================");
        $this->command?->info(" PRODUCTION ACCOUNTS SEEDED SUCCESSFULLY ");
        $this->command?->info("=================================================");
        $this->command?->info(" 1. Founder    : Username: founder    | Pass: FounderF3#2026   | Role: super_admin");
        $this->command?->info(" 2. Tata Usaha : Username: tatausaha  | Pass: TatausahaF3#2026 | Role: tata_usaha");
        $this->command?->info(" 3. Keuangan   : Username: keuangan   | Pass: KeuanganF3#2026  | Role: finance");
        $this->command?->info("=================================================");
    }
}
