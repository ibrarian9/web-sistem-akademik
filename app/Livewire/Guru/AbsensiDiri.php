<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use App\Models\AbsensiGuru;
use App\Models\Pengaturan;
use Carbon\Carbon;

class AbsensiDiri extends Component
{
    public ?string $waktu_datang = null;
    public ?string $waktu_pulang = null;
    public string $statusToday = 'Belum Hadir';
    public array $history = [];

    // Global settings
    public string $targetJamMasuk = '07:00';
    public int $toleransiMenit = 15;

    public function mount()
    {
        $this->loadSettings();
        $this->checkTodayAttendance();
        $this->loadHistory();
    }

    public bool $hasPiketToday = false;

    public function loadSettings()
    {
        $this->toleransiMenit = intval(Pengaturan::where('key', 'toleransi_keterlambatan')->value('value') ?? 15);
        
        $guru = auth()->user()->guru;
        if ($guru) {
            $jenis = strtolower($guru->jenis_guru);
            if ($jenis === 'umum') {
                $this->targetJamMasuk = '09:30';
                $this->hasPiketToday = false;
            } else { // tahfidz / keduanya
                $todayDayNameIndonesian = match (Carbon::today()->dayOfWeekIso) {
                    1 => 'senin',
                    2 => 'selasa',
                    3 => 'rabu',
                    4 => 'kamis',
                    5 => 'jumat',
                    default => null
                };

                $this->hasPiketToday = false;
                if ($todayDayNameIndonesian) {
                    $this->hasPiketToday = \App\Models\JadwalPiketGuru::where('guru_id', $guru->id)
                        ->where('hari', $todayDayNameIndonesian)
                        ->whereHas('semester', function ($q) {
                            $q->where('status_aktif', true);
                        })
                        ->exists();
                }

                $this->targetJamMasuk = $this->hasPiketToday ? '06:30' : '06:45';
            }
        } else {
            $this->targetJamMasuk = Pengaturan::where('key', 'jam_masuk')->value('value') ?? '07:00';
        }
    }

    public function checkTodayAttendance()
    {
        $guru = auth()->user()->guru;
        if (!$guru) {
            return;
        }

        $absensi = AbsensiGuru::where('guru_id', $guru->id)
            ->whereDate('tanggal', Carbon::today())
            ->first();

        if ($absensi) {
            $this->waktu_datang = $absensi->waktu_datang ? date('H:i:s', strtotime($absensi->waktu_datang)) : null;
            $this->waktu_pulang = $absensi->waktu_pulang ? date('H:i:s', strtotime($absensi->waktu_pulang)) : null;
            $this->statusToday = $absensi->status;
        } else {
            $this->waktu_datang = null;
            $this->waktu_pulang = null;
            $this->statusToday = 'Belum Hadir';
        }
    }

    public function loadHistory()
    {
        $guru = auth()->user()->guru;
        if (!$guru) {
            return;
        }

        $this->history = AbsensiGuru::where('guru_id', $guru->id)
            ->orderBy('tanggal', 'desc')
            ->limit(15)
            ->get()
            ->toArray();
    }

    public function checkIn()
    {
        session()->flash('error', 'Pencatatan presensi kehadiran guru dan karyawan dikelola secara terpusat oleh Tata Usaha. Guru tidak dapat melakukan absensi mandiri.');
    }

    public function checkOut()
    {
        session()->flash('error', 'Pencatatan presensi kehadiran guru dan karyawan dikelola secara terpusat oleh Tata Usaha. Guru tidak dapat melakukan absensi mandiri.');
    }

    public function render()
    {
        return view('livewire.guru.absensi-diri')
            ->layout('components.layouts.app', ['title' => 'Absensi Mandiri Guru']);
    }
}
