<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use App\Models\GuruMapelKelas;
use App\Models\JadwalPelajaran;
use App\Models\AbsensiGuru;
use App\Models\Pengaturan;
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

        // 1. Get all assigned classes for this teacher (GMK, Wali Kelas Umum, and Guru Tahfizh)
        $gmkAssignments = GuruMapelKelas::where('guru_id', $guru->id)->get();
        $gmkClasses = $gmkAssignments->pluck('kelas_id')->filter();
        $waliClasses = \App\Models\Kelas::where('guru_umum_id', $guru->id)->pluck('id');
        $tahfidzClasses = \App\Models\Kelas::where('guru_tahfidz_id', $guru->id)->pluck('id');

        $allClassIds = $gmkClasses->merge($waliClasses)->merge($tahfidzClasses)->unique()->values();
        $allClasses = \App\Models\Kelas::whereIn('id', $allClassIds)->get();

        $this->totalKelas = $allClasses->count();

        // 2. Get total mapel
        $gmkMapelIds = $gmkAssignments->pluck('mapel_id')->filter()->unique();
        if ($gmkMapelIds->isNotEmpty()) {
            $this->totalMapel = $gmkMapelIds->count();
        } else {
            $jenis = strtolower($guru->jenis_guru);
            if ($jenis === 'tahfidz' || $jenis === 'tahfizh' || $tahfidzClasses->isNotEmpty()) {
                $this->totalMapel = \App\Models\MataPelajaran::whereIn('jenis', ['tahfidz', 'tahfizh', 'agama'])->count() ?: 1;
            } elseif ($this->totalKelas > 0) {
                $this->totalMapel = \App\Models\MataPelajaran::where('jenis', 'umum')->count() ?: 1;
            } else {
                $this->totalMapel = 0;
            }
        }

        // 3. Get schedules for today
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

        // Determine target jam masuk from dynamic Settings
        $jamPiket = Pengaturan::where('key', 'jam_masuk_piket')->value('value') ?? '06:30';
        $jamNonPiket = Pengaturan::where('key', 'jam_masuk_non_piket')->value('value') ?? '06:45';
        $jamUmum = Pengaturan::where('key', 'jam_masuk_guru_umum')->value('value') ?? '09:30';

        $jenis = strtolower($guru->jenis_guru);
        if ($jenis === 'umum') {
            $this->targetJamMasuk = $jamUmum;
        } else {
            $this->targetJamMasuk = $this->hasPiketHariIni ? $jamPiket : $jamNonPiket;
        }

        $todaySchedules = JadwalPelajaran::whereHas('guruMapelKelas', function ($q) use ($guru) {
            $q->where('guru_id', $guru->id);
        })
        ->where('hari', $hariIni)
        ->orderBy('jam_mulai')
        ->get();

        if ($todaySchedules->isEmpty()) {
            // Check any schedule in GMK across the week
            $todaySchedules = JadwalPelajaran::whereHas('guruMapelKelas', function ($q) use ($guru) {
                $q->where('guru_id', $guru->id);
            })
            ->orderBy('jam_mulai')
            ->get();
        }

        $this->jadwalHariIni = $todaySchedules->count() > 0 ? $todaySchedules->count() : $allClasses->count();

        if ($todaySchedules->isNotEmpty()) {
            $this->schedules = $todaySchedules->map(function ($s) {
                return [
                    'jam' => date('H:i', strtotime($s->jam_mulai)) . ' - ' . date('H:i', strtotime($s->jam_selesai)),
                    'kelas' => 'Kelas ' . ($s->guruMapelKelas->kelas->nama_kelas ?? '-'),
                    'mapel' => $s->guruMapelKelas->mapel->nama_mapel ?? '-',
                ];
            })->toArray();
        } else {
            $this->schedules = $allClasses->map(function ($k) {
                $isTahfidz = $k->jenis_kelas === 'tahfidz';
                $mapelName = $isTahfidz ? 'Mutaba\'ah Tahfizh Al-Qur\'an' : 'Mata Pelajaran Bimbingan Kelas';
                return [
                    'jam' => '07:30 - 12:00',
                    'kelas' => 'Kelas ' . $k->nama_kelas,
                    'mapel' => $mapelName,
                ];
            })->toArray();
        }

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
