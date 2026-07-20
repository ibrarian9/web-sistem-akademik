<?php

namespace App\Livewire\Finance;

use Livewire\Component;
use App\Models\Pembayaran;
use App\Models\JenisTagihan;
use App\Models\DanaBos;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class ArusMasuk extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterJenis = 'semua'; // 'semua', 'spp', 'tahunan', 'pembangunan', 'bos', 'lainnya'

    public function render()
    {
        // 1. Calculate totals per category
        $sppTotal = Pembayaran::whereHas('tagihan.jenisTagihan', function ($q) {
            $q->where('nama', 'like', '%SPP%');
        })->sum('nominal_dibayar');

        $tahunanTotal = Pembayaran::whereHas('tagihan.jenisTagihan', function ($q) {
            $q->where('nama', 'like', '%Tahunan%')
              ->orWhere('nama', 'like', '%Daftar Ulang%');
        })->sum('nominal_dibayar');

        $pembangunanTotal = Pembayaran::whereHas('tagihan.jenisTagihan', function ($q) {
            $q->where('nama', 'like', '%Pembangunan%')
              ->orWhere('nama', 'like', '%Gedung%');
        })->sum('nominal_dibayar');

        $bosTotal = DanaBos::where('jenis', 'masuk')->sum('nominal');

        $nonSppKasTotal = \App\Models\PemasukanKas::sum('jumlah');

        $lainnyaTotal = Pembayaran::whereHas('tagihan.jenisTagihan', function ($q) {
            $q->where('nama', 'not like', '%SPP%')
              ->where('nama', 'not like', '%Tahunan%')
              ->where('nama', 'not like', '%Daftar Ulang%')
              ->where('nama', 'not like', '%Pembangunan%')
              ->where('nama', 'not like', '%Gedung%');
        })->sum('nominal_dibayar') + $nonSppKasTotal;

        $grandTotalInflow = $sppTotal + $tahunanTotal + $pembangunanTotal + $bosTotal + $lainnyaTotal;

        // 2. Fetch payments list
        $query = Pembayaran::with(['tagihan.siswa.user', 'tagihan.jenisTagihan', 'petugas'])
            ->latest();

        if ($this->search) {
            $query->whereHas('tagihan.siswa.user', function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterJenis !== 'semua') {
            if ($this->filterJenis === 'spp') {
                $query->whereHas('tagihan.jenisTagihan', function ($q) {
                    $q->where('nama', 'like', '%SPP%');
                });
            } elseif ($this->filterJenis === 'tahunan') {
                $query->whereHas('tagihan.jenisTagihan', function ($q) {
                    $q->where('nama', 'like', '%Tahunan%')->orWhere('nama', 'like', '%Daftar Ulang%');
                });
            } elseif ($this->filterJenis === 'pembangunan') {
                $query->whereHas('tagihan.jenisTagihan', function ($q) {
                    $q->where('nama', 'like', '%Pembangunan%')->orWhere('nama', 'like', '%Gedung%');
                });
            } elseif ($this->filterJenis === 'lainnya') {
                $query->whereHas('tagihan.jenisTagihan', function ($q) {
                    $q->where('nama', 'not like', '%SPP%')
                      ->where('nama', 'not like', '%Tahunan%')
                      ->where('nama', 'not like', '%Pembangunan%');
                });
            }
        }

        $pembayarans = $query->paginate(15);

        return view('livewire.finance.arus-masuk', [
            'sppTotal' => $sppTotal,
            'tahunanTotal' => $tahunanTotal,
            'pembangunanTotal' => $pembangunanTotal,
            'bosTotal' => $bosTotal,
            'lainnyaTotal' => $lainnyaTotal,
            'grandTotalInflow' => $grandTotalInflow,
            'pembayarans' => $pembayarans,
        ])->layout('components.layouts.app', ['title' => 'Arus Masuk Keuangan']);
    }
}
