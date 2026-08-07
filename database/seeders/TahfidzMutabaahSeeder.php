<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\NilaiTahfidz;
use App\Models\Role;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TahfidzMutabaahSeeder extends Seeder
{
    /**
     * Run the database seeds for Mutaba'ah Guru Tahfizh SD TAHFIZH F3.
     */
    public function run(): void
    {
        // 1. Ensure Guru Role exists
        $guruRole = Role::where('nama', 'guru')->first();
        if (!$guruRole) {
            $guruRole = Role::create(['nama' => 'guru', 'deskripsi' => 'Guru Pengajar']);
        }

        // 2. Create / Retrieve Guru Tahfizh User
        $userTahfizh = User::firstOrCreate(
            ['username' => 'gurutahfidz'],
            [
                'email' => 'gurutahfidz@sistem.id',
                'nama' => 'Ustadz Nurul Mina, S.Pd., Gr',
                'password' => Hash::make('password123'),
                'role_id' => $guruRole->id,
            ]
        );

        $guruTahfizh = Guru::firstOrCreate(
            ['user_id' => $userTahfizh->id],
            [
                'nip' => '200003152202211000',
                'jenis_guru' => 'tahfidz',
                'tanggal_masuk' => date('Y-m-d'),
                'status_aktif' => true,
            ]
        );

        // 3. Get Active Semester
        $semester = Semester::where('status_aktif', true)->first() ?? Semester::first();

        // 4. Create Halaqah Tahfizh (Halaqah Ustadz Nurul Mina) assigned to Guru Tahfizh
        $halaqah = Kelas::firstOrCreate(
            ['nama_kelas' => 'Halaqah Ustadz Nurul Mina'],
            [
                'jenis_kelas' => 'tahfidz',
                'tingkat' => 5,
                'semester_id' => $semester ? $semester->id : null,
                'guru_tahfidz_id' => $guruTahfizh->id,
            ]
        );

        // Ensure teacher and jenis_kelas are assigned
        $halaqah->jenis_kelas = 'tahfidz';
        $halaqah->guru_tahfidz_id = $guruTahfizh->id;
        $halaqah->save();

        // Also ensure default class 5A exists as Kelas Umum
        $kelasUmum5A = Kelas::firstOrCreate(
            ['nama_kelas' => '5A'],
            [
                'jenis_kelas' => 'umum',
                'tingkat' => 5,
                'semester_id' => $semester ? $semester->id : null,
            ]
        );
        $kelasUmum5A->jenis_kelas = 'umum';
        $kelasUmum5A->save();

        // 5. Create Santri List matching physical sheet MUTABA'AH GURU TAHFIZH SD TAHFIZH F3
        $santriNames = [
            ['nama' => 'Adiza Naura Khairani', 'nisn' => '3152208186', 'gender' => 'P'],
            ['nama' => 'Aldrick Ahmad Gibran', 'nisn' => '3163248115', 'gender' => 'L'],
            ['nama' => 'Ayudia Sabila Fazia Setiawan', 'nisn' => '3166339219', 'gender' => 'P'],
            ['nama' => 'Azilla Dwi Putri', 'nisn' => '3152800686', 'gender' => 'P'],
            ['nama' => 'Damar Hafiz Hartanta', 'nisn' => '3156185254', 'gender' => 'L'],
            ['nama' => 'Dzakiyyah Talita Sakhi', 'nisn' => '3145752104', 'gender' => 'P'],
            ['nama' => 'Fadhil Muhammad Rasyid Harahap', 'nisn' => '3157420070', 'gender' => 'L'],
            ['nama' => 'Fadhilah Azizah', 'nisn' => '3169233082', 'gender' => 'P'],
            ['nama' => 'Farhah Nur Shafiyyah', 'nisn' => '3158584102', 'gender' => 'P'],
            ['nama' => 'Hafizha Tazkiatun Nafsia', 'nisn' => '3142136660', 'gender' => 'P'],
            ['nama' => 'Hamza Pansuri Hanafi', 'nisn' => '3164814725', 'gender' => 'L'],
            ['nama' => 'Kanaya Shaqueena Afanya', 'nisn' => '3142752794', 'gender' => 'P'],
            ['nama' => 'Keyla Annasya', 'nisn' => '3166732112', 'gender' => 'P'],
            ['nama' => 'Luthfia Talitazahra S', 'nisn' => '3148565899', 'gender' => 'P'],
            ['nama' => 'Luthfiah Qanita Putri', 'nisn' => '0145175736', 'gender' => 'P'],
            ['nama' => 'M. Syamil Zuhdy P', 'nisn' => '3162016567', 'gender' => 'L'],
            ['nama' => 'Muhammad \'Ammaar Yaasir Lubis', 'nisn' => '3173869791', 'gender' => 'L'],
            ['nama' => 'Muhammad Hafidz Anggara Hsb', 'nisn' => '3169772263', 'gender' => 'L'],
            ['nama' => 'Najwa Bella Kartika', 'nisn' => '3154073465', 'gender' => 'P'],
            ['nama' => 'Nur Aisyah', 'nisn' => '3161638823', 'gender' => 'P'],
        ];

        $muridRole = Role::where('nama', 'murid')->first() ?? Role::create(['nama' => 'murid']);

        foreach ($santriNames as $idx => $sData) {
            $username = 'santri_' . $sData['nisn'];
            $userMurid = User::withTrashed()->where('username', $username)->first();
            if (!$userMurid) {
                $userMurid = User::withTrashed()->where('nama', $sData['nama'])->first();
            }
            if (!$userMurid) {
                $userMurid = User::create([
                    'username' => $username,
                    'email' => $username . '@sistem.id',
                    'nama' => $sData['nama'],
                    'password' => Hash::make('password123'),
                    'role_id' => $muridRole->id,
                ]);
            } else {
                if ($userMurid->trashed()) {
                    $userMurid->restore();
                }
            }

            $siswa = Siswa::withTrashed()->where('nisn', $sData['nisn'])->first() ?? Siswa::withTrashed()->where('user_id', $userMurid->id)->first();
            if (!$siswa) {
                $siswa = Siswa::create([
                    'user_id' => $userMurid->id,
                    'kelas_id' => $kelasUmum5A->id,
                    'kelas_tahfidz_id' => $halaqah->id,
                    'nis' => '80' . str_pad($idx + 1, 3, '0', STR_PAD_LEFT),
                    'nisn' => $sData['nisn'],
                    'jenis_kelamin' => $sData['gender'],
                    'status' => 'aktif',
                ]);
            } else {
                if ($siswa->trashed()) {
                    $siswa->restore();
                }
                $siswa->kelas_id = $kelasUmum5A->id;
                $siswa->kelas_tahfidz_id = $halaqah->id;
                $siswa->save();
            }





            // Populate sample Mutaba'ah record matching physical sheet
            if ($idx === 0) {
                NilaiTahfidz::updateOrCreate(
                    [
                        'siswa_id' => $siswa->id,
                        'semester_id' => $semester->id,
                    ],
                    [
                        'surah' => 'Al-Baqarah (39-40)',
                        'juz' => 1,
                        'materi_tahsin' => 'Al-Baqarah (4-5)',
                        'nilai_tahsin' => 95,
                        'murajaah_bersama' => 'Juz 30',
                        'murajaah_mandiri' => 'Al-Baqarah (1-30)',
                        'nilai_murajaah' => 85,
                        'materi_kitabah' => 'Al-Baqarah (39-40)',
                        'nilai_kitabah' => 95,
                        'materi_ziyadah' => 'Al-Baqarah (39-40)',
                        'nilai_ziyadah' => 90,
                        'nilai_kelancaran' => 85,
                        'nilai_tajwid' => 95,
                        'predikat_keagamaan' => 'Sangat Baik',
                        'catatan_ustadz' => 'Alhamdulillah bacaan tajwid lancar dan makhraj fasih.',
                    ]
                );
            } elseif ($idx < 5) {
                NilaiTahfidz::updateOrCreate(
                    [
                        'siswa_id' => $siswa->id,
                        'semester_id' => $semester->id,
                    ],
                    [
                        'surah' => 'Al-Baqarah',
                        'juz' => 1,
                        'materi_tahsin' => 'Al-Baqarah 1-10',
                        'nilai_tahsin' => 90,
                        'murajaah_bersama' => 'Juz 30',
                        'murajaah_mandiri' => 'Juz 29',
                        'nilai_murajaah' => 88,
                        'materi_kitabah' => 'An-Naba 1-20',
                        'nilai_kitabah' => 92,
                        'materi_ziyadah' => 'Al-Baqarah 11-20',
                        'nilai_ziyadah' => 88,
                        'nilai_kelancaran' => 88,
                        'nilai_tajwid' => 90,
                        'predikat_keagamaan' => 'Sangat Baik',
                        'catatan_ustadz' => 'Makhraj huruf fasih dan hafalan sangat baik.',
                    ]
                );
            }
        }
    }
}
