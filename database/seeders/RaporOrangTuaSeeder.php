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
use App\Models\KomponenNilai;
use App\Models\Nilai;
use App\Models\Rapor;
use App\Models\RaporDetail;
use App\Models\JenisTagihan;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\Ekstrakurikuler;
use App\Models\SiswaEkstrakurikuler;
use App\Models\Notifikasi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class RaporOrangTuaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Roles
        $roleMurid = Role::firstOrCreate(['nama' => 'murid']);
        $roleGuru = Role::firstOrCreate(['nama' => 'guru']);
        $roleFinance = Role::firstOrCreate(['nama' => 'finance']);

        // 2. Finance User for payment recording
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

        // 3. Tahun Ajaran & Semester
        TahunAjaran::query()->update(['status_aktif' => false]);
        $tahunAjaran = TahunAjaran::firstOrCreate([
            'nama' => '2025/2026',
        ], [
            'status_aktif' => true,
        ]);
        $tahunAjaran->update(['status_aktif' => true]);

        Semester::where('tahun_ajaran_id', $tahunAjaran->id)->update(['status_aktif' => false]);
        $semesterGanjil = Semester::firstOrCreate([
            'tahun_ajaran_id' => $tahunAjaran->id,
            'semester' => 'ganjil',
        ], [
            'tanggal_mulai' => '2025-07-15',
            'tanggal_selesai' => '2025-12-20',
            'status_aktif' => true,
        ]);
        $semesterGanjil->update(['status_aktif' => true]);

        // 4. Guru
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

        $guru = Guru::firstOrCreate([
            'nip' => '1234567890',
        ], [
            'user_id' => $userGuru->id,
            'jenis_guru' => 'umum',
            'no_hp' => $userGuru->no_hp,
            'alamat' => $userGuru->alamat,
            'tanggal_masuk' => '2020-01-01',
            'status_aktif' => true,
        ]);

        // 5. Kelas
        $kelas = Kelas::firstOrCreate([
            'nama_kelas' => '1A',
            'semester_id' => $semesterGanjil->id,
        ], [
            'tingkat' => '1',
            'guru_umum_id' => $guru->id,
            'guru_tahfidz_id' => $guru->id,
        ]);

        // 6. User Siswa / Orang Tua (Utama)
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

        $siswa = Siswa::firstOrCreate([
            'nis' => '9999',
        ], [
            'user_id' => $userSiswa->id,
            'nisn' => '0099999999',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Yogyakarta',
            'tanggal_lahir' => '2016-01-01',
            'alamat' => $userSiswa->alamat,
            'nama_wali' => 'Bapak/Ibu Wali Siswa',
            'no_hp_wali' => '081234567894',
            'kelas_id' => $kelas->id,
            'tanggal_masuk' => '2025-07-01',
            'status' => 'aktif',
        ]);

        // Update kelas_id if needed
        if (!$siswa->kelas_id) {
            $siswa->update(['kelas_id' => $kelas->id]);
        }

        SiswaKelas::firstOrCreate([
            'siswa_id' => $siswa->id,
            'semester_id' => $semesterGanjil->id,
        ], [
            'kelas_id' => $kelas->id,
            'status' => 'aktif',
        ]);

        // 7. Ensure SPP is LUNAS (So `hasOutstanding` is false in RaporNilai)
        $jtSpp = JenisTagihan::where('nama', 'SPP')
            ->orWhere('nama', 'like', '%SPP%')
            ->first();

        if (!$jtSpp) {
            $jtSpp = JenisTagihan::create([
                'nama' => 'SPP',
                'kategori' => 'rutin',
                'default_nominal' => 350000.00,
                'is_blocking' => true,
            ]);
        }

        $months = [
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'
        ];

        // Collect target students (the main student + any demo students if existing)
        $targetStudents = Siswa::whereIn('id', [$siswa->id])->get();
        if ($targetStudents->count() === 1) {
            $extraStudents = Siswa::where('status', 'aktif')->get();
            $targetStudents = $targetStudents->merge($extraStudents)->unique('id');
        }

        foreach ($targetStudents as $st) {
            foreach ($months as $mIdx => $mName) {
                $dueDate = ($mIdx < 6 ? '2025-' : '2026-') . sprintf('%02d', ($mIdx < 6 ? $mIdx + 7 : $mIdx - 5)) . '-10';
                
                $tagihan = Tagihan::firstOrCreate([
                    'siswa_id' => $st->id,
                    'jenis_tagihan_id' => $jtSpp->id,
                    'tahun_ajaran_id' => $tahunAjaran->id,
                    'bulan' => $mName,
                ], [
                    'nominal' => 350000.00,
                    'total_dibayar' => 350000.00,
                    'status' => 'lunas',
                    'jatuh_tempo' => $dueDate,
                ]);

                if ($tagihan->status !== 'lunas') {
                    $tagihan->update([
                        'status' => 'lunas',
                        'total_dibayar' => $tagihan->nominal > 0 ? $tagihan->nominal : 350000.00,
                    ]);
                }

                Pembayaran::firstOrCreate([
                    'tagihan_id' => $tagihan->id,
                ], [
                    'tanggal_bayar' => Carbon::parse($dueDate)->subDays(3)->toDateString(),
                    'nominal_dibayar' => 350000.00,
                    'metode_bayar' => 'transfer',
                    'petugas_id' => $userFinance->id,
                ]);
            }

            // Also mark any other blocking bills for this student as lunas
            Tagihan::where('siswa_id', $st->id)
                ->whereHas('jenisTagihan', function ($q) {
                    $q->where('is_blocking', true);
                })
                ->where('status', '!=', 'lunas')
                ->each(function ($t) {
                    $t->update([
                        'status' => 'lunas',
                        'total_dibayar' => $t->nominal,
                    ]);
                });
        }

        // 8. Mata Pelajaran (Umum & Tahfizh)
        $mapels = [
            ['nama_mapel' => 'Matematika', 'jenis' => 'umum', 'deskripsi' => 'Mata pelajaran matematika SD'],
            ['nama_mapel' => 'Bahasa Indonesia', 'jenis' => 'umum', 'deskripsi' => 'Bahasa dan Sastra Indonesia SD'],
            ['nama_mapel' => 'IPA', 'jenis' => 'umum', 'deskripsi' => 'Ilmu Pengetahuan Alam SD'],
            ['nama_mapel' => 'IPS', 'jenis' => 'umum', 'deskripsi' => 'Ilmu Pengetahuan Sosial SD'],
            ['nama_mapel' => 'Tahfidz Al-Quran', 'jenis' => 'tahfidz', 'deskripsi' => 'Hafalan dan Murajaah Al-Quran'],
            ['nama_mapel' => 'Pendidikan Agama Islam', 'jenis' => 'tahfidz', 'deskripsi' => 'Pendidikan Agama dan Morals'],
        ];

        $mapelModels = [];
        foreach ($mapels as $m) {
            $mapel = MataPelajaran::updateOrCreate(['nama_mapel' => $m['nama_mapel']], $m);
            $mapelModels[$m['nama_mapel']] = $mapel;

            GuruMapelKelas::firstOrCreate([
                'guru_id' => $guru->id,
                'kelas_id' => $kelas->id,
                'mapel_id' => $mapel->id,
                'semester_id' => $semesterGanjil->id,
            ]);
        }

        // 9. Komponen Nilai
        $knUh = KomponenNilai::firstOrCreate(['nama' => 'Ulangan Harian (UH)'], ['kategori' => 'pengetahuan', 'bobot' => 30]);
        $knPts = KomponenNilai::firstOrCreate(['nama' => 'Penilaian Tengah Semester (PTS)'], ['kategori' => 'pengetahuan', 'bobot' => 30]);
        $knPas = KomponenNilai::firstOrCreate(['nama' => 'Penilaian Akhir Semester (PAS)'], ['kategori' => 'pengetahuan', 'bobot' => 40]);

        // Seed daily marks in `nilai` table
        foreach ($mapelModels as $mapel) {
            Nilai::firstOrCreate([
                'siswa_id' => $siswa->id,
                'mapel_id' => $mapel->id,
                'semester_id' => $semesterGanjil->id,
                'komponen_nilai_id' => $knUh->id,
            ], [
                'guru_id' => $guru->id,
                'kelas_id' => $kelas->id,
                'tanggal' => '2025-09-15',
                'nilai' => 88.00,
                'catatan' => 'Sangat baik',
            ]);

            Nilai::firstOrCreate([
                'siswa_id' => $siswa->id,
                'mapel_id' => $mapel->id,
                'semester_id' => $semesterGanjil->id,
                'komponen_nilai_id' => $knPts->id,
            ], [
                'guru_id' => $guru->id,
                'kelas_id' => $kelas->id,
                'tanggal' => '2025-10-20',
                'nilai' => 90.00,
                'catatan' => 'Pertahankan',
            ]);

            Nilai::firstOrCreate([
                'siswa_id' => $siswa->id,
                'mapel_id' => $mapel->id,
                'semester_id' => $semesterGanjil->id,
                'komponen_nilai_id' => $knPas->id,
            ], [
                'guru_id' => $guru->id,
                'kelas_id' => $kelas->id,
                'tanggal' => '2025-12-10',
                'nilai' => 92.00,
                'catatan' => 'Luar biasa',
            ]);
        }

        // 10. Header Rapor (Published)
        $rapor = Rapor::updateOrCreate([
            'siswa_id' => $siswa->id,
            'semester_id' => $semesterGanjil->id,
        ], [
            'kelas_id' => $kelas->id,
            'catatan_wali_kelas' => 'Ananda Siswa Berprestasi menunjukkan ketekunan, akhlak mulia, dan prestasi akademis yang sangat memuaskan di semester ganjil ini. Pertahankan terus semangat belajarnya!',
            'tanggal_terbit' => Carbon::now()->subDays(2)->toDateString(),
        ]);

        // 11. Details Rapor
        $raporGrades = [
            'Matematika' => ['pengetahuan' => 90.00, 'keterampilan' => 88.00, 'sikap' => 92.00, 'keagamaan' => 90.00, 'akhir' => 90.00, 'predikat' => 'A'],
            'Bahasa Indonesia' => ['pengetahuan' => 92.00, 'keterampilan' => 90.00, 'sikap' => 94.00, 'keagamaan' => 91.00, 'akhir' => 91.75, 'predikat' => 'A'],
            'IPA' => ['pengetahuan' => 85.00, 'keterampilan' => 87.00, 'sikap' => 90.00, 'keagamaan' => 88.00, 'akhir' => 87.50, 'predikat' => 'B'],
            'IPS' => ['pengetahuan' => 88.00, 'keterampilan' => 85.00, 'sikap' => 89.00, 'keagamaan' => 87.00, 'akhir' => 87.25, 'predikat' => 'B'],
            'Tahfidz Al-Quran' => ['pengetahuan' => 95.00, 'keterampilan' => 95.00, 'sikap' => 98.00, 'keagamaan' => 96.00, 'akhir' => 96.00, 'predikat' => 'A'],
            'Pendidikan Agama Islam' => ['pengetahuan' => 90.00, 'keterampilan' => 92.00, 'sikap' => 95.00, 'keagamaan' => 94.00, 'akhir' => 92.75, 'predikat' => 'A'],
        ];

        foreach ($raporGrades as $mapelNama => $scores) {
            if (isset($mapelModels[$mapelNama])) {
                RaporDetail::updateOrCreate([
                    'rapor_id' => $rapor->id,
                    'mapel_id' => $mapelModels[$mapelNama]->id,
                ], [
                    'nilai_pengetahuan' => $scores['pengetahuan'],
                    'nilai_keterampilan' => $scores['keterampilan'],
                    'nilai_sikap' => $scores['sikap'],
                    'nilai_keagamaan' => $scores['keagamaan'],
                    'nilai_akhir' => $scores['akhir'],
                    'predikat' => $scores['predikat'],
                ]);
            }
        }

        // 12. Ekstrakurikuler
        $ekskulPramuka = Ekstrakurikuler::firstOrCreate(
            ['nama' => 'Pramuka / Hizbul Wathan'],
            ['pembina_guru_id' => $guru->id, 'deskripsi' => 'Kepanduan dan karakter']
        );

        $ekskulTahfidz = Ekstrakurikuler::firstOrCreate(
            ['nama' => 'Tahfidz Club'],
            ['pembina_guru_id' => $guru->id, 'deskripsi' => 'Pendalaman hafalan Al-Qur\'an']
        );

        SiswaEkstrakurikuler::updateOrCreate([
            'siswa_id' => $siswa->id,
            'ekstrakurikuler_id' => $ekskulPramuka->id,
            'semester_id' => $semesterGanjil->id,
        ], [
            'predikat' => 'A',
            'catatan' => 'Sangat aktif dan disiplin dalam setiap latihan kepanduan.',
        ]);

        SiswaEkstrakurikuler::updateOrCreate([
            'siswa_id' => $siswa->id,
            'ekstrakurikuler_id' => $ekskulTahfidz->id,
            'semester_id' => $semesterGanjil->id,
        ], [
            'predikat' => 'A',
            'catatan' => 'Telah menyelesaikan target murojaah Juz 30 dengan tartil.',
        ]);

        // 13. Notifikasi Penerbitan Rapor
        Notifikasi::create([
            'user_id' => $userSiswa->id,
            'siswa_id' => $siswa->id,
            'judul' => 'Penerbitan Rapor Akademik Hasil Belajar',
            'isi_pesan' => 'Rapor Hasil Belajar Anda untuk Semester Ganjil (2025/2026) telah resmi diterbitkan oleh guru.',
            'jenis' => 'rapor_terbit',
            'channel' => 'in_app',
            'status_kirim' => 'terkirim',
            'dikirim_pada' => Carbon::now()->subDays(2),
        ]);
    }
}
