<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Tagihan;

class Dashboard extends Component
{
    public int $totalSiswa = 0;
    public int $totalGuru = 0;
    public int $totalKelas = 0;
    public string $totalTunggakan = 'Rp 0';

    public function mount()
    {
        $this->totalSiswa = Siswa::where('status', 'aktif')->count();
        $this->totalGuru = Guru::where('status_aktif', true)->count();
        $this->totalKelas = Kelas::count();
        
        $tunggakanNominal = Tagihan::whereIn('status', ['belum_bayar', 'sebagian'])->sum(\DB::raw('nominal - total_dibayar'));
        $this->totalTunggakan = 'Rp ' . number_format($tunggakanNominal, 0, ',', '.');
    }

    public function render()
    {
        return view('livewire.super-admin.dashboard')
            ->layout('components.layouts.app', ['title' => 'Dashboard Super Admin']);
    }
}
