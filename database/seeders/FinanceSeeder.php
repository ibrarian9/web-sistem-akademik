<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\JenisTagihan;
use App\Models\KategoriPengeluaran;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\PemasukanKas;
use App\Models\Pengeluaran;
use App\Models\PengajuanDana;
use App\Models\GajiGuru;
use App\Models\Peminjaman;
use App\Models\DanaBos;
use Carbon\Carbon;
use Illuminate\Support\Str;

class FinanceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pastikan Seeder Dasar Berjalan
        $this->call([
            RoleSeeder::class,
            JenisTagihanSeeder::class,
            KategoriPengeluaranSeeder::class,
        ]);

        $ta = TahunAjaran::where('status_aktif', true)->first();
        if (!$ta) {
            $ta = TahunAjaran::create([
                'nama' => '2025/2026',
                'status_aktif' => true,
            ]);
        }

        // Ambil Petugas Finance / Superadmin
        $roleFinance = Role::where('nama', 'finance')->first();
        $petugas = User::where('role_id', $roleFinance?->id)->first();
        if (!$petugas) {
            $petugas = User::where('username', 'admin')->first() ?? User::first();
        }

        $roleKoordinator = Role::where('nama', 'koordinator')->first();
        $koordinator = User::where('role_id', $roleKoordinator?->id)->first() ?? $petugas;

        $roleKepala = Role::where('nama', 'kepala-sekolah')->first() ?? Role::where('nama', 'yayasan')->first();
        $kepalaYayasan = User::where('role_id', $roleKepala?->id)->first() ?? $petugas;

        $siswas = Siswa::with('user')->get();
        $gurus = Guru::with('user')->get();

        if ($siswas->isEmpty() || $gurus->isEmpty()) {
            // Jika belum ada siswa/guru, panggil DemoDataSeeder
            $this->call(DemoDataSeeder::class);
            $siswas = Siswa::with('user')->get();
            $gurus = Guru::with('user')->get();
        }

        $sppJenis = JenisTagihan::where('nama', 'SPP')->first() ?? JenisTagihan::first();
        $bukuJenis = JenisTagihan::where('nama', 'Uang Buku')->first() ?? JenisTagihan::skip(1)->first();
        $bangunanJenis = JenisTagihan::where('nama', 'Uang Pembangunan')->first() ?? $sppJenis;

        $months = ['Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'];
        $methods = ['Tunai', 'Transfer Bank', 'E-Wallet'];

        // 2. Seed Tagihan & Pembayaran SPP (Juli 2025 - Juni 2026)
        $resiCounter = 1000;
        foreach ($siswas as $siswaIndex => $siswa) {
            foreach ($months as $monthIndex => $bulanName) {
                $year = ($monthIndex >= 6) ? 2026 : 2025;
                $dueDate = Carbon::createFromDate($year, ($monthIndex + 7) > 12 ? ($monthIndex - 5) : ($monthIndex + 7), 10);
                $nominalSPP = $sppJenis->default_nominal ?: 350000;

                // Tentukan status simulasi realistis
                // Siswa awal lunas, pertengahan sebagian/tunggakan
                if ($monthIndex < 8) {
                    // Bulan-bulan awal (Juli - Februari): 85% Lunas
                    $isLunas = ($siswaIndex % 10 !== 0);
                    $isSebagian = false;
                } else {
                    // Bulan-bulan akhir: Variatif
                    $isLunas = ($siswaIndex % 3 === 0);
                    $isSebagian = ($siswaIndex % 3 === 1);
                }

                $status = 'belum_bayar';
                $dibayar = 0;

                if ($isLunas) {
                    $status = 'lunas';
                    $dibayar = $nominalSPP;
                } elseif ($isSebagian) {
                    $status = 'sebagian';
                    $dibayar = $nominalSPP / 2;
                }

                $tagihan = Tagihan::firstOrCreate(
                    [
                        'siswa_id' => $siswa->id,
                        'jenis_tagihan_id' => $sppJenis->id,
                        'tahun_ajaran_id' => $ta->id,
                        'bulan' => $bulanName,
                    ],
                    [
                        'nominal' => $nominalSPP,
                        'total_dibayar' => $dibayar,
                        'status' => $status,
                        'jatuh_tempo' => $dueDate->format('Y-m-d'),
                    ]
                );

                if ($dibayar > 0 && $tagihan->pembayarans()->count() === 0) {
                    $resiCounter++;
                    $payDate = $dueDate->copy()->subDays(rand(1, 5));
                    Pembayaran::firstOrCreate(
                        [
                            'tagihan_id' => $tagihan->id,
                        ],
                        [
                            'no_resi' => 'RESI-' . $year . sprintf('%02d', $monthIndex + 1) . '-' . Str::random(5),
                            'tanggal_bayar' => $payDate->format('Y-m-d'),
                            'nominal_dibayar' => $dibayar,
                            'kelebihan_bayar' => 0,
                            'metode_bayar' => $methods[rand(0, 2)],
                            'is_void' => false,
                            'petugas_id' => $petugas->id,
                        ]
                    );
                }
            }

            // Tagihan Non-SPP (Uang Buku & Pembangunan)
            $tagihanBuku = Tagihan::firstOrCreate(
                [
                    'siswa_id' => $siswa->id,
                    'jenis_tagihan_id' => $bukuJenis->id,
                    'tahun_ajaran_id' => $ta->id,
                    'bulan' => 'Tahunan',
                ],
                [
                    'nominal' => $bukuJenis->default_nominal ?: 600000,
                    'total_dibayar' => $bukuJenis->default_nominal ?: 600000,
                    'status' => 'lunas',
                    'jatuh_tempo' => '2025-08-15',
                ]
            );

            if ($tagihanBuku->pembayarans()->count() === 0) {
                $resiCounter++;
                Pembayaran::firstOrCreate(
                    [
                        'tagihan_id' => $tagihanBuku->id,
                    ],
                    [
                        'no_resi' => 'RESI-202507-' . Str::random(5),
                        'tanggal_bayar' => '2025-07-15',
                        'nominal_dibayar' => $tagihanBuku->nominal,
                        'kelebihan_bayar' => 0,
                        'metode_bayar' => 'Transfer Bank',
                        'is_void' => false,
                        'petugas_id' => $petugas->id,
                    ]
                );
            }
        }

        // 3. Seed Pemasukan Kas Non-SPP (Infaq, Sedekah, Donasi)
        $pemasukanList = [
            ['kategori' => 'Infaq Subuh', 'jumlah' => 1250000, 'tanggal' => '2026-07-01', 'keterangan' => 'Infaq Subuh Jamaah Sekolah & Wali Murid'],
            ['kategori' => 'Sedekah Maghrib Mengaji', 'jumlah' => 850000, 'tanggal' => '2026-07-05', 'keterangan' => 'Sedekah kegiatan Maghrib Mengaji rutin'],
            ['kategori' => 'Donasi Donatur', 'jumlah' => 5000000, 'tanggal' => '2026-07-10', 'keterangan' => 'Donasi hamba Allah untuk santunan anak yatim'],
            ['kategori' => 'Wakaf Al-Qur\'an', 'jumlah' => 2400000, 'tanggal' => '2026-07-12', 'keterangan' => 'Wakaf 30 mushaf Al-Qur\'an dari alumni'],
            ['kategori' => 'Infaq Jumat', 'jumlah' => 1750000, 'tanggal' => '2026-07-18', 'keterangan' => 'Infaq Kotak Jumat Musholla Sekolah'],
        ];

        foreach ($pemasukanList as $pem) {
            PemasukanKas::firstOrCreate(
                [
                    'kategori' => $pem['kategori'],
                    'tanggal' => $pem['tanggal'],
                    'jumlah' => $pem['jumlah'],
                ],
                [
                    'keterangan' => $pem['keterangan'],
                    'petugas_id' => $petugas->id,
                ]
            );
        }

        // 4. Seed Pengeluaran Operasional
        $katList = KategoriPengeluaran::all();
        $katAtk = $katList->where('nama', 'Alat Tulis Kantor (ATK)')->first() ?? $katList->first();
        $katListrik = $katList->where('nama', 'Listrik & Air')->first() ?? $katList->first();
        $katMaint = $katList->where('nama', 'Pemeliharaan & Kebersihan')->first() ?? $katList->first();

        $pengeluaranData = [
            ['kategori_pengeluaran_id' => $katListrik->id, 'jumlah' => 1850000, 'tanggal' => '2026-07-02', 'keterangan' => 'Pembayaran tagihan Listrik PLN & Air PDAM bulan Juli'],
            ['kategori_pengeluaran_id' => $katAtk->id, 'jumlah' => 950000, 'tanggal' => '2026-07-04', 'keterangan' => 'Pembelian Kertas HVS, Spidol Whiteboard & Tinta Printer'],
            ['kategori_pengeluaran_id' => $katMaint->id, 'jumlah' => 1200000, 'tanggal' => '2026-07-08', 'keterangan' => 'Servis dan cuci AC 4 unit di ruang kelas & laboratorium'],
        ];

        foreach ($pengeluaranData as $exp) {
            Pengeluaran::firstOrCreate(
                [
                    'kategori_pengeluaran_id' => $exp['kategori_pengeluaran_id'],
                    'tanggal' => $exp['tanggal'],
                    'jumlah' => $exp['jumlah'],
                ],
                [
                    'keterangan' => $exp['keterangan'],
                    'petugas_id' => $petugas->id,
                ]
            );
        }

        // 5. Seed Pengajuan Penggunaan Dana
        $pengajuanItems = [
            [
                'judul' => 'Pembelian Buku LKS Kurikulum Merdeka Semester Ganjil',
                'kategori' => 'Buku / LKS',
                'nominal' => 3400000,
                'tanggal_pengajuan' => '2026-07-03',
                'keterangan' => 'Pengadaan LKS 120 pasang untuk siswa kelas 7, 8, dan 9.',
                'pemohon_id' => $petugas->id,
                'status' => 'direalisasi',
                'disetujui_koordinator_id' => $koordinator->id,
                'tanggal_persetujuan_koordinator' => now()->subDays(10),
                'disetujui_kepala_yayasan_id' => $kepalaYayasan->id,
                'tanggal_persetujuan_kepala_yayasan' => now()->subDays(8),
            ],
            [
                'judul' => 'Pengadaan Seragam Olahraga Tambahan',
                'kategori' => 'Seragam',
                'nominal' => 1800000,
                'tanggal_pengajuan' => '2026-07-10',
                'keterangan' => 'Tambahan 20 stel baju olahraga siswa baru.',
                'pemohon_id' => $petugas->id,
                'status' => 'disetujui',
                'disetujui_koordinator_id' => $koordinator->id,
                'tanggal_persetujuan_koordinator' => now()->subDays(5),
                'disetujui_kepala_yayasan_id' => $kepalaYayasan->id,
                'tanggal_persetujuan_kepala_yayasan' => now()->subDays(3),
            ],
            [
                'judul' => 'Perbaikan Sound System & Mic Wireless Ruang Utama',
                'kategori' => 'Sarana Prasarana',
                'nominal' => 850000,
                'tanggal_pengajuan' => '2026-07-15',
                'keterangan' => 'Penggantian mic wireless dan kabel mixer utama.',
                'pemohon_id' => $petugas->id,
                'status' => 'menunggu_koordinator',
            ],
        ];

        foreach ($pengajuanItems as $item) {
            PengajuanDana::firstOrCreate(
                [
                    'judul' => $item['judul'],
                    'tanggal_pengajuan' => $item['tanggal_pengajuan'],
                ],
                $item
            );
        }

        // 6. Seed Peminjaman & Gaji Guru
        if ($gurus->isNotEmpty()) {
            $guru1 = $gurus->first();
            $peminjaman = Peminjaman::firstOrCreate(
                [
                    'guru_id' => $guru1->id,
                    'tanggal_pinjam' => '2026-06-01',
                ],
                [
                    'nominal' => 2000000,
                    'tenor_bulan' => 5,
                    'cicilan_per_bulan' => 400000,
                    'sisa_pinjaman' => 160000,
                    'status' => 'berjalan',
                ]
            );

            foreach ($gurus as $guru) {
                $gajiPokok = $guru->gaji_pokok ?: 2500000;
                $bpjs = 100000;
                $maghrib = 200000;
                $potPinjam = ($guru->id === $guru1->id) ? 400000 : 0;
                $total = $gajiPokok + $bpjs + $maghrib - $potPinjam;

                GajiGuru::updateOrCreate(
                    [
                        'guru_id' => $guru->id,
                        'bulan' => 'Juli',
                        'tahun' => 2026,
                    ],
                    [
                        'gaji_pokok' => $gajiPokok,
                        'insentif_bpjs' => $bpjs,
                        'insentif_maghrib_mengaji' => $maghrib,
                        'potongan_peminjaman' => $potPinjam,
                        'potongan_lainnya' => 0,
                        'total_diterima' => $total,
                        'tanggal_bayar' => '2026-07-01',
                        'status' => 'dibayar',
                    ]
                );
            }
        }

        // 7. Seed Dana BOS
        DanaBos::firstOrCreate(
            [
                'tahun_ajaran_id' => $ta->id,
                'kategori' => 'Pencairan BOS Tahap I',
            ],
            [
                'jenis' => 'masuk',
                'tanggal' => '2026-07-05',
                'nominal' => 45000000,
                'keterangan' => 'Pencairan dana BOS Tahap I Tahun Ajaran 2025/2026 via Rekening Bank Jabar',
            ]
        );

        DanaBos::firstOrCreate(
            [
                'tahun_ajaran_id' => $ta->id,
                'kategori' => 'Pembelian Buku Kurikulum Merdeka',
            ],
            [
                'jenis' => 'keluar',
                'tanggal' => '2026-07-12',
                'nominal' => 12500000,
                'keterangan' => 'Belanja buku teks pendamping siswa dari Penerbit Resmi',
            ]
        );
    }
}
