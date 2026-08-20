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
        $gurus = Guru::where('status_aktif', true)
            ->join('users', 'guru.user_id', '=', 'users.id')
            ->orderBy('users.nama', 'asc')
            ->select('guru.*')
            ->get();

        $start = Carbon::create(intval($this->tahun), intval($this->bulan), 1)->startOfMonth();
        $end = Carbon::create(intval($this->tahun), intval($this->bulan), 1)->endOfMonth();
        $daysInMonth = $start->daysInMonth;

        $absensiRecords = AbsensiGuru::whereBetween('tanggal', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->get();

        // Build a robust lookup map
        $absensiMap = [];
        foreach ($absensiRecords as $record) {
            $recordDate = Carbon::parse($record->tanggal)->format('Y-m-d');
            $absensiMap[$record->guru_id . '_' . $recordDate] = $record;
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
                    $isHoliday = KalenderAkademik::isHolidayDate($dateStr);
                    $days[$day] = $isHoliday ? 'libur' : null;
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
