<?php

namespace App\Livewire\Murid;

use App\Models\Siswa;
use App\Models\Tabungan;
use Livewire\Component;

class TabunganSaya extends Component
{
    public function render()
    {
        $user = auth()->user();
        $siswa = Siswa::with('kelas')->where('user_id', $user->id)->first();

        $mutasiList = collect();
        $saldoTerkini = 0;
        $totalSetor = 0;
        $totalTarik = 0;

        if ($siswa) {
            $mutasiList = Tabungan::with('petugas')
                ->where('siswa_id', $siswa->id)
                ->orderBy('tanggal', 'desc')
                ->orderBy('id', 'desc')
                ->get();

            $totalSetor = (float) Tabungan::where('siswa_id', $siswa->id)->where('jenis', 'setor')->sum('nominal');
            $totalTarik = (float) Tabungan::where('siswa_id', $siswa->id)->where('jenis', 'tarik')->sum('nominal');
            
            $latest = $mutasiList->first();
            $saldoTerkini = $latest ? (float) $latest->saldo_akhir : 0;
        }

        return view('livewire.murid.tabungan-saya', [
            'siswa' => $siswa,
            'mutasiList' => $mutasiList,
            'saldoTerkini' => $saldoTerkini,
            'totalSetor' => $totalSetor,
            'totalTarik' => $totalTarik,
        ])->layout('components.layouts.app', ['title' => 'Tabungan Saya']);
    }
}
