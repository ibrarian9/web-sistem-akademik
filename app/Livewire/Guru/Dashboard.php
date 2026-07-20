<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use App\Models\GuruMapelKelas;
use App\Models\JadwalPelajaran;
use App\Models\AbsensiGuru;
use Carbon\Carbon;

class Dashboard extends Component
{
    public int $totalKelas = 0;
    public int $totalMapel = 0;
    public int $jadwalHariIni = 0;
    public string $statusAbsensi = 'Belum Hadir';
    public ?string $waktuCheckIn = null;
    public array $schedules = [];

    public bool $hasPiketHariIni = false;
    public string $targetJamMasuk = '07:00';

    public function mount()
    {
        $user = auth()->user();
        $guru = $user->guru;

        if (!$guru) {
            return;
        }

        // Get total classes and mapel assigned to this teacher
        $assignments = GuruMapelKelas::where('guru_id', $guru->id)
            ->whereHas('semester.tahunAjaran', function ($q) {
                $q->where('status_aktif', true);
            })
            ->get();

        $this->totalKelas = $assignments->pluck('kelas_id')->unique()->count();
        $this->totalMapel = $assignments->pluck('mapel_id')->unique()->count();

        // Get schedules for today
        $hariMap = [
            0 => 'minggu',
            1 => 'senin',
            2 => 'selasa',
            3 => 'rabu',
            4 => 'kamis',
            5 => 'jumat',
            6 => 'sabtu',
        ];
        $hariIni = $hariMap[Carbon::now()->dayOfWeek] ?? 'senin';

        // Check piket schedule for today
        $this->hasPiketHariIni = \App\Models\JadwalPiketGuru::where('guru_id', $guru->id)
            ->where('hari', $hariIni)
            ->whereHas('semester', function ($q) {
                $q->where('status_aktif', true);
            })->exists();

        // Determine target jam masuk
        $jenis = strtolower($guru->jenis_guru);
        if ($jenis === 'umum') {
            $this->targetJamMasuk = '09:30';
        } else { // tahfidz / keduanya
            $this->targetJamMasuk = $this->hasPiketHariIni ? '06:30' : '06:45';
        }

        $todaySchedules = JadwalPelajaran::whereHas('guruMapelKelas', function ($q) use ($guru) {
            $q->where('guru_id', $guru->id)
              ->whereHas('semester.tahunAjaran', function ($query) {
                  $query->where('status_aktif', true);
              });
        })
        ->where('hari', $hariIni)
        ->orderBy('jam_mulai')
        ->get();

        $this->jadwalHariIni = $todaySchedules->count();

        // Format today's schedules list for display
        $this->schedules = $todaySchedules->map(function ($s) {
            return [
                'jam' => date('H:i', strtotime($s->jam_mulai)) . ' - ' . date('H:i', strtotime($s->jam_selesai)),
                'kelas' => 'Kelas ' . ($s->guruMapelKelas->kelas->nama_kelas ?? '-'),
                'mapel' => $s->guruMapelKelas->mapel->nama_mapel ?? '-',
            ];
        })->toArray();

        // Check attendance today
        $absensi = AbsensiGuru::where('guru_id', $guru->id)
            ->whereDate('tanggal', Carbon::today())
            ->first();

        if ($absensi) {
            $this->waktuCheckIn = date('H:i', strtotime($absensi->waktu_datang));
            $this->statusAbsensi = ucfirst($absensi->status);
        }
    }

    public function render()
    {
        return view('livewire.guru.dashboard')
            ->layout('components.layouts.app', ['title' => 'Dashboard Guru']);
    }
}
