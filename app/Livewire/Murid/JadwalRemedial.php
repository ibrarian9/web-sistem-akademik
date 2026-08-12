<?php

namespace App\Livewire\Murid;

use App\Models\JadwalRemedial as JadwalRemedialModel;
use App\Models\Siswa;
use Livewire\Component;

class JadwalRemedial extends Component
{
    public function render()
    {
        $user = auth()->user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        $remedialList = collect();

        if ($siswa) {
            $remedialList = JadwalRemedialModel::with(['guru.user', 'mapel', 'kelas'])
                ->where('kelas_id', $siswa->kelas_id)
                ->where(function ($q) use ($siswa) {
                    $q->whereNull('siswa_id')->orWhere('siswa_id', $siswa->id);
                })
                ->orderBy('tanggal', 'desc')
                ->get();
        }

        return view('livewire.murid.jadwal-remedial', [
            'remedialList' => $remedialList,
            'siswa' => $siswa,
        ])->layout('components.layouts.app', ['title' => 'Jadwal Remedial Saya']);
    }
}
