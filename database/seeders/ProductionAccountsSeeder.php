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
        $roleGuru       = Role::firstOrCreate(['nama' => 'guru']);
        $roleMurid      = Role::firstOrCreate(['nama' => 'murid']);

        // 2. Akun Founder (Super Admin)
        User::updateOrCreate(
            ['username' => 'marwansyah'],
            [
                'nama'       => 'Marwansyah',
                'email'      => 'marwansyah@gmail.com',
                'password'   => Hash::make('password123'),
                'role_id'    => $roleSuperAdmin->id,
                'status'     => 'aktif',
                'jabatan'    => 'Founder / Pembina Yayasan',
            ]
        );

        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'nama'       => 'H. Ahmad Syarifuddin',
                'email'      => 'admin@yayasan.or.id',
                'password'   => Hash::make('admin123'),
                'role_id'    => $roleSuperAdmin->id,
                'status'     => 'aktif',
            ]
        );

        // 3. Akun Tata Usaha
        User::updateOrCreate(
            ['username' => 'tatausaha'],
            [
                'nama'       => 'Dewi Rahmawati, S.Pd.',
                'email'      => 'tu@yayasan.or.id',
                'password'   => Hash::make('tatausaha123'),
                'role_id'    => $roleTataUsaha->id,
                'status'     => 'aktif',
            ]
        );

        User::updateOrCreate(
            ['username' => 'aulliahaf'],
            [
                'nama'       => 'Aulia',
                'email'      => 'aulliahaf@gmail.com',
                'password'   => Hash::make('05062003'),
                'role_id'    => $roleTataUsaha->id,
                'status'     => 'aktif',
            ]
        );

        // 4. Akun Keuangan
        User::updateOrCreate(
            ['username' => 'keuangan'],
            [
                'nama'       => 'Bendahara Keuangan',
                'email'      => 'keuangan@gmail.com',
                'password'   => Hash::make('finance123'),
                'role_id'    => $roleFinance->id,
                'status'     => 'aktif',
            ]
        );

        User::updateOrCreate(
            ['username' => 'finance'],
            [
                'nama'       => 'Siti Aminah, S.E.',
                'email'      => 'finance@yayasan.or.id',
                'password'   => Hash::make('finance123'),
                'role_id'    => $roleFinance->id,
                'status'     => 'aktif',
            ]
        );

        // 5. Akun Guru
        User::updateOrCreate(
            ['username' => 'guru'],
            [
                'nama'       => 'Guru Teladan, S.Pd.',
                'email'      => 'guru@yayasan.or.id',
                'password'   => Hash::make('guru123'),
                'role_id'    => $roleGuru->id,
                'status'     => 'aktif',
            ]
        );

        User::updateOrCreate(
            ['username' => 'gurutahfidz'],
            [
                'nama'       => 'Ustadz Nurul Mina, S.Pd.',
                'email'      => 'gurutahfidz@sistem.id',
                'password'   => Hash::make('guru123'),
                'role_id'    => $roleGuru->id,
                'status'     => 'aktif',
            ]
        );

        // 6. Akun Siswa / Wali
        User::updateOrCreate(
            ['username' => 'siswa'],
            [
                'nama'       => 'Siswa Berprestasi',
                'email'      => 'siswa@yayasan.or.id',
                'password'   => Hash::make('siswa123'),
                'role_id'    => $roleMurid->id,
                'status'     => 'aktif',
            ]
        );

        $this->command?->info("=================================================");
        $this->command?->info(" PRODUCTION & DEMO ACCOUNTS SEEDED SUCCESSFULLY ");
        $this->command?->info("=================================================");
        $this->command?->info(" 1. Founder / Admin : Username: marwansyah| Pass: password123");
        $this->command?->info("                      Username: admin     | Pass: admin123");
        $this->command?->info(" 2. Tata Usaha      : Username: tatausaha | Pass: tatausaha123");
        $this->command?->info("                      Username: aulliahaf | Pass: 05062003");
        $this->command?->info(" 3. Keuangan        : Username: keuangan  | Pass: finance123");
        $this->command?->info("                      Username: finance   | Pass: finance123");
        $this->command?->info(" 4. Guru            : Username: guru        | Pass: guru123");
        $this->command?->info("                      Username: gurutahfidz | Pass: guru123");
        $this->command?->info(" 5. Siswa / Wali    : Username: siswa     | Pass: siswa123");
        $this->command?->info("=================================================");
    }
}
