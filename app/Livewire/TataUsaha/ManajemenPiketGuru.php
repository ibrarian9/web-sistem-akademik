<?php

namespace App\Livewire\TataUsaha;

use Livewire\Component;
use App\Models\JadwalPiketGuru;
use App\Models\Guru;
use App\Models\Pengaturan;
use App\Models\Semester;

class ManajemenPiketGuru extends Component
{
    public ?int $selectedGuruId = null;
    public string $selectedHari = 'senin';

    // Flexible Check-in Time Settings
    public string $jamMasukPiket = '06:30';
    public string $jamMasukNonPiket = '06:45';
    public string $jamMasukGuruUmum = '09:30';

    public function mount()
    {
        $this->loadJamSettings();
    }

    public function loadJamSettings()
    {
        $this->jamMasukPiket = Pengaturan::where('key', 'jam_masuk_piket')->value('value') ?? '06:30';
        $this->jamMasukNonPiket = Pengaturan::where('key', 'jam_masuk_non_piket')->value('value') ?? '06:45';
        $this->jamMasukGuruUmum = Pengaturan::where('key', 'jam_masuk_guru_umum')->value('value') ?? '09:30';
    }

    public function canManage(): bool
    {
        $role = auth()->user()->role->nama ?? '';
        return in_array($role, ['tata_usaha', 'super_admin']);
    }

    public function updateJamSettings()
    {
        if (!$this->canManage()) {
            session()->flash('error', 'Akses ditolak. Anda tidak memiliki wewenang merubah pengaturan jam piket.');
            return;
        }

        $this->validate([
            'jamMasukPiket' => 'required',
            'jamMasukNonPiket' => 'required',
            'jamMasukGuruUmum' => 'required',
        ]);

        Pengaturan::updateOrCreate(['key' => 'jam_masuk_piket'], ['value' => $this->jamMasukPiket]);
        Pengaturan::updateOrCreate(['key' => 'jam_masuk_non_piket'], ['value' => $this->jamMasukNonPiket]);
        Pengaturan::updateOrCreate(['key' => 'jam_masuk_guru_umum'], ['value' => $this->jamMasukGuruUmum]);

        session()->flash('message', 'Pengaturan Fleksibel Jam Check-In (Piket & Non-Piket) berhasil diperbarui.');
    }

    public function addPiket()
    {
        if (!$this->canManage()) {
            session()->flash('error', 'Anda hanya memiliki hak akses untuk melihat jadwal piket guru.');
            return;
        }

        $this->validate([
            'selectedGuruId' => 'required|exists:guru,id',
            'selectedHari' => 'required|in:senin,selasa,rabu,kamis,jumat',
        ]);

        $activeSemester = Semester::where('status_aktif', true)->first() ?? Semester::first();
        if (!$activeSemester) {
            session()->flash('error', 'Semester tidak ditemukan dalam database.');
            return;
        }

        $exists = JadwalPiketGuru::where('guru_id', $this->selectedGuruId)
            ->where('hari', $this->selectedHari)
            ->where('semester_id', $activeSemester->id)
            ->exists();

        if ($exists) {
            session()->flash('error', 'Guru tersebut sudah ditugaskan piket pada hari yang dipilih.');
            return;
        }

        JadwalPiketGuru::create([
            'guru_id' => $this->selectedGuruId,
            'hari' => $this->selectedHari,
            'semester_id' => $activeSemester->id,
        ]);

        session()->flash('message', 'Jadwal piket guru berhasil ditambahkan.');
        $this->selectedGuruId = null;
    }

    public function deletePiket($id)
    {
        if (!$this->canManage()) {
            session()->flash('error', 'Anda hanya memiliki hak akses untuk melihat jadwal piket guru.');
            return;
        }

        JadwalPiketGuru::destroy($id);
        session()->flash('message', 'Jadwal piket guru berhasil dihapus.');
    }

    public function render()
    {
        $activeSemester = Semester::where('status_aktif', true)->first() ?? Semester::first();

        $piketSchedules = [];
        $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat'];

        if ($activeSemester) {
            foreach ($days as $day) {
                $piketSchedules[$day] = JadwalPiketGuru::where('semester_id', $activeSemester->id)
                    ->where('hari', $day)
                    ->with('guru.user')
                    ->get();
            }
        }

        $gurus = Guru::where('status_aktif', true)->with('user')->get();

        return view('livewire.tata-usaha.manajemen-piket-guru', [
            'piketSchedules' => $piketSchedules,
            'gurus' => $gurus,
            'days' => $days,
            'canManage' => $this->canManage(),
        ])->layout('components.layouts.app', ['title' => $this->canManage() ? 'Kelola Jadwal Piket Guru' : 'Jadwal Piket Guru']);
    }
}
