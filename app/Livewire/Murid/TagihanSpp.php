<?php

namespace App\Livewire\Murid;

use Livewire\Component;
use App\Models\Siswa;
use App\Models\Tagihan;

class TagihanSpp extends Component
{
    public float $totalTunggakan = 0.00;
    public array $invoices = [];

    public function mount()
    {
        $this->loadTagihan();
    }

    public function loadTagihan()
    {
        $siswa = auth()->user()->siswa;
        if (!$siswa) {
            return;
        }

        $records = Tagihan::where('siswa_id', $siswa->id)
            ->with(['pembayarans', 'jenisTagihan'])
            ->orderBy('jatuh_tempo', 'desc')
            ->get();

        $this->totalTunggakan = 0.00;
        $this->invoices = [];

        foreach ($records as $r) {
            if ($r->status !== 'lunas') {
                $this->totalTunggakan += floatval($r->nominal - $r->total_dibayar);
            }

            $payments = [];
            foreach ($r->pembayarans as $p) {
                $payments[] = [
                    'tanggal' => date('d-m-Y', strtotime($p->tanggal_bayar)),
                    'jumlah' => floatval($p->nominal_dibayar),
                    'metode' => $p->metode_bayar,
                    'bukti' => $p->bukti_bayar,
                ];
            }

            $this->invoices[] = [
                'id' => $r->id,
                'jenis' => $r->jenisTagihan->nama ?? 'Tagihan',
                'bulan' => $r->bulan ?: '-',
                'nominal' => floatval($r->nominal),
                'total_dibayar' => floatval($r->total_dibayar),
                'sisa' => floatval($r->nominal - $r->total_dibayar),
                'status' => $r->status,
                'jatuh_tempo' => date('d-m-Y', strtotime($r->jatuh_tempo)),
                'pembayaran' => $payments,
            ];
        }
    }

    public function render()
    {
        return view('livewire.murid.tagihan-spp')
            ->layout('components.layouts.app', ['title' => 'Tagihan SPP Murid']);
    }
}
