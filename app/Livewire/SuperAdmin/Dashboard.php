<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Tagihan;
use App\Models\User;
use App\Models\Role;
use App\Models\AbsensiSiswa;
use Carbon\Carbon;

class Dashboard extends Component
{
    public int $totalSiswa = 0;
    public int $totalGuru = 0;
    public int $totalKelas = 0;
    public string $totalTunggakan = 'Rp 0';

    // Chart Data Properties
    public array $classLabels = [];
    public array $classStudentCounts = [];
    public array $roleLabels = [];
    public array $roleUserCounts = [];

    public function mount()
    {
        $this->totalSiswa = Siswa::where('status', 'aktif')->count();
        $this->totalGuru = Guru::where('status_aktif', true)->count();
        $this->totalKelas = Kelas::count();
        
        $tunggakanNominal = Tagihan::whereIn('status', ['belum_bayar', 'sebagian'])
            ->selectRaw('SUM(nominal - total_dibayar) as aggregate')
            ->value('aggregate') ?? 0.00;
        $this->totalTunggakan = 'Rp ' . number_format($tunggakanNominal, 0, ',', '.');

        $this->loadChartData();
    }

    public function loadChartData()
    {
        // 1. Class student distribution (Top 8 classes)
        $classes = Kelas::withCount(['siswa' => function ($q) {
            $q->where('status', 'aktif');
        }])
        ->orderBy('nama_kelas', 'asc')
        ->limit(10)
        ->get();

        $this->classLabels = $classes->pluck('nama_kelas')->map(fn($n) => 'Kls ' . $n)->toArray();
        $this->classStudentCounts = $classes->pluck('siswa_count')->toArray();

        // 2. User Distribution per Role
        $roles = Role::withCount('users')->get();
        $this->roleLabels = $roles->pluck('nama')->map(fn($r) => ucfirst(str_replace('_', ' ', $r)))->toArray();
        $this->roleUserCounts = $roles->pluck('users_count')->toArray();
    }

    public function render()
    {
        return view('livewire.super-admin.dashboard')
            ->layout('components.layouts.app', ['title' => 'Dashboard Super Admin']);
    }
}
