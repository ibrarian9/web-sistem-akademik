<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class KalenderAkademik extends Model
{
    use HasFactory, Auditable;

    protected $table = 'kalender_akademik';

    protected $fillable = [
        'tahun_ajaran_id',
        'nama_kegiatan',
        'jenis',
        'tanggal_mulai',
        'tanggal_selesai',
        'liburkan_presensi',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'liburkan_presensi' => 'boolean',
    ];

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    /**
     * Get official Indonesian national public holidays for a given year.
     * Includes fixed dates (1 Jan, 1 Mei, 1 Jun, 17 Agt, 25 Des)
     * and dynamic religious dates for 2024 through 2028.
     *
     * @param int $year
     * @return array<int, array{nama: string, tanggal_mulai: string, tanggal_selesai: string, jenis: string, keterangan: string}>
     */
    public static function getNationalHolidays(int $year): array
    {
        // Fixed annual public holidays in Indonesia
        $fixed = [
            [
                'nama' => 'Tahun Baru Masehi',
                'tanggal_mulai' => "{$year}-01-01",
                'tanggal_selesai' => "{$year}-01-01",
                'jenis' => 'hari_libur',
                'keterangan' => 'Libur Nasional Resmi (Tanggal Merah)',
            ],
            [
                'nama' => 'Hari Buruh Internasional',
                'tanggal_mulai' => "{$year}-05-01",
                'tanggal_selesai' => "{$year}-05-01",
                'jenis' => 'hari_libur',
                'keterangan' => 'Libur Nasional Resmi (Tanggal Merah)',
            ],
            [
                'nama' => 'Hari Lahir Pancasila',
                'tanggal_mulai' => "{$year}-06-01",
                'tanggal_selesai' => "{$year}-06-01",
                'jenis' => 'hari_libur',
                'keterangan' => 'Libur Nasional Resmi (Tanggal Merah)',
            ],
            [
                'nama' => 'Hari Kemerdekaan RI (HUT RI)',
                'tanggal_mulai' => "{$year}-08-17",
                'tanggal_selesai' => "{$year}-08-17",
                'jenis' => 'hari_libur',
                'keterangan' => 'Libur Nasional Resmi (Tanggal Merah)',
            ],
            [
                'nama' => 'Hari Raya Natal',
                'tanggal_mulai' => "{$year}-12-25",
                'tanggal_selesai' => "{$year}-12-25",
                'jenis' => 'hari_libur',
                'keterangan' => 'Libur Nasional Resmi (Tanggal Merah)',
            ],
        ];

        // Dynamic religious and lunar dates by year
        $dynamicByYear = [
            2024 => [
                ['nama' => 'Isra Mi\'raj Nabi Muhammad SAW', 'tanggal_mulai' => '2024-02-08', 'tanggal_selesai' => '2024-02-08'],
                ['nama' => 'Tahun Baru Imlek 2575 Kongzili', 'tanggal_mulai' => '2024-02-10', 'tanggal_selesai' => '2024-02-10'],
                ['nama' => 'Hari Suci Nyepi (Tahun Baru Saka 1946)', 'tanggal_mulai' => '2024-03-11', 'tanggal_selesai' => '2024-03-11'],
                ['nama' => 'Wafat Yesus Kristus', 'tanggal_mulai' => '2024-03-29', 'tanggal_selesai' => '2024-03-29'],
                ['nama' => 'Hari Paskah', 'tanggal_mulai' => '2024-03-31', 'tanggal_selesai' => '2024-03-31'],
                ['nama' => 'Hari Raya Idul Fitri 1445 H', 'tanggal_mulai' => '2024-04-10', 'tanggal_selesai' => '2024-04-11'],
                ['nama' => 'Kenaikan Yesus Kristus', 'tanggal_mulai' => '2024-05-09', 'tanggal_selesai' => '2024-05-09'],
                ['nama' => 'Hari Raya Waisak 2568 BE', 'tanggal_mulai' => '2024-05-23', 'tanggal_selesai' => '2024-05-23'],
                ['nama' => 'Hari Raya Idul Adha 1445 H', 'tanggal_mulai' => '2024-06-17', 'tanggal_selesai' => '2024-06-17'],
                ['nama' => 'Tahun Baru Islam 1446 H (1 Muharram)', 'tanggal_mulai' => '2024-07-07', 'tanggal_selesai' => '2024-07-07'],
                ['nama' => 'Maulid Nabi Muhammad SAW', 'tanggal_mulai' => '2024-09-16', 'tanggal_selesai' => '2024-09-16'],
            ],
            2025 => [
                ['nama' => 'Isra Mi\'raj Nabi Muhammad SAW', 'tanggal_mulai' => '2025-01-27', 'tanggal_selesai' => '2025-01-27'],
                ['nama' => 'Tahun Baru Imlek 2576 Kongzili', 'tanggal_mulai' => '2025-01-29', 'tanggal_selesai' => '2025-01-29'],
                ['nama' => 'Hari Suci Nyepi (Tahun Baru Saka 1947)', 'tanggal_mulai' => '2025-03-29', 'tanggal_selesai' => '2025-03-29'],
                ['nama' => 'Hari Raya Idul Fitri 1446 H', 'tanggal_mulai' => '2025-03-31', 'tanggal_selesai' => '2025-04-01'],
                ['nama' => 'Wafat Yesus Kristus', 'tanggal_mulai' => '2025-04-18', 'tanggal_selesai' => '2025-04-18'],
                ['nama' => 'Hari Paskah', 'tanggal_mulai' => '2025-04-20', 'tanggal_selesai' => '2025-04-20'],
                ['nama' => 'Hari Raya Waisak 2569 BE', 'tanggal_mulai' => '2025-05-12', 'tanggal_selesai' => '2025-05-12'],
                ['nama' => 'Kenaikan Yesus Kristus', 'tanggal_mulai' => '2025-05-29', 'tanggal_selesai' => '2025-05-29'],
                ['nama' => 'Hari Raya Idul Adha 1446 H', 'tanggal_mulai' => '2025-06-07', 'tanggal_selesai' => '2025-06-07'],
                ['nama' => 'Tahun Baru Islam 1447 H (1 Muharram)', 'tanggal_mulai' => '2025-06-27', 'tanggal_selesai' => '2025-06-27'],
                ['nama' => 'Maulid Nabi Muhammad SAW', 'tanggal_mulai' => '2025-09-05', 'tanggal_selesai' => '2025-09-05'],
            ],
            2026 => [
                ['nama' => 'Isra Mi\'raj Nabi Muhammad SAW', 'tanggal_mulai' => '2026-01-16', 'tanggal_selesai' => '2026-01-16'],
                ['nama' => 'Tahun Baru Imlek 2577 Kongzili', 'tanggal_mulai' => '2026-02-17', 'tanggal_selesai' => '2026-02-17'],
                ['nama' => 'Hari Suci Nyepi (Tahun Baru Saka 1948)', 'tanggal_mulai' => '2026-03-19', 'tanggal_selesai' => '2026-03-19'],
                ['nama' => 'Hari Raya Idul Fitri 1447 H', 'tanggal_mulai' => '2026-03-20', 'tanggal_selesai' => '2026-03-21'],
                ['nama' => 'Wafat Yesus Kristus', 'tanggal_mulai' => '2026-04-03', 'tanggal_selesai' => '2026-04-03'],
                ['nama' => 'Hari Paskah', 'tanggal_mulai' => '2026-04-05', 'tanggal_selesai' => '2026-04-05'],
                ['nama' => 'Kenaikan Yesus Kristus', 'tanggal_mulai' => '2026-05-14', 'tanggal_selesai' => '2026-05-14'],
                ['nama' => 'Hari Raya Idul Adha 1447 H', 'tanggal_mulai' => '2026-05-27', 'tanggal_selesai' => '2026-05-27'],
                ['nama' => 'Hari Raya Waisak 2570 BE', 'tanggal_mulai' => '2026-05-31', 'tanggal_selesai' => '2026-05-31'],
                ['nama' => 'Tahun Baru Islam 1448 H (1 Muharram)', 'tanggal_mulai' => '2026-06-16', 'tanggal_selesai' => '2026-06-16'],
                ['nama' => 'Maulid Nabi Muhammad SAW', 'tanggal_mulai' => '2026-08-25', 'tanggal_selesai' => '2026-08-25'],
            ],
            2027 => [
                ['nama' => 'Isra Mi\'raj Nabi Muhammad SAW', 'tanggal_mulai' => '2027-01-06', 'tanggal_selesai' => '2027-01-06'],
                ['nama' => 'Tahun Baru Imlek 2578 Kongzili', 'tanggal_mulai' => '2027-02-06', 'tanggal_selesai' => '2027-02-06'],
                ['nama' => 'Hari Suci Nyepi (Tahun Baru Saka 1949)', 'tanggal_mulai' => '2027-03-09', 'tanggal_selesai' => '2027-03-09'],
                ['nama' => 'Hari Raya Idul Fitri 1448 H', 'tanggal_mulai' => '2027-03-10', 'tanggal_selesai' => '2027-03-11'],
                ['nama' => 'Wafat Yesus Kristus', 'tanggal_mulai' => '2027-03-26', 'tanggal_selesai' => '2027-03-26'],
                ['nama' => 'Hari Paskah', 'tanggal_mulai' => '2027-03-28', 'tanggal_selesai' => '2027-03-28'],
                ['nama' => 'Kenaikan Yesus Kristus', 'tanggal_mulai' => '2027-05-06', 'tanggal_selesai' => '2027-05-06'],
                ['nama' => 'Hari Raya Idul Adha 1448 H', 'tanggal_mulai' => '2027-05-16', 'tanggal_selesai' => '2027-05-16'],
                ['nama' => 'Hari Raya Waisak 2571 BE', 'tanggal_mulai' => '2027-05-20', 'tanggal_selesai' => '2027-05-20'],
                ['nama' => 'Tahun Baru Islam 1449 H (1 Muharram)', 'tanggal_mulai' => '2027-06-06', 'tanggal_selesai' => '2027-06-06'],
                ['nama' => 'Maulid Nabi Muhammad SAW', 'tanggal_mulai' => '2027-08-15', 'tanggal_selesai' => '2027-08-15'],
                ['nama' => 'Isra Mi\'raj Nabi Muhammad SAW 1449 H', 'tanggal_mulai' => '2027-12-26', 'tanggal_selesai' => '2027-12-26'],
            ],
            2028 => [
                ['nama' => 'Tahun Baru Imlek 2579 Kongzili', 'tanggal_mulai' => '2028-01-26', 'tanggal_selesai' => '2028-01-26'],
                ['nama' => 'Hari Raya Idul Fitri 1449 H', 'tanggal_mulai' => '2028-02-27', 'tanggal_selesai' => '2028-02-28'],
                ['nama' => 'Hari Suci Nyepi (Tahun Baru Saka 1950)', 'tanggal_mulai' => '2028-03-26', 'tanggal_selesai' => '2028-03-26'],
                ['nama' => 'Wafat Yesus Kristus', 'tanggal_mulai' => '2028-04-14', 'tanggal_selesai' => '2028-04-14'],
                ['nama' => 'Hari Raya Idul Adha 1449 H', 'tanggal_mulai' => '2028-05-05', 'tanggal_selesai' => '2028-05-05'],
                ['nama' => 'Hari Raya Waisak 2572 BE', 'tanggal_mulai' => '2028-05-09', 'tanggal_selesai' => '2028-05-09'],
                ['nama' => 'Kenaikan Yesus Kristus', 'tanggal_mulai' => '2028-05-25', 'tanggal_selesai' => '2028-05-25'],
                ['nama' => 'Tahun Baru Islam 1450 H (1 Muharram)', 'tanggal_mulai' => '2028-05-25', 'tanggal_selesai' => '2028-05-25'],
                ['nama' => 'Maulid Nabi Muhammad SAW', 'tanggal_mulai' => '2028-08-03', 'tanggal_selesai' => '2028-08-03'],
            ],
        ];

        $results = $fixed;
        if (isset($dynamicByYear[$year])) {
            foreach ($dynamicByYear[$year] as $item) {
                $results[] = array_merge([
                    'jenis' => 'hari_libur',
                    'keterangan' => 'Libur Nasional Resmi (Tanggal Merah)',
                ], $item);
            }
        }

        // Sort ascending by start date
        usort($results, fn($a, $b) => strcmp($a['tanggal_mulai'], $b['tanggal_mulai']));

        return $results;
    }

    /**
     * Check if a specific date string (Y-m-d) is an official Indonesian national holiday.
     */
    public static function isNationalHoliday(string $dateStr): bool
    {
        $year = (int) substr($dateStr, 0, 4);
        if ($year < 2000) return false;

        $holidays = self::getNationalHolidays($year);
        foreach ($holidays as $h) {
            if ($h['tanggal_mulai'] <= $dateStr && $h['tanggal_selesai'] >= $dateStr) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get detailed information about why a date is a holiday (Sunday, National Holiday, or DB record).
     *
     * @param string $dateStr (Y-m-d)
     * @return array{nama: string, jenis: string, liburkan_presensi: bool, is_tanggal_merah: bool, sumber: string}|null
     */
    public static function getHolidayInfo(string $dateStr): ?array
    {
        // 1. Check custom / explicitly recorded events in database first
        $dbRecord = self::where('liburkan_presensi', true)
            ->whereDate('tanggal_mulai', '<=', $dateStr)
            ->whereDate('tanggal_selesai', '>=', $dateStr)
            ->first();

        if ($dbRecord) {
            return [
                'nama' => $dbRecord->nama_kegiatan,
                'jenis' => $dbRecord->jenis,
                'liburkan_presensi' => (bool) $dbRecord->liburkan_presensi,
                'is_tanggal_merah' => ($dbRecord->jenis === 'hari_libur'),
                'sumber' => 'database',
            ];
        }

        // 2. Check official Indonesian national public holidays
        $year = (int) substr($dateStr, 0, 4);
        if ($year >= 2000) {
            $nationalHolidays = self::getNationalHolidays($year);
            foreach ($nationalHolidays as $h) {
                if ($h['tanggal_mulai'] <= $dateStr && $h['tanggal_selesai'] >= $dateStr) {
                    return [
                        'nama' => $h['nama'],
                        'jenis' => 'hari_libur',
                        'liburkan_presensi' => true,
                        'is_tanggal_merah' => true,
                        'sumber' => 'libur_nasional',
                    ];
                }
            }
        }

        // 3. Check if Sunday (Hari Ahad / Minggu)
        try {
            $parsed = \Carbon\Carbon::parse($dateStr);
            if ($parsed->isSunday()) {
                return [
                    'nama' => 'Hari Ahad / Minggu (Libur Akhir Pekan)',
                    'jenis' => 'hari_libur',
                    'liburkan_presensi' => true,
                    'is_tanggal_merah' => true,
                    'sumber' => 'mingguan',
                ];
            }
        } catch (\Throwable $e) {
            // Invalid date format
        }

        return null;
    }

    /**
     * Check if a specific date string (Y-m-d) is considered a holiday (presensi waived).
     * Returns true if Sunday, official national public holiday, or database holiday record.
     */
    public static function isHolidayDate(string $dateStr): bool
    {
        // 1. Every Sunday (Ahad/Minggu) is an official weekly red date (libur mingguan)
        try {
            $parsed = \Carbon\Carbon::parse($dateStr);
            if ($parsed->isSunday()) {
                return true;
            }
        } catch (\Throwable $e) {
            // Invalid date format
        }

        // 2. Official Indonesian National Public Holidays (Tanggal Merah)
        if (self::isNationalHoliday($dateStr)) {
            return true;
        }

        // 3. Database holiday records with liburkan_presensi = true
        return self::where('liburkan_presensi', true)
            ->whereDate('tanggal_mulai', '<=', $dateStr)
            ->whereDate('tanggal_selesai', '>=', $dateStr)
            ->exists();
    }
}
