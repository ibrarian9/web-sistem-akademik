<?php

namespace App\Services;

use App\Models\Siswa;
use App\Models\Guru;
use App\Models\User;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\JenisTagihan;
use App\Models\Tagihan;
use App\Models\KategoriPengeluaran;
use App\Models\Role;
use App\Models\MataPelajaran;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AuditLogFormatter
{
    /**
     * Cache untuk menghindari query berulang selama 1 request lifecycle.
     */
    protected static array $relationCache = [];

    /**
     * Bersihkan deskripsi audit log dari raw JSON string dan ID numerik.
     */
    public static function cleanDescription(?string $desc): string
    {
        if (empty($desc)) {
            return '-';
        }

        // 1. Deteksi dan ekstrak jika ada string JSON di dalam kurung atau di dalam teks
        // Contoh: Membuat data Pengeluaran ({"id":10,"nama":"ATK & Fotokopi","jenis":"operasional",...})
        if (preg_match('/\((\{.*?\})\)/s', $desc, $matches) || preg_match('/(\{["\'].*?["\']\s*:.*?\})/s', $desc, $matches)) {
            $rawJson = $matches[1];
            $decoded = json_decode($rawJson, true);
            if (is_array($decoded)) {
                $humanName = $decoded['nama']
                    ?? $decoded['name']
                    ?? $decoded['nama_lengkap']
                    ?? $decoded['judul']
                    ?? $decoded['kategori']
                    ?? $decoded['keterangan']
                    ?? $decoded['deskripsi']
                    ?? null;

                if ($humanName && is_string($humanName)) {
                    $desc = str_replace($matches[0], " ({$humanName})", $desc);
                } else {
                    $desc = str_replace($matches[0], '', $desc);
                }
            }
        }

        // 2. Hilangkan pattern ID numerik yang mengganggu seperti "ID 123" atau "(#123)"
        // Contoh: "Gagal menghapus data guru ID 12: Guru tidak ditemukan" -> "Gagal menghapus data guru: Guru tidak ditemukan"
        $desc = preg_replace('/\s+ID\s+\d+/i', '', $desc);
        $desc = preg_replace('/\s*\(\#\d+\)/i', '', $desc);

        // 3. Normalisasi nama model teknis PascalCase menjadi kalimat Bahasa Indonesia yang ramah
        $replacements = [
            'Membuat data PemasukanKas' => 'Mencatat Pemasukan Kas',
            'Memperbarui data PemasukanKas' => 'Memperbarui Data Pemasukan Kas',
            'Menghapus data PemasukanKas' => 'Menghapus Data Pemasukan Kas',
            'Membuat data Pengeluaran' => 'Mencatat Pengeluaran Kas',
            'Memperbarui data Pengeluaran' => 'Memperbarui Data Pengeluaran Kas',
            'Menghapus data Pengeluaran' => 'Menghapus Data Pengeluaran Kas',
            'Membuat data Tagihan' => 'Menerbitkan Tagihan Siswa',
            'Memperbarui data Tagihan' => 'Memperbarui Tagihan Siswa',
            'Menghapus data Tagihan' => 'Menghapus Tagihan Siswa',
            'Membuat data Pembayaran' => 'Menerima Pembayaran Siswa',
            'Memperbarui data Pembayaran' => 'Memperbarui Pembayaran Siswa',
            'Menghapus data Pembayaran' => 'Membatalkan Pembayaran Siswa',
            'Membuat data Siswa' => 'Mendaftarkan Data Siswa Baru',
            'Memperbarui data Siswa' => 'Memperbarui Profil Siswa',
            'Menghapus data Siswa' => 'Menghapus Data Siswa',
            'Membuat data Guru' => 'Menambahkan Guru Baru',
            'Memperbarui data Guru' => 'Memperbarui Profil Guru',
            'Menghapus data Guru' => 'Menghapus Data Guru',
            'Membuat data Kelas' => 'Menambahkan Kelas Baru',
            'Memperbarui data Kelas' => 'Memperbarui Data Kelas',
            'Menghapus data Kelas' => 'Menghapus Data Kelas',
            'Membuat data Tabungan' => 'Mencatat Transaksi Tabungan',
            'Memperbarui data Tabungan' => 'Memperbarui Transaksi Tabungan',
            'Menghapus data Tabungan' => 'Menghapus Transaksi Tabungan',
            'Membuat data DanaBos' => 'Mencatat Buku Kas Dana BOS',
            'Memperbarui data DanaBos' => 'Memperbarui Buku Kas Dana BOS',
            'Menghapus data DanaBos' => 'Menghapus Catatan Dana BOS',
            'Membuat data GajiGuru' => 'Mencatat Pembayaran Gaji Guru',
            'Memperbarui data GajiGuru' => 'Memperbarui Data Gaji Guru',
            'Menghapus data GajiGuru' => 'Menghapus Catatan Gaji Guru',
            'Membuat data User' => 'Membuat Akun Pengguna',
            'Memperbarui data User' => 'Memperbarui Akun Pengguna',
            'Menghapus data User' => 'Menghapus Akun Pengguna',
        ];

        foreach ($replacements as $search => $replace) {
            if (str_starts_with($desc, $search)) {
                $desc = substr_replace($desc, $replace, 0, strlen($search));
                break;
            }
        }

        // Rapikan spasi ganda dan tanda baca ganda
        $desc = preg_replace('/\s+/', ' ', trim($desc));
        $desc = str_replace([': :', '  ', '()'], [':', ' ', ''], $desc);

        return $desc;
    }

    /**
     * Terjemahkan nama class model menjadi nama modul yang ramah manusia.
     */
    public static function formatModelName(?string $subjectType): string
    {
        if (empty($subjectType)) {
            return 'Sistem Umum';
        }

        $base = class_basename($subjectType);

        $map = [
            'Tagihan' => 'Tagihan Siswa',
            'Pembayaran' => 'Pembayaran & Kasir',
            'PemasukanKas' => 'Kas Masuk & Infaq',
            'Pengeluaran' => 'Pengeluaran Kas',
            'Tabungan' => 'Tabungan Siswa',
            'DanaBos' => 'Buku Kas Dana BOS',
            'GajiGuru' => 'Penggajian Guru',
            'PengajuanDana' => 'Pengajuan Dana Operasional',
            'Siswa' => 'Data Siswa',
            'Guru' => 'Data Guru & Pendidik',
            'Kelas' => 'Data Rombongan Belajar (Kelas)',
            'User' => 'Akun Pengguna',
            'Role' => 'Peran & Hak Akses',
            'Nilai' => 'Penilaian Akademik',
            'NilaiSas' => 'Penilaian Akhir Semester (SAS)',
            'NilaiSumatifTp' => 'Penilaian Sumatif TP',
            'NilaiTahfidz' => 'Penilaian Tahfidz Al-Qur\'an',
            'NilaiP5' => 'Penilaian Proyek P5',
            'Rapor' => 'Rapor Siswa',
            'CatatanPendampingan' => 'Catatan Pendampingan Siswa',
            'RiwayatSurat' => 'Administrasi Persuratan',
            'JenisTagihan' => 'Jenis Tagihan',
            'KategoriPengeluaran' => 'Kategori Pos Pengeluaran',
            'TahunAjaran' => 'Tahun Ajaran',
            'Semester' => 'Semester Akademik',
            'MataPelajaran' => 'Mata Pelajaran',
            'AbsensiSiswa' => 'Presensi Siswa',
            'AbsensiGuru' => 'Presensi Guru',
            'KalenderAkademik' => 'Kalender Akademik',
        ];

        return $map[$base] ?? Str::headline($base);
    }

    /**
     * Parse User Agent teknis menjadi informasi browser, OS, dan perangkat yang ramah manusia.
     */
    public static function parseUserAgent(?string $userAgent): array
    {
        if (empty($userAgent) || $userAgent === 'Standard Web Browser' || $userAgent === 'CLI/System') {
            return [
                'short' => 'Web Browser',
                'browser' => 'Web Browser Standar',
                'platform' => 'Komputer Desktop',
                'device' => 'Desktop',
                'raw' => $userAgent ?: '-',
            ];
        }

        // Deteksi Platform / OS
        $os = 'Komputer';
        $isMobile = false;

        if (stripos($userAgent, 'Windows NT 10.0') !== false || stripos($userAgent, 'Windows 11') !== false) {
            $os = 'Windows 10/11';
        } elseif (stripos($userAgent, 'Windows NT') !== false || stripos($userAgent, 'Windows') !== false) {
            $os = 'Windows';
        } elseif (stripos($userAgent, 'Android') !== false) {
            $os = 'Android';
            $isMobile = true;
        } elseif (stripos($userAgent, 'iPhone') !== false) {
            $os = 'iPhone (iOS)';
            $isMobile = true;
        } elseif (stripos($userAgent, 'iPad') !== false) {
            $os = 'iPad (iPadOS)';
            $isMobile = true;
        } elseif (stripos($userAgent, 'Macintosh') !== false || stripos($userAgent, 'Mac OS X') !== false) {
            $os = 'macOS Apple';
        } elseif (stripos($userAgent, 'Linux') !== false) {
            $os = 'Linux';
        }

        // Deteksi Browser
        $browser = 'Browser Web';
        if (stripos($userAgent, 'Edg/') !== false) {
            $browser = 'Microsoft Edge';
        } elseif (stripos($userAgent, 'OPR/') !== false || stripos($userAgent, 'Opera') !== false) {
            $browser = 'Opera';
        } elseif (stripos($userAgent, 'Chrome/') !== false) {
            $browser = 'Chrome';
        } elseif (stripos($userAgent, 'Firefox/') !== false) {
            $browser = 'Firefox';
        } elseif (stripos($userAgent, 'Safari/') !== false) {
            $browser = 'Safari';
        } elseif (stripos($userAgent, 'Postman') !== false) {
            $browser = 'Postman API';
        }

        $short = "{$browser} di {$os}";

        return [
            'short' => $short,
            'browser' => $browser,
            'platform' => $os,
            'device' => $isMobile ? 'Perangkat Seluler' : 'Komputer Desktop',
            'raw' => $userAgent,
        ];
    }

    /**
     * Format attribute_changes mentah dari database menjadi daftar informasi yang ramah manusia:
     * - Tanpa ID numerik mentah (meresolusi foreign keys ke nama asli).
     * - Tanpa JSON mentah (menguraikan array/json menjadi teks terstruktur).
     * - Memfilter field internal teknis (id, created_at, updated_at, password hash).
     * - Memformat angka menjadi Rupiah dan tanggal menjadi tanggal Indonesia.
     */
    public static function formatAttributeChanges(array $rawChanges, ?string $subjectType = null): array
    {
        if (empty($rawChanges)) {
            return [];
        }

        // Daftar key internal yang tidak perlu ditampilkan ke user
        $ignoredKeys = [
            'id', 'created_at', 'updated_at', 'deleted_at',
            'remember_token', 'two_factor_secret', 'two_factor_recovery_codes',
            'email_verified_at', 'uuid',
        ];

        $results = [];

        foreach ($rawChanges as $key => $val) {
            if (in_array($key, $ignoredKeys, true)) {
                continue;
            }

            // Khusus password hash: JANGAN tampilkan hash kriptografi mentah!
            if ($key === 'password') {
                $results[] = [
                    'key' => 'password',
                    'label' => 'Kata Sandi (Password)',
                    'value' => 'Telah diperbarui (Dirahasiakan demi keamanan)',
                    'type' => 'badge',
                    'badge_variant' => 'amber',
                ];
                continue;
            }

            $label = self::resolveFieldLabel($key);
            $formatted = self::resolveFieldValue($key, $val, $subjectType);

            $results[] = [
                'key' => $key,
                'label' => $label,
                'value' => $formatted['value'],
                'type' => $formatted['type'],
                'badge_variant' => $formatted['badge_variant'] ?? null,
                'sub_items' => $formatted['sub_items'] ?? null,
            ];
        }

        return $results;
    }

    /**
     * Terjemahkan nama kolom database ke label Bahasa Indonesia yang ramah.
     */
    protected static function resolveFieldLabel(string $key): string
    {
        $labels = [
            // Relasi & Pengenal
            'siswa_id' => 'Nama Siswa Penerima',
            'guru_id' => 'Nama Guru',
            'shadow_teacher_id' => 'Guru Pendamping (Shadow Teacher)',
            'petugas_id' => 'Petugas Pelaksana',
            'user_id' => 'Nama Pengguna (Akun)',
            'causer_id' => 'Pelaku Aktivitas',
            'kelas_id' => 'Rombongan Belajar (Kelas)',
            'tahun_ajaran_id' => 'Tahun Ajaran',
            'semester_id' => 'Semester Akademik',
            'jenis_tagihan_id' => 'Jenis Tagihan',
            'tagihan_id' => 'Tagihan Terkait',
            'kategori_pengeluaran_id' => 'Kategori Pos Pengeluaran',
            'role_id' => 'Peran Pengguna (Role)',
            'mata_pelajaran_id' => 'Mata Pelajaran',

            // Data Pribadi & Profil
            'nama' => 'Nama Lengkap',
            'nama_lengkap' => 'Nama Lengkap',
            'name' => 'Nama Lengkap',
            'nama_guru' => 'Nama Guru',
            'nama_siswa' => 'Nama Siswa',
            'username' => 'Nama Akun (Username)',
            'email' => 'Alamat Email',
            'nis' => 'Nomor Induk Siswa (NIS)',
            'nisn' => 'NISN',
            'nip' => 'NIP',
            'jenis_kelamin' => 'Jenis Kelamin',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'alamat' => 'Alamat Lengkap',
            'no_hp' => 'Nomor WhatsApp atau HP',
            'telepon' => 'Nomor Telepon',

            // Keuangan & Angka
            'nominal' => 'Nominal Tagihan',
            'jumlah' => 'Jumlah Uang',
            'total' => 'Total Keseluruhan',
            'saldo' => 'Saldo Tabungan',
            'nominal_dibayar' => 'Nominal yang Dibayar',
            'total_dibayar' => 'Total Pembayaran',
            'sisa_tagihan' => 'Sisa Tagihan Belum Lunas',
            'kembalian' => 'Uang Kembalian',
            'biaya' => 'Biaya',
            'debit' => 'Pemasukan (Debit)',
            'kredit' => 'Pengeluaran (Kredit)',
            'nominal_default' => 'Nominal Standar',

            // Periode & Waktu
            'bulan' => 'Bulan Periode',
            'tahun' => 'Tahun',
            'tanggal' => 'Tanggal Transaksi',
            'tanggal_bayar' => 'Tanggal Pembayaran',
            'jatuh_tempo' => 'Tanggal Jatuh Tempo',
            'tanggal_mulai' => 'Tanggal Mulai Berlaku',
            'tanggal_selesai' => 'Tanggal Batas Akhir',

            // Status & Keterangan
            'status' => 'Status Data',
            'status_aktif' => 'Status Keaktifan',
            'is_aktif' => 'Status Aktif',
            'is_blocking' => 'Sifat Tagihan (Blokir Ujian)',
            'keterangan' => 'Keterangan Tambahan',
            'deskripsi' => 'Deskripsi Aktivitas',
            'catatan' => 'Catatan Khusus',
            'alasan' => 'Alasan Perubahan',
            'metode_pembayaran' => 'Metode Pembayaran',
            'no_resi' => 'Nomor Kwitansi',
            'bukti' => 'Berkas Bukti',
            'kategori' => 'Kategori Data',
            'jenis' => 'Jenis Data',
            'tingkat' => 'Tingkat Pendidikan',
        ];

        return $labels[$key] ?? Str::headline(str_replace('_id', '', $key));
    }

    /**
     * Resolusi nilai field database menjadi nilai yang bersih dan manusiawi.
     */
    protected static function resolveFieldValue(string $key, $val, ?string $subjectType = null): array
    {
        // 1. Tangani nilai NULL atau kosong
        if (is_null($val) || $val === '') {
            return [
                'type' => 'text',
                'value' => '-',
            ];
        }

        // 2. Tangani Boolean
        if (is_bool($val)) {
            return [
                'type' => 'badge',
                'value' => $val ? 'Ya / Aktif' : 'Tidak / Nonaktif',
                'badge_variant' => $val ? 'emerald' : 'stone',
            ];
        }

        // 3. Tangani Array atau JSON bersarang (JANGAN TAMPILKAN JSON MENTAH!)
        if (is_array($val)) {
            return self::formatArrayHumanReadable($val);
        }

        // Cek jika string adalah JSON valid
        if (is_string($val) && (str_starts_with(trim($val), '{') || str_starts_with(trim($val), '['))) {
            $decoded = json_decode($val, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return self::formatArrayHumanReadable($decoded);
            }
        }

        // 4. Resolusi Foreign Key *_id ke Nama Nyata Model Terkait
        if (str_ends_with($key, '_id')) {
            $resolvedRel = self::resolveForeignKeyValue($key, (int)$val);
            if ($resolvedRel !== null) {
                return [
                    'type' => 'text_highlight',
                    'value' => $resolvedRel,
                ];
            }
        }

        // 5. Format Nominal Uang (Rupiah)
        $currencyKeys = [
            'nominal', 'jumlah', 'total', 'saldo', 'nominal_dibayar',
            'total_dibayar', 'sisa_tagihan', 'kembalian', 'biaya', 'debit',
            'kredit', 'nominal_default'
        ];
        if (in_array($key, $currencyKeys, true) && is_numeric($val)) {
            return [
                'type' => 'currency',
                'value' => 'Rp ' . number_format((float)$val, 0, ',', '.'),
            ];
        }

        // 6. Format Tanggal
        $dateKeys = [
            'tanggal', 'tanggal_bayar', 'jatuh_tempo', 'tanggal_mulai',
            'tanggal_selesai', 'tanggal_lahir'
        ];
        if (in_array($key, $dateKeys, true) && is_string($val) && preg_match('/^\d{4}-\d{2}-\d{2}/', $val)) {
            try {
                $carbon = Carbon::parse($val);
                $formattedDate = $carbon->translatedFormat('d F Y');
                if (strlen($val) > 10 && $carbon->format('H:i:s') !== '00:00:00') {
                    $formattedDate .= ' (' . $carbon->format('H:i') . ' WIB)';
                }
                return [
                    'type' => 'text',
                    'value' => $formattedDate,
                ];
            } catch (\Throwable $e) {
                // Fallback jika parse gagal
            }
        }

        // 7. Format Status
        if ($key === 'status') {
            $statusMap = [
                'lunas' => ['Lunas', 'emerald'],
                'belum_bayar' => ['Belum Bayar', 'rose'],
                'sebagian' => ['Dicicil / Sebagian', 'amber'],
                'aktif' => ['Aktif', 'emerald'],
                'nonaktif' => ['Nonaktif', 'stone'],
                'pending' => ['Menunggu Verifikasi', 'amber'],
                'disetujui' => ['Disetujui', 'emerald'],
                'ditolak' => ['Ditolak', 'rose'],
            ];

            $s = strtolower((string)$val);
            if (isset($statusMap[$s])) {
                return [
                    'type' => 'badge',
                    'value' => $statusMap[$s][0],
                    'badge_variant' => $statusMap[$s][1],
                ];
            }
        }

        // 8. Format Jenis Kelamin
        if ($key === 'jenis_kelamin') {
            $jk = strtoupper((string)$val);
            return [
                'type' => 'text',
                'value' => ($jk === 'L' || $jk === 'LAKI-LAKI') ? 'Laki-laki' : 'Perempuan',
            ];
        }

        // 9. Format Metode Pembayaran
        if ($key === 'metode_pembayaran') {
            $m = strtolower((string)$val);
            $mMap = [
                'tunai' => 'Tunai / Cash Langsung',
                'transfer' => 'Transfer Bank',
                'tabungan' => 'Potong Saldo Tabungan Siswa',
                'qris' => 'QRIS Pembayaran Digital',
            ];
            return [
                'type' => 'text',
                'value' => $mMap[$m] ?? Str::headline($m),
            ];
        }

        return [
            'type' => 'text',
            'value' => (string)$val,
        ];
    }

    /**
     * Resolusi foreign key ID ke nama record aslinya dari DB.
     */
    protected static function resolveForeignKeyValue(string $key, int $id): ?string
    {
        $cacheKey = "{$key}_{$id}";
        if (isset(self::$relationCache[$cacheKey])) {
            return self::$relationCache[$cacheKey];
        }

        $result = null;

        try {
            switch ($key) {
                case 'siswa_id':
                    $siswa = Siswa::with('user')->find($id);
                    if ($siswa) {
                        $nama = $siswa->user->nama ?? 'Siswa';
                        $nis = $siswa->nis ? " (NIS: {$siswa->nis})" : '';
                        $result = "{$nama}{$nis}";
                    }
                    break;

                case 'guru_id':
                    $guru = Guru::with('user')->find($id);
                    if ($guru) {
                        $result = $guru->user->nama ?? 'Guru';
                    }
                    break;

                case 'shadow_teacher_id':
                    $user = User::find($id);
                    if ($user) {
                        $result = "{$user->nama} (Guru Pendamping)";
                    } else {
                        $guru = Guru::with('user')->find($id);
                        $result = ($guru->user->nama ?? 'Guru Pendamping');
                    }
                    break;

                case 'petugas_id':
                case 'user_id':
                case 'causer_id':
                    $user = User::with('role')->find($id);
                    if ($user) {
                        $roleName = $user->role->nama ?? '';
                        $roleText = $roleName ? ' (' . ucwords(str_replace('_', ' ', $roleName)) . ')' : '';
                        $result = "{$user->nama}{$roleText}";
                    }
                    break;

                case 'kelas_id':
                    $kelas = Kelas::find($id);
                    if ($kelas) {
                        $result = "Kelas {$kelas->nama_kelas}";
                    }
                    break;

                case 'tahun_ajaran_id':
                    $ta = TahunAjaran::find($id);
                    if ($ta) {
                        $result = "T.A. {$ta->nama}";
                    }
                    break;

                case 'semester_id':
                    $sem = Semester::find($id);
                    if ($sem) {
                        $result = "Semester " . ucfirst($sem->semester);
                    }
                    break;

                case 'jenis_tagihan_id':
                    $jt = JenisTagihan::find($id);
                    if ($jt) {
                        $result = $jt->nama;
                    }
                    break;

                case 'tagihan_id':
                    $tagihan = Tagihan::with(['jenisTagihan', 'siswa.user'])->find($id);
                    if ($tagihan) {
                        $jNama = $tagihan->jenisTagihan->nama ?? 'Tagihan';
                        $bNama = $tagihan->bulan ?? '';
                        $sNama = $tagihan->siswa->user->nama ?? 'Siswa';
                        $result = "{$jNama} ({$bNama}) - {$sNama}";
                    }
                    break;

                case 'kategori_pengeluaran_id':
                    $kp = KategoriPengeluaran::find($id);
                    if ($kp) {
                        $result = $kp->nama;
                    }
                    break;

                case 'role_id':
                    $role = Role::find($id);
                    if ($role) {
                        $result = ucwords(str_replace('_', ' ', $role->nama));
                    }
                    break;

                case 'mata_pelajaran_id':
                    $mapel = MataPelajaran::find($id);
                    if ($mapel) {
                        $result = $mapel->nama_pelajaran ?? $mapel->nama;
                    }
                    break;
            }
        } catch (\Throwable $e) {
            $result = null;
        }

        if ($result === null) {
            // Fallback ramah jika data di DB sudah dihapus (soft-delete / force delete)
            $result = "Data Terkait (ID #{$id})";
        }

        self::$relationCache[$cacheKey] = $result;
        return $result;
    }

    /**
     * Format array menjadi struktur yang ramah manusia (bukan raw JSON string).
     */
    protected static function formatArrayHumanReadable(array $arr): array
    {
        if (empty($arr)) {
            return [
                'type' => 'text',
                'value' => '(Data Kosong)',
            ];
        }

        $items = [];
        foreach ($arr as $k => $v) {
            $subLabel = is_string($k) ? self::resolveFieldLabel($k) : "Poin " . ($k + 1);
            if (is_array($v)) {
                $subVal = implode(', ', array_map(function ($sk, $sv) {
                    return is_string($sk) ? "{$sk}: {$sv}" : (string)$sv;
                }, array_keys($v), $v));
            } elseif (is_numeric($v) && (str_contains(strtolower((string)$k), 'jumlah') || str_contains(strtolower((string)$k), 'nominal'))) {
                $subVal = 'Rp ' . number_format((float)$v, 0, ',', '.');
            } else {
                $subVal = is_bool($v) ? ($v ? 'Ya' : 'Tidak') : (string)$v;
            }
            $items[] = "{$subLabel}: {$subVal}";
        }

        return [
            'type' => 'list',
            'value' => implode(' • ', $items),
            'sub_items' => $items,
        ];
    }

    /**
     * Format properti konteks request (menghapus IP & User Agent jika sudah ada di header).
     */
    public static function formatProperties(array $rawProps): array
    {
        if (empty($rawProps)) {
            return [];
        }

        $excludeKeys = ['ip_address', 'user_agent'];
        $results = [];

        foreach ($rawProps as $key => $val) {
            if (in_array($key, $excludeKeys, true)) {
                continue;
            }

            $label = match ($key) {
                'role' => 'Peran Saat Aksi (Role)',
                'attempted_username' => 'Username yang Dicoba',
                'guard' => 'Portal Autentikasi',
                'file_name' => 'Nama Berkas Ekspor',
                'total_records' => 'Jumlah Data Diproses',
                'reason' => 'Alasan Aksi',
                'module' => 'Nama Modul',
                default => Str::headline($key),
            };

            if (is_array($val)) {
                $formattedVal = self::formatArrayHumanReadable($val)['value'];
            } else {
                $formattedVal = (string)$val;
            }

            $results[] = [
                'label' => $label,
                'value' => $formattedVal,
            ];
        }

        return $results;
    }
}
