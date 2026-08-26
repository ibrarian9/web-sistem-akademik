<?php

namespace App\Livewire\Shared\Laporan;

use Livewire\Component;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\AbsensiSiswa;
use App\Models\GuruMapelKelas;
use App\Models\KalenderAkademik;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class RekapAbsensiSiswa extends Component
{
    public $kelasId;
    public $bulan;
    public $tahun;

    public function mount()
    {
        $this->bulan = intval(date('m'));
        $this->tahun = intval(date('Y'));

        $classes = $this->getAvailableClasses();
        if ($classes->count() > 0) {
            $this->kelasId = $classes->first()->id;
        }
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

    public function getAvailableClasses()
    {
        $user = auth()->user();
        if ($user && $user->role && $user->role->nama === 'guru') {
            $guru = $user->guru;
            if ($guru) {
                $kelasIds = Kelas::where('guru_umum_id', $guru->id)
                    ->orWhere('guru_tahfidz_id', $guru->id)
                    ->pluck('id')
                    ->merge(
                        GuruMapelKelas::where('guru_id', $guru->id)->pluck('kelas_id')
                    )
                    ->unique();
                return Kelas::whereIn('id', $kelasIds)->orderBy('nama_kelas', 'asc')->get();
            }
            return collect();
        }
        return Kelas::orderBy('nama_kelas', 'asc')->get();
    }

    public function getMatrixData()
    {
        if (!$this->kelasId) {
            return [
                'matrix' => [],
                'daysInMonth' => 0,
                'kelas' => null
            ];
        }

        $kelas = Kelas::with(['guruUmum.user', 'guruTahfidz.user'])->find($this->kelasId);
        
        $students = Siswa::with('user')
            ->where(function ($q) {
                $q->where('siswa.kelas_id', $this->kelasId)
                  ->orWhere('siswa.kelas_tahfidz_id', $this->kelasId)
                  ->orWhereIn('siswa.id', function($sub) {
                      $sub->select('siswa_id')
                          ->from('absensi_siswa')
                          ->where('kelas_id', $this->kelasId);
                  });
            })
            ->where('siswa.status', 'aktif')
            ->join('users', 'siswa.user_id', '=', 'users.id')
            ->orderBy('users.nama', 'asc')
            ->select('siswa.*')
            ->get();

        $start = Carbon::create(intval($this->tahun), intval($this->bulan), 1)->startOfMonth();
        $end = Carbon::create(intval($this->tahun), intval($this->bulan), 1)->endOfMonth();
        $daysInMonth = $start->daysInMonth;

        $startDateStr = $start->format('Y-m-d');
        $endDateStr = $end->format('Y-m-d');

        $absensiRecords = AbsensiSiswa::where('kelas_id', $this->kelasId)
            ->whereBetween('tanggal', [$startDateStr, $endDateStr])
            ->get();

        // Build a robust lookup map
        $absensiMap = [];
        foreach ($absensiRecords as $record) {
            $recordDate = Carbon::parse($record->tanggal)->format('Y-m-d');
            $absensiMap[$record->siswa_id . '_' . $recordDate] = $record;
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
        foreach ($students as $siswa) {
            $hadir = 0;
            $izin = 0;
            $tidakHadir = 0;
            $days = [];

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $dateStr = sprintf('%s-%02d-%02d', $this->tahun, intval($this->bulan), $day);
                $record = $absensiMap[$siswa->id . '_' . $dateStr] ?? null;

                if ($record) {
                    $status = $record->status;
                    if ($status === 'hadir') {
                        $hadir++;
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

            $totalRecorded = $hadir + $izin + $tidakHadir;
            $rate = $totalRecorded > 0 ? round(($hadir / $totalRecorded) * 100, 1) : 100.0;

            $matrix[] = [
                'siswa' => $siswa,
                'days' => $days,
                'hadir' => $hadir,
                'izin' => $izin,
                'tidak_hadir' => $tidakHadir,
                'rate' => $rate
            ];
        }

        return [
            'matrix' => $matrix,
            'daysInMonth' => $daysInMonth,
            'kelas' => $kelas
        ];
    }

    public function downloadPdf()
    {
        $data = $this->getMatrixData();
        if (!$data['kelas'] || empty($data['matrix'])) {
            session()->flash('error', 'Tidak ada data absensi untuk dicetak.');
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
            'kelas' => $data['kelas'],
            'namaBulan' => $bulanNames[$monthKey] ?? 'Bulan ' . $this->bulan,
            'tahun' => $this->tahun
        ];

        $pdf = Pdf::loadView('livewire.shared.laporan.pdf-absensi-siswa', $pdfData)
            ->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'rekap-absensi-siswa-' . strtolower(str_replace(' ', '-', $data['kelas']->nama_kelas)) . '-' . $this->bulan . '-' . $this->tahun . '.pdf');
    }

    public function render()
    {
        $classes = $this->getAvailableClasses();
        $data = $this->getMatrixData();

        return view('livewire.shared.laporan.rekap-absensi-siswa', array_merge($data, [
            'classes' => $classes
        ]))->layout('components.layouts.app', ['title' => 'Rekap Absensi Siswa']);
    }
}
