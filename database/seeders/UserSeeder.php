<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Guru;
use App\Models\Siswa;
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
        $roleTataUsaha = Role::where('nama', 'tata_usaha')->first();
        $roleFinance = Role::where('nama', 'finance')->first();
        $roleGuru = Role::where('nama', 'guru')->first();
        $roleMurid = Role::where('nama', 'murid')->first();
        $roleKepalaSekolah = Role::where('nama', 'kepala_sekolah')->first();
        $roleKoordinator = Role::where('nama', 'koordinator')->first();

        // 1. Create Super Admin / Kepala Yayasan
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

        // 2. Create Tata Usaha Staff
        if ($roleTataUsaha) {
            User::firstOrCreate([
                'username' => 'tatausaha',
            ], [
                'nama' => 'Dewi Rahmawati, S.Pd.',
                'email' => 'tu@yayasan.or.id',
                'password' => Hash::make('tatausaha123'),
                'role_id' => $roleTataUsaha->id,
                'no_hp' => '081234567895',
                'alamat' => 'Sleman, Yogyakarta',
                'status' => 'aktif',
            ]);
        }

        // 3. Create Finance Staff
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

        // 4. Create Guru (Teacher) User and profile
        if ($roleGuru) {
            $userGuru = User::firstOrCreate([
                'username' => 'guru',
            ], [
                'nama' => 'Guru Teladan, S.Pd.',
                'email' => 'guru@yayasan.or.id',
                'password' => Hash::make('guru123'),
                'role_id' => $roleGuru->id,
                'no_hp' => '081234567892',
                'alamat' => 'Sleman, Yogyakarta',
                'status' => 'aktif',
            ]);

            Guru::firstOrCreate([
                'nip' => '1234567890',
            ], [
                'user_id' => $userGuru->id,
                'jenis_guru' => 'umum',
                'no_hp' => $userGuru->no_hp,
                'alamat' => $userGuru->alamat,
                'tanggal_masuk' => '2026-01-01',
                'status_aktif' => true,
            ]);
        }

        // 5. Create Murid (Student) User and profile
        if ($roleMurid) {
            $userSiswa = User::firstOrCreate([
                'username' => 'siswa',
            ], [
                'nama' => 'Siswa Berprestasi',
                'email' => 'siswa@yayasan.or.id',
                'password' => Hash::make('siswa123'),
                'role_id' => $roleMurid->id,
                'no_hp' => '081234567893',
                'alamat' => 'Sleman, Yogyakarta',
                'status' => 'aktif',
            ]);

            Siswa::firstOrCreate([
                'nis' => '9999',
            ], [
                'user_id' => $userSiswa->id,
                'nisn' => '0099999999',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Yogyakarta',
                'tanggal_lahir' => '2012-01-01',
                'alamat' => $userSiswa->alamat,
                'nama_wali' => 'Wali Siswa',
                'no_hp_wali' => '081234567894',
                'kelas_id' => null, // No class assigned initially
                'tanggal_masuk' => '2026-01-01',
                'status' => 'aktif',
            ]);
        }

        // 6. Create Kepala Sekolah
        if ($roleKepalaSekolah) {
            User::firstOrCreate([
                'username' => 'kepala',
            ], [
                'nama' => 'Dr. H. M. Yusuf, M.A.',
                'email' => 'kepala@yayasan.or.id',
                'password' => Hash::make('kepala123'),
                'role_id' => $roleKepalaSekolah->id,
                'no_hp' => '081234567896',
                'alamat' => 'Sleman, Yogyakarta',
                'status' => 'aktif',
            ]);
        }

        // 7. Create Koordinator
        if ($roleKoordinator) {
            User::firstOrCreate([
                'username' => 'koordinator',
            ], [
                'nama' => 'Ustadz Ahmad Fauzi, S.Pd.I.',
                'email' => 'koordinator@yayasan.or.id',
                'password' => Hash::make('koordinator123'),
                'role_id' => $roleKoordinator->id,
                'no_hp' => '081234567897',
                'alamat' => 'Sleman, Yogyakarta',
                'status' => 'aktif',
            ]);
        }
    }
}
