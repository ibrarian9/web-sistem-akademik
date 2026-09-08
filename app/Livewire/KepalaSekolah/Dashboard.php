<?php

namespace App\Livewire\KepalaSekolah;

use Livewire\Component;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Tagihan;
use App\Models\Nilai;
use App\Models\Semester;
use App\Models\AbsensiSiswa;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public int $totalSiswa = 0;
    public int $totalGuru = 0;
    public int $totalKelas = 0;
    public int $totalSiswaMenunggak = 0;
    public float $totalNominalTunggakan = 0;
    public float $persentaseKehadiran = 0;
    public array $kelasAverages = [];

    public function mount()
    {
        $this->totalSiswa = Siswa::where('status', 'aktif')->count();
        $this->totalGuru = Guru::where('status_aktif', true)->count();
        $this->totalKelas = Kelas::count();

        $activeSemester = Semester::where('status_aktif', true)->first();

        // Hitung Tunggakan Siswa
        $tagihanUnpaid = Tagihan::whereIn('status', ['belum_bayar', 'sebagian']);
        if ($activeSemester && $activeSemester->tahun_ajaran_id) {
            $tagihanUnpaid->where('tahun_ajaran_id', $activeSemester->tahun_ajaran_id);
        }
        $this->totalSiswaMenunggak = (clone $tagihanUnpaid)->distinct('siswa_id')->count('siswa_id');
        $this->totalNominalTunggakan = floatval((clone $tagihanUnpaid)->sum(DB::raw('nominal - total_dibayar')));

        // Hitung Tingkat Kehadiran Siswa
        $totalAbsen = AbsensiSiswa::count();
        if ($totalAbsen > 0) {
            $hadirCount = AbsensiSiswa::where('status', 'hadir')->count();
            $this->persentaseKehadiran = round(($hadirCount / $totalAbsen) * 100, 1);
        }

        // Hitung Rata-rata Nilai Per Kelas
        if ($activeSemester) {
            $kelass = Kelas::with('guruUmum.user')->get();
            foreach ($kelass as $k) {
                $avg = Nilai::where('kelas_id', $k->id)
                    ->where('semester_id', $activeSemester->id)
                    ->avg('nilai');

                $this->kelasAverages[] = [
                    'nama_kelas' => $k->nama_kelas,
                    'tingkat' => $k->tingkat,
                    'wali_kelas' => $k->guruUmum->user->nama ?? '-',
                    'avg' => round(floatval($avg ?? 0), 2),
                ];
            }
        }
    }

    public function render()
    {
        $roleName = auth()->user()->role->nama ?? '';
        $title = in_array($roleName, ['pengawas', 'koordinator'])
            ? 'Dashboard Pengawas Sekolah'
            : 'Dashboard Kepala Sekolah';

        return view('livewire.kepala-sekolah.dashboard')
            ->layout('components.layouts.app', ['title' => $title]);
    }
}

