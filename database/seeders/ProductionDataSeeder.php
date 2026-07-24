<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\SiswaKelas;
use App\Models\MataPelajaran;
use App\Models\GuruMapelKelas;
use App\Models\JenisTagihan;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\KategoriPengeluaran;
use App\Models\Pengeluaran;
use App\Models\Peminjaman;
use App\Models\GajiGuru;
use App\Models\Notifikasi;
use App\Models\KomponenNilai;
use App\Models\Nilai;
use App\Models\Rapor;
use App\Models\RaporDetail;
use App\Models\DanaBos;
use App\Models\JadwalPelajaran;
use App\Models\Ekstrakurikuler;
use App\Models\SiswaEkstrakurikuler;
use App\Models\PemasukanKas;
use App\Models\PengajuanDana;
use App\Models\JadwalPiketGuru;
use App\Models\BobotNilaiGuru;
use App\Models\PengajuanKoreksiNilai;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductionDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Get Roles
        $roleAdmin = Role::where('nama', 'super_admin')->first();
        $roleGuru = Role::where('nama', 'guru')->first();
        $roleMurid = Role::where('nama', 'murid')->first();
        $roleFinance = Role::where('nama', 'finance')->first();

        // Ensure roles exist
        if (!$roleAdmin || !$roleGuru || !$roleMurid || !$roleFinance) {
            $this->call(RoleSeeder::class);
            $roleAdmin = Role::where('nama', 'super_admin')->first();
            $roleGuru = Role::where('nama', 'guru')->first();
            $roleMurid = Role::where('nama', 'murid')->first();
            $roleFinance = Role::where('nama', 'finance')->first();
        }

        // Get or Create Finance User
        $userFinance = User::where('username', 'finance')->first();
        if (!$userFinance) {
            $userFinance = User::create([
                'username' => 'finance',
                'nama' => 'Siti Aminah, S.E.',
                'email' => 'finance@yayasan.or.id',
                'password' => Hash::make('finance123'),
                'role_id' => $roleFinance->id,
                'no_hp' => '081234567891',
                'alamat' => 'Bantul, Yogyakarta',
                'status' => 'aktif',
            ]);
        }

        // Ensure JenisTagihan & KategoriPengeluaran seeded
        $this->call(JenisTagihanSeeder::class);
        $this->call(KategoriPengeluaranSeeder::class);
        $this->call(KomponenNilaiSeeder::class);

        // 2. Setup Tahun Ajaran & Semester
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
            'status_aktif' => false,
        ]);

        $semesterGenap = Semester::firstOrCreate([
            'tahun_ajaran_id' => $tahunAjaran->id,
            'semester' => 'genap',
        ], [
            'tanggal_mulai' => '2026-01-05',
            'tanggal_selesai' => '2026-06-20',
            'status_aktif' => true,
        ]);

        // 3. Create Additional Teachers if needed
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

        // Get the default teacher (from UserSeeder / UserSeeder.php)
        $defaultGuruModel = Guru::where('nip', '1234567890')->first();
        if ($defaultGuruModel) {
            $gurus['guru'] = $defaultGuruModel;
        } else {
            // In case it doesn't exist, create it to prevent errors
            $userGuru = User::firstOrCreate([
                'username' => 'guru',
            ], [
                'nama' => 'Guru Teladan, S.Pd.',
                'email' => 'guru@yayasan.or.id',
                'password' => Hash::make('guru123'),
                'role_id' => $roleGuru->id,
                'no_hp' => '081234567890',
                'alamat' => 'Sleman, Yogyakarta',
                'status' => 'aktif',
            ]);

            $defaultGuruModel = Guru::firstOrCreate([
                'nip' => '1234567890',
            ], [
                'user_id' => $userGuru->id,
                'jenis_guru' => 'umum',
                'no_hp' => $userGuru->no_hp,
                'alamat' => $userGuru->alamat,
                'tanggal_masuk' => '2020-01-01',
                'status_aktif' => true,
            ]);
            $gurus['guru'] = $defaultGuruModel;
        }

        // 4. Create Kelas (Sekolah Dasar / SD: Tingkat 1 - 6)
        $kelasData = [
            ['nama_kelas' => '1A', 'tingkat' => '1', 'umum' => 'guru', 'tahfidz' => 'hasan'],
            ['nama_kelas' => '1B', 'tingkat' => '1', 'umum' => 'lutfi', 'tahfidz' => 'hasan'],
            ['nama_kelas' => '2A', 'tingkat' => '2', 'umum' => 'fatma', 'tahfidz' => 'dewi'],
            ['nama_kelas' => '3A', 'tingkat' => '3', 'umum' => 'budi', 'tahfidz' => 'dewi'],
            ['nama_kelas' => '4A', 'tingkat' => '4', 'umum' => 'lutfi', 'tahfidz' => 'hasan'],
            ['nama_kelas' => '5A', 'tingkat' => '5', 'umum' => 'fatma', 'tahfidz' => 'dewi'],
            ['nama_kelas' => '6A', 'tingkat' => '6', 'umum' => 'guru', 'tahfidz' => 'hasan'],
        ];

        $kelasModels = [];
        foreach ($kelasData as $k) {
            $kelas = Kelas::firstOrCreate([
                'nama_kelas' => $k['nama_kelas'],
                'semester_id' => $semesterGenap->id,
            ], [
                'tingkat' => $k['tingkat'],
                'guru_umum_id' => $gurus[$k['umum']]->id,
                'guru_tahfidz_id' => $gurus[$k['tahfidz']]->id,
            ]);
            $kelasModels[$k['nama_kelas']] = $kelas;
        }

        // 5. Create Students (Generate 25 students)
        $firstNames = ['Ahmad', 'Bambang', 'Candra', 'Dina', 'Eka', 'Farhan', 'Gita', 'Hendra', 'Indah', 'Joko', 'Kevin', 'Laras', 'Muhammad', 'Nadia', 'Oki', 'Putri', 'Rian', 'Siti', 'Taufik', 'Ulfa', 'Vina', 'Wawan', 'Yulia', 'Zaki', 'Arif'];
        $lastNames = ['Fauzi', 'Tri', 'Wijaya', 'Marlina', 'Saputra', 'Adit', 'Kirana', 'Setiawan', 'Permata', 'Susilo', 'Pratama', 'Dewi', 'Rizky', 'Fitri', 'Hidayat', 'Wulandari', 'Ardiansyah', 'Aminah', 'Akbar', 'Rahma', 'Lestari', 'Nugroho', 'Putra', 'Ramadhan', 'Kurniawan'];

        $siswaModels = [];

        // Check if default student 'siswa' (NIS 9999) exists and add to models first
        $defaultSiswa = Siswa::where('nis', '9999')->first();
        if ($defaultSiswa) {
            $assignedKelas = $kelasModels['1A'];
            $defaultSiswa->update([
                'kelas_id' => $assignedKelas->id,
            ]);

            SiswaKelas::firstOrCreate([
                'siswa_id' => $defaultSiswa->id,
                'semester_id' => $semesterGenap->id,
            ], [
                'kelas_id' => $assignedKelas->id,
                'status' => 'aktif',
            ]);

            $siswaModels[] = $defaultSiswa;
        }

        for ($i = 0; $i < 25; $i++) {
            $nis = 1000 + $i;
            $nama = $firstNames[$i] . ' ' . $lastNames[$i];
            $username = 'siswa' . ($i + 1);

            $user = User::firstOrCreate([
                'username' => $username,
            ], [
                'nama' => $nama,
                'email' => $username . '@siswa.yayasan.or.id',
                'password' => Hash::make('siswa123'),
                'role_id' => $roleMurid->id,
                'no_hp' => '0878' . rand(10000000, 99999999),
                'alamat' => 'Yogyakarta',
                'status' => 'aktif',
            ]);

            // Assign to class rotatingly: 1A, 1B, 2A, 3A, 4A, 5A, 6A
            $kelasKeys = array_keys($kelasModels);
            $assignedKelasName = $kelasKeys[$i % count($kelasKeys)];
            $assignedKelas = $kelasModels[$assignedKelasName];

            $siswa = Siswa::firstOrCreate([
                'nis' => (string)$nis,
            ], [
                'user_id' => $user->id,
                'nisn' => '0098' . rand(100000, 999999),
                'jenis_kelamin' => ($i % 2 == 0) ? 'L' : 'P',
                'tempat_lahir' => 'Yogyakarta',
                'tanggal_lahir' => '2016-05-10',
                'alamat' => 'Yogyakarta',
                'nama_wali' => 'Wali dari ' . $nama,
                'no_hp_wali' => '0899' . rand(10000000, 99999999),
                'kelas_id' => $assignedKelas->id,
                'tanggal_masuk' => '2025-07-01',
                'status' => 'aktif',
            ]);

            SiswaKelas::firstOrCreate([
                'siswa_id' => $siswa->id,
                'semester_id' => $semesterGenap->id,
            ], [
                'kelas_id' => $assignedKelas->id,
                'status' => 'aktif',
            ]);

            $siswaModels[] = $siswa;
        }

        // 6. Setup Mata Pelajaran & Penugasan
        $mapels = [
            ['nama_mapel' => 'Matematika', 'jenis' => 'umum', 'deskripsi' => 'Mata pelajaran matematika SD'],
            ['nama_mapel' => 'IPA', 'jenis' => 'umum', 'deskripsi' => 'Ilmu Pengetahuan Alam SD'],
            ['nama_mapel' => 'IPS', 'jenis' => 'umum', 'deskripsi' => 'Ilmu Pengetahuan Sosial SD'],
            ['nama_mapel' => 'Bahasa Indonesia', 'jenis' => 'umum', 'deskripsi' => 'Bahasa dan Sastra Indonesia SD'],
            ['nama_mapel' => 'Tahfidz Al-Quran', 'jenis' => 'tahfidz', 'deskripsi' => 'Pembelajaran hafalan Al-Quran SD'],
        ];

        $mapelModels = [];
        foreach ($mapels as $m) {
            $mapel = MataPelajaran::firstOrCreate(['nama_mapel' => $m['nama_mapel']], $m);
            $mapelModels[$m['nama_mapel']] = $mapel;
        }

        $gmkLookup = [];
        foreach ($kelasModels as $className => $kelas) {
            $gmkLookup[$className] = [];
            foreach ($mapelModels as $mapelName => $mapel) {
                $assignedTeacher = ($mapel->jenis === 'tahfidz') ? $gurus['hasan'] : $gurus['budi'];
                if ($className === '1A') {
                    $assignedTeacher = ($mapel->jenis === 'tahfidz') ? $gurus['hasan'] : $gurus['guru'];
                } elseif ($className === '1B' || $className === '5A') {
                    $assignedTeacher = ($mapel->jenis === 'tahfidz') ? $gurus['hasan'] : $gurus['lutfi'];
                } elseif ($className === '2A' || $className === '6A') {
                    $assignedTeacher = ($mapel->jenis === 'tahfidz') ? $gurus['dewi'] : $gurus['fatma'];
                }

                $gmk = GuruMapelKelas::firstOrCreate([
                    'guru_id' => $assignedTeacher->id,
                    'kelas_id' => $kelas->id,
                    'mapel_id' => $mapel->id,
                    'semester_id' => $semesterGenap->id,
                ]);

                $gmkLookup[$className][$mapelName] = $gmk;
            }
        }

        // 6b. Seed Jadwal Pelajaran (Schedules) for each class
        $schedulePattern = [
            'senin' => [
                ['mapel' => 'Matematika', 'start' => '07:30:00', 'end' => '09:00:00'],
                ['mapel' => 'IPA', 'start' => '09:00:00', 'end' => '10:30:00'],
                ['mapel' => 'Tahfidz Al-Quran', 'start' => '10:30:00', 'end' => '12:00:00'],
            ],
            'selasa' => [
                ['mapel' => 'IPS', 'start' => '07:30:00', 'end' => '09:00:00'],
                ['mapel' => 'Bahasa Indonesia', 'start' => '09:00:00', 'end' => '10:30:00'],
                ['mapel' => 'Matematika', 'start' => '10:30:00', 'end' => '12:00:00'],
            ],
            'rabu' => [
                ['mapel' => 'IPA', 'start' => '07:30:00', 'end' => '09:00:00'],
                ['mapel' => 'IPS', 'start' => '09:00:00', 'end' => '10:30:00'],
                ['mapel' => 'Tahfidz Al-Quran', 'start' => '10:30:00', 'end' => '12:00:00'],
            ],
            'kamis' => [
                ['mapel' => 'Bahasa Indonesia', 'start' => '07:30:00', 'end' => '09:00:00'],
                ['mapel' => 'Matematika', 'start' => '09:00:00', 'end' => '10:30:00'],
                ['mapel' => 'IPA', 'start' => '10:30:00', 'end' => '12:00:00'],
            ],
            'jumat' => [
                ['mapel' => 'Tahfidz Al-Quran', 'start' => '07:30:00', 'end' => '09:00:00'],
                ['mapel' => 'IPS', 'start' => '09:00:00', 'end' => '10:30:00'],
            ],
        ];

        foreach ($kelasModels as $className => $kelas) {
            foreach ($schedulePattern as $day => $sessions) {
                foreach ($sessions as $session) {
                    $mapelName = $session['mapel'];
                    $gmk = $gmkLookup[$className][$mapelName] ?? null;
                    if ($gmk) {
                        JadwalPelajaran::firstOrCreate([
                            'guru_mapel_kelas_id' => $gmk->id,
                            'hari' => $day,
                            'jam_mulai' => $session['start'],
                            'jam_selesai' => $session['end'],
                        ]);
                    }
                }
            }
        }

        // 7. Seed Financial Billings & Payments (Juli 2025 - Juni 2026)
        $months = [
            ['nama' => 'Juli', 'tahun' => 2025, 'date' => '2025-07'],
            ['nama' => 'Agustus', 'tahun' => 2025, 'date' => '2025-08'],
            ['nama' => 'September', 'tahun' => 2025, 'date' => '2025-09'],
            ['nama' => 'Oktober', 'tahun' => 2025, 'date' => '2025-10'],
            ['nama' => 'November', 'tahun' => 2025, 'date' => '2025-11'],
            ['nama' => 'Desember', 'tahun' => 2025, 'date' => '2025-12'],
            ['nama' => 'Januari', 'tahun' => 2026, 'date' => '2026-01'],
            ['nama' => 'Februari', 'tahun' => 2026, 'date' => '2026-02'],
            ['nama' => 'Maret', 'tahun' => 2026, 'date' => '2026-03'],
            ['nama' => 'April', 'tahun' => 2026, 'date' => '2026-04'],
            ['nama' => 'Mei', 'tahun' => 2026, 'date' => '2026-05'],
            ['nama' => 'Juni', 'tahun' => 2026, 'date' => '2026-06'],
        ];

        $jtSpp = JenisTagihan::where('nama', 'like', '%SPP%')->first() ?: JenisTagihan::create(['nama' => 'SPP', 'kategori' => 'rutin', 'default_nominal' => 350000, 'is_blocking' => true]);
        $jtInfaq = JenisTagihan::where('nama', 'like', '%Infaq%')->first() ?: JenisTagihan::create(['nama' => 'Infaq', 'kategori' => 'one_time', 'default_nominal' => 50000, 'is_blocking' => false]);
        $jtBuku = JenisTagihan::where('nama', 'like', '%Buku%')->first() ?: JenisTagihan::create(['nama' => 'Uang Buku', 'kategori' => 'tahunan', 'default_nominal' => 250000, 'is_blocking' => true]);
        $jtPendaftaran = JenisTagihan::where('nama', 'like', '%Pendaftaran%')->first() ?: JenisTagihan::create(['nama' => 'Uang Pendaftaran', 'kategori' => 'one_time', 'default_nominal' => 150000, 'is_blocking' => true]);
        $methods = ['Transfer Bank', 'Cash', 'QRIS', 'Virtual Account'];

        // Seed one-time admission & books at the start of school year (July 2025)
        foreach ($siswaModels as $siswa) {
            // Uang Pendaftaran
            $tagihanDaftar = Tagihan::firstOrCreate([
                'siswa_id' => $siswa->id,
                'jenis_tagihan_id' => $jtPendaftaran->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'bulan' => 'Juli',
            ], [
                'nominal' => $jtPendaftaran->default_nominal,
                'total_dibayar' => $jtPendaftaran->default_nominal,
                'status' => 'lunas',
                'jatuh_tempo' => '2025-07-10',
            ]);

            Pembayaran::firstOrCreate([
                'tagihan_id' => $tagihanDaftar->id,
            ], [
                'tanggal_bayar' => '2025-07-05',
                'nominal_dibayar' => $jtPendaftaran->default_nominal,
                'metode_bayar' => $methods[rand(0, 3)],
                'petugas_id' => $userFinance->id,
            ]);

            // Uang Buku
            $tagihanBuku = Tagihan::firstOrCreate([
                'siswa_id' => $siswa->id,
                'jenis_tagihan_id' => $jtBuku->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'bulan' => 'Juli',
            ], [
                'nominal' => $jtBuku->default_nominal,
                'total_dibayar' => $jtBuku->default_nominal,
                'status' => 'lunas',
                'jatuh_tempo' => '2025-07-20',
            ]);

            Pembayaran::firstOrCreate([
                'tagihan_id' => $tagihanBuku->id,
            ], [
                'tanggal_bayar' => '2025-07-15',
                'nominal_dibayar' => $jtBuku->default_nominal,
                'metode_bayar' => $methods[rand(0, 3)],
                'petugas_id' => $userFinance->id,
            ]);
        }

        // Seed SPP & Infaq over 12 months
        foreach ($months as $monthIdx => $m) {
            foreach ($siswaModels as $siswaIdx => $siswa) {
                // Determine payment status realistically
                // Most are lunas. A few become late in recent months (May/June 2026).
                $status = 'lunas';
                if ($monthIdx >= 10 && $siswaIdx % 8 == 0) {
                    $status = 'belum_bayar'; // arrears
                } elseif ($monthIdx >= 11 && $siswaIdx % 6 == 0) {
                    $status = 'sebagian'; // partial
                }

                // SPP Bill
                $totalPaidSpp = ($status === 'lunas') ? $jtSpp->default_nominal : (($status === 'sebagian') ? 150000 : 0);
                $tagihanSpp = Tagihan::firstOrCreate([
                    'siswa_id' => $siswa->id,
                    'jenis_tagihan_id' => $jtSpp->id,
                    'tahun_ajaran_id' => $tahunAjaran->id,
                    'bulan' => $m['nama'],
                ], [
                    'nominal' => $jtSpp->default_nominal,
                    'total_dibayar' => $totalPaidSpp,
                    'status' => $status,
                    'jatuh_tempo' => $m['date'] . '-10',
                ]);

                if ($totalPaidSpp > 0) {
                    Pembayaran::firstOrCreate([
                        'tagihan_id' => $tagihanSpp->id,
                    ], [
                        'tanggal_bayar' => $m['date'] . '-' . sprintf('%02d', rand(1, 9)),
                        'nominal_dibayar' => $totalPaidSpp,
                        'metode_bayar' => $methods[rand(0, 3)],
                        'petugas_id' => $userFinance->id,
                    ]);
                }

                // Infaq (Voluntary payment: only created if paid / 'lunas')
                if ($status === 'lunas') {
                    $tagihanInfaq = Tagihan::firstOrCreate([
                        'siswa_id' => $siswa->id,
                        'jenis_tagihan_id' => $jtInfaq->id,
                        'tahun_ajaran_id' => $tahunAjaran->id,
                        'bulan' => $m['nama'],
                    ], [
                        'nominal' => $jtInfaq->default_nominal,
                        'total_dibayar' => $jtInfaq->default_nominal,
                        'status' => 'lunas',
                        'jatuh_tempo' => $m['date'] . '-15',
                    ]);

                    Pembayaran::firstOrCreate([
                        'tagihan_id' => $tagihanInfaq->id,
                    ], [
                        'tanggal_bayar' => $m['date'] . '-' . sprintf('%02d', rand(10, 14)),
                        'nominal_dibayar' => $jtInfaq->default_nominal,
                        'metode_bayar' => $methods[rand(0, 3)],
                        'petugas_id' => $userFinance->id,
                    ]);
                }
            }
        }

        // 8. Seed Operational Expenditures (Pengeluaran)
        $expenseCategories = KategoriPengeluaran::all();
        $catInternet = $expenseCategories->where('nama', 'Lainnya')->first(); // fallback
        $catSosial = $expenseCategories->where('nama', 'Sosial')->first();
        
        // Find specific categories or fall back
        $catListrik = KategoriPengeluaran::firstOrCreate(['nama' => 'Listrik & Air'], ['jenis' => 'operasional']);
        $catInternet = KategoriPengeluaran::firstOrCreate(['nama' => 'Internet & Telepon'], ['jenis' => 'operasional']);
        $catAtk = KategoriPengeluaran::firstOrCreate(['nama' => 'ATK & Fotokopi'], ['jenis' => 'operasional']);
        $catPemeliharaan = KategoriPengeluaran::firstOrCreate(['nama' => 'Pemeliharaan Gedung'], ['jenis' => 'operasional']);

        foreach ($months as $m) {
            // Internet (Monthly)
            Pengeluaran::create([
                'kategori_pengeluaran_id' => $catInternet->id,
                'jumlah' => 350000.00,
                'tanggal' => $m['date'] . '-03',
                'keterangan' => 'Tagihan Internet IndiHome Wifi Sekolah periode ' . $m['nama'] . ' ' . $m['tahun'],
                'petugas_id' => $userFinance->id,
            ]);

            // Listrik & Air (Monthly)
            Pengeluaran::create([
                'kategori_pengeluaran_id' => $catListrik->id,
                'jumlah' => rand(1100000, 1400000),
                'tanggal' => $m['date'] . '-10',
                'keterangan' => 'Pembayaran listrik PLN & air PDAM sekolah periode ' . $m['nama'] . ' ' . $m['tahun'],
                'petugas_id' => $userFinance->id,
            ]);

            // ATK (Monthly)
            Pengeluaran::create([
                'kategori_pengeluaran_id' => $catAtk->id,
                'jumlah' => rand(400000, 600000),
                'tanggal' => $m['date'] . '-15',
                'keterangan' => 'Pembelian ATK, kertas HVS, spidol, tinta printer periode ' . $m['nama'] . ' ' . $m['tahun'],
                'petugas_id' => $userFinance->id,
            ]);

            // Irregular Pemeliharaan
            if (rand(0, 1) === 1) {
                Pengeluaran::create([
                    'kategori_pengeluaran_id' => $catPemeliharaan->id,
                    'jumlah' => rand(1500000, 3000000),
                    'tanggal' => $m['date'] . '-22',
                    'keterangan' => 'Servis AC ruang guru dan pengecatan gerbang sekolah',
                    'petugas_id' => $userFinance->id,
                ]);
            }

            // 8b. Seed Non-SPP Cash Inflow Records (PemasukanKas: Infaq Subuh, Sedekah, Maghrib Mengaji)
            PemasukanKas::create([
                'kategori' => 'Infaq Subuh',
                'jumlah' => rand(250000, 600000),
                'tanggal' => $m['date'] . '-07',
                'keterangan' => 'Kotak Infaq Jamaah Subuh Masjid Sekolah - pekan pertama ' . $m['nama'],
                'petugas_id' => $userFinance->id,
            ]);

            PemasukanKas::create([
                'kategori' => 'Sedekah Maghrib Mengaji',
                'jumlah' => rand(300000, 750000),
                'tanggal' => $m['date'] . '-18',
                'keterangan' => 'Donasi Wali Murid program Maghrib Mengaji & Tahfizh Quran ' . $m['nama'],
                'petugas_id' => $userFinance->id,
            ]);

            PemasukanKas::create([
                'kategori' => 'Donasi Perorangan',
                'jumlah' => rand(500000, 1500000),
                'tanggal' => $m['date'] . '-25',
                'keterangan' => 'Sumbangan H. Ahmad Subarkah (Donatur) untuk fasilitas sekolah',
                'petugas_id' => $userFinance->id,
            ]);
        }

        // 8c. Seed Budget Submissions (PengajuanDana)
        PengajuanDana::create([
            'judul' => 'Pengadaan Buku Pengayaan Kurikulum Merdeka SD',
            'pemohon_id' => $userFinance->id,
            'kategori' => 'Pembelian Buku',
            'nominal' => 750000.00, // < 1 JT -> cukup Koordinator
            'tanggal_pengajuan' => now()->subDays(6)->toDateString(),
            'keterangan' => 'Pembelian 15 eksemplar modul pendamping siswa SD kelas 1 & 2',
            'status' => 'disetujui',
            'tanggal_persetujuan_koordinator' => now()->subDays(5),
        ]);

        PengajuanDana::create([
            'judul' => 'Pengadaan 2 Unit Router WiFi & Kategori LAN',
            'pemohon_id' => $userFinance->id,
            'kategori' => 'Sarana Prasarana',
            'nominal' => 450000.00, // < 1 JT -> cukup Koordinator
            'tanggal_pengajuan' => now()->subDays(15)->toDateString(),
            'keterangan' => 'Peremajaan jaringan internet untuk mendukung Ujian Asesmen Nasional',
            'status' => 'direalisasi',
            'tanggal_persetujuan_koordinator' => now()->subDays(12),
        ]);

        PengajuanDana::create([
            'judul' => 'Perbaikan & Pengecatan Atap Gedung Kelas 6',
            'pemohon_id' => $userFinance->id,
            'kategori' => 'Operasional',
            'nominal' => 2800000.00, // > 1 JT -> Koordinator & Kepala Yayasan
            'tanggal_pengajuan' => now()->subDays(5)->toDateString(),
            'keterangan' => 'Perbaikan kebocoran genteng dan pengecatan dinding kelas',
            'status' => 'disetujui',
            'tanggal_persetujuan_koordinator' => now()->subDays(3),
            'tanggal_persetujuan_kepala_yayasan' => now()->subDays(1),
        ]);

        PengajuanDana::create([
            'judul' => 'Pengadaan Seragam Kontingen Lomba PMR & Pramuka',
            'pemohon_id' => $userFinance->id,
            'kategori' => 'Seragam',
            'nominal' => 1850000.00, // > 1 JT
            'tanggal_pengajuan' => now()->subDays(3)->toDateString(),
            'keterangan' => 'Pengadaan 20 stel kaos seragam perlombaan tingkat kabupaten',
            'status' => 'menunggu_kepala_yayasan',
            'tanggal_persetujuan_koordinator' => now()->subDays(2),
        ]);

        // 9. Seed Dana BOS Transactions
        $catBos = KategoriPengeluaran::where('nama', 'Dana BOS')->first();
        // BOS Stage 1 Masuk (August 2025)
        DanaBos::create([
            'tahun_ajaran_id' => $tahunAjaran->id,
            'jenis' => 'masuk',
            'tanggal' => '2025-08-05',
            'nominal' => 45000000.00,
            'kategori' => 'BOS Reguler Tahap I',
            'keterangan' => 'Pencairan Dana BOS Reguler Semester Ganjil',
        ]);

        // BOS Stage 2 Masuk (February 2026)
        DanaBos::create([
            'tahun_ajaran_id' => $tahunAjaran->id,
            'jenis' => 'masuk',
            'tanggal' => '2026-02-12',
            'nominal' => 45000000.00,
            'kategori' => 'BOS Reguler Tahap II',
            'keterangan' => 'Pencairan Dana BOS Reguler Semester Genap',
        ]);

        // BOS Keluar (Belanja Buku, Laptop)
        DanaBos::create([
            'tahun_ajaran_id' => $tahunAjaran->id,
            'jenis' => 'keluar',
            'tanggal' => '2025-08-20',
            'nominal' => 15000000.00,
            'kategori' => 'Belanja Buku Perpustakaan',
            'keterangan' => 'Pembelian buku paket Kurikulum Merdeka SD kelas 1 & 4',
        ]);

        DanaBos::create([
            'tahun_ajaran_id' => $tahunAjaran->id,
            'jenis' => 'keluar',
            'tanggal' => '2026-03-05',
            'nominal' => 18000000.00,
            'kategori' => 'Belanja Chromebook & Media Pembelajaran',
            'keterangan' => 'Pengadaan 4 Unit Chromebook untuk Asesmen Nasional',
        ]);

        // 10. Seed Peminjaman / Kasbon Guru
        // Loan for Budi
        $loanBudi = Peminjaman::create([
            'guru_id' => $gurus['budi']->id,
            'tanggal_pinjam' => '2025-10-05',
            'nominal' => 3000000.00,
            'tenor_bulan' => 10,
            'cicilan_per_bulan' => 300000.00,
            'sisa_pinjaman' => 300000.00, // already paid 9 installments (repaid 2,700,000)
            'status' => 'berjalan',
        ]);

        // Loan for Lutfi
        $loanLutfi = Peminjaman::create([
            'guru_id' => $gurus['lutfi']->id,
            'tanggal_pinjam' => '2026-02-10',
            'nominal' => 2000000.00,
            'tenor_bulan' => 5,
            'cicilan_per_bulan' => 400000.00,
            'sisa_pinjaman' => 0.00, // Paid off (February, March, April, May, June)
            'status' => 'lunas',
        ]);

        // Loan for Hasan
        $loanHasan = Peminjaman::create([
            'guru_id' => $gurus['hasan']->id,
            'tanggal_pinjam' => '2026-05-15',
            'nominal' => 5000000.00,
            'tenor_bulan' => 10,
            'cicilan_per_bulan' => 500000.00,
            'sisa_pinjaman' => 4000000.00, // Repaid 2 installments (May, June)
            'status' => 'berjalan',
        ]);

        // 11. Seed Teacher Salaries (Gaji Guru) - Jan 2026 to Jun 2026
        $catGaji = KategoriPengeluaran::where('nama', 'Gaji Guru')->first();
        $salaryMonths = [
            ['nama' => 'Januari', 'tahun' => 2026, 'date' => '2026-01-25'],
            ['nama' => 'Februari', 'tahun' => 2026, 'date' => '2026-02-25'],
            ['nama' => 'Maret', 'tahun' => 2026, 'date' => '2026-03-25'],
            ['nama' => 'April', 'tahun' => 2026, 'date' => '2026-04-25'],
            ['nama' => 'Mei', 'tahun' => 2026, 'date' => '2026-05-25'],
            ['nama' => 'Juni', 'tahun' => 2026, 'date' => '2026-06-25'],
        ];

        foreach ($salaryMonths as $sm) {
            foreach ($gurus as $uname => $guru) {
                $gajiPokok = 2500000.00;
                $insentifBpjs = 150000.00;
                $insentifMaghrib = 200000.00;
                $potonganLainnya = 0.00;

                // Determine loans active during that month
                $potonganPeminjaman = 0.00;
                if ($uname === 'budi') {
                    $potonganPeminjaman = 300000.00; // Loan budget
                } elseif ($uname === 'lutfi' && $sm['tahun'] == 2026 && in_array($sm['nama'], ['Februari', 'Maret', 'April', 'Mei', 'Juni'])) {
                    $potonganPeminjaman = 400000.00;
                } elseif ($uname === 'hasan' && $sm['tahun'] == 2026 && in_array($sm['nama'], ['Mei', 'Juni'])) {
                    $potonganPeminjaman = 500000.00;
                }

                $totalDiterima = ($gajiPokok + $insentifBpjs + $insentifMaghrib) - ($potonganPeminjaman + $potonganLainnya);

                // Create Expenditure record first
                $exp = Pengeluaran::create([
                    'kategori_pengeluaran_id' => $catGaji->id,
                    'jumlah' => $totalDiterima,
                    'tanggal' => $sm['date'],
                    'keterangan' => "Gaji Guru: {$guru->user->nama} - Periode {$sm['nama']} {$sm['tahun']}",
                    'petugas_id' => $userFinance->id,
                ]);

                // Create salary record
                GajiGuru::firstOrCreate([
                    'guru_id' => $guru->id,
                    'bulan' => $sm['nama'],
                    'tahun' => $sm['tahun'],
                ], [
                    'pengeluaran_id' => $exp->id,
                    'gaji_pokok' => $gajiPokok,
                    'insentif_bpjs' => $insentifBpjs,
                    'insentif_maghrib_mengaji' => $insentifMaghrib,
                    'potongan_peminjaman' => $potonganPeminjaman,
                    'potongan_lainnya' => $potonganLainnya,
                    'total_diterima' => $totalDiterima,
                    'tanggal_bayar' => $sm['date'],
                    'status' => 'dibayar',
                ]);
            }
        }

        // Seed July 2026 Gaji as DRAFT
        foreach ($gurus as $uname => $guru) {
            $gajiPokok = 2500000.00;
            $insentifBpjs = 150000.00;
            $insentifMaghrib = 200000.00;
            $potonganLainnya = 0.00;

            $potonganPeminjaman = 0.00;
            if ($uname === 'budi') {
                $potonganPeminjaman = 300000.00;
            } elseif ($uname === 'hasan') {
                $potonganPeminjaman = 500000.00;
            }

            $totalDiterima = ($gajiPokok + $insentifBpjs + $insentifMaghrib) - ($potonganPeminjaman + $potonganLainnya);

            GajiGuru::firstOrCreate([
                'guru_id' => $guru->id,
                'bulan' => 'Juli',
                'tahun' => 2026,
            ], [
                'gaji_pokok' => $gajiPokok,
                'insentif_bpjs' => $insentifBpjs,
                'insentif_maghrib_mengaji' => $insentifMaghrib,
                'potongan_peminjaman' => $potonganPeminjaman,
                'potongan_lainnya' => $potonganLainnya,
                'total_diterima' => $totalDiterima,
                'tanggal_bayar' => '2026-07-25',
                'status' => 'draft',
            ]);
        }

        // 12. Seed Notifications
        foreach ($siswaModels as $idx => $siswa) {
            Notifikasi::create([
                'user_id' => $siswa->user_id,
                'siswa_id' => $siswa->id,
                'judul' => 'Tagihan Terbit',
                'isi_pesan' => 'Tagihan SPP dan Infaq untuk bulan Juli 2026 telah terbit. Silakan lakukan pembayaran sebelum tanggal jatuh tempo.',
                'jenis' => 'tagihan',
                'channel' => 'in_app',
                'status_kirim' => 'terkirim',
                'dikirim_pada' => '2026-07-01 08:00:00',
            ]);

            if ($idx % 3 === 0) {
                Notifikasi::create([
                    'user_id' => $siswa->user_id,
                    'siswa_id' => $siswa->id,
                    'judul' => 'Pembayaran SPP Diterima',
                    'isi_pesan' => 'Terima kasih, pembayaran SPP bulan Juni 2026 sebesar Rp 350.000 telah kami terima.',
                    'jenis' => 'tagihan',
                    'channel' => 'in_app',
                    'status_kirim' => 'terkirim',
                    'dikirim_pada' => '2026-06-08 10:15:00',
                    'dibaca_pada' => '2026-06-08 12:30:00',
                ]);
            }
        }

        // 13. Seed Grades & Rapor for Semester Ganjil (2025/2026)
        $knUlangan = KomponenNilai::where('nama', 'like', '%UH%')->first();
        $knUts = KomponenNilai::where('nama', 'like', '%PTS%')->first();
        $knUas = KomponenNilai::where('nama', 'like', '%PAS%')->first();

        foreach ($siswaModels as $siswa) {
            $catatanWali = 'Ananda ' . $siswa->user->nama . ' menunjukkan peningkatan belajar yang luar biasa. Pertahankan prestasi ini di semester depan!';
            $rapor = Rapor::firstOrCreate([
                'siswa_id' => $siswa->id,
                'semester_id' => $semesterGanjil->id,
            ], [
                'kelas_id' => $siswa->kelas_id,
                'catatan_wali_kelas' => $catatanWali,
                'tanggal_terbit' => '2025-12-19',
            ]);

            foreach ($mapelModels as $mapel) {
                // Determine randomized scores for kognitif, psikomotor, afektif, keagamaan
                $cog = rand(75, 95);
                $psy = rand(78, 93);
                $aff = rand(80, 95);
                $rel = rand(82, 98);
                $avg = round(($cog + $psy + $aff + $rel) / 4, 2);

                $pred = 'B';
                if ($avg >= 90) $pred = 'A';
                elseif ($avg < 80) $pred = 'C';

                RaporDetail::firstOrCreate([
                    'rapor_id' => $rapor->id,
                    'mapel_id' => $mapel->id,
                ], [
                    'nilai_pengetahuan' => $cog,
                    'nilai_keterampilan' => $psy,
                    'nilai_sikap' => $aff,
                    'nilai_keagamaan' => $rel,
                    'nilai_akhir' => $avg,
                    'predikat' => $pred,
                ]);

                // Create underlying daily marks in `nilai`
                $className = $siswa->kelas->nama_kelas;
                $gmk = $gmkLookup[$className][$mapel->nama_mapel] ?? null;
                $teacherId = $gmk ? $gmk->guru_id : ($siswa->kelas->guru_umum_id ?? $gurus['budi']->id);

                if ($knUlangan) {
                    Nilai::firstOrCreate([
                        'siswa_id' => $siswa->id,
                        'mapel_id' => $mapel->id,
                        'semester_id' => $semesterGanjil->id,
                        'komponen_nilai_id' => $knUlangan->id,
                    ], [
                        'guru_id' => $teacherId,
                        'kelas_id' => $siswa->kelas_id,
                        'tanggal' => '2025-09-12',
                        'nilai' => $cog - 2,
                    ]);
                }

                if ($knUts) {
                    Nilai::firstOrCreate([
                        'siswa_id' => $siswa->id,
                        'mapel_id' => $mapel->id,
                        'semester_id' => $semesterGenap->id,
                        'komponen_nilai_id' => $knUts->id,
                    ], [
                        'guru_id' => $teacherId,
                        'kelas_id' => $siswa->kelas_id,
                        'tanggal' => '2026-03-15',
                        'nilai' => rand(75, 95),
                    ]);
                }
            }
        }

        // 14. Seed Attendance History (AbsensiSiswa) for last 20 weekdays
        $days = [];
        $current = Carbon::now();
        while (count($days) < 20) {
            if (!$current->isWeekend()) {
                $days[] = $current->toDateString();
            }
            $current->subDay();
        }
        $days = array_reverse($days);

        foreach ($siswaModels as $siswa) {
            foreach ($days as $day) {
                // Determine status: 90% hadir, 7% izin, 3% tidak_hadir
                $rand = rand(1, 100);
                $status = 'hadir';
                $catatan = null;
                if ($rand > 97) {
                    $status = 'tidak_hadir';
                    $catatan = 'Alasan tidak jelas';
                } elseif ($rand > 90) {
                    $status = 'izin';
                    $catatan = 'Sakit / Keperluan keluarga';
                }

                if ($siswa->kelas_id) {
                    $className = $siswa->kelas->nama_kelas;
                    $gmk = $gmkLookup[$className]['Matematika'] ?? null;
                    $teacherId = $gmk ? $gmk->guru_id : ($siswa->kelas->guru_umum_id ?? $gurus['budi']->id);

                    \App\Models\AbsensiSiswa::firstOrCreate([
                        'siswa_id' => $siswa->id,
                        'kelas_id' => $siswa->kelas_id,
                        'tanggal' => $day,
                    ], [
                        'guru_id' => $teacherId,
                        'status' => $status,
                        'catatan' => $catatan,
                    ]);
                }
            }
        }

        // 15. Seed Activity Logs for Default Student (siswa_id of defaultSiswa)
        if ($defaultSiswa) {
            $causerTeacher = $defaultGuruModel->user ?? $gurus['budi']->user;
            $causerFinance = $userFinance;

            $activities = [
                [
                    'description' => 'Penempatan kelas akademik oleh Tata Usaha',
                    'subject_type' => 'App\Models\SiswaKelas',
                    'subject_id' => 1,
                    'event' => 'created',
                    'causer_type' => 'App\Models\User',
                    'causer_id' => 1,
                    'created_at' => Carbon::now()->subMonths(6)->toDateString() . ' 08:30:00',
                ],
                [
                    'description' => 'Penerbitan tagihan SPP bulanan',
                    'subject_type' => 'App\Models\Tagihan',
                    'subject_id' => 1,
                    'event' => 'created',
                    'causer_type' => 'App\Models\User',
                    'causer_id' => $causerFinance->id,
                    'created_at' => Carbon::now()->subMonths(3)->toDateString() . ' 09:00:00',
                ],
                [
                    'description' => 'Pengisian absensi harian kelas 7A',
                    'subject_type' => 'App\Models\AbsensiSiswa',
                    'subject_id' => 1,
                    'event' => 'created',
                    'causer_type' => 'App\Models\User',
                    'causer_id' => $causerTeacher->id,
                    'created_at' => Carbon::now()->subDays(5)->toDateString() . ' 07:45:00',
                ],
                [
                    'description' => 'Penginputan nilai Ulangan Harian Matematika',
                    'subject_type' => 'App\Models\Nilai',
                    'subject_id' => 1,
                    'event' => 'created',
                    'causer_type' => 'App\Models\User',
                    'causer_id' => $causerTeacher->id,
                    'created_at' => Carbon::now()->subDays(3)->toDateString() . ' 14:20:00',
                ],
                [
                    'description' => 'Pembayaran tagihan SPP Lunas',
                    'subject_type' => 'App\Models\Pembayaran',
                    'subject_id' => 1,
                    'event' => 'created',
                    'causer_type' => 'App\Models\User',
                    'causer_id' => $causerFinance->id,
                    'created_at' => Carbon::now()->subDays(2)->toDateString() . ' 10:15:00',
                ],
                [
                    'description' => 'Penerbitan Rapor Hasil Belajar Semester Ganjil',
                    'subject_type' => 'App\Models\Rapor',
                    'subject_id' => 1,
                    'event' => 'created',
                    'causer_type' => 'App\Models\User',
                    'causer_id' => $causerTeacher->id,
                    'created_at' => Carbon::now()->subDays(1)->toDateString() . ' 16:00:00',
                ],
            ];

            foreach ($activities as $act) {
                DB::table('activity_log')->insert(array_merge($act, [
                    'siswa_id' => $defaultSiswa->id,
                    'ip_address' => '127.0.0.1',
                    'updated_at' => $act['created_at'],
                ]));
            }
        }

        // 16. Seed AbsensiGuru for the default teacher (guru)
        if ($defaultGuruModel) {
            foreach ($days as $day) {
                // Determine status: 95% hadir, 5% izin
                $rand = rand(1, 100);
                $status = 'hadir';
                $waktuDatang = '07:15:00';
                $waktuPulang = '15:30:00';
                if ($rand > 95) {
                    $status = 'izin';
                    $waktuDatang = null;
                    $waktuPulang = null;
                }

                \App\Models\AbsensiGuru::firstOrCreate([
                    'guru_id' => $defaultGuruModel->id,
                    'tanggal' => $day,
                ], [
                    'waktu_datang' => $waktuDatang,
                    'waktu_pulang' => $waktuPulang,
                    'status' => $status,
                    'catatan' => $status === 'izin' ? 'Sakit / Keperluan dinas' : null,
                ]);
            }
        }

        // 17. Seed Ekstrakurikuler & SiswaEkstrakurikuler
        $gurus = Guru::all();
        if ($gurus->count() > 0) {
            $ekskulsData = [
                ['nama' => 'Pramuka / Hizbul Wathan', 'deskripsi' => 'Pengembangan karakter, kepemimpinan, dan kecakapan kepanduan.'],
                ['nama' => 'Tahfidz Club', 'deskripsi' => 'Program pendalaman dan murojaah hafalan Al-Qur\'an santri.'],
                ['nama' => 'Pencak Silat Tapak Suci', 'deskripsi' => 'Olahraga seni beladiri tradisional dan kebugaran jasmani.'],
                ['nama' => 'Karya Ilmiah Remaja (KIR)', 'deskripsi' => 'Pengembangan riset, karya tulis ilmiah, dan eksplorasi sains.'],
                ['nama' => 'Futsal & Olahraga', 'deskripsi' => 'Pengembangan minat bakat olahraga sepak bola mini.'],
            ];

            $createdEkskuls = [];
            foreach ($ekskulsData as $idx => $eData) {
                $pembina = $gurus[$idx % $gurus->count()];
                $ekskul = Ekstrakurikuler::firstOrCreate(
                    ['nama' => $eData['nama']],
                    [
                        'pembina_guru_id' => $pembina->id,
                        'deskripsi' => $eData['deskripsi'],
                    ]
                );
                $createdEkskuls[] = $ekskul;
            }

            // Assign active students to extracurriculars
            $activeStudents = Siswa::where('status', 'aktif')->limit(20)->get();
            $predikats = ['A', 'A', 'B', 'B', 'A'];
            foreach ($activeStudents as $sIdx => $st) {
                $ekskulTarget = $createdEkskuls[$sIdx % count($createdEkskuls)];
                SiswaEkstrakurikuler::firstOrCreate([
                    'siswa_id' => $st->id,
                    'ekstrakurikuler_id' => $ekskulTarget->id,
                    'semester_id' => $semesterGanjil->id,
                ], [
                    'predikat' => $predikats[$sIdx % count($predikats)],
                    'catatan' => 'Menunjukkan kedisiplinan dan keaktifan yang sangat baik.',
                ]);
            }
        }

        // 18. Seed JadwalPiketGuru
        $hariList = ['senin', 'selasa', 'rabu', 'kamis', 'jumat'];
        if ($gurus->count() > 0) {
            foreach ($hariList as $hIdx => $hari) {
                $guruPiket = $gurus[$hIdx % $gurus->count()];
                JadwalPiketGuru::firstOrCreate([
                    'guru_id' => $guruPiket->id,
                    'hari' => $hari,
                    'semester_id' => $semesterGanjil->id,
                ]);
            }
        }

        // 19. Seed BobotNilaiGuru
        $guruMapelKelases = GuruMapelKelas::limit(10)->get();
        $komponens = KomponenNilai::all();
        if ($guruMapelKelases->count() > 0 && $komponens->count() > 0) {
            foreach ($guruMapelKelases as $gmk) {
                foreach ($komponens as $k) {
                    $defaultBobot = match ($k->kode) {
                        'UH' => 30.00,
                        'UTS' => 30.00,
                        'UAS' => 40.00,
                        default => 20.00,
                    };

                    BobotNilaiGuru::firstOrCreate([
                        'guru_mapel_kelas_id' => $gmk->id,
                        'komponen_nilai_id' => $k->id,
                    ], [
                        'bobot' => $defaultBobot,
                    ]);
                }
            }
        }

        // 20. Seed PengajuanKoreksiNilai
        $sampleNilaiList = Nilai::with('guru')->limit(5)->get();
        $userKoordinator = User::whereHas('role', function ($q) {
            $q->where('nama', 'koordinator');
        })->first();

        if ($sampleNilaiList->count() > 0) {
            $reasons = [
                'Salah input nilai ulangan harian #2 karena kekeliruan lembar jawaban.',
                'Siswa telah mengikuti ujian susulan dan mendapatkan perbaikan nilai.',
                'Koreksi penjumlahan bobot tugas mandiri.',
            ];

            foreach ($sampleNilaiList as $nIdx => $n) {
                $teacher = $n->guru ?? $gurus->first();
                if (!$teacher) continue;

                $status = $nIdx === 0 ? 'pending' : ($nIdx === 1 ? 'disetujui' : 'ditolak');

                PengajuanKoreksiNilai::firstOrCreate([
                    'nilai_id' => $n->id,
                    'diajukan_oleh_guru_id' => $teacher->id,
                ], [
                    'nilai_baru' => min(100, floatval($n->nilai) + 10),
                    'alasan' => $reasons[$nIdx % count($reasons)],
                    'status' => $status,
                    'disetujui_oleh_user_id' => $status !== 'pending' ? ($userKoordinator->id ?? 1) : null,
                ]);
            }
        }

        // 21. Seed Alumni Students
        $alumniData = [
            [
                'nis' => '8001',
                'nisn' => '0080000001',
                'nama' => 'Muhammad Rizky Pratama',
                'tahun_lulus' => 2025,
                'catatan' => 'Lulus Cumlaude. Melanjutkan ke SMP Negeri 1 Yogyakarta.',
            ],
            [
                'nis' => '8002',
                'nisn' => '0080000002',
                'nama' => 'Aisyah Humaira',
                'tahun_lulus' => 2025,
                'catatan' => 'Hafal 5 Juz Al-Qur\'an. Melanjutkan ke Pondok Pesantren Mu\'allimat.',
            ],
            [
                'nis' => '8003',
                'nisn' => '0080000003',
                'nama' => 'Farhan Abdillah',
                'tahun_lulus' => 2024,
                'catatan' => 'Melanjutkan ke SMP IT Abu Bakar Yogyakarta.',
            ],
            [
                'nis' => '8004',
                'nisn' => '0080000004',
                'nama' => 'Nabila Az-Zahra',
                'tahun_lulus' => 2024,
                'catatan' => 'Juara 1 Lomba MTQ Tingkat Kabupaten. Melanjutkan ke MTsN 1 Yogyakarta.',
            ],
        ];

        foreach ($alumniData as $a) {
            $userAlumni = User::firstOrCreate([
                'username' => strtolower(str_replace(' ', '', $a['nama'])),
            ], [
                'nama' => $a['nama'],
                'email' => strtolower(str_replace(' ', '', $a['nama'])) . '@alumni.yayasan.or.id',
                'password' => Hash::make('alumni123'),
                'role_id' => $roleMurid->id,
                'no_hp' => '089' . rand(10000000, 99999999),
                'alamat' => 'Sleman, Yogyakarta',
                'status' => 'nonaktif',
            ]);

            Siswa::firstOrCreate([
                'nis' => $a['nis'],
            ], [
                'user_id' => $userAlumni->id,
                'nisn' => $a['nisn'],
                'jenis_kelamin' => rand(0, 1) ? 'L' : 'P',
                'tempat_lahir' => 'Yogyakarta',
                'tanggal_lahir' => '2010-05-12',
                'alamat' => 'Sleman, Yogyakarta',
                'nama_wali' => 'Orang Tua ' . $a['nama'],
                'no_hp_wali' => '0812' . rand(10000000, 99999999),
                'kelas_id' => null,
                'tanggal_masuk' => '2020-07-15',
                'status' => 'lulus',
                'tahun_lulus' => $a['tahun_lulus'],
                'catatan_alumni' => $a['catatan'],
            ]);
        }
    }
}
