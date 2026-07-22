<?php

namespace App\Livewire\Murid;

use Livewire\Component;
use App\Models\AbsensiSiswa;
use Carbon\Carbon;

class KehadiranSaya extends Component
{
    public int $totalHadir = 0;
    public int $totalIzin = 0;
    public int $totalSakit = 0;
    public int $totalTidakHadir = 0;
    public int $totalPertemuan = 0;
    public float $rate = 100.00;
    public string $performanceCategory = 'Sangat Baik';
    
    public string $selectedMonth = '';
    public string $selectedStatus = '';

    public function mount()
    {
        $this->loadAttendance();
    }

    public function updatedSelectedMonth()
    {
        $this->loadAttendance();
    }

    public function updatedSelectedStatus()
    {
        $this->loadAttendance();
    }

    public function loadAttendance()
    {
        $siswa = auth()->user()->siswa;
        if (!$siswa) {
            return;
        }

        $query = AbsensiSiswa::where('siswa_id', $siswa->id)
            ->orderBy('tanggal', 'desc');

        $allRecords = $query->get();

        // Summary counts (unfiltered by status to keep persistent stats cards)
        $this->totalHadir = $allRecords->where('status', 'hadir')->count();
        $this->totalIzin = $allRecords->where('status', 'izin')->count();
        $this->totalSakit = $allRecords->where('status', 'sakit')->count();
        $this->totalTidakHadir = $allRecords->where('status', 'tidak_hadir')->count();
        $this->totalPertemuan = $allRecords->count();

        if ($this->totalPertemuan > 0) {
            $this->rate = round(($this->totalHadir / $this->totalPertemuan) * 100, 1);
        } else {
            $this->rate = 100.00;
        }

        if ($this->rate >= 90) {
            $this->performanceCategory = 'Sangat Baik';
        } elseif ($this->rate >= 75) {
            $this->performanceCategory = 'Baik';
        } elseif ($this->rate >= 60) {
            $this->performanceCategory = 'Cukup';
        } else {
            $this->performanceCategory = 'Perlu Perhatian';
        }
    }

    public function render()
    {
        $siswa = auth()->user()->siswa;
        $history = [];

        if ($siswa) {
            $query = AbsensiSiswa::where('siswa_id', $siswa->id)
                ->orderBy('tanggal', 'desc');

            if (!empty($this->selectedMonth)) {
                $query->whereMonth('tanggal', Carbon::parse($this->selectedMonth)->month)
                      ->whereYear('tanggal', Carbon::parse($this->selectedMonth)->year);
            }

            if (!empty($this->selectedStatus)) {
                $query->where('status', $this->selectedStatus);
            }

            $records = $query->get();

            $history = $records->map(function ($item) {
                $carbonDate = Carbon::parse($item->tanggal);
                return [
                    'id' => $item->id,
                    'tanggal' => $carbonDate->isoFormat('D MMMM YYYY'),
                    'hari' => $carbonDate->isoFormat('dddd'),
                    'status' => $item->status,
                    'catatan' => $item->catatan ?: '-',
                    'guru' => $item->guru->user->nama ?? 'Wali Kelas / Guru Piket',
                ];
            })->toArray();
        }

        return view('livewire.murid.kehadiran-saya', [
            'history' => $history,
        ])->layout('components.layouts.app', ['title' => 'Kehadiran Saya']);
    }
}
