<?php

namespace App\Livewire\Shared\Laporan;

use Livewire\Component;
use App\Models\Guru;
use App\Models\AbsensiGuru;
use App\Models\KalenderAkademik;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class RekapAbsensiGuru extends Component
{
    public $bulan;
    public $tahun;

    public function mount()
    {
        $roleName = auth()->user()->role->nama ?? '';
        if (!in_array($roleName, ['super_admin', 'tata_usaha', 'kepala_sekolah', 'pengawas', 'koordinator'])) {
            abort(403, 'Unauthorized.');
        }

        $this->bulan = intval(date('m'));
        $this->tahun = intval(date('Y'));
    }

    public function setPeriodPreset(string $preset)
    {
        if ($preset === 'this_month') {
            $this->bulan = intval(date('m'));
            $this->tahun = intval(date('Y'));
        } elseif ($preset === 'last_month') {
            $lastMonth = Carbon::now()->subMonth();
            $this->bulan = intval($lastMonth->format('m'));
            $this->tahun = intval($lastMonth->format('Y'));
        }
    }

    public function getMatrixData()
    {
        $gurus = Guru::with('user')
            ->where('status_aktif', true)
            ->join('users', 'guru.user_id', '=', 'users.id')
            ->orderBy('users.nama', 'asc')
            ->select('guru.*')
            ->get();

        $start = Carbon::create(intval($this->tahun), intval($this->bulan), 1)->startOfMonth();
        $end = Carbon::create(intval($this->tahun), intval($this->bulan), 1)->endOfMonth();
        $daysInMonth = $start->daysInMonth;

        $startDateStr = $start->format('Y-m-d');
        $endDateStr = $end->format('Y-m-d');

        // Fetch all attendance records in one batch query
        $absensiRecords = AbsensiGuru::whereBetween('tanggal', [$startDateStr, $endDateStr])
            ->get();

        // Build a fast lookup map
        $absensiMap = [];
        foreach ($absensiRecords as $record) {
            $recordDate = Carbon::parse($record->tanggal)->format('Y-m-d');
            $absensiMap[$record->guru_id . '_' . $recordDate] = $record;
        }

        // Fetch all holiday calendar records for this month in ONE query
        $holidays = KalenderAkademik::where('liburkan_presensi', true)
            ->where(function ($q) use ($startDateStr, $endDateStr) {
                $q->whereBetween('tanggal_mulai', [$startDateStr, $endDateStr])
                  ->orWhereBetween('tanggal_selesai', [$startDateStr, $endDateStr])
                  ->orWhere(function ($sub) use ($startDateStr, $endDateStr) {
                      $sub->where('tanggal_mulai', '<=', $startDateStr)
                          ->where('tanggal_selesai', '>=', $endDateStr);
                  });
            })
            ->get(['tanggal_mulai', 'tanggal_selesai']);

        // Precompute holiday dates in memory (0 queries inside loop)
        $holidayDayMap = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dStr = sprintf('%s-%02d-%02d', $this->tahun, intval($this->bulan), $d);
            $isHoliday = false;
            foreach ($holidays as $h) {
                $hStart = $h->tanggal_mulai ? $h->tanggal_mulai->format('Y-m-d') : null;
                $hEnd = $h->tanggal_selesai ? $h->tanggal_selesai->format('Y-m-d') : null;
                if ($hStart && $hEnd && $hStart <= $dStr && $hEnd >= $dStr) {
                    $isHoliday = true;
                    break;
                }
            }

            // Automatically recognize Sundays and official Indonesian national holidays (Tanggal Merah)
            if (!$isHoliday) {
                try {
                    $isSunday = \Carbon\Carbon::createFromDate((int) $this->tahun, (int) $this->bulan, $d)->isSunday();
                } catch (\Throwable $e) {
                    $isSunday = false;
                }
                $isHoliday = $isSunday || KalenderAkademik::isNationalHoliday($dStr);
            }

            $holidayDayMap[$d] = $isHoliday;
        }

        $matrix = [];
        foreach ($gurus as $guru) {
            $hadir = 0;
            $telat = 0;
            $izin = 0;
            $tidakHadir = 0;
            $days = [];

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $dateStr = sprintf('%s-%02d-%02d', $this->tahun, intval($this->bulan), $day);
                $record = $absensiMap[$guru->id . '_' . $dateStr] ?? null;

                if ($record) {
                    $status = $record->status;
                    if ($status === 'hadir') {
                        $hadir++;
                    } elseif ($status === 'telat') {
                        $telat++;
                    } elseif ($status === 'izin') {
                        $izin++;
                    } elseif ($status === 'tidak_hadir') {
                        $tidakHadir++;
                    }
                    $days[$day] = $status;
                } else {
                    $days[$day] = $holidayDayMap[$day] ? 'libur' : null;
                }
            }

            $totalHadir = $hadir + $telat;
            $totalRecorded = $totalHadir + $izin + $tidakHadir;
            $rate = $totalRecorded > 0 ? round(($totalHadir / $totalRecorded) * 100, 1) : 100.0;

            $matrix[] = [
                'guru' => $guru,
                'days' => $days,
                'hadir' => $hadir,
                'telat' => $telat,
                'izin' => $izin,
                'tidak_hadir' => $tidakHadir,
                'rate' => $rate
            ];
        }

        return [
            'matrix' => $matrix,
            'daysInMonth' => $daysInMonth
        ];
    }

    public function downloadExcel()
    {
        $data = $this->getMatrixData();
        if (empty($data['matrix'])) {
            session()->flash('error', 'Tidak ada data absensi guru untuk diekspor ke Excel.');
            return;
        }

        $bulanNames = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        $monthKey = sprintf('%02d', intval($this->bulan));
        $namaBulan = $bulanNames[$monthKey] ?? 'Bulan ' . $this->bulan;
        $tahun = $this->tahun;
        $daysInMonth = $data['daysInMonth'];
        $matrix = $data['matrix'];

        $filename = 'rekap-absensi-guru-' . $this->bulan . '-' . $this->tahun . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($matrix, $daysInMonth, $namaBulan, $tahun) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Microsoft Excel
            fputs($file, "\xEF\xBB\xBF");

            // Title & Metadata
            fputcsv($file, ['REKAPITULASI ABSENSI GURU & TENAGA PENDIDIK']);
            fputcsv($file, ['Periode', $namaBulan . ' ' . $tahun]);
            fputcsv($file, ['Tanggal Ekspor', date('d/m/Y H:i')]);
            fputcsv($file, []);

            // Build Header Row
            $headerRow = ['No', 'NIP', 'Nama Guru'];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $headerRow[] = (string)$d;
            }
            $headerRow[] = 'Hadir (H)';
            $headerRow[] = 'Terlambat (T)';
            $headerRow[] = 'Izin (I)';
            $headerRow[] = 'Alpa (A)';
            $headerRow[] = 'Kehadiran (%)';

            fputcsv($file, $headerRow);

            // Data Rows
            $no = 1;
            $statusCodes = [
                'hadir' => 'H',
                'telat' => 'T',
                'izin' => 'I',
                'tidak_hadir' => 'A',
                'libur' => 'L',
            ];

            foreach ($matrix as $row) {
                $nip = $row['guru']->nip ? '="' . $row['guru']->nip . '"' : '-';
                $nama = $row['guru']->user->nama ?? '-';

                $dataRow = [$no++, $nip, $nama];

                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $dayStatus = $row['days'][$d] ?? null;
                    $code = $dayStatus ? ($statusCodes[$dayStatus] ?? strtoupper($dayStatus)) : '-';
                    $dataRow[] = $code;
                }

                $dataRow[] = $row['hadir'];
                $dataRow[] = $row['telat'];
                $dataRow[] = $row['izin'];
                $dataRow[] = $row['tidak_hadir'];
                $dataRow[] = $row['rate'] . '%';

                fputcsv($file, $dataRow);
            }

            fputcsv($file, []);
            fputcsv($file, ['Keterangan Status:']);
            fputcsv($file, ['H = Hadir Tepat Waktu', 'T = Hadir Terlambat', 'I = Izin / Sakit', 'A = Alpa / Tanpa Keterangan', 'L = Hari Libur', '- = Belum Diinput']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function downloadPdf()
    {
        $data = $this->getMatrixData();
        if (empty($data['matrix'])) {
            session()->flash('error', 'Tidak ada data absensi guru untuk dicetak.');
            return;
        }

        $bulanNames = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        $monthKey = sprintf('%02d', intval($this->bulan));

        $pdfData = [
            'matrix' => $data['matrix'],
            'daysInMonth' => $data['daysInMonth'],
            'namaBulan' => $bulanNames[$monthKey] ?? 'Bulan ' . $this->bulan,
            'tahun' => $this->tahun
        ];

        $pdf = Pdf::loadView('livewire.shared.laporan.pdf-absensi-guru', $pdfData)
            ->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'rekap-absensi-guru-' . $this->bulan . '-' . $this->tahun . '.pdf');
    }

    public function render()
    {
        $data = $this->getMatrixData();

        return view('livewire.shared.laporan.rekap-absensi-guru', $data)
            ->layout('components.layouts.app', ['title' => 'Rekap Absensi Guru']);
    }
}
