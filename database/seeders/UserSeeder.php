<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleAdmin = Role::where('nama', 'super_admin')->first();
        $roleFinance = Role::where('nama', 'finance')->first();

        // 1. Create Super Admin
        if ($roleAdmin) {
            User::firstOrCreate([
                'username' => 'admin',
            ], [
                'nama' => 'H. Ahmad Syarifuddin',
                'email' => 'admin@yayasan.or.id',
                'password' => Hash::make('admin123'),
                'role_id' => $roleAdmin->id,
                'no_hp' => '081234567890',
                'alamat' => 'Sleman, Yogyakarta',
                'status' => 'aktif',
            ]);
        }

        // 2. Create Finance Staff
        if ($roleFinance) {
            User::firstOrCreate([
                'username' => 'finance',
            ], [
                'nama' => 'Siti Aminah, S.E.',
                'email' => 'finance@yayasan.or.id',
                'password' => Hash::make('finance123'),
                'role_id' => $roleFinance->id,
                'no_hp' => '081234567891',
                'alamat' => 'Bantul, Yogyakarta',
                'status' => 'aktif',
            ]);
        }
    }
}
