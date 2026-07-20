<?php

namespace App\Livewire\KepalaSekolah;

use Livewire\Component;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\Pengeluaran;
use App\Models\Nilai;
use App\Models\Semester;

class Dashboard extends Component
{
    public int $totalSiswa = 0;
    public int $totalGuru = 0;
    public float $totalPemasukan = 0;
    public float $totalPengeluaran = 0;
    public array $kelasAverages = [];

    public function mount()
    {
        $this->totalSiswa = Siswa::where('status', 'aktif')->count();
        $this->totalGuru = Guru::where('status_aktif', true)->count();
        $this->totalPemasukan = floatval(Pembayaran::sum('nominal_dibayar'));
        $this->totalPengeluaran = floatval(Pengeluaran::sum('jumlah'));

        $activeSemester = Semester::where('status_aktif', true)->first();

        if ($activeSemester) {
            $kelass = Kelas::with('guruUmum.user')->get();
            foreach ($kelass as $k) {
                $avg = Nilai::where('kelas_id', $k->id)
                    ->where('semester_id', $activeSemester->id)
                    ->avg('nilai');

                $this->kelasAverages[] = [
                    'nama_kelas' => $k->nama_kelas,
                    'wali_kelas' => $k->guruUmum->user->nama ?? '-',
                    'avg' => round(floatval($avg ?? 0), 2),
                ];
            }
        }
    }

    public function render()
    {
        return view('livewire.kepala-sekolah.dashboard')
            ->layout('components.layouts.app', ['title' => 'Dashboard Kepala Sekolah']);
    }
}
