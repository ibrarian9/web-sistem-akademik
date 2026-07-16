<?php

namespace App\Livewire\Murid;

use Livewire\Component;
use App\Models\JadwalPelajaran as JadwalModel;
use Illuminate\Support\Facades\DB;

class JadwalPelajaran extends Component
{
    public array $schedules = [];

    public function mount()
    {
        $this->loadSchedule();
    }

    public function loadSchedule()
    {
        $siswa = auth()->user()->siswa;
        if (!$siswa) {
            return;
        }

        $activeSemester = DB::table('semester')
            ->join('tahun_ajaran', 'semester.tahun_ajaran_id', '=', 'tahun_ajaran.id')
            ->where('tahun_ajaran.status_aktif', true)
            ->where('semester.status_aktif', true)
            ->select('semester.id')
            ->first();

        if (!$activeSemester) {
            return;
        }

        $records = JadwalModel::whereHas('guruMapelKelas', function ($q) use ($siswa, $activeSemester) {
            $q->where('kelas_id', $siswa->kelas_id)
              ->where('semester_id', $activeSemester->id);
        })
        ->with(['guruMapelKelas.mapel', 'guruMapelKelas.guru.user'])
        ->orderByRaw("FIELD(hari, 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu')")
        ->orderBy('jam_mulai')
        ->get();

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
                    'mapel' => $r->guruMapelKelas->mapel->nama_mapel ?? '-',
                    'guru' => $r->guruMapelKelas->guru->user->nama ?? '-',
                ];
            }
        }
    }

    public function render()
    {
        return view('livewire.murid.jadwal-pelajaran')
            ->layout('components.layouts.app', ['title' => 'Jadwal Pelajaran Murid']);
    }
}
