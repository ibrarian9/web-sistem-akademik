<?php

namespace Database\Seeders;

use App\Models\CapaianGuru;
use App\Models\Guru;
use App\Models\Semester;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Database\Seeder;

class CapaianGuruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeTahun = TahunAjaran::where('status_aktif', true)->first() ?? TahunAjaran::first();
        $activeSemester = Semester::where('status_aktif', true)->first() ?? Semester::first();

        $evaluator = User::whereHas('role', function ($q) {
            $q->whereIn('nama', ['super_admin', 'kepala_sekolah']);
        })->first() ?? User::first();

        $evaluatorId = $evaluator ? $evaluator->id : null;

        $gurus = Guru::with('user')->get();

        if ($gurus->isEmpty()) {
            return;
        }

        $dummyItems = [
            [
                'guru_username' => 'guru',
                'guru_name_contains' => 'Teladan',
                'items' => [
                    [
                        'judul' => 'Sertifikasi Pendidik Guru Penggerak Angkatan 10 Kemendikbudristek',
                        'kategori' => 'sertifikasi',
                        'deskripsi' => 'Program pendidikan kepemimpinan pembelajaran selama 6 bulan dengan fokus diferensiasi pembelajaran, coaching klinis, dan implementasi budaya positif di lingkungan sekolah.',
                        'link_gdrive' => 'https://drive.google.com/drive/folders/1A2bC3dE4fG5hI6jK7lM8nO9pQrStUvWx?usp=sharing',
                        'skor_nilai' => 96.00,
                        'predikat' => 'Sangat Baik',
                        'catatan_evaluasi' => 'Luar biasa, dokumen portofolio aksi nyata sangat lengkap dan berdampak nyata bagi ekosistem pembelajaran.',
                        'status_penilaian' => 'dinilai',
                        'tanggal_penilaian' => '2026-07-28',
                    ],
                    [
                        'judul' => 'Penyusunan Modul Ajar Tematik Interaktif Berbasis Canva & Wordwall',
                        'kategori' => 'pengembangan_diri',
                        'deskripsi' => 'Pembuatan 12 set modul ajar interaktif dan gamifikasi kuis untuk mata pelajaran IPAS dan Matematika kelas 3 SD.',
                        'link_gdrive' => 'https://drive.google.com/file/d/1B2c3D4e5F6g7H8i9J0k1L2m3N4o5P6qR/view?usp=sharing',
                        'skor_nilai' => 92.50,
                        'predikat' => 'Sangat Baik',
                        'catatan_evaluasi' => 'Media pembelajaran sangat kreatif dan berhasil mendongkrak keaktifan dan minat belajar santri.',
                        'status_penilaian' => 'dinilai',
                        'tanggal_penilaian' => '2026-08-05',
                    ],
                    [
                        'judul' => 'Pelatihan Pemanfaatan Artificial Intelligence (AI) untuk Efisiensi Administrasi Guru',
                        'kategori' => 'pelatihan',
                        'deskripsi' => 'Workshop 32 JP tentang pemanfaatan prompt AI dalam perancangan asesmen, rubrik penilaian, dan diferensiasi materi belajar.',
                        'link_gdrive' => 'https://drive.google.com/file/d/1XyZ9876543210AbCdEfGhIjKlMnOpQrS/view?usp=sharing',
                        'skor_nilai' => null,
                        'predikat' => null,
                        'catatan_evaluasi' => null,
                        'status_penilaian' => 'diajukan',
                        'tanggal_penilaian' => null,
                    ],
                ],
            ],
            [
                'guru_username' => 'hasan',
                'guru_name_contains' => 'Hasan Basri',
                'items' => [
                    [
                        'judul' => 'Dauroh & Sertifikasi Sanad Tajwid Matan Al-Jazariyah (Predikat Mumtaz)',
                        'kategori' => 'sertifikasi',
                        'deskripsi' => 'Pengambilan sanad talaqqi matan Al-Jazariyah 107 bait bersama Masyaikh bersanad internasional selama 40 jam pertemuan intensif.',
                        'link_gdrive' => 'https://drive.google.com/drive/folders/1C3d4E5f6G7h8I9j0K1l2M3n4O5p6Q7rS?usp=sharing',
                        'skor_nilai' => 98.00,
                        'predikat' => 'Sangat Baik',
                        'catatan_evaluasi' => 'Sangat membanggakan yayasan. Keilmuan tajwid sanad langsung diaplikasikan dengan presisi pada halaqah santri binaan.',
                        'status_penilaian' => 'dinilai',
                        'tanggal_penilaian' => '2026-07-30',
                    ],
                    [
                        'judul' => 'Penerapan Metode Talaqqi & Tikrar Mandiri pada Halaqah Juz 29',
                        'kategori' => 'capaian_kinerja',
                        'deskripsi' => 'Capaian 92% santri halaqah berhasil menyelesaikan target hafalan Surah Al-Mulk s/d Al-Mursalat dengan mutqin sebelum batas waktu semester.',
                        'link_gdrive' => 'https://drive.google.com/file/d/1D4e5F6g7H8i9J0k1L2m3N4o5P6q7R8sT/view?usp=sharing',
                        'skor_nilai' => null,
                        'predikat' => null,
                        'catatan_evaluasi' => null,
                        'status_penilaian' => 'diajukan',
                        'tanggal_penilaian' => null,
                    ],
                ],
            ],
            [
                'guru_username' => 'dewi',
                'guru_name_contains' => 'Dewi Lestari',
                'items' => [
                    [
                        'judul' => 'Workshop Metode Pengajaran Tahfidz Anak Usia Dini & Makharijul Huruf',
                        'kategori' => 'pelatihan',
                        'deskripsi' => 'Pelatihan teknik artikulasi makhraj dan sifat huruf yang menyenangkan dan adaptif untuk santri kelas awal (1 & 2 SD).',
                        'link_gdrive' => 'https://drive.google.com/drive/folders/1E5f6G7h8I9j0K1l2M3n4O5p6Q7r8S9tU?usp=sharing',
                        'skor_nilai' => 90.00,
                        'predikat' => 'Sangat Baik',
                        'catatan_evaluasi' => 'Implementasi metode sangat tepat untuk karakter anak pemula tahfizh. Lanjutkan inovasi media kartu hurufnya.',
                        'status_penilaian' => 'dinilai',
                        'tanggal_penilaian' => '2026-08-08',
                    ],
                    [
                        'judul' => 'Penyusunan Lembar Mutabaah Harian & Kartu Prestasi Tahfidz Tematik',
                        'kategori' => 'pengembangan_diri',
                        'deskripsi' => 'Desain buku panduan murajaah mandiri di rumah yang terintegrasi dengan pemantauan harian oleh orang tua santri.',
                        'link_gdrive' => 'https://drive.google.com/file/d/1F6g7H8i9J0k1L2m3N4o5P6q7R8s9T0uV/view?usp=sharing',
                        'skor_nilai' => null,
                        'predikat' => null,
                        'catatan_evaluasi' => null,
                        'status_penilaian' => 'diajukan',
                        'tanggal_penilaian' => null,
                    ],
                ],
            ],
            [
                'guru_username' => 'budi',
                'guru_name_contains' => 'Budi Santoso',
                'items' => [
                    [
                        'judul' => 'Bimtek Asesmen Diagnostik & Pembelajaran Berdiferensiasi Numerasi SD',
                        'kategori' => 'pelatihan',
                        'deskripsi' => 'Bimbingan teknis 32 JP oleh BPMP mengenai pemetaan kemampuan numerasi awal dan penyesuaian strategi pembelajaran di kelas.',
                        'link_gdrive' => 'https://drive.google.com/drive/folders/1G7h8I9j0K1l2M3n4O5p6Q7r8S9t0U1vW?usp=sharing',
                        'skor_nilai' => 87.50,
                        'predikat' => 'Baik',
                        'catatan_evaluasi' => 'Hasil asesmen diagnostik telah disosialisasikan dan diterapkan dengan tertib pada kelas yang diampu.',
                        'status_penilaian' => 'dinilai',
                        'tanggal_penilaian' => '2026-08-02',
                    ],
                    [
                        'judul' => 'Inovasi Pembelajaran Matematika Kontekstual Berbasis Alat Peraga Edukasi',
                        'kategori' => 'capaian_kinerja',
                        'deskripsi' => 'Peningkatan ketuntasan hasil belajar konsep operasi hitung pecahan dan perkalian siswa kelas 4 mencapai rata-rata kelas 88.5.',
                        'link_gdrive' => 'https://drive.google.com/file/d/1H8i9J0k1L2m3N4o5P6q7R8s9T0u1V2wX/view?usp=sharing',
                        'skor_nilai' => null,
                        'predikat' => null,
                        'catatan_evaluasi' => null,
                        'status_penilaian' => 'diajukan',
                        'tanggal_penilaian' => null,
                    ],
                ],
            ],
            [
                'guru_username' => 'lutfi',
                'guru_name_contains' => 'Lutfi Hakim',
                'items' => [
                    [
                        'judul' => 'Workshop Penulisan Karya Tulis Ilmiah & Best Practice Guru SD',
                        'kategori' => 'pelatihan',
                        'deskripsi' => 'Penyusunan naskah best practice berjudul "Peningkatan Minat Baca Siswa SD Tahfizh Melalui Gerakan Pojok Literasi Qurani".',
                        'link_gdrive' => 'https://drive.google.com/drive/folders/1I9j0K1l2M3n4O5p6Q7r8S9t0U1v2W3xY?usp=sharing',
                        'skor_nilai' => 89.00,
                        'predikat' => 'Baik',
                        'catatan_evaluasi' => 'Karya tulis sangat aplikatif dan inspiratif. Direkomendasikan untuk diajukan ke seminar nasional.',
                        'status_penilaian' => 'dinilai',
                        'tanggal_penilaian' => '2026-08-11',
                    ],
                ],
            ],
            [
                'guru_username' => 'fatma',
                'guru_name_contains' => 'Fatmawati',
                'items' => [
                    [
                        'judul' => 'Pelatihan Manajemen Kelas Ramah Anak & Pencegahan Bullying',
                        'kategori' => 'pelatihan',
                        'deskripsi' => 'Pelatihan 24 JP mengenai strategi penciptaan iklim kelas positif, regulasi emosi murid, dan disiplin positif tanpa kekerasan.',
                        'link_gdrive' => 'https://drive.google.com/file/d/1J0k1L2m3N4o5P6q7R8s9T0u1V2w3X4yZ/view?usp=sharing',
                        'skor_nilai' => 94.00,
                        'predikat' => 'Sangat Baik',
                        'catatan_evaluasi' => 'Sangat aplikatif, suasana belajar di kelas terasa semakin hangat, nyaman, dan mendukung psikologis anak.',
                        'status_penilaian' => 'dinilai',
                        'tanggal_penilaian' => '2026-08-12',
                    ],
                    [
                        'judul' => 'Dokumentasi Pembiasaan Adab Makan & Kebersihan Lingkungan Kelas',
                        'kategori' => 'capaian_kinerja',
                        'deskripsi' => 'Program terstruktur pembentukan karakter Islami dan kemandirian santri melalui pembiasaan adab harian di sekolah.',
                        'link_gdrive' => 'https://drive.google.com/drive/folders/1K1l2M3n4O5p6Q7r8S9t0U1v2W3x4Y5zA?usp=sharing',
                        'skor_nilai' => null,
                        'predikat' => null,
                        'catatan_evaluasi' => null,
                        'status_penilaian' => 'diajukan',
                        'tanggal_penilaian' => null,
                    ],
                ],
            ],
            [
                'guru_username' => 'nurul_mina',
                'guru_name_contains' => 'Nurul Mina',
                'items' => [
                    [
                        'judul' => 'Sertifikasi Penguji Tahfidz Al-Quran Tingkat Kota & Provinsi (LPTQ)',
                        'kategori' => 'sertifikasi',
                        'deskripsi' => 'Uji kompetensi dewan juri Musabaqah Hifzhil Quran (MHQ) cabang 1 s/d 5 Juz serta standarisasi fashahah dan tajwid.',
                        'link_gdrive' => 'https://drive.google.com/drive/folders/1L2m3N4o5P6q7R8s9T0u1V2w3X4y5Z6aB?usp=sharing',
                        'skor_nilai' => 97.50,
                        'predikat' => 'Sangat Baik',
                        'catatan_evaluasi' => 'Kredibilitas sertifikasi sangat tinggi dan sangat memperkuat jaminan mutu lulusan tahfidz sekolah kita.',
                        'status_penilaian' => 'dinilai',
                        'tanggal_penilaian' => '2026-07-25',
                    ],
                    [
                        'judul' => 'Bimbingan Khusus Santri Berprestasi Juara 1 MHQ 3 Juz Tingkat Kota',
                        'kategori' => 'capaian_kinerja',
                        'deskripsi' => 'Membina dan mengantarkan santri delegasi SD Tahfizh F3 meraih Juara 1 Lomba Tahfidz Quran 3 Juz antar SDIT se-Kota Pekanbaru.',
                        'link_gdrive' => 'https://drive.google.com/file/d/1M3n4O5p6Q7r8S9t0U1v2W3x4Y5z6A7bC/view?usp=sharing',
                        'skor_nilai' => null,
                        'predikat' => null,
                        'catatan_evaluasi' => null,
                        'status_penilaian' => 'diajukan',
                        'tanggal_penilaian' => null,
                    ],
                ],
            ],
            [
                'guru_username' => 'ahmad',
                'guru_name_contains' => 'ahmad',
                'items' => [
                    [
                        'judul' => 'Pelatihan Metode Yanbua & Qiroati Terpadu untuk Guru Al-Quran',
                        'kategori' => 'pelatihan',
                        'deskripsi' => 'Penguatan metodologi bimbingan membaca Al-Quran tartil dan pengenalan gharib bagi anak tingkat dasar.',
                        'link_gdrive' => 'https://drive.google.com/drive/folders/1N4o5P6q7R8s9T0u1V2w3X4y5Z6a7B8cD?usp=sharing',
                        'skor_nilai' => 85.00,
                        'predikat' => 'Baik',
                        'catatan_evaluasi' => 'Penerapan metode sudah baik, pastikan pengulangan makhraj huruf istila dan shafir lebih ditekankan.',
                        'status_penilaian' => 'dinilai',
                        'tanggal_penilaian' => '2026-08-01',
                    ],
                ],
            ],
            [
                'guru_username' => 'aulia',
                'guru_name_contains' => 'Aulia',
                'items' => [
                    [
                        'judul' => 'Pengembangan Video Animasi Interaktif Kisah 25 Nabi & Rasul',
                        'kategori' => 'pengembangan_diri',
                        'deskripsi' => 'Pembuatan seri video animasi pendek untuk penguatan nilai tauhid dan sejarah kebudayaan Islam siswa kelas rendah.',
                        'link_gdrive' => 'https://drive.google.com/file/d/1O5p6Q7r8S9t0U1v2W3x4Y5z6A7b8C9dE/view?usp=sharing',
                        'skor_nilai' => null,
                        'predikat' => null,
                        'catatan_evaluasi' => null,
                        'status_penilaian' => 'diajukan',
                        'tanggal_penilaian' => null,
                    ],
                ],
            ],
        ];

        foreach ($dummyItems as $data) {
            $targetGuru = $gurus->first(function ($g) use ($data) {
                $username = strtolower($g->user->username ?? '');
                $nama = strtolower($g->user->nama ?? '');
                return str_contains($username, strtolower($data['guru_username']))
                    || str_contains($nama, strtolower($data['guru_name_contains']));
            });

            if (!$targetGuru) {
                // Fallback to random teacher from list
                $targetGuru = $gurus->random();
            }

            foreach ($data['items'] as $item) {
                CapaianGuru::updateOrCreate(
                    [
                        'guru_id' => $targetGuru->id,
                        'judul' => $item['judul'],
                    ],
                    [
                        'kategori' => $item['kategori'],
                        'deskripsi' => $item['deskripsi'],
                        'link_gdrive' => $item['link_gdrive'],
                        'tahun_ajaran_id' => $activeTahun ? $activeTahun->id : null,
                        'semester_id' => $activeSemester ? $activeSemester->id : null,
                        'penilai_id' => $item['status_penilaian'] === 'dinilai' ? $evaluatorId : null,
                        'skor_nilai' => $item['skor_nilai'],
                        'predikat' => $item['predikat'],
                        'catatan_evaluasi' => $item['catatan_evaluasi'],
                        'status_penilaian' => $item['status_penilaian'],
                        'tanggal_penilaian' => $item['tanggal_penilaian'],
                    ]
                );
            }
        }
    }
}
