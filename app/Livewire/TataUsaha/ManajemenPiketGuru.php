<?php

namespace App\Livewire\TataUsaha;

use Livewire\Component;
use App\Models\JadwalPiketGuru;
use App\Models\Guru;
use App\Models\Semester;

class ManajemenPiketGuru extends Component
{
    public ?int $selectedGuruId = null;
    public string $selectedHari = 'senin';

    public function addPiket()
    {
        $this->validate([
            'selectedGuruId' => 'required|exists:guru,id',
            'selectedHari' => 'required|in:senin,selasa,rabu,kamis,jumat',
        ]);

        $activeSemester = Semester::where('status_aktif', true)->first();
        if (!$activeSemester) {
            session()->flash('error', 'Semester aktif tidak ditemukan.');
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
        JadwalPiketGuru::destroy($id);
        session()->flash('message', 'Jadwal piket guru berhasil dihapus.');
    }

    public function render()
    {
        $activeSemester = Semester::where('status_aktif', true)->first();

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
        ])->layout('components.layouts.app', ['title' => 'Kelola Jadwal Piket Guru']);
    }
}
