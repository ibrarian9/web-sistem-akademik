<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use App\Models\JadwalPelajaran;

class JadwalMengajar extends Component
{
    public array $schedules = [];

    public function mount()
    {
        $guru = auth()->user()->guru;
        if (!$guru) {
            return;
        }

        // Get all schedules for this teacher in the active semester
        $records = JadwalPelajaran::whereHas('guruMapelKelas', function ($q) use ($guru) {
            $q->where('guru_id', $guru->id)
              ->whereHas('semester.tahunAjaran', function ($query) {
                  $query->where('status_aktif', true);
              });
        })
        ->orderByRaw("FIELD(hari, 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu')")
        ->orderBy('jam_mulai')
        ->get();

        // Group by day for clean dashboard display
        $this->schedules = [
            'senin' => [],
            'selasa' => [],
            'rabu' => [],
            'kamis' => [],
            'jumat' => [],
            'sabtu' => [],
        ];

        foreach ($records as $r) {
            $day = strtolower($r->hari);
            if (array_key_exists($day, $this->schedules)) {
                $this->schedules[$day][] = [
                    'jam' => date('H:i', strtotime($r->jam_mulai)) . ' - ' . date('H:i', strtotime($r->jam_selesai)),
                    'kelas' => 'Kelas ' . ($r->guruMapelKelas->kelas->nama_kelas ?? '-'),
                    'mapel' => $r->guruMapelKelas->mapel->nama_mapel ?? '-',
                ];
            }
        }
    }

    public function render()
    {
        return view('livewire.guru.jadwal-mengajar')
            ->layout('components.layouts.app', ['title' => 'Jadwal Mengajar Guru']);
    }
}
