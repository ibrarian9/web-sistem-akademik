<?php

namespace App\Livewire\Murid;

use Livewire\Component;
use App\Models\Siswa;
use App\Models\AbsensiSiswa;
use Illuminate\Support\Facades\DB;

class KehadiranSaya extends Component
{
    public int $totalHadir = 0;
    public int $totalIzin = 0;
    public int $totalTidakHadir = 0;
    public float $rate = 100.00;
    public array $history = [];

    public function mount()
    {
        $this->loadAttendance();
    }

    public function loadAttendance()
    {
        $siswa = auth()->user()->siswa;
        if (!$siswa) {
            return;
        }

        $records = AbsensiSiswa::where('siswa_id', $siswa->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        $this->totalHadir = $records->where('status', 'hadir')->count();
        $this->totalIzin = $records->where('status', 'izin')->count();
        $this->totalTidakHadir = $records->where('status', 'tidak_hadir')->count();

        $total = $records->count();
        if ($total > 0) {
            $this->rate = round(($this->totalHadir / $total) * 100, 2);
        } else {
            $this->rate = 100.00;
        }

        $this->history = $records->map(fn($item) => [
            'tanggal' => date('d-m-Y', strtotime($item->tanggal)),
            'status' => $item->status,
            'catatan' => $item->catatan ?: '-',
        ])->toArray();
    }

    public function render()
    {
        return view('livewire.murid.kehadiran-saya')
            ->layout('components.layouts.app', ['title' => 'Kehadiran Saya']);
    }
}
