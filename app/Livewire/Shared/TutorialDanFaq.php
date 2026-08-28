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
            ],
            [
                'id' => 'penggajian-yayasan-f3',
                'category' => 'finance',
                'category_label' => 'Finance & Bendahara',
                'role_badge' => 'Payroll Yayasan F3',
                'title' => 'Panduan Penggajian Pegawai Yayasan F3, Edit Pra-Generate & Slip Gaji QR',
                'problem_desc' => 'Bagaimana alur lengkap pembuatan honorarium/gaji, penyesuaian nominal, dan penerbitan slip gaji resmi?',
                'consequences' => [
                    'Menjamin transparansi gaji pokok, berkala, insentif, ekskul, potongan sosial Rp 10.000, BPJSTK, dan potongan kasbon.',
                    'Gaji berstatus Dibayar otomatis membukukan transaksi pengeluaran di Buku Kas Yayasan dan memotong saldo kasbon guru.',
                    'Menerbitkan Slip Gaji Digital Resmi ber-QR Code untuk masing-masing pegawai.'
                ],
                'steps' => [
                    [
                        'step' => 1,
                        'title' => 'Buka Menu Gaji Guru',
                        'desc' => 'Masuk ke menu "Gaji Guru" pada sidebar Pengeluaran & Anggaran Yayasan.'
                    ],
                    [
                        'step' => 2,
                        'title' => 'Generate Draf Massal atau Buat Manual',
                        'desc' => 'Klik "Generate Draf Gaji" untuk seluruh guru sekaligus atau "Buat Gaji Manual" untuk pegawai perorangan. Pada modal generate, Anda dapat langsung mengedit angka per kolom (Gaji Pokok, Ekskul, Insentif, Kasbon) sebelum draf disimpan.'
                    ],
                    [
                        'step' => 3,
                        'title' => 'Tinjau & Ubah Fleksibel Kapan Saja',
                        'desc' => 'Klik tombol "Ubah" pada baris guru kapan saja untuk merevisi nominal gaji. Jika gaji sudah dibayar, sistem otomatis menyinkronkan nilai pengeluaran kas.'
                    ],
                    [
                        'step' => 4,
                        'title' => 'Proses Pembayaran (Bayar)',
                        'desc' => 'Klik tombol "Bayar" untuk mencatat pengeluaran kas yayasan dan memotong saldo cicilan kasbon guru secara otomatis.'
                    ],
                    [
                        'step' => 5,
                        'title' => 'Cetak & Unduh Slip Gaji PDF Ber-QR Code',
                        'desc' => 'Klik "Slip" untuk pratinjau slip ber-QR Code, "Unduh" untuk file PDF satuan, atau "Bulk Unduh PDF" untuk mengunduh seluruh slip pegawai dalam satu berkas.'
                    ]
                ],
                'action_route' => in_array($role, ['finance', 'super_admin']) ? 'finance.gaji-guru' : null,
                'action_label' => 'Buka Manajemen Gaji Guru'
            ],
            [
                'id' => 'fasilitas-kasbon-guru',
                'category' => 'finance',
                'category_label' => 'Finance & Bendahara',
                'role_badge' => 'Integrasi Kasbon',
                'title' => 'Panduan Fasilitas Kasbon Pegawai & Pemotongan Cicilan Otomatis',
                'problem_desc' => 'Bagaimana mencatat pinjaman kasbon dan memastikan pemotongan cicilan berjalan otomatis?',
                'consequences' => [
                    'Menghindari lupa potong cicilan pinjaman pada saat proses penggajian bulanan.',
                    'Sisa saldo pinjaman otomatis berkurang secara akurat dan transparan saat gaji dibayarkan.'
                ],
                'steps' => [
                    [
                        'step' => 1,
                        'title' => 'Catat Pinjaman Baru',
                        'desc' => 'Buka menu "Kasbon Guru" (/finance/peminjaman) lalu klik tombol "Catat Pinjaman Baru".'
                    ],
                    [
                        'step' => 2,
                        'title' => 'Isi Nominal & Tenor Bulan',
                        'desc' => 'Pilih nama guru, masukkan nominal pinjaman, tenor bulan, serta tanggal pencairan kasbon. Sistem otomatis menghitung cicilan per bulan.'
                    ],
                    [
                        'step' => 3,
                        'title' => 'Otomatis Terdeteksi di Draf Gaji',
                        'desc' => 'Saat periode penggajian tiba, kolom Kasbon otomatis terisi nominal cicilan bulanan guru yang bersangkutan.'
                    ],
                    [
                        'step' => 4,
                        'title' => 'Otomatis Mengurangi Saldo Saat Bayar',
                        'desc' => 'Ketika status gaji dibayar, sisa saldo kasbon otomatis terpotong. Status pinjaman akan berubah otomatis menjadi "Lunas" setelah seluruh cicilan selesai.'
                    ]
                ],
                'action_route' => in_array($role, ['finance', 'super_admin']) ? 'finance.peminjaman' : null,
                'action_label' => 'Buka Menu Kasbon Guru'
            ],
            [
                'id' => 'guru-slip-gaji-mandiri',
                'category' => 'guru',
                'category_label' => 'Guru & Pengajar',
                'role_badge' => 'Slip Gaji Mandiri',
                'title' => 'Cara Guru Melihat & Mengunduh Slip Gaji Digital Mandiri',
                'problem_desc' => 'Bagaimana guru mengecek rincian penerimaan honorarium dan bukti slip gaji ber-QR Code?',
                'consequences' => [
                    'Guru mendapatkan transparansi penuh atas gaji pokok, insentif, ekskul, dan potongan.',
                    'Guru dapat mengunduh dokumen slip gaji resmi kapan saja tanpa perlu meminta berkas cetak ke bendahara.'
                ],
                'steps' => [
                    [
                        'step' => 1,
                        'title' => 'Buka Menu Slip Gaji Saya',
                        'desc' => 'Login ke akun Guru, klik menu "Slip Gaji Saya" pada sidebar kelompok Keuangan Saya.'
                    ],
                    [
                        'step' => 2,
                        'title' => 'Pilih Periode Bulan & Tahun',
                        'desc' => 'Pilih periode bulan dan tahun penggajian untuk melihat kartu slip gaji resmi.'
                    ],
                    [
                        'step' => 3,
                        'title' => 'Cek Rincian & Unduh PDF',
                        'desc' => 'Periksa rincian Penerimaan (Gaji Pokok, Berkala, Ekskul, Insentif, BPJS), Potongan (Sosial, Kasbon, BPJSTK), dan Take Home Pay, lalu klik tombol "Unduh PDF Resmi" untuk menyimpan file PDF ber-QR Code.'
                    ]
                ],
                'action_route' => $role === 'guru' ? 'guru.slip-gaji-saya' : null,
                'action_label' => 'Buka Slip Gaji Saya'
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
                'question' => 'Apakah kasbon guru otomatis memotong slip gaji dan mengurangi saldo pinjaman?',
                'answer' => 'Ya, sistem secara otomatis mendeteksi pinjaman aktif guru dan mengisi nilai cicilan ke kolom Kasbon saat draf gaji digenerate. Begitu tombol "Bayar" diklik, sisa saldo kasbon di menu Kasbon Guru otomatis terpotong dan statusnya otomatis menjadi "Lunas" bila seluruh angsuran telah terpenuhi.',
                'category' => 'finance'
            ],
            [
                'id' => 'faq-4',
                'question' => 'Apakah Finance dapat mengubah nominal gaji setelah digenerate atau setelah dibayar?',
                'answer' => 'Ya, Finance memiliki fleksibilitas penuh: dapat mengedit nominal langsung di tabel modal sebelum digenerate, maupun menekan tombol "Ubah" pada baris tabel kapan saja. Jika gaji yang diubah sudah berstatus Dibayar, sistem otomatis menyinkronkan nilai pengeluaran kas di Buku Kas Keuangan Yayasan.',
                'category' => 'finance'
            ],
            [
                'id' => 'faq-5',
                'question' => 'Bagaimana jika ingin membatalkan status pembayaran gaji yang keliru?',
                'answer' => 'Finance cukup menekan tombol "Batal" (ikon putar balik) pada baris gaji terkait. Sistem akan mengembalikan status gaji menjadi Draf, menghapus transaksi pengeluaran kas otomatis, dan mengembalikan nominal cicilan ke saldo kasbon guru.',
                'category' => 'finance'
            ],
            [
                'id' => 'faq-6',
                'question' => 'Bagaimana cara orang tua memberikan tanggapan setoran Tahfizh anak?',
                'answer' => 'Orang tua / Wali murid dapat masuk ke Portal Murid -> Menu Evaluasi Tahfizh -> klik tombol "Beri Tanggapan Orang Tua" pada baris setoran mutaba\'ah ustadz.',
                'category' => 'murid'
            ],
            [
                'id' => 'faq-7',
                'question' => 'Apakah Rapor Digital Kurikulum Merdeka mendukung cetak PDF?',
                'answer' => 'Ya, sistem dilengkapi dengan PDF Render Engine otomatis untuk Rapor Akademik (Kurikulum Merdeka & KTSP) serta Lembar Rapor Tahfizh yang siap dicetak atau diunduh langsung dalam format PDF resmi.',
                'category' => 'guru'
            ],
            [
                'id' => 'faq-8',
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
