<?php

namespace App\Livewire\Shared;

use Livewire\Component;

class TutorialDanFaq extends Component
{
    public string $search = '';
    public string $selectedCategory = 'semua';
    public ?string $openFaqId = null;

    public function selectCategory(string $category): void
    {
        $this->selectedCategory = $category;
    }

    public function toggleFaq(string $faqId): void
    {
        if ($this->openFaqId === $faqId) {
            $this->openFaqId = null;
        } else {
            $this->openFaqId = $faqId;
        }
    }

    public function render()
    {
        $role = auth()->user()->role->nama ?? 'murid';

        $tutorials = [
            [
                'id' => 'buat-mapel',
                'category' => 'tata_usaha',
                'category_label' => 'Tata Usaha & Admin',
                'role_badge' => 'Penting untuk Rapor & Jadwal',
                'title' => 'Cara Membuat & Pengaturan Mata Pelajaran',
                'problem_desc' => 'Apa yang terjadi jika Mata Pelajaran belum dibuat?',
                'consequences' => [
                    'Dropdown filter mata pelajaran pada menu Guru (Setup Bab & TP, Input Nilai) akan kosong.',
                    'Penugasan Guru ke Kelas (GuruMapelKelas) tidak bisa diset sehingga Jadwal Pelajaran Mingguan belum dapat disusun.',
                    'Kalkulasi nilai dan pembuatan Rapor Digital tidak dapat memuat nilai mata pelajaran umum.'
                ],
                'steps' => [
                    [
                        'step' => 1,
                        'title' => 'Buka Menu Mata Pelajaran',
                        'desc' => 'Login sebagai Super Admin atau Tata Usaha, lalu klik menu "Mata Pelajaran" pada sidebar Tata Kelola / Data Master.'
                    ],
                    [
                        'step' => 2,
                        'title' => 'Klik Tombol Tambah Mata Pelajaran',
                        'desc' => 'Klik tombol "Tambah Mata Pelajaran Baru" di bagian kanan atas halaman.'
                    ],
                    [
                        'step' => 3,
                        'title' => 'Isi Data Lengkap Mata Pelajaran',
                        'desc' => 'Lengkapi Kode Mapel (misal: MTK-SD), Nama Mapel (misal: Matematika), Kelompok Mapel (Kelompok A / B / Agama), serta KKM (misal: 75).'
                    ],
                    [
                        'step' => 4,
                        'title' => 'Simpan & Petakan ke Rombel / Guru',
                        'desc' => 'Klik "Simpan Mata Pelajaran", kemudian buka menu "Jadwal Pelajaran" / "Plotting Kelas" untuk menentukan Guru Pengampu setiap rombel.'
                    ]
                ],
                'action_route' => in_array($role, ['super_admin', 'tata_usaha']) ? 'super-admin.mapel' : null,
                'action_label' => 'Buka Menu Manajemen Mata Pelajaran'
            ],
            [
                'id' => 'setup-kurikulum-merdeka',
                'category' => 'guru',
                'category_label' => 'Guru & Pengajar',
                'role_badge' => 'Kurikulum Merdeka',
                'title' => 'Panduan Setup Bab (Lingkup Materi) & Tujuan Pembelajaran (TP)',
                'problem_desc' => 'Bagaimana menyusun TP agar Auto-Narasi Rapor Digital berjalan otomatis?',
                'consequences' => [
                    'Jika Bab & TP belum diisi, guru tidak dapat menginput Nilai Sumatif Per-TP.',
                    'Auto-narasi deskripsi capaian pembelajaran pada rapor murid tidak akan muncul.'
                ],
                'steps' => [
                    [
                        'step' => 1,
                        'title' => 'Pilih Mata Pelajaran',
                        'desc' => 'Masuk ke menu "Setup Bab & TP", lalu pilih mata pelajaran yang Anda ampu pada dropdown filter.'
                    ],
                    [
                        'step' => 2,
                        'title' => 'Tambah Bab / Lingkup Materi',
                        'desc' => 'Klik tombol "Tambah Bab" untuk mendaftarkan nama Bab (contoh: Bab 1 Bilangan Bulat) beserta nomor urutannya.'
                    ],
                    [
                        'step' => 3,
                        'title' => 'Input Tujuan Pembelajaran (TP)',
                        'desc' => 'Klik "Tambah TP" di dalam bab tersebut. Masukkan deskripsi ringkas capaian kompetensi (misal: membaca dan menulis bilangan bulat).'
                    ],
                    [
                        'step' => 4,
                        'title' => 'Simpan Template Frasa Narasi',
                        'desc' => 'Tentukan frasa pembuka untuk nilai tertinggi & terendah agar kalimat rapor tersusun rapi secara otomatis.'
                    ]
                ],
                'action_route' => $role === 'guru' ? 'guru.kurikulum-merdeka' : null,
                'action_label' => 'Buka Setup Bab & TP'
            ],
            [
                'id' => 'setoran-tahfidz',
                'category' => 'guru',
                'category_label' => 'Guru & Pengajar',
                'role_badge' => 'Tahfizh & Mutabaah',
                'title' => 'Cara Pengisian Setoran & Mutaba\'ah Tahfizh Harian',
                'problem_desc' => 'Bagaimana mencatat capaian hafalan dan muraja\'ah santri?',
                'consequences' => [
                    'Santri dan orang tua tidak dapat memantau progres hafalan di portal murid.',
                    'Rapor Tahfizh semesteran tidak mendapatkan data agregat Juz dan Surah.'
                ],
                'steps' => [
                    [
                        'step' => 1,
                        'title' => 'Pilih Kelas & Santri',
                        'desc' => 'Masuk ke menu "Setoran Tahfizh", pilih rombel Halaqah dan pilih nama santri yang menyetor.'
                    ],
                    [
                        'step' => 2,
                        'title' => 'Input Rincian Setoran',
                        'desc' => 'Isikan Ziyadah (Juz, Surah, Ayat), Muraja\'ah (Bersama & Mandiri), Tahsin, serta Kitabah.'
                    ],
                    [
                        'step' => 3,
                        'title' => 'Berikan Catatan & Predikat Ustadz',
                        'desc' => 'Ketikkan motivasi/catatan evaluasi pembimbing dan pilih predikat keagamaan (misal: Sangat Baik).'
                    ]
                ],
                'action_route' => $role === 'guru' ? 'guru.input-tahfidz' : null,
                'action_label' => 'Buka Menu Setoran Tahfizh'
            ],
            [
                'id' => 'pembayaran-spp',
                'category' => 'finance',
                'category_label' => 'Finance & Wali Murid',
                'role_badge' => 'Keuangan & SPP',
                'title' => 'Panduan Transaksi Pembayaran SPP & Cetak Resi',
                'problem_desc' => 'Bagaimana prosedur mencatat pembayaran dan membagikan bukti resi?',
                'consequences' => [
                    'Status tagihan santri akan tetap tertunggak jika tidak di-input oleh staf bendahara.',
                    'Orang tua tidak menerima bukti pembayaran resmi yang tervalidasi.'
                ],
                'steps' => [
                    [
                        'step' => 1,
                        'title' => 'Cari Nama / NIS Santri',
                        'desc' => 'Buka menu "Input Pembayaran", ketikkan nama atau NIS santri pada kolom pencarian.'
                    ],
                    [
                        'step' => 2,
                        'title' => 'Pilih Bulan / Item Tagihan',
                        'desc' => 'Centang item tagihan yang dibayarkan (SPP, Daftar Ulang, Kegiatan, dll).'
                    ],
                    [
                        'step' => 3,
                        'title' => 'Proses & Cetak Resi PDF',
                        'desc' => 'Pilih metode pembayaran (Tunai/Transfer), klik Simpan, lalu klik tombol "Cetak Resi PDF" untuk mengunduh bukti resmi.'
                    ]
                ],
                'action_route' => in_array($role, ['finance', 'super_admin']) ? 'finance.input-pembayaran' : null,
                'action_label' => 'Buka Input Pembayaran'
            ]
        ];

        $faqs = [
            [
                'id' => 'faq-1',
                'question' => 'Mengapa nama siswa atau kelas tidak muncul di akun Guru?',
                'answer' => 'Hal ini terjadi apabila guru belum dipetakan ke rombel/kelas umum di menu Plotting Kelas atau belum ditugaskan mengampu mata pelajaran di kelas tersebut. Pastikan Tata Usaha / Super Admin sudah mengatur penugasan guru di menu Manajemen Jadwal / Plotting Kelas.',
                'category' => 'guru'
            ],
            [
                'id' => 'faq-2',
                'question' => 'Apa yang terjadi jika Mata Pelajaran belum dibikin di awal tahun ajaran?',
                'answer' => 'Jika mata pelajaran belum dibuat, fitur seperti Setup Bab & TP, Input Nilai Sumatif, dan Jadwal Pelajaran tidak bisa digunakan karena tidak ada mapel yang dipilih. Buat terlebih dahulu Mata Pelajaran di menu "Mata Pelajaran" (Role Super Admin / Tata Usaha).',
                'category' => 'tata_usaha'
            ],
            [
                'id' => 'faq-3',
                'question' => 'Bagaimana cara orang tua memberikan tanggapan setoran Tahfizh anak?',
                'answer' => 'Orang tua / Wali murid dapat masuk ke Portal Murid -> Menu Evaluasi Tahfizh -> klik tombol "Beri Tanggapan Orang Tua" pada baris setoran mutaba\'ah ustadz.',
                'category' => 'murid'
            ],
            [
                'id' => 'faq-4',
                'question' => 'Apakah Rapor Digital Kurikulum Merdeka mendukung cetak PDF?',
                'answer' => 'Ya, sistem dilengkapi dengan PDF Render Engine otomatis untuk Rapor Akademik (Kurikulum Merdeka & KTSP) serta Lembar Rapor Tahfizh yang siap dicetak atau diunduh langsung dalam format PDF resmi.',
                'category' => 'guru'
            ],
            [
                'id' => 'faq-5',
                'question' => 'Bagaimana prosedur pergantian password akun login?',
                'answer' => 'Pengguna dapat mengganti password mandiri melalui menu "Profil Saya" -> "Ubah Password". Jika lupa password, Tata Usaha atau Super Admin dapat mereset password melalui menu Manajemen User.',
                'category' => 'semua'
            ]
        ];

        // Filter Tutorials
        $filteredTutorials = array_filter($tutorials, function ($t) {
            $matchesCat = $this->selectedCategory === 'semua' || $t['category'] === $this->selectedCategory;
            $matchesSearch = empty($this->search) || 
                str_contains(strtolower($t['title']), strtolower($this->search)) ||
                str_contains(strtolower($t['problem_desc']), strtolower($this->search));
            return $matchesCat && $matchesSearch;
        });

        // Filter FAQs
        $filteredFaqs = array_filter($faqs, function ($f) {
            $matchesCat = $this->selectedCategory === 'semua' || $f['category'] === $this->selectedCategory;
            $matchesSearch = empty($this->search) || 
                str_contains(strtolower($f['question']), strtolower($this->search)) ||
                str_contains(strtolower($f['answer']), strtolower($this->search));
            return $matchesCat && $matchesSearch;
        });

        return view('livewire.shared.tutorial-dan-faq', [
            'tutorials' => $filteredTutorials,
            'faqs' => $filteredFaqs,
            'userRole' => $role
        ])->layout('layouts.app', ['title' => 'Pusat Panduan Tutorial System & FAQ']);
    }
}
