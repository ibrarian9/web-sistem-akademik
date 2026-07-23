<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'super_admin',
            'tata_usaha',
            'guru',
            'murid',
            'finance',
            'kepala_sekolah',
            'pengawas',
        ];

        // Rename legacy koordinator role to pengawas if exists
        Role::where('nama', 'koordinator')->update(['nama' => 'pengawas']);

        foreach ($roles as $role) {
            Role::firstOrCreate(['nama' => $role]);
        }
    }
}
