<?php

namespace App\Livewire\TataUsaha;

use Livewire\Component;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;

class Dashboard extends Component
{
    public int $totalSiswa = 0;
    public int $totalGuru = 0;
    public int $totalKelas = 0;

    public function mount(): void
    {
        $this->totalSiswa = Siswa::where('status', 'aktif')->count();
        $this->totalGuru = Guru::where('status_aktif', true)->count();
        $this->totalKelas = Kelas::count();
    }

    public function render()
    {
        return view('livewire.tata-usaha.dashboard')
            ->layout('components.layouts.app', ['title' => 'Dashboard Tata Usaha']);
    }
}
