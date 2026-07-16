<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\SiswaKelas;
use App\Models\MataPelajaran;
use App\Models\GuruMapelKelas;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $roleAdmin = Role::where('nama', 'super_admin')->first();
        $roleGuru = Role::where('nama', 'guru')->first();
        $roleMurid = Role::where('nama', 'murid')->first();
        $roleFinance = Role::where('nama', 'finance')->first();

        // 1. Create Super Admin
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

        // 2. Create Finance Staff
        $userFinance = User::firstOrCreate([
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

        // 3. Create Tahun Ajaran & Semesters
        $tahunAjaran = TahunAjaran::firstOrCreate([
            'nama' => '2025/2026',
        ], [
            'status_aktif' => true,
        ]);

        $semesterGanjil = Semester::firstOrCreate([
            'tahun_ajaran_id' => $tahunAjaran->id,
            'semester' => 'ganjil',
        ], [
            'tanggal_mulai' => '2025-07-15',
            'tanggal_selesai' => '2025-12-20',
            'status_aktif' => true,
        ]);

        $semesterGenap = Semester::firstOrCreate([
            'tahun_ajaran_id' => $tahunAjaran->id,
            'semester' => 'genap',
        ], [
            'tanggal_mulai' => '2026-01-05',
            'tanggal_selesai' => '2026-06-20',
            'status_aktif' => false,
        ]);

        // 4. Create Teachers (Guru)
        $teachersData = [
            ['nama' => 'Budi Santoso, S.Pd.', 'username' => 'budi', 'nip' => '198501012010011001', 'jenis' => 'umum'],
            ['nama' => 'Lutfi Hakim, S.Pd.', 'username' => 'lutfi', 'nip' => '198702022012011002', 'jenis' => 'umum'],
            ['nama' => 'Hasan Basri, S.Pd.I.', 'username' => 'hasan', 'nip' => '199003032015011003', 'jenis' => 'tahfidz'],
            ['nama' => 'Fatmawati, S.Pd.', 'username' => 'fatma', 'nip' => '198904042013022004', 'jenis' => 'umum'],
            ['nama' => 'Dewi Lestari, S.S.Q.', 'username' => 'dewi', 'nip' => '199205052018022005', 'jenis' => 'tahfidz'],
        ];

        $gurus = [];
        foreach ($teachersData as $t) {
            $user = User::firstOrCreate([
                'username' => $t['username'],
            ], [
                'nama' => $t['nama'],
                'email' => $t['username'] . '@yayasan.or.id',
                'password' => Hash::make('guru123'),
                'role_id' => $roleGuru->id,
                'no_hp' => '0857' . rand(10000000, 99999999),
                'alamat' => 'Yogyakarta',
                'status' => 'aktif',
            ]);

            $guru = Guru::firstOrCreate([
                'nip' => $t['nip'],
            ], [
                'user_id' => $user->id,
                'jenis_guru' => $t['jenis'],
                'no_hp' => $user->no_hp,
                'alamat' => $user->alamat,
                'tanggal_masuk' => '2020-01-01',
                'status_aktif' => true,
            ]);

            $gurus[$t['username']] = $guru;
        }

        // 5. Create Kelas
        $kelasData = [
            ['nama_kelas' => '7A', 'tingkat' => '7', 'umum' => 'budi', 'tahfidz' => 'hasan'],
            ['nama_kelas' => '7B', 'tingkat' => '7', 'umum' => 'lutfi', 'tahfidz' => 'hasan'],
            ['nama_kelas' => '8A', 'tingkat' => '8', 'umum' => 'fatma', 'tahfidz' => 'dewi'],
            ['nama_kelas' => '8B', 'tingkat' => '8', 'umum' => 'budi', 'tahfidz' => 'dewi'],
            ['nama_kelas' => '9A', 'tingkat' => '9', 'umum' => 'lutfi', 'tahfidz' => 'hasan'],
        ];

        $kelasModels = [];
        foreach ($kelasData as $k) {
            $kelas = Kelas::firstOrCreate([
                'nama_kelas' => $k['nama_kelas'],
                'semester_id' => $semesterGanjil->id,
            ], [
                'tingkat' => $k['tingkat'],
                'guru_umum_id' => $gurus[$k['umum']]->id,
                'guru_tahfidz_id' => $gurus[$k['tahfidz']]->id,
            ]);
            $kelasModels[$k['nama_kelas']] = $kelas;
        }

        // 6. Create Siswa
        $siswaData = [
            ['nama' => 'Ahmad Fauzi', 'nis' => '1001', 'kelas' => '7A'],
            ['nama' => 'Bambang Tri', 'nis' => '1002', 'kelas' => '7A'],
            ['nama' => 'Candra Wijaya', 'nis' => '1003', 'kelas' => '7A'],
            ['nama' => 'Dina Marlina', 'nis' => '1004', 'kelas' => '7A'],
            ['nama' => 'Eka Saputra', 'nis' => '1005', 'kelas' => '7A'],
            ['nama' => 'Farhan Adit', 'nis' => '1006', 'kelas' => '7B'],
            ['nama' => 'Gita Kirana', 'nis' => '1007', 'kelas' => '7B'],
            ['nama' => 'Hendra Setiawan', 'nis' => '1008', 'kelas' => '7B'],
            ['nama' => 'Indah Permata', 'nis' => '1009', 'kelas' => '7B'],
            ['nama' => 'Joko Susilo', 'nis' => '1010', 'kelas' => '7B'],
        ];

        foreach ($siswaData as $index => $s) {
            $username = 'siswa' . ($index + 1);
            $user = User::firstOrCreate([
                'username' => $username,
            ], [
                'nama' => $s['nama'],
                'email' => $username . '@siswa.yayasan.or.id',
                'password' => Hash::make('siswa123'),
                'role_id' => $roleMurid->id,
                'no_hp' => '0878' . rand(10000000, 99999999),
                'alamat' => 'Yogyakarta',
                'status' => 'aktif',
            ]);

            $siswa = Siswa::firstOrCreate([
                'nis' => $s['nis'],
            ], [
                'user_id' => $user->id,
                'nisn' => '0098' . rand(100000, 999999),
                'jenis_kelamin' => ($index % 2 == 0) ? 'L' : 'P',
                'tempat_lahir' => 'Yogyakarta',
                'tanggal_lahir' => '2012-05-10',
                'alamat' => 'Yogyakarta',
                'nama_wali' => 'Wali dari ' . $s['nama'],
                'no_hp_wali' => '0899' . rand(10000000, 99999999),
                'kelas_id' => $kelasModels[$s['kelas']]->id,
                'tanggal_masuk' => '2025-07-01',
                'status' => 'aktif',
            ]);

            SiswaKelas::firstOrCreate([
                'siswa_id' => $siswa->id,
                'semester_id' => $semesterGanjil->id,
            ], [
                'kelas_id' => $kelasModels[$s['kelas']]->id,
                'status' => 'aktif',
            ]);
        }

        // 7. Create Mata Pelajaran (Mapel)
        $mapels = [
            ['nama_mapel' => 'Matematika', 'jenis' => 'umum', 'deskripsi' => 'Mata pelajaran matematika umum'],
            ['nama_mapel' => 'IPA', 'jenis' => 'umum', 'deskripsi' => 'Ilmu Pengetahuan Alam'],
            ['nama_mapel' => 'IPS', 'jenis' => 'umum', 'deskripsi' => 'Ilmu Pengetahuan Sosial'],
            ['nama_mapel' => 'Bahasa Indonesia', 'jenis' => 'umum', 'deskripsi' => 'Bahasa dan Sastra Indonesia'],
            ['nama_mapel' => 'Tahfidz Al-Quran', 'jenis' => 'tahfidz', 'deskripsi' => 'Pembelajaran hafalan Al-Quran'],
        ];

        $mapelModels = [];
        foreach ($mapels as $m) {
            $mapel = MataPelajaran::firstOrCreate(['nama_mapel' => $m['nama_mapel']], $m);
            $mapelModels[$m['nama_mapel']] = $mapel;
        }

        // 8. Penugasan Guru Mapel Kelas (GuruMapelKelas)
        // Matematika di Kelas 7A diajar oleh budi
        // Tahfidz di Kelas 7A diajar oleh hasan
        // IPA di Kelas 7B diajar oleh lutfi
        // Tahfidz di Kelas 7B diajar oleh hasan
        
        $penugasans = [
            ['guru' => 'budi', 'kelas' => '7A', 'mapel' => 'Matematika'],
            ['guru' => 'budi', 'kelas' => '7A', 'mapel' => 'Bahasa Indonesia'],
            ['guru' => 'hasan', 'kelas' => '7A', 'mapel' => 'Tahfidz Al-Quran'],
            ['guru' => 'lutfi', 'kelas' => '7B', 'mapel' => 'IPA'],
            ['guru' => 'lutfi', 'kelas' => '7B', 'mapel' => 'IPS'],
            ['guru' => 'hasan', 'kelas' => '7B', 'mapel' => 'Tahfidz Al-Quran'],
        ];

        foreach ($penugasans as $p) {
            GuruMapelKelas::firstOrCreate([
                'guru_id' => $gurus[$p['guru']]->id,
                'kelas_id' => $kelasModels[$p['kelas']]->id,
                'mapel_id' => $mapelModels[$p['mapel']]->id,
                'semester_id' => $semesterGanjil->id,
            ]);
        }
    }
}
