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
        $guru = auth()->user()->guru;
        if (!$guru) {
            session()->flash('error', 'Data kepegawaian guru tidak ditemukan.');
            return;
        }

        // Prevent double check-in
        $existing = AbsensiGuru::where('guru_id', $guru->id)
            ->whereDate('tanggal', Carbon::today())
            ->first();

        if ($existing) {
            session()->flash('error', 'Anda sudah melakukan check-in hari ini.');
            return;
        }

        $now = Carbon::now();
        $limitTime = Carbon::createFromFormat('H:i', $this->targetJamMasuk)->addMinutes($this->toleransiMenit);

        $status = 'hadir';
        if ($now->format('H:i') > $limitTime->format('H:i')) {
            $status = 'telat';
        }

        AbsensiGuru::create([
            'guru_id' => $guru->id,
            'tanggal' => Carbon::today(),
            'waktu_datang' => $now->toTimeString(),
            'status' => $status,
            'diinput_oleh' => auth()->id(),
        ]);

        session()->flash('message', 'Check-in berhasil dilakukan.');
        $this->checkTodayAttendance();
        $this->loadHistory();
    }

    public function checkOut()
    {
        $guru = auth()->user()->guru;
        if (!$guru) {
            return;
        }

        $absensi = AbsensiGuru::where('guru_id', $guru->id)
            ->whereDate('tanggal', Carbon::today())
            ->first();

        if (!$absensi) {
            session()->flash('error', 'Anda harus check-in terlebih dahulu.');
            return;
        }

        if ($absensi->waktu_pulang) {
            session()->flash('error', 'Anda sudah melakukan check-out hari ini.');
            return;
        }

        $absensi->update([
            'waktu_pulang' => Carbon::now()->toTimeString(),
        ]);

        session()->flash('message', 'Check-out berhasil dilakukan.');
        $this->checkTodayAttendance();
        $this->loadHistory();
    }

    public function render()
    {
        return view('livewire.guru.absensi-diri')
            ->layout('components.layouts.app', ['title' => 'Absensi Mandiri Guru']);
    }
}
