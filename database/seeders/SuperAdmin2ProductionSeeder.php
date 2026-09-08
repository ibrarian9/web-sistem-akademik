<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdmin2ProductionSeeder extends Seeder
{
    /**
     * Seed production account for Super Admin 2 (Rina).
     * Usage: php artisan db:seed --class=SuperAdmin2ProductionSeeder
     */
    public function run(): void
    {
        // 1. Ensure Role super_admin_2 exists
        $roleSuperAdmin2 = Role::firstOrCreate(
            ['nama' => 'super_admin_2']
        );

        $email = 'Ina724152@gmail.com';
        $password = '724152';
        $nama = 'Rina';
        $desiredUsername = 'rina';

        // Check if user with this email already exists
        $user = User::where('email', $email)->first();

        // Check if username is already taken by someone else
        $usernameTaken = User::where('username', $desiredUsername)
            ->when($user, fn($q) => $q->where('id', '!=', $user->id))
            ->exists();

        $username = $usernameTaken ? 'ina724152' : $desiredUsername;

        if ($user) {
            $user->update([
                'nama' => $nama,
                'password' => Hash::make($password),
                'role_id' => $roleSuperAdmin2->id,
                'status' => 'aktif',
                'jabatan' => 'Super Admin 2',
            ]);
        } else {
            $user = User::create([
                'nama' => $nama,
                'username' => $username,
                'email' => $email,
                'password' => Hash::make($password),
                'role_id' => $roleSuperAdmin2->id,
                'status' => 'aktif',
                'jabatan' => 'Super Admin 2',
            ]);
        }

        if (isset($this->command)) {
            $this->command->info("=== SEEDER SUPER ADMIN 2 BERHASIL DIJALANKAN ===");
            $this->command->info("Nama     : {$user->nama}");
            $this->command->info("Email    : {$user->email}");
            $this->command->info("Username : {$user->username}");
            $this->command->info("Password : {$password}");
            $this->command->info("Role     : super_admin_2");
            $this->command->info("Status   : {$user->status}");
            $this->command->info("================================================");
        }
    }
}
