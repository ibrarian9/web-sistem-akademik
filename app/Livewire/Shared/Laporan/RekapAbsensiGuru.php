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
        if (!in_array($roleName, ['super_admin', 'tata_usaha'])) {
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
